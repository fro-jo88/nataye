<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - Nataye NSES</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gray-50">
    <div class="min-h-screen">
        <nav class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                            <h1 class="text-xl font-bold">Nataye NSES</h1>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <span class="text-gray-700 mr-4">Welcome, {{ auth()->user()->full_name }}</span>
                        <form method="POST" action="/logout" class="inline">
                            @csrf
                            <button type="submit" class="text-red-600 hover:text-red-800">Logout</button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h2 class="text-2xl font-semibold mb-4">Dashboard</h2>
                        <p class="text-gray-600">Role: {{ auth()->user()->role->name }}</p>
                        
                        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="bg-blue-50 p-6 rounded-lg">
                                <h3 class="font-semibold text-lg mb-2">Students</h3>
                                <p class="text-3xl font-bold text-blue-600">{{ \App\Models\Student::count() }}</p>
                            </div>
                            <div class="bg-green-50 p-6 rounded-lg">
                                <h3 class="font-semibold text-lg mb-2">Teachers</h3>
                                <p class="text-3xl font-bold text-green-600">{{ \App\Models\Teacher::count() }}</p>
                            </div>
                            <div class="bg-purple-50 p-6 rounded-lg">
                                <h3 class="font-semibold text-lg mb-2">Classes</h3>
                                <p class="text-3xl font-bold text-purple-600">{{ \App\Models\ClassModel::count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
