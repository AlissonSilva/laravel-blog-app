<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Post;

class PostController extends Controller
{

    /**
     * Get post
     * @OA\Get (
     *     path="/api/posts/",
     *     tags={"Post"},
     *     security={{"Authorization": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="success",
     *          @OA\JsonContent(
     *              @OA\Property(property="posts", type="object",
     *                  @OA\Property(property="id", type="number", example=1),
     *                  @OA\Property(property="user_id", type="number", example=1),
     *                  @OA\Property(property="body", type="string", example="First Post!"),
     *                  @OA\Property(property="image", type="files", example=null),
     *                  @OA\Property(property="created_at", type="string", example="2022-12-11T09:25:53.000000Z"),
     *                  @OA\Property(property="updated_at", type="string", example="2022-12-11T09:25:53.000000Z"),
     *                  @OA\Property(property="comments_count", type="number", example=0),
     *                  @OA\Property(property="likes_count", type="number", example=1),
     *                  @OA\Property(property="user", type="object",
     *                      @OA\Property(property="id", type="number", example=1),
     *                      @OA\Property(property="name", type="string", example="Admin"),
     *                      @OA\Property(property="image", type="files", example=null),
     *                  ),
     *                  @OA\Property(property="likes", type="array",
     *                    @OA\Items(
     *                         @OA\Property(property="id", type="number", example=1),
     *                          @OA\Property(property="user_id", type="number", example=1),
     *                          @OA\Property(property="post_id", type="number", example=1),
     *                      ),
     *                  ),
     *              ),
     * 
     *          ),
     * 
     *     ),
     * 
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated"),
     *          )
     *      )
     * )
     */
    public function index()
    {
        return response([
            'posts' => Post::orderBy('created_at', 'desc')->with('user:id,name,image')->withCount('comments', 'likes')
            ->with('likes', function($like){
                return $like->where('user_id', auth()->user()->id)
                    ->select('id', 'user_id', 'post_id')->get();
            })
            ->get()
        ], 200);
    }

    /**
     * Get unique post
     * @OA\Get (
     *     path="/api/posts/{id}",
     *     tags={"Post"},
     *     security={{"Authorization": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="success",
     *          @OA\JsonContent(
     *              @OA\Property(property="post", type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="id", type="number", example=2),
     *                      @OA\Property(property="user_id", type="number", example=1),
     *                      @OA\Property(property="body", type="string", example="First Post!"),
     *                      @OA\Property(property="image", type="files", example=null),
     *                      @OA\Property(property="created_at", type="string", example="2022-12-11T09:25:53.000000Z"),
     *                      @OA\Property(property="updated_at", type="string", example="2022-12-11T09:25:53.000000Z"),
     *                      @OA\Property(property="comments_count", type="number", example=0),
     *                      @OA\Property(property="likes_count", type="number", example=1)
     *                  ),
     *              ),
     * 
     *          ),
     *      ),
     * 
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated"),
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        return response([
            'post' => Post::where('id',$id)->withCount('comments','likes')->get()
        ],200);
    }

    /**
     * Register new post
     * @OA\Post (
     *     path="/api/posts/",
     *     tags={"Post"},
     *     security={{"Authorization": {}}},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      type="object",
     *                      @OA\Property(
     *                          property="body",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="image",
     *                          type="file"
     *                      )
     *                 ),
     *                 example={
     *                     "body":"First Post!",
     *                     "image":null
     *                }
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Post created"),
     *              @OA\Property(property="post", type="object",
     *                  @OA\Property(property="body", type="string", example="First Post!"),
     *                  @OA\Property(property="user_id", type="number", example=1),
     *                  @OA\Property(property="image", type="files", example=null),
     *                  @OA\Property(property="updated_at", type="string", example="2022-12-18T14:57:47.000000Z"),
     *                  @OA\Property(property="created_at", type="string", example="2022-12-18T14:57:47.000000Z"),
     *                  @OA\Property(property="id", type="number", example=2)
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated"),
     *          )
     *      )
     * )
     */
    public function store(Request $request)
    {
        $attrs = $request->validate([
            'body'=>'required|string'
        ]);

        $image = $this->saveImage($request->image, 'posts');

        $post = Post::create([
            'body' => $attrs['body'],
            'user_id' => auth()->user()->id,
            'image' => $image
        ]);

        return response([
            'message' => 'Post created',
            'post' => $post
        ],200);
    }

     /**
     * Update post
     * @OA\Put (
     *     path="/api/posts/{id}",
     *     tags={"Post"},
     *     security={{"Authorization": {}}},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         @OA\Schema(
     *             @OA\Property(
     *                  type="object",
     *                  @OA\Property(
     *                       property="body",
     *                       type="string"
     *                  )
     *                 ),
     *             example={
     *                 "body":"First post! Show update!"
     *              }
     *            )
     *         ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Post updated"),
     *              @OA\Property(property="post", type="object",
     *                  @OA\Property(property="id", type="number", example=2),
     *                  @OA\Property(property="user_id", type="number", example=1),
     *                  @OA\Property(property="body", type="string", example="First post! Show update!"),
     *                  @OA\Property(property="image", type="files", example=null),
     *                  @OA\Property(property="created_at", type="string", example="2022-12-18T14:57:47.000000Z"),
     *                  @OA\Property(property="updated_at", type="string", example="2022-12-18T15:07:12.000000Z")
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated"),
     *          )
     *      )
     * )
     */
    public function update(Request $request, $id)
    {
        $post = Post::find($id);

        if(!$post){
            return response([
                'message'=>'Post not found.'
            ], 403);
        }

        if($post->user_id != auth()->user()->id){
            return response([
                'message'=>'Permission denied.'
            ], 403);
        }

        $attrs = $request->validate([
            'body'=>'required|string'
        ]);

        $post->update([
            'body' => $attrs['body']
        ]);

        return response([
            'message' => 'Post updated',
            'post' => $post
        ],200);
    }

    /**
     * Delete post
     * @OA\Delete (
     *     path="/api/posts/{id}",
     *     tags={"Post"},
     *     security={{"Authorization": {}}},
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="int")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="success",
     *         @OA\JsonContent(
     *             @OA\Property(property="msg", type="string", example="Post deleted.")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $post = Post::find($id);

        if(!$post){
            return response([
                'message'=>'Post not found.'
            ], 403);
        }

        if($post->user_id != auth()->user()->id){
            return response([
                'message'=>'Permission denied.'
            ], 403);
        }

        $post->comments()->delete();
        $post->likes()->delete();
        $post->delete();

        return response([
            'message' => 'Post deleted.'
        ], 200);
    }
}
