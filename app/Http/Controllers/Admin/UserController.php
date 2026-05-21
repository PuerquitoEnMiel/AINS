<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * List all users with search and role filter.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Search by name or email
        if ($search = $request->input('search')) {
            $driver = DB::connection()->getDriverName();
            $likeOperator = $driver === 'pgsql' ? 'ilike' : 'like';
            $query->where(function ($q) use ($search, $likeOperator) {
                $q->where('name', $likeOperator, "%{$search}%")
                    ->orWhere('email', $likeOperator, "%{$search}%");
            });
        }

        // Filter by role
        if ($role = $request->input('role')) {
            $query->where('role', $role);
        }

        $users = $query->withCount(['favorites', 'reviews', 'conversations'])
            ->latest()
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Update user role via AJAX.
     */
    public function updateRole(Request $request, User $user)
    {
        $data = $request->validate([
            'role' => 'required|in:admin,teacher,student',
        ]);

        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => "Rol de {$user->name} actualizado a {$data['role']}.",
        ]);
    }
}
