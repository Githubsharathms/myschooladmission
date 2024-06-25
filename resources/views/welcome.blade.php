@extends('layouts.app')

@section('title', 'Home')

@section('head')
    <!-- Additional CSS or scripts for the home page -->
    <link href="{{ asset('css/welcome.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/icofont/1.0.1/css/icofont.min.css">
@endsection

@section('content')

    <header class="sticky-top">
        @include('includes.header') <!-- Include the header here -->
    </header>

    <!-- Banner section with inline CSS for background image -->
    <div class="banner-container" style="background-image: url('{{ asset('/assets/home_banner.jpg') }}');">
        <div class="container">
        <div class="row justify-content-center align-items-center text-center">
                <div class="col-md-8 text-center">
                    <h2>FIND THE BEST SCHOOL NEAR YOU</h2>
                    <p>Search the best schools from a list of 25,000 plus schools located across India. My School Admission offers personalized counseling support to help you find exactly what you're looking for.</p>
                </div>
            </div>

            <div class="row justify-content-center align-items-center mt-4">
                <div class="col-xl-7 col-lg-7 custom-search-form-container">
                    <form class="custom-search-form" action="#">
                        <div class="custom-search-form-categories">
                            <div class="icon">
                                <i class="icofont-ui-home"></i>
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
                                <i class="icofont-search"></i> Search
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>



    @include('includes.footer')

@endsection
