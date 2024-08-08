<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Butik Antam</title>
    <link rel="shortcut icon" href="image/favicon/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="<?php echo base_url('assets/frontend/css/bootstrap.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/frontend/fonts/fontawesome-5/css/all.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/frontend/plugins/aos/aos.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/frontend/plugins/fancybox/jquery.fancybox.min.css'); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/frontend/css/slick.css'); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/frontend/css/main.css'); ?>">

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-CGVVLJ8P0L">
    </script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'G-CGVVLJ8P0L');
    </script>
</head>

<body data-theme="light">
    <div class="site-wrapper position-relative overflow-hidden ">
        <div id="loading">
            <div class="preloader">
                <svg version="1.1" id="L9" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 200 200" enable-background="new 0 0 0 0" xml:space="preserve">
                    <circle cx="20" cy="50" r="8" fill="#FF4B36">
                        <animateTransform attributeType="xml" attributeName="transform" type="translate" values="0 0; 0 50; 0 0" begin="0" dur="1.2s" repeatCount="indefinite" />
                    </circle>
                    <circle cx="50" cy="50" r="8" fill="#FF713B">
                        <animateTransform attributeType="xml" attributeName="transform" type="translate" values="0 0; 0 50; 0 0" begin="0.2s" dur="1.2s" repeatCount="indefinite" />
                    </circle>
                    <circle cx="80" cy="50" r="8" fill="#FF713B">
                        <animateTransform attributeType="xml" attributeName="transform" type="translate" values="0 0; 0 50; 0 0" begin="0.4s" dur="1.2s" repeatCount="indefinite" />
                    </circle>
                    <circle cx="110" cy="50" r="8" fill="#FF873D">
                        <animateTransform attributeType="xml" attributeName="transform" type="translate" values="0 0; 0 50; 0 0" begin="0.6s" dur="1.2s" repeatCount="indefinite" />
                    </circle>
                    <circle cx="140" cy="50" r="8" fill="#FF873D">
                        <animateTransform attributeType="xml" attributeName="transform" type="translate" values="0 0; 0 50; 0 0" begin="0.8s" dur="1.2s" repeatCount="indefinite" />
                    </circle>
                    <circle cx="170" cy="50" r="8" fill="#FF9C40">
                        <animateTransform attributeType="xml" attributeName="transform" type="translate" values="0 0; 0 50; 0 0" begin="1s" dur="1.2s" repeatCount="indefinite" />
                    </circle>
                </svg>
            </div>
        </div>

        <header class="site-header site-header--menu-right site-header dark-mode-texts site-header--absolute">
            <div class="container">
                <nav class="navbar site-navbar offcanvas-active navbar-expand-lg  px-lg-2 px-0 pt-lg-3 pt-7">
                    <div class="brand-logo pt-1">
                        <a href="#">
                            <img src="<?php echo base_url('assets/frontend/image/logo/logo-2.png') ?>" alt="image" class="light-version-logo">
                            <img src="<?php echo base_url('assets/logo.png') ?>" alt="image" class="dark-version-logo" style="width: 200px;">
                        </a>
                    </div>
                    <div class="collapse navbar-collapse" id="mobile-menu">
                        <div class="navbar-nav-wrapper">
                            <ul class="navbar-nav main-menu">
                                <li class="nav-item">
                                    <a class="nav-link" href="#about" role="button" aria-expanded="false">About</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#contact" role="button" aria-expanded="false">Contact</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#testimonial" role="button" aria-expanded="false">Testimonial</a>
                                </li>
                            </ul>
                        </div>
                        <button class="d-block d-lg-none offcanvas-btn-close" type="button" data-toggle="collapse" data-target="#mobile-menu" aria-controls="mobile-menu" aria-expanded="true" aria-label="Toggle navigation">
                            <i class="gr-cross-icon"></i>
                        </button>
                    </div>
                    <div class="header-btn pl-lg-8 ml-auto">
                        <a class="btn header-button header-btn-1 mr-lg-0 mr-3" href="<?php echo site_url('signup') ?>">
                            DAFTAR
                        </a>
                    </div>
                    <button class="navbar-toggler btn-close-off-canvas  hamburger-icon border-0" type="button" data-toggle="collapse" data-target="#mobile-menu" aria-controls="mobile-menu" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="hamburger hamburger--squeeze js-hamburger">
                            <span class="hamburger-box">
                                <span class="hamburger-inner"></span>
                            </span>
                        </span>
                    </button>
                </nav>
            </div>
        </header>

        <section class="jumbotron text-center" class="mx-12">
            <div class="container">
                <img src="<?php echo base_url('assets/frontend/image/banner/banner.png') ?>" alt="" class="img-fluid">
            </div>
        </section>

        <section class="mx-12">
            <div>
                <h4>Logam Mulia ANTAM Reinvented with Certicard</h4>

                <p>Logam mulia ini diproduksi oleh PT Aneka Tambang, Tbk dengan kadar terpercaya, puritas tertinggi 99.99%.
                    Edisi terbaru ini telah dilengkapi dengan kemasan Certicard dimana Anda bisa mengecek keaslian logam mulia Anda dengan
                    menggunakan aplikasi Certieye yang dapat diunduh melalui Play Store (Android) maupun App Store (iPhone). Silahkan klik
                    "Verifikasi Produk" untuk informasi lebih lengkap.
                </p>
            </div>

            <div class="row">
                <div class="col-md-3 col-6 py-4">
                    <div class="card">
                        <img src="<?php echo base_url('assets/frontend/image/products/product-4.png') ?>" class="card-img-top" alt="Emas LM 1 gram">
                        <div class="card-body">
                            <h5 class="card-title">Emas LM 1 gram</h5>
                            <p class="card-text">Harga kami jual: Rp 115.000</p>
                            <p class="card-text">Harga kami beli: Rp 990.000</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6 py-4">
                    <div class="card">
                        <img src="<?php echo base_url('assets/frontend/image/products/product-5.png') ?>" class="card-img-top" alt="">
                        <div class="card-body">
                            <h5 class="card-title">Emas LM 2 gram</h5>
                            <p class="card-text">Harga kami jual: 2.175.000</p>
                            <p class="card-text">Harga kami beli: 1.980.000</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6 py-4">
                    <div class="card">
                        <img src="<?php echo base_url('assets/frontend/image/products/product-6.png') ?>" class="card-img-top" alt="">
                        <div class="card-body">
                            <h5 class="card-title">Emas LM 3 gram</h5>
                            <p class="card-text">Harga kami jual: 3.235.000</p>
                            <p class="card-text">Harga kami beli: 2.970.000</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6 py-4">
                    <div class="card">
                        <img src="<?php echo base_url('assets/frontend/image/products/product-7.png') ?>" class="card-img-top" alt="">
                        <div class="card-body">
                            <h5 class="card-title">Emas LM 5 gram</h5>
                            <p class="card-text">Harga kami jual: 5.360.000</p>
                            <p class="card-text">Harga kami beli: 4.950.000</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6 py-4">
                    <div class="card">
                        <img src="<?php echo base_url('assets/frontend/image/products/product-8.png') ?>" class="card-img-top" alt="">
                        <div class="card-body">
                            <h5 class="card-title">Emas LM 10 gram</h5>
                            <p class="card-text">Harga kami jual: 10.670.000</p>
                            <p class="card-text">Harga kami beli: 9.900.000</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6 py-4">
                    <div class="card">
                        <img src="<?php echo base_url('assets/frontend/image/products/product-9.png') ?>" class="card-img-top" alt="">
                        <div class="card-body">
                            <h5 class="card-title">Emas LM 25 gram</h5>
                            <p class="card-text">Harga kami jual: 26.560.000</p>
                            <p class="card-text">Harga kami beli: 24.750.000</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6 py-4">
                    <div class="card">
                        <img src="<?php echo base_url('assets/frontend/image/products/product-10.png') ?>" class="card-img-top" alt="">
                        <div class="card-body">
                            <h5 class="card-title">Emas LM 50 gram</h5>
                            <p class="card-text">Harga kami jual: 53.050.000</p>
                            <p class="card-text">Harga kami beli: 49.500.000</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6 py-4">
                    <div class="card">
                        <img src="<?php echo base_url('assets/frontend/image/products/product-11.png') ?>" class="card-img-top" alt="">
                        <div class="card-body">
                            <h5 class="card-title">Emas LM 100 gram</h5>
                            <p class="card-text">Harga kami jual: 106.000.000</p>
                            <p class="card-text">Harga kami beli: 102.000.000</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="mx-12">
            <div class="row justify-content-center mb-n9 mt-21">
                <div class="col-lg-4 col-md-6 col-sm-11 col-xs-12 mb-9" data-aos="fade-up" data-aos-duration="800" data-aos-once="true">
                    <div class="card-hover-2 h-100 bg-white rounded-10">
                        <div class="card-image position-relative">
                            <img class="w-100" src="<?php echo base_url('assets/frontend/image/products/product-1.png') ?>" alt="image">
                            <div class="card-date card-gradient-1 pt-10 pb-3 pl-10 position-absolute absolute-bottom-left w-100">
                                <h4 class="font-size-4 text-white">Truntum</h4>
                            </div>
                        </div>
                        <div class="px-9 py-10">
                            <h4 class="font-size-7 mb-7 text-black">Truntum</h4>
                            <a target="_blank" href="<?php echo site_url('assets/frontend/image/products/product-1.png') ?>">View Details</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-11 col-xs-12 mb-9" data-aos="fade-up" data-aos-duration="800" data-aos-once="true">
                    <div class="card-hover-2 h-100 bg-white rounded-10">
                        <div class="card-image position-relative">
                            <img class="w-100" src="<?php echo base_url('assets/frontend/image/products/product-2.png') ?>" alt="image">
                            <div class="card-date card-gradient-1 pt-10 pb-3 pl-10 position-absolute absolute-bottom-left w-100">
                                <h4 class="font-size-4 text-white">Parang Barong</h4>
                            </div>
                        </div>
                        <div class="px-9 py-10">
                            <h4 class="font-size-7 mb-7 text-black">Parang Barong</h4>
                            <a target="_blank" href="<?php echo site_url('assets/frontend/image/products/product-2.png') ?>">View Details</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-11 col-xs-12 mb-9" data-aos="fade-up" data-aos-duration="800" data-aos-once="true">
                    <div class="card-hover-2 h-100 bg-white rounded-10">
                        <div class="card-image position-relative">
                            <img class="w-100" src="<?php echo base_url('assets/frontend/image/products/product-3.png') ?>" alt="image">
                            <div class="card-date card-gradient-1 pt-10 pb-3 pl-10 position-absolute absolute-bottom-left w-100">
                                <h4 class="font-size-4 text-white">Wahyu Tumurun</h4>
                            </div>
                        </div>
                        <div class="px-9 py-10">
                            <h4 class="font-size-7 mb-7 text-black">Wahyu Tumurun</h4>
                            <a target="_blank" href="<?php echo site_url('assets/frontend/image/products/product-3.png') ?>">View Details</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="mx-12 my-18">
            <div>
                <div>
                    <h4>Verifikasi Produk</h4>

                    <p>Emas/ logam mulia ANTAM hadir dengan desain dan kemasan baru untuk meningkatkan nilai lebih (value) berupa perlindungan
                        dan keamanan bagi investasi Anda. Desain kemasan yang lebih modern dan elegan membuat Anda bangga menggunakannya sebagai
                        penghargaan bagi orang-orang terbaik di sekitar Anda. Produk dengan kadar dan kualitas yang senantiasa menjamin
                        kenyamanan dan keamanan investasi jangka panjang.
                    </p>
                </div>

                <div class="d-flex justify-content-center mt-12">
                    <img src="<?php echo base_url('assets/frontend/image/products/detail-product.png') ?>" class="img-fluid" alt="">
                </div>
            </div>
        </section>

        <section class="bg-white-lilac position-relative pt-lg-5 pb-lg-5 pt-md-5 pb-md-5 pt-4 pb-4">
            <div class="container">
                <div class="row justify-content-center mb-5">
                    <div class="col-lg-7 text-center">
                        <h2 class="text-black font-size-6 mb-4 py-12">Product Image</h2>
                    </div>
                </div>
                <div class="row justify-content-center pb-12">
                    <div class="col-12 col-md-6 col-lg-4 mb-4">
                        <div class="card">
                            <img src="<?php echo base_url('assets/frontend/image/products/product-image-1.png') ?>" alt="" class="card-img-top img-fluid" style="width: 100%; height: auto;">
                            <div class="card-body">
                                <a target="_blank" href="<?php echo base_url('assets/frontend/image/products/product-image-1.png') ?>">View Image</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4 mb-4">
                        <div class="card">
                            <img src="<?php echo base_url('assets/frontend/image/products/product-image-2.png') ?>" alt="" class="card-img-top img-fluid" style="width: 100%; height: auto;">
                            <div class="card-body">
                                <a target="_blank" href="<?php echo base_url('assets/frontend/image/products/product-image-2.png') ?>">View Image</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4 mb-4">
                        <div class="card">
                            <img src="<?php echo base_url('assets/frontend/image/products/product-image-3.png') ?>" alt="" class="card-img-top img-fluid" style="width: 100%; height: auto;">
                            <div class="card-body">
                                <a target="_blank" href="<?php echo base_url('assets/frontend/image/products/product-image-3.png') ?>">View Image</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="bg-catskillwhite py-lg-25 py-md-19 py-15">
            <div class="container">
                <div class="row align-items-center justify-content-center">
                    <div class="col-lg-6 col-md-7" data-aos="fade-right" data-aos-duration="800" data-aos-once="true">
                        <div class="content pr-xl-10">
                            <h2 class="text-cod-gray font-size-10 heading-letter-spacing-3 mb-8 pr-xl-18 pr-md-0 pr-sm-15"><span>BUTIK ANTAM</span><br>
                                <span>Logam Mulia</span>
                            </h2>
                            <p class="font-size-5 text-boulder mb-0">
                                JAKARTA GARDEN CITY<br>
                                Jl. Asya Boulevard No. 9A<br>
                                Cakung Timur, Cakung, Jakarta Timur,
                                DKI Jakarta 13110
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-5 mt-9 mt-md-0" data-aos="fade-left" data-aos-duration="800" data-aos-once="true">
                        <div id="carousel" class="carousel slide" data-ride="carousel">
                            <div class="carousel-inner">
                                <div class="carousel-item active">
                                    <img class="d-block w-100 rounded-10" src="<?php echo base_url('assets/frontend/image/products/butik-antam-1.png') ?>" alt="Product Image 1">
                                </div>
                                <div class="carousel-item">
                                    <img class="d-block w-100 rounded-10" src="<?php echo base_url('assets/frontend/image/products/butik-antam-2.png') ?>" alt="Product Image 2">
                                </div>
                                <div class="carousel-item">
                                    <img class="d-block w-100 rounded-10" src="<?php echo base_url('assets/frontend/image/products/butik-antam-3.png') ?>" alt="Product Image 3">
                                </div>
                                <div class="carousel-item">
                                    <img class="d-block w-100 rounded-10" src="<?php echo base_url('assets/frontend/image/products/butik-antam-5.png') ?>" alt="Product Image 5">
                                </div>
                                <div class="carousel-item">
                                    <img class="d-block w-100 rounded-10" src="<?php echo base_url('assets/frontend/image/products/butik-antam-6.png') ?>" alt="Product Image 6">
                                </div>
                                <div class="carousel-item">
                                    <img class="d-block w-100 rounded-10" src="<?php echo base_url('assets/frontend/image/products/butik-antam-7.png') ?>" alt="Product Image 7">
                                </div>
                                <div class="carousel-item">
                                    <img class="d-block w-100 rounded-10" src="<?php echo base_url('assets/frontend/image/products/butik-antam-8.png') ?>" alt="Product Image 8">
                                </div>
                                <div class="carousel-item">
                                    <img class="d-block w-100 rounded-10" src="<?php echo base_url('assets/frontend/image/products/butik-antam-9.png') ?>" alt="Product Image 9">
                                </div>
                            </div>
                            <a class="carousel-control-prev" href="#carousel" role="button" data-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="sr-only">Previous</span>
                            </a>
                            <a class="carousel-control-next" href="#carousel" role="button" data-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="sr-only">Next</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            $(document).ready(function() {
                $('#carousel').carousel();
            });
        </script>

        <footer class="bg-white pt-lg-23 pb-lg-11 pt-md-19 pt-15 pb-9">
            <div class="container">
                <div class="row mb-n11 justify-content-center justify-content-md-start">
                    <div class="col-xl-4 col-lg-4 col-md-10 col-xs-10 mb-11">
                        <a href="#"><img class="mb-7" style="width: 200px;" src="<?php echo base_url('assets/logo.png') ?>" alt="image"></a>
                        <p class="text-boulder font-size-2 line-height-1p6 mb-0 pr-xl-20 pr-lg-10 pr-md-32">
                            JAKARTA GARDEN CITY<br>
                            Jl. Asya Boulevard No. 9A<br>
                            Cakung Timur, Cakung, Jakarta Timur<br>
                            DKI Jakarta 13110
                        </p>
                        <div class="l4-social-icon mx-n5 mt-9">
                            <a class="text-default-color-3 mx-5" href="#"><i class="fab fa-facebook-f"></i></a>
                            <a class="text-default-color-3 mx-5" href="#"><i class="fab fa-google"></i></a>
                            <a class="text-default-color-3 mx-5" href="#"><i class="fab fa-instagram"></i></a>
                        </div>
                    </div>
                    <div class="col-xl-2 col-lg-2 col-md-3 col-xs-5 mb-11">
                        <h3 class="font-family-1 text-boulder font-size-2 line-height-1p6 font-weight-normal mb-7">Sitemap</h3>
                        <ul class="list-unstyled mb-n5">
                            <li class="mb-5"><a class="text-cod-gray font-size-2 line-height-1p6" href="<?php echo site_url('login') ?>">Member Area</a></li>
                            <li class="mb-5"><a class="text-cod-gray font-size-2 line-height-1p6" href="<?php echo site_url('signup') ?>">Daftar</a></li>
                        </ul>
                    </div>
                    <div class="col-xl-3 col-lg-2 col-md-3 col-xs-5  mb-11 pl-xl-14 pl-lg-8">
                        <h3 class="font-family-1 text-boulder font-size-2 font-weight-normal line-height-1p6 mb-7">Official hour</h3>
                        <ul class="list-unstyled mb-n5">
                            <li class="mb-5 text-cod-gray font-size-2 line-height-1p6">Senin - Sabtu Pukul 08.00 - 17.00</a></li>
                        </ul>
                    </div>
                    <div class="col-xl-3 col-lg-4 col-md-6 col-xs-10  mb-11">
                        <h3 class="font-family-1 text-boulder font-size-2 line-height-1p6 font-weight-normal mb-7">Contact Us</h3>
                        <div class="text-cod-gray font-size-2 line-height-1p6 mb-8 pr-xl-3 pr-lg-12 pr-md-20 pr-4">
                            <a href="https://628">WhatsApp</a><br>
                            <a href="https://instagram.com/butik_antam">Instagram</a><br>
                            <a href="https://facebook.com/butik_antam">Facebook</a><br>
                        </div>
                        <div class="contact-form position-relative">
                            <form action="#">
                                <input type="email" name="email" id="email-2" class="form-control text-cod-gray font-size-2 line-height-1p6 pr-12" placeholder="Your message">
                                <div class="send-icon">
                                    <i class="fas fa-paper-plane text-red-orange font-size-5"></i>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="mt-lg-19 mb-lg-11 mt-md-19 mt-15 mb-9 border-top border-nobel border-width-1 opacity-2"></div>
                    </div>
                    <div class="col-lg-12">
                        <div class="text-center">
                            <p class="font-size-2 line-height-1p6 text-gray mb-0">Copyright © 2024 Butik Antam</p>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>
    <script src="<?php echo base_url('assets/frontend/js/vendor.min.js') ?>"></script>
    <script src="<?php echo base_url('assets/frontend/plugins/fancybox/jquery.fancybox.min.js') ?>"></script>
    <script src="<?php echo base_url('assets/frontend/plugins/aos/aos.min.js') ?>"></script>
    <script src="<?php echo base_url('assets/frontend/plugins/slick/slick.min.js') ?>"></script>
    <script src="<?php echo base_url('assets/frontend/plugins/counter-up/waypoint.js') ?>"></script>
    <script src="<?php echo base_url('assets/frontend/plugins/counter-up/jquery.counterup.min.js') ?>"></script>
    <script src="<?php echo base_url('assets/frontend/js/custom.js') ?>"></script>
</body>

</html>