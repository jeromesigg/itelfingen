<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Notifications\ContactCreatedNotification;
use Illuminate\Http\Request;
use Notification;
use Validator;

class ContactController extends Controller
{
    //
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->to(url()->previous().'#contact')
                ->withErrors($validator, 'contact')
                ->withInput();
        }
        // Nachrichten ohne Leerzeichen (ein Wort, ein Link) sind fast immer Spam.
        if (! preg_match('/\s/', $request->input('content'))) {
            abort(response()->view('errors.422', [], 422));
        }

        $contact = Contact::create($request->only(['name', 'email', 'subject', 'content']));
        Notification::send($contact, new ContactCreatedNotification($contact));

        return redirect()->to(url()->previous().'#contact')->with('success_contact', 'Vielen Dank für die Nachricht. Wir werden uns so schnell wie möglich melden.');
    }
}
