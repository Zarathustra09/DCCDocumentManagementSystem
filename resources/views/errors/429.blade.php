@extends('layouts.error')

@section('title', '429 Too Many Requests')

@section('content')
    <div class="error-illustration">
        <svg viewBox="0 0 400 300" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Speedometer illustration -->
            <circle cx="200" cy="180" r="80" fill="none" stroke="#0049AB" stroke-width="8"/>
            <circle cx="200" cy="180" r="70" fill="none" stroke="#FF7300" stroke-width="4" opacity="0.3"/>

            <!-- Speed marks -->
            <line x1="140" y1="150" x2="150" y2="155" stroke="#0049AB" stroke-width="3"/>
            <line x1="130" y1="180" x2="140" y2="180" stroke="#0049AB" stroke-width="3"/>
            <line x1="140" y1="210" x2="150" y2="205" stroke="#0049AB" stroke-width="3"/>

            <line x1="260" y1="150" x2="250" y2="155" stroke="#0049AB" stroke-width="3"/>
            <line x1="270" y1="180" x2="260" y2="180" stroke="#0049AB" stroke-width="3"/>
            <line x1="260" y1="210" x2="250" y2="205" stroke="#0049AB" stroke-width="3"/>

            <line x1="180" y1="115" x2="185" y2="125" stroke="#0049AB" stroke-width="3"/>
            <line x1="220" y1="115" x2="215" y2="125" stroke="#0049AB" stroke-width="3"/>

            <!-- Needle (pointing to max) -->
            <line x1="200" y1="180" x2="245" y2="145" stroke="#FF7300" stroke-width="4" stroke-linecap="round"/>
            <circle cx="200" cy="180" r="8" fill="#FF7300"/>

            <!-- Warning indicator -->
            <circle cx="200" cy="180" r="25" fill="#FF7300" opacity="0.2"/>

            <!-- Speed lines -->
            <line x1="280" y1="120" x2="300" y2="110" stroke="#FFD700" stroke-width="3"/>
            <line x1="290" y1="140" x2="315" y2="135" stroke="#FFD700" stroke-width="3"/>
            <line x1="295" y1="160" x2="325" y2="160" stroke="#FFD700" stroke-width="3"/>
        </svg>
    </div>

    <h1 class="error-code">429</h1>
    <h2 class="error-title">Slow Down!</h2>
    <p class="error-description">
        You're making too many requests.
        Please wait a moment and try again.
    </p>

    <div>
        <a href="javascript:location.reload()" class="btn-home">
            <i class='bx bx-refresh' style="margin-right: 8px;"></i>
            Try Again
        </a>
        <a href="{{ url('/') }}" class="btn-secondary">
            <i class='bx bx-home-alt' style="margin-right: 8px;"></i>
            Back to Home
        </a>
    </div>
@endsection
