@extends('layouts.admin')

@section('title', 'Thêm phòng')

@section('content')
    <div class="mb-4">
        <h2 class="section-title">Thêm phòng</h2>
        <p class="section-subtitle">Tạo mới phòng để đưa vào khai thác và bán phòng.</p>
    </div>

    <div class="premium-card">
        <div class="card-body p-4">
            @if($danhSachLoaiPhong->isEmpty())
                <div class="alert alert-warning rounded-4 mb-4">
                    Chưa có loại phòng hoạt động. Vui lòng
                    <a href="{{ route('loai-phong.create') }}" class="alert-link">tạo loại phòng</a>
                    trước khi thêm phòng mới.
                </div>
            @endif

            <form action="{{ route('phong.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                @include('phong._form')

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-gradient" @disabled($danhSachLoaiPhong->isEmpty())>
                        <i class="fa-solid fa-floppy-disk me-2"></i>Lưu phòng
                    </button>
                    <a href="{{ route('phong.index') }}" class="btn btn-soft">Quay lại</a>
                </div>
            </form>
        </div>
    </div>
@endsection
