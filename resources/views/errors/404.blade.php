@extends('layouts.error')

@section('title', '404 Not Found')

@section('content')
    <div class="error-illustration">
        <svg viewBox="0 0 400 300" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Lost astronaut illustration -->
            <circle cx="200" cy="150" r="120" fill="#FFE5D9" opacity="0.3"/>

            <!-- Helmet -->
            <circle cx="200" cy="120" r="50" fill="#FF7300"/>
            <circle cx="200" cy="120" r="40" fill="#0049AB" opacity="0.3"/>
            <ellipse cx="200" cy="120" rx="35" ry="25" fill="white" opacity="0.9"/>

            <!-- Body -->
            <rect x="170" y="160" width="60" height="80" rx="10" fill="#FF7300"/>
            <rect x="180" y="170" width="40" height="20" fill="white" opacity="0.3"/>

            <!-- Arms -->
            <rect x="140" y="170" width="30" height="15" rx="7" fill="#FF7300"/>
            <rect x="230" y="170" width="30" height="15" rx="7" fill="#FF7300"/>

            <!-- Legs -->
            <rect x="175" y="235" width="20" height="40" rx="10" fill="#FF7300"/>
            <rect x="205" y="235" width="20" height="40" rx="10" fill="#FF7300"/>

            <!-- Stars -->
            <circle cx="100" cy="50" r="3" fill="#FFD700"/>
            <circle cx="280" cy="80" r="2" fill="#FFD700"/>
            <circle cx="320" cy="150" r="2.5" fill="#FFD700"/>
            <circle cx="80" cy="200" r="2" fill="#FFD700"/>

            <!-- Planet -->
            <circle cx="320" cy="220" r="25" fill="#0049AB" opacity="0.6"/>
            <ellipse cx="320" cy="220" rx="40" ry="5" fill="#0049AB" opacity="0.3"/>

            <!-- Satellite -->
            <rect x="70" y="90" width="20" height="8" fill="#A0AEC0"/>
            <rect x="60" y="92" width="8" height="4" fill="#A0AEC0"/>
            <rect x="90" y="92" width="8" height="4" fill="#A0AEC0"/>
        </svg>
    </div>

    <h1 class="error-code">404</h1>
    <h2 class="error-title">Houston, We Have a Problem!</h2>
    <p class="error-description">
        The page you're looking for has drifted into deep space.
        Let's get you back to familiar territory.
    </p>

    <div>
        <a href="{{ url('/') }}" class="btn-home">
            <i class='bx bx-home-alt' style="margin-right: 8px;"></i>
            Back to Home
        </a>
        <a href="javascript:history.back()" class="btn-secondary">
            <i class='bx bx-arrow-back' style="margin-right: 8px;"></i>
            Go Back
        </a>
    </div>
@endsection
