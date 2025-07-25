<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::latest()->when(request()->q, function ($users) {
            $users = $users->where('name', 'like', '%' . request()->q . '%');
        })->paginate(10);

        return view('admin.user.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.user.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required',
            'email'      => 'required|email|unique:users',
            'password'   => 'required|confirmed'
        ]);

        $user = User::create([
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => bcrypt($request->password),
        ]);

        if ($user) {
            return redirect()->route('admin.user.index')->with(['success' => 'Data Berhasil Disimpan!']);
        } else {
            return redirect()->route('admin.user.index')->with(['error' => 'Data Gagal Disimpan!']);
        }
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
        return view('admin.user.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'       => 'required',
            'email'      => 'required|email|unique:users,email,' . $user->id,
            'password'   => 'required|confirmed'
        ]);

        if ($request->password == "") {

            $user = User::findOrFail($user->id);
            $user->update([
                'name'       => $request->name,
                'email'      => $request->email
            ]);
        } else {

            $user = User::findOrFail($user->id);
            $user->update([
                'name'       => $request->name,
                'email'      => $request->email,
                'password'   => bcrypt($request->password),
            ]);
        }

        if ($user) {
            return redirect()->route('admin.user.index')->with(['success' => 'Data Berhasil Diupdate!']);
        } else {
            return redirect()->route('admin.user.index')->with(['error' => 'Data Gagal Diupdate!']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        if ($user) {
            return response()->json([
                'status' => 'success'
            ]);
        } else {
            return response()->json([
                'status' => 'error'
            ]);
        }
    }
}
