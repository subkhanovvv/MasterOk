@extends('layouts.auth')       
@section('content')
    
<form class="form" method="POST" action="{{route('ProcessLogin')}}">
    @csrf
    <p class="form-title"> 
        <img src="{{ asset('../admin../assets/images/logo.png') }}">   
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