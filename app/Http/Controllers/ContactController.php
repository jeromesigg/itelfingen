<?php

namespace App\Http\Controllers;

use App\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    //
    public function store(Request $request) { 
        
        $input = $request->all();
        $email = $input['email'];
        $name = $input['name'];
        $subject = $input['subject'];
        
        $input['done'] = false;
        $data = array('name'=>$name, 'email'=>$email, 'subject'=>$subject, 'text'=> $input['content']);
        Mail::send('emails.send_contact',  $data, function($message) use($email, $name){
            $message
                ->to($email, $name)
                ->replyto(config('mail.from.address'), config('mail.from.name'))
                ->subject('Kopie deiner Nachricht ans Ferien- und Lagerhaus Itelfingen');
        });
        Mail::send('emails.send_contact',  $data, function($message){
          $message->to(config('mail.from.address'), config('mail.from.name'))->subject('Kontaktanfrage Ferien- und Lagerhaus Itelfingen');
        });
        Contact::create($input);      
        return redirect()->to(url()->previous() . '#contact')->with('success_contact', 'Vielen Dank für die Nachricht. Wir werden uns so schnell wie möglich melden.');
    
    }
}
