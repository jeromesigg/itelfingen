<?php

namespace App\Http\Controllers;

use App\Photo;
use App\History;
use App\ArchiveStatus;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class AdminHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $histories = History::orderBy('sort-index')->paginate(10);
        return view('admin.histories.index', compact('histories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('admin.histories.create');
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
        if($file = $request->file('photo_id')){
            $name = time() . str_replace(' ', '', $file->getClientOriginalName());
            $file->move('images', $name);
            $photo = Photo::create(['file'=>$name]); 
            $input['photo_id'] = $photo->id;
        }
        $input['shorttitle'] = Str::slug($input['title'],'_');
        $index = History::all()->count();
        $input['sort-index'] = $index + 1;
        $input['archive_status_id'] = config('status.aktiv');
        History::create($input);

        return redirect('/admin/histories');
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
        $archive_statuses = ArchiveStatus::pluck('name','id')->all();
        $history = History::findOrFail($id);
        return view('admin.histories.edit', compact('archive_statuses','history'));
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
        if($file = $request->file('photo_id')){
            $name = time() . str_replace(' ', '', $file->getClientOriginalName());
            $file->move('images', $name);
            $photo = Photo::create(['file'=>$name]);
            
            $input['photo_id'] = $photo->id;
        }
        $input['shorttitle'] = Str::slug($input['title'],'_');

        History::whereId($id)->first()->update($input);
        return redirect('/admin/histories');
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
        $history = History::findOrFail($id);

        $history->delete();

        return redirect('/admin/histories');
    }
}
