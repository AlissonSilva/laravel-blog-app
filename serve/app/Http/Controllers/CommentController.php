<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Post;


class CommentController extends Controller
{
    public function index($id)
    {
        $post = Post::find($id);

        if(!$post){
            return respnose([
                'message' => 'Post not found'
            ],403);
        }

        return response([
            'comment' => $post->comments()->with('user:id,name,image')->get()
        ],200);
    }

    public function store(Request $request, $id)
    {
        $post = Post::find($id);

        if(!$post){
            return respnose([
                'comment' => 'Post not found'
            ],403);
        }

        $attrs = $request->validate([
            'comment'=>'required|string'
        ]);

        Comment::create([
            'comment' => $attrs['comment'],
            'post_id' => $id,
            'user_id' => auth()->user()->id
        ]);

        return response([
            'message' => 'Comment created.'
        ],200);
    }

    public function update(Request $request, $id)
    {
        $comment = Comment::find($id);

        if(!$comment){
            return response([
                'message' => 'Comment not found.'
            ],403);
        }

        if($comment->user_id != auth()->user()->id){
            return response([
                'message' => 'Permission denid.'
            ], 403);
        }

        $attrs = $request->validate([
            'comment'=>'required|string'
        ]);

        $comment->update([
            'comment' => $attrs['comment']
        ]);

        return resonse([
            'message' => 'comment update'
        ], 200);
    }

    public function destroy($id)
    {
        $comment = Comment::find($id);

        if(!$comment){
            return response([
                'message' => 'Comment not found.'
            ],403);
        }

        if($comment->user_id != auth()->user()->id){
            return response([
                'message' => 'Permission denid.'
            ], 403);
        }

        $comment->delete();

        return response([
            'message', 'Comment delete'
        ],200);
    }
}
