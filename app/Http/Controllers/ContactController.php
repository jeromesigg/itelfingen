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
        $input = $request->all();
        if (! preg_match('/\s/', $input['content'])) {
            abort(response()->view('errors.422', [], 422));
        }
        $validator = Validator::make($input, [
            'name' => 'required',
            'email' => 'email|required',
            'content' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->to(url()->previous().'#contact')
                ->withErrors($validator, 'contact')
                ->withInput();
        }

        $contact = Contact::create($request->only(['name', 'email', 'subject', 'content']));
        Notification::send($contact, new ContactCreatedNotification($contact));

        return redirect()->to(url()->previous().'#contact')->with('success_contact', 'Vielen Dank für die Nachricht. Wir werden uns so schnell wie möglich melden.');
    }
}
