<?php

namespace App\Http\Controllers;

use App\Models\ArchiveStatus;
use App\Models\PricelistPosition;
use Illuminate\Http\Request;
use Ixudra\Curl\Facades\Curl;

class AdminPricelistPositionController extends Controller
{
    public function index()
    {
        //
        $positions = PricelistPosition::orderby('bexio_code')->paginate(10);

        return view('admin.positions.index', compact('positions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $positions = Curl::to('https://api.bexio.com/2.0/article')
        ->withHeader('Accept: application/json')
        ->withBearer(config('app.bexio_token'))
        ->withContentType('application/json')
        ->asJson(true)
        ->get();

        foreach ($positions as $position) {
            $position_db = PricelistPosition::where('bexio_id', $position['id']);
            if ($position_db->count() > 0) {
                $position_db->update([
                    'name' => $position['deliverer_name'],
                    'price' => $position['sale_price'], ]);
            } else {
                PricelistPosition::create([
                    'name' => $position['deliverer_name'],
                    'bexio_id' => $position['id'],
                    'bexio_code' => $position['intern_code'],
                    'price' => $position['sale_price'],
                    'archive_status_id' => config('status.aktiv'),
                    'show' => true, ]);
            }
        }

        return redirect()->back();
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
        $archive_statuses = ArchiveStatus::pluck('name', 'id')->all();
        $position = PricelistPosition::findOrFail($id);

        return view('admin.positions.edit', compact('archive_statuses', 'position'));
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
        if ($request['show'] === null) {
            $request['show'] = 0;
        }
        $input = $request->all();
        PricelistPosition::whereId($id)->first()->update($input);

        return redirect('/admin/positions');
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
        $position = PricelistPosition::findOrFail($id);

        $position->delete();

        return redirect('/admin/positions');
    }
}
