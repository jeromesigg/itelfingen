<?php

namespace App\Http\Controllers;

use App\Models\ArchiveStatus;
use App\Models\Pricelist;
use Illuminate\Http\Request;

class AdminPricelistController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $pricelists = Pricelist::orderBy('sort-index')->paginate(10);

        return view('admin.pricelists.index', compact('pricelists'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('admin.pricelists.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $input = $request->all();
        $index = Pricelist::all()->count();
        $input['sort-index'] = $index + 1;
        $input['archive_status_id'] = config('status.aktiv');
        Pricelist::create($input);

        return redirect('/admin/pricelists');
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
        $archive_statuses = ArchiveStatus::pluck('name', 'id')->all();
        $pricelist = Pricelist::findOrFail($id);

        return view('admin.pricelists.edit', compact('archive_statuses', 'pricelist'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $input = $request->all();
        Pricelist::whereId($id)->first()->update($input);

        return redirect('/admin/pricelists');
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
        $pricelist = Pricelist::findOrFail($id);

        $pricelist->delete();

        return redirect('/admin/pricelists');
    }
}
