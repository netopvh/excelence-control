<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function getEmployees()
    {
        return response()->json(User::with('roles')->get());
    }

    public function getUsers()
    {
        $users = User::with(['roles' => function ($query) {
            $query->where('name', '!=', 'superadmin');
        }]);

        return DataTables::of($users)
            ->editColumn('created_at', function ($model) {
                return $model->created_at->format('d/m/Y');
            })
            ->editColumn('roles', function ($model) {
                return $model->roles->map(function ($role) {
                    return '<span class="badge bg-primary">' . e($role->name) . '</span>';
                })->implode(' ');
            })
            ->addColumn('action', function ($model) {
                return $model->id;
            })
            ->setRowId('id')
            ->rawColumns(['roles'])
            ->make(true);
    }

    public function show($id)
    {
        $user = User::with('roles')->findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'username' => 'required',
            'password' => 'required',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => bcrypt($request->password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User created',
            'data' => $user
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'username' => 'required',
        ]);

        $user = User::findOrFail($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->username = $request->username;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'User updated',
            'data' => $user
        ]);
    }

    public function updatePassword(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|confirmed',
        ]);

        $user = User::find($id);
        $user->password = bcrypt($request->password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password updated',
            'data' => $user
        ]);
    }
}
