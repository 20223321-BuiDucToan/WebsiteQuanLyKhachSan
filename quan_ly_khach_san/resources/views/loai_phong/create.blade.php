@extends('layouts.admin')

@section('title', 'Thêm loại phòng')

@section('content')
    <div class="mb-4">
        <h2 class="section-title">Thêm loại phòng</h2>
        <p class="section-subtitle">Tạo hạng phòng chuẩn để quản lý giá, sức chứa và tiện ích thống nhất.</p>
    </div>

    <div class="premium-card">
        <div class="card-body p-4">
            <form action="{{ route('loai-phong.store') }}" method="POST">
                @csrf

                @include('loai_phong._form')

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-gradient">
                        <i class="fa-solid fa-floppy-disk me-2"></i>Lưu loại phòng
                    </button>
                    <a href="{{ route('loai-phong.index') }}" class="btn btn-soft">Quay lại</a>
                </div>
            </form>
        </div>
    </div>
@endsection
