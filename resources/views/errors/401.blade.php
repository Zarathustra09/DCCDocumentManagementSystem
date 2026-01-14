@extends('layouts.error')

@section('title', '401 Unauthorized')

@section('content')
    <div class="error-illustration">
        <svg viewBox="0 0 400 300" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Key and door illustration -->
            <!-- Door -->
            <rect x="140" y="80" width="120" height="160" rx="8" fill="#0049AB"/>
            <rect x="150" y="90" width="100" height="140" fill="#1e3a8a"/>

            <!-- Door handle -->
            <circle cx="230" cy="160" r="8" fill="#FF7300"/>
            <rect x="225" y="160" width="12" height="4" rx="2" fill="#FF7300"/>

            <!-- Door panels -->
            <rect x="160" y="100" width="80" height="50" rx="4" fill="#0049AB" opacity="0.3"/>
            <rect x="160" y="160" width="80" height="50" rx="4" fill="#0049AB" opacity="0.3"/>

            <!-- Key -->
            <g transform="translate(280, 140) rotate(45 0 0)">
                <circle r="15" fill="#FFD700"/>
                <circle r="8" fill="none" stroke="#FF7300" stroke-width="2"/>
                <rect x="12" y="-3" width="35" height="6" fill="#FFD700"/>
                <rect x="40" y="-6" width="4" height="4" fill="#FFD700"/>
                <rect x="40" y="2" width="4" height="4" fill="#FFD700"/>
            </g>

            <!-- Question mark -->
            <text x="200" y="180" font-size="48" fill="white" text-anchor="middle" font-weight="bold">?</text>

            <!-- Decorative dots -->
            <circle cx="100" cy="120" r="3" fill="#FF7300" opacity="0.5"/>
            <circle cx="110" cy="180" r="4" fill="#FFD700" opacity="0.5"/>
            <circle cx="300" cy="100" r="3" fill="#FF7300" opacity="0.5"/>
        </svg>
    </div>

    <h1 class="error-code">401</h1>
    <h2 class="error-title">Authentication Required</h2>
    <p class="error-description">
        You need to be logged in to access this page.
        Please sign in to continue.
    </p>

    <div>
        <a href="{{ route('login') }}" class="btn-home">
            <i class='bx bx-log-in' style="margin-right: 8px;"></i>
            Login
        </a>
        <a href="{{ url('/') }}" class="btn-secondary">
            <i class='bx bx-home-alt' style="margin-right: 8px;"></i>
            Back to Home
        </a>
    </div>
@endsection
