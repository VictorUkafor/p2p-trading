<?php

namespace App\Http\Controllers\API;

use App\Model\Notification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class NotificationController extends Controller
{
    public function notifications(Request $request){

        $notifications = $request->user->notifications;

        if($notifications){
            return response()->json([
                'notifications' => $notifications
            ], 200); 
        }

        return response()->json([
            'errorMessage' => 'Notifications has not been set',
        ], 404); 

    }


    public function pushNotification(Request $request){

        $notification = $request->user->notifications;

        if(!$notification){
            $notification = new Notification; 
            $notification->user_id = $request->user->id; 
        }


        $previous_value = $notification->push_notification ? 
        $notification->push_notification : false;

        $notification->push_notification = $request->push_notification ? 
        $request->push_notification : $previous_value;

        if($notification->save()){
            return response()->json([
                'successMessage' => 'notification set successfully',
                'notification' => $notification
            ], 201);
        }
        
        return response()->json([
            'errorMessage' => 'Internal server error',
        ]   , 500); 

    }


    public function emailNotification(Request $request){

        $notification = $request->user->notifications;

        if(!$notification){
            $notification = new Notification; 
            $notification->user_id = $request->user->id; 
        }

        $previous_value = $notification->email_notification ? 
        $notification->email_notification : true;

        $notification->email_notification = $request->email_notification ? 
        $request->email_notification : $previous_value;

        if($notification->save()){
            return response()->json([
                'successMessage' => 'notification set successfully',
                'notification' => $notification
            ], 201);
        }
        
        return response()->json([
            'errorMessage' => 'Internal server error',
        ]   , 500); 

    }


    public function autoLogout(Request $request){

        $notification = $request->user->notifications;

        if(!$notification){
            $notification = new Notification; 
            $notification->user_id = $request->user->id; 
        }

        $previous_value = $notification->email_notification ? 
        $notification->email_notification : true;

        $notification->auto_logout = $request->auto_logout ? 
        $request->auto_logout : $previous_value;

        if($notification->save()){
            return response()->json([
                'successMessage' => 'notification set successfully',
                'notification' => $notification
            ], 201);
        }
        
        return response()->json([
            'errorMessage' => 'Internal server error',
        ]   , 500); 

    }


}
