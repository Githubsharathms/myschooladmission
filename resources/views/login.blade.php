<!-- resources/views/login.blade.php -->

@extends('layouts.app')

@section('title', 'Login Form')

@section('head')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')
<div class="container">
    <h2>Login Form</h2>
    <div id="message" class="alert" style="display: none;"></div>

    <form id="login-form">
        <!-- Country Code and Mobile Number Step -->
        <div id="mobile-step">
            <div class="form-group">
                <label for="mobile_number" class="d-block">Mobile Number:</label>
                <input type="tel" class="form-control" id="mobile_number" name="mobile_number" required>
            </div>
            <button type="button" class="btn btn-primary" onclick="submitMobile()">Submit Mobile Number</button>
        </div>

        <!-- OTP Step -->
        <div id="otp-step" class="hidden">
            <div class="form-group">
                <label for="mobile_display">Mobile Number:</label>
                <input type="text" class="form-control" id="mobile_display" name="mobile_display" readonly>
            </div>
            <div class="form-group">
                <label for="otp_display">OTP:</label>
                <input type="text" class="form-control" id="otp_display" name="otp_display" readonly>
            </div>
            <div class="form-group">
                <label for="otp">Enter OTP:</label>
                <input type="text" class="form-control" id="otp" name="otp" required>
            </div>
            <p id="otp-timer"></p>
            <button type="button" class="btn btn-primary" onclick="submitOTP()">Submit OTP</button>
            <button type="button" class="btn btn-link hidden" id="resend-otp-button" onclick="resendOTP()">Resend OTP</button>
        </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>

<script>
    $(document).ready(function() {
        // Initialize intl-tel-input
        var input = document.querySelector("#mobile_number");
        var iti = window.intlTelInput(input, {
            initialCountry: "IN",  // Set default country to India
            separateDialCode: true,
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
        });

        // Validate phone number on input blur
        input.addEventListener('blur', function() {
            if (iti.isValidNumber()) {
                $('#message').hide(); // Hide any existing error messages
            } else {
                $('#message').removeClass('alert-info').addClass('alert-danger').html('Invalid phone number for the selected country code.').show();
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
            url: "{{ route('userlogin.submitMobile') }}",
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
                    $('#message').removeClass('alert-info').addClass('alert-danger').html(response.error).show();
                    return;
                }
                $('#mobile_display').val(fullNumber); // Display entered mobile number
                $('#otp_display').val(response.otp); // Display generated OTP
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
                $('#otp-timer').text("OTP has expired. Please request a new OTP.");
                $('#otp').prop('disabled', true);
                $('#resend-otp-button').removeClass('hidden'); // Show the resend OTP button
            }
        }, 1000);
    }

    function submitOTP() {
        $.ajax({
            url: "{{ route('userlogin.submitOTP') }}",
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
                    $('#message').removeClass('alert-danger').addClass('alert-success').html(response.message).show();
                    $('#login-form').hide(); // Hide the entire form upon successful login
                    clearInterval(otpTimer); // Stop the timer on successful OTP submission
                }
            },
            error: function(response) {
                $('#message').removeClass('alert-info').addClass('alert-danger').html('Error: ' + response.responseJSON.message).show();
            }
        });
    }

    function resendOTP() {
        $.ajax({
            url: "{{ route('userlogin.submitMobile') }}", // Use the same route as submitMobile for resending OTP
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
                $('#otp_display').val(response.otp); // Display new OTP
                $('#otp').val(''); // Clear the OTP input field
                $('#otp').prop('disabled', false); // Enable the OTP input field
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
</script>
@endsection
