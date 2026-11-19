@extends('layouts.app')

@section('title', __('admin.login'))

@section('content')
    <form method="POST" action="{{ route('admin.login.store') }}" class="admin-surface mx-auto max-w-md p-8">
        @csrf
        <h1 class="text-2xl font-semibold tracking-tight">{{ __('admin.login') }}</h1>

        <label class="mt-5 block text-sm font-medium text-stone-700" for="email">{{ __('admin.email') }}</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus class="admin-input mt-1.5 w-full">
        @error('email') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror

        <label class="mt-4 block text-sm font-medium text-stone-700" for="password">{{ __('admin.password') }}</label>
        <input id="password" name="password" type="password" required class="admin-input mt-1.5 w-full">

        <label class="mt-4 flex items-center gap-2 text-sm">
            <input type="checkbox" name="remember" value="1">
            {{ __('admin.remember') }}
        </label>

        <button type="submit" class="mt-6 inline-flex cursor-pointer rounded-xl bg-blue-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-800">{{ __('admin.login') }}</button>
    </form>
@endsection
