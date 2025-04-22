@extends('layouts.admin')

@section('content')
    <div class="main-content-inner">
        <div class="main-content-wrap">
            <div class="flex items-center flex-wrap justify-between gap20 mb-27">
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
                        <a href="{{ route('profile') }}">
                            <div class="text-tiny">Профиль</div>
                        </a>
                    </li>
                    <li>
                        <i class="icon-chevron-right"></i>
                    </li>
                    <li>
                        <div class="text-tiny">Редактировать Профиль</div>
                    </li>
                </ul>
            </div>
            <div class="wg-box">
                <form class="form-new-product form-style-1" action="{{ route('update-profile') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="{{ $user->id }}">
                    <fieldset class="name">
                        <div class="body-title">Имя пользователя <span class="tf-color-1">*</span></div>
                        <input class="flex-grow" type="text" placeholder="Имя пользователя" name="name" tabindex="0"
                            value="{{ $user->name }}" aria-required="true">
                    </fieldset>
                    <fieldset class="name">
                        <div class="body-title">Электронная почта<span class="tf-color-1">*</span></div>
                        <input class="flex-grow" type="email" placeholder="Электронная почта" name="email"
                            tabindex="0" value="{{ $user->email }}" aria-required="true">
                    </fieldset>
                    <fieldset class="name">
                        <div class="body-title">Новый пароль<span class="tf-color-1">*</span></div>
                        <input class="flex-grow" type="password" placeholder="Новый пароль" name="password  " tabindex="0"
                            value="" aria-required="true">
                    </fieldset>
                    <div class="bot">
                        <div></div>
                        <button class="tf-button w208" type="submit">Сохранить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
