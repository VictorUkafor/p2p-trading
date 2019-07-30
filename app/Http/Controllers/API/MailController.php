<?php

namespace App\Http\Controllers\API;

use App\Model\Mail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JD\Cloudder\Facades\Cloudder;


class MailController extends Controller
{
    public function create(Request $request){
        try{

            $file = '';
            if($request->file){
                $file = str_random(20).time();
                Cloudder::upload($request->file->getRealPath(), $file);
            }
            
           $mail = new Mail;
           $mail->user_id = $request->user->id;
           $mail->name = $request->name;
           $mail->email = $request->email;
           $mail->subject = $request->subject;
           $mail->message = $request->message;
           $mail->file = $file;
           $mail->save();
            
            return response()->json([
                'successMessage' => 'Your message has been sent successfully',
            ]   , 201);
        
        } catch(Exception $e) {
            
            return response()->json([
                'errorMessage' => $e->getMessage(),
            ]   , 500); 
        }

    }


}
