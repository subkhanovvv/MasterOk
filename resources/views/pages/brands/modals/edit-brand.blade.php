@extends('layouts.admin')

@section('content')
    <div class="main-content-inner">
        <div class="main-content-wrap">
            <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                <h3>Информация о бренде</h3>
                <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                    <li>
                        <a href="{{ route('index') }}">
                            <div class="text-tiny">Панель</div>
                        </a>
                    </li>
                    <li>
                        <i class="icon-chevron-right"></i>
                    </li>
                    <li>
                        <a href="{{ route('brand') }}">
                            <div class="text-tiny">Бренды</div>
                        </a>
                    </li>
                    <li>
                        <i class="icon-chevron-right"></i>
                    </li>
                    <li>
                        <div class="text-tiny">Редактировать бренд</div>
                    </li>
                </ul>
            </div>
            <div class="wg-box">
                <form class="form-new-product form-style-1" action="{{ route('update-brand') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="{{ $brands->id }}">
                    <fieldset class="name">
                        <div class="body-title">Название бренда <span class="tf-color-1">*</span></div>
                        <input class="flex-grow @error('name') is-invalid @enderror" type="text"
                            placeholder="Название бренда" name="name" tabindex="0" value="{{ $brands->name }}"
                            aria-required="true" required>
                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong class="text-danger">{{ $message }}</strong>
                            </span>
                        @enderror
                    </fieldset>
                    <fieldset class="name">
                        <div class="body-title">Телефон бренда<span class="tf-color-1">*</span></div>
                        <input class="flex-grow  @error('phone') is-invalid @enderror" type="text"
                            placeholder="Телефон бренда" name="phone" tabindex="0" value="{{ $brands->phone }}"
                            aria-required="true" required>
                        @error('phone')
                            <span class="invalid-feedback" role="alert">
                                <strong class="text-danger">{{ $message }}</strong>
                            </span>
                        @enderror
                    </fieldset>
                    <fieldset class="name">
                        <div class="body-title">Описание бренда<span class="tf-color-1">*</span></div>
                        <input class="flex-grow @error('description') is-invalid @enderror" type="text"
                            placeholder="необязательно" name="description" tabindex="0" value="{{ $brands->description }}"
                            aria-required="true" required>
                        @error('description')
                            <span class="invalid-feedback" role="alert">
                                <strong class="text-danger">{{ $message }}</strong>
                            </span>
                        @enderror
                    </fieldset>
                    <fieldset>
                        <div class="body-title">Загрузить изображения <span class="tf-color-1">*</span>
                        </div>
                        <div class="upload-image flex-grow">
                            @if ($brands->photo)
                                <div class="item" id="imgpreview">
                                    <img src="{{ Storage::url($brands->photo) }}" alt="{{ $brands->name }}"
                                        style="max-width: 200px; margin-top: 10px;">
                                </div>
                            @endif
                            <div id="upload-file" class="item up-load">
                                <label class="uploadfile" for="myFile">
                                    <span class="icon">
                                        <i class="icon-upload-cloud"></i>
                                    </span>
                                    <span class="body-text">Перетащите изображения сюда или выберите <span
                                            class="tf-color">Нажмите для выбора</span></span>
                                    <input type="file" id="myFile" name="photo" accept="photo/*">
                                </label>
                            </div>
                        </div>
                    </fieldset>

                    <div class="bot">
                        <div></div>
                        <button class="tf-button w208" type="submit">Сохранить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        $(function() {
            $("#myFile").on("change", function(e) {
                const [file] = this.files;
                if (file) {
                    $("#imgpreview img").attr("src", URL.createObjectURL(file));
                    $("#imgpreview").show();
                }
            });
            $("input[name='phone']").on("input", function() {
                $(this).val(StringTophone($(this).val()));
            });

        });

        function StringTophone(text) {
            let digits = text.replace(/\D/g, ""); // Remove non-digit characters

            if (digits.startsWith("998")) {
                return "+" + digits;
            }

            return "+998" + digits;
        }
    </script>
@endsection
