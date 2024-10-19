<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Otp;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Twilio\Rest\Client;


class AuthController extends Controller {

    public function register(Request $request) {
   
        $input = Validator::make($request->all(), [
            'name'=>'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'phone' => 'required|numeric'
        ]); 
        
        if ($input->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $input->errors()
            ], 422);
        }
        $user = User::create([
            'name'=>$request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'phone' => $request->phone,
        ]);

        $token = $user->createToken('Personal Access Token')->accessToken;

        return response()->json(['token' => $token], 201);
    }

    public function login(Request $request) {
        
        $input = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($input->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $input->errors()
            ], 422);
             }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {

            $token = Auth::user()->createToken('MyApp')->plainTextToken;

            return response()->json(['token' => $token], 200);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

    }

    public function sendOtp(Request $request)
{
    $input = Validator::make($request->all(), [
        'phone' => 'required|numeric'
        ]);

    if ($input->fails()) {
        return response()->json([
            'status' => 'error',
            'errors' => $input->errors()
        ], 422);
         } 
    $user = User::where('phone' , '=' ,$request->phone )->first();

    if(!$user)
    return response()->json(['This phone not belong to anny user'], 404);


    

    $client = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
    $otp = rand(100000, 999999);

    try{
        $client->messages->create(
            $request->phone,
            [
                'from' => env('TWILIO_PHONE_NUMBER'),
                'body' => "Your OTP is $otp",
            ]
        );

        $otp = Otp::create([
            'user_id'=>$user->id,
            'otp_code' => $otp,
            'expires_at' => now()->addMinutes(5),
        ]);


        return response()->json(['Message sent successfully!'], 200);
    }catch (Twilio\Exceptions\RestException $e) {
        return response()->json(["Error: " . $e->getMessage()], 401);
        
    }
    
}

public function verifyOtp(Request $request) {
    $input = Validator::make($request->all(), [
        'phone' => 'required|numeric',
        'otp_code'=>'required'
        ]);

    if ($input->fails()) {
        return response()->json([
            'status' => 'error',
            'errors' => $input->errors()
        ], 422);
         } 


             $otp = DB::table('otps')
              ->select('otps.*')
              ->leftJoin('users', 'users.id', '=', 'otps.user_id')
              ->where('expires_at', '>', now())
              ->where('users.phone','=',$request->phone)->first();

    if ($otp) {
        $otp->update(['used_at' => now()]);
        return response()->json(['message' => 'OTP verified'], 200);
    }
    return response()->json(['error' => 'Invalid OTP'], 400);
}


}
