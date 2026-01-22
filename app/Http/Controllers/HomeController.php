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
    public function index(HomeDataTable $dataTable)
    {
        $canApprove = false;
        $pendingCount = 0;

        // Check if user has approval permissions
        if (Auth::user()->can('approve document registration') ||
            Auth::user()->can('view pending document registrations') ||
            Auth::user()->can('view all document registrations')) {

            $canApprove = Auth::user()->can('approve document registration');

            // Count pending document registrations
            $pendingCount = DocumentRegistrationEntry::whereHas('status', function ($q) {
                $q->where('name', 'Pending');
            })->count();

            // Use Yajra DataTable render when allowed
            return $dataTable->render('home', compact('canApprove', 'pendingCount'));
        }

        return view('home', compact('canApprove', 'pendingCount'));
    }
}
