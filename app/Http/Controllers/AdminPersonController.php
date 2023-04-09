<?php

namespace App\Http\Controllers;

use App\Models\ArchiveStatus;
use App\Models\Person;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminPersonController extends Controller
{
    //
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $people = Person::orderBy('sort-index')->paginate(10);

        return view('admin.people.index', compact('people'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('admin.people.create');
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
        $input = $request->all();
        if ($file = $request->file('photo_id')) {
            $name = Str::uuid() . '_' . $input['name'].'.jpg';
            $file->move('images', $name);
            $photo = Photo::create(['file' => $name]);

            $input['photo_id'] = $photo->id;
        }
        $index = Person::all()->count();
        $input['sort-index'] = $index + 1;
        $input['archive_status_id'] = config('status.aktiv');
        Person::create($input);

        return redirect('/admin/people');
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
        $archive_statuses = ArchiveStatus::pluck('name', 'id')->all();
        $person = Person::findOrFail($id);

        return view('admin.people.edit', compact('archive_statuses', 'person'));
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
        $input = $request->all();
        if ($file = $request->file('photo_id')) {
            $name = Str::uuid() . '_' . $input['name'].'.jpg';
            $file->move('images', $name);
            $photo = Photo::create(['file' => $name]);

            $input['photo_id'] = $photo->id;
        }
        Person::whereId($id)->first()->update($input);

        return redirect('/admin/people');
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
        $person = Person::findOrFail($id);
        if ($person->photo) {
            unlink(public_path().$person->photo->file);
        }
        $person->delete();

        return redirect('/admin/people');
    }
}
