@extends('layouts.admin')

@section('title', 'Cap nhat dich vu')

@section('content')
    <div class="mb-4">
        <h2 class="section-title">Cap nhat dich vu {{ $dichVu->ma_dich_vu }}</h2>
        <p class="section-subtitle">Dieu chinh ten, gia ban, don vi tinh va trang thai de phu hop quy trinh van hanh hien tai.</p>
    </div>

    <div class="premium-card">
        <div class="card-body p-4">
            <form action="{{ route('dich-vu.update', $dichVu) }}" method="POST" class="row g-4">
                @csrf
                @method('PATCH')

                @include('dich_vu._form', ['dichVu' => $dichVu])

                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-gradient">
                        <i class="fa-solid fa-floppy-disk me-2"></i>Luu thay doi
                    </button>
                    <a href="{{ route('dich-vu.index') }}" class="btn btn-soft">Quay lại</a>
                </div>
            </form>
        </div>
    </div>
@endsection
