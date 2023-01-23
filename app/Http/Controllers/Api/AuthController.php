<?php

namespace App\Http\Controllers\Api;

use App\Models\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'confirm_password' => 'required|same:password'
        ]);

        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->errors()
            ];
            return response()->json($response, 400);
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $auth = Auth::create($input);

        $success['token'] = $auth->createToken("Myapp")->plainTextToken;
        $success['name'] = $auth->name;

        $response = [
            'success' => true,
            'data' => $success,
            'message' => 'Registered Successfully'
        ];

        return response()->json($response, 200);
    }

    public function login(Request $request)
    {
        if (FacadesAuth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $auth = Auth::user();
            $success['token'] = $auth->createToken("Myapp")->plainTextToken;
            $success['name'] = $auth->name;

            $response = [
                'success' => true,
                'data' => $success,
                'message' => 'Registered Successfully'
            ];
        }
    }
}
