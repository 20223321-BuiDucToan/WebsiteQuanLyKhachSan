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
                'login.required' => 'Vui long nhap email hoac ten dang nhap.',
                'password.required' => 'Vui long nhap mat khau.',
            ],
        );

        $field = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'ten_dang_nhap';

        $nguoiDung = NguoiDung::where($field, $request->login)->first();

        if (!$nguoiDung) {
            return back()->withErrors([
                'login' => 'Tai khoan khong ton tai.',
            ])->withInput();
        }

        if ($nguoiDung->trang_thai === 'tam_khoa') {
            return back()->withErrors([
                'login' => 'Tai khoan da bi tam khoa.',
            ])->withInput();
        }

        if (Auth::attempt([$field => $request->login, 'password' => $request->password], $request->has('remember'))) {
            $request->session()->regenerate();

            $nguoiDung->update([
                'lan_dang_nhap_cuoi' => now(),
            ]);

            if ($nguoiDung->vai_tro === 'khach_hang') {
                return redirect()->route('booking.index')->with('success', 'Dang nhap thanh cong.');
            }

            return redirect()->route('dashboard')->with('success', 'Dang nhap thanh cong.');
        }

        return back()->withErrors([
            'password' => 'Mat khau khong chinh xac.',
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
                'ho_ten.required' => 'Ho ten khong duoc de trong.',
                'ten_dang_nhap.required' => 'Ten dang nhap khong duoc de trong.',
                'ten_dang_nhap.unique' => 'Ten dang nhap da ton tai.',
                'email.required' => 'Email khong duoc de trong.',
                'email.email' => 'Email khong dung dinh dang.',
                'email.unique' => 'Email da ton tai.',
                'password.required' => 'Mat khau khong duoc de trong.',
                'password.min' => 'Mat khau phai tu 6 ky tu tro len.',
                'password.confirmed' => 'Xac nhan mat khau khong khop.',
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

        return redirect()->route('booking.index')->with('success', 'Dang ky tai khoan khach hang thanh cong.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('booking.index')->with('success', 'Dang xuat thanh cong.');
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
                'ten_dang_nhap.required' => 'Vui long nhap ten dang nhap.',
                'email.required' => 'Vui long nhap email.',
                'email.email' => 'Email khong dung dinh dang.',
            ],
        );

        $nguoiDung = NguoiDung::query()
            ->where('ten_dang_nhap', $request->ten_dang_nhap)
            ->where('email', $request->email)
            ->first();

        if (!$nguoiDung) {
            return back()->withErrors([
                'email' => 'Ten dang nhap va email khong khop trong he thong.',
            ])->withInput();
        }

        if ($nguoiDung->trang_thai === 'tam_khoa') {
            return back()->withErrors([
                'ten_dang_nhap' => 'Tai khoan nay dang bi tam khoa.',
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
            ->with('success', 'Xac thuc thong tin thanh cong. Ban co the dat lai mat khau ngay bay gio.')
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
                'ten_dang_nhap.required' => 'Vui long nhap ten dang nhap.',
                'email.required' => 'Vui long nhap email.',
                'email.email' => 'Email khong dung dinh dang.',
                'email.exists' => 'Email khong ton tai.',
                'token.required' => 'Token khong hop le.',
                'password.required' => 'Vui long nhap mat khau moi.',
                'password.min' => 'Mat khau moi phai tu 6 ky tu.',
                'password.confirmed' => 'Xac nhan mat khau khong khop.',
            ],
        );

        $nguoiDung = NguoiDung::query()
            ->where('ten_dang_nhap', $request->ten_dang_nhap)
            ->where('email', $request->email)
            ->first();

        if (!$nguoiDung) {
            return back()->withErrors([
                'email' => 'Ten dang nhap va email khong khop.',
            ])->withInput();
        }

        $resetRow = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$resetRow) {
            return back()->withErrors([
                'email' => 'Yeu cau dat lai mat khau khong ton tai.',
            ])->withInput();
        }

        if (!Hash::check($request->token, $resetRow->token)) {
            return back()->withErrors([
                'token' => 'Lien ket dat lai mat khau khong hop le.',
            ])->withInput();
        }

        if (empty($resetRow->created_at) || now()->diffInMinutes($resetRow->created_at) > 60) {
            return back()->withErrors([
                'token' => 'Lien ket dat lai mat khau da het han.',
            ])->withInput();
        }

        $nguoiDung->update([
            'password' => Hash::make($request->password),
        ]);

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Dat lai mat khau thanh cong. Hay dang nhap lai.');
    }

    private function dongBoKhachHang(NguoiDung $nguoiDung): void
    {
        $khachHang = null;

        if (!empty($nguoiDung->email)) {
            $khachHang = KhachHang::query()
                ->where('email', $nguoiDung->email)
                ->first();
        }

        if (!$khachHang && empty($nguoiDung->email) && !empty($nguoiDung->so_dien_thoai)) {
            $khachHang = KhachHang::query()
                ->where('so_dien_thoai', $nguoiDung->so_dien_thoai)
                ->first();
        }

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
            'ma_khach_hang' => $this->taoMaKhachHang(),
            'ho_ten' => $nguoiDung->ho_ten,
            'so_dien_thoai' => $nguoiDung->so_dien_thoai,
            'email' => $nguoiDung->email,
            'hang_khach_hang' => 'thuong',
            'trang_thai' => 'hoat_dong',
        ]);
    }

    private function taoMaKhachHang(): string
    {
        do {
            $maKhachHang = 'KH' . now()->format('ymdHis') . random_int(10, 99);
        } while (KhachHang::query()->where('ma_khach_hang', $maKhachHang)->exists());

        return $maKhachHang;
    }
}
