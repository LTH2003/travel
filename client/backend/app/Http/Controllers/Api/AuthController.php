<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Services\OtpService;

class AuthController extends Controller
{
    // 🧩 Đăng ký tài khoản
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => 'user',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status'  => true,
            'message' => 'Đăng ký thành công',
            'user'    => $user,
            'token'   => $token,
        ], 201);
    }

    // 🧩 Đăng nhập tài khoản
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email hoặc mật khẩu không đúng.'],
            ]);
        }

        // Kiểm tra nếu user bật 2FA
        if ($user->two_factor_enabled) {
            // Gửi OTP
            $otpService = app(OtpService::class);
            $otpService->sendOtp($user);

            return response()->json([
                'status' => true,
                'message' => 'OTP đã được gửi đến email của bạn',
                'requires_otp' => true,
                'user_id' => $user->id,
                'user_email' => $user->email,
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status'  => true,
            'message' => 'Đăng nhập thành công',
            'user'    => $user,
            'token'   => $token,
        ]);
    }

    // 🧩 Lấy thông tin user đang đăng nhập
    public function me(Request $request)
    {
        return response()->json([
            'status' => true,
            'user'   => $request->user(),
        ]);
    }

    // 🧩 Đăng xuất
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Đăng xuất thành công',
        ]);
    }

    // 🧩 Cập nhật thông tin hồ sơ
    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'sometimes|required|string|max:255',
            'email'   => 'sometimes|required|email|max:255|unique:users,email,' . $request->user()->id,
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'bio'     => 'nullable|string|max:1000',
            'avatar'  => 'nullable|string',
        ]);

        $user = $request->user();

        // Xử lý avatar Base64
        if (!empty($validated['avatar']) && strpos($validated['avatar'], 'data:image') === 0) {
            try {
                // Decode Base64
                $image_data = base64_decode(preg_replace('#^data:image/[^;]+;base64,#', '', $validated['avatar']));
                
                // Tạo thư mục nếu chưa có
                $dir = storage_path('app/public/avatars');
                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }
                
                // Tạo tên file
                $filename = 'avatars/' . $user->id . '_' . time() . '.png';
                
                // Lưu file
                Storage::disk('public')->put($filename, $image_data);
                
                // Xóa avatar cũ nếu tồn tại
                if ($user->avatar && strpos($user->avatar, '/storage/') !== false) {
                    $old_path = str_replace('/storage/', '', $user->avatar);
                    if (Storage::disk('public')->exists($old_path)) {
                        Storage::disk('public')->delete($old_path);
                    }
                }
                
                // Lưu URL đầy đủ vào database (absolute URL để frontend có thể truy cập)
                $validated['avatar'] = url('/storage/' . $filename);
            } catch (\Exception $e) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Lỗi xử lý ảnh: ' . $e->getMessage(),
                ], 422);
            }
        }

        $user->update($validated);

        return response()->json([
            'status'  => true,
            'message' => 'Cập nhật thông tin thành công',
            'user'    => $user,
        ]);
    }

    // 🧩 Verify OTP từ login
    public function verifyOtp(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer',
            'code' => 'required|string|size:6',
        ]);

        $user = User::findOrFail($validated['user_id']);
        $otpService = app(OtpService::class);

        if (!$otpService->verifyOtp($user, $validated['code'])) {
            return response()->json([
                'status' => false,
                'message' => 'Mã xác thực không đúng hoặc đã hết hạn',
            ], 422);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Xác thực thành công',
            'user' => $user,
            'token' => $token,
        ]);
    }

    // 🧩 Enable 2FA (bước 1: gửi OTP)
    public function enableTwoFactor(Request $request)
    {
        $user = $request->user();

        if ($user->two_factor_enabled) {
            return response()->json([
                'status' => false,
                'message' => '2FA đã được kích hoạt',
            ], 422);
        }

        $otpService = app(OtpService::class);
        $otpService->sendOtp($user);

        return response()->json([
            'status' => true,
            'message' => 'OTP đã được gửi đến email của bạn',
            'requires_confirmation' => true,
        ]);
    }

    // 🧩 Confirm 2FA (bước 2: xác thực OTP)
    public function confirmTwoFactor(Request $request)
    {
        $user = $request->user();
        
        $validated = $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $otpService = app(OtpService::class);

        if (!$otpService->verifyOtp($user, $validated['code'])) {
            return response()->json([
                'status' => false,
                'message' => 'Mã xác thực không đúng hoặc đã hết hạn',
            ], 422);
        }

        $user->update(['two_factor_enabled' => true]);

        return response()->json([
            'status' => true,
            'message' => '2FA đã được kích hoạt thành công',
            'user' => $user,
        ]);
    }

    // 🧩 Disable 2FA (yêu cầu xác thực mật khẩu)
    public function disableTwoFactor(Request $request)
    {
        $user = $request->user();
        
        $validated = $request->validate([
            'password' => 'required|string',
        ]);

        if (!Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Mật khẩu không đúng',
            ], 422);
        }

        $user->update(['two_factor_enabled' => false]);

        return response()->json([
            'status' => true,
            'message' => '2FA đã bị vô hiệu hóa',
            'user' => $user,
        ]);
    }

    // 🧩 Resend OTP
    public function resendOtp(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer',
        ]);

        $user = User::findOrFail($validated['user_id']);
        $otpService = app(OtpService::class);

        try {
            $otpService->resendOtp($user);
            return response()->json([
                'status' => true,
                'message' => 'OTP đã được gửi lại',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 429);
        }
    }
}
