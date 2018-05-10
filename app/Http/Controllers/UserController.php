<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon ;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Hash;
use App\User;
use App\Role;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
        return view('user.user-list');
    }

    public function getUserData()
    {
        $users = DB::table('users')->join('roles', 'users.role_id', '=', 'roles.id')
            ->select(['users.id','users.name', 'users.username', 'users.email', 'roles.name as role', 'users.status', 'users.created_at']);

        return Datatables::of($users)
            ->addColumn('action', function ($users) {
                return 
                '<form method="POST" action="'.route('user.deactivate',base64_encode($users->id)).'">'
                .'<input type="hidden" name="_method" value="PATCH">'
                .'<button title="Nonaktifkan Pengguna" type="submit" class="btn btn-xs btn-primary waves-effect"><i class="material-icons">cancel</i></button>'
                .'</form>'
                .'<a title="Rubah Role" class="btn btn-xs btn-primary" href="'. url('role')."/".base64_encode($users->id) .'"><i class="material-icons">create</i></a>' 
                .'<form method="POST" action="'.route('user.activate',base64_encode($users->id)).'">'
                .'<input type="hidden" name="_method" value="PATCH">'
                .'<button title="Aktifkan Pengguna" type="submit" class="btn btn-xs btn-primary waves-effect"><i class="material-icons">add_circle</i></button>'
                .'</form>';
            })
            ->editColumn('created_at', function ($users) {
                return $users->created_at ? with(new Carbon($users->created_at))->format('m/d/Y') : '';
            })
            ->filterColumn('created_at', function ($query, $keyword) {
                $query->whereRaw("DATE_FORMAT(created_at,'%m/%d/%Y') like ?", ["%$keyword%"]);
            })
            ->editColumn('id', 'ID: {{$id}}')
            ->removeColumn('password')
            ->make(true);
    }

    public function activateUser($id){
        $idUser = base64_decode($id);
        $User = User::findOrfail($idUser);
        if ($User->status == "tidak aktif") {
            $User->status = "aktif";
            $User->save();

            return redirect()->route('user')->withSuccess('User Telah Diaktifkan');
        } else {
            return redirect()->route('user')->withSuccess('User Masih Aktif');
        }
    }

    public function deactivateUser($id){
        $idUser = base64_decode($id);
        $User = User::findOrfail($idUser);
        if ($User->status == "aktif") {
            $User->status = "tidak aktif";
            $User->save();

            return redirect()->route('user')->withSuccess('User Telah Dinonaktifkan');
        } else {
            return redirect()->route('user')->withSuccess('User Tidak Aktif');
        }
    }

    public function updateProfil(User $user, Request $request)
    {
        $user->update([
            'name' => request('nama'),
            'username' => request('username'),
            'email' => request('email'),
        ]);

        return redirect()->back()->withSuccess('Profil Sudah Diganti');
    }

    public function changePassword($id)
    {
        if (request('password') == request('konfirmasi')) {
            $user = User::findOrfail($id);
            $user->password = Hash::make(request('password'));
            $user->save();
        } else {
            return redirect()->back()->withDanger('Konfirmasi Password Tidak Sama');    
        }

        return redirect()->back()->withSuccess('Password Sudah Diganti');
    }

    public function editRole($id){
        $user = User::findOrfail(base64_decode($id));
        $roles = Role::all();
        return view('user.ganti-role',compact('roles','user'));
    }

    public function updateRole($id){
        $user = User::findOrfail($id);
        $user->role_id = request('role');
        $user->save();
        return redirect()->route('user')->withSuccess('Role Untuk '.$user->name.' Sudah Diganti');
    }

}
