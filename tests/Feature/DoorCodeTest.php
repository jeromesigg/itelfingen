<?php

namespace Tests\Feature;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\Support\BookingScenario;
use Tests\TestCase;

/**
 * Türcode für eine Buchung (POST admin/events/createAPICode): legt in Glutz einen Besucher an,
 * setzt den Code und gibt den Hauseingang für die Aufenthaltsdauer frei. Glutz ist JSON-RPC und gefakt.
 */
class DoorCodeTest extends TestCase
{
    private BookingScenario $s;

    /** @var array<string, mixed> Antwort (result) pro Glutz-Methode */
    private array $glutz;

    protected function setUp(): void
    {
        parent::setUp();

        $this->s = BookingScenario::create();
        $this->glutz = [
            'eAccess.createUser' => 501,
            'eAccess.getModel' => [],
            'eAccess.getAccessPoint' => 9,
            'eAccess.getAccessPointsOfUser' => [],
            'eAccess.getDeviceEvaluationAndUpdateState' => ['updateNeeded' => []],
        ];
        Http::fake(fn (Request $r) => Http::response([
            'jsonrpc' => '2.0',
            'result' => array_key_exists($r['method'], $this->glutz) ? $this->glutz[$r['method']] : true,
            'id' => 1,
        ]));
    }

    private function createCode(string $code = '4711')
    {
        return $this->actingAs($this->s->user('verwalter'))
            ->postJson('/admin/events/createAPICode', ['event_id' => $this->s->confirmedEvent->id, 'code' => $code]);
    }

    private function glutzCalls(string $method): array
    {
        return Http::recorded()->filter(fn ($pair) => $pair[0]['method'] === $method)->map(fn ($pair) => $pair[0]['params'])->values()->all();
    }

    public function test_creates_visitor_with_code_for_stay(): void
    {
        $event = $this->s->confirmedEvent;

        $this->createCode()->assertOk()->assertJson(['success' => 'Code erfolgreich erstellt.', 'User' => 'Besucher '.$event->number()]);

        $event->refresh();
        $this->assertSame(4711, $event->code);
        $this->assertSame('501', $event->glutz_user_id);
        $this->assertSame([['Codes', ['code' => '4711', 'userId' => 501]]], $this->glutzCalls('eAccess.setModel'));
        $this->assertSame([[[9], '501']], $this->glutzCalls('eAccess.addAccessPointsToUser'));
        // Gültig vom Anreisetag 00:00 bis Abreisetag 23:59
        $validity = $this->glutzCalls('eAccess.setAccessRightProperties')[0][2];
        $this->assertSame(now()->parse($event->start_date)->format('Y-m-d').'T00:00:00.000', $validity['validFrom']);
        $this->assertSame(now()->parse($event->end_date)->format('Y-m-d').'T23:59:59.999', $validity['validTo']);
    }

    public function test_reuses_existing_glutz_user(): void
    {
        $this->s->confirmedEvent->update(['glutz_user_id' => '777']);
        $this->glutz['eAccess.getAccessPointsOfUser'] = [9];

        $this->createCode()->assertOk();

        $this->assertSame([], $this->glutzCalls('eAccess.createUser'));
        $this->assertSame([], $this->glutzCalls('eAccess.addAccessPointsToUser'));
    }

    public function test_code_of_another_visitor_is_rejected(): void
    {
        $this->glutz['eAccess.getModel'] = [['code' => '4711', 'userId' => 999]];

        $this->createCode()->assertStatus(500)->assertJson(['error' => 'Code bereits in Verwendung für anderen User.']);

        $this->assertNull($this->s->confirmedEvent->fresh()->code);
        $this->assertSame([], $this->glutzCalls('eAccess.setModel'));
    }

    public function test_code_must_have_four_digits(): void
    {
        $this->createCode('123')->assertUnprocessable();

        Http::assertNothingSent();
    }

    public function test_unreachable_glutz_returns_json_error(): void
    {
        // Glutz liefert keinen Benutzer: bisher leere Antwort, das JavaScript zeigte nur "SyntaxError".
        $this->glutz['eAccess.createUser'] = null;

        $this->createCode()->assertStatus(502)->assertJsonStructure(['error']);

        $this->assertNull($this->s->confirmedEvent->fresh()->code);
    }

    public function test_missing_access_point_returns_json_error(): void
    {
        $this->glutz['eAccess.getAccessPoint'] = null;

        $this->createCode()->assertStatus(502)->assertJsonStructure(['error']);

        $this->assertNull($this->s->confirmedEvent->fresh()->code);
    }
}
