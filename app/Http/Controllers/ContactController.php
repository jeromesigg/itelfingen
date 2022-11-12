<?php

namespace App\Http\Controllers;

use App\Mail\ContactCreated;
use App\Models\Contact;
use App\Notifications\ContactCreatedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Notification;
use Validator;

class ContactController extends Controller
{
    //
    public function store(Request $request) {

        $input = $request->all();
        $validator = Validator::make($input, [
            'name'  => 'required',
            'email'  => 'email|required',
            'content'  => 'required',
            'g-recaptcha-response' => 'recaptcha'
        ]);

        if ($validator->fails()) {
            return redirect()->to(url()->previous() . '#contact')
                        ->withErrors($validator, 'contact')
                        ->withInput();
        }

        $contact = Contact::create($input);
        Notification::send($contact, new ContactCreatedNotification($contact));
        return redirect()->to(url()->previous() . '#contact')->with('success_contact', 'Vielen Dank für die Nachricht. Wir werden uns so schnell wie möglich melden.');

    }
}
