<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'registerAdviser', 'register', 'registerUser']]);
    }
    /**
     * Get a JWT via given credentials.
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid Inputs',
                'error' => $validator->errors()
            ], 422);
        }
        if (!$token = auth('api')->attempt($validator->validated())) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid Credentials',
            ], 400);
        }


        return $this->respondWithToken($token);
    }
    /**
     * Register a User.
     * @return \Illuminate\Http\JsonResponse
     */

    //Registrar Administrador
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:3|max:50',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid Inputs',
                'error' => $validator->errors()
            ], 401);
        }
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();

        //Asignar todos los roles al Administrador
        $roles = Role::whereIn('roleName', ['admin', 'adviser', 'user'])->get();
        $user->roles()->attach($roles);

        return response()->json([
            'status' => true,
            'message' => 'Admin successfully registered',
            'user' => $user
        ], 201);
    }

    //Registrar Estudiante
    public function registerUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:3|max:50',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid Inputs',
                'error' => $validator->errors()
            ], 401);
        }
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();

        //Asignar rol de Estudiante
        $roles = Role::where('roleName', ['user'])->first();
        $user->roles()->attach($roles);


        return response()->json([
            'status' => true,
            'message' => 'User successfully registered',
            'user' => $user
        ], 201);
    }


    //Registrar Asesor y/o profesor
    public function registerAdviser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:3|max:50',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid Inputs',
                'error' => $validator->errors()
            ], 401);
        }
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();

        //Asignar rol de Asesor
        $roles = Role::where('roleName', ['adviser'])->first();
        $user->roles()->attach($roles);


        return response()->json([
            'status' => true,
            'message' => 'Adviser successfully registered',
            'user' => $user
        ], 201);
    }
    /**
     * Log the user out (Invalidate the token).
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        try {
            auth('api')->logout();
            return response()->json([
                'status' => true,
                'message' => 'Successfully logged out'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Sorry, cannot logout'
            ], 500);
        }
    }
    /**
     * Get the authenticated User.
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile(Request $request)
    {
        return response()->json([
            'status' => true,
            'message' => 'User found',
            'data' => auth('api')->user()
        ], 200);
    }
    /**
     * Refresh a token.
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh(Request $request)
    {
        return $this->respondWithToken(auth('api')->refresh());
    }
    /**
     * Get the token array structure.
     * @param string $token
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {

        $user = auth('api')->user();
        $user->load('roles'); // Carga la relación "roles"
        $roles = $user->roles->pluck('roleName')->toArray(); // Accede a la relación "roles"

        $minutes = auth('api')->factory()->getTTL() * 60;
        $timestamp = now()->addMinute($minutes);
        $expires_at = date('M d, Y H:i A', strtotime($timestamp));
        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_at' => $expires_at,
            'roles' => $roles
        ], 200);
    }
}
