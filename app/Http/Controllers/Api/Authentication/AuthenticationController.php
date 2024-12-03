<?php

namespace App\Http\Controllers\Api\Authentication;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\SignUpRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Spatie\RouteAttributes\Attributes\Post;

class AuthenticationController extends Controller
{
    /**
     * Login Route
     *
     * @param LoginRequest $request
     * @return JsonResponse
     * @throws \Exception
     */
    #[Post('login')]
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->where('active', 1)->first();

        if (!$user->checkPassword($request->password)) {
            throw new \Exception('invalid_credentials');
        }

        return response()->json([
            'token' => $user->createToken('web_browser')->plainTextToken,
            'user' => $user
        ]);
    }

    #[Post('sign-up')]
    public function signUp(SignUpRequest $request): JsonResponse
    {
        try {

            DB::beginTransaction();
            $user = User::create($request->validated());

        } catch (\Exception $ex) {
            throw new \Exception("Error: " . $ex->getMessage());
        }

        DB::commit();
        return response()->json([
            'token' => $user->createToken('web_browser')->plainTextToken,
        ]);
    }
}
