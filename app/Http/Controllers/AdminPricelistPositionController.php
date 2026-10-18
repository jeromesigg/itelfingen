<?php

namespace App\Http\Controllers;

use App\Enums\ArchiveState;
use App\Models\ArchiveStatus;
use App\Models\PricelistPosition;
use App\Services\BexioApiService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AdminPricelistPositionController extends Controller
{
    public function index()
    {
        //
        $positions = PricelistPosition::orderby('bexio_code')->get();

        return view('admin.positions.index', compact('positions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
        $positions = app(BexioApiService::class)->postAction('article/');

        foreach ($positions as $position) {
            $position_db = PricelistPosition::where('bexio_id', $position['id']);
            if ($position_db->count() > 0) {
                $position_db->update([
                    'price' => $position['sale_price'], ]);
            } else {
                PricelistPosition::create([
                    'name' => $position['deliverer_name'],
                    'bexio_id' => $position['id'],
                    'bexio_code' => $position['intern_code'],
                    'price' => $position['sale_price'],
                    'archive_status_id' => ArchiveState::Aktiv,
                    'show' => true, ]);
            }
        }

        return redirect()->back();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        //
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
        $position = PricelistPosition::findOrFail($id);

        return view('admin.positions.edit', compact('archive_statuses', 'position'));
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
        $input['show'] = $request->has('show');
        PricelistPosition::whereId($id)->first()->update($input);

        return redirect('/admin/positions');
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
        $position = PricelistPosition::findOrFail($id);

        $position->delete();

        return redirect('/admin/positions');
    }
}
