@extends('layouts.app')

@section('title', 'Signup Form')

@section('content')
<div class="container">
    <h2>Sign Up</h2>
    <div id="message" class="alert" style="display: none;"></div>

    <form id="signup-form">
        <!-- Country Code and Mobile Number Step -->
        <div id="mobile-step">
            <div class="form-group">
                <label for="mobile_number" class="d-block">Mobile Number:</label>
                <input type="tel" class="form-control" id="mobile_number" name="mobile_number" required>
                <div id="number-error" class="text-danger" style="display: none;">Invalid phone number for the selected country code.</div>
            </div>
            <button type="button" class="btn btn-primary" onclick="submitMobile()">Sign Up</button>
        </div>

        <!-- OTP Step -->
        <div id="otp-step" class="hidden">
            <div class="form-group">
                <label for="mobile_display">Mobile Number:</label>
                <input type="text" class="form-control" id="mobile_display" name="mobile_display" readonly>
            </div>
            <div class="form-group">
                <p id="otp-timer" class="mb-1"></p>
                <span id="otp-expired-container" class="d-flex align-items-center">
                    <span id="otp-expired-msg" class="text-danger mr-2"></span>
                    <button type="button" class="btn btn-link hidden p-0" id="resend-otp-button" onclick="resendOTP()">Resend OTP</button>
                </span>
            </div>
            <div class="form-group">
                <label for="otp">OTP:</label>
                <input type="text" class="form-control" id="otp" name="otp" required>
            </div>
            <button type="button" class="btn btn-primary" id="submit-otp-button" onclick="submitOTP()">Submit OTP</button>
        </div>

        <!-- Name Step -->
        <div id="name-step" class="hidden">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" class="form-control" id="name" name="name" required oninput="validateName(this)">
                <div id="name-error" class="text-danger" style="display: none;">Only alphabets and spaces are allowed.</div>
            </div>
            <button type="button" class="btn btn-primary" onclick="submitName()">Sign Up</button>
        </div>
    </form>

    <!-- Login button container -->
    <div id="login-button-container" class="text-center mt-3" style="display: none;">
        Already have an account? <a href="{{ route('login') }}" class="btn btn-link">Login</a>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>

