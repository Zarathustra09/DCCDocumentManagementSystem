@extends('layouts.error')

@section('title', '403 Forbidden')

@section('content')
    <div class="error-illustration">
        <svg viewBox="0 0 400 300" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Lock and shield illustration -->
            <circle cx="200" cy="150" r="100" fill="#FFE5D9" opacity="0.2"/>

            <!-- Shield -->
            <path d="M200 80 L260 100 L260 160 Q260 200 200 230 Q140 200 140 160 L140 100 Z" fill="#0049AB" opacity="0.8"/>
            <path d="M200 90 L250 105 L250 155 Q250 190 200 215 Q150 190 150 155 L150 105 Z" fill="#FF7300"/>

            <!-- Lock -->
            <rect x="175" y="140" width="50" height="60" rx="8" fill="#0049AB"/>
            <circle cx="200" cy="125" r="20" stroke="#0049AB" stroke-width="8" fill="none"/>
            <rect x="196" y="115" width="8" height="15" fill="#0049AB"/>

            <!-- Keyhole -->
            <circle cx="200" cy="165" r="8" fill="white"/>
            <rect x="196" y="165" width="8" height="20" fill="white"/>

            <!-- Decorative elements -->
            <circle cx="120" cy="100" r="4" fill="#FF7300" opacity="0.5"/>
            <circle cx="280" cy="120" r="5" fill="#0049AB" opacity="0.5"/>
            <circle cx="100" cy="180" r="3" fill="#FF7300" opacity="0.5"/>
            <circle cx="300" cy="190" r="4" fill="#0049AB" opacity="0.5"/>
        </svg>
    </div>

    <h1 class="error-code">403</h1>
    <h2 class="error-title">Access Denied</h2>
    <p class="error-description">
        You don't have permission to access this resource.
        If you believe this is a mistake, please contact dcc@smartprobegroup.com.
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
