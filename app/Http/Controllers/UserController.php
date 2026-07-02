<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\JsonDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $role = $request->input('role');
        $kelas = $request->input('kelas');

        $users = User::query()
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('email', 'like', '%' . $search . '%')
                      ->orWhere('uid', 'like', '%' . $search . '%');
                });
            })
            ->when($role && $role !== 'all', function ($query) use ($role) {
                return $query->where('role', $role);
            })
            ->when($kelas && $kelas !== 'all', function ($query) use ($kelas) {
                return $query->where('kelas', $kelas);
            })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        $availableClasses = User::whereNotNull('kelas')
            ->where('kelas', '!=', '')
            ->distinct()
            ->pluck('kelas');

        return view('pages.users.index', compact('users', 'search', 'role', 'kelas', 'availableClasses'));
    }

    public function create()
    {
        return view('pages.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|string|email|max:255|unique:users,email',
            'password'    => 'required|string|min:6',
            'uid'         => 'required|string|max:50|unique:users,uid',
            'role'        => 'required|in:admin,user',
            'kelas'       => 'nullable|string|max:50',
            'no_hp'       => 'nullable|string|max:15',
            'rfid_status' => 'required|in:active,inactive',
        ]);

        User::create([
            'name'        => $request->name,
            'email'       => $request->email,
            'password'    => Hash::make($request->password),
            'uid'         => $request->uid,
            'role'        => $request->role,
            'kelas'       => $request->role === 'admin' ? null : $request->kelas,
            'no_hp'       => $request->no_hp,
            'rfid_status' => $request->rfid_status,
        ]);

        JsonDatabase::syncFromDatabase();

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('pages.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password'    => 'nullable|string|min:6',
            'uid'         => ['required', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
            'role'        => 'required|in:admin,user',
            'kelas'       => 'nullable|string|max:50',
            'no_hp'       => 'nullable|string|max:15',
            'rfid_status' => 'required|in:active,inactive',
        ]);

        $userData = [
            'name'        => $request->name,
            'email'       => $request->email,
            'uid'         => $request->uid,
            'role'        => $request->role,
            'kelas'       => $request->role === 'admin' ? null : $request->kelas,
            'no_hp'       => $request->no_hp,
            'rfid_status' => $request->rfid_status,
        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        JsonDatabase::syncFromDatabase();

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui!');
    }

    public function updateStatus(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'rfid_status' => 'required|in:active,inactive',
        ]);

        $user->update([
            'rfid_status' => $request->rfid_status,
        ]);

        JsonDatabase::syncFromDatabase();

        return response()->json([
            'success' => true,
            'message' => 'Status RFID berhasil diperbarui!',
        ]);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if (Auth::id() === $user->id) {
            return redirect()->route('users.index')->with('error', 'Anda tidak dapat menghapus diri sendiri!');
        }

        $user->delete();

        JsonDatabase::syncFromDatabase();

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus!');
    }

    public function autocomplete(Request $request)
    {
        $keyword = $request->query('keyword');
        $angkatan = $request->query('angkatan');
        $jurusan = $request->query('jurusan');
        $kelas = $request->query('kelas');
        $role = $request->query('role');

        $query = User::query();

        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', '%' . $keyword . '%')
                  ->orWhere('email', 'like', '%' . $keyword . '%')
                  ->orWhere('uid', 'like', '%' . $keyword . '%');
            });
        }

        if ($role && $role !== 'all') {
            $query->where('role', $role);
        }

        if ($angkatan && $angkatan !== 'all') {
            $query->where('kelas', 'like', $angkatan . '-%');
        }

        if ($jurusan && $jurusan !== 'all') {
            $query->where('kelas', 'like', '%-' . $jurusan . ' %');
        }

        if ($kelas) {
            $query->where('kelas', 'like', '%' . $kelas . '%');
        }

        $users = $query->orderBy('name', 'asc')->take(50)->get();

        return response()->json($users);
    }
}

