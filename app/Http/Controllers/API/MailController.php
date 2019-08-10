<?php

namespace App\Http\Controllers\API;

use App\Model\Mail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JD\Cloudder\Facades\Cloudder;


class MailController extends Controller {


    /**
     * @SWG\POST(
     *     path="/api/v1/mail-us",
     *     summary="Sends email to the admin",
     *     description="Sends email to the admin for special actions",
     *     operationId="create",
     *     tags={"mail"},
     *     @SWG\Parameter(
     *         name="name",
     *         in="query",
     *         description="The name of the user",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="email",
     *         in="query",
     *         description="The email of the user",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="subject",
     *         in="query",
     *         description="The subject of the email",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="message",
     *         in="query",
     *         description="The message of the email",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response="201",
     *         description="Operation successfull"
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Internal server error"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Invalid input field"
     *     ),
     * )
     */  

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
