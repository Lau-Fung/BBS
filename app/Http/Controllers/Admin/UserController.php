<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.users.index', [
            'users' => User::with('roles')->paginate(20),
            'roles' => Role::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::orderBy('name')->pluck('name','id'); // for dropdown
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        // create user
        $data = $request->validated();

        $user = new User();
        $user->name  = $data['name'];
        $user->email = $data['email'];
        $user->password = Hash::make($data['password'] ?? str()->random(16));
        $user->email_verified_at = $request->boolean('verified') ? now() : null;
        $user->save();

        // assign role(s)
        if (!empty($data['roles'])) {
            $guard = config('auth.defaults.guard', 'web');

            $roles = collect($data['roles'])->map(function ($value) use ($guard) {
                return is_numeric($value)
                    ? Role::findById((int) $value, $guard)   // resolve id → Role
                    : Role::findByName($value, $guard);      // resolve name → Role
            })->filter(); // drop nulls

            $user->syncRoles($roles);
        }

        return redirect()->route('admin.users.index')
            ->with('status', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', [
            'user' => $user->load('roles'),
            'roles' => Role::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        // $this->authorize('users.assign-roles'); // optional Gate if you add it
        $request->validate([
            'roles' => ['array'],
            'roles.*' => ['exists:roles,name'],
        ]);
        $user->syncRoles($request->input('roles', []));
        return back()->with('status', __('Roles updated.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        // Don’t allow deleting yourself
        if (auth()->id() === $user->id) {
            return back()->withErrors(['user' => 'You cannot delete your own account.']);
        }

        // If using soft deletes:
        $user->delete();

        // If you want hard delete instead:
        // $user->forceDelete();

        return back()->with('status', 'User deleted successfully.');
    }
}
