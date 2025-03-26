<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Checkpoint;
use Illuminate\Http\Request;
use App\Models\ArchiveStatus;

class AdminCheckpointController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $rooms_select = Room::orderBy('sort-index')->pluck('name', 'id')->all();
        $archive_statuses = ArchiveStatus::pluck('name', 'id')->all();
        $rooms = Room::orderBy('sort-index')->get();

        return view('admin.checkpoints.index', compact('rooms_select', 'archive_statuses', 'rooms'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $input = $request->all();
        $index = Checkpoint::all()->count();
        $input['sort-index'] = $index + 1;
        $input['archive_status_id'] = config('status.aktiv');
        Checkpoint::create($input);

        return redirect('/admin/checkpoints');
    }

    /**
     * Display the specified resource.
     */
    public function show(Checkpoint $checkpoint)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Checkpoint $checkpoint)
    {
        //
        $archive_statuses = ArchiveStatus::pluck('name', 'id')->all();
        $rooms = Room::pluck('name', 'id')->all();

        return view('admin.checkpoints.edit', compact('archive_statuses', 'checkpoint', 'rooms'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Checkpoint $checkpoint)
    {
        //
        $input = $request->all();
        $checkpoint->update($input);

        return redirect('/admin/checkpoints');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Checkpoint $checkpoint)
    {
        //
        $checkpoint->delete();

        return redirect('/admin/checkpoints');
    }
}
