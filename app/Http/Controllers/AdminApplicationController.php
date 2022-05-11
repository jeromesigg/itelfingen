<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class AdminApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return view('admin.applications.index');
    }

    public function createDataTables()
    {
        $applications = Application::get();

        return DataTables::of($applications)
            ->addColumn('refuse', function (Application $application) {
                return $application['refuse'] ? 'Ja' : 'Nein';
            })
            ->addColumn('invoice_send', function (Application $application) {
                return $application['invoice_send'] ? 'Ja' : 'Nein';
            })
            ->addColumn('Actions', function(Application $application) {
                $buttons = '<form action="'.\URL::route('applications.update', $application).'" method="patch">' . csrf_field();
                if(!$application['invoice_send']){
                    $buttons .= '  <button type="submit" class="btn btn-secondary btn-sm">Ablehnen</button>';
                };
                $buttons .= '</form>';
                return $buttons;
            })
            ->editColumn('created_at', function (Application $application) {
                return [
                    'display' => Carbon::parse($application['created_at'])->format('d.m.Y'),
                    'sort' => Carbon::parse($application['created_at'])->diffInDays('01.01.2022')
                ];
            })
            ->rawColumns(['Actions'])
            ->make(true);
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
    public function edit()
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Application $application)
    {
        //
        return $application;
        $input = $request->all();
        $input['refuse'] = true;
        Application::whereId($id)->first()->update($input);
        return redirect('/admin/applications');
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

