<!-- resources/views/includes/header.blade.php -->

<header>
    <nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top">
        <a class="navbar-brand" href="/">
            <img src="{{ asset('assets/logo-54-X-384-removebg-preview.png') }}" alt="Your Logo">
        </a>

        <!-- City Selector -->
        <div class=" p-1 d-flex align-items-center">
            <div class="logo-search">
                <div class="city-changer d-flex align-items-center">
                    <span class="show_full_city d-flex align-items-center">
                        <i class="fas fa-map-marker-alt"></i>
                        <span class="ml-2">Select City</span>
                        <i class="fas fa-chevron-down ml-1"></i>
                    </span>
                </div>
            </div>
        </div>

        <!-- Navbar Toggler -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar Items -->
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="#" style="color: black;">Add School</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('signup.form') }}" style="color: black;">Signup</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('login') }}" style="color: black;">Login</a>
                </li>
            </ul>
        </div>
    </nav>
</header>
