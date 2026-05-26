@extends('layouts.admin')

@section('title', 'Thêm loại phòng')

@section('content')
    <div class="form-page">
        <div class="form-page-header">
            <div>
                <h2 class="form-page-title">Thêm loại phòng</h2>
                <p class="form-page-subtitle">Giữ biểu mẫu ngắn gọn để tạo nhanh loại phòng mới, sau đó có thể chỉnh lại chi tiết bất cứ lúc nào.</p>
            </div>
        </div>

        <div class="form-shell">
            <div class="form-shell__body">
                <form action="{{ route('loai-phong.store') }}" method="POST">
                    @csrf

                    @include('loai_phong._form')

                    <div class="form-actions">
                        <button type="submit" class="btn btn-gradient">
                            <i class="fa-solid fa-floppy-disk me-2"></i>Lưu loại phòng
                        </button>
                        <a href="{{ route('loai-phong.index') }}" class="btn btn-soft">Quay lại</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
