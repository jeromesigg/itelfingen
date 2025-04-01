<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use App\Models\ArchiveStatus;

class AdminRoomsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $rooms = Room::orderBy('sort-index')->get();

        return view('admin.rooms.index', compact('rooms'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('admin.rooms.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $input = $request->all();
        $index = Room::all()->count();
        $input['sort-index'] = $index + 1;
        $input['archive_status_id'] = config('status.aktiv');
        Room::create($input);

        return redirect('/admin/rooms');
    }

    /**
     * Display the specified resource.
     */
    public function show(Room $room)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Room $room)
    {
        //
        $archive_statuses = ArchiveStatus::pluck('name', 'id')->all();

        return view('admin.rooms.edit', compact('archive_statuses', 'room'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Room $room)
    {
        //
        $input = $request->all();
        $room->update($input);

        return redirect('/admin/rooms');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Room $room)
    {
        //
        $room->delete();

        return redirect('/admin/rooms');
    }
}
