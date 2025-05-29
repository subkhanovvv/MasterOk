@extends('layouts.admin')

@section('content')
    <div class="card shadow-sm border-0">
        <div class="card-body p-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h4 font-weight-bold text-dark mb-0">Настройки приложения</h1>
                    <p class="text-muted mb-0">Управление основными параметрами системы</p>
                </div>
                <button type="submit" form="settings-form" class="btn btn-primary px-4 py-2 shadow-sm">
                    <i class="fas fa-save mr-2"></i> Сохранить изменения
                </button>
            </div>

            <hr class="my-4">

            <form id="settings-form" method="POST" action="#" enctype="multipart/form-data" class="needs-validation" novalidate>
                @csrf

                <!-- Branding Section -->
                <div class="mb-5">
                    <h2 class="h5 font-weight-bold text-dark mb-4">
                        <i class="fas fa-palette text-primary mr-2"></i> Брендинг
                    </h2>
                    
                    <div class="row">
                        <!-- Logo Upload -->
                        <div class="col-md-6 mb-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <label class="form-label fw-bold">Логотип</label>
                                    <p class="text-muted small mb-3">Основной логотип приложения</p>
                                    
                                    <div class="d-flex align-items-center">
                                        <div class="me-4">
                                            <div class="bg-light rounded-3 p-3 border" style="width: 120px; height: 60px;">
                                                <img src="/placeholder-logo.png" alt="Logo Preview" 
                                                     class="img-fluid h-100 w-auto d-block mx-auto" id="logo-preview">
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="file-upload-wrapper">
                                                <input type="file" id="logo" name="logo" 
                                                       class="file-upload-input" accept="image/png, image/svg+xml">
                                                <label for="logo" class="file-upload-label btn btn-outline-secondary w-100">
                                                    <i class="fas fa-cloud-upload-alt me-2"></i> Загрузить логотип
                                                </label>
                                                <small class="file-name text-muted d-block mt-2 small" id="logo-filename">Файл не выбран</small>
                                            </div>
                                            <div class="form-text small">Рекомендуемый размер: 240×80px, PNG/SVG</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Favicon Upload -->
                        <div class="col-md-6 mb-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <label class="form-label fw-bold">Фавикон</label>
                                    <p class="text-muted small mb-3">Иконка для вкладки браузера</p>
                                    
                                    <div class="d-flex align-items-center">
                                        <div class="me-4">
                                            <div class="bg-light rounded-3 p-2 border" style="width: 60px; height: 60px;">
                                                <img src="/placeholder-favicon.png" alt="Favicon Preview" 
                                                     class="img-fluid h-100 w-auto d-block mx-auto" id="favicon-preview">
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="file-upload-wrapper">
                                                <input type="file" id="favicon" name="favicon" 
                                                       class="file-upload-input" accept="image/png, image/x-icon">
                                                <label for="favicon" class="file-upload-label btn btn-outline-secondary w-100">
                                                    <i class="fas fa-cloud-upload-alt me-2"></i> Загрузить фавикон
                                                </label>
                                                <small class="file-name text-muted d-block mt-2 small" id="favicon-filename">Файл не выбран</small>
                                            </div>
                                            <div class="form-text small">Рекомендуемый размер: 32×32px, PNG/ICO</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- General Settings -->
                <div class="mb-4">
                    <h2 class="h5 font-weight-bold text-dark mb-4">
                        <i class="fas fa-cog text-primary mr-2"></i> Основные настройки
                    </h2>
                    
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="site_name" class="form-label fw-bold">Название сайта</label>
                                <input type="text" class="form-control form-control-lg" id="site_name" name="site_name" 
                                       value="Мой сайт" placeholder="Введите название" required>
                                <div class="invalid-feedback">Пожалуйста, укажите название сайта</div>
                            </div>
                            
                            <div class="mb-0">
                                <label for="site_description" class="form-label fw-bold">Описание сайта</label>
                                <textarea class="form-control" id="site_description" name="site_description" 
                                          rows="3" placeholder="Краткое описание вашего сайта"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end pt-3">
                    <button type="submit" class="btn btn-primary px-4 py-2 shadow-sm me-3">
                        <i class="fas fa-save mr-2"></i> Сохранить изменения
                    </button>
                    <a href="#" class="btn btn-outline-secondary px-4 py-2">Отмена</a>
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
        }
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1.2rem rgba(0,0,0,.08) !important;
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
                    fileName.textContent = file.name;
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        preview.src = event.target.result;
                    };
                    reader.readAsDataURL(file);
                } else {
                    fileName.textContent = 'Файл не выбран';
                    preview.src = '/placeholder-logo.png';
                }
            });

            // Favicon upload
            document.getElementById('favicon').addEventListener('change', function(e) {
                const file = e.target.files[0];
                const preview = document.getElementById('favicon-preview');
                const fileName = document.getElementById('favicon-filename');
                
                if (file) {
                    fileName.textContent = file.name;
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        preview.src = event.target.result;
                    };
                    reader.readAsDataURL(file);
                } else {
                    fileName.textContent = 'Файл не выбран';
                    preview.src = '/placeholder-favicon.png';
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