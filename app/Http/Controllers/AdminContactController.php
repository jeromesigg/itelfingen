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

    public function createDataTables(Request $request)
    {
        $input = $request->all();
        $done = ! ($input['done'] != 'Alle');
        $contacts = Contact::where('done', $done)->orderby('created_at', 'DESC')->get();

        return DataTables::of($contacts)
            ->editColumn('created_at', function (Contact $contact) {
                return [
                    'display' => Carbon::parse($contact['created_at'])->format('d.m.Y'),
                    'sort' => Carbon::parse($contact['created_at'])->diffInDays('01.01.2021'),
                ];
            })
            ->editColumn('user', function (Contact $contact) {
                return $contact->user ? $contact->user['username'] : '';
            })
            ->editColumn('done', function (Contact $contact) {
                return $contact->done ? 'Ja' : 'Nein';
            })
            ->addColumn('Actions', function (Contact $contact) {
                $buttons = '<form action="'.\URL::route('contacts.done', $contact).'" method="POST">'.csrf_field();
                if (! $contact['done']) {
                    $buttons .= '  <button type="submit" class="btn btn-secondary btn-sm">Bearbeitet</button>';
                }
                $buttons .= '</form>';

                return $buttons;
            })
            ->rawColumns(['name', 'Actions'])
            ->make(true);
    }

    public function done(Contact $contact)
    {
        //
        $contact->update(['user_id' => Auth::user()->id, 'done' => true]);

        return redirect('/admin/contacts');
    }
}
