<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
    @yield('title', 'AITaxiNoiBai - Dịch vụ taxi chuyên nghiệp từ Hà Nội - Sân bay Nội Bài')
    </title>
    <meta name="description" content="@yield('description', 'Dịch vụ taxi chuyên nghiệp từ Hà Nội đến sân bay Nội Bài và ngược lại. Đặt xe nhanh chóng, an toàn, giá cả hợp lý.')">
    <meta name="keywords" content="@yield('keywords', 'taxi, Hà Nội, Nội Bài, đặt xe, dịch vụ taxi')">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    @yield('css')

    <style>

        a {
            text-decoration: none!important;
            color: inherit;
        }
        .taxi-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 2px solid #e9ecef;
            cursor: pointer;
        }
        .taxi-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            border-color: #007bff;
        }
        .price-tag {
            background: linear-gradient(45deg, #28a745, #20c997);
            color: white;
            font-weight: bold;
            font-size: 1.2em;
        }
        .booking-form {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            color: white;
        }
        .form-control:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        .btn-custom {
            background: #007bff;
            border: none;
            font-weight: bold;
            color: white;
        }
        .btn-custom:hover {
            background: #0056b3;
            transform: translateY(-2px);
            color: white;
        }
        .nav-tabs .nav-link {
            border: none;
            color: #6c757d;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            position: relative;
        }
        .nav-tabs .nav-link.active {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            border-radius: 0;
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.4);
            transform: translateY(-2px);
        }
        .nav-tabs .nav-link:hover {
            border: none;
            background: rgba(0, 123, 255, 0.1);
            color: #007bff;
        }
        .night-option {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            position: relative;
            overflow: hidden;
        }
        .night-option::before {
            content: '✨🌙';
            position: absolute;
            top: 8px;
            right: 10px;
            font-size: 1rem;
            opacity: 0.8;
            animation: twinkle 2s infinite;
        }
        @keyframes twinkle {
            0%, 100% { opacity: 0.8; }
            50% { opacity: 0.4; }
        }
        .night-option .fa-moon {
            color: #f8f9fa !important;
            text-shadow: 0 0 10px rgba(248, 249, 250, 0.5);
        }
        .day-option .fa-sun {
            color: #ffc107 !important;
            text-shadow: 0 0 10px rgba(255, 193, 7, 0.3);
        }
        .nav-pills .nav-link {
            border-radius: 25px;
            margin: 0 5px;
            transition: all 0.3s ease;
            background: rgba(108, 117, 125, 0.1);
            color: #6c757d;
            font-weight: 600;
        }
        .nav-pills .nav-link.active {
            background: #007bff;
            color: white;
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
            transform: translateY(-2px);
        }
        .nav-pills .nav-link:hover {
            background: rgba(0, 123, 255, 0.2);
            color: #007bff;
            transform: translateY(-1px);
        }
        .night-option .price-tag {
            background: #6c757d;
        }
        .night-option .btn-custom {
            background: #007bff;
        }
        .night-option .btn-custom:hover {
            background: #0056b3;
        }
        .day-option {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            color: #333;
        }
        @media (max-width: 768px) {
            .nav-tabs .nav-link {
                font-size: 0.9rem;
                padding: 0.75rem 0.5rem;
            }
            .taxi-card {
                margin-bottom: 1rem;
            }
            .container {
                padding-left: 1rem;
                padding-right: 1rem;
            }
        }
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
            background: linear-gradient(45deg, #007bff, #28a745);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .navbar {
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .nav-link {
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .nav-link:hover {
            color: #007bff !important;
            transform: translateY(-1px);
        }
        .hover-primary:hover {
            color: #007bff !important;
            transform: scale(1.2);
            transition: all 0.3s ease;
        }
        .price-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        .original-price {
            text-decoration: line-through;
            color: #dc3545;
            font-size: 0.9rem;
        }
        .discounted-price {
            color: #dc3545;
            font-weight: bold;
            font-size: 1.1rem;
        }
        .discount-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #dc3545;
            color: white;
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-weight: bold;
        }
        .floating-btn {
            position: fixed;
            left: 30px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            transition: all 0.3s ease;
            text-decoration: none;
            animation: pulse 2s infinite;
        }
        .floating-call-btn {
            bottom: 30px;
            background: linear-gradient(45deg, #28a745, #20c997);
            box-shadow: 0 4px 20px rgba(40, 167, 69, 0.4);
        }
        .floating-call-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 25px rgba(40, 167, 69, 0.6);
            background: linear-gradient(45deg, #20c997, #17a2b8);
        }
        .floating-zalo-btn {
            bottom: 100px;
            background: linear-gradient(45deg, #0068ff, #0091ff);
            box-shadow: 0 4px 20px rgba(0, 104, 255, 0.4);
        }
        .floating-zalo-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 25px rgba(0, 104, 255, 0.6);
            background: linear-gradient(45deg, #0091ff, #00a8ff);
        }
        .floating-btn i {
            color: white;
            font-size: 1.5rem;
        }
        @keyframes pulse {
            0% { box-shadow: 0 4px 20px rgba(40, 167, 69, 0.4), 0 0 0 0 rgba(40, 167, 69, 0.7); }
            70% { box-shadow: 0 4px 20px rgba(40, 167, 69, 0.4), 0 0 0 10px rgba(40, 167, 69, 0); }
            100% { box-shadow: 0 4px 20px rgba(40, 167, 69, 0.4), 0 0 0 0 rgba(40, 167, 69, 0); }
        }
        @keyframes pulseZalo {
            0% { box-shadow: 0 4px 20px rgba(0, 104, 255, 0.4), 0 0 0 0 rgba(0, 104, 255, 0.7); }
            70% { box-shadow: 0 4px 20px rgba(0, 104, 255, 0.4), 0 0 0 10px rgba(0, 104, 255, 0); }
            100% { box-shadow: 0 4px 20px rgba(0, 104, 255, 0.4), 0 0 0 0 rgba(0, 104, 255, 0); }
        }
        .floating-zalo-btn {
            animation: pulseZalo 2s infinite;
        }
        @media (max-width: 768px) {
            .floating-btn {
                left: 20px;
                width: 55px;
                height: 55px;
            }
            .floating-call-btn {
                bottom: 20px;
            }
            .floating-zalo-btn {
                bottom: 85px;
            }
            .floating-btn i {
                font-size: 1.3rem;
            }
        }
    </style>


</head>
<body class="bg-light">

<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-RWC1N1J0HG"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-RWC1N1J0HG');
</script>

<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top">
    <div class="container">
        <a class="navbar-brand" href="/">
            <i class="fas fa-taxi me-2"></i>AITaxiNoiBai
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="#home">
                        <i class="fas fa-home me-1"></i>Trang chủ
                    </a>
                </li>
{{--                <li class="nav-item">--}}
{{--                    <a class="nav-link" href="#about">--}}
{{--                        <i class="fas fa-info-circle me-1"></i>Giới thiệu--}}
{{--                    </a>--}}
{{--                </li>--}}
                <li class="nav-item">
                    <a class="nav-link" href="#" onclick="openPricingModal()">
                        <i class="fas fa-tags me-1"></i>Bảng giá
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/tin-tuc">
                        <i class="fas fa-file-contract me-1"></i>Tin tức
                    </a>
                </li>
{{--                <li class="nav-item">--}}
{{--                    <a class="nav-link" href="#terms">--}}
{{--                        <i class="fas fa-file-contract me-1"></i>Điều khoản--}}
{{--                    </a>--}}
{{--                </li>--}}
                <li class="nav-item">
                    <a class="nav-link btn btn-outline-primary ms-2 px-3" href="#booking" onclick="openPricingModal()">
                        <i class="fas fa-phone me-1"></i>Đặt xe ngay
                    </a>
                </li>
                <?php
                if(isAdminACP_()){
                    ?>
                <li class="nav-item">
                    <a class="nav-link btn btn-outline-primary ms-2 px-3" href="/admin">
                        <i class="fa fa-user"></i> ADMIN
                    </a>
                </li>
                <?php
                }
                ?>
            </ul>
        </div>
    </div>
</nav>


@yield('content')



<!-- Footer -->
<footer class="bg-dark text-light py-5">
    <div class="container">
        <div class="row g-4">
            <!-- Cột 1: Thông tin công ty -->
            <div class="col-lg-4 col-md-6">
                <h5 class="fw-bold mb-3">
                    <i class="fas fa-taxi me-2 text-primary"></i>AITaxiNoiBai
                </h5>
                <p class="text-muted">
                    Dịch vụ taxi chuyên nghiệp từ Hà Nội đến sân bay Nội Bài và ngược lại.
                    Đảm bảo an toàn, đúng giờ, giá cả hợp lý.
                </p>
                <div class="d-flex gap-3">
                    <a href="#" class="text-light fs-4 hover-primary">
                        <i class="fab fa-facebook"></i>
                    </a>
                    <a href="#" class="text-light fs-4 hover-primary">
                        <i class="fab fa-youtube"></i>
                    </a>
                    <a href="#" class="text-light fs-4 hover-primary">
                        <i class="fab fa-tiktok"></i>
                    </a>
                    <a href="#" class="text-light fs-4 hover-primary">
                        <i class="fab fa-zalo"></i>
                    </a>
                </div>
            </div>

            <!-- Cột 2: Liên hệ -->
            <div class="col-lg-4 col-md-6">
                <h5 class="fw-bold mb-3">
                    <i class="fas fa-phone me-2 text-success"></i>Liên Hệ
                </h5>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="fas fa-phone-alt me-2 text-primary"></i>
                        <strong>Hotline:</strong>
                        <a href="tel:0974594945" class="text-decoration-none text-light">0974.594.945</a>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-envelope me-2 text-primary"></i>
                        <strong>Email:</strong>
                        <a href="mailto:info@aitaxinoibai.com" class="text-decoration-none text-light">info@aitaxinoibai.com</a>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-map-marker-alt me-2 text-primary"></i>
                        <strong>Địa chỉ:</strong> CT3C Khu đô thị Nam Cường, Cổ Nhuế, Hà Nội
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-clock me-2 text-primary"></i>
                        <strong>Hoạt động:</strong> 24/7 - Mọi lúc mọi nơi
                    </li>
                </ul>
            </div>

            <!-- Cột 3: Dịch vụ -->
            <div class="col-lg-4 col-md-12">
                <h5 class="fw-bold mb-3">
                    <i class="fas fa-car me-2 text-warning"></i>Dịch Vụ
                </h5>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="fas fa-plane me-2 text-info"></i>
                        <a href="#booking" class="text-decoration-none text-light">Taxi Hà Nội - Nội Bài</a>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-plane-arrival me-2 text-info"></i>
                        <a href="#booking" class="text-decoration-none text-light">Taxi Nội Bài - Hà Nội</a>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-car me-2 text-info"></i>
                        <a href="#" class="text-decoration-none text-light">Thuê xe theo giờ</a>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-route me-2 text-info"></i>
                        <a href="#" class="text-decoration-none text-light">Taxi liên tỉnh</a>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-users me-2 text-info"></i>
                        <a href="#" class="text-decoration-none text-light">Dịch vụ doanh nghiệp</a>
                    </li>
                </ul>

                <!-- Quick booking button -->
                <div class="mt-4">
                    <a href="#booking" class="btn btn-primary btn-lg w-100">
                        <i class="fas fa-phone me-2"></i>Đặt Xe Ngay
                    </a>
                </div>
            </div>
        </div>

        <!-- Copyright -->
        <hr class="my-4 border-secondary">
        <div class="row align-items-center">
            <div class="col-md-6">
                <p class="mb-0 text-muted">
                    © 2024 AITaxiNoiBai. All rights reserved.
                </p>
            </div>
            <div class="col-md-6 text-md-end">
                <div class="d-flex justify-content-md-end gap-3">
                    <a href="#terms" class="text-decoration-none text-muted">Điều khoản</a>
                    <a href="#" class="text-decoration-none text-muted">Chính sách bảo mật</a>
                    <a href="#" class="text-decoration-none text-muted">Hỗ trợ</a>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Floating Buttons -->
<a href="https://zalo.me/0974594945" target="_blank" class="floating-btn floating-zalo-btn" title="Chat Zalo: 0974594945">
    <img src="/images/icon/zalo-svg.svg" style='max-width: 65px; border: 1px solid white; border-radius: 50% ' alt="">
</a>

<a href="tel:0974594945" class="floating-btn floating-call-btn" title="Gọi ngay: 0974594945">
    <i class="fas fa-phone-alt"></i>
</a>

<!-- Pricing Modal -->
<div class="modal fade" id="pricingModal" tabindex="-1" aria-labelledby="pricingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="pricingModalLabel">
                    <i class="fas fa-tags me-2"></i>Bảng Giá Taxi Hà Nội - Nội Bài
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="table-dark">
                        <tr>

                            <th scope="col">Hướng đi</th>
                            <th scope="col">Loại xe</th>
                            <th scope="col">Khung giờ</th>
                            <th scope="col" class="">Giá cước</th>
                            <th scope="col" class="text-center">Hành động</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>

                            <td>
                                <i class="fas fa-plane-departure me-2 text-primary"></i>
                                Hà Nội - Nội Bài                            </td>
                            <td>
                                <i class="fas fa-car me-2 text-info"></i>
                                <br>
                                5 chỗ                            </td>
                            <td>
                                <i class="fas fa-sun me-2 text-warning"></i>
                                Giờ ngày (06:30 - 22:00)                            </td>
                            <td class="text-end">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <small class="text-decoration-line-through text-muted">200k VNĐ</small><br>
                                        <strong class="text-danger">150k VNĐ</strong>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-warning btn-sm"
                                        onclick="bookFromPricing(1, 'Hà Nội - Nội Bài', '5 chỗ', 'Giờ ngày (06:30 - 22:00)', 150)">
                                    <i class="fas fa-taxi me-1"></i>Đặt xe
                                </button>
                            </td>
                        </tr>
                        <tr>

                            <td>
                                <i class="fas fa-plane-departure me-2 text-primary"></i>
                                Hà Nội - Nội Bài                            </td>
                            <td>
                                <i class="fas fa-car me-2 text-info"></i>
                                <br>
                                5 chỗ                            </td>
                            <td>
                                <i class="fas fa-moon me-2 text-warning"></i>
                                Giờ đêm (22:00 - 06:30)                            </td>
                            <td class="text-end">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <small class="text-decoration-line-through text-muted">250k VNĐ</small><br>
                                        <strong class="text-danger">200k VNĐ</strong>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-warning btn-sm"
                                        onclick="bookFromPricing(2, 'Hà Nội - Nội Bài', '5 chỗ', 'Giờ đêm (22:00 - 06:30)', 200)">
                                    <i class="fas fa-taxi me-1"></i>Đặt xe
                                </button>
                            </td>
                        </tr>
                        <tr>

                            <td>
                                <i class="fas fa-plane-departure me-2 text-primary"></i>
                                Hà Nội - Nội Bài                            </td>
                            <td>
                                <i class="fas fa-car me-2 text-info"></i>
                                <br>
                                7 chỗ                            </td>
                            <td>
                                <i class="fas fa-sun me-2 text-warning"></i>
                                Giờ ngày (06:30 - 22:00)                            </td>
                            <td class="text-end">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <small class="text-decoration-line-through text-muted">250k VNĐ</small><br>
                                        <strong class="text-danger">200k VNĐ</strong>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-warning btn-sm"
                                        onclick="bookFromPricing(3, 'Hà Nội - Nội Bài', '7 chỗ', 'Giờ ngày (06:30 - 22:00)', 200)">
                                    <i class="fas fa-taxi me-1"></i>Đặt xe
                                </button>
                            </td>
                        </tr>
                        <tr>

                            <td>
                                <i class="fas fa-plane-departure me-2 text-primary"></i>
                                Hà Nội - Nội Bài                            </td>
                            <td>
                                <i class="fas fa-car me-2 text-info"></i>
                                <br>
                                7 chỗ                            </td>
                            <td>
                                <i class="fas fa-moon me-2 text-warning"></i>
                                Giờ đêm (22:00 - 06:30)                            </td>
                            <td class="text-end">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <small class="text-decoration-line-through text-muted">300k VNĐ</small><br>
                                        <strong class="text-danger">250k VNĐ</strong>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-warning btn-sm"
                                        onclick="bookFromPricing(4, 'Hà Nội - Nội Bài', '7 chỗ', 'Giờ đêm (22:00 - 06:30)', 250)">
                                    <i class="fas fa-taxi me-1"></i>Đặt xe
                                </button>
                            </td>
                        </tr>
                        <tr>

                            <td>
                                <i class="fas fa-plane-arrival me-2 text-primary"></i>
                                Nội Bài - Hà Nội                            </td>
                            <td>
                                <i class="fas fa-car me-2 text-info"></i>
                                <br>
                                5 chỗ                            </td>
                            <td>
                                <i class="fas fa-sun me-2 text-warning"></i>
                                Giờ ngày (06:30 - 22:00)                            </td>
                            <td class="text-end">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <small class="text-decoration-line-through text-muted">250k VNĐ</small><br>
                                        <strong class="text-danger">200k VNĐ</strong>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-warning btn-sm"
                                        onclick="bookFromPricing(5, 'Nội Bài - Hà Nội', '5 chỗ', 'Giờ ngày (06:30 - 22:00)', 200)">
                                    <i class="fas fa-taxi me-1"></i>Đặt xe
                                </button>
                            </td>
                        </tr>
                        <tr>

                            <td>
                                <i class="fas fa-plane-arrival me-2 text-primary"></i>
                                Nội Bài - Hà Nội                            </td>
                            <td>
                                <i class="fas fa-car me-2 text-info"></i>
                                <br>
                                5 chỗ                            </td>
                            <td>
                                <i class="fas fa-moon me-2 text-warning"></i>
                                Giờ đêm (22:00 - 06:30)                            </td>
                            <td class="text-end">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <small class="text-decoration-line-through text-muted">300k VNĐ</small><br>
                                        <strong class="text-danger">250k VNĐ</strong>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-warning btn-sm"
                                        onclick="bookFromPricing(6, 'Nội Bài - Hà Nội', '5 chỗ', 'Giờ đêm (22:00 - 06:30)', 250)">
                                    <i class="fas fa-taxi me-1"></i>Đặt xe
                                </button>
                            </td>
                        </tr>
                        <tr>

                            <td>
                                <i class="fas fa-plane-arrival me-2 text-primary"></i>
                                Nội Bài - Hà Nội                            </td>
                            <td>
                                <i class="fas fa-car me-2 text-info"></i>
                                <br>
                                7 chỗ                            </td>
                            <td>
                                <i class="fas fa-sun me-2 text-warning"></i>
                                Giờ ngày (06:30 - 22:00)                            </td>
                            <td class="text-end">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <small class="text-decoration-line-through text-muted">300k VNĐ</small><br>
                                        <strong class="text-danger">250k VNĐ</strong>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-warning btn-sm"
                                        onclick="bookFromPricing(7, 'Nội Bài - Hà Nội', '7 chỗ', 'Giờ ngày (06:30 - 22:00)', 250)">
                                    <i class="fas fa-taxi me-1"></i>Đặt xe
                                </button>
                            </td>
                        </tr>
                        <tr>

                            <td>
                                <i class="fas fa-plane-arrival me-2 text-primary"></i>
                                Nội Bài - Hà Nội                            </td>
                            <td>
                                <i class="fas fa-car me-2 text-info"></i>
                                <br>
                                7 chỗ                            </td>
                            <td>
                                <i class="fas fa-moon me-2 text-warning"></i>
                                Giờ đêm (22:00 - 06:30)                            </td>
                            <td class="text-end">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <small class="text-decoration-line-through text-muted">350k VNĐ</small><br>
                                        <strong class="text-danger">300k VNĐ</strong>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-warning btn-sm"
                                        onclick="bookFromPricing(8, 'Nội Bài - Hà Nội', '7 chỗ', 'Giờ đêm (22:00 - 06:30)', 300)">
                                    <i class="fas fa-taxi me-1"></i>Đặt xe
                                </button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <div class="d-flex align-items-center justify-content-between w-100">
                    <div class="d-flex align-items-center text-muted">
                        <i class="fas fa-info-circle me-2"></i>
                        <small>Giá đã bao gồm VAT. Chọn "Đặt xe" ở hàng tương ứng để đặt xe ngay.</small>
                    </div>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Đóng
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places&callback=initMap"></script>
<script>
    function selectOption(id, direction, carType, timePeriod, price) {
        // Hiển thị form đặt xe
        document.getElementById('bookingSection').classList.remove('d-none');

        // Kiểm tra discount từ PHP (discount = 0 theo user đã thay đổi)
        const discount = 50;

        // Cập nhật thông tin lựa chọn
        document.getElementById('selectedOptionId').value = id;
        document.getElementById('selectedOptionText').textContent =
            `${direction} - ${carType} - ${timePeriod}`;

        // Cập nhật hiển thị giá dựa vào discount
        const priceContainer = document.getElementById('selectedPriceContainer');
        if (discount > 0) {
            const originalPrice = price + discount;
            priceContainer.innerHTML = `
                <div class="price-container justify-content-end">
                    <div class="original-price">${originalPrice.toLocaleString()}k VNĐ</div>
                    <h4 class="mb-0 discounted-price">${price.toLocaleString()}k VNĐ</h4>
                </div>
            `;
        } else {
            priceContainer.innerHTML = `<h4 class="text-success mb-0">${price.toLocaleString()}k VNĐ</h4>`;
        }

        // Cập nhật label và placeholder dựa trên hướng đi
        const locationLabel = document.getElementById('locationLabel');
        const locationInput = document.getElementById('pickupLocation');

        if (direction.includes('Hà Nội → Nội Bài')) {
            locationLabel.innerHTML = '<i class="fas fa-map-marker-alt me-2"></i>Điểm đón';
            locationInput.placeholder = 'Nhập địa chỉ điểm đón tại Hà Nội...';
            locationInput.name = 'pickup_location';
        } else {
            locationLabel.innerHTML = '<i class="fas fa-map-marker-alt me-2"></i>Điểm đến';
            locationInput.placeholder = 'Nhập địa chỉ điểm đến tại Hà Nội...';
            locationInput.name = 'destination_location';
        }

        // Scroll to form
        document.getElementById('bookingSection').scrollIntoView({
            behavior: 'smooth'
        });

        // Set default date to today
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('pickupDate').value = today;

        // Set default time
        const now = new Date();
        const timeString = now.getHours().toString().padStart(2, '0') + ':' +
            now.getMinutes().toString().padStart(2, '0');
        document.getElementById('pickupTime').value = timeString;
    }

    function cancelBooking() {
        document.getElementById('bookingSection').classList.add('d-none');
        document.getElementById('bookingForm').reset();
    }

    // Phone number validation
    document.getElementById('phoneNumber').addEventListener('input', function(e) {
        const phoneRegex = /^(0|\+84)[3|5|7|8|9][0-9]{8}$/;
        const isValid = phoneRegex.test(e.target.value);

        if (e.target.value && !isValid) {
            e.target.classList.add('is-invalid');
        } else {
            e.target.classList.remove('is-invalid');
        }
    });

    // Form submission
    document.getElementById('bookingForm').addEventListener('submit', function(e) {
        e.preventDefault();

        // Validate phone number
        const phoneInput = document.getElementById('phoneNumber');
        const phoneRegex = /^(0|\+84)[3|5|7|8|9][0-9]{8}$/;

        if (!phoneRegex.test(phoneInput.value)) {
            phoneInput.classList.add('is-invalid');
            phoneInput.focus();
            return;
        }

        // Collect form data
        const locationInput = document.getElementById('pickupLocation');
        const formData = {
            option_id: document.getElementById('selectedOptionId').value,
            customer_name: document.getElementById('customerName').value,
            phone_number: document.getElementById('phoneNumber').value,
            pickup_date: document.getElementById('pickupDate').value,
            pickup_time: document.getElementById('pickupTime').value,
            location: locationInput.value,
            location_type: locationInput.name, // pickup_location hoặc destination_location
            notes: document.getElementById('notes').value
        };

        console.log('Booking data:', formData);

        // Show success message
        alert('Đặt xe thành công! Chúng tôi sẽ liên hệ với bạn sớm nhất.');

        // Reset form
        this.reset();
        cancelBooking();
    });

    // Function để mở modal bảng giá
    function openPricingModal() {
        const modal = new bootstrap.Modal(document.getElementById('pricingModal'));
        modal.show();
    }

    // Function để hiển thị popup liên hệ cho nút "Gọi ngay"
    function showContactOptions(direction, carType, timePeriod, price) {
        // Tạo modal popup với 2 nút
        const contactModal = `
            <div class="modal fade" id="contactModal" tabindex="-1" aria-labelledby="contactModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title" id="contactModalLabel">
                                <i class="fas fa-taxi me-2"></i>Đặt xe: ${direction} - ${carType}
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center p-4">
                            <div class="mb-3">
                                <h6 class="text-muted">${timePeriod}</h6>
                                <h4 class="text-success fw-bold">${price.toLocaleString()}k VNĐ</h4>
                            </div>
                            <p class="text-muted mb-4">Chọn cách liên hệ để đặt xe:</p>

                            <div class="row g-3">
                                <div class="col-6">
                                    <a href="tel:0974594945" class="btn btn-success btn-lg w-100 text-decoration-none">
                                        <i class="fas fa-phone mb-2 d-block fs-2"></i>
                                        <div class="fw-bold">Gọi ngay</div>
                                        <small>0974.594.945</small>
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="https://zalo.me/0974594945" target="_blank" class="btn btn-primary btn-lg w-100 text-decoration-none">
                                        <i class="fab fa-facebook-messenger mb-2 d-block fs-2"></i>
                                        <div class="fw-bold">Chat Zalo</div>
                                        <small>Gọi ngay</small>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer justify-content-center">
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>Hỗ trợ 24/7 - Phản hồi nhanh chóng
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Remove existing contact modal if any
        const existingContactModal = document.getElementById('contactModal');
        if (existingContactModal) {
            existingContactModal.remove();
        }

        // Add modal to body
        document.body.insertAdjacentHTML('beforeend', contactModal);

        // Show contact modal
        const modal = new bootstrap.Modal(document.getElementById('contactModal'));
        modal.show();
    }

    // Function để đặt xe từ bảng giá
    function bookFromPricing(id, direction, carType, timePeriod, price) {
        // Đóng modal bảng giá trước
        const pricingModal = bootstrap.Modal.getInstance(document.getElementById('pricingModal'));
        pricingModal.hide();

        // Hiện popup liên hệ sau khi modal bảng giá đóng
        pricingModal._element.addEventListener('hidden.bs.modal', function() {
            showContactOptions(direction, carType, timePeriod, price);
        }, { once: true });
    }

    // Function để mở bản đồ chọn địa điểm
    function openMap() {
        // Kiểm tra hướng đi để hiển thị đúng title
        const locationLabel = document.getElementById('locationLabel').textContent;
        const title = locationLabel.includes('Điểm đến') ? 'Chọn điểm đến' : 'Chọn điểm đón';

        // Tạo modal với Google Maps thật
        const mapModal = `
                    <div class="modal fade" id="mapModal" tabindex="-1">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">${title}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body p-0">
                                    <div class="row g-0">
                                        <div class="col-md-4 bg-light p-3">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Tìm kiếm địa điểm:</label>
                                                <input type="text" class="form-control" id="mapSearchInput"
                                                    placeholder="Nhập địa chỉ hoặc địa điểm...">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Địa điểm phổ biến:</label>
                                                <div class="list-group" id="locationSuggestions">
                                                    <button type="button" class="list-group-item list-group-item-action py-2" onclick="selectLocation('Sân bay Nội Bài, Hà Nội')">
                                                        <i class="fas fa-plane me-2 text-primary"></i>Sân bay Nội Bài
                                                    </button>
                                                    <button type="button" class="list-group-item list-group-item-action py-2" onclick="selectLocation('Ga Hà Nội, Hoàn Kiếm, Hà Nội')">
                                                        <i class="fas fa-train me-2 text-success"></i>Ga Hà Nội
                                                    </button>
                                                    <button type="button" class="list-group-item list-group-item-action py-2" onclick="selectLocation('Hồ Gươm, Hoàn Kiếm, Hà Nội')">
                                                        <i class="fas fa-water me-2 text-info"></i>Hồ Gươm
                                                    </button>
                                                    <button type="button" class="list-group-item list-group-item-action py-2" onclick="selectLocation('Phố cổ Hà Nội')">
                                                        <i class="fas fa-building me-2 text-warning"></i>Phố cổ Hà Nội
                                                    </button>
                                                    <button type="button" class="list-group-item list-group-item-action py-2" onclick="selectLocation('Mỹ Đình, Nam Từ Liêm, Hà Nội')">
                                                        <i class="fas fa-home me-2 text-danger"></i>Mỹ Đình
                                                    </button>
                                                    <button type="button" class="list-group-item list-group-item-action py-2" onclick="selectLocation('Cầu Giấy, Hà Nội')">
                                                        <i class="fas fa-road me-2 text-secondary"></i>Cầu Giấy
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="d-grid">
                                                <button type="button" class="btn btn-primary" onclick="confirmMapSelection()">
                                                    <i class="fas fa-check me-2"></i>Xác nhận địa điểm
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div id="googleMap" style="height: 500px; width: 100%;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

        // Remove existing modal if any
        const existingModal = document.getElementById('mapModal');
        if (existingModal) {
            existingModal.remove();
        }

        // Add modal to body
        document.body.insertAdjacentHTML('beforeend', mapModal);

        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('mapModal'));
        modal.show();

        // Initialize Google Map after modal is shown
        modal._element.addEventListener('shown.bs.modal', function() {
            initializeMapModal();
        });
    }

    // Google Maps variables
    let map;
    let marker;
    let autocomplete;
    let selectedLocation = null;

    // Initialize Google Maps
    function initMap() {
        // Map sẽ được khởi tạo khi modal mở
    }

    function initializeMapModal() {
        // Tọa độ mặc định (Hà Nội)
        const hanoi = { lat: 21.0285, lng: 105.8542 };

        // Tạo map
        map = new google.maps.Map(document.getElementById('googleMap'), {
            zoom: 13,
            center: hanoi,
            mapTypeControl: false,
            fullscreenControl: false,
            streetViewControl: false,
        });

        // Tạo marker
        marker = new google.maps.Marker({
            position: hanoi,
            map: map,
            draggable: true,
            title: "Chọn địa điểm"
        });

        // Xử lý click trên map
        map.addListener('click', function(event) {
            const clickedLocation = event.latLng;
            marker.setPosition(clickedLocation);

            // Reverse geocoding để lấy địa chỉ
            const geocoder = new google.maps.Geocoder();
            geocoder.geocode({ location: clickedLocation }, function(results, status) {
                if (status === 'OK' && results[0]) {
                    selectedLocation = {
                        address: results[0].formatted_address,
                        lat: clickedLocation.lat(),
                        lng: clickedLocation.lng()
                    };

                    // Cập nhật search input
                    document.getElementById('mapSearchInput').value = selectedLocation.address;
                }
            });
        });

        // Xử lý drag marker
        marker.addListener('dragend', function() {
            const position = marker.getPosition();

            // Reverse geocoding
            const geocoder = new google.maps.Geocoder();
            geocoder.geocode({ location: position }, function(results, status) {
                if (status === 'OK' && results[0]) {
                    selectedLocation = {
                        address: results[0].formatted_address,
                        lat: position.lat(),
                        lng: position.lng()
                    };

                    document.getElementById('mapSearchInput').value = selectedLocation.address;
                }
            });
        });

        // Setup autocomplete cho search input
        const searchInput = document.getElementById('mapSearchInput');
        autocomplete = new google.maps.places.Autocomplete(searchInput, {
            bounds: new google.maps.LatLngBounds(
                new google.maps.LatLng(20.8, 105.5), // SW
                new google.maps.LatLng(21.3, 106.1)  // NE
            ),
            strictBounds: true,
            componentRestrictions: { country: 'vn' }
        });

        // Xử lý khi chọn từ autocomplete
        autocomplete.addListener('place_changed', function() {
            const place = autocomplete.getPlace();

            if (place.geometry) {
                selectedLocation = {
                    address: place.formatted_address || place.name,
                    lat: place.geometry.location.lat(),
                    lng: place.geometry.location.lng()
                };

                // Di chuyển map và marker
                map.setCenter(place.geometry.location);
                marker.setPosition(place.geometry.location);

                if (place.geometry.viewport) {
                    map.fitBounds(place.geometry.viewport);
                } else {
                    map.setZoom(17);
                }
            }
        });
    }

    function selectLocation(location) {
        selectedLocation = { address: location };
        document.getElementById('mapSearchInput').value = location;

        // Tìm vị trí trên map nếu có
        if (map && typeof google !== 'undefined') {
            const geocoder = new google.maps.Geocoder();
            geocoder.geocode({ address: location }, function(results, status) {
                if (status === 'OK' && results[0]) {
                    const pos = results[0].geometry.location;
                    map.setCenter(pos);
                    marker.setPosition(pos);
                    selectedLocation.lat = pos.lat();
                    selectedLocation.lng = pos.lng();
                }
            });
        }
    }

    function confirmMapSelection() {
        if (selectedLocation && selectedLocation.address) {
            document.getElementById('pickupLocation').value = selectedLocation.address;

            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('mapModal'));
            modal.hide();
        } else {
            alert('Vui lòng chọn một địa điểm trên bản đồ hoặc từ danh sách!');
        }
    }

    // Smooth scrolling for navigation links
    document.addEventListener('DOMContentLoaded', function() {
        // Handle navigation clicks
        const navLinks = document.querySelectorAll('.navbar-nav .nav-link[href^="#"]');

        navLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                const href = this.getAttribute('href');

                // Special handling for sections that don't exist yet
                if (href === '#about' || href === '#pricing' || href === '#terms') {
                    e.preventDefault();
                    // alert(`Trang "${this.textContent.trim()}" đang được phát triển. Vui lòng quay lại sau!`);
                    return;
                }

                // Smooth scroll for existing sections
                if (href.startsWith('#')) {
                    e.preventDefault();
                    const target = document.querySelector(href);
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });

                        // Update active menu
                        navLinks.forEach(navLink => navLink.classList.remove('active'));
                        this.classList.add('active');

                        // Collapse mobile menu
                        const navbarCollapse = document.querySelector('.navbar-collapse');
                        if (navbarCollapse.classList.contains('show')) {
                            const bsCollapse = new bootstrap.Collapse(navbarCollapse);
                            bsCollapse.hide();
                        }
                    }
                }
            });
        });

        // Update active menu on scroll
        window.addEventListener('scroll', function() {
            const sections = ['home', 'booking'];
            const scrollPos = window.scrollY + 100;

            sections.forEach(section => {
                const element = document.getElementById(section);
                if (element) {
                    const offsetTop = element.offsetTop;
                    const offsetBottom = offsetTop + element.offsetHeight;

                    if (scrollPos >= offsetTop && scrollPos < offsetBottom) {
                        navLinks.forEach(link => link.classList.remove('active'));
                        const activeLink = document.querySelector(`.nav-link[href="#${section}"]`);
                        if (activeLink) {
                            activeLink.classList.add('active');
                        }
                    }
                }
            });
        });
    });
</script>
</body>
</html>
