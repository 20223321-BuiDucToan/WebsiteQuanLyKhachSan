<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\DatLaiMatKhauMail;
use App\Models\KhachHang;
use App\Models\NguoiDung;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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
                return redirect()->route('booking.account')->with('success', 'Đăng nhập thành công.');
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
                'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
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

        return redirect()->route('booking.account')->with('success', 'Đăng ký tài khoản khách hàng thành công.');
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
                'email' => 'required|email|max:255',
            ],
            [
                'email.required' => 'Vui lòng nhập email.',
                'email.email' => 'Email không đúng định dạng.',
            ],
        );

        $nguoiDung = NguoiDung::query()
            ->where('email', $request->email)
            ->where('trang_thai', 'hoat_dong')
            ->first();

        $resetLink = null;

        if ($nguoiDung) {
            $token = Str::random(64);

            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $nguoiDung->email],
                [
                    'token' => Hash::make($token),
                    'created_at' => now(),
                ],
            );

            $resetLink = route('password.reset', [
                'token' => $token,
                'email' => $nguoiDung->email,
            ]);

            try {
                Mail::to($nguoiDung->email)->send(new DatLaiMatKhauMail($nguoiDung, $resetLink));
            } catch (\Throwable $exception) {
                Log::error('Không gửi được email đặt lại mật khẩu.', [
                    'email' => $nguoiDung->email,
                    'message' => $exception->getMessage(),
                ]);

                return back()
                    ->withInput()
                    ->withErrors([
                        'email' => 'Hệ thống chưa gửi được email đặt lại mật khẩu. Vui lòng thử lại sau.',
                    ]);
            }
        }

        return back()
            ->with('success', 'Nếu email tồn tại và tài khoản đang hoạt động, hệ thống đã gửi liên kết đặt lại mật khẩu. Liên kết có hiệu lực trong 60 phút.')
            ->with('auth_modal', 'forgot');
    }

    public function showResetPassword(Request $request, string $token)
    {
        $email = $request->query('email');
        $resetRow = $email
            ? DB::table('password_reset_tokens')->where('email', $email)->first()
            : null;

        if (!$resetRow || !Hash::check($token, $resetRow->token) || $this->tokenDatLaiMatKhauHetHan($resetRow->created_at)) {
            return redirect()
                ->route('password.request')
                ->withErrors([
                    'email' => 'Liên kết đặt lại mật khẩu không hợp lệ hoặc đã hết hạn. Vui lòng gửi yêu cầu mới.',
                ]);
        }

        return view('auth.reset_password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate(
            [
                'email' => 'required|email|exists:nguoi_dung,email',
                'token' => 'required',
                'password' => 'required|string|min:8|confirmed',
            ],
            [
                'email.required' => 'Vui lòng nhập email.',
                'email.email' => 'Email không đúng định dạng.',
                'email.exists' => 'Email không tồn tại.',
                'token.required' => 'Token không hợp lệ.',
                'password.required' => 'Vui lòng nhập mật khẩu mới.',
                'password.min' => 'Mật khẩu mới phải có ít nhất 8 ký tự.',
                'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
            ],
        );

        $nguoiDung = NguoiDung::query()
            ->where('email', $request->email)
            ->first();

        if (!$nguoiDung) {
            return back()->withErrors([
                'email' => 'Không tìm thấy tài khoản phù hợp.',
            ])->withInput();
        }

        if ($nguoiDung->trang_thai !== 'hoat_dong') {
            return redirect()
                ->route('password.request')
                ->withErrors([
                    'email' => 'Tài khoản chưa thể đặt lại mật khẩu. Vui lòng liên hệ bộ phận hỗ trợ.',
                ]);
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

        if ($this->tokenDatLaiMatKhauHetHan($resetRow->created_at)) {
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

    private function tokenDatLaiMatKhauHetHan($createdAt): bool
    {
        if (!$createdAt) {
            return true;
        }

        return Carbon::parse($createdAt)->addMinutes(60)->isPast();
    }

    private function dongBoKhachHang(NguoiDung $nguoiDung): void
    {
        KhachHang::dongBoTuTaiKhoan($nguoiDung);
    }
}
