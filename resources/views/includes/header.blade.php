<header>
    <nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top">
        <a class="navbar-brand" href="/">
            <img src="{{ asset('assets/logo-54-X-384-removebg-preview.png') }}" alt="Your Logo">
        </a>

        <!-- City Selector -->
        <div class="ml-auto border rounded border-secondary p-1 d-flex align-items-center">
            <div class="logo-search">
                <div class="city-changer d-flex align-items-center">
                    <span class="show_full_city d-flex align-items-center">
                        <svg viewBox="0 0 16 16" width="1em" height="1em" focusable="false" role="img" aria-label="geo alt fill" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi-geo-alt-fill b-icon bi text-ezy-primary">
                            <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"></path>
                        </svg>
                        <span class="ml-2">Select City</span>
                    </span>
                    <svg viewBox="0 0 16 16" width="1em" height="1em" focusable="false" role="img" aria-label="chevron down" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi-chevron-down b-icon bi ml-1" style="color:#000;">
                        <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"></path>
                    </svg>
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
            </ul>
        </div>
    </nav>
</header>
