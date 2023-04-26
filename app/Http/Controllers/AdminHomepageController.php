<?php

namespace App\Http\Controllers;

use App\Models\Homepage;
use App\Models\Photo;
use Illuminate\Http\Request;

class AdminHomepageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $homepages = Homepage::all();
        if ($homepages->count() === 0) {
            $homepages = Homepage::create();
        }

        return view('admin.homepages.index', compact('homepages'));
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
        $homepage = Homepage::FindOrFail($id);
        if ($homepage->count() === 0) {
            $homepage = Homepage::create();
        }

        return view('admin.homepages.edit', compact('homepage'));
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

        if ($file = $request->file('main_photo_id')) {
            $name = 'hero-bg.webp';
            $file->move('images', $name);
            $photo = Photo::create(['file' => $name]);

            $input['main_photo_id'] = $photo->id;
        }
        if ($file = $request->file('background_top_photo_id')) {
            $name = 'about-bg.webp';
            $file->move('images', $name);
            $photo = Photo::create(['file' => $name]);

            $input['background_top_photo_id'] = $photo->id;
        }
        if ($file = $request->file('background_bottom_photo_id')) {
            $name = 'events-bg.webp';
            $file->move('images', $name);
            $photo = Photo::create(['file' => $name]);

            $input['background_bottom_photo_id'] = $photo->id;
        }
        if ($file = $request->file('big_login_photo_id')) {
            $name = 'login.webp';
            $file->move('images', $name);
            $photo = Photo::create(['file' => $name]);

            $input['big_login_photo_id'] = $photo->id;
        }
        if ($file = $request->file('small_login_photo_id')) {
            $name = 'logo.png';
            $file->move('images', $name);
            $photo = Photo::create(['file' => $name]);

            $input['small_login_photo_id'] = $photo->id;
        }
        Homepage::findOrFail($id)->update($input);

        return redirect('/admin/homepages');
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
    }
}
