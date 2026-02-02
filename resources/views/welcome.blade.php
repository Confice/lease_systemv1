<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome | LeaseEase x StoreEdge {{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">

    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* SNEAT THEME VARIABLES - UPDATED TO GREEN */
        :root {
            /* Changed from Purple (#696cff) to Green */
            --bs-primary: #6b7a56; 
            --bs-primary-hover: #6b7a56;
            
            --bs-body-bg: #efefea;
            --bs-font-sans-serif: "Public Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", "Oxygen", "Ubuntu", "Cantarell", "Fira Sans", "Droid Sans", "Helvetica Neue", sans-serif;
        }

        body {
            font-family: var(--bs-font-sans-serif);
            background-color: var(--bs-body-bg);
            color: #566a7f;
            padding-top: 70px; /* Space for fixed navbar */
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Helper to force Bootstrap text to use our variable */
        .text-primary {
            color: var(--bs-primary) !important;
        }

        /* NAVBAR STYLES */
        .navbar {
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 1px 15px rgba(0,0,0,0.04);
            height: 70px;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: #566a7f;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-link {
            font-weight: 500;
            color: #566a7f;
            margin: 0 10px;
        }

        .nav-link:hover, .nav-link.active {
            color: var(--bs-primary);
        }

        /* HERO SECTION */
        .hero-section {
            position: relative;
            /* Replace this URL with your actual image */
            background-image: url('https://images.unsplash.com/photo-1451187580459-43490279c0fa?q=80&w=2072&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
        }

        .hero-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(43, 44, 64, 0.7); /* Dark overlay */
        }

        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 800px;
            padding: 20px;
        }

        /* BUTTONS */
        .btn-primary {
            background-color: var(--bs-primary);
            border-color: var(--bs-primary);
            /* Updated shadow to match green (rgba of #28c76f) */
            box-shadow: 0 0.125rem 0.25rem 0 rgba(40, 199, 111, 0.4);
            font-weight: 500;
        }

        .btn-primary:hover {
            background-color: var(--bs-primary-hover) !important;
            border-color: var(--bs-primary-hover) !important;
            transform: translateY(-1px);
        }

        /* SECTION STYLING */
        section {
            padding: 80px 0;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 50px;
        }
        
        .section-title h2 {
            font-weight: 700;
            color: #566a7f;
            text-transform: uppercase;
            font-size: 1.5rem;
            letter-spacing: 1px;
        }
        
        .section-title p {
            color: #a1acb8;
        }

        /* FOOTER STYLING */
        footer {
            background-color: #2b2c40; /* Dark footer matching theme contrast */
            color: #a3a4cc;
            padding-top: 60px;
            padding-bottom: 20px;
        }

        footer h5 {
            color: #fff;
            font-weight: 600;
            margin-bottom: 20px;
        }

        footer a {
            color: #a3a4cc;
            text-decoration: none;
            transition: 0.3s;
        }

        footer a:hover {
            color: var(--bs-primary);
        }

        .social-icons a {
            font-size: 1.5rem;
            margin-right: 15px;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container-xxl">
            <a class="navbar-brand" href="#">
                <span class="app-brand-logo demo">
                    <img src="{{ asset('sneat/assets/img/favicon/favicon.ico') }}" alt="Logo" style="width: 30px; height: auto;">
                </span>
                LeaseEase x StoreEdge
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" href="#home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About Us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact Us</a>
                    </li>
                </ul>

                <div class="d-flex">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn btn-primary px-4">
                                <i class='bx bx-tachometer me-1'></i> Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary px-4">
                                <i class='bx bx-log-in me-1'></i> Login
                            </a>
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <header id="home" class="hero-section">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h1 class="display-3 fw-bold mb-4">Streamline Your Workflow</h1>
            <p class="lead mb-4 opacity-75">
                The most efficient way to manage your data and operations. Secure, fast, and reliable.
            </p>
            <div class="d-flex gap-3 justify-content-center">
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn btn-primary btn-lg">Go to Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary btn-lg">Get Started</a>
                @endauth
            </div>
        </div>
    </header>

    <section id="about" class="bg-white">
        <div class="container-xxl">
            <div class="section-title">
                <h2>About Us</h2>
                <p>Building the future of system management</p>
            </div>
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <img src="https://cdni.iconscout.com/illustration/premium/thumb/team-discussion-illustration-download-in-svg-png-gif-file-formats--meeting-brainstorming-business-people-pack-illustrations-4753068.png" alt="About" class="img-fluid">
                </div>
                <div class="col-lg-6">
                    <h3 class="fw-bold text-primary mb-3">Our Mission</h3>
                    <p class="mb-4">
                        We aim to provide a seamless experience for administrators and users alike. Our system incorporates state-of-the-art security features and an intuitive user interface inspired by the best modern design standards.
                    </p>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class='bx bx-shield-quarter text-primary fs-3 me-3'></i>
                                <div>
                                    <h6 class="mb-0 fw-bold">Secure</h6>
                                    <small>Enterprise grade</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class='bx bx-rocket text-primary fs-3 me-3'></i>
                                <div>
                                    <h6 class="mb-0 fw-bold">Fast</h6>
                                    <small>Optimized performance</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="contact" style="background-color: #f5f5f9;">
        <div class="container-xxl">
            <div class="section-title">
                <h2>Contact Us</h2>
                <p>We'd love to hear from you</p>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-5">
                            <form>
                                <div class="row mb-3">
                                    <div class="col-md-6 mb-3 mb-md-0">
                                        <label class="form-label">Full Name</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class='bx bx-user'></i></span>
                                            <input type="text" class="form-control" placeholder="John Doe">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Email Address</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class='bx bx-envelope'></i></span>
                                            <input type="email" class="form-control" placeholder="john@example.com">
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Message</label>
                                    <textarea class="form-control" rows="4" placeholder="How can we help you?"></textarea>
                                </div>
                                <button type="button" class="btn btn-primary w-100">Send Message</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <div class="container-xxl">
            <div class="row">
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <div class="d-flex align-items-center mb-3">
                        <i class='bx bxs-layer text-primary fs-3 me-2'></i>
                        <h4 class="mb-0 text-white fw-bold">{{ config('app.name', 'Laravel') }}</h4>
                    </div>
                    <p class="small">
                        A professional Laravel starter kit based on the Sneat admin template design system.
                    </p>
                    <div class="social-icons mt-3">
                        <a href="#"><i class='bx bxl-facebook'></i></a>
                        <a href="#"><i class='bx bxl-twitter'></i></a>
                        <a href="#"><i class='bx bxl-linkedin'></i></a>
                        <a href="#"><i class='bx bxl-github'></i></a>
                    </div>
                </div>

                <div class="col-lg-2 col-md-4 mb-4 mb-md-0">
                    <h5>Menu</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#home">Home</a></li>
                        <li class="mb-2"><a href="#about">About Us</a></li>
                        <li class="mb-2"><a href="#contact">Contact</a></li>
                        <li class="mb-2"><a href="{{ route('login') }}">Login</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-4 mb-4 mb-md-0">
                    <h5>Contact Info</h5>
                    <ul class="list-unstyled">
                        <li class="mb-3 d-flex text-white-50">
                            <i class='bx bx-map me-2 mt-1 text-primary'></i>
                            <span>1234 System Blvd, Suite 100<br>Tech City, TC 90210</span>
                        </li>
                        <li class="mb-3 d-flex text-white-50">
                            <i class='bx bx-phone me-2 mt-1 text-primary'></i>
                            <span>+1 (555) 123-4567</span>
                        </li>
                        <li class="mb-3 d-flex text-white-50">
                            <i class='bx bx-envelope me-2 mt-1 text-primary'></i>
                            <span>support@example.com</span>
                        </li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-4">
                    <h5>Newsletter</h5>
                    <p class="small text-white-50">Subscribe for the latest updates.</p>
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Email" style="border-radius: 0.375rem 0 0 0.375rem;">
                        <button class="btn btn-primary" type="button" style="border-radius: 0 0.375rem 0.375rem 0;">
                            <i class='bx bx-send'></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <hr class="my-4" style="border-color: rgba(255,255,255,0.1);">
            
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    &copy; {{ date('Y') }} <strong>{{ config('app.name') }}</strong>. All rights reserved.
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <a href="#" class="me-3 small">Privacy Policy</a>
                    <a href="#" class="small">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    // Offset for fixed header
                    const headerOffset = 70;
                    const elementPosition = targetElement.getBoundingClientRect().top;
                    const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                    window.scrollTo({
                        top: offsetPosition,
                        behavior: "smooth"
                    });
                }
            });
        });
    </script>
</body>
</html>