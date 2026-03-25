<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\NguoiDung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required',
            'password' => 'required',
        ], [
            'login.required' => 'Vui lòng nhập email hoặc tên đăng nhập.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ]);

        $field = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'ten_dang_nhap';

        $nguoiDung = NguoiDung::where($field, $request->login)->first();

        if (!$nguoiDung) {
            return back()->withErrors([
                'login' => 'Tài khoản không tồn tại.',
            ])->withInput();
        }

        if ($nguoiDung->trang_thai === 'tam_khoa') {
            return back()->withErrors([
                'login' => 'Tài khoản đã bị tạm khóa.',
            ])->withInput();
        }

        if (Auth::attempt([$field => $request->login, 'password' => $request->password], $request->has('remember'))) {
            $request->session()->regenerate();

            $nguoiDung->update([
                'lan_dang_nhap_cuoi' => now(),
            ]);

            return redirect()->route('dashboard')->with('success', 'Đăng nhập thành công.');
        }

        return back()->withErrors([
            'password' => 'Mật khẩu không chính xác.',
        ])->withInput();
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'ho_ten' => 'required|string|max:100',
            'ten_dang_nhap' => 'required|string|max:50|unique:nguoi_dung,ten_dang_nhap',
            'email' => 'required|email|max:100|unique:nguoi_dung,email',
            'so_dien_thoai' => 'nullable|string|max:15',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'ho_ten.required' => 'Họ tên không được để trống.',
            'ten_dang_nhap.required' => 'Tên đăng nhập không được để trống.',
            'ten_dang_nhap.unique' => 'Tên đăng nhập đã tồn tại.',
            'email.required' => 'Email không được để trống.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email đã tồn tại.',
            'password.required' => 'Mật khẩu không được để trống.',
            'password.min' => 'Mật khẩu phải từ 6 ký tự trở lên.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        $nguoiDung = NguoiDung::create([
            'ho_ten' => $request->ho_ten,
            'ten_dang_nhap' => $request->ten_dang_nhap,
            'email' => $request->email,
            'so_dien_thoai' => $request->so_dien_thoai,
            'password' => Hash::make($request->password),
            'vai_tro' => 'nhan_vien',
            'trang_thai' => 'hoat_dong',
        ]);

        Auth::login($nguoiDung);

        return redirect()->route('dashboard')->with('success', 'Đăng ký thành công.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Đăng xuất thành công.');
    }

    public function showForgotPassword()
    {
        return view('auth.forgot_password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:nguoi_dung,email',
        ], [
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không đúng định dạng.',
            'email.exists' => 'Email không tồn tại trong hệ thống.',
        ]);

        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($token),
                'created_at' => now(),
            ]
        );

        $resetLink = route('password.reset', ['token' => $token]) . '?email=' . urlencode($request->email);

        return back()->with('success', 'Tạo liên kết đặt lại mật khẩu thành công. Link test: ' . $resetLink);
    }

    public function showResetPassword(Request $request, $token)
    {
        return view('auth.reset_password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:nguoi_dung,email',
            'token' => 'required',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không đúng định dạng.',
            'email.exists' => 'Email không tồn tại.',
            'token.required' => 'Token không hợp lệ.',
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.min' => 'Mật khẩu mới phải từ 6 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        $resetRow = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$resetRow) {
            return back()->withErrors([
                'email' => 'Yêu cầu đặt lại mật khẩu không tồn tại.',
            ])->withInput();
        }

        if (!Hash::check($request->token, $resetRow->token)) {
            return back()->withErrors([
                'token' => 'Liên kết đặt lại mật khẩu không hợp lệ.',
            ])->withInput();
        }

        if (now()->diffInMinutes($resetRow->created_at) > 60) {
            return back()->withErrors([
                'token' => 'Liên kết đặt lại mật khẩu đã hết hạn.',
            ])->withInput();
        }

        $nguoiDung = NguoiDung::where('email', $request->email)->first();

        $nguoiDung->update([
            'password' => Hash::make($request->password),
        ]);

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Đặt lại mật khẩu thành công. Hãy đăng nhập lại.');
    }
}