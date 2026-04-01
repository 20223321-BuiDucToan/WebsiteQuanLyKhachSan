@extends('layouts.admin')

@section('title', 'Quản lý người dùng')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4 gap-3 flex-wrap">
        <div>
            <h2 class="section-title">Quản lý người dùng</h2>
            <p class="section-subtitle">Quản lý tài khoản nội bộ với phân quyền rõ ràng theo vai trò.</p>
        </div>

        <a href="{{ route('nguoi-dung.create') }}" class="btn btn-gradient">
            <i class="fa-solid fa-plus me-2"></i>Thêm người dùng
        </a>
    </div>

    <div class="premium-card mb-4">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('nguoi-dung.index') }}" class="row g-3">
                <div class="col-md-5">
                    <label class="form-label">Từ khóa</label>
                    <input
                        type="text"
                        name="tu_khoa"
                        class="form-control"
                        value="{{ request('tu_khoa') }}"
                        placeholder="Họ tên, email, tên đăng nhập, số điện thoại"
                    >
                </div>

                <div class="col-md-3">
                    <label class="form-label">Vai trò</label>
                    <select name="vai_tro" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="admin" {{ request('vai_tro') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="nhan_vien" {{ request('vai_tro') == 'nhan_vien' ? 'selected' : '' }}>Nhân viên</option>
                        <option value="khach_hang" {{ request('vai_tro') == 'khach_hang' ? 'selected' : '' }}>Khách hàng</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Trạng thái</label>
                    <select name="trang_thai" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="hoat_dong" {{ request('trang_thai') == 'hoat_dong' ? 'selected' : '' }}>Hoạt động</option>
                        <option value="tam_khoa" {{ request('trang_thai') == 'tam_khoa' ? 'selected' : '' }}>Tạm khóa</option>
                    </select>
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-gradient w-100">Lọc</button>
                </div>
            </form>
        </div>
    </div>

    <div class="premium-card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Người dùng</th>
                            <th>Liên hệ</th>
                            <th>Vai trò</th>
                            <th>Trạng thái</th>
                            <th>Lần đăng nhập cuối</th>
                            <th class="text-center pe-4">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($danhSachNguoiDung as $nguoiDung)
                            <tr>
                                <td class="ps-4">{{ $nguoiDung->id }}</td>
                                <td>
                                    <div class="fw-bold">{{ $nguoiDung->ho_ten }}</div>
                                    <div class="text-muted small">{{ $nguoiDung->ten_dang_nhap }}</div>
                                </td>
                                <td>
                                    <div>{{ $nguoiDung->email }}</div>
                                    <div class="text-muted small">{{ $nguoiDung->so_dien_thoai }}</div>
                                </td>
                                <td>
                                    @if($nguoiDung->vai_tro === 'admin')
                                        <span class="badge-role-admin">Admin</span>
                                    @elseif($nguoiDung->vai_tro === 'khach_hang')
                                        <span class="badge-role-khach-hang">Khách hàng</span>
                                    @else
                                        <span class="badge-role-nhan-vien">Nhân viên</span>
                                    @endif
                                </td>
                                <td>
                                    @if($nguoiDung->trang_thai === 'hoat_dong')
                                        <span class="badge-status-hoat-dong">Hoạt động</span>
                                    @else
                                        <span class="badge-status-tam-khoa">Tạm khóa</span>
                                    @endif
                                </td>
                                <td>{{ $nguoiDung->lan_dang_nhap_cuoi ? $nguoiDung->lan_dang_nhap_cuoi->format('d/m/Y H:i') : 'Chưa có' }}</td>
                                <td class="text-center pe-4">
                                    <a href="{{ route('nguoi-dung.show', $nguoiDung->id) }}" class="btn btn-info btn-sm rounded-pill">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>

                                    <a href="{{ route('nguoi-dung.edit', $nguoiDung->id) }}" class="btn btn-warning btn-sm rounded-pill text-white">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>

                                    <form action="{{ route('nguoi-dung.doi-trang-thai', $nguoiDung->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-secondary btn-sm rounded-pill" onclick="return confirm('Bạn có chắc muốn đổi trạng thái tài khoản này?')">
                                            <i class="fa-solid fa-lock"></i>
                                        </button>
                                    </form>

                                    <form action="{{ route('nguoi-dung.destroy', $nguoiDung->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm rounded-pill" onclick="return confirm('Bạn có chắc muốn xóa người dùng này?')">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Chưa có dữ liệu người dùng.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4">{{ $danhSachNguoiDung->links() }}</div>
        </div>
    </div>
@endsection
