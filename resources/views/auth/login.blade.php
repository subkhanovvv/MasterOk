@extends('layouts.auth')
@section('content')
    <form class="form" method="POST" action="{{ route('ProcessLogin') }}">
        @csrf
        <p class="form-title ">
                @if ($settings->name)
                    <h1 style="color: #4F46E5" class="text-center">{{ $settings->name }}</h1>
                @endif
        <div class="text-center login-title">Войдите в свой аккаунт</div>
        </p>
        <div class="input-container">
            <input type="text" placeholder="Введите имя пользователя" name="name">
        </div>
        <div class="input-container">
            <input type="password" placeholder="Введите пароль" name="password">
        </div>
        <button type="submit" class="submit">
            Войти
        </button>
    </form>
@endsection
