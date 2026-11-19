<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\StaffRole;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function index(): View
    {
        return view('admin.users.index', [
            'users' => User::query()->orderBy('name')->get(),
            'roles' => StaffRole::cases(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => $data['role'],
            'is_admin' => $data['role'] === StaffRole::Admin->value,
        ]);

        return back()->with('status', __('admin.saved'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $this->validated($request, $user);

        if ($request->user()?->is($user) && $data['role'] !== StaffRole::Admin->value) {
            throw ValidationException::withMessages([
                'role' => __('admin.keep_admin'),
            ]);
        }

        $user->fill([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'is_admin' => $data['role'] === StaffRole::Admin->value,
        ]);

        if ($data['password'] !== null) {
            $user->password = $data['password'];
        }

        $user->save();

        return back()->with('status', __('admin.saved'));
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($request->user()?->is($user)) {
            return back()->with('status', __('admin.keep_self'));
        }

        $user->delete();

        return back()->with('status', __('admin.deleted'));
    }

    /**
     * @return array{name: string, email: string, password: string|null, role: string}
     */
    private function validated(Request $request, ?User $user = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user)],
            'password' => [$user === null ? 'required' : 'nullable', 'string', Password::min(8)],
            'role' => ['required', Rule::enum(StaffRole::class)],
        ]);

        return [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'] ?? null,
            'role' => $data['role'],
        ];
    }
}
