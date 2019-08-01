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
            $notification->push_notification = false;
        }

        if($notification){
            $notification->push_notification = !$notification->push_notification;
        }


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


        $notification->email_notification = !$notification->email_notification;

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
            $notification->auto_logout = false;
        }

        if($notification){
            $notification->auto_logout = !$notification->auto_logout;
        }


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
