@extends('layouts.admin')

@section('title', 'Cập nhật phòng')

@section('content')
    <div class="mb-4">
        <h2 class="section-title">Cập nhật phòng {{ $phong->so_phong }}</h2>
        <p class="section-subtitle">Điều chỉnh thông tin, giá và trạng thái vận hành phòng.</p>
    </div>

    <div class="premium-card">
        <div class="card-body p-4">
            <form action="{{ route('phong.update', $phong) }}" method="POST">
                @csrf
                @method('PUT')

                @include('phong._form')

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-gradient">
                        <i class="fa-solid fa-floppy-disk me-2"></i>Lưu thay đổi
                    </button>
                    <a href="{{ route('phong.index') }}" class="btn btn-soft">Quay lại</a>
                </div>
            </form>
        </div>
    </div>
@endsection
