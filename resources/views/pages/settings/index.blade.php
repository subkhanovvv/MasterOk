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

            <form id="settings-form" method="POST" action="#" enctype="multipart/form-data" class="needs-validation"
                novalidate>
                @csrf
                <!-- General Settings -->
                <div class="mb-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-primary bg-gradient p-2 rounded-circle me-3">
                            <i class="fas fa-cog text-white fa-fw"></i>
                        </div>
                        <h2 class="h5 font-weight-bold text-gray-800 mb-0">Основные настройки</h2>
                    </div>

                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-transparent border-0 py-3">
                            <h3 class="h6 font-weight-bold text-gray-800 mb-0">Информация о сайте</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                <label for="site_name" class="form-label fw-bold">Название сайта</label>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-lg" id="site_name"
                                        name="name" value="{{$settings->name}}" placeholder="Введите название" required>
                                </div>
                                <div class="invalid-feedback">Пожалуйста, укажите название сайта</div>
                            </div>

                            <div class="mb-0">
                                <label for="site_description" class="form-label fw-bold">Описание сайта</label>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-lg"
                                        id="site_description" name="site_description" value="{{$settings->mini_name}}"
                                        placeholder="Введите описание" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end pt-3 border-top">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-2"></i> Сохранить
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .file-upload-input {
            width: 0.1px;
            height: 0.1px;
            opacity: 0;
            overflow: hidden;
            position: absolute;
            z-index: -1;
        }

        .file-upload-label {
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .file-upload-label:hover {
            background-color: #f8f9fa;
        }

        .card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            border-radius: 12px !important;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, .1) !important;
        }

        .card-header {
            border-radius: 12px 12px 0 0 !important;
        }

        .input-group-text {
            transition: all 0.3s ease;
        }

        .form-control:focus+.input-group-text {
            border-color: #86b7fe;
            background-color: #f8f9fa;
        }

        .bg-gradient {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        }

        .rounded-pill {
            border-radius: 50rem !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // File upload handling with preview
        document.addEventListener('DOMContentLoaded', function() {
            // Logo upload
            document.getElementById('logo').addEventListener('change', function(e) {
                const file = e.target.files[0];
                const preview = document.getElementById('logo-preview');
                const fileName = document.getElementById('logo-filename');

                if (file) {
                    fileName.textContent = file.name.length > 20 ?
                        file.name.substring(0, 15) + '...' + file.name.split('.').pop() :
                        file.name;
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        preview.src = event.target.result;
                        preview.classList.add('shadow-sm');
                    };
                    reader.readAsDataURL(file);
                } else {
                    fileName.textContent = 'Файл не выбран';
                    preview.src = '/placeholder-logo.png';
                    preview.classList.remove('shadow-sm');
                }
            });

            // Favicon upload
            document.getElementById('favicon').addEventListener('change', function(e) {
                const file = e.target.files[0];
                const preview = document.getElementById('favicon-preview');
                const fileName = document.getElementById('favicon-filename');

                if (file) {
                    fileName.textContent = file.name.length > 20 ?
                        file.name.substring(0, 15) + '...' + file.name.split('.').pop() :
                        file.name;
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        preview.src = event.target.result;
                        preview.classList.add('shadow-sm');
                    };
                    reader.readAsDataURL(file);
                } else {
                    fileName.textContent = 'Файл не выбран';
                    preview.src = '/placeholder-favicon.png';
                    preview.classList.remove('shadow-sm');
                }
            });

            // Form validation
            const form = document.getElementById('settings-form');
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    </script>
@endpush
