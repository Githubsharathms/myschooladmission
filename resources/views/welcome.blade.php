<!-- resources/views/welcome.blade.php -->

@extends('layouts.app')

@section('title', 'Home')

@section('head')
    <!-- Additional CSS or scripts for the home page -->
    <link href="{{ asset('css/welcome.css') }}" rel="stylesheet">
    <link href="{{ asset('css/footer.css') }}" rel="stylesheet"> <!-- Add this line -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/icofont/1.0.1/css/icofont.min.css" integrity="sha512-5lQ6x7Oh+4d8+LADvEq5eOTf26/c0PRw4tnZOHltx+8XZUsJoRxAfhISV1kR1T+GSyMBxGk3aQQsTjPP1M1arA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
@endsection

@section('content')

    @include('includes.header')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center">
                <h2>FIND THE BEST SCHOOL NEAR YOU</h2>
                <p>Search the best schools from a list of 25,000 plus schools located across India. My School Admission offers personalized counseling support to help you find exactly what you're looking for.</p>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-xl-7 col-lg-7 custom-search-form-container">
                <form class="custom-search-form" action="#">
                    <div class="custom-search-form-categories">
                        <div class="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512">
                                <rect x="48" y="48" width="176" height="176" rx="20" ry="20" fill="none"
                                    stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="32" />
                                <rect x="288" y="48" width="176" height="176" rx="20" ry="20" fill="none"
                                    stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="32" />
                                <rect x="48" y="288" width="176" height="176" rx="20" ry="20" fill="none"
                                    stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="32" />
                                <rect x="288" y="288" width="176" height="176" rx="20" ry="20" fill="none"
                                    stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="32" />
                            </svg>
                        </div>
                        <div class="categories-box">
                            <select class="wide">
                                <option data-display="Explore">All Categories</option>
                                <option value="1">Day School</option>
                                <option value="2">Pre School</option>
                                <option value="3">Boarding School</option>
                            </select>
                        </div>
                    </div>

                    <input placeholder="Find School In Your Locality" type="text">
                    <div class="custom-search-btn">
                        <button class="default-button" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('includes.footer')

@endsection
