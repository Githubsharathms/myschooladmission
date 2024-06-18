@extends('layouts.app')

@section('title', 'User Details')

@section('head')
    <link rel="stylesheet" href="{{ asset('css/userdetails.css') }}">
@endsection

@section('content')
<div class="container">
    <h2>User Details</h2>

    <!-- User List with Edit/Delete Buttons -->
    <div class="card mb-4">
        <div class="card-header">User List</div>
        <div class="card-body">
            @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Mobile Number</th>
                        <th>Name</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td contenteditable="false" class="editable" id="mobile_number_{{ $user->id }}">{{ $user->mobile_number }}</td>
                        <td contenteditable="false" class="editable" id="name_{{ $user->id }}">{{ $user->name }}</td>
                        <td>{{ $user->created_at }}</td>
                        <td>{{ $user->updated_at }}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-warning" onclick="editUser({{ $user->id }})" id="edit_btn_{{ $user->id }}">Edit</button>
                            <button type="button" class="btn btn-sm btn-primary hidden" onclick="updateUser({{ $user->id }})" id="update_btn_{{ $user->id }}">Update</button>
                            <form action="{{ route('userdetails.delete', ['id' => $user->id]) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create User Button at the Bottom -->
    <div class="text-center">
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createUserModal">Create User</button>
    </div>
</div>

<!-- Create User Modal -->
<div class="modal fade" id="createUserModal" tabindex="-1" role="dialog" aria-labelledby="createUserModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createUserModalLabel">Create User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="message" class="alert" style="display: none;"></div>
                <form id="createUserForm">
                    @csrf
                    <div id="mobile-step">
                        <div class="form-group">
                            <label for="mobile_number">Mobile Number:</label>
                            <input type="text" class="form-control" id="mobile_number" name="mobile_number" required>
                        </div>
                        <button type="button" class="btn btn-primary" onclick="submitMobile()">Submit Mobile Number</button>
                    </div>

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
                        <input type="hidden" id="signup_id" name="signup_id">
                        <button type="button" class="btn btn-primary" onclick="submitOTP()">Submit OTP</button>
                        <button type="button" class="btn btn-link hidden" id="resend-otp-button" onclick="resendOTP()">Resend OTP</button>
                    </div>

                    <div id="name-step" class="hidden">
                        <div class="form-group">
                            <label for="name">Name:</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <button type="button" class="btn btn-primary" onclick="submitName()">Submit Name</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript to handle create user form submission and OTP functionality -->
<script>
    let otpTimer;

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
                $('#signup_id').val(response.signup_id); // Set the hidden signup_id field
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
        let timer = duration, minutes, seconds;
        otpTimer = setInterval(function () {
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
                signup_id: $('#signup_id').val(), // Ensure signup_id is sent
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
                $('#signup_id').val(response.signup_id); // Set the hidden signup_id field
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
                signup_id: $('#signup_id').val(),
                name: $('#name').val(),
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#message').removeClass('alert-danger').addClass('alert-success').html(response.message).show();
                $('#createUserForm').hide(); // Hide the entire form upon successful signup
                location.reload(); // Reload the page to show updated user list
            },
            error: function(response) {
                $('#message').removeClass('alert-success').addClass('alert-danger').html('Error: ' + response.responseJSON.message).show();
            }
        });
    }

    function editUser(userId) {
        // Make fields editable
        $('#mobile_number_' + userId).attr('contenteditable', true).addClass('editable');
        $('#name_' + userId).attr('contenteditable', true).addClass('editable');
        $('#edit_btn_' + userId).addClass('hidden');
        $('#update_btn_' + userId).removeClass('hidden');
    }

    function updateUser(userId) {
        // Get edited values
        const mobileNumber = $('#mobile_number_' + userId).text();
        const name = $('#name_' + userId).text();

        $.ajax({
            url: "{{ route('userdetails.update', ['id' => '__id__']) }}".replace('__id__', userId),
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                mobile_number: mobileNumber,
                name: name,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.error) {
                    alert(response.error);
                } else {
                    location.reload();
                }
            },
            error: function(response) {
                alert('Error: ' + response.responseJSON.message);
            }
        });
    }
</script>
@endsection
