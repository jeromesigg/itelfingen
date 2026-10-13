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
use Yajra\DataTables\Facades\DataTables;

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

    public function createDataTables()
    {
        $faqs = Faq::get();

        return DataTables::of($faqs)
            ->addColumn('image', function (Faq $faq) {
                $url = $faq->photo ? $faq->photo['file'] : 'https://via.placeholder.com/50';

                //               return $url;
                return '<img height="50" src="'.$url.'" alt="" class="img-fluid">';
            })
            ->addColumn('name', function (Faq $faq) {
                return '<a class="text-orientalpink" href="'.route('faqs.edit', $faq->id).'">'.$faq->name.'</a>';
            })
            ->addColumn('description', function (Faq $faq) {
                return substr($faq->description, 0, 500);
            })

            ->addColumn('chapter', function (Faq $faq) {
                return $faq->faq_chapter->name;
            })
            ->addColumn('archive_status', function (Faq $faq) {
                return $faq->archive_status->name;
            })
            ->rawColumns(['image', 'name'])
            ->make(true);
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
