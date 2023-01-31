<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\{
    User,
    Post
};
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Laravel\Passport\Passport;
use Laravel\Socialite\Facades\Socialite;
use Exception;
use Illuminate\Support\Facades\Auth;

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
        if(auth()->user()->role == 'SuperAdmin')
        {
            $data=new User;
        }
        else if(auth()->user()->role == 'Admin'){
            $data=User::where('role','Admin')
                ->orWhere('role','User');
        }
        $perpage = 5;
        $page = $request->input('page',1);
        $total = $data->count();
        $result = $data->offset(($page - 1 )* $perpage)->limit($perpage)->get(
            ['id',
            'name',
            'email',
            'role',
            'facebook_id'
            ]
        );
            foreach($result as $key => $value)
            {
                if(empty($result[$key]['facebook_id'])){
                    $result[$key]['facebook_id'] = "InActive";
                }
                else{
                    $result[$key]['facebook_id'] = "Active";
                }
            }
        // dd($result->facebook_id);
        $lastpage =  ceil($total/$perpage);
        if($page > $lastpage)
        {
            $result = "No Record Found";
            return response()->json([
                'Current_page' => $page,
                'user' => $result,
                'total' => $total,
                'last_page' => $lastpage
            ],404);
        }
        else{
            return response()->json([
                'Current_page' => $page,
                'user' => $result,
                'total' => $total,
                'last_page' => $lastpage
            ],200);
        }
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
            'email' => 'required|unique:users|email',
            'password' => 'required|min:6',
            'role' => 'required',
        ],[
            'name.required' => 'Name is must.',
            'name.min' => 'Name must have 5 char.',
        ]);
        if($validate->fails()){
        return response()->json([
            'message' =>$validate->errors(),
        ],412);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role
        ]);
        return response()->json([
            'message' =>'User Created Successfully',
            'user' => $user->orderBy('id','desc')->first()
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
        ],412);
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

//* <-----------------------This Route Upload the Image on database------------------------------>

public function upload(Request $request){
    $validate = Validator::make($request->all(), [
        'title' => 'required|unique:posts|min:3',
        'desc' => 'required',
        'image' => 'mimes:png,jpg',
    ]);
    if($validate->fails()){
        return response()->json([
            'message' =>$validate->errors(),
        ],412);
    }

    if($request->hasFile('image'))
    {
        $imageName = time().'.'.$request->image->extension();
        // dd($imageName);
        $request->image->storeAs('public/images/', $imageName);
        $post = Post::create([
            'user_id' => auth()->user()->id,
            'title' => $request->title,
            'desc' => $request->desc,
            'image' => $imageName,
        ]);
    }
    else{
        $post = Post::create([
            'user_id' => auth()->user()->id,
            'title' => $request->title,
            'desc' => $request->desc,
        ]);
    }

        return response()->json([
            'message' =>'User Created Successfully',
            'user' => $post->orderBy('id','desc')->first()
        ],201);

}

