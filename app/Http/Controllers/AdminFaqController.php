<?php

namespace App\Http\Controllers;

use App\Enums\ArchiveState;
use App\Helper\ImageUpload;
use App\Models\ArchiveStatus;
use App\Models\Faq;
use App\Models\FaqChapter;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AdminFaqController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
        $faq_chapters = FaqChapter::pluck('name', 'id')->all();

        return view('admin.faqs.index', compact('faq_chapters'));
    }

    public function createDataTablesTanStack(Request $request)
    {

        $faqs = Faq::get();

        // Transformation der Daten (gleich wie Yajra, aber manuel)
        $data = $faqs->map(function (Faq $faq) {
            return [
                // HTML-Spalten (meta.html): Werte escapen
                'image' => $faq->photo ? '<img height="50" src="'.e($faq->photo['file']).'" alt="'.e($faq->name).'" class="img-fluid">' : '',
                'name' => '<a class="text-orientalpink" href="'.route('faqs.edit', $faq->id).'">'.e($faq->name).'</a>',
                'description' => substr($faq->description, 0, 500),
                'chapter' => $faq->faq_chapter?->name ?? '',
                'archive_status' => $faq->archive_status?->name ?? '',
            ];
        });

        // TanStack-freundliches Response-Format
        return response()->json([
            'data' => $data,
            'rowCount' => count($data),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
        return view('admin.faqs.create');
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
        $index = Faq::all()->count();
        $input['sort-index'] = $index + 1;
        $input['archive_status_id'] = ArchiveState::Aktiv;
        Faq::create($input);

        return redirect('/admin/faqs');
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
        $faq_chapters = FaqChapter::pluck('name', 'id')->all();
        $faq = Faq::findOrFail($id);

        return view('admin.faqs.edit', compact('archive_statuses', 'faq', 'faq_chapters'));
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

        Faq::whereId($id)->first()->update($input);

        return redirect('/admin/faqs');
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
        $faq = Faq::findOrFail($id);

        $faq->delete();

        return redirect('/admin/faqs');
    }
}
