<?php

namespace App\Http\Controllers;
use App\Photo;
use App\Picture;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class AdminPicturesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $pictures = Picture::paginate(10);
        return view('admin.pictures.index', compact('pictures'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('admin.pictures.create');
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
            $name = time() . '_' .$file->getClientOriginalName();
            $file->move('images', $name);
            $photo = Photo::create(['file'=>$name]);
            $input['photo_id'] = $photo->id;

            if($input['cropped_photo_id']){ 
                $name = time() . '_cropped_' .$file->getClientOriginalName();
                Image::make($input['cropped_photo_id'])->save('images/'.$name);

                $photo_cropped = Photo::create(['file'=>$name]);
                $input['cropped_photo_id'] = $photo_cropped->id;
            }
        }

        Picture::create($input);

        return redirect('/admin/pictures/create');
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
        $picture = Picture::findOrFail($id);
        return view('admin.pictures.edit', compact('picture'));
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
            $name = time() . $file->getClientOriginalName();
            $file->move('images', $name);
            $photo = Photo::create(['file'=>$name]);
            
            $input['photo_id'] = $photo->id;

            if($input['cropped_photo_id']){ 
                $name = time() . '_cropped_' .$file->getClientOriginalName();
                Image::make($input['cropped_photo_id'])->save('images/'.$name);

                $photo_cropped = Photo::create(['file'=>$name]);
                $input['cropped_photo_id'] = $photo_cropped->id;
            }
        }
        Picture::whereId($id)->first()->update($input);
        return redirect('/admin/pictures');
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
        $picture = Picture::findOrFail($id);
        unlink(public_path() . $picture->photo->file);

        $picture->delete();

        return redirect('/admin/pictures');
    }
}
