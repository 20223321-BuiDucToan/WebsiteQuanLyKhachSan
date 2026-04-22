<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\KhachHang;
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
        $request->validate(
            [
                'login' => 'required',
                'password' => 'required',
            ],
            [
                'login.required' => 'Vui lòng nhập email hoặc tên đăng nhập.',
                'password.required' => 'Vui lòng nhập mật khẩu.',
            ],
        );

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

            if ($nguoiDung->vai_tro === 'khach_hang') {
                return redirect()->route('booking.index')->with('success', 'Đăng nhập thành công.');
            }

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
        $request->validate(
            [
                'ho_ten' => 'required|string|max:100',
                'ten_dang_nhap' => 'required|string|max:50|unique:nguoi_dung,ten_dang_nhap',
                'email' => 'required|email|max:100|unique:nguoi_dung,email',
                'so_dien_thoai' => 'nullable|string|max:15',
                'password' => 'required|string|min:6|confirmed',
            ],
            [
                'ho_ten.required' => 'Họ tên không được để trống.',
                'ten_dang_nhap.required' => 'Tên đăng nhập không được để trống.',
                'ten_dang_nhap.unique' => 'Tên đăng nhập đã tồn tại.',
                'email.required' => 'Email không được để trống.',
                'email.email' => 'Email không đúng định dạng.',
                'email.unique' => 'Email đã tồn tại.',
                'password.required' => 'Mật khẩu không được để trống.',
                'password.min' => 'Mật khẩu phải từ 6 ký tự trở lên.',
                'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
            ],
        );

        $nguoiDung = DB::transaction(function () use ($request) {
            $nguoiDung = NguoiDung::create([
                'ho_ten' => $request->ho_ten,
                'ten_dang_nhap' => $request->ten_dang_nhap,
                'email' => $request->email,
                'so_dien_thoai' => $request->so_dien_thoai,
                'password' => Hash::make($request->password),
                'vai_tro' => 'khach_hang',
                'trang_thai' => 'hoat_dong',
            ]);

            $this->dongBoKhachHang($nguoiDung);

            return $nguoiDung;
        });

        Auth::login($nguoiDung);

        return redirect()->route('booking.index')->with('success', 'Đăng ký tài khoản khách hàng thành công.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('booking.index')->with('success', 'Đăng xuất thành công.');
    }

    public function showForgotPassword()
    {
        return view('auth.forgot_password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(
            [
                'ten_dang_nhap' => 'required|string|max:100',
                'email' => 'required|email|max:255',
            ],
            [
                'ten_dang_nhap.required' => 'Vui lòng nhập tên đăng nhập.',
                'email.required' => 'Vui lòng nhập email.',
                'email.email' => 'Email không đúng định dạng.',
            ],
        );

        $nguoiDung = NguoiDung::query()
            ->where('ten_dang_nhap', $request->ten_dang_nhap)
            ->where('email', $request->email)
            ->first();

        if (!$nguoiDung) {
            return back()->withErrors([
                'email' => 'Tên đăng nhập và email không khớp trong hệ thống.',
            ])->withInput();
        }

        if ($nguoiDung->trang_thai === 'tam_khoa') {
            return back()->withErrors([
                'ten_dang_nhap' => 'Tài khoản này đang bị tạm khóa.',
            ])->withInput();
        }

        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $nguoiDung->email],
            [
                'token' => Hash::make($token),
                'created_at' => now(),
            ],
        );

        $resetLink = route('password.reset', ['token' => $token])
            . '?email=' . urlencode($nguoiDung->email)
            . '&ten_dang_nhap=' . urlencode($nguoiDung->ten_dang_nhap);

        return back()
            ->with('success', 'Xác thực thông tin thành công. Bạn có thể đặt lại mật khẩu ngay bây giờ.')
            ->with('reset_link', $resetLink);
    }

    public function showResetPassword(Request $request, string $token)
    {
        return view('auth.reset_password', [
            'token' => $token,
            'email' => $request->query('email'),
            'ten_dang_nhap' => $request->query('ten_dang_nhap'),
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate(
            [
                'ten_dang_nhap' => 'required|string|max:100',
                'email' => 'required|email|exists:nguoi_dung,email',
                'token' => 'required',
                'password' => 'required|string|min:6|confirmed',
            ],
            [
                'ten_dang_nhap.required' => 'Vui lòng nhập tên đăng nhập.',
                'email.required' => 'Vui lòng nhập email.',
                'email.email' => 'Email không đúng định dạng.',
                'email.exists' => 'Email không tồn tại.',
                'token.required' => 'Token không hợp lệ.',
                'password.required' => 'Vui lòng nhập mật khẩu mới.',
                'password.min' => 'Mật khẩu mới phải từ 6 ký tự.',
                'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
            ],
        );

        $nguoiDung = NguoiDung::query()
            ->where('ten_dang_nhap', $request->ten_dang_nhap)
            ->where('email', $request->email)
            ->first();

        if (!$nguoiDung) {
            return back()->withErrors([
                'email' => 'Tên đăng nhập và email không khớp.',
            ])->withInput();
        }

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

        if (empty($resetRow->created_at) || now()->diffInMinutes($resetRow->created_at) > 60) {
            return back()->withErrors([
                'token' => 'Liên kết đặt lại mật khẩu đã hết hạn.',
            ])->withInput();
        }

        $nguoiDung->update([
            'password' => Hash::make($request->password),
        ]);

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Đặt lại mật khẩu thành công. Hãy đăng nhập lại.');
    }

    private function dongBoKhachHang(NguoiDung $nguoiDung): void
    {
        $khachHang = KhachHang::timTheoThongTinLienHe($nguoiDung->email, $nguoiDung->so_dien_thoai);

        if ($khachHang) {
            $khachHang->update([
                'ho_ten' => $nguoiDung->ho_ten,
                'email' => $nguoiDung->email,
                'so_dien_thoai' => $nguoiDung->so_dien_thoai,
                'trang_thai' => 'hoat_dong',
            ]);

            return;
        }

        KhachHang::create([
            'ma_khach_hang' => KhachHang::taoMaMoi(),
            'ho_ten' => $nguoiDung->ho_ten,
            'so_dien_thoai' => $nguoiDung->so_dien_thoai,
            'email' => $nguoiDung->email,
            'hang_khach_hang' => 'thuong',
            'trang_thai' => 'hoat_dong',
        ]);
    }
}
