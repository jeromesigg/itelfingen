<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Application|Factory|View
    {
        //
        $title = 'Anfragen';

        return view('admin.contacts.index', compact('title'));
    }

    public function createDataTablesTanStack(Request $request)
    {

        $input = $request->all();
        if (! isset($input['status'])) {
            $input['status'] = 'Alle';
        }
        if ($input['status'] == 'Alle') {
            $contacts = Contact::orderby('created_at', 'DESC')->get();
        } else {
            $done = ($input['status'] == 'Bearbeitet');
            $contacts = Contact::where('done', $done)->orderby('created_at', 'DESC')->get();
        }

        $data = $contacts->map(fn (Contact $contact) => $this->row($contact));

        // TanStack-freundliches Response-Format
        return response()->json([
            'data' => $data,
            'rowCount' => count($data),
        ]);
    }

    public function done(Request $request, Contact $contact): JsonResponse|RedirectResponse
    {
        $contact->update(['user_id' => Auth::user()->id, 'done' => true]);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Anfrage als bearbeitet markiert.', 'row' => $this->row($contact->fresh('user'))]);
        }

        return redirect('/admin/contacts');
    }

    /**
     * Zeile der Tabelle (DataTable.vue). Textspalten ohne HTML, Aktionen als Daten.
     *
     * @return array<string, mixed>
     */
    private function row(Contact $contact): array
    {
        return [
            'id' => $contact->id,
            'created_at' => [
                'display' => Carbon::parse($contact['created_at'])->format('d.m.Y'),
                'sort' => Carbon::parse($contact['created_at'])->toISOString(),
            ],
            'name' => $contact['name'],
            'email' => $contact['email'],
            'subject' => $contact['subject'],
            'content' => $contact['content'],
            'done' => $contact['done'] ? 'Ja' : 'Nein',
            'user' => $contact->user['username'] ?? '',
            'actions' => $contact['done'] ? [] : [
                ['label' => 'Bearbeitet', 'url' => route('contacts.done', $contact)],
            ],
        ];
    }
}
