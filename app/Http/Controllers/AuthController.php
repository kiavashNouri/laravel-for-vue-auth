<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required'],
        ]);
        try {
            $user=User::create([
                'name'=>$request->name,
                'email'=>$request->email,
                'password'=>Hash::make($request->password)
            ]);
           $token=$user->createToken('userToken')->plainTextToken;
            $message = "کاربر عزیز،شما با موفقیت ثبت نام شدید :)";

            $response=[
                'user'=>$user,
                'message'=>$message,
                'token'=>$token
            ];
            return \response()->json($response,\Symfony\Component\HttpFoundation\Response::HTTP_CREATED);
        }catch (QueryException $e) {
            $message=$e->getMessage();
            $response=[
              'message'=>$message
            ];
            return \response()->json($response,\Symfony\Component\HttpFoundation\Response::HTTP_INTERNAL_SERVER_ERROR);


        }
    }

    public function login(Request $request)
    {
       $credentials=$request->validate([
            'email' =>"required|email",
            'password' =>"required",
        ]);
       $user=User::where('email',$credentials['email'])->first();
       if(!$user||!Hash::check($credentials['password'],$user->password)){
           $response=[
             'message'=>'احتمالا شما ثبت نام نشدین!'
           ];
           return \response()->json($response,\Symfony\Component\HttpFoundation\Response::HTTP_UNAUTHORIZED);
       }else{
           $attempt=Auth::attempt(['email'=>$request->email,'password'=>$request->password]);
           if ($attempt){
               $token=$user->createToken('userToken')->plainTextToken;
               $response=[
                   'token'=>$token,
                   'user'=>$user
               ];
               return \response()->json($response,\Symfony\Component\HttpFoundation\Response::HTTP_OK);
           }else{
               throw ValidationException::withMessages([
                   'email' => ['The provided credentials are incorrect.'],
               ]);
           }

       }
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();
        Session::flush();
        return ['message' => 'You have been logged out'];
    }
}
