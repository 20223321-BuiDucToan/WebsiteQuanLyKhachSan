@extends('layouts.admin')

@section('title', 'Thêm người dùng')

@section('content')
    <div class="mb-4">
        <h2 class="section-title">Thêm người dùng</h2>
        <p class="section-subtitle">Tạo mới tài khoản nội bộ cho hệ thống quản lý.</p>
    </div>

    <div class="premium-card">
        <div class="card-body p-4">
            <form action="{{ route('nguoi-dung.store') }}" method="POST">
                @csrf

                @include('nguoi_dung._form')

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-gradient">
                        <i class="fa-solid fa-floppy-disk me-2"></i>Lưu
                    </button>

                    <a href="{{ route('nguoi-dung.index') }}" class="btn btn-soft">Quay lại</a>
                </div>
            </form>
        </div>
    </div>
@endsection
