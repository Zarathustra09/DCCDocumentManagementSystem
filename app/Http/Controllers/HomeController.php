<?php

namespace App\Http\Controllers;

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
    public function index()
    {
        $pendingRegistrations = collect();
        $canApprove = false;

        // Check if user has approval permissions
        if (Auth::user()->can('approve document registration') ||
            Auth::user()->can('view pending document registrations') ||
            Auth::user()->can('view all document registrations')) {

            $canApprove = Auth::user()->can('approve document registration');

            // Get pending document registrations
            $pendingRegistrations = DocumentRegistrationEntry::with(['submittedBy'])
                ->where('status', 'pending')
                ->latest('submitted_at')
                ->get();
        }

        return view('home', compact('pendingRegistrations', 'canApprove'));
    }
}
