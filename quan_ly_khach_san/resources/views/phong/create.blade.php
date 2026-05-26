@extends('layouts.admin')

@section('title', 'Thêm phòng')

@section('content')
    <div class="form-page">
        <div class="form-page-header">
            <div>
                <h2 class="form-page-title">Thêm phòng</h2>
                <p class="form-page-subtitle">Màn hình nhập liệu được tách theo từng nhóm để dễ theo dõi hơn khi thêm phòng mới và gắn ảnh.</p>
            </div>
        </div>

        <div class="form-shell">
            <div class="form-shell__body">
                @if($danhSachLoaiPhong->isEmpty())
                    <div class="alert alert-warning rounded-4 mb-0">
                        Chưa có loại phòng hoạt động. Vui lòng
                        <a href="{{ route('loai-phong.create') }}" class="alert-link">tạo loại phòng</a>
                        trước khi thêm phòng mới.
                    </div>
                @endif

                <form action="{{ route('phong.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    @include('phong._form')

                    <div class="form-actions">
                        <button type="submit" class="btn btn-gradient" @disabled($danhSachLoaiPhong->isEmpty())>
                            <i class="fa-solid fa-floppy-disk me-2"></i>Lưu phòng
                        </button>
                        <a href="{{ route('phong.index') }}" class="btn btn-soft">Quay lại</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
