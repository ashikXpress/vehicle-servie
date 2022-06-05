<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index() {
        $users = User::orderBy('name')->get();

        return view('user_management.all', compact('users'));
    }

    public function add() {
        return view('user_management.add');
    }

    public function addPost(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed'
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();

        $user->syncPermissions($request->permission);

        return redirect()->route('user.all')->with('message', 'User add successfully.');
    }

    public function edit(User $user) {
        return view('user_management.edit', compact('user'));
    }

    public function editPost(User $user, Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'password' => 'nullable|string|min:8|confirmed'
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->password) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        $user->syncPermissions($request->permission);

        return redirect()->route('user.all')->with('message', 'User edit successfully.');
    }
}
