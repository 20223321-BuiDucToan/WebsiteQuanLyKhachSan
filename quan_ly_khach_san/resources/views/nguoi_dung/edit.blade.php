@extends('layouts.admin')

@section('title', 'Sửa người dùng')

@section('content')
    <div class="mb-4">
        <h2 class="section-title mb-1">Cập nhật người dùng</h2>
        <p class="section-subtitle">Chỉnh sửa thông tin tài khoản nội bộ</p>
    </div>

    <div class="card premium-card">
        <div class="card-body p-4">
            <form action="{{ route('nguoi-dung.update', $nguoiDung->id) }}" method="POST">
                @csrf
                @method('PUT')

                @include('nguoi_dung._form')

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-premium btn-gradient">
                        <i class="fa-solid fa-floppy-disk me-2"></i>Cập nhật
                    </button>

                    <a href="{{ route('nguoi-dung.index') }}" class="btn btn-light border rounded-4 px-4">
                        Quay lại
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection