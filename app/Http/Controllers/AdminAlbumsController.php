<?php

namespace App\Http\Controllers;

use App\Album;
use Illuminate\Http\Request;

class AdminAlbumsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $albums = Album::paginate(10);
        return view('admin.albums.index', compact('albums'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
        if($request->has('default_album')){
            $input['default_album'] = true;
        }else{
            $input['default_album'] = false;
        }
        $internal_name = str_replace(' ', '', $input['name']);
        $internal_name = str_replace('(', '', $internal_name);
        $internal_name = str_replace(')', '', $internal_name);
        $input['internal_name'] = $internal_name;
        Album::create($input);
        return redirect('admin/albums');
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
        $album = Album::findOrFail($id);
        return view('admin.albums.edit', compact('album'));
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
        if($request->has('default_album')){
            $input['default_album'] = true;
        }else{
            $input['default_album'] = false;
        }
        $internal_name = str_replace(' ', '', $input['name']);
        $internal_name = str_replace('(', '', $internal_name);
        $internal_name = str_replace(')', '', $internal_name);
        $input['internal_name'] = $internal_name;
        Album::findOrFail($id)->update($input);

        return redirect('/admin/albums');
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
        Album::findOrFail($id)->delete();
        return redirect('/admin/albums');
    }
}
