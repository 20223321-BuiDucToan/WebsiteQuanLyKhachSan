@extends('layouts.admin')

@section('title', 'Thêm người dùng')

@section('content')
    <div class="form-page">
        <div class="form-page-header">
            <div>
                <h2 class="form-page-title">Thêm người dùng</h2>
                <p class="form-page-subtitle">Biểu mẫu được gom theo nhóm để bạn tạo nhanh tài khoản mới và phân quyền dễ hơn.</p>
            </div>
        </div>

        <div class="form-shell">
            <div class="form-shell__body">
                <form action="{{ route('nguoi-dung.store') }}" method="POST">
                    @csrf

                    @include('nguoi_dung._form')

                    <div class="form-actions">
                        <button type="submit" class="btn btn-gradient">
                            <i class="fa-solid fa-floppy-disk me-2"></i>Lưu tài khoản
                        </button>
                        <a href="{{ route('nguoi-dung.index') }}" class="btn btn-soft">Quay lại</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
