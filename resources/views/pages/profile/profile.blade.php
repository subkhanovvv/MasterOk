@extends('layouts.admin')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>

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
                        <div class="text-tiny">Профиль</div>
                    </li>
                </ul>
            </div>

            <div class="container mx-auto mt-8">
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex items-center space-x-6">
                        <div class="w-24 h-24">
                            <img class="w-full h-full rounded-full object-cover" src="{{ asset('images/avatar/user.png') }}"
                                alt="Profile Picture">
                        </div>
                        <div>
                            <h1 class="text-2xl font-semibold">{{ $user->name }}</h1>
                            <p class="text-gray-600">{{ $user->email }}</p>
                        </div>
                        <div>
                            <a href="{{ route('edit-profile', ['id' => $user->id]) }}"
                                class="fa-solid fa-pen-to-square text-4xl" style="color:#5271ff"></a>
                        </div>
                    </div>
                </div>
                <div class="bg-white shadow rounded p-6 mt-6 space-x-4">
                    <h3 class="text-3xl font-semibold mb-4 pl-4">Данные профиля</h3>
                    <br>
                    <ul class="space-y-4 text-2xl pl-2">
                        <li><strong>Имя: </strong>{{ $user->name }}</li>
                        <li><strong>Почта: </strong> {{ $user->email }}</li>
                        <li><strong>Зарегистрирован: </strong> {{ $user->created_at->format('d-m-y') }}</li>
                        <li><strong>Обновление: </strong> {{ $user->updated_at ?? 'Не обновлено' }}</li>
                    </ul>
                </div>
            </div>
        </div>

    </div>
@endsection
