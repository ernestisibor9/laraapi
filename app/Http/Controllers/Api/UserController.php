<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * @OA\Info(
 *     title="User Authentication API",
 *     version="1.0.0",
 *     description="This is the API documentation for User Management.",
 *     @OA\Contact(
 *         email="support@example.com"
 *     ),
 *     @OA\License(
 *         name="Apache 2.0",
 *         url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *     )
 * )
 */



class UserController extends Controller
{
/**
 * @OA\Post(
 *     path="/api/register",
 *     summary="User Registration",
 *     description="Registers a new user with the provided details.",
 *     operationId="registerUser",
 *     tags={"Authentication"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 type="object",
 *                 required={"name", "email", "password", "phone", "photo"},
 *                 @OA\Property(property="name", type="string", description="Full name of the user"),
 *                 @OA\Property(property="email", type="string", format="email", description="Email address of the user"),
 *                 @OA\Property(property="password", type="string", format="password", description="Password for the user account"),
 *                 @OA\Property(property="phone", type="string", description="Phone number of the user"),
 *                 @OA\Property(property="photo", type="string", format="binary", description="Profile photo of the user")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="User registered successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="User registered successfully"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", description="User ID"),
 *                 @OA\Property(property="name", type="string", description="User's name"),
 *                 @OA\Property(property="email", type="string", description="User's email")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validation errors",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Validation errors occurred"),
 *             @OA\Property(property="errors", type="object", additionalProperties={"type": "string"})
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="An error occurred during registration")
 *         )
 *     )
 * )
 */
    // Register
    public function Register(Request $request){
        // Validate the request
        $request->validate([
            'name' =>'required|string|max:255',
            'email' =>'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:2',
            'phone' => 'required',
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:3048',
        ]);

        if ($request->file('photo')) {

        // Upload the photo
            $image = $request->file('photo');
            $filename = date('YmdHi') . $image->getClientOriginalName();
            $image->move(public_path('upload/user_images/'), $filename);

            $save_url = 'upload/user_images/' . $filename;

        // Create the user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'photo' => $save_url
        ]);
        // Response
        return response()->json([
            'status' => true,
            'message' => 'User registered successfully',
            'user' => $user
        ]);
        }
        return response()->json([
            'error' => 'Failed to upload photo'
        ], 400);
    }

    /**
 * @OA\Post(
 *     path="/api/login",
 *     summary="User Login and Token Generation",
 *     description="Authenticates a user using email and password, and returns a generated access token.",
 *     operationId="loginAndGenerateToken",
 *     tags={"Authentication"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email", "password"},
 *             @OA\Property(property="email", type="string", format="email", description="User's email address"),
 *             @OA\Property(property="password", type="string", format="password", description="User's password")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Login successful",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Login successful"),
 *             @OA\Property(property="access_token", type="string", description="Generated JWT access token"),
 *             @OA\Property(property="token_type", type="string", example="Bearer", description="Type of the token")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized - Invalid credentials",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Invalid credentials")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validation errors",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Validation errors occurred"),
 *             @OA\Property(property="errors", type="object", additionalProperties={"type": "string"})
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="An error occurred during login")
 *         )
 *     )
 * )
 */
    // Login
    public function Login(Request $request){
        // Validate the request
        $request->validate([
            'email' =>'required|string|email|max:255',
            'password' => 'required|string|min:2',
        ]);

        // JWTAuth
        $token = JWTAuth::attempt([
            'email' => $request->email,
            'password' => $request->password,
        ]);
        if (!empty($token)) {
            // Response
            return response()->json([
            'status' => true,
            'message' => 'User login successfully',
            'token' => $token
        ]);
        }
        // Response
        return response()->json([
            'status' => false,
            'message' => 'Invalid credentials',
        ]);
    }

    /**
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Use 'Bearer <JWT>' as the value for the Authorization header."
 * )
 */

/**
 * @OA\Get(
 *     path="/api/profile",
 *     summary="Get Authenticated User Profile",
 *     description="Fetches the profile details of the currently authenticated user.",
 *     operationId="getUserProfile",
 *     tags={"User"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Authenticated user profile retrieved successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="User profile retrieved successfully"),
 *             @OA\Property(property="user", type="object",
 *                 @OA\Property(property="id", type="integer", example=1, description="User ID"),
 *                 @OA\Property(property="name", type="string", example="John Doe", description="User's name"),
 *                 @OA\Property(property="email", type="string", example="johndoe@example.com", description="User's email address"),
 *                 @OA\Property(property="phone", type="string", example="+1234567890", description="User's phone number"),
 *                 @OA\Property(property="photo", type="string", example="https://example.com/photos/johndoe.jpg", description="URL to the user's profile photo")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized - User not authenticated",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Unauthenticated user")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="An error occurred while retrieving the profile")
 *         )
 *     )
 * )
 */
    // Profile
    public function Profile(){
        $userData = auth()->user();

        return response()->json([
           'status' => true,
           'message' => 'User profile',
            'user' => $userData
        ]);
    }
    // RefreshToken
    public function RefreshToken(){
        $newToken = auth()->refresh();

        return response()->json([
           'status' => true,
           'message' => 'Token refreshed successfully',
            'token' => $newToken
        ]);
    }
    // Logout
    public function Logout(){
        auth()->logout();

        return response()->json([
           'status' => true,
           'message' => 'User logged out successfully'
        ]);
    }
}
