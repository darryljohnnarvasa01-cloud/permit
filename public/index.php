<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Valencia City Motorela Permit System</title>
    <link href="/permit/public/css/tailwind.css?v=<?= time() ?>" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
    
    <!-- Navigation Bar -->
    <nav class="fixed w-full top-0 z-50 bg-white/80 backdrop-blur-lg shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center space-x-3">
                    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-3 rounded-xl shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">Valencia City</h1>
                        <p class="text-xs text-gray-600 font-medium">Motorela Permit System</p>
                    </div>
                </div>
                <div class="hidden md:flex space-x-4">
                    <a href="#features" class="text-gray-700 hover:text-blue-600 px-4 py-2 rounded-lg hover:bg-blue-50 transition-all font-medium">Features</a>
                    <a href="#how-it-works" class="text-gray-700 hover:text-blue-600 px-4 py-2 rounded-lg hover:bg-blue-50 transition-all font-medium">How It Works</a>
                    <a href="#contact" class="text-gray-700 hover:text-blue-600 px-4 py-2 rounded-lg hover:bg-blue-50 transition-all font-medium">Contact</a>
                    <a href="/permit/public/login.php" class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-6 py-2 rounded-xl hover:shadow-xl transition-all transform hover:-translate-y-0.5 font-semibold">Login</a>
                    <a href="/permit/public/register.php" class="bg-white text-blue-600 px-6 py-2 rounded-xl border-2 border-blue-600 hover:bg-blue-50 transition-all transform hover:-translate-y-0.5 font-semibold">Register</a>
                </div>
                <!-- Mobile Menu Button -->
                <div class="md:hidden" x-data="{ open: false }">
                    <button @click="open = !open" class="text-gray-700 p-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <div x-show="open" @click.away="open = false" class="absolute right-4 mt-2 w-48 bg-white rounded-xl shadow-2xl py-2">
                        <a href="#features" class="block px-4 py-2 text-gray-700 hover:bg-blue-50">Features</a>
                        <a href="#how-it-works" class="block px-4 py-2 text-gray-700 hover:bg-blue-50">How It Works</a>
                        <a href="#contact" class="block px-4 py-2 text-gray-700 hover:bg-blue-50">Contact</a>
                        <a href="/permit/public/login.php" class="block px-4 py-2 text-blue-600 hover:bg-blue-50 font-semibold">Login</a>
                        <a href="/permit/public/register.php" class="block px-4 py-2 text-blue-600 hover:bg-blue-50 font-semibold">Register</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="pt-32 pb-20 px-4 sm:px-6 lg:px-8 min-h-screen flex items-center">
        <div class="max-w-7xl mx-auto">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div class="space-y-8 animate-fade-in">
                    <div class="inline-block">
                        <span class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-4 py-2 rounded-full text-sm font-semibold shadow-lg">
                            🎉 Welcome to Modern Permit Management
                        </span>
                    </div>
                    <h1 class="text-5xl md:text-6xl font-bold leading-tight">
                        <span class="bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 bg-clip-text text-transparent">
                            Streamline Your
                        </span>
                        <br />
                        <span class="text-gray-900">Motorela Permits</span>
                    </h1>
                    <p class="text-xl text-gray-600 leading-relaxed">
                        Apply, track, and manage your motorela permits online. Fast, secure, and hassle-free registration process for Valencia City operators.
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <a href="/permit/public/register.php" class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-8 py-4 rounded-xl text-lg font-semibold hover:shadow-2xl transition-all transform hover:-translate-y-1 hover:scale-105 inline-flex items-center space-x-2">
                            <span>Get Started</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </a>
                        <a href="/permit/public/login.php" class="bg-white text-blue-600 px-8 py-4 rounded-xl text-lg font-semibold border-2 border-blue-600 hover:bg-blue-50 transition-all transform hover:-translate-y-1 inline-flex items-center space-x-2">
                            <span>Login</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                        </a>
                    </div>
                    <!-- Stats -->
                    <div class="grid grid-cols-3 gap-6 pt-8">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-blue-600">1000+</div>
                            <div class="text-sm text-gray-600">Permits Issued</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-indigo-600">24/7</div>
                            <div class="text-sm text-gray-600">Online Access</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-purple-600">Fast</div>
                            <div class="text-sm text-gray-600">Processing</div>
                        </div>
                    </div>
                </div>
                <div class="relative animate-scale-in">
                    <div class="relative z-10">
                        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 400'%3E%3Cdefs%3E%3ClinearGradient id='grad1' x1='0%25' y1='0%25' x2='100%25' y2='100%25'%3E%3Cstop offset='0%25' style='stop-color:%232563eb;stop-opacity:1' /%3E%3Cstop offset='100%25' style='stop-color:%237c3aed;stop-opacity:1' /%3E%3C/linearGradient%3E%3C/defs%3E%3Crect width='400' height='400' rx='20' fill='url(%23grad1)' /%3E%3Ctext x='50%25' y='50%25' font-size='100' fill='white' text-anchor='middle' dominant-baseline='middle'%3E🏍️%3C/text%3E%3C/svg%3E" alt="Motorela" class="rounded-3xl shadow-2xl">
                    </div>
                    <!-- Floating Elements -->
                    <div class="absolute -top-6 -right-6 w-24 h-24 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full animate-bounce-slow opacity-80"></div>
                    <div class="absolute -bottom-6 -left-6 w-32 h-32 bg-gradient-to-br from-green-400 to-cyan-500 rounded-full animate-pulse-slow opacity-80"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Powerful Features</h2>
                <p class="text-xl text-gray-600">Everything you need for efficient permit management</p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all transform hover:-translate-y-2 animate-fade-in">
                    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 w-16 h-16 rounded-xl flex items-center justify-center mb-6 shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Online Application</h3>
                    <p class="text-gray-600">Apply for new permits or renewals anytime, anywhere. No more long queues!</p>
                </div>
                
                <!-- Feature 2 -->
                <div class="bg-gradient-to-br from-purple-50 to-pink-50 p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all transform hover:-translate-y-2 animate-fade-in" style="animation-delay: 0.1s;">
                    <div class="bg-gradient-to-r from-purple-600 to-pink-600 w-16 h-16 rounded-xl flex items-center justify-center mb-6 shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Real-Time Tracking</h3>
                    <p class="text-gray-600">Monitor your application status in real-time with instant notifications.</p>
                </div>
                
                <!-- Feature 3 -->
                <div class="bg-gradient-to-br from-green-50 to-cyan-50 p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all transform hover:-translate-y-2 animate-fade-in" style="animation-delay: 0.2s;">
                    <div class="bg-gradient-to-r from-green-600 to-cyan-600 w-16 h-16 rounded-xl flex items-center justify-center mb-6 shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Secure & Safe</h3>
                    <p class="text-gray-600">Your data is encrypted and protected with industry-standard security.</p>
                </div>
                
                <!-- Feature 4 -->
                <div class="bg-gradient-to-br from-yellow-50 to-orange-50 p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all transform hover:-translate-y-2 animate-fade-in" style="animation-delay: 0.3s;">
                    <div class="bg-gradient-to-r from-yellow-600 to-orange-600 w-16 h-16 rounded-xl flex items-center justify-center mb-6 shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">QR Code Integration</h3>
                    <p class="text-gray-600">Get digital permits with QR codes for easy verification and validation.</p>
                </div>
                
                <!-- Feature 5 -->
                <div class="bg-gradient-to-br from-red-50 to-rose-50 p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all transform hover:-translate-y-2 animate-fade-in" style="animation-delay: 0.4s;">
                    <div class="bg-gradient-to-r from-red-600 to-rose-600 w-16 h-16 rounded-xl flex items-center justify-center mb-6 shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Document Upload</h3>
                    <p class="text-gray-600">Upload all required documents digitally. No more physical paperwork!</p>
                </div>
                
                <!-- Feature 6 -->
                <div class="bg-gradient-to-br from-teal-50 to-emerald-50 p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all transform hover:-translate-y-2 animate-fade-in" style="animation-delay: 0.5s;">
                    <div class="bg-gradient-to-r from-teal-600 to-emerald-600 w-16 h-16 rounded-xl flex items-center justify-center mb-6 shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Admin Dashboard</h3>
                    <p class="text-gray-600">Comprehensive admin panel for efficient permit processing and management.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="py-20 bg-gradient-to-br from-blue-50 to-indigo-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">How It Works</h2>
                <p class="text-xl text-gray-600">Get your permit in 4 simple steps</p>
            </div>
            <div class="grid md:grid-cols-4 gap-8">
                <div class="text-center animate-fade-in">
                    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 shadow-xl">
                        <span class="text-3xl font-bold text-white">1</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Register</h3>
                    <p class="text-gray-600">Create your account with basic information</p>
                </div>
                <div class="text-center animate-fade-in" style="animation-delay: 0.1s;">
                    <div class="bg-gradient-to-r from-purple-600 to-pink-600 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 shadow-xl">
                        <span class="text-3xl font-bold text-white">2</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Apply</h3>
                    <p class="text-gray-600">Fill out the online application form</p>
                </div>
                <div class="text-center animate-fade-in" style="animation-delay: 0.2s;">
                    <div class="bg-gradient-to-r from-green-600 to-cyan-600 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 shadow-xl">
                        <span class="text-3xl font-bold text-white">3</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Upload</h3>
                    <p class="text-gray-600">Submit required documents online</p>
                </div>
                <div class="text-center animate-fade-in" style="animation-delay: 0.3s;">
                    <div class="bg-gradient-to-r from-yellow-600 to-orange-600 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 shadow-xl">
                        <span class="text-3xl font-bold text-white">4</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Receive</h3>
                    <p class="text-gray-600">Get your digital permit with QR code</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl md:text-5xl font-bold text-white mb-6">Ready to Get Started?</h2>
            <p class="text-xl text-blue-100 mb-8">Join hundreds of motorela operators using our platform</p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="/permit/public/register.php" class="bg-white text-blue-600 px-8 py-4 rounded-xl text-lg font-semibold hover:shadow-2xl transition-all transform hover:-translate-y-1 hover:scale-105 inline-flex items-center space-x-2">
                    <span>Create Account</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contact" class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-2xl font-bold mb-4">Valencia City</h3>
                    <p class="text-gray-400">Motorela Permit System</p>
                    <p class="text-gray-400 mt-2">Making permit processing easier and faster for everyone.</p>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="/permit/public/login.php" class="text-gray-400 hover:text-white transition-colors">Login</a></li>
                        <li><a href="/permit/public/register.php" class="text-gray-400 hover:text-white transition-colors">Register</a></li>
                        <li><a href="#features" class="text-gray-400 hover:text-white transition-colors">Features</a></li>
                        <li><a href="#how-it-works" class="text-gray-400 hover:text-white transition-colors">How It Works</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Contact Us</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li>📍 Valencia City Hall</li>
                        <li>📧 permits@valencia.gov.ph</li>
                        <li>📞 (123) 456-7890</li>
                        <li>🕐 Mon-Fri: 8:00 AM - 5:00 PM</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; <?= date('Y') ?> Valencia City Motorela Permit System. All rights reserved.</p>
            </div>
        </div>
    </footer>

</body>
</html>
