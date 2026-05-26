@php
    $nguoiDungHienTai = $nguoiDung ?? null;
@endphp

<div class="d-grid gap-3">
    <section class="form-section">
        <h3 class="form-section__title">Thông tin đăng nhập</h3>
        <p class="form-section__description">Nhập các trường cơ bản để tạo tài khoản và dùng cho quá trình đăng nhập vào hệ thống.</p>

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Họ tên</label>
                <input
                    type="text"
                    name="ho_ten"
                    class="form-control @error('ho_ten') is-invalid @enderror"
                    value="{{ old('ho_ten', $nguoiDungHienTai?->ho_ten ?? '') }}"
                    placeholder="Ví dụ: Nguyễn Minh An"
                    required
                >
                @error('ho_ten')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label">Tên đăng nhập</label>
                <input
                    type="text"
                    name="ten_dang_nhap"
                    class="form-control @error('ten_dang_nhap') is-invalid @enderror"
                    value="{{ old('ten_dang_nhap', $nguoiDungHienTai?->ten_dang_nhap ?? '') }}"
                    placeholder="Ví dụ: nhanvien01"
                    required
                >
                @error('ten_dang_nhap')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label">Mật khẩu</label>
                <input
                    type="password"
                    name="password"
                    class="form-control @error('password') is-invalid @enderror"
                    placeholder="{{ $nguoiDungHienTai ? 'Để trống nếu không đổi mật khẩu' : 'Nhập mật khẩu đăng nhập' }}"
                >
                <div class="field-note">{{ $nguoiDungHienTai ? 'Chỉ nhập khi cần đổi mật khẩu cho tài khoản này.' : 'Bạn có thể dùng mật khẩu dễ nhớ để tạo nhanh tài khoản test.' }}</div>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label">Xác nhận mật khẩu</label>
                <input type="password" name="password_confirmation" class="form-control" placeholder="Nhập lại mật khẩu">
            </div>
        </div>
    </section>

    <section class="form-section">
        <h3 class="form-section__title">Liên hệ và phân quyền</h3>
        <p class="form-section__description">Các trường dưới đây giúp xác định vai trò, trạng thái hoạt động và thông tin liên hệ của người dùng.</p>

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input
                    type="email"
                    name="email"
                    class="form-control @error('email') is-invalid @enderror"
                    value="{{ old('email', $nguoiDungHienTai?->email ?? '') }}"
                    placeholder="Ví dụ: nhanvien@example.com"
                    required
                >
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label">Số điện thoại</label>
                <input
                    type="text"
                    name="so_dien_thoai"
                    class="form-control @error('so_dien_thoai') is-invalid @enderror"
                    value="{{ old('so_dien_thoai', $nguoiDungHienTai?->so_dien_thoai ?? '') }}"
                    placeholder="Ví dụ: 0901234567"
                    required
                >
                @error('so_dien_thoai')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label">Vai trò</label>
                <select name="vai_tro" class="form-select @error('vai_tro') is-invalid @enderror" required>
                    <option value="admin" @selected(old('vai_tro', $nguoiDungHienTai?->vai_tro ?? '') === 'admin')>Quản trị viên</option>
                    <option value="nhan_vien" @selected(old('vai_tro', $nguoiDungHienTai?->vai_tro ?? '') === 'nhan_vien')>Nhân viên</option>
                    <option value="khach_hang" @selected(old('vai_tro', $nguoiDungHienTai?->vai_tro ?? '') === 'khach_hang')>Khách hàng</option>
                </select>
                @error('vai_tro')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label">Trạng thái</label>
                <select name="trang_thai" class="form-select @error('trang_thai') is-invalid @enderror" required>
                    <option value="hoat_dong" @selected(old('trang_thai', $nguoiDungHienTai?->trang_thai ?? 'hoat_dong') === 'hoat_dong')>Hoạt động</option>
                    <option value="tam_khoa" @selected(old('trang_thai', $nguoiDungHienTai?->trang_thai ?? '') === 'tam_khoa')>Tạm khóa</option>
                </select>
                @error('trang_thai')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-12">
                <label class="form-label">Địa chỉ</label>
                <textarea
                    name="dia_chi"
                    rows="3"
                    class="form-control @error('dia_chi') is-invalid @enderror"
                    placeholder="Nhập địa chỉ liên hệ nếu cần..."
                >{{ old('dia_chi', $nguoiDungHienTai?->dia_chi ?? '') }}</textarea>
                @error('dia_chi')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </section>
</div>
