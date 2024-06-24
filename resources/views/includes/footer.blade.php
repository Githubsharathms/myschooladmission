<!-- resources/views/includes/footer.blade.php -->
@section('head')
    <!-- Additional CSS or scripts for the home page -->
    <link href="{{ asset('css/footer.css') }}" rel="stylesheet">

@endsection
<footer class="footer mt-20 py-3 bg-light">
    <div class="container">
        <div class="row">
            <!-- School Section -->
            <div class="col-md-3">
                <h5>School</h5>
                <ul class="list-unstyled">
                    <li><a href="#">Day School </a></li>
                    <li><a href="#">Boarding School </a></li>
                    <li><a href="#">Pre School </a></li>
                </ul>
            </div>
            <!-- Curriculum Section -->
            <div class="col-md-3">
                <h5>Curriculum</h5>
                <ul class="list-unstyled">
                    <li><a href="#">CBSE</a></li>
                    <li><a href="#">ICSE</a></li>
                    <li><a href="#">State Board</a></li>
                </ul>
            </div>
            <!-- Gender Section -->
            <div class="col-md-3">
                <h5>Gender</h5>
                <ul class="list-unstyled">
                    <li><a href="#">Boys</a></li>
                    <li><a href="#">Girls</a></li>
                    <li><a href="#">Co-Ed</a></li>
                </ul>
            </div>
            <!-- Quick Links Section -->
            <div class="col-md-3">
                <h5>Quick Links</h5>
                <ul class="list-unstyled">
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Contact Us</a></li>
                    <li><a href="#">Add School</a></li>
                    <li><a href="#">Blog</a></li>
                </ul>
            </div>
        </div>

        <!-- Footer Bottom Text -->
        <div class="row mt-3">
            <div class="col text-center">
                <span class="text-muted">&copy; {{ date('Y') }} Your Company Name. All rights reserved.</span>
            </div>
        </div>
    </div>
</footer>
