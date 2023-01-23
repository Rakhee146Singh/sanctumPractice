<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ResetPassword;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Mail\Message;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ResetPasswordController extends Controller
{
    public function send_reset_password_email(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);
        $email = $request->email;

        //Check user's mail exists or not
        $user = User::where('email', $email)->first();
        if (!$user) {
            return response([
                'message' => 'Email does not exists',
                'status' => 'failed'
            ], 404);
        }

        //generate token
        $token = Str::random(60);
        ResetPassword::create([
            'email' => $email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        // dump("http//127.0.0.1:8000/api/resetpassword" . $token);

        //Sending Email with Password Reset View
        Mail::send('reset', ['token' => $token], function (Message $message) use ($email) {
            $message->subject('Reset Your Password');
            $message->to($email);
        });

        return response([
            'message' => 'Password Reset Email succesfully',
            'status' => 'success'
        ], 404);
    }

    public function reset(Request $request, $token)
    {
        //Delete Token older than 1 minute
        $formatted = Carbon::now()->subMinutes(1)->toDateTimeString();
        ResetPassword::where('created_at', '<=', $formatted)->delete();

        $request->validate([
            'password' => 'required|confirmed',
        ]);
        $resetpassword = ResetPassword::where('token', $token)->first();
        if (!$resetpassword) {
            return response([
                'message' => 'Token is Invalid or expired',
                'status' => 'failed'
            ], 404);
        }
        $user = User::where('email', $resetpassword->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        //Delete the token after resetting the password
        ResetPassword::where('email', $user->email)->delete();
        return response([
            'message' => 'Password Reset Successfully',
            'status' => 'success'
        ], 200);
    }
}
