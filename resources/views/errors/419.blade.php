@extends('layouts.error')

@section('title', '419 Page Expired')

@section('content')
    <div class="error-illustration">
        <svg viewBox="0 0 400 300" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Hourglass illustration -->
            <g transform="translate(200, 150)">
                <!-- Top -->
                <rect x="-40" y="-80" width="80" height="10" rx="5" fill="#0049AB"/>

                <!-- Glass body -->
                <path d="M -35 -70 L -15 -30 L -15 30 L -35 70 L 35 70 L 15 30 L 15 -30 L 35 -70 Z"
                      fill="#FF7300" opacity="0.3" stroke="#0049AB" stroke-width="3"/>

                <!-- Sand top -->
                <ellipse cx="0" cy="-50" rx="25" ry="8" fill="#FFD700"/>
                <path d="M -25 -50 L -10 -25 L 10 -25 L 25 -50" fill="#FFD700"/>

                <!-- Sand bottom -->
                <path d="M -10 25 L -25 50 L 25 50 L 10 25" fill="#FFD700"/>
                <ellipse cx="0" cy="50" rx="25" ry="8" fill="#FFD700"/>

                <!-- Falling sand -->
                <rect x="-2" y="-25" width="4" height="50" fill="#FFD700" opacity="0.6"/>

                <!-- Bottom -->
                <rect x="-40" y="70" width="80" height="10" rx="5" fill="#0049AB"/>
            </g>

            <!-- Clock arrows around -->
            <g transform="translate(120, 100)">
                <path d="M 0 -15 L 5 -10 L 0 -5" stroke="#FF7300" stroke-width="2" fill="none"/>
            </g>
            <g transform="translate(280, 200)">
                <path d="M 0 -15 L 5 -10 L 0 -5" stroke="#FF7300" stroke-width="2" fill="none"/>
            </g>
        </svg>
    </div>

    <h1 class="error-code">419</h1>
    <h2 class="error-title">Page Expired</h2>
    <p class="error-description">
        Your session has expired due to inactivity.
        Please refresh the page and try again.
    </p>

    <div>
        <a href="javascript:location.reload()" class="btn-home">
            <i class='bx bx-refresh' style="margin-right: 8px;"></i>
            Refresh Page
        </a>
        <a href="{{ url('/') }}" class="btn-secondary">
            <i class='bx bx-home-alt' style="margin-right: 8px;"></i>
            Back to Home
        </a>
    </div>
@endsection
