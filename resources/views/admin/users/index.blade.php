@php
    $userQuery = trim((string) request('q', ''));
    $userRole = (string) request('role', '');
    $userRows = $users->filter(function ($user) use ($userQuery, $userRole): bool {
        if ($userRole !== '' && $user->role?->value !== $userRole) {
            return false;
        }

        $haystack = mb_strtolower($user->name.' '.$user->email);

        return $userQuery === '' || str_contains($haystack, mb_strtolower($userQuery));
    })->values();
@endphp

@extends('layouts.admin')

@section('title', __('admin.users'))

@section('content')
    <x-admin.data-table :is-empty="$userRows->isEmpty()">
        <x-slot:toolbar>
            <h1 class="mr-2 text-base font-semibold">{{ __('admin.users') }}</h1>
            <x-admin.segments>
                <x-admin.filter :href="route('admin.users.index')" :active="$userRole === '' && $userQuery === ''">{{ __('admin.all_orders') }}</x-admin.filter>
                @foreach ($roles as $role)
                    <x-admin.filter :href="route('admin.users.index', ['role' => $role->value, 'q' => $userQuery])" :active="$userRole === $role->value">{{ __("admin.role_{$role->value}") }}</x-admin.filter>
                @endforeach
            </x-admin.segments>
            <x-admin.search class="ml-auto w-full max-w-sm" :action="route('admin.users.index')" :value="$userQuery" />
            <x-admin.button type="button" data-open-dialog="user-create">{{ __('admin.new_user') }}</x-admin.button>
        </x-slot:toolbar>
        <x-slot:head>
            <th class="px-4 py-3 font-medium">{{ __('admin.name') }}</th>
            <th class="px-4 py-3 font-medium">{{ __('admin.email') }}</th>
            <th class="px-4 py-3 font-medium">{{ __('admin.role') }}</th>
            <th class="px-4 py-3"></th>
        </x-slot:head>
        @foreach ($userRows as $user)
            <tr class="hover:bg-stone-50">
                <td class="px-4 py-3 font-medium">{{ $user->name }}</td>
                <td class="px-4 py-3 text-stone-600">{{ $user->email }}</td>
                <td class="px-4 py-3 text-stone-500">{{ __("admin.role_{$user->role?->value}") }}</td>
                <td class="px-2 py-2">
                    <x-admin.row-menu :edit="'#user-'.$user->id" :delete="auth()->id() === $user->id ? null : route('admin.users.destroy', $user)" />
                </td>
            </tr>
        @endforeach
    </x-admin.data-table>

    <x-admin.dialog id="user-create" :title="__('admin.new_user')" :open="$errors->any() && old('dialog') === 'user-create'">
        <form method="POST" action="{{ route('admin.users.store') }}" class="grid gap-3 sm:grid-cols-2">
            @csrf
            <input type="hidden" name="dialog" value="user-create">
            <x-admin.field name="name" :label="__('admin.name')" :value="old('dialog') === 'user-create' ? old('name') : ''" required />
            <x-admin.field name="email" type="email" :label="__('admin.email')" :value="old('dialog') === 'user-create' ? old('email') : ''" required />
            <x-admin.field name="password" type="password" :label="__('admin.password')" required />
            <x-admin.field name="role" type="select" :label="__('admin.role')">
                @foreach ($roles as $role)
                    <option value="{{ $role->value }}" @selected(old('role') === $role->value)>{{ __("admin.role_{$role->value}") }}</option>
                @endforeach
            </x-admin.field>
            <div class="sm:col-span-2"><x-admin.button>{{ __('admin.save') }}</x-admin.button></div>
        </form>
    </x-admin.dialog>

    @foreach ($users as $user)
        <x-admin.dialog :id="'user-'.$user->id" :title="$user->name" :open="$errors->any() && (string) old('dialog') === 'user-'.$user->id">
            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="grid gap-3 sm:grid-cols-2">
                @csrf
                @method('PUT')
                <input type="hidden" name="dialog" value="user-{{ $user->id }}">
                <x-admin.field name="name" :label="__('admin.name')" :value="(string) old('dialog') === 'user-'.$user->id ? old('name', $user->name) : $user->name" required />
                <x-admin.field name="email" type="email" :label="__('admin.email')" :value="(string) old('dialog') === 'user-'.$user->id ? old('email', $user->email) : $user->email" required />
                <x-admin.field name="password" type="password" :label="__('admin.password_optional')" />
                <x-admin.field name="role" type="select" :label="__('admin.role')">
                    @foreach ($roles as $role)
                        <option value="{{ $role->value }}" @selected($user->role?->value === $role->value)>{{ __("admin.role_{$role->value}") }}</option>
                    @endforeach
                </x-admin.field>
                <p class="text-sm text-stone-500 sm:col-span-2">{{ auth()->id() === $user->id ? __('admin.keep_admin') : '' }}</p>
                <div class="sm:col-span-2"><x-admin.button>{{ __('admin.save') }}</x-admin.button></div>
            </form>
        </x-admin.dialog>
    @endforeach
@endsection
