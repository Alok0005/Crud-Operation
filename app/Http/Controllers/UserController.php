<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use App\Models\User;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Crypt;
use PharIo\Manifest\Email;

class UserController extends Controller
{
    public static function register(LoginRequest $req)  
   {
        $emailExist = User::where('email',$req->input('email'))->first(); //first()method return only one record from the database
        if($emailExist){
            return  response()->json(['error'=> 'Email Already Exists'],400);
        }
        $user = new User;
        $user->name = $req->input('name');
        $user->email= $req->input('email');
        $user->password=Crypt::encrypt($req->input('password'));
        $user->save();
        $result = [
            'name' => $user->name,
            'email' => $user->email,
        ];
        return response()->json($result);
    }

    public static function login(LoginRequest $req)  
    {
         $emailExist = User::where('email',$req->input('email'))->first();
         if(!$emailExist){
             return  response()->json(['error'=> 'User not found'],400);
         }
         if($req->input('password') == Crypt::decrypt($emailExist->password)){
            $result = [
                'name' => $emailExist->name,
                'email' => $emailExist->email,
                'id' => $emailExist->id
            ];
            return response()->json($result);
         }else{
            return  response()->json(['error'=> 'Invalid Credentials'],400);
         }    
    }

    public static function update(LoginRequest $req)
    {
        $req->validate([
            'name'=> 'required',
            'password'=> 'required',
        ]);
        $user = User::find($req->id);
        $user->name = $req->name;
        $user->password = Crypt::encrypt($req->input('password'));
        $user->save();
        $result = [
            'name' => $user->name,
            'email' => $user->email,
        ];
        return response()->json([$result]);
    }

    public static function destroy(Request $req)
    {
        $userExist = User::where('id',$req->id)->first();
        if(!$userExist) return response()->json(['error'=> 'User not found']);
        User::where('id',$req->id)->delete();
        return response()->json('Deleted Succesfully');
    }

} 
