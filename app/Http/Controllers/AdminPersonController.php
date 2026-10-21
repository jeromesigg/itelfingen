<?php

namespace App\Http\Controllers;

use App\Enums\ArchiveState;
use App\Helper\ImageUpload;
use App\Models\ArchiveStatus;
use App\Models\Person;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;

class AdminPersonController extends Controller
{
    //
    /**
     * Display a listing of the resource.
     *
     * @return Response
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
     * @return Response
     */
    public function create()
    {
        //
        return view('admin.people.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        //
        $input = $request->all();
        if ($file = $request->file('photo_id')) {
            $name = ImageUpload::store($file);
            $photo = Photo::create(['file' => $name]);

            $input['photo_id'] = $photo->id;
        }
        $index = Person::all()->count();
        $input['sort-index'] = $index + 1;
        $input['archive_status_id'] = ArchiveState::Aktiv;
        Person::create($input);

        return redirect('/admin/people');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
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
        $archive_statuses = ArchiveStatus::pluck('name', 'id')->all();
        $person = Person::findOrFail($id);

        return view('admin.people.edit', compact('archive_statuses', 'person'));
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
        $input = $request->all();
        if ($file = $request->file('photo_id')) {
            $name = ImageUpload::store($file);
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
     * @return Response
     */
    public function destroy($id)
    {
        //
        $person = Person::findOrFail($id);
        // Fehlt die Datei schon, trotzdem löschen (unlink brach sonst mit einem Fehler ab)
        if ($person->photo) {
            File::delete(public_path().$person->photo->file);
        }
        $person->delete();

        return redirect('/admin/people');
    }
}
