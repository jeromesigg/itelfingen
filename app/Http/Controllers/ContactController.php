<?php

namespace App\Http\Controllers;

use App\Mail\ContactCreated;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
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


        $email = $input['email'];
        $name = $input['name'];
        $subject = $input['subject'];

        $input['done'] = false;
        $data = array('name'=>$name, 'email'=>$email, 'subject'=>$subject, 'text'=> $input['content']);

        $contact = Contact::create($input);
//        return (new ContactCreated($contact));
        Mail::send(new ContactCreated($contact));
        return redirect()->to(url()->previous() . '#contact')->with('success_contact', 'Vielen Dank für die Nachricht. Wir werden uns so schnell wie möglich melden.');

    }
}
