<?php

namespace App\Http\Controllers;

use App\Enums\ArchiveState;
use App\Helper\ImageUpload;
use App\Models\ArchiveStatus;
use App\Models\FaqChapter;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;

class AdminFaqChapterController extends Controller
{
    public function index()
    {
        //
        $faq_chapters = FaqChapter::orderBy('sort-index')->get();

        return view('admin.faq_chapters.index', compact('faq_chapters'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
        return view('admin.faq_chapters.create');
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
        $index = FaqChapter::all()->count();
        $input['sort-index'] = $index + 1;
        $input['archive_status_id'] = ArchiveState::Aktiv;
        if (! empty($input['new_photo_id'])) {
            $name = time().'_faq_chapter.png';
            File::ensureDirectoryExists(public_path('images'));
            ImageUpload::saveDataUri($input['new_photo_id'], public_path('images/'.$name), 'new_photo_id');

            $photo = Photo::create(['file' => $name]);
            $input['photo_id'] = $photo->id;
        }
        FaqChapter::create($input);

        return redirect('/admin/faq_chapters');
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
        $faqchapter = FaqChapter::findOrFail($id);

        return view('admin.faq_chapters.edit', compact('archive_statuses', 'faqchapter'));
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
        if (! empty($input['new_photo_id'])) {
            $name = time().'_faq_chapter.png';
            File::ensureDirectoryExists(public_path('images'));
            ImageUpload::saveDataUri($input['new_photo_id'], public_path('images/'.$name), 'new_photo_id');

            $photo = Photo::create(['file' => $name]);
            $input['photo_id'] = $photo->id;
        }
        FaqChapter::whereId($id)->first()->update($input);

        return redirect('/admin/faq_chapters');
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
        $faqchapter = FaqChapter::findOrFail($id);

        $faqchapter->delete();

        return redirect('/admin/faq_chapters');
    }
}
