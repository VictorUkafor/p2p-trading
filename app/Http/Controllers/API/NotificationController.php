<?php

namespace App\Http\Controllers\API;

use App\Model\Notification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class NotificationController extends Controller {


    /**
     * @SWG\GET(
     *     path="/api/v1/notifications",
     *     summary="Returns notifications settings",
     *     description="Returns notifications settings",
     *     operationId="notifications",
     *     tags={"notifications"},
     *     @SWG\Response(
     *         response="200",
     *         description="Operation successfull"
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Internal server error"
     *     ),
     * )
     */ 


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


    /**
     * @SWG\POST(
     *     path="/api/v1/notifications/push",
     *     summary="Set push notification",
     *     description="Set push notification",
     *     operationId="push",
     *     tags={"notifications"},
     *     @SWG\Response(
     *         response="201",
     *         description="Operation successfull"
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Internal server error"
     *     ),
     * )
     */     


    public function push(Request $request){

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


    /**
     * @SWG\POST(
     *     path="/api/v1/notifications/email",
     *     summary="Set email notification",
     *     description="Set email notification",
     *     operationId="email",
     *     tags={"notifications"},
     *     @SWG\Response(
     *         response="201",
     *         description="Operation successfull"
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Internal server error"
     *     ),
     * )
     */    


    public function email(Request $request){

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


    /**
     * @SWG\POST(
     *     path="/api/v1/notifications/auto-logout",
     *     summary="Set auto logout",
     *     description="Set auto logout",
     *     operationId="autoLogout",
     *     tags={"notifications"},
     *     @SWG\Response(
     *         response="201",
     *         description="Operation successfull"
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Internal server error"
     *     ),
     * )
     */     

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
