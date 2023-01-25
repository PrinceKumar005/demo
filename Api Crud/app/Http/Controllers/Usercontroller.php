<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Auth;
use Validator;


class Usercontroller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    //* <-----------------------This Route get all the User Information That create account ------------------------------>
    public function index(Request $request)
    {
        $data=new User;
        $perpage = 5;
        $page = $request->input('page',1);
        $total = $data->count();
        $result = $data->offset(($page - 1 )* $perpage)->limit($perpage)->get(['id','name','email']);
        return response()->json([
            'user' => $result,
            'total' => $total,
            'page' => $page,
            'last_page' => ceil($total/$perpage)
        ],200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
//* <-----------------------This Route Create New User ------------------------------>
    public function create(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required|min:5',
            'email' => 'required|unique:users',
            'password' => 'required|min:6',
        ],[
            'name.required' => 'Name is must.',
            'name.min' => 'Name must have 5 char.',
        ]);
        if($validate->fails()){
        return response()->json([
            'message' =>$validate->errors(),
        ]);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);
        return response()->json([
            'message' =>'User Created Successfully',
            'user' => $user
        ],201);
    }

    /**
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
//* <-----------------------This Route Login User and Provide them Api Key ------------------------------>
    public function login(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if($validate->fails()){
        return response()->json([
            'message' =>$validate->errors(),
        ]);
        }
        if(Auth::attempt($request->only('email','password')))
        {
            $user = auth()->user();
            $success['token']=$user->createToken('MyApp')->accessToken;
            $success['name']=$user->name;
            return response()->json([$success],200);
        }
        else{
            return response()->json([
                'message'=>'Check login credentials and try again'
            ],401);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
//* <-----------------------This Route Show Only Selected User ------------------------------>
    public function show($id)
    {
        $data = User::where('id',$id)->get();
        if(!empty($data))
        {
            return response()->json([
                'user' => $data
            ],200);
        }
        else{
            return response()->json([
                'message' => 'User Not Found'
            ],404);
        }
    }

    /**
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
//* <-----------------------This Route Provide The Information Of Login User------------------------------>
    public function userinfo()
    {
        $id = auth()->user()->id;
        $data = User::where('id',$id)->get();
        if(!empty($data))
        {
            return response()->json([
                'user' => $data,
                'message' => 'Login User Credentials'
            ],200);
        }
        else{
            return response()->json([
                'message' => 'User Not Found'
            ],404);
        }
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    //* <-----------------------This Route Update The Existing User Name Email Passsword of Selected User ------------------------------>
    public function update(Request $request)
    {
        $id = auth()->user()->id;
        $data = User::where('id',$id);
        if(!empty($data))
        {
            $user = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ];
            // $data -> name = $request->name;
            // $data -> email = $request->email;
            // $data -> password = Hash::make($request->password);
            $data -> update($user);
            return response()->json([
                'message'=>'User Upated Successful'
            ],201);
        }
        else{
            return response()->json([
                'message'=>'User Not Found'
            ],404);
        }
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    //* <-----------------------This Route Delete the Selected User ------------------------------>
    public function destroy($id)
    {
        // $id = auth()->user()->id;
        $data = User::find($id);
        if(!empty($data)){
            $data -> delete();
            return response()->json([
                'message'=>'User Deleted Successful'
            ],200);
        }
        else{
            return response()->json([
                'message'=>'User Not Found'
            ],404);
        }
    }
}
