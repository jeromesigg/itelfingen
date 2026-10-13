<?php

namespace App\Http\Controllers;

use App\Enums\ArchiveState;
use App\Models\ArchiveStatus;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AdminTestimonialController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
        $testimonials = Testimonial::orderBy('sort-index')->paginate(10);

        return view('admin.testimonials.index', compact('testimonials'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
        return view('admin.testimonials.create');
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
        $index = Testimonial::all()->count();
        $input['sort-index'] = $index + 1;
        $input['archive_status_id'] = ArchiveState::Aktiv;
        Testimonial::create($input);

        return redirect('/admin/testimonials');
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
        $testimonial = Testimonial::findOrFail($id);

        return view('admin.testimonials.edit', compact('archive_statuses', 'testimonial'));
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
        Testimonial::whereId($id)->first()->update($input);

        return redirect('/admin/testimonials');
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
        $testimonial = Testimonial::findOrFail($id);

        $testimonial->delete();

        return redirect('/admin/testimonials');
    }
}
