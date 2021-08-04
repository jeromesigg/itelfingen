<?php

namespace App\Http\Controllers;

use App\Role;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //       
        $user = Auth::user();
        if($user->isTeam() && !$user->isAdmin()){
            $users =  User::whereId($user->id)->paginate(10);
            $roles =Role::whereId($user->role_id)->pluck('name','id')->all();

        }
        else{
            $users = User::paginate(10);
            $roles = Role::pluck('name','id')->all();
        }
        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        if(!Auth::user()->isAdmin()){
            $roles = Role::where('is_admin', false)->pluck('name','id')->all();
        } else {
            $roles = Role::pluck('name','id')->all();

        }
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        if(trim($request->password) == ''){
            $input = $request->except('password');
        }
        else{
            $input = $request->all();
            $input['password'] = bcrypt($request->password);
        }
        
        if($file = $request->file('signature')){
            $path = Storage::putFileAs('signature/'.$user->username, $file, $user->username.'.'.$file->extension(), ['disk' => 'local']);
            $input['signature'] = $path;
        }

        User::create($input);

        return redirect('/admin/users');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        if(Auth::user()->isTeam() && !Auth::user()->isAdmin()){
            $roles =Role::whereId(Auth::user()->role_id)->pluck('name','id')->all();

        }
        else{
            $roles = Role::pluck('name','id')->all();
        }
        $user = User::findOrFail($id);

        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $user = User::findOrFail($id);


        if(trim($request->password) == ''){
            $input = $request->except('password');
        }
        else{
            $input = $request->all();
            $input['password'] = bcrypt($request->password);
        }

        if($file = $request->file('signature')){
            $path = Storage::putFileAs('signature/'.$user->username, $file, $user->username.'.'.$file->extension(), ['disk' => 'local']);
            $input['signature'] = $path;
        }

        $user->update($input);
        return redirect('/admin/users');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        User::findOrFail($id)->delete();
        return redirect('/admin/users');
    }

    public function get_signature(User $user)
    {
        /**this will force download your file**/
        return Storage::download($user->signature);
    }
    
}
