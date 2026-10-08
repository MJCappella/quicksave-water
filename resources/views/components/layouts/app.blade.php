<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Dashboard' }} | QUICKSAVE AGENCIES LTD - Drinking Water ERP</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="h-full font-sans antialiased text-slate-800 flex overflow-hidden" x-data="{ sidebarOpen: false, userMenuOpen: false }">

    <!-- Mobile sidebar overlay -->
    <div x-cloak x-show="sidebarOpen" class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-xs lg:hidden" @click="sidebarOpen = false"></div>

    <!-- SIDEBAR -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
           class="fixed inset-y-0 left-0 z-50 w-72 bg-gradient-to-b from-slate-900 via-emerald-950 to-slate-950 text-slate-200 transition-transform duration-200 ease-in-out lg:static lg:translate-x-0 flex flex-col shadow-2xl border-r border-emerald-900/40">
        
        <!-- Company Brand Logo Header -->
        <div class="h-20 flex items-center px-6 gap-3.5 border-b border-emerald-900/50 bg-slate-950/40">
            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-emerald-400 to-teal-600 flex items-center justify-center text-slate-950 font-black text-xl shadow-lg shadow-emerald-500/20 ring-2 ring-emerald-400/40">
                QS
            </div>
            <div class="flex-1 min-w-0">
                <div class="font-extrabold text-white text-base tracking-tight truncate flex items-center gap-1.5">
                    <span>QUICKSAVE</span>
                    <span class="text-xs px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-400 font-semibold border border-emerald-500/30">WATER</span>
                </div>
                <div class="text-[10px] text-emerald-300/80 font-medium tracking-wider uppercase truncate">
                    Agencies Ltd • Packaging ERP
                </div>
            </div>
        </div>

        <!-- Navigation Links Scrollable Area -->
        <nav class="flex-1 overflow-y-auto px-4 py-4 space-y-6 text-xs">
            
            <!-- Quick POS launcher button -->
            <div class="pt-1 pb-2">
                <a href="{{ route('pos.index') }}" 
                   class="group flex items-center justify-center gap-2 w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white font-bold shadow-lg shadow-emerald-600/30 transition-all transform hover:-translate-y-0.5 text-xs">
                    <svg class="w-4 h-4 text-white animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Point of Sale (POS)</span>
                    <span class="text-[9px] bg-slate-950/30 px-1.5 py-0.5 rounded-full uppercase tracking-wider font-semibold">Fast</span>
                </a>
            </div>

            <!-- SECTION: HOME -->
            <div>
                <div class="px-2 pb-1.5 text-[10px] font-bold uppercase tracking-wider text-emerald-400/80 flex items-center justify-between">
                    <span>Home</span>
                </div>
                <div class="space-y-1">
                    <a href="{{ route('dashboard') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('dashboard') ? 'bg-emerald-600/30 text-white border-l-4 border-emerald-400' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span>Dashboard</span>
                    </a>
                </div>
            </div>

            <!-- SECTION: PRODUCTION -->
            <div>
                <div class="px-2 pb-1.5 text-[10px] font-bold uppercase tracking-wider text-emerald-400/80">
                    1. Production Module
                </div>
                <div class="space-y-1">
                    <a href="{{ route('production.index') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('production.index') ? 'bg-emerald-600/30 text-white border-l-4 border-emerald-400' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                        <span>Bottling Batches</span>
                    </a>
                    <a href="{{ route('production.materials') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('production.materials') ? 'bg-emerald-600/30 text-white border-l-4 border-emerald-400' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        <span>Material Inventory & Purchases</span>
                    </a>
                    <a href="{{ route('production.utilization') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('production.utilization') ? 'bg-emerald-600/30 text-white border-l-4 border-emerald-400' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        <span>Utilization Tracking</span>
                    </a>
                </div>
            </div>

            <!-- SECTION: INVENTORY & ASSETS -->
            <div>
                <div class="px-2 pb-1.5 text-[10px] font-bold uppercase tracking-wider text-emerald-400/80">
                    2. Inventory & Assets
                </div>
                <div class="space-y-1">
                    <a href="{{ route('inventory.index') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('inventory.index') ? 'bg-emerald-600/30 text-white border-l-4 border-emerald-400' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                        <span>Finished Products Inventory</span>
                    </a>
                    <a href="{{ route('inventory.transfers') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('inventory.transfers') ? 'bg-emerald-600/30 text-white border-l-4 border-emerald-400' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                        <span>Store Transfers</span>
                    </a>
                    <a href="{{ route('inventory.adjustments') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('inventory.adjustments') ? 'bg-emerald-600/30 text-white border-l-4 border-emerald-400' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                        <span>Stock Adjustments</span>
                    </a>
                    <a href="{{ route('inventory.movements') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('inventory.movements') ? 'bg-emerald-600/30 text-white border-l-4 border-emerald-400' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Stock Movement Ledger</span>
                    </a>
                    <a href="{{ route('assets.index') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('assets.index') ? 'bg-emerald-600/30 text-white border-l-4 border-emerald-400' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        <span>Fixed Asset Register</span>
                    </a>
                </div>
            </div>

            <!-- SECTION: SALES & RECEIVABLES (AR) -->
            <div>
                <div class="px-2 pb-1.5 text-[10px] font-bold uppercase tracking-wider text-emerald-400/80">
                    3. Sales & Receivables (AR)
                </div>
                <div class="space-y-1">
                    <a href="{{ route('sales.customers') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('sales.customers') ? 'bg-emerald-600/30 text-white border-l-4 border-emerald-400' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span>Customer List & Balances</span>
                    </a>
                    <a href="{{ route('sales.quotations') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('sales.quotations') ? 'bg-emerald-600/30 text-white border-l-4 border-emerald-400' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Quotations</span>
                    </a>
                    <a href="{{ route('sales.orders') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('sales.orders') ? 'bg-emerald-600/30 text-white border-l-4 border-emerald-400' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        <span>Sales Orders</span>
                    </a>
                    <a href="{{ route('sales.delivery-notes') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('sales.delivery-notes') ? 'bg-emerald-600/30 text-white border-l-4 border-emerald-400' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17a5 5 0 01-.916-9.916 5.002 5.002 0 019.832 0A5.002 5.002 0 0116 17m-7-5l3-3m0 0l3 3m-3-3v12"/></svg>
                        <span>Delivery Notes</span>
                    </a>
                    <a href="{{ route('sales.invoices') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('sales.invoices') ? 'bg-emerald-600/30 text-white border-l-4 border-emerald-400' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                        <span>Sales Invoices</span>
                    </a>
                    <a href="{{ route('sales.credit-notes') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('sales.credit-notes') ? 'bg-emerald-600/30 text-white border-l-4 border-emerald-400' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                        <span>Credit Notes</span>
                    </a>
                    <a href="{{ route('sales.receipts') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('sales.receipts') ? 'bg-emerald-600/30 text-white border-l-4 border-emerald-400' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span>Customer & Direct Receipts</span>
                    </a>
                    <a href="{{ route('sales.aging-report') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('sales.aging-report') ? 'bg-emerald-600/30 text-white border-l-4 border-emerald-400' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>AR Aging Report</span>
                    </a>
                    <a href="{{ route('sales.reports') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('sales.reports') ? 'bg-emerald-600/30 text-white border-l-4 border-emerald-400' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Sales Reports</span>
                    </a>
                </div>
            </div>

            <!-- SECTION: PAYABLES (AP) -->
            <div>
                <div class="px-2 pb-1.5 text-[10px] font-bold uppercase tracking-wider text-emerald-400/80">
                    4. Payables Module (AP)
                </div>
                <div class="space-y-1">
                    <a href="{{ route('payables.vendors') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('payables.vendors') ? 'bg-emerald-600/30 text-white border-l-4 border-emerald-400' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        <span>Vendor List & Balances</span>
                    </a>
                    <a href="{{ route('payables.requisitions') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('payables.requisitions') ? 'bg-emerald-600/30 text-white border-l-4 border-emerald-400' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Purchase Requisitions</span>
                    </a>
                    <a href="{{ route('payables.quotes') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('payables.quotes') ? 'bg-emerald-600/30 text-white border-l-4 border-emerald-400' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/></svg>
                        <span>Purchase Quotes</span>
                    </a>
                    <a href="{{ route('payables.orders') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('payables.orders') ? 'bg-emerald-600/30 text-white border-l-4 border-emerald-400' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        <span>Purchase Orders</span>
                    </a>
                    <a href="{{ route('payables.grns') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('payables.grns') ? 'bg-emerald-600/30 text-white border-l-4 border-emerald-400' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Goods Received Note (G.R.N)</span>
                    </a>
                    <a href="{{ route('payables.invoices') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('payables.invoices') ? 'bg-emerald-600/30 text-white border-l-4 border-emerald-400' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Purchase Invoices</span>
                    </a>
                    <a href="{{ route('payables.debit-notes') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('payables.debit-notes') ? 'bg-emerald-600/30 text-white border-l-4 border-emerald-400' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 0118 0z"/></svg>
                        <span>Debit Notes</span>
                    </a>
                    <a href="{{ route('payables.vouchers') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('payables.vouchers') ? 'bg-emerald-600/30 text-white border-l-4 border-emerald-400' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span>Payment Vouchers</span>
                    </a>
                    <a href="{{ route('payables.aging-report') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('payables.aging-report') ? 'bg-emerald-600/30 text-white border-l-4 border-emerald-400' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>AP Ageing Report</span>
                    </a>
                    <a href="{{ route('payables.reports') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('payables.reports') ? 'bg-emerald-600/30 text-white border-l-4 border-emerald-400' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Purchase Reports</span>
                    </a>
                </div>
            </div>

            <!-- SECTION: FUNDS & BANK MANAGEMENT -->
            <div>
                <div class="px-2 pb-1.5 text-[10px] font-bold uppercase tracking-wider text-emerald-400/80">
                    5. Funds & Bank Management
                </div>
                <div class="space-y-1">
                    <a href="{{ route('banking.petty-cash') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('banking.petty-cash') ? 'bg-emerald-600/30 text-white border-l-4 border-emerald-400' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span>Petty Cashbook</span>
                    </a>
                    <a href="{{ route('banking.main-cash') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('banking.main-cash') ? 'bg-emerald-600/30 text-white border-l-4 border-emerald-400' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/></svg>
                        <span>Main Cashbook</span>
                    </a>
                    <a href="{{ route('banking.mpesa') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('banking.mpesa') ? 'bg-emerald-600/30 text-white border-l-4 border-emerald-400' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        <span class="flex items-center justify-between w-full">
                            <span>M-Pesa Paybill / Till</span>
                            <span class="text-[9px] bg-emerald-500/20 text-emerald-300 px-1.5 py-0.2 rounded font-semibold border border-emerald-500/30">Live</span>
                        </span>
                    </a>
                    <a href="{{ route('banking.reconciliation') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('banking.reconciliation') ? 'bg-emerald-600/30 text-white border-l-4 border-emerald-400' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        <span>Bank Reconciliation</span>
                    </a>
                </div>
            </div>

            <!-- SECTION: FINANCIAL REPORTS & GENERAL LEDGER -->
            <div class="pb-6">
                <div class="px-2 pb-1.5 text-[10px] font-bold uppercase tracking-wider text-emerald-400/80">
                    6. General Ledger & Reports
                </div>
                <div class="space-y-1">
                    <a href="{{ route('reports.accounts-ledger') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('reports.accounts-ledger') ? 'bg-emerald-600/30 text-white border-l-4 border-emerald-400' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        <span>Accounts Ledgers</span>
                    </a>
                    <a href="{{ route('reports.general-ledger') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('reports.general-ledger') ? 'bg-emerald-600/30 text-white border-l-4 border-emerald-400' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                        <span>General Ledger (Journals)</span>
                    </a>
                    <a href="{{ route('reports.trial-balance') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('reports.trial-balance') ? 'bg-emerald-600/30 text-white border-l-4 border-emerald-400' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                        <span>Trial Balance</span>
                    </a>
                    <a href="{{ route('reports.income-statement') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('reports.income-statement') ? 'bg-emerald-600/30 text-white border-l-4 border-emerald-400' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        <span>Income Statement (P&L)</span>
                    </a>
                    <a href="{{ route('reports.balance-sheet') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('reports.balance-sheet') ? 'bg-emerald-600/30 text-white border-l-4 border-emerald-400' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        <span>Balance Sheet</span>
                    </a>
                </div>
            </div>

        </nav>

        <!-- Sidebar Footer / User Profile -->
        <div class="p-3.5 border-t border-emerald-900/50 bg-slate-950/60 flex items-center justify-between">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-9 h-9 rounded-full bg-emerald-600 flex items-center justify-center text-white font-bold text-sm ring-2 ring-emerald-400/30">
                    J
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-white truncate">James Mwangi</p>
                    <p class="text-[10px] text-emerald-400 truncate">Branch Manager</p>
                </div>
            </div>
            <a href="{{ route('dashboard') }}" class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition" title="Logout">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            </a>
        </div>
    </aside>

    <!-- MAIN CONTENT AREA -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-slate-100/70">
        
        <!-- TOP APP BAR -->
        <header class="h-16 bg-white border-b border-slate-200/80 px-4 sm:px-6 flex items-center justify-between shrink-0 shadow-xs z-10">
            <div class="flex items-center gap-3">
                <button type="button" @click="sidebarOpen = true" class="lg:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                
                <!-- Store Badge / Branch Indicator (matching screenshot) -->
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                        <span class="font-bold tracking-wide uppercase">BRANCH 1 - NAIROBI</span>
                        <span class="text-emerald-500">•</span>
                        <span>SUPERIOR CENTER (STR-NRB-01)</span>
                    </span>
                </div>
            </div>

            <!-- Top Actions -->
            <div class="flex items-center gap-3">
                <a href="{{ route('inventory.transfers') }}" 
                   class="hidden sm:inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-semibold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 transition shadow-xs">
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    <span>Inter-Store Transfer</span>
                </a>

                <a href="{{ route('pos.index') }}" 
                   class="inline-flex items-center gap-2 px-4 py-1.5 rounded-lg text-xs font-bold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 shadow-md shadow-emerald-600/20 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Record New Sale (POS)</span>
                </a>

                <div class="h-6 w-px bg-slate-200"></div>

                <div class="flex items-center gap-2">
                    <span class="hidden md:inline-block px-2.5 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                        Branch Manager
                    </span>
                    <button class="text-xs text-slate-500 hover:text-slate-800 font-medium flex items-center gap-1">
                        <span>Logout</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </div>
            </div>
        </header>

        <!-- FLASH NOTIFICATIONS -->
        @if (session('success'))
            <div class="mx-6 mt-4 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center justify-between shadow-xs" x-data="{ show: true }" x-show="show">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <span class="font-semibold">{{ session('success') }}</span>
                </div>
                <button @click="show = false" class="text-emerald-600 hover:text-emerald-900">&times;</button>
            </div>
        @endif

        @if ($errors->any())
            <div class="mx-6 mt-4 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs shadow-xs" x-data="{ show: true }" x-show="show">
                <div class="flex items-center justify-between mb-1">
                    <span class="font-bold flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-rose-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        Please correct the following errors:
                    </span>
                    <button @click="show = false" class="text-rose-600 hover:text-rose-900">&times;</button>
                </div>
                <ul class="list-disc list-inside space-y-0.5 text-rose-700">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- DYNAMIC PAGE BODY -->
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
            @yield('content')
            {{ $slot ?? '' }}
        </main>
    </div>

</body>
</html>
