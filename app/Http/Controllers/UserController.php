<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use DB;
use App\User;
use App\Role;
use Auth;
use Session;

class UserController extends Controller {

    public function __construct() {
        $this->middleware('auth');
    }

    public function index() {
        $users = User::orderBy('role_id', 'ASC')->orderBy('id','DESC')->paginate(10);
        return view('user.index', ['users' => $users]);
    }

    public function add_user(Request $request) {
        if ($request->isMethod('POST')) {
            $this->validate($request, [
                'name' => 'required|max:255',
                'role_id' => 'required',
                'email' => 'required|email|max:255|unique:users',
                'password' => 'required|min:6',
            ]);            
            $user = new User();
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->password = bcrypt($request->input('password'));
            $user->role_id = $request->input('role_id');
            $user->save();
            Session::flash('es_message', 'User Created Successfully.');
            return redirect('user/list');
        }
        $roles = Role::all();
        return view('user.add_user', ['roles' => $roles]);
    }
    
    public function edit_user(Request $request, $id = null){
        if ($request->isMethod('POST')) {
            $this->validate($request, [
                'name' => 'required|max:255',
                'role_id' => 'required',
                'email' => 'required|email|max:255|unique:users',               
            ]);            
            $user = User::find($id);
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            if(!empty($request->input('password'))){
                $user->password = bcrypt($request->input('password'));            
            }
            $user->role_id = $request->input('role_id');
            $user->save();
            Session::flash('es_message', 'User Edited Successfully.');
            return redirect('user/list');
        }
        $user = User::find($id);
        $roles = Role::all();
        return view('user.edit_user', ['roles' => $roles, 'user' => $user]);
    }
    
    public function delete_user($id=null){
        $user = User::find($id);
        $user->delete();
        Session::flash('es_message', 'User Deleted Successfully.');
        return redirect('user/list');
    }

    public function change_password() {
        return view('user.change_password');
    }

    public function change_password_save(Request $request) {
        $this->validate($request, [
            'password' => 'required'
        ]);
        $user_id = Auth::user()->id;
        if ($request->isMethod('POST')) {
            if (($request->input('password') == $request->input('password_confirmation')) && !empty($request->input('password'))) {
                $user = User::update_password($user_id, $request->input('password'));
                Session::flash('es_message', 'Password Changed Successfully.');
            } else {
                Session::flash('es_message', 'Password Can Not Be Saved.');
            }
            return redirect('/change/password');
        }
    }

}
