@extends('layouts.auth')       
@section('content')
    
<form class="form">
    <p class="form-title"> 
        <img src="{{ asset('images/logo/logo3.png') }}">   
        <div class="text-center login-title">Войдите в свой аккаунт</div>
   </p>
    <div class="input-container">
        <input type="text" placeholder="Введите имя пользователя">
    </div>
    <div class="input-container">
        <input type="password" placeholder="Введите пароль">
    </div>
    <button type="submit" class="submit">
        Войти
    </button>
</form>
@endsection