<script>
    $(document).ready(function() {
        // Initialize intl-tel-input
        var input = document.querySelector("#mobile_number");
        var iti = window.intlTelInput(input, {
            initialCountry: "in", // Set default country to India
            separateDialCode: true,
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
        });

        // Validate phone number on input blur
        input.addEventListener('blur', function() {
            if (iti.isValidNumber()) {
                $('#number-error').hide();
            } else {
                $('#number-error').show();
            }
        });

        // Set initial country in mobile number input
        input.addEventListener("countrychange", function() {
            var selectedCountry = iti.getSelectedCountryData();
            $('#country_code').val('+' + selectedCountry.dialCode);
        });
    });

    let otpTimer;

    function submitMobile() {
        var input = document.querySelector("#mobile_number");
        var iti = window.intlTelInputGlobals.getInstance(input);

        if (!iti.isValidNumber()) {
            $('#message').removeClass('alert-info').addClass('alert-danger').html('Invalid phone number for the selected country code.').show();
            return;
        }

        var fullNumber = iti.getNumber();

        $.ajax({
            url: "{{ route('signup.submitMobile') }}",
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                mobile_number: fullNumber,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.error) {
                    if (response.error === 'This number is already taken.') {
                        alert(response.error); // Display alert popup
                        $('#login-button-container').show(); // Display login button
                    } else {
                        $('#message').removeClass('alert-info').addClass('alert-danger').html(response.error).show();
                    }
                    return;
                }
                // Proceed with the rest of the success logic as before
                $('#mobile_display').val(fullNumber); // Display entered mobile number
                alert('Your OTP is: ' + response.otp); // Display OTP in alert
                $('#mobile-step').addClass('hidden');
                $('#otp-step').removeClass('hidden');
                $('#message').removeClass('alert-danger').addClass('alert-info').html('OTP sent').show();

                // Hide the resend OTP button initially
                $('#resend-otp-button').addClass('hidden');

                // Start OTP timer
                startOtpTimer(60); // 60 seconds timer
            },
            error: function(response) {
                $('#message').removeClass('alert-info').addClass('alert-danger').html('Error: ' + response.responseJSON.message).show();
            }
        });
    }

    function startOtpTimer(duration) {
        let timer = duration,
            minutes, seconds;
        otpTimer = setInterval(function() {
            minutes = parseInt(timer / 60, 10);
            seconds = parseInt(timer % 60, 10);

            minutes = minutes < 10 ? "0" + minutes : minutes;
            seconds = seconds < 10 ? "0" + seconds : seconds;

            $('#otp-timer').text("OTP expires in " + minutes + ":" + seconds);

            if (--timer < 0) {
                clearInterval(otpTimer);
                $('#otp-timer').text("OTP has expired.");
                $('#otp').prop('disabled', true);
                $('#resend-otp-button').removeClass('hidden'); // Show the resend OTP button
            }
        }, 1000);
    }

    function submitOTP() {
        $.ajax({
            url: "{{ route('signup.submitOTP') }}",
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                otp: $('#otp').val(),
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.error) {
                    if (response.error === 'OTP has expired. Please request a new OTP.') {
                        $('#otp').prop('disabled', true);
                        $('#otp-timer').text(response.error);
                        $('#message').removeClass('alert-info').addClass('alert-danger').html(response.error).show();
                    } else {
                        $('#message').removeClass('alert-info').addClass('alert-danger').html(response.error).show();
                    }
                } else {
                    $('#name-step').removeClass('hidden'); // Show the name step without hiding OTP step
                    $('#message').hide();
                    clearInterval(otpTimer); // Stop the timer on successful OTP submission
                    $('#submit-otp-button').hide(); // Hide the Submit OTP button
                }
            },
            error: function(response) {
                $('#message').removeClass('alert-info').addClass('alert-danger').html('Error: ' + response.responseJSON.message).show();
            }
        });
    }

    function resendOTP() {
        $.ajax({
            url: "{{ route('signup.submitMobile') }}", // Use the same route as submitMobile for resending OTP
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                mobile_number: $('#mobile_display').val(), // Use the existing mobile number
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.error) {
                    $('#message').removeClass('alert-info').addClass('alert-danger').html(response.error).show();
                    return;
                }
                alert('Your OTP is: ' + response.otp); // Display new OTP in alert
                $('#otp').val(''); // Clear the OTP input field
                $('#otp').prop('disabled', false); // Re-enable the OTP input field
                $('#message').removeClass('alert-danger').addClass('alert-info').html('OTP resent').show();

                // Hide the resend OTP button
                $('#resend-otp-button').addClass('hidden');

                // Restart OTP timer
                clearInterval(otpTimer);
                startOtpTimer(60); // 60 seconds timer
            },
            error: function(response) {
                $('#message').removeClass('alert-info').addClass('alert-danger').html('Error: ' + response.responseJSON.message).show();
            }
        });
    }

    function submitName() {
        $.ajax({
            url: "{{ route('signup.submitName') }}",
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                name: $('#name').val(),
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#message').removeClass('alert-danger').addClass('alert-success').html(response.message).show();
                $('#signup-form').hide(); // Hide the entire form upon successful signup
            },
            error: function(response) {
                $('#message').removeClass('alert-success').addClass('alert-danger').html('Error: ' + response.responseJSON.message).show();
            }
        });
    }

    function validateName(input) {
        var regex = /^[a-zA-Z\s]*$/;
        if (!regex.test(input.value)) {
            input.value = input.value.replace(/[^a-zA-Z\s]/g, '');
            $('#name-error').show();
        } else {
            $('#name-error').hide();
        }
    }
</script>
@endsection
