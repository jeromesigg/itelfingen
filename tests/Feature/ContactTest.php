<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ContactTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('migrate --seed');
    }

    public function tearDown(): void
    {
        Artisan::call('migrate:reset');
        parent::tearDown();
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->post('/contacts', [
            'name' => 'Jerome Sigg',
            'email' => 'jerome.sigg@gmail.com',
            'subject' => 'Test',
            'content' => 'Inhalt',
            'g-recaptcha-response' => '1',
        ]);

        $response->assertStatus(200);
    }
}
