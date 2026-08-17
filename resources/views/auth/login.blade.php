@extends('layouts.auth')

@section('content')
<div class="w-full max-w-md bg-surface-container-lowest rounded-xl shadow-sm border border-surface-border p-8">
    <div class="text-center mb-8 flex flex-col items-center">
        <img src="{{ asset('images/logo_simpro_lockup_wordmark.png') }}" alt="SIMPRO Logo" class="h-32 w-auto mb-6 object-contain">
        <p class="text-body-md text-text-muted">Masuk ke akun Anda</p>
    </div>

    @if ($errors->any())
        <div class="mb-4 p-4 bg-error-container text-on-error-container rounded-lg text-body-sm">
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('login') ?? '/login' }}" method="POST">
        @csrf
        <div class="mb-5">
            <label for="username" class="block text-label-sm font-medium text-on-surface mb-2">Username / Email</label>
            <input type="text" name="username" id="username" class="w-full rounded border-outline-variant bg-surface-container-lowest px-4 py-2 text-body-main focus:ring-primary focus:border-primary transition-colors" value="{{ old('username') }}" required autofocus placeholder="Masukkan username">
        </div>

        <div class="mb-6">
            <label for="password" class="block text-label-sm font-medium text-on-surface mb-2">Password</label>
            <input type="password" name="password" id="password" class="w-full rounded border-outline-variant bg-surface-container-lowest px-4 py-2 text-body-main focus:ring-primary focus:border-primary transition-colors" required placeholder="Masukkan password">
        </div>

        <div class="flex items-center justify-between mb-6">
            <label class="flex items-center text-body-sm text-text-muted cursor-pointer hover:text-on-surface transition-colors">
                <input type="checkbox" name="remember" class="rounded border-outline-variant text-primary mr-2 focus:ring-primary">
                Ingat Saya
            </label>
        </div>

        <button type="submit" class="w-full bg-primary hover:bg-primary-light text-on-primary font-medium py-2.5 rounded-lg transition-all duration-200 ease-out active:scale-[0.97] hover:brightness-110 shadow-sm">
            Masuk
        </button>
    </form>
</div>
@endsection
