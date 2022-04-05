<?php

namespace App\Http\Controllers;

use App\Models\ArchiveStatus;
use App\Models\FaqChapter;
use Illuminate\Http\Request;

class AdminFaqChapterController extends Controller
{
    public function index()
    {
        //
        $faq_chapters = FaqChapter::orderBy('sort-index')->paginate(10);
        return view('admin.faq_chapters.index', compact('faq_chapters'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('admin.faq_chapters.create');
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
        $index = FaqChapter::all()->count();
        $input['sort-index'] = $index + 1;
        $input['archive_status_id'] = config('status.aktiv');
        FaqChapter::create($input);

        return redirect('/admin/faq_chapters');
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
        $faqchapter = FaqChapter::findOrFail($id);
        return view('admin.faq_chapters.edit', compact('archive_statuses','faqchapter'));
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
        FaqChapter::whereId($id)->first()->update($input);
        return redirect('/admin/faq_chapters');
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
        $faqchapter = FaqChapter::findOrFail($id);

        $faqchapter->delete();

        return redirect('/admin/faq_chapters');
    }
}
