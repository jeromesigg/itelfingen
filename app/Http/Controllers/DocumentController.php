<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\IOFactory;
use Illuminate\Support\Facades\Mail;
use PhpOffice\PhpWord\TemplateProcessor;

class DocumentController extends Controller
{
    //   
    public function convertWordToPDF()
    {
            /* Set the PDF Engine Renderer Path */
        $domPdfPath = base_path('vendor/dompdf/dompdf');
        Settings::setPdfRendererPath($domPdfPath);
        Settings::setPdfRendererName('DomPDF');

        /*@ Reading doc file */
        $template = new TemplateProcessor(public_path('contracts/result.docx'));
    
        /*@ Replacing variables in doc file */
        $template->setValue('date', date('d-m-Y'));
        $template->setValue('title', 'Mr.');
        $template->setValue('firstname', 'Scratch');
        $template->setValue('lastname', 'Coder');

        /*@ Save Temporary Word File With New Name */
        $saveDocPath = public_path('contracts/new-result.docx');
        $template->saveAs($saveDocPath);

        // Load temporarily create word file
        $Content = IOFactory::load($saveDocPath); 
    
        //Save it into PDF
        $savePdfPath = public_path('contracts/new-result.pdf');
        
        /*@ If already PDF exists then delete it */
        if ( file_exists($savePdfPath) ) {
            unlink($savePdfPath);
        }

        //Save it into PDF
        $PDFWriter = IOFactory::createWriter($Content,'PDF');
        $PDFWriter->save($savePdfPath); 
        /*@ Remove temporarily created word file */
        if ( file_exists($saveDocPath) ) {
            unlink($saveDocPath);
        }

        $data["email"] = "jerome.sigg@gmail.com";
        $data["title"] = "From ItSolutionStuff.com";
        $data["body"] = "This is Demo";
  
        Mail::send('emails.myTestMail', $data, function($message)use($data, $savePdfPath) {
            $message->to($data["email"], $data["email"])
                    ->subject($data["title"])
                    ->attach($savePdfPath, [
                        'as'    => 'Vertrag.pdf',
                        'mime'   => 'application/pdf',
                    ]);
        });
    }
}
