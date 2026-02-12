@extends('layouts.error')

@section('title', 'Download Export')

@section('content')
    <div class="error-illustration">
        <svg viewBox="0 0 400 300" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Download icon illustration -->
            <circle cx="200" cy="150" r="120" fill="#FFE5D9" opacity="0.3"/>

            <!-- Cloud -->
            <ellipse cx="200" cy="120" rx="60" ry="40" fill="#0049AB" opacity="0.6"/>
            <circle cx="170" cy="120" r="30" fill="#0049AB" opacity="0.6"/>
            <circle cx="230" cy="120" r="30" fill="#0049AB" opacity="0.6"/>

            <!-- Download arrow -->
            <rect x="190" y="130" width="20" height="60" fill="#FF7300"/>
            <polygon points="200,200 170,170 230,170" fill="#FF7300"/>

            <!-- Document -->
            <rect x="160" y="210" width="80" height="60" rx="5" fill="white" stroke="#0049AB" stroke-width="3"/>
            <line x1="175" y1="225" x2="225" y2="225" stroke="#0049AB" stroke-width="2"/>
            <line x1="175" y1="240" x2="225" y2="240" stroke="#0049AB" stroke-width="2"/>
            <line x1="175" y1="255" x2="205" y2="255" stroke="#0049AB" stroke-width="2"/>

            <!-- Stars/sparkles -->
            <circle cx="100" cy="80" r="3" fill="#FFD700"/>
            <circle cx="280" cy="100" r="2" fill="#FFD700"/>
            <circle cx="320" cy="180" r="2.5" fill="#FFD700"/>
            <circle cx="80" cy="200" r="2" fill="#FFD700"/>
        </svg>
    </div>

    <h1 class="error-code" style="font-size: 48px;">
        <i class='bx bxs-download' style="color: #FF7300;"></i>
    </h1>
    <h2 class="error-title">Your Export is Ready!</h2>
    <p class="error-description">
        Your download should start automatically.<br>
        If it doesn't, click the button below.
    </p>

    <div style="background: #f8f9fa; border-radius: 12px; padding: 20px; margin: 30px 0; text-align: left;">
        <p style="margin-bottom: 10px; color: #555; font-size: 14px;">
            <strong style="color: #333;">Control No:</strong> {{ $export->control_no }}
        </p>
        <p style="margin-bottom: 10px; color: #555; font-size: 14px;">
            <strong style="color: #333;">File:</strong> {{ basename($export->file_name) }}
        </p>
        <p style="margin-bottom: 10px; color: #555; font-size: 14px;">
            <strong style="color: #333;">Status:</strong> {{ ucfirst($export->status) }}
        </p>
        <p style="margin-bottom: 0; color: #555; font-size: 14px;">
            <strong style="color: #333;">Completed:</strong> {{ optional($export->completed_at)->format('F d, Y \a\t h:i A') }}
        </p>
    </div>

    <div>
        <a href="{{ $downloadUrl }}" class="btn-home" id="download-btn">
            <i class='bx bxs-download' style="margin-right: 8px;"></i>
            Download Export
        </a>
        <a href="{{ route('home') }}" class="btn-secondary">
            <i class='bx bx-home-alt' style="margin-right: 8px;"></i>
            Go to Home
        </a>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const url = @json($downloadUrl);
            const timer = setTimeout(() => { window.location = url; }, 400);
            const btn = document.getElementById('download-btn');
            if (btn) {
                btn.addEventListener('click', () => clearTimeout(timer));
            }
        });
    </script>
@endsection
