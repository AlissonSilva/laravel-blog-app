<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{

    /**
     * Register
     * @OA\Post (
     *     path="/api/register/",
     *     tags={"User"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      type="object",
     *                      @OA\Property(
     *                          property="name",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="email",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="password",
     *                          type="password"
     *                      ),
     *                      @OA\Property(
     *                          property="password_confirmation",
     *                          type="password"
     *                      )
     *                 ),
     *                 example={
     *                     "name":"Admin",
     *                     "email":"admin@admin.com",
     *                     "password":"123456",
     *                     "password_confirmation":"123456"
     *                }
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="user", type="object",
     *                  @OA\Property(property="id", type="number", example=1),
     *                  @OA\Property(property="name", type="string", example="Admin"),
     *                  @OA\Property(property="email", type="string", example="admin@admin.com"),
     *                  @OA\Property(property="created_at", type="string", example="2022-12-11T09:25:53.000000Z"),
     *                  @OA\Property(property="updated_at", type="string", example="2022-12-11T09:25:53.000000Z"),
     *              ),
     *              @OA\Property(property="token", type="string", example="2|RlmF3V1BwHtXErI9md4OBu9ZvyjNmtCjt1Mnfv4f"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Content",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="The email has already been taken."),
     *          )
     *      )
     * )
     */

    public function register(Request $request){

        $attrs = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed'
        ]);

        $user = User::create([
            'name' => $attrs['name'],
            'email' => $attrs['email'],
            'password' => bcrypt($attrs['password'])
        ]);

        return response([
            'user' => $user,
            'token' => $user->createToken('secret')->plainTextToken
        ]);


    }


   /**
     * Login
     * @OA\Post (
     *     path="/api/login/",
     *     tags={"User"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      type="object",
     *                      @OA\Property(
     *                          property="email",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="password",
     *                          type="password"
     *                      )
     *                 ),
     *                 example={
     *                     "email":"admin@admin.com",
     *                     "password":"123456"
     *                }
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(
     *              @OA\Property(property="user", type="object",
     *                  @OA\Property(property="id", type="number", example=1),
     *                  @OA\Property(property="name", type="string", example="Admin"),
     *                  @OA\Property(property="email", type="string", example="admin@admin.com"),
     *                  @OA\Property(property="image", type="string", example="null"),
     *                  @OA\Property(property="created_at", type="string", example="2022-12-11T09:25:53.000000Z"),
     *                  @OA\Property(property="updated_at", type="string", example="2022-12-11T09:25:53.000000Z"),
     *              ),
     *              @OA\Property(property="token", type="string", example="2|RlmF3V1BwHtXErI9md4OBu9ZvyjNmtCjt1Mnfv4f"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Invalid credentials."),
     *          )
     *      )
     * )
     */
    public function login(Request $request)    {
        //validate fields
        $attrs = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        // attempt login
        if(!Auth::attempt($attrs))
        {
            return response([
                'message' => 'Invalid credentials.'
            ], 403);
        }

        //return user & token in response
        return response([
            'user' => auth()->user(),
            'token' => auth()->user()->createToken('secret')->plainTextToken
        ], 200);
    }

     /**
     * Logout
     * @OA\Post (
     *     path="/api/logout/",
     *     tags={"User"},
     *     security={{"Authorization": {}}},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(
     *                  @OA\Property(property="message", type="string", example="Logout success.")
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

    public function logout(){
        auth()->user()->tokens()->delete();
        return response([
            'message' => 'Logout success.'
        ],200);
    }


    /**
     * Get Detail User
     * @OA\Get (
     *     path="/api/user/",
     *     tags={"User"},
     *     security={{"Authorization": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="success",
     *          @OA\JsonContent(
     *              @OA\Property(property="user", type="object",
     *                  @OA\Property(property="id", type="number", example=1),
     *                  @OA\Property(property="name", type="string", example="Admin"),
     *                  @OA\Property(property="email", type="string", example="admin@admin.com"),
     *                  @OA\Property(property="image", type="string", example="null"),
     *                  @OA\Property(property="email_verified_at", type="string", example="null"),
     *                  @OA\Property(property="created_at", type="string", example="2022-12-11T09:25:53.000000Z"),
     *                  @OA\Property(property="updated_at", type="string", example="2022-12-11T09:25:53.000000Z"),
     *              )
     *          )
     *     )
     * )
     */

    public function user(){
        return response([
            'user' => auth()->user()
        ],200);
    }

     /**
     * Update
     * @OA\Put (
     *     path="/api/user/",
     *     tags={"User"},
     *     security={{"Authorization": {}}},
     *     @OA\Parameter(
     *         in="path",
     *         name="name",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         in="path",
     *         name="image",
     *         required=false,
     *         @OA\Schema(type="file")
     *     ),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         @OA\Schema(
     *             @OA\Property(
     *                  type="object",
     *                  @OA\Property(
     *                       property="name",
     *                       type="string"
     *                  ),
     *                  @OA\Property(
     *                       property="image",
     *                       type="file"
     *                  )
     *                 ),
     *             example={
     *                 "name":"Admin",
     *                 "image":"null"
     *              }
     *            )
     *         ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(
     *                  @OA\Property(property="message", type="string", example="User updated.")
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
    public function update(Request $request)
    {
        $attrs = $request->validate([
            'name' => 'required|string'
        ]);

        $image = $this->saveImage($request->image, 'profiles');

        auth()->user()->update([
            'name' => $attrs['name'],
            'image' => $image
        ]);

        return response([
            'message' => 'User updated.',
            'image' => $image
        ],200);
    }
}
