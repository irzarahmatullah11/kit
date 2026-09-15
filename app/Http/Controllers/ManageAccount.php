<?php

namespace App\Http\Controllers;


use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use App\Models\Employ;
use App\Models\User;

class ManageAccount extends Controller
{
    public function showAccounts():View{
        $employees = Employ::with('roleData')->get();
        if (!$employees) {
            abort(404, 'Data tidak ditemukan');
        }
        return view('manageacc', compact('employees'));
    }

    public function createAccount():View{
        $roles = Role::all();
        return view('manageaccounts.add', compact('roles'));
    }

    public function storeAccount(Request $request){
        $request->validate([
            'employ_name' => 'required|string|max:255',
            'email'       => 'required|email|max:255|unique:employ,email',
            'role'        => 'required|exists:role,role_id',
            'password'    => 'required|string|min:6',
        ], [
            'employ_name.required' => 'Nama employee wajib diisi.',
            'email.required'       => 'Email wajib diisi.',
            'email.unique'         => 'Email ini sudah terdaftar.',
            'role.required'        => 'Role wajib dipilih.',
            'role.exists'          => 'Role yang dipilih tidak valid.',
            'password.required'    => 'Password wajib diisi.',
            'password.min'         => 'Password minimal harus 6 karakter.'
        ]);

        $user = new Employ();
        
        $user->employ_name = $request->employ_name;
        $user->email       = $request->email;
        $user->role        = $request->role; 
        $user->password    = bcrypt($request->password); 

        $user->save();

        return redirect()->route('admin.account.show')->with('success', 'Akun employee berhasil ditambahkan!');
    }

    public function editAccount($id):view{
        $user = Employ::find($id);
        $roles = Role::all();

        if (!$user) {
            abort(404, 'Data tidak ditemukan');
        }
        return view('manageaccounts.edit', compact('user', 'roles'));
    }

    public function editpush(Request $request, $id){
        $request->validate([
            'employ_name' => 'required|string|max:255',
            'email'       => 'required|email|max:255|unique:employ,email,' . $id . ',employ_id',
            'role'        => 'required|exists:role,role_id',
        ], [
            'employ_name.required' => 'Nama employee wajib diisi.',
            'email.required'       => 'Email wajib diisi.',
            'email.unique'         => 'Email ini sudah digunakan oleh pengguna lain.',
            'role.required'        => 'Role wajib dipilih.',
            'role.exists'          => 'Role yang dipilih tidak valid.'
        ]);

        $user = Employ::find($id);

        if (!$user) {
            abort(404, 'Data tidak ditemukan');
        }

        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        $user->employ_name = $request->employ_name;
        $user->email       = $request->email;
        $user->role        = $request->role; 

        $user->save();

        return redirect()->route('admin.account.show')->with('success', 'Data employee berhasil diperbarui!');
    }

    public function deleteAccount($id)
    {
        $user = Employ::find($id);

        if (!$user) {
            return redirect()->route('admin.account.show')->with('error', 'Data employee tidak ditemukan.');
        }

        $user->delete();

        return redirect()->route('admin.account.show')->with('success', 'Akun employee berhasil dihapus!');
    }
}
