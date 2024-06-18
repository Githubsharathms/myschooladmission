<!-- resources/views/signup.blade.php -->

@extends('layouts.app')

@section('title', 'Signup Form')

@section('content')
<div class="container">
    <h2>Signup Form</h2>
    <div id="message" class="alert" style="display: none;"></div>

    <form id="signup-form">
        <!-- Mobile Step -->
        <div id="mobile-step">
            <div class="form-group">
                <label for="mobile_number">Mobile Number:</label>
                <input type="text" class="form-control" id="mobile_number" name="mobile_number" onkeypress="return isNumber(event)" required>
                <small id="number-message" class="text-danger" style="display: none;">Only numbers allowed.</small>
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

        <!-- Name Step -->
        <div id="name-step" class="hidden">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" class="form-control" id="name" name="name" onkeypress="return isAlphabetOrSpace(event)" required>
                <small id="name-message" class="text-danger" style="display: none;">Only alphabets and spaces allowed.</small>
            </div>
            <button type="button" class="btn btn-primary" onclick="submitName()">Submit Name</button>
        </div>
    </form>
</div>

<script>
    let otpTimer;

    function isNumber(event) {
        var charCode = (event.which) ? event.which : event.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            document.getElementById('number-message').style.display = 'block';
            return false;
        }
        document.getElementById('number-message').style.display = 'none';
        return true;
    }

    function isAlphabetOrSpace(event) {
        var charCode = (event.which) ? event.which : event.keyCode;
        if ((charCode < 65 || charCode > 90) && (charCode < 97 || charCode > 122) && charCode != 32 && charCode != 0) {
            document.getElementById('name-message').style.display = 'block';
            return false;
        }
        document.getElementById('name-message').style.display = 'none';
        return true;
    }

    function submitMobile() {
        $.ajax({
            url: "{{ route('signup.submitMobile') }}",
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                mobile_number: $('#mobile_number').val(),
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.error) {
                    $('#message').removeClass('alert-info').addClass('alert-danger').html(response.error).show();
                    return;
                }
                $('#mobile_display').val($('#mobile_number').val()); // Display entered mobile number
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
</script>
@endsection
