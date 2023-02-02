<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Validator;

class Postcontroller extends Controller
{


//* <-----------------------This Route update Post from database------------------------------>

    Public Function updatepost(Request $request,$id)
    {
        $user = auth()->user()->id;
        $data = Post::where('user_id',$user)
                ->find($id);

        if($data != null)
        {

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
                        if(empty($data)){

                        }
                        else{
                        $d=$data->update($post);
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
                    return response()->json([
                        'message'=>'Post Updated Successful'
                    ],201);
        }
    }
        else{
            return response()->json([
                'message'=>'Post not Available in Your Account'
            ],404);
        }

    }
    //* <-----------------------This Route Search Post from database------------------------------>

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
    //* <-----------------------This Route Upload the Post on database------------------------------>

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

//* <-----------------------This Route get all the Post of user from database------------------------------>

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
}
