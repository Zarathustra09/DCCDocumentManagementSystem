@extends('layouts.error')

@section('title', '500 Internal Server Error')

@section('content')
    <div class="error-illustration">
        <svg viewBox="0 0 400 300" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Broken server/computer illustration -->
            <!-- Monitor -->
            <rect x="120" y="100" width="160" height="120" rx="8" fill="#0049AB"/>
            <rect x="130" y="110" width="140" height="90" fill="#1e3a8a"/>

            <!-- Error screen -->
            <text x="200" y="145" font-size="24" fill="#FF7300" text-anchor="middle" font-weight="bold">ERROR</text>
            <line x1="150" y1="160" x2="250" y2="160" stroke="#FF7300" stroke-width="2"/>
            <line x1="150" y1="170" x2="230" y2="170" stroke="#FF7300" stroke-width="1"/>
            <line x1="150" y1="178" x2="240" y2="178" stroke="#FF7300" stroke-width="1"/>

            <!-- Monitor stand -->
            <rect x="185" y="220" width="30" height="20" fill="#0049AB"/>
            <rect x="160" y="240" width="80" height="8" rx="4" fill="#0049AB"/>

            <!-- Sparks/Glitches -->
            <circle cx="100" cy="120" r="3" fill="#FFD700"/>
            <circle cx="110" cy="110" r="2" fill="#FFD700"/>
            <circle cx="290" cy="130" r="3" fill="#FFD700"/>
            <circle cx="300" cy="120" r="2" fill="#FFD700"/>
            <line x1="95" y1="125" x2="88" y2="132" stroke="#FFD700" stroke-width="2"/>
            <line x1="295" y1="135" x2="302" y2="142" stroke="#FFD700" stroke-width="2"/>

            <!-- Warning signs -->
            <g transform="translate(80, 180)">
                <polygon points="15,5 25,25 5,25" fill="#FF7300"/>
                <text x="15" y="22" font-size="16" fill="white" text-anchor="middle" font-weight="bold">!</text>
            </g>

            <g transform="translate(300, 170)">
                <polygon points="15,5 25,25 5,25" fill="#FF7300"/>
                <text x="15" y="22" font-size="16" fill="white" text-anchor="middle" font-weight="bold">!</text>
            </g>
        </svg>
    </div>

    <h1 class="error-code">500</h1>
    <h2 class="error-title">Something Went Wrong</h2>
    <p class="error-description">
        We encountered an unexpected error. Our team has been notified
        and we're working to fix it. Please try again later.
    </p>

    <div>
        <a href="{{ url('/') }}" class="btn-home">
            <i class='bx bx-home-alt' style="margin-right: 8px;"></i>
            Back to Home
        </a>
        <a href="javascript:location.reload()" class="btn-secondary">
            <i class='bx bx-refresh' style="margin-right: 8px;"></i>
            Refresh Page
        </a>
    </div>
@endsection
