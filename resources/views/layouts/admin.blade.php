<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'HRIS - Archipelago Jaya Nusantara')</title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Nunito', sans-serif; }
        .sidebar { transition: transform 0.3s ease-in-out; }
        @media (max-width: 768px) {
            .sidebar-open { transform: translateX(0) !important; }
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-800 antialiased overflow-x-hidden">

    <!-- SIDEBAR -->
    <div class="fixed inset-y-0 left-0 w-[260px] bg-white border-r border-gray-100 z-[100] transform -translate-x-full md:translate-x-0 p-6 sidebar overflow-y-auto" id="sidebar">
        <!-- Logo Image & Text -->
        <div class="mb-10 flex items-center">
            <img src="{{ asset('img/logo.png') }}" class="h-10 mr-3 invert" alt="Logo ARCHIPELAGO">
            <div>
                <h2 class="font-extrabold text-xl text-gray-800 mb-0 leading-tight">ARCHIPELAGO</h2>
                <div class="text-[11px] text-orange-500 font-extrabold tracking-widest uppercase mt-1">HR SYSTEM</div>
            </div>
        </div>

        <div class="text-[11px] text-gray-400 font-bold mt-6 mb-3 uppercase tracking-wider">Main Menu</div>
        
        @php
            $linkClasses = "flex items-center px-4 py-3 text-sm font-semibold rounded-xl mb-2 transition-all duration-300 text-gray-500 hover:bg-orange-50 hover:text-orange-500 no-underline cursor-pointer";
            $activeClasses = "bg-orange-500 !text-white shadow-[0_4px_15px_rgba(255,165,0,0.25)]";
        @endphp

        <a href="{{ route('dashboard') }}" class="{{ $linkClasses }} {{ request()->routeIs('dashboard') ? $activeClasses : '' }}">
            <i class="bi bi-grid-1x2-fill mr-3 text-lg"></i> Dashboard
        </a>
        
        @if(auth()->user()->employee_role != 'employee')
        <a href="{{ url('/karyawan') }}" class="{{ $linkClasses }} {{ request()->is('karyawan*') ? $activeClasses : '' }}">
            <i class="bi bi-people-fill mr-3 text-lg"></i> Employees
        </a>
        @endif
        
        @if(auth()->user()->employee_role == 'employee')
        <a href="{{ route('presence.create') }}" class="{{ $linkClasses }} {{ request()->routeIs('presence.create') ? $activeClasses : '' }}">
            <i class="bi bi-camera-fill mr-3 text-lg"></i> Attendance Check
        </a>
        @elseif(auth()->user()->employee_role == 'hr')
        <a href="{{ route('presence.create') }}" class="{{ $linkClasses }} {{ request()->routeIs('presence.create') ? $activeClasses : '' }}">
            <i class="bi bi-camera-fill mr-3 text-lg"></i> Attendance Check
        </a>
        <a href="{{ url('/presence') }}" class="{{ $linkClasses }} {{ request()->is('presence*') && !request()->routeIs('presence.create') ? $activeClasses : '' }}">
            <i class="bi bi-calendar-check-fill mr-3 text-lg"></i> Attendance List
        </a>
        @else
        <a href="{{ url('/presence') }}" class="{{ $linkClasses }} {{ request()->is('presence*') ? $activeClasses : '' }}">
            <i class="bi bi-calendar-check-fill mr-3 text-lg"></i> Attendance List
        </a>
        @endif
        
        <a href="{{ url('/payroll') }}" class="{{ $linkClasses }} {{ request()->is('payroll*') ? $activeClasses : '' }}">
            <i class="bi bi-cash-stack mr-3 text-lg"></i> {{ auth()->user()->employee_role == 'employee' ? 'My Payroll' : 'Payroll' }}
        </a>
        
        @if(auth()->user()->employee_role == 'employee')
        <a href="{{ route('leave.index') }}" class="{{ $linkClasses }} {{ request()->is('*leave*') ? $activeClasses : '' }}">
            <i class="bi bi-envelope-paper-fill mr-3 text-lg"></i> Leave & Permission
        </a>
        <a href="{{ route('overtime.index') }}" class="{{ $linkClasses }} {{ request()->is('*overtime*') ? $activeClasses : '' }}">
            <i class="bi bi-briefcase-fill mr-3 text-lg"></i> Overtime
        </a>
        <a href="{{ route('reimbursement.index') }}" class="{{ $linkClasses }} {{ request()->is('*reimbursement*') ? $activeClasses : '' }}">
            <i class="bi bi-receipt mr-3 text-lg"></i> Reimbursement
        </a>
        @elseif(auth()->user()->employee_role == 'hr')
        
        <!-- Leave Submen -->
        <a onclick="toggleSubmenu('leaveCollapse')" class="{{ $linkClasses }} justify-between {{ request()->is('*leave*') ? $activeClasses : '' }}">
            <div class="flex items-center"><i class="bi bi-envelope-paper-fill mr-3 text-lg"></i> Leave & Permission</div>
            <i class="bi bi-chevron-down text-sm transition-transform duration-200" id="icon-leaveCollapse" style="{{ request()->is('*leave*') ? 'transform: rotate(180deg);' : '' }}"></i>
        </a>
        <div class="{{ request()->is('*leave*') ? 'block' : 'hidden' }}" id="leaveCollapse">
            <div class="flex flex-col gap-1 mt-1 mb-2">
                <a href="{{ route('leave.index') }}" class="pl-12 py-2 text-[13px] font-semibold text-gray-500 hover:text-orange-500 no-underline {{ request()->routeIs('leave.index', 'leave.create') ? '!text-orange-500' : '' }}">My Leave & Permission</a>
                <a href="{{ route('hr.leave.index') }}" class="pl-12 py-2 text-[13px] font-semibold text-gray-500 hover:text-orange-500 no-underline {{ request()->routeIs('hr.leave.index') ? '!text-orange-500' : '' }}">Employee Leave & Permission</a>
            </div>
        </div>

        <!-- Overtime Submenu -->
        <a onclick="toggleSubmenu('overtimeCollapse')" class="{{ $linkClasses }} justify-between {{ request()->is('*overtime*') ? $activeClasses : '' }}">
            <div class="flex items-center"><i class="bi bi-briefcase-fill mr-3 text-lg"></i> Overtime</div>
            <i class="bi bi-chevron-down text-sm transition-transform duration-200" id="icon-overtimeCollapse" style="{{ request()->is('*overtime*') ? 'transform: rotate(180deg);' : '' }}"></i>
        </a>
        <div class="{{ request()->is('*overtime*') ? 'block' : 'hidden' }}" id="overtimeCollapse">
            <div class="flex flex-col gap-1 mt-1 mb-2">
                <a href="{{ route('overtime.index') }}" class="pl-12 py-2 text-[13px] font-semibold text-gray-500 hover:text-orange-500 no-underline {{ request()->routeIs('overtime.index') ? '!text-orange-500' : '' }}">My Overtime</a>
                <a href="{{ route('hr.overtime.index') }}" class="pl-12 py-2 text-[13px] font-semibold text-gray-500 hover:text-orange-500 no-underline {{ request()->routeIs('hr.overtime.index') ? '!text-orange-500' : '' }}">Employee Overtime</a>
            </div>
        </div>

        <!-- Reimbursement Submenu -->
        <a onclick="toggleSubmenu('reimbursementCollapse')" class="{{ $linkClasses }} justify-between {{ request()->is('*reimbursement*') ? $activeClasses : '' }}">
            <div class="flex items-center"><i class="bi bi-receipt mr-3 text-lg"></i> Reimbursement</div>
            <i class="bi bi-chevron-down text-sm transition-transform duration-200" id="icon-reimbursementCollapse" style="{{ request()->is('*reimbursement*') ? 'transform: rotate(180deg);' : '' }}"></i>
        </a>
        <div class="{{ request()->is('*reimbursement*') ? 'block' : 'hidden' }}" id="reimbursementCollapse">
            <div class="flex flex-col gap-1 mt-1 mb-2">
                <a href="{{ route('reimbursement.index') }}" class="pl-12 py-2 text-[13px] font-semibold text-gray-500 hover:text-orange-500 no-underline {{ request()->routeIs('reimbursement.index', 'reimbursement.create') ? '!text-orange-500' : '' }}">My Reimbursement</a>
                <a href="{{ route('hr.reimbursement.index') }}" class="pl-12 py-2 text-[13px] font-semibold text-gray-500 hover:text-orange-500 no-underline {{ request()->routeIs('hr.reimbursement.index', 'hr.reimbursement.show') ? '!text-orange-500' : '' }}">Employee Reimbursement</a>
            </div>
        </div>
        
        @elseif(auth()->user()->employee_role == 'owner')
        <a href="{{ route('hr.leave.index') }}" class="{{ $linkClasses }} {{ request()->is('*leave*') ? $activeClasses : '' }}">
            <i class="bi bi-envelope-paper-fill mr-3 text-lg"></i> Leave & Permission
        </a>
        <a href="{{ route('hr.overtime.index') }}" class="{{ $linkClasses }} {{ request()->is('*overtime*') ? $activeClasses : '' }}">
            <i class="bi bi-briefcase-fill mr-3 text-lg"></i> Overtime
        </a>
        <a href="{{ route('hr.reimbursement.index') }}" class="{{ $linkClasses }} {{ request()->is('*reimbursement*') ? $activeClasses : '' }}">
            <i class="bi bi-receipt mr-3 text-lg"></i> Reimbursement
        </a>
        @endif
        
        @if(auth()->user()->employee_role == 'employee')
        <a href="{{ route('profile.face') }}" class="{{ $linkClasses }} {{ request()->routeIs('profile.face') ? $activeClasses : '' }}">
            <i class="bi bi-person-bounding-box mr-3 text-lg"></i> Face Enrollment
        </a>
        @endif

        <div class="text-[11px] text-gray-400 font-bold mt-6 mb-3 uppercase tracking-wider">System</div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="w-full text-left flex items-center px-4 py-3 text-sm font-bold text-red-500 hover:bg-red-50 hover:text-red-600 rounded-xl transition-colors duration-200 border-0 bg-transparent cursor-pointer">
                <i class="bi bi-box-arrow-right mr-3 text-lg"></i> Logout
            </button>
        </form>
    </div>

    <!-- MAIN CONTENT -->
    <div class="md:ml-[260px] p-4 md:p-8 pt-0 md:pt-8 transition-all duration-300 relative">
        <!-- TOP NAVBAR -->
        <div class="flex justify-between items-center mb-8 bg-white px-6 py-4 rounded-2xl shadow-[0_4px_20px_rgba(0,0,0,0.03)] selection:bg-orange-100">
            <div class="flex items-center">
                <button class="md:hidden bg-gray-100 p-2 rounded-lg text-gray-600 hover:bg-gray-200 transition mr-4 border-0" onclick="toggleSidebar()">
                    <i class="bi bi-list text-xl"></i>
                </button>
                <h4 class="font-extrabold text-xl m-0 text-gray-800 tracking-tight">@yield('header_title', 'Dashboard')</h4>
            </div>
            
            <div class="flex items-center gap-4 md:gap-6">
                @if(isset($pendingLeaveCount) && $pendingLeaveCount > 0 && (auth()->user()->employee_role == 'hr' || auth()->user()->employee_role == 'owner'))
                <a href="{{ route('hr.leave.index') }}" class="relative text-gray-400 hover:text-orange-500 transition-colors">
                    <i class="bi bi-bell-fill text-xl"></i>
                    <span class="absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white">
                        {{ $pendingLeaveCount }}
                    </span>
                </a>
                @endif

                <div class="flex items-center gap-3 cursor-pointer group">
                    <div class="text-right hidden md:block">
                        <div class="font-bold text-sm text-gray-800 group-hover:text-orange-500 transition-colors">{{ auth()->user()->employee_name }}</div>
                        <div class="text-[11px] text-gray-400 uppercase tracking-wide">{{ ucfirst(auth()->user()->employee_role) }}</div>
                    </div>
                    <div class="w-10 h-10 bg-orange-500 text-white rounded-xl flex items-center justify-center font-bold text-base shadow-sm group-hover:bg-orange-600 transition-colors">
                        {{ substr(auth()->user()->employee_name, 0, 2) }}
                    </div>
                </div>
            </div>
        </div>

        <!-- CONTENT -->
        <div class="w-full">
            @yield('content')
        </div>
    </div>

    <!-- Backdrop for mobile sidebar -->
    <div id="sidebarBackdrop" class="fixed inset-0 bg-gray-900/50 z-40 hidden md:hidden transition-opacity opacity-0" onclick="toggleSidebar()"></div>

    <script>
        function toggleSubmenu(id) {
            const menu = document.getElementById(id);
            const icon = document.getElementById('icon-' + id);
            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
                menu.classList.add('block');
                icon.style.transform = 'rotate(180deg)';
            } else {
                menu.classList.add('hidden');
                menu.classList.remove('block');
                icon.style.transform = '';
            }
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            
            sidebar.classList.toggle('-translate-x-full');
            sidebar.classList.toggle('sidebar-open');
            
            if (sidebar.classList.contains('sidebar-open')) {
                backdrop.classList.remove('hidden');
                setTimeout(() => backdrop.classList.remove('opacity-0'), 10);
            } else {
                backdrop.classList.add('opacity-0');
                setTimeout(() => backdrop.classList.add('hidden'), 300);
            }
        }
    </script>
    @stack('scripts')
</body>
</html>
