<?php

namespace App\Http\Controllers;

use App\Enums\ArchiveState;
use App\Enums\UserRole;
use App\Models\ArchiveStatus;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

/**
 * Admins verwalten alle Benutzer (create/store/destroy nur in der Routengruppe role:admin).
 * Team und Verwalter sehen und bearbeiten nur ihr eigenes Profil, ohne Rolle und Status.
 */
class AdminUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
        $user = Auth::user();
        if (! $user->isAdmin()) {
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
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        //
        $request->validate(['role_id' => ['nullable', Rule::enum(UserRole::class)]]);
        $input = $request->only(['username', 'fullname', 'phone', 'role_id']);
        if (trim($request->password ?? '') != '') {
            $input['password'] = bcrypt($request->password);
        }
        $input['is_active'] = 1;

        $user = User::create($input);

        if ($file = $request->file('signature')) {
            $user->update(['signature' => $this->storeSignature($user, $file)]);
        }

        return redirect('/admin/users');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
        $user = $this->findEditable($id);
        if (! Auth::user()->isAdmin()) {
            $roles = Role::whereId(Auth::user()->role_id)->pluck('name', 'id')->all();
        } else {
            $roles = Role::pluck('name', 'id')->all();
        }
        // Das Formular zeigt den Status als Archiv-Status, gespeichert wird is_active.
        $user->archive_status_id = $user->is_active ? ArchiveState::Aktiv : ArchiveState::Archiviert;
        $archive_statuses = ArchiveStatus::pluck('name', 'id')->all();

        return view('admin.users.edit', compact('user', 'roles', 'archive_statuses'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
        $user = $this->findEditable($id);
        $request->validate(['role_id' => ['nullable', Rule::enum(UserRole::class)]]);

        $input = $request->only(['username', 'fullname', 'phone']);
        if (trim($request->password ?? '') != '') {
            $input['password'] = bcrypt($request->password);
        }
        if ($file = $request->file('signature')) {
            $input['signature'] = $this->storeSignature($user, $file);
        }
        // Rolle und Status setzt nur ein Admin.
        if (Auth::user()->isAdmin()) {
            if ($request->filled('role_id')) {
                $input['role_id'] = $request->role_id;
            }
            if ($request->filled('archive_status_id')) {
                $input['is_active'] = ArchiveState::tryFrom($request->integer('archive_status_id')) === ArchiveState::Aktiv;
            }
        }

        $user->update($input);

        return redirect('/admin/users');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
        User::findOrFail($id)->delete();

        return redirect('/admin/users');
    }

    public function get_signature(User $user)
    {
        abort_unless(Auth::user()->isAdmin() || $user->is(Auth::user()), 403);
        abort_if(empty($user->signature), 404);

        /**this will force download your file**/
        return Storage::download($user->signature);
    }

    /**
     * Admins bearbeiten alle Benutzer, alle anderen nur sich selbst.
     */
    private function findEditable($id): User
    {
        $user = User::findOrFail($id);
        abort_unless(Auth::user()->isAdmin() || $user->is(Auth::user()), 403);

        return $user;
    }

    private function storeSignature(User $user, $file): string
    {
        return Storage::putFileAs('signature/'.$user->username, $file, $user->username.'.'.$file->extension(), ['disk' => 'local']);
    }
}
