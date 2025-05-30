@extends('layouts.admin')

@section('content')
    <div class="card shadow-sm rounded-lg border-0">
        <div class="card-body p-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h4 font-weight-bold text-gray-800 mb-1">Настройки приложения</h1>
                    <p class="text-muted small mb-0">Управление основными параметрами системы</p>
                </div>
                <button type="submit" form="settings-form" class="btn btn-primary px-4 py-2 shadow-sm rounded-pill">
                    <i class="fas fa-save mr-2"></i> Сохранить изменения
                </button>
            </div>

            <hr class="my-4 bg-light">

            <form id="settings-form" method="POST" action="#" enctype="multipart/form-data" class="needs-validation" novalidate>
                @csrf

                <!-- Branding Section -->
                <div class="mb-5">
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-primary bg-gradient p-2 rounded-circle me-3">
                            <i class="fas fa-palette text-white fa-fw"></i>
                        </div>
                        <h2 class="h5 font-weight-bold text-gray-800 mb-0">Брендинг</h2>
                    </div>
                    
                    <div class="row g-4">
                        <!-- Logo Upload -->
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-transparent border-0 py-3">
                                    <h3 class="h6 font-weight-bold text-gray-800 mb-0">Логотип</h3>
                                </div>
                                <div class="card-body pt-0">
                                    <p class="text-muted small mb-3">Основной логотип приложения</p>
                                    
                                    <div class="d-flex align-items-center">
                                        <div class="me-4">
                                            <div class="bg-light rounded-3 p-3 border d-flex align-items-center justify-content-center" style="width: 140px; height: 80px;">
                                                <img src="/placeholder-logo.png" alt="Logo Preview" 
                                                     class="img-fluid h-100 w-auto" id="logo-preview">
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="file-upload-wrapper">
                                                <input type="file" id="logo" name="logo" 
                                                       class="file-upload-input" accept="image/png, image/svg+xml">
                                                <label for="logo" class="file-upload-label btn btn-outline-secondary w-100 rounded-pill">
                                                    <i class="fas fa-cloud-upload-alt me-2"></i> Загрузить логотип
                                                </label>
                                                <small class="file-name text-muted d-block mt-2 small" id="logo-filename">Файл не выбран</small>
                                            </div>
                                            <div class="form-text small mt-1">Рекомендуемый размер: 240×80px, PNG/SVG</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Favicon Upload -->
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-transparent border-0 py-3">
                                    <h3 class="h6 font-weight-bold text-gray-800 mb-0">Фавикон</h3>
                                </div>
                                <div class="card-body pt-0">
                                    <p class="text-muted small mb-3">Иконка для вкладки браузера</p>
                                    
                                    <div class="d-flex align-items-center">
                                        <div class="me-4">
                                            <div class="bg-light rounded-3 p-2 border d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                                                <img src="/placeholder-favicon.png" alt="Favicon Preview" 
                                                     class="img-fluid h-100 w-auto" id="favicon-preview">
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="file-upload-wrapper">
                                                <input type="file" id="favicon" name="favicon" 
                                                       class="file-upload-input" accept="image/png, image/x-icon">
                                                <label for="favicon" class="file-upload-label btn btn-outline-secondary w-100 rounded-pill">
                                                    <i class="fas fa-cloud-upload-alt me-2"></i> Загрузить фавикон
                                                </label>
                                                <small class="file-name text-muted d-block mt-2 small" id="favicon-filename">Файл не выбран</small>
                                            </div>
                                            <div class="form-text small mt-1">Рекомендуемый размер: 32×32px, PNG/ICO</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

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
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-globe text-muted"></i>
                                    </span>
                                    <input type="text" class="form-control form-control-lg border-start-0" id="site_name" name="site_name" 
                                           value="Мой сайт" placeholder="Введите название" required>
                                </div>
                                <div class="invalid-feedback">Пожалуйста, укажите название сайта</div>
                            </div>
                            
                            <div class="mb-0">
                                <label for="site_description" class="form-label fw-bold">Описание сайта</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 align-items-start pt-2">
                                        <i class="fas fa-align-left text-muted"></i>
                                    </span>
                                    <textarea class="form-control border-start-0 ps-3" id="site_description" name="site_description" 
                                              rows="3" placeholder="Краткое описание вашего сайта"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end pt-3 border-top">
                    <a href="#" class="btn btn-outline-secondary px-4 py-2 rounded-pill me-3">Отмена</a>
                    <button type="submit" class="btn btn-primary px-4 py-2 shadow-sm rounded-pill">
                        <i class="fas fa-save mr-2"></i> Сохранить изменения
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
            box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,.1) !important;
        }
        .card-header {
            border-radius: 12px 12px 0 0 !important;
        }
        .input-group-text {
            transition: all 0.3s ease;
        }
        .form-control:focus + .input-group-text {
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