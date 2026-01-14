@extends('layouts.error')

@section('title', '503 Service Unavailable')

@section('content')
    <div class="error-illustration">
        <svg viewBox="0 0 400 300" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Robot under maintenance -->
            <!-- Head -->
            <rect x="150" y="80" width="100" height="80" rx="10" fill="#FF7300"/>
            <circle cx="180" cy="110" r="12" fill="#FFD700"/>
            <circle cx="220" cy="110" r="12" fill="#FFD700"/>
            <rect x="175" y="140" width="50" height="8" rx="4" fill="white" opacity="0.5"/>

            <!-- Antenna -->
            <line x1="200" y1="80" x2="200" y2="50" stroke="#0049AB" stroke-width="4"/>
            <circle cx="200" cy="45" r="8" fill="#0049AB"/>

            <!-- Body -->
            <rect x="140" y="165" width="120" height="100" rx="15" fill="#FF7300"/>
            <circle cx="170" cy="200" r="8" fill="white" opacity="0.3"/>
            <circle cx="200" cy="200" r="8" fill="white" opacity="0.3"/>
            <circle cx="230" cy="200" r="8" fill="white" opacity="0.3"/>
            <rect x="160" y="225" width="80" height="4" fill="#FFD700"/>
            <rect x="165" y="235" width="70" height="4" fill="#FFD700"/>

            <!-- Arms -->
            <rect x="100" y="180" width="40" height="15" rx="7" fill="#0049AB"/>
            <circle cx="95" cy="187" r="12" fill="#A0AEC0"/>
            <rect x="260" y="180" width="40" height="15" rx="7" fill="#0049AB"/>
            <circle cx="305" cy="187" r="12" fill="#A0AEC0"/>

            <!-- Tools -->
            <g transform="translate(280, 100)">
                <rect x="0" y="0" width="8" height="40" fill="#FFD700"/>
                <circle cx="4" cy="45" r="6" fill="#FFD700"/>
            </g>

            <g transform="translate(110, 110)">
                <path d="M 0 10 L 10 0 L 20 10 L 10 20 Z" fill="#0049AB"/>
            </g>

            <!-- Gears -->
            <circle cx="320" cy="220" r="15" fill="#A0AEC0" opacity="0.4"/>
            <circle cx="340" cy="240" r="20" fill="#A0AEC0" opacity="0.3"/>
            <circle cx="80" cy="240" r="18" fill="#A0AEC0" opacity="0.4"/>
        </svg>
    </div>

    <h1 class="error-code">503</h1>
    <h2 class="error-title">We're Upgrading!</h2>
    <p class="error-description">
        We'll be back online shortly. Thanks for your patience!
    </p>

    <div>
        <a href="{{ url('/') }}" class="btn-home">
            <i class='bx bx-refresh' style="margin-right: 8px;"></i>
            Try Again
        </a>
    </div>
@endsection
