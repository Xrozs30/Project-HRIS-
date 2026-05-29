<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - HRIS ARCHIPELAGO</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <!-- Load Tailwind CSS -> Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
        }
    </style>
</head>
<body class="bg-white m-0 p-0 overflow-x-hidden min-h-screen">

    <div class="flex flex-col md:flex-row min-h-screen">
        <!-- LEFT PANEL -->
        <div class="hidden md:flex flex-col items-center justify-center p-8 text-center md:w-5/12 lg:w-5/12" style="background-color: #2b211d;">
            <div>
                <img src="{{ asset('img/logo.png') }}" alt="Archipelago Logo" class="max-w-[250px] mb-8 invert mx-auto brightness-0">
                <div class="text-[1.7rem] font-bold tracking-[1px] mb-2 text-white">ARCHIPELAGO</div>
                <div class="text-base font-normal text-white/70">Jaya Nusantara Internal Portal</div>
            </div>
        </div>

        <!-- RIGHT PANEL -->
        <div class="flex items-center justify-center p-8 md:w-7/12 lg:w-7/12 w-full bg-white">
            <div class="w-full max-w-[420px]">
                <div class="mb-8">
                    <h1 class="text-[2rem] font-bold text-gray-900 mb-2">Login</h1>
                    <p class="text-[0.95rem] text-gray-500 mb-10">Welcome back! Please enter your details.</p>
                </div>

                @if(session('error'))
                    <div class="bg-red-50 text-red-700 border border-red-200 text-sm px-4 py-3 rounded-xl mb-8 flex justify-between items-center" role="alert">
                        <div>{{ session('error') }}</div>
                        <button type="button" class="text-red-700 hover:text-red-900 focus:outline-none" onclick="this.parentElement.style.display='none'">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                @endif
                @if(session('warning'))
                    <div class="bg-amber-50 text-amber-700 border border-amber-200 text-sm px-4 py-3 rounded-xl mb-8 flex justify-between items-center" role="alert">
                        <div>{{ session('warning') }}</div>
                        <button type="button" class="text-amber-700 hover:text-amber-900 focus:outline-none" onclick="this.parentElement.style.display='none'">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    
                    <div class="mb-6">
                        <label class="block text-[0.85rem] font-bold text-gray-600 mb-2">Email Address</label>
                        <div class="relative flex items-stretch w-full">
                            <span class="flex items-center whitespace-nowrap px-4 bg-gray-50 border border-gray-200 border-r-0 rounded-l-xl text-gray-400">
                                <i class="bi bi-envelope"></i>
                            </span>
                            <input type="email" name="email" class="w-full bg-gray-50 border border-gray-200 focus:border-[#f05a22] focus:bg-white text-gray-800 rounded-r-xl px-4 py-3 focus:outline-none focus:ring-1 focus:ring-[#f05a22] text-[0.95rem] transition-colors" placeholder="admin@archipelago.com" required autofocus>
                        </div>
                        @error('email')
                            <div class="text-red-500 text-sm mt-2 font-medium">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-8">
                        <label class="block text-[0.85rem] font-bold text-gray-600 mb-2">Password</label>
                        <div class="relative flex items-stretch w-full">
                            <span class="flex items-center whitespace-nowrap px-4 bg-gray-50 border border-gray-200 border-r-0 rounded-l-xl text-gray-400">
                                <i class="bi bi-lock"></i>
                            </span>
                            <input type="password" name="password" class="w-full bg-gray-50 border border-gray-200 focus:border-[#f05a22] focus:bg-white text-gray-800 rounded-r-xl px-4 py-3 focus:outline-none focus:ring-1 focus:ring-[#f05a22] text-[0.95rem] transition-colors" placeholder="••••••••" required>
                        </div>
                    </div>

                    <div class="flex justify-end items-center text-[0.85rem] mb-8">
                        <a href="#" class="text-[#f05a22] hover:text-[#d04a1a] font-bold no-underline transition-colors">Forgot Password?</a>
                    </div>

                    <div class="grid">
                        <button type="submit" class="bg-[#f05a22] hover:bg-[#d04a1a] text-white border-none py-3.5 px-4 font-bold text-base rounded-xl transition-colors cursor-pointer w-full text-center">Sign In</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>
</html>
