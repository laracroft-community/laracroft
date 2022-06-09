<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Repository\Repository;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * @var User
     */
    private $model;

    /**
     * @var Repository
     */
    private $repository;

    public function __construct()
    {
        $this->model = new User();
        $this->repository = new Repository($this->model);
    }


    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        $request['password'] = Hash::make($request['password']);
        $request['remember_token'] = Str::random(10);
        $user = User::create($request->toArray());
        $token = $user->createToken('Laravel Password Grant Client')->accessToken;
        $response = ['token' => $token];
        return response($response, 200);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->respondBadRequest(['errors' => $validator->errors()->all()]);
        }

        $user = User::where('username', $request->username)
            ->with('permissions', 'roles.permissions')
            ->first();

        $badResponse = [
            'user' => null,
            'login_response' => [
                'authenticated' => false,
                'double_auth_enabled' => false,
                'token' => null
            ],
        ];
        
        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                $token = $user->createToken('bob')->accessToken;
                $response = [
                    'user' => $this->getUser($user),
                    'login_response' => [
                        'authenticated' => true,
                        'double_auth_enabled' => false,
                        'token' => $token
                    ],
                ];
                return $this->loginResponse($response);
            } else {
                // $response = ["Password mismatch"];
                return $this->loginResponse($badResponse, false, 402);
            }
        } else {
            // $response = ['User does not exist'];
            return $this->loginResponse($badResponse, false, 402);
        }
    }

    private function getUser(User $user)
    {
        $permissionRoles = collect();
        $permission = $user->permissions->map(function ($item) {
            return $item->label;
        });

        if ($user->roles->isNotEmpty()) {
            $permissionRoles = $user->roles->permissions->map(function ($item) {
                return $item->label;
            });
        }

        $permissions = $permissionRoles->concat($permission)->unique()->toArray();

        return [
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'permissions' => $permissions,
            'roles' => $user->roles->toArray()
        ];
    }

    /**
     * login response
     * 
     * @param $response_data
     * @param $success
     * @param $code
     * @return array
     */
    private function loginResponse($response_data, $success = true, $code = 200)
    {
        return [
            'success' => $success, 
            'code' => $code, 
            'body' => [
                'error_message' => null,
                'errors' => null,
                'response_data' => $response_data,
            ]
        ];
    }

    public function logout(Request $request)
    {
        $token = $request->user()->token();
        $token->revoke();
        $response = ['You have been successfully logged out!'];
        return $this->respondOk($response);
    }

    public function token(Request $request)
    {
        $request->request->add([
            'grant_type' => 'password',
            'client_id' => config('auth.passport.client_id'),
            'client_secret' => config('auth.passport.client_secret'),
        ]);

        $proxy = Request::create('oauth/token', 'post');

        return Route::dispatch($proxy);
    }
}
