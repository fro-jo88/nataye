<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nataye Smart Education System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gray-50">
    <div class="min-h-screen flex items-center justify-center">
        <div class="max-w-4xl mx-auto px-4 py-12">
            <div class="text-center">
                <h1 class="text-5xl font-bold text-gray-900 mb-4">
                    Nataye Smart Education System
                </h1>
                <p class="text-xl text-gray-600 mb-8">
                    Modern School Management Platform
                </p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-12">
                    <div class="bg-white p-6 rounded-lg shadow-lg">
                        <h3 class="text-2xl font-semibold mb-2">For Students & Parents</h3>
                        <p class="text-gray-600 mb-4">Access grades, attendance, and stay connected</p>
                        <a href="/dashboard" class="inline-block bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                            Login
                        </a>
                    </div>
                    
                    <div class="bg-white p-6 rounded-lg shadow-lg">
                        <h3 class="text-2xl font-semibold mb-2">For Teachers & Staff</h3>
                        <p class="text-gray-600 mb-4">Manage classes, exams, and student records</p>
                        <a href="/dashboard" class="inline-block bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700">
                            Login
                        </a>
                    </div>
                </div>

                <div class="mt-12 text-sm text-gray-500">
                    <p>API Documentation: <a href="/api/documentation" class="text-blue-600 hover:underline">View Docs</a></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
