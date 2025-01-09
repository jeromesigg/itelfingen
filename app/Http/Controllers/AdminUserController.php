<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\ArchiveStatus;
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
        if ($user->isTeam() && ! $user->isAdmin()) {
            $users = User::whereId($user->id)->get();
            $roles = Role::whereId($user->role_id)->pluck('name', 'id')->all();
        } else {
            $users = User::all();
            $roles = Role::pluck('name', 'id')->all();
        }
        $archive_statuses = ArchiveStatus::pluck('name', 'id')->all();

        return view('admin.users.index', compact('users', 'roles', 'archive_statuses'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        if (! Auth::user()->isAdmin()) {
            $roles = Role::where('is_admin', false)->pluck('name', 'id')->all();
        } else {
            $roles = Role::pluck('name', 'id')->all();
        }

        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        if (trim($request->password) == '') {
            $input = $request->except('password');
        } else {
            $input = $request->all();
            $input['password'] = bcrypt($request->password);
        }

        if ($file = $request->file('signature')) {
            $path = Storage::putFileAs('signature/'.$user->username, $file, $user->username.'.'.$file->extension(), ['disk' => 'local']);
            $input['signature'] = $path;
        }
        $input['is_active'] = 1;

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
        if (Auth::user()->isTeam() && ! Auth::user()->isAdmin()) {
            $roles = Role::whereId(Auth::user()->role_id)->pluck('name', 'id')->all();
        } else {
            $roles = Role::pluck('name', 'id')->all();
        }
        $user = User::findOrFail($id);
        $archive_statuses = ArchiveStatus::pluck('name', 'id')->all();

        return view('admin.users.edit', compact('user', 'roles', 'archive_statuses'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $user = User::findOrFail($id);

        if (trim($request->password) == '') {
            $input = $request->except('password');
        } else {
            $input = $request->all();
            $input['password'] = bcrypt($request->password);
        }

        if ($file = $request->file('signature')) {
            $path = Storage::putFileAs('signature/'.$user->username, $file, $user->username.'.'.$file->extension(), ['disk' => 'local']);
            $input['signature'] = $path;
        }
        if($input['archive_status_id'] == config('status.aktiv')) {
            $input['archive_status_id'] = 1;
        }
        else {
            $input['archive_status_id'] = 0;
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
