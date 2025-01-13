<?php 
 
namespace App\Http\Controllers; 
 
use App\Models\User;
 
//import resource PostResource 
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller; 
use Illuminate\Support\Facades\Validator;

class UserController extends Controller 
{     
    /** 
     * index 
     * 
     * @return void 
     */ 
    // public function index() 
    // { 
    //     //get all posts 
    //     $user = User::latest()->paginate(5); 
 
    //     //return collection of posts as a resource 
    //     return new UserResource(true, 'List Data Users', $user); 
    // } 

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'email' => 'required|unique:users',
            'password' => 'required',
            'role_id' => 'required|'.Rule::in(['1', '2']),
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
        ]);

        return response(['data' => $user]);
    }


    // public function login(Request $request)
    // {
    //     $request->validate([
    //         'email' => 'required|email',
    //         'password' => 'required|min:6',
    //     ]);

    //     $user = User::where('email', $request->email)->first();

    //     if ($user && Hash::check($request->password, $user->password)) {
    //         $token = $user->createToken('YourAppName')->plainTextToken;

    //         return response()->json([
    //             'message' => 'Login successful',
    //             'token' => $token,
    //         ]);
    //     }

    //     return response()->json([
    //         'message' => 'Invalid credentials',
    //     ], 401);
    // }

    // public function getUserInfo(Request $request)
    // {
    //     $user = User::where('email', $request->email)->first();

    //     return response()->json([
    //         'name' => $user->name,
    //     ]);
    // }
}