//* <-----------------------This Route get all the Images of user from database------------------------------>

    Public Function getupload(){
        if(auth()->user()->role == 'SuperAdmin')
        {
            $data =Post::get();
        }
        else{
            $data = Post::where('user_id',auth()->user()->id)->get(['title','desc','image']);
        }
        return response()->json([
            'message' =>'Success',
            'user' => $data
        ],200);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    //* <-----------------------This Route Update The Existing User Name Email Passsword of Active User ------------------------------>
    public function update(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required|min:5',
            'email' => 'required|unique:users|email',
            'password' => 'required|min:6',
            'role' => 'required',
        ],[
            'name.required' => 'Name is must.',
            'name.min' => 'Name must have 5 char.',
        ]);
        if($validate->fails()){
            return response()->json([
                'message' =>$validate->errors(),
            ],412);
        }
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
            ],200);
        }
        else{
            return response()->json([
                'message'=>'User Not Found'
            ],404);
        }
    }

    //* <-----------------------This Route Update The Existing User Name Email Passsword of Selected User ------------------------------>
    public function updateAdminUser(Request $request,$id)
    {

        $validate = Validator::make($request->all(), [
            'name' => 'required|min:5',
            'email' => 'required|unique:users|email',
            'password' => 'required|min:6',
            'role' => 'required',
        ],[
            'name.required' => 'Name is must.',
            'name.min' => 'Name must have 5 char.',
        ]);

        if($validate->fails()){
            return response()->json([
                'message' =>$validate->errors(),
            ],412);
        }
        $data = User::find($id);
        if(!empty($data))
        {
            if($data->role == 'SuperAdmin'){
                return response()->json([
                    'message'=>'You Dont Have Permission To Update This User'
                ],401);
            }
            else{

                $user = [
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'role' => $request->role
                ];
                // $data -> name = $request->name;
                // $data -> email = $request->email;
                // $data -> password = Hash::make($request->password);
                $data -> update($user);
                return response()->json([
                    'message'=>'User Upated Successful'
                ],200);
            }
        }
        else{
            return response()->json([
                'message'=>'User Not Found'
            ],404);
        }
    }

    Public Function updatepost(Request $request,$id)
    {
        $user = auth()->user()->id;
        $data = Post::where('user_id',$user)
                ->find($id);

        $validate = Validator::make($request->all(), [
                'title' => 'required|unique:posts|min:3',
                'desc' => 'required',
                'image' => 'mimes:png,jpg',
            ]);
            if($validate->fails()){
                return response()->json([
                    'message' =>$validate->errors(),
                ],412);
            }
            if($request->hasFile('image'))
             {

                unlink(public_path('storage/images/'.$data->image));
                $imageName = time().'.'.$request->image->extension();
                // dd($imageName);
                $request->image->storeAs('public/images/', $imageName);
                $post = [
                    'title' => $request->title,
                'desc' => $request->desc,
                'image' => $imageName
                ];
                $d=$data->update($post);
                if($d == 0){
                    return response()->json([
                        'message'=>'Post not Available in Your Account'
                    ],404);
                }
                else{
                    return response()->json([
                        'message'=>'Post Updated Successful'
                    ],201);
                }

            }
            else{
                $post = [
                    'title' => $request->title,
                    'desc' => $request->desc,
                ];
                $d=$data->update($post);
                if($d == 0)
                {
                    return response()->json([
                        'message'=>'Post not Available in Your Account'
                    ],404);
                }
                else{
                    return response()->json([
                        'message'=>'Post Updated Successful'
                    ],201);
                }
            }

    }

    Public Function search(Request $request){
        $validate = Validator::make($request->all(), [
            'title' => 'required',
        ]);
        if($validate->fails()){
            return response()->json([
                'message' =>$validate->errors(),
            ],412);
        }
        $id = auth()->user()->id;
        if(auth()->user()->role == 'SuperAdmin')
        {
            $post = Post::where('title','like','%'.$request->title.'%')->get();
        }
        else{
            $post = Post::where('user_id',$id)
                    ->where('title','like','%'.$request->title.'%')->get(['title','desc','image']);
        }

        if($post != null)
        {
            return response()->json([
                'message'=>'Success',
                'post' => $post
            ],200);
        }
        else{
            return response()->json([
                'message'=>'Post Not Found',
            ],404);
        }


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    //* <-----------------------This Route Delete the Active User ------------------------------>
    public function destroy()
    {
        $id = auth()->user()->id;
        $data = User::find($id);
        if(!empty($data)){
            $data -> delete();
            return response()->json([
                'message'=>'User Deleted Successful'
            ],202);
        }
        else{
            return response()->json([
                'message'=>'User Not Found'
            ],404);
        }
    }

    //* <-----------------------This Route Delete the Selected User ------------------------------>
    public function destroyAdminUser($id)
    {
        // $id = auth()->user()->id;
        $data = User::find($id);
        if(!empty($data)){
            if($data->role == 'SuperAdmin'){
                return response()->json([
                    'message'=>'You Dont Have Permission To Delete This User'
                ],401);
            }
            else{
                $data -> delete();
                return response()->json([
                    'message'=>'User Deleted Successful'
                ],202);
            }
        }
        else{
            return response()->json([
                'message'=>'User Not Found'
            ],404);
        }
    }

    //* <-----------------------This Route Logout the Current User ------------------------------>
    public function logout()
    {
       $id = auth()->user()->id;
       Passport::token()->where('user_id',$id)->delete();

       return response()->json([
           'Message' => 'User Logout Successful'
       ],200);
    }






    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback()
    {
        try {

            $user = Socialite::driver('facebook')->user();

            // dd($user);

            $finduser = User::where('facebook_id', $user->id)->first();

            if($finduser){

                Auth::login($finduser);

                return redirect()->intended('/');

            }else{
                $newUser = User::updateOrCreate(['email' => $user->email],[
                        'name' => $user->name,
                        'facebook_id'=> $user->id,
                        'password' => Hash::make('123456')
                    ]);

                Auth::login($newUser);

                return redirect()->intended('/');
            }

        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }


}
