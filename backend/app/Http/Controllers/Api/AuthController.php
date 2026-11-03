<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'phone' => $request->phone,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => new UserResource($user),
            'token' => $token
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Email hoặc mật khẩu không đúng.',
            ], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user'  => new UserResource($user),
            'token' => $token,
        ]);
    }

    public function logout(): JsonResponse
    {
        Auth::user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Đăng xuất thành công.',
        ]);
    }

    public function me(): JsonResponse
    {
        return response()->json(new UserResource(Auth::user()));
    }

    /**
     * Cập nhật thông tin cá nhân khách hàng
     */
    public function updateProfile(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|string',
        ]);

        $user->update([
            'name' => $validated['name'],
            'phone' => $validated['phone'] ?? $user->phone,
            'avatar' => $validated['avatar'] ?? $user->avatar,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật thông tin thành công.',
            'user' => new UserResource($user->fresh()),
        ]);
    }

    /**
     * Tải lên ảnh đại diện cho khách hàng
     */
    public function uploadAvatar(Request $request): JsonResponse
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        /** @var User $user */
        $user = Auth::user();

        $file = $request->file('avatar');
        $fileName = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('uploads/avatars', $fileName, 'public');

        $url = asset('storage/' . $path);

        $user->update([
            'avatar' => $url,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật ảnh đại diện thành công!',
            'avatar_url' => $url,
            'user' => new UserResource($user->fresh()),
        ]);
    }

    /**
     * Đổi mật khẩu tài khoản
     */
    public function updatePassword(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => 'nullable|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        if ($user->password && !Hash::check($validated['current_password'] ?? '', $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Mật khẩu hiện tại không chính xác.',
            ], 422);
        }

        $user->update([
            'password' => Hash::make($validated['new_password']),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Đổi mật khẩu thành công.',
        ]);
    }

    /**
     * Chuyển hướng sang Google OAuth
     */
    public function redirectToGoogle(): RedirectResponse
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    /**
     * Lấy URL chuyển hướng OAuth của Google dạng JSON
     */
    public function getGoogleAuthUrl(): JsonResponse
    {
        try {
            $url = Socialite::driver('google')->stateless()->redirect()->getTargetUrl();

            return response()->json([
                'status' => true,
                'url' => $url,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Chưa cấu hình Google Client ID / Secret trên hệ thống.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Xử lý callback sau khi người dùng xác thực Google thành công
     */
    public function handleGoogleCallback(): RedirectResponse
    {
        $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');

        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Throwable $e) {
            return redirect($frontendUrl . '/login?error=' . urlencode('Đăng nhập Google thất bại: ' . $e->getMessage()));
        }

        $email = $googleUser->getEmail();
        if (!$email) {
            return redirect($frontendUrl . '/login?error=' . urlencode('Không tìm thấy địa chỉ email từ tài khoản Google'));
        }

        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $email)
            ->first();

        if ($user) {
            $user->update([
                'google_id' => $googleUser->getId(),
                'avatar' => $user->avatar ?: $googleUser->getAvatar(),
                'email_verified_at' => $user->email_verified_at ?: now(),
            ]);
        } else {
            $user = User::create([
                'name' => $googleUser->getName() ?: 'Khách hàng',
                'email' => $email,
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'role' => 'customer',
                'email_verified_at' => now(),
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return redirect($frontendUrl . '/auth/callback?token=' . $token);
    }

    /**
     * Đăng nhập / Đăng ký qua Google Credential Token (One-Tap hoặc Popup SDK)
     */
    public function googleTokenLogin(Request $request): JsonResponse
    {
        $request->validate([
            'credential' => 'required|string',
        ]);

        $credential = $request->input('credential');

        // Xác thực token qua Google TokenInfo API chính thức
        $response = Http::get('https://oauth2.googleapis.com/tokeninfo', [
            'id_token' => $credential,
        ]);

        if ($response->failed()) {
            return response()->json([
                'message' => 'Mã xác thực Google không hợp lệ hoặc đã hết hạn.',
            ], 400);
        }

        $payload = $response->json();
        $googleId = $payload['sub'] ?? null;
        $email = $payload['email'] ?? null;
        $name = $payload['name'] ?? 'Khách hàng';
        $avatar = $payload['picture'] ?? null;

        if (!$email || !$googleId) {
            return response()->json([
                'message' => 'Không thể lấy thông tin người dùng từ Google.',
            ], 400);
        }

        $user = User::where('google_id', $googleId)
            ->orWhere('email', $email)
            ->first();

        if ($user) {
            $user->update([
                'google_id' => $googleId,
                'avatar' => $user->avatar ?: $avatar,
                'email_verified_at' => $user->email_verified_at ?: now(),
            ]);
        } else {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'google_id' => $googleId,
                'avatar' => $avatar,
                'role' => 'customer',
                'email_verified_at' => now(),
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Đăng nhập Google thành công',
            'user' => new UserResource($user),
            'token' => $token,
        ]);
    }
}