@extends('layouts.admin')

@section('content')
    <div class="card shadow-sm rounded-lg border-0">
        <div class="card-body p-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h4 font-weight-bold text-gray-800 mb-1">Настройки приложения</h1>
                    <p class="text-muted small mb-0">Управление основными параметрами системы</p>
                </div>
            </div>

            <hr class="my-4 bg-light">

            <form id="settings-form" method="POST" action="{{ route('settings.update') }}">
                @csrf
                <!-- General Settings -->
                <div class="mb-4">
                    <div class="d-flex align-items-center mb-4">
                        <h2 class="h5 font-weight-bold text-gray-800 mb-0">Основные настройки</h2>
                    </div>

                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <div class="mb-4">
                                <label for="site_name" class="form-label fw-bold">Название сайта</label>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-lg" id="site_name" name="name"
                                        value="{{ $settings->name }}" placeholder="Введите название" required>
                                </div>
                                <div class="invalid-feedback">Пожалуйста, укажите название сайта</div>
                            </div>

                            <div class="mb-0">
                                <label for="mini_name" class="form-label fw-bold">Короткое имя</label>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-lg" id="mini_name"
                                        name="mini_name" value="{{ $settings->mini_name }}"
                                        placeholder="Введите Короткое имя" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end pt-3 border-top">
                    <button type="submit" class="btn btn-primary">
                        Сохранить
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
