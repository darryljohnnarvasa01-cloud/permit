<?php
require_once __DIR__ . '/../../../app/core/session.php';
require_once __DIR__ . '/../../../app/helpers/helper.php';

Session::start();
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Valencia City Motorela Permit System' ?></title>
    <link href="/permit/public/css/tailwind.css?v=<?= time() ?>" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="h-full">
    <?php if (Session::isLoggedIn()): ?>
    <div class="min-h-full">
        <!-- Enhanced Professional Navbar with Gradient -->
        <nav class="bg-gradient-to-r from-indigo-600 via-blue-600 to-cyan-600 shadow-lg sticky top-0 z-50" x-data="{ mobileMenuOpen: false }">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    <div class="flex items-center space-x-8">
                        <div class="flex-shrink-0 flex items-center space-x-3">
                            <!-- Logo Icon -->
                            <div class="bg-white/20 backdrop-blur-sm p-2 rounded-lg ring-2 ring-white/30">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-white text-lg font-bold tracking-tight">Valencia City</h1>
                                <p class="text-blue-100 text-xs font-medium">Motorela Permit System</p>
                            </div>
                        </div>
                        <div class="hidden md:block">
                            <div class="flex items-baseline space-x-1">
                                <?php $currentPage = basename($_SERVER['PHP_SELF']); ?>
                                <a href="/permit/public/dashboard.php" class="<?= ($currentPage === 'dashboard.php') ? 'nav-item-light nav-item-active-light' : 'nav-item-light' ?>">
                                    <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                    </svg>
                                    Dashboard
                                </a>
                                
                                <?php if (Session::isAdmin() || Session::isStaff()): ?>
                                <a href="/permit/public/admin/motorela_manage.php" class="<?= ($currentPage === 'motorela_manage.php') ? 'nav-item-light nav-item-active-light' : 'nav-item-light' ?>">
                                    <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Permits
                                </a>
                                <?php endif; ?>
                                
                                <?php if (Session::isAdmin()): ?>
                                <a href="/permit/public/admin/categories.php" class="<?= ($currentPage === 'categories.php') ? 'nav-item-light nav-item-active-light' : 'nav-item-light' ?>">
                                    <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                    </svg>
                                    Categories
                                </a>
                                <a href="/permit/public/admin/permit_types.php" class="<?= ($currentPage === 'permit_types.php') ? 'nav-item-light nav-item-active-light' : 'nav-item-light' ?>">
                                    <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                    Permit Types
                                </a>
                                <a href="/permit/public/admin/users.php" class="<?= ($currentPage === 'users.php') ? 'nav-item-light nav-item-active-light' : 'nav-item-light' ?>">
                                    <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                    Users
                                </a>
                                <a href="/permit/public/admin/logs.php" class="<?= ($currentPage === 'logs.php') ? 'nav-item-light nav-item-active-light' : 'nav-item-light' ?>">
                                    <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Activity Logs
                                </a>
                                <?php endif; ?>
                                
                                <?php if (Session::isApplicant()): ?>
                                <a href="/permit/public/application_new.php" class="<?= ($currentPage === 'application_new.php') ? 'nav-item-light nav-item-active-light' : 'nav-item-light' ?>">New Permit</a>
                                <a href="/permit/public/application_renew.php" class="<?= ($currentPage === 'application_renew.php') ? 'nav-item-light nav-item-active-light' : 'nav-item-light' ?>">Renewal</a>
                                <a href="/permit/public/my_permits.php" class="<?= ($currentPage === 'my_permits.php') ? 'nav-item-light nav-item-active-light' : 'nav-item-light' ?>">My Permits</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <!-- User Role Badge - Hidden on mobile -->
                        <span class="hidden sm:inline-flex px-3 py-1.5 text-xs font-semibold rounded-full bg-white/20 text-white backdrop-blur-sm ring-1 ring-white/30">
                            <?= ucfirst(Session::getUserRole()) ?>
                        </span>
                        
                        <!-- User Dropdown - Visible on all screens -->
                        <div class="hidden md:block relative" x-data="{ open: false }">
                            <button @click="open = !open" type="button" class="flex items-center space-x-2 text-white hover:bg-white/20 px-3 py-2 rounded-lg backdrop-blur-sm transition-all duration-200">
                                <div class="w-8 h-8 bg-white/30 backdrop-blur-sm rounded-full flex items-center justify-center ring-2 ring-white/40">
                                    <span class="text-white text-sm font-bold"><?= strtoupper(substr(Session::getUserName(), 0, 1)) ?></span>
                                </div>
                                <span class="font-semibold"><?= escape(Session::getUserName()) ?></span>
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div x-show="open" @click.away="open = false" class="absolute right-0 z-10 mt-2 w-56 origin-top-right rounded-lg bg-white dark:bg-gray-800 shadow-lg ring-1 ring-black ring-opacity-5 overflow-hidden">
                                <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white"><?= escape(Session::getUserName()) ?></p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate"><?= escape(Session::get('user_email')) ?></p>
                                </div>
                                <div class="py-1">
                                    <a href="/permit/public/profile.php" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        My Profile
                                    </a>
                                    <a href="/permit/public/logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20">
                                        Sign out
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Mobile Menu Button -->
                        <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="md:hidden inline-flex items-center justify-center p-2 rounded-lg text-white hover:bg-white/20 backdrop-blur-sm transition-colors">
                            <span class="sr-only">Open main menu</span>
                            <svg x-show="!mobileMenuOpen" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            <svg x-show="mobileMenuOpen" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Mobile Menu Panel -->
            <div x-show="mobileMenuOpen" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-1"
                 class="md:hidden bg-gradient-to-b from-indigo-700 to-blue-700 border-t border-white/20 shadow-lg">
                <div class="px-4 pt-2 pb-3 space-y-1">
                    <!-- User Info Section -->
                    <div class="flex items-center space-x-3 px-3 py-3 border-b border-white/20">
                        <div class="w-10 h-10 bg-white/30 backdrop-blur-sm rounded-full flex items-center justify-center ring-2 ring-white/40">
                            <span class="text-white text-sm font-bold"><?= strtoupper(substr(Session::getUserName(), 0, 1)) ?></span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-white"><?= escape(Session::getUserName()) ?></p>
                            <p class="text-xs text-blue-100 truncate"><?= escape(Session::get('user_email')) ?></p>
                            <span class="inline-flex mt-1 px-2 py-0.5 text-xs font-semibold rounded-full bg-white/20 text-white backdrop-blur-sm">
                                <?= ucfirst(Session::getUserRole()) ?>
                            </span>
                        </div>
                    </div>
                    
                    <!-- Navigation Links -->
                    <a href="/permit/public/dashboard.php" class="block px-3 py-2 rounded-lg text-base font-medium text-white hover:bg-white/20 backdrop-blur-sm transition-colors">
                        Dashboard
                    </a>
                    
                    <?php if (Session::isAdmin() || Session::isStaff()): ?>
                    <a href="/permit/public/admin/motorela_manage.php" class="block px-3 py-2 rounded-lg text-base font-medium text-white hover:bg-white/20 backdrop-blur-sm transition-colors">
                        Permits
                    </a>
                    <?php endif; ?>
                    
                    <?php if (Session::isAdmin()): ?>
                    <a href="/permit/public/admin/categories.php" class="block px-3 py-2 rounded-lg text-base font-medium text-white hover:bg-white/20 backdrop-blur-sm transition-colors">
                        Categories
                    </a>
                    <a href="/permit/public/admin/permit_types.php" class="block px-3 py-2 rounded-lg text-base font-medium text-white hover:bg-white/20 backdrop-blur-sm transition-colors">
                        Permit Types
                    </a>
                    <a href="/permit/public/admin/users.php" class="block px-3 py-2 rounded-lg text-base font-medium text-white hover:bg-white/20 backdrop-blur-sm transition-colors">
                        Users
                    </a>
                    <a href="/permit/public/admin/logs.php" class="block px-3 py-2 rounded-lg text-base font-medium text-white hover:bg-white/20 backdrop-blur-sm transition-colors">
                        Activity Logs
                    </a>
                    <?php endif; ?>
                    
                    <?php if (Session::isApplicant()): ?>
                    <a href="/permit/public/application_new.php" class="block px-3 py-2 rounded-lg text-base font-medium text-white hover:bg-white/20 backdrop-blur-sm transition-colors">
                        New Permit
                    </a>
                    <a href="/permit/public/application_renew.php" class="block px-3 py-2 rounded-lg text-base font-medium text-white hover:bg-white/20 backdrop-blur-sm transition-colors">
                        Renewal
                    </a>
                    <a href="/permit/public/my_permits.php" class="block px-3 py-2 rounded-lg text-base font-medium text-white hover:bg-white/20 backdrop-blur-sm transition-colors">
                        My Permits
                    </a>
                    <?php endif; ?>
                    
                    <!-- Profile & Logout -->
                    <div class="border-t border-white/20 pt-2 mt-2">
                        <a href="/permit/public/profile.php" class="block px-3 py-2 rounded-lg text-base font-medium text-white hover:bg-white/20 backdrop-blur-sm transition-colors">
                            My Profile
                        </a>
                        <a href="/permit/public/logout.php" class="block px-3 py-2 rounded-lg text-base font-medium text-red-200 hover:bg-red-600/30 backdrop-blur-sm transition-colors">
                            Sign out
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <main>
            <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
                <?php if ($message = Session::flash('success')): ?>
                    <div class="alert alert-success"><?= escape($message) ?></div>
                <?php endif; ?>
                
                <?php if ($message = Session::flash('error')): ?>
                    <div class="alert alert-error"><?= escape($message) ?></div>
                <?php endif; ?>
                
                <?php if ($message = Session::flash('info')): ?>
                    <div class="alert alert-info"><?= escape($message) ?></div>
                <?php endif; ?>
    <?php else: ?>
    <!-- Public header for login/register pages -->
    <div class="min-h-full">
    <?php endif; ?>
