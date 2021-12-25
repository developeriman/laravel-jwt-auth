<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB; 
use Carbon\Carbon; 
use App\Models\User; 
use Mail; 
use App\Mail\ForgotPasswordMail;
use Hash;
use Illuminate\Support\Str;
  

class ForgotPasswordController extends Controller
{
       public function forgetPassword(Request $request)
      {
          $request->validate([
              'email' => 'required|email|exists:users',
          ]);
  
          $token = Str::random(64);
  
          DB::table('password_resets')->insert([
              'email' => $request->email, 
              'token' => $token, 
              'created_at' => Carbon::now()
            ]);
          $details = [
            'subject'=> 'Reset password',
            'token'=>$token
          ];
        
        Mail::to($request->email)->send(new ForgotPasswordMail($details));
        return response()->json([
            'success'=>true,
            'message'=> 'We have e-mailed your password reset token!'
        ]); 
  
        // return response()->json([
        //     'success'=>true,
        //     'message'=> 'We have e-mailed your password reset token!'
        // ]); 
      }

         public function resetPassword(Request $request)
      {
          $request->validate([
              'email' => 'required|email|exists:users',
              'password' => 'required|string|min:6|confirmed',
              'password_confirmation' => 'required'
          ]);
  
          $updatePassword = DB::table('password_resets')
                              ->where([
                                'token' => $request->token,
                                'email' => $request->token,
                              ])
                              ->first();
  
          if(!$updatePassword){
                return response()->json([
                'success'=>false,
                'error'=> 'Invalid token!'
            ]); 
          }
  
          $user = User::where('email', $request->email)
                      ->update(['password' => Hash::make($request->password)]);
 
          DB::table('password_resets')->where(['email'=> $request->email])->delete();

            return response()->json([
                'success'=>true,
                'message'=> 'Your password has been changed!'
            ]);
    
      }
}
