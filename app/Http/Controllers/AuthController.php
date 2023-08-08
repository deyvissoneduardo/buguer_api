<?php

namespace App\Http\Controllers;

use App\Helpers\StringHelper;
use App\Http\Requests\AuthLoginRequest;
use App\Http\Requests\AuthRegisterRequest;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Response;
use App\Helpers\RequestResponse;
use Illuminate\Support\Facades\Auth;
class AuthController extends Controller
{
    public function username()
    {
        return 'cpf';
    }

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'index']]);
    }

    private function findUserByCFP($cpf)
    {
        return User::where('cpf', $cpf)->first();
    }

    public function index()
    {
        $user = User::all();
        return RequestResponse::success($user);
    }

    public function login(AuthLoginRequest $request)
    {
        try {
            $request['cpf'] = StringHelper::removeSpecialCharacters($request['cpf']);

            $credentials = $request->only('cpf', 'password');
            $user = User::where('cpf', $request->cpf)->first();

            if (!$user || !Auth::guard('api')->attempt($credentials)) {
                return RequestResponse::error('Invalid credentials', [], Response::HTTP_UNAUTHORIZED);
            }

            return $this->respondWithToken(Auth::guard('api')->user(), $request);
        } catch (ValidationException $e) {
            return RequestResponse::error('Validation Error', $e);
        } catch (\Exception $e) {
            return RequestResponse::error('Internal Server Error', $e, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function register(AuthRegisterRequest $request)
    {
        try {
            $request['cpf'] = StringHelper::removeSpecialCharacters($request['cpf']);

            $user = $this->findUserByCFP($request->cpf);
            if ($user) {
                return RequestResponse::error('User Already Registered', [], Response::HTTP_CONFLICT);
            }

            $user = User::firstOrCreate(['cpf' => $request->cpf], [
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);

            return RequestResponse::success($user);
        } catch (\Exception $e) {
            return RequestResponse::error('Internal Server Error', $e, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function me()
    {
        return response()->json(auth('api')->user());
    }

    public function logout()
    {
        auth('api')->logout();
        return RequestResponse::success([], 'Successfully logged out');
    }

    protected function respondWithToken($user, Request $request)
    {
        try {
            $token = Auth::guard('api')->attempt($request->only('cpf', 'password'));
            return RequestResponse::success([
                'access_token' => 'bearer ' . $token,
                'expires_in' => Auth::factory()->getTTL() * 60,
            ]);
        } catch (\Exception $e) {
            return RequestResponse::error('Internal Server Error', $e, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
