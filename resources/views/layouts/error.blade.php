<!DOCTYPE html>
<html
    lang="en"
    class="light-style layout-menu-fixed"
    dir="ltr"
    data-theme="theme-default"
    data-assets-path="../assets/"
    data-template="vertical-menu-template-free"
>
<head>
    <meta charset="utf-8" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
    />

    <title>{{ config('app.name', 'Laravel') }}</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" href="{{asset('schoollogo.png')}}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet"
    />

    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="{{ asset('dashboard/assets/vendor/fonts/boxicons.css') }}" />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>
    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('dashboard/assets/vendor/css/core.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('dashboard/assets/vendor/css/theme-default.css') }}" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('dashboard/assets/css/demo.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('dashboard/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />

    <link rel="stylesheet" href="{{ asset('dashboard/assets/vendor/libs/apex-charts/apex-charts.css') }}" />

    <!-- Page CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/driver.js@latest/dist/driver.js.iife.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/driver.js@latest/dist/driver.css"/>
    <!-- Helpers -->
    <script src="{{ asset('dashboard/assets/vendor/js/helpers.js') }}"></script>

    <!-- Template customizer & Theme config files -->
    <script src="{{ asset('dashboard/assets/js/config.js') }}"></script>
    @stack('styles')
    <!DOCTYPE html>
    <html lang="en" class="light-style" dir="ltr" data-theme="theme-default">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
        <title>@yield('title', 'Error') - {{ config('app.name') }}</title>
        <meta name="description" content="" />

        <!-- Favicon -->
        <link rel="icon" type="image/x-icon" href="{{ asset('dashboard/assets/img/favicon/favicon.ico') }}" />

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />

        <!-- Icons -->
        <link rel="stylesheet" href="{{ asset('dashboard/assets/vendor/fonts/boxicons.css') }}" />

        <!-- Core CSS -->
        <link rel="stylesheet" href="{{ asset('dashboard/assets/vendor/css/core.css') }}" />
        <link rel="stylesheet" href="{{ asset('dashboard/assets/vendor/css/theme-default.css') }}" />
        <link rel="stylesheet" href="{{ asset('dashboard/assets/css/demo.css') }}" />

        <style>
            body {
                margin: 0;
                padding: 0;
                overflow-x: hidden;
            }

            .error-container {
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                background: linear-gradient(135deg, #FF7300 0%, #0049AB 100%);
                position: relative;
                overflow: hidden;
            }

            .error-container::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
                opacity: 0.4;
            }

            .error-content {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                border-radius: 24px;
                padding: 60px 40px;
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
                text-align: center;
                max-width: 600px;
                margin: 20px;
                position: relative;
                z-index: 1;
                animation: slideUp 0.6s ease-out;
            }

            @keyframes slideUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .error-code {
                font-size: 120px;
                font-weight: 700;
                background: linear-gradient(135deg, #FF7300 0%, #0049AB 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
                line-height: 1;
                margin-bottom: 20px;
                text-shadow: 0 4px 20px rgba(255, 115, 0, 0.3);
            }

            .error-title {
                font-size: 32px;
                font-weight: 600;
                color: #2d3748;
                margin-bottom: 16px;
            }

            .error-description {
                font-size: 18px;
                color: #718096;
                margin-bottom: 32px;
                line-height: 1.6;
            }

            .error-illustration {
                margin-bottom: 32px;
                animation: float 3s ease-in-out infinite;
            }

            @keyframes float {
                0%, 100% {
                    transform: translateY(0px);
                }
                50% {
                    transform: translateY(-20px);
                }
            }

            .error-illustration svg {
                max-width: 280px;
                height: auto;
            }

            .btn-home {
                display: inline-block;
                padding: 14px 32px;
                background: linear-gradient(135deg, #FF7300 0%, #0049AB 100%);
                color: white;
                text-decoration: none;
                border-radius: 50px;
                font-weight: 600;
                font-size: 16px;
                transition: all 0.3s ease;
                box-shadow: 0 4px 15px rgba(255, 115, 0, 0.4);
            }

            .btn-home:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(255, 115, 0, 0.6);
                color: white;
            }

            .btn-secondary {
                display: inline-block;
                padding: 14px 32px;
                background: transparent;
                color: #FF7300;
                text-decoration: none;
                border-radius: 50px;
                font-weight: 600;
                font-size: 16px;
                border: 2px solid #FF7300;
                margin-left: 12px;
                transition: all 0.3s ease;
            }

            .btn-secondary:hover {
                background: #FF7300;
                color: white;
                transform: translateY(-2px);
            }

            .floating-shapes {
                position: absolute;
                width: 100%;
                height: 100%;
                overflow: hidden;
                z-index: 0;
            }

            .shape {
                position: absolute;
                opacity: 0.1;
                animation: float-shape 20s infinite ease-in-out;
            }

            .shape:nth-child(1) {
                top: 10%;
                left: 10%;
                animation-delay: 0s;
            }

            .shape:nth-child(2) {
                top: 60%;
                right: 10%;
                animation-delay: 4s;
            }

            .shape:nth-child(3) {
                bottom: 20%;
                left: 20%;
                animation-delay: 8s;
            }

            @keyframes float-shape {
                0%, 100% {
                    transform: translate(0, 0) rotate(0deg);
                }
                33% {
                    transform: translate(30px, -30px) rotate(120deg);
                }
                66% {
                    transform: translate(-20px, 20px) rotate(240deg);
                }
            }
        </style>
</head>

<body>

<div class="error-container">
    <div class="floating-shapes">
        <div class="shape">
            <svg width="100" height="100" viewBox="0 0 100 100" fill="white">
                <circle cx="50" cy="50" r="40"/>
            </svg>
        </div>
        <div class="shape">
            <svg width="80" height="80" viewBox="0 0 100 100" fill="white">
                <rect x="20" y="20" width="60" height="60" transform="rotate(45 50 50)"/>
            </svg>
        </div>
        <div class="shape">
            <svg width="120" height="120" viewBox="0 0 100 100" fill="white">
                <polygon points="50,10 90,90 10,90"/>
            </svg>
        </div>
    </div>

    <div class="error-content">
        @yield('content')
    </div>
</div>
<!-- Core JS -->
<!-- build:js assets/vendor/js/core.js -->
<script src="{{ asset('dashboard/assets/vendor/libs/jquery/jquery.js') }}"></script>
<script src="{{ asset('dashboard/assets/vendor/libs/popper/popper.js') }}"></script>
<script src="{{ asset('dashboard/assets/vendor/js/bootstrap.js') }}"></script>
<script src="{{ asset('dashboard/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>

<script src="{{ asset('dashboard/assets/vendor/js/menu.js') }}"></script>
<!-- endbuild -->

<!-- Vendors JS -->
<script src="{{ asset('dashboard/assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>

<!-- Main JS -->
<script src="{{ asset('dashboard/assets/js/main.js') }}"></script>

<!-- Page JS -->
<script src="{{ asset('dashboard/assets/js/dashboards-analytics.js') }}"></script>

<!-- Place this tag in your head or just before your close body tag. -->
<script async defer src="https://buttons.github.io/buttons.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@stack('scripts')
@stack('driverjs')
</body>
</html>
