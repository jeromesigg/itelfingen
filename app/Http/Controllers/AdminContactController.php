<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class AdminContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        //
        $title = 'Anfragen';

        return view('admin.contacts.index', compact('title'));
    }

    public function createDataTablesTanStack(Request $request)
    {
        
        $input = $request->all();
        if (!isset($input['status'])) {
            $input['status'] = 'Alle';
        }
        if($input['status'] == 'Alle') {
        $contacts = Contact::orderby('created_at', 'DESC')->get();
        } else {
            $done = ($input['status'] == 'Bearbeitet');
            $contacts = Contact::where('done', $done)->orderby('created_at', 'DESC')->get();
        }

        // Transformation der Daten (gleich wie Yajra, aber manuel)
        $data = $contacts->map(function (Contact $contact) {
            return [
                'created_at' => [
                    'display' => Carbon::parse($contact['created_at'])->format('d.m.Y'),
                    'sort' => Carbon::parse($contact['created_at'])->toISOString(),
                ],
                'name' => $contact['name'],
                'email' => $contact['email'],
                'subject' =>  $contact['subject'],
                'content' => $contact['content'],
                'done' =>  $contact['done'] ? 'Ja' : 'Nein',
                'user' => $contact->user['username'] ?? '',
                'actions' => '<form action="'.\URL::route('contacts.done', $contact).'" method="post">'.csrf_field().(!$contact['done'] ? '<button type="submit" class="btn btn-secondary btn-sm">Bearbeitet</button>' : '').'</form>',
            ];
        });

        // TanStack-freundliches Response-Format
        return response()->json([
            'data' => $data,
            'rowCount' => count($data),
        ]);
    }


    public function done(Contact $contact)
    {
        //
        $contact->update(['user_id' => Auth::user()->id, 'done' => true]);

        return redirect('/admin/contacts');
    }
}
