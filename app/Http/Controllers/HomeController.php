<?php

namespace App\Http\Controllers;

use App\DataTables\HomeDataTable;
use App\Models\DocumentRegistrationEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request, HomeDataTable $dataTable)
    {
        $canApprove = false;
        $pendingCount = 0;
        $noDcnCount = 0;
        $pendingTable = null;
        $noDcnTable = null;

        $canView = Auth::user()->can('approve document registration') ||
            Auth::user()->can('view pending document registrations') ||
            Auth::user()->can('view all document registrations');

        if (!$canView) {
            return view('home', compact('canApprove', 'pendingCount', 'noDcnCount'));
        }

        $canApprove = Auth::user()->can('approve document registration');

        if ($request->ajax()) {
            $mode = $request->input('mode') === 'no_dcn' ? 'no_dcn' : 'pending';
            return app(HomeDataTable::class)
                ->setMode($mode)
                ->setTableId($mode === 'no_dcn' ? 'noDcnRegistrationsTable' : 'pendingRegistrationsTable')
                ->ajax();
        }

        $pendingCount = DocumentRegistrationEntry::where('status_id', 1)->count();
        $noDcnCount = DocumentRegistrationEntry::whereNull('dcn_no')
            ->where('status_id', '!=', 3)
            ->whereHas('status', function($q) {
                $q->where('name', '!=', 'Cancelled');
            })
            ->count();

        $pendingTable = $dataTable
            ->setMode('pending')
            ->setTableId('pendingRegistrationsTable')
            ->html();

        $noDcnTable = app(HomeDataTable::class)
            ->setMode('no_dcn')
            ->setTableId('noDcnRegistrationsTable')
            ->html();

        return view('home', compact('canApprove', 'pendingCount', 'noDcnCount', 'pendingTable', 'noDcnTable'));
    }
}
