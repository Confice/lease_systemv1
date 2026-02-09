{{--
  Landing page – Stall leasing website.
  IMAGE PLACEHOLDERS (replace with your own in public/images/):
  - Hero background: public/images/hero.jpg (or change .hero-section background-image URL below)
  - About section: public/images/about.jpg (or change #about-img src)
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Stall Leasing Made Simple | {{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&family=Fraunces:ital,opsz,wght@0,9..144,600;0,9..144,700;1,9..144,400&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --lease-primary: #5a6b47;
            --lease-primary-dark: #4a5a3a;
            --lease-accent: #7f9267;
            --lease-bg: #f8f7f4;
            --lease-text: #2d3436;
            --lease-muted: #636e72;
            --lease-card: #ffffff;
        }

        body {
            font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--lease-bg);
            color: var(--lease-text);
            padding-top: 72px;
            min-height: 100vh;
        }

        .text-lease-primary { color: var(--lease-primary) !important; }
        .bg-lease-primary { background-color: var(--lease-primary) !important; }

        /* Navbar */
        .navbar {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(12px);
            box-shadow: 0 1px 0 rgba(0,0,0,0.06);
            height: 72px;
        }
        .navbar-brand {
            font-family: 'Fraunces', serif;
            font-weight: 700;
            font-size: 1.35rem;
            color: var(--lease-text);
        }
        .nav-link {
            font-weight: 500;
            color: var(--lease-text);
            margin: 0 6px;
            padding: 8px 14px !important;
            border-radius: 8px;
        }
        .nav-link:hover, .nav-link.active {
            color: var(--lease-primary);
            background: rgba(90, 107, 71, 0.08);
        }
        .btn-lease {
            background: var(--lease-primary);
            border: none;
            color: #fff;
            font-weight: 600;
            padding: 10px 22px;
            border-radius: 10px;
        }
        .btn-lease:hover {
            background: var(--lease-primary-dark);
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(90, 107, 71, 0.35);
        }

        /* Hero */
        .hero-section {
            position: relative;
            min-height: 85vh;
            display: flex;
            align-items: center;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            /* Replace with your image: background-image: url("{{ asset('images/hero.jpg') }}"); */
            background-image: url("https://images.unsplash.com/photo-1441986300917-64674bd600d8?q=80&w=2070&auto=format&fit=crop");
        }
        .hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(105deg, rgba(45, 52, 54, 0.88) 0%, rgba(90, 107, 71, 0.75) 100%);
        }
        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 640px;
        }
        .hero-content h1 {
            font-family: 'Fraunces', serif;
            font-weight: 700;
            font-size: clamp(2.25rem, 5vw, 3.25rem);
            line-height: 1.2;
            color: #fff;
            margin-bottom: 1.25rem;
        }
        .hero-content .lead {
            font-size: 1.15rem;
            color: rgba(255,255,255,0.9);
            margin-bottom: 2rem;
        }
        .hero-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-top: 2rem;
        }
        .hero-badges span {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: rgba(255,255,255,0.85);
            font-size: 0.9rem;
        }
        .hero-badges i { color: var(--lease-accent); }

        /* Section titles */
        .section-tag {
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--lease-accent);
            margin-bottom: 0.5rem;
        }
        .section-title h2 {
            font-family: 'Fraunces', serif;
            font-weight: 700;
            color: var(--lease-text);
            font-size: 2rem;
        }
        .section-title p { color: var(--lease-muted); }

        /* About */
        .about-img-wrap {
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(0,0,0,0.12);
        }
        .about-img-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        /* Placeholder: replace src with {{ asset('images/about.jpg') }} and add your image to public/images/ */
        .about-feature {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 1.25rem;
        }
        .about-feature-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: rgba(90, 107, 71, 0.12);
            color: var(--lease-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        /* Contact */
        .contact-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        .contact-card .form-control, .contact-card .input-group-text {
            border-radius: 10px;
            border-color: #e9ecef;
        }
        .contact-card .form-control:focus {
            border-color: var(--lease-primary);
            box-shadow: 0 0 0 3px rgba(90, 107, 71, 0.15);
        }

        /* Footer */
        footer {
            background: var(--lease-text);
            color: rgba(255,255,255,0.75);
            padding-top: 3.5rem;
            padding-bottom: 1.5rem;
        }
        footer h5 {
            color: #fff;
            font-weight: 600;
            font-size: 1rem;
            margin-bottom: 1.25rem;
        }
        footer a {
            color: rgba(255,255,255,0.75);
            text-decoration: none;
            transition: color 0.2s;
        }
        footer a:hover { color: var(--lease-accent); }
        footer .brand { font-family: 'Fraunces', serif; font-weight: 700; color: #fff; }

        /* ========== Mobile & responsive ========== */
        @media (max-width: 991.98px) {
            body { padding-top: 56px; }
            .navbar { height: auto; min-height: 56px; padding-top: 0.5rem; padding-bottom: 0.5rem; }
            .navbar .container-xxl { padding-left: 1rem; padding-right: 1rem; }
            .navbar-brand { font-size: 1.15rem; }
            .navbar-brand img { width: 28px; height: 28px; }
            .navbar-collapse {
                margin-top: 0.75rem;
                padding: 1rem 0;
                border-top: 1px solid rgba(0,0,0,0.06);
            }
            .navbar-nav .nav-link { padding: 10px 12px !important; margin: 2px 0; }
            .navbar .d-flex { margin-top: 0.5rem; }
            .navbar .btn-lease { width: 100%; justify-content: center; }

            .hero-section { min-height: 75vh; padding: 2rem 0; align-items: center; }
            .hero-content { padding-left: 0; padding-right: 0; text-align: center; }
            .hero-content h1 { font-size: 1.75rem; margin-bottom: 1rem; }
            .hero-content .lead { font-size: 1rem; margin-bottom: 1.5rem; }
            .hero-content .d-flex { justify-content: center; }
            .hero-content .btn-lease.btn-lg { width: 100%; max-width: 280px; }
            .hero-badges { justify-content: center; margin-top: 1.5rem; gap: 0.75rem; }
            .hero-badges span { font-size: 0.85rem; }

            .section-title h2 { font-size: 1.5rem; }
            .section-title p { font-size: 0.95rem; }
            #about .py-5 { padding-top: 2.5rem !important; padding-bottom: 2.5rem !important; }
            #about .row.g-5 { --bs-gutter-y: 2rem; }
            .about-img-wrap { margin-bottom: 1.5rem; border-radius: 12px; }
            .about-feature { margin-bottom: 1rem; }
            .about-feature-icon { width: 44px; height: 44px; }

            #contact .py-5 { padding-top: 2.5rem !important; padding-bottom: 2.5rem !important; }
            .contact-card .card-body { padding: 1.25rem !important; }
            .contact-card .btn-lease.py-3 { padding: 0.75rem 1rem !important; }

            footer { padding-top: 2.5rem; padding-bottom: 1.25rem; }
            footer .row.g-4 > [class*="col-"] { margin-bottom: 1.5rem; }
            footer h5 { margin-bottom: 0.75rem; }
            footer .brand { font-size: 1.1rem; }
            footer .input-group.input-group-sm { flex-wrap: nowrap; }
            footer .input-group .form-control { min-width: 0; }
        }

        @media (max-width: 575.98px) {
            body { padding-top: 52px; }
            .navbar { min-height: 52px; }
            .navbar-brand { font-size: 1rem; }
            .navbar-brand .me-2 { margin-right: 0.4rem !important; }

            .hero-section { min-height: 70vh; padding: 1.5rem 0; }
            .hero-content h1 { font-size: 1.5rem; }
            .hero-content .lead { font-size: 0.95rem; }
            .hero-badges { flex-direction: column; align-items: center; margin-top: 1.25rem; }

            .section-tag { font-size: 0.75rem; }
            .section-title h2 { font-size: 1.35rem; }
            .section-title.mb-5 { margin-bottom: 2rem !important; }
            #about .order-lg-1 h3 { font-size: 1.25rem; }
            .about-feature-icon { width: 40px; height: 40px; }
            .about-feature h6 { font-size: 0.95rem; }
            .about-feature small { font-size: 0.8rem; }

            .contact-card .card-body { padding: 1rem !important; }
            footer .row.align-items-center .col-md-6 { text-align: center !important; }
            footer .row.align-items-center .text-md-end { text-align: center !important; }
        }

        /* Touch-friendly tap targets and prevent zoom on focus (iOS) */
        @media (max-width: 991.98px) {
            .nav-link, .btn-lease { min-height: 44px; display: inline-flex; align-items: center; }
            .navbar-toggler { padding: 0.5rem 0.6rem; min-width: 44px; min-height: 44px; }
        }
        @media (pointer: coarse) {
            .btn-lease:active { transform: scale(0.98); }
        }

        /* Prevent horizontal overflow on very small screens */
        html { overflow-x: hidden; }
        body { overflow-x: hidden; }
        .container-xxl { padding-left: 1rem; padding-right: 1rem; }
        @media (min-width: 576px) {
            .container-xxl { padding-left: 1.5rem; padding-right: 1.5rem; }
        }
        @media (min-width: 992px) {
            .container-xxl { padding-left: 2rem; padding-right: 2rem; }
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container-xxl">
            <a class="navbar-brand" href="#home">
                <img src="{{ asset('sneat/assets/img/favicon/favicon.ico') }}" alt="" width="32" height="32" class="d-inline-block align-text-top me-2">
                LeaseEase x StoreEdge
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link active" href="#home">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#about">About Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact">Contact Us</a></li>
                </ul>
                <div class="d-flex">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn btn-lease">
                                <i class="bx bx-tachometer me-1"></i> Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-lease">
                                <i class="bx bx-log-in me-1"></i> Login
                            </a>
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <header id="home" class="hero-section">
        <div class="hero-overlay"></div>
        <div class="container-xxl">
            <div class="hero-content">
                <h1>Lease Your Stall. Grow Your Business.</h1>
                <p class="lead">
                    Find and secure the right marketplace stall for your venture. Simple applications, clear terms, and support every step of the way.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn btn-lease btn-lg">Go to Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-lease btn-lg">Apply or sign in</a>
                    @endauth
                </div>
                <div class="hero-badges">
                    <span><i class="bx bx-check-circle"></i> Transparent process</span>
                    <span><i class="bx bx-check-circle"></i> Online applications</span>
                    <span><i class="bx bx-check-circle"></i> Lease management</span>
                </div>
            </div>
        </div>
    </header>

    <section id="about" class="py-5 bg-white">
        <div class="container-xxl py-5">
            <div class="section-title text-center mb-5">
                <p class="section-tag">About Us</p>
                <h2>Your partner in stall leasing</h2>
                <p class="mb-0">We connect vendors with quality marketplace stalls</p>
            </div>
            <div class="row align-items-center g-5">
                <div class="col-lg-6 order-lg-2">
                    <div class="about-img-wrap">
                        {{-- Replace with your image: <img src="{{ asset('images/about.jpg') }}" alt="About our stall leasing" id="about-img"> --}}
                        <img src="https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?q=80&w=800&auto=format&fit=crop" alt="Stall leasing – replace with your image in public/images/about.jpg" id="about-img">
                    </div>
                </div>
                <div class="col-lg-6 order-lg-1">
                    <p class="section-tag">Our mission</p>
                    <h3 class="fw-bold text-lease-primary mb-4">Making stall leasing straightforward and fair</h3>
                    <p class="text-lease-muted mb-4">
                        We run marketplace stall leasing so vendors can focus on their business. From application to contract and billing, everything is handled in one place—clear, secure, and easy to use.
                    </p>
                    <div class="about-feature">
                        <div class="about-feature-icon"><i class="bx bx-store-alt fs-5"></i></div>
                        <div>
                            <h6 class="fw-bold mb-1">Stall discovery</h6>
                            <small class="text-muted">Browse stalls by location and size; apply online.</small>
                        </div>
                    </div>
                    <div class="about-feature">
                        <div class="about-feature-icon"><i class="bx bx-file-blank fs-5"></i></div>
                        <div>
                            <h6 class="fw-bold mb-1">Simple applications</h6>
                            <small class="text-muted">Submit proposals and requirements through the platform.</small>
                        </div>
                    </div>
                    <div class="about-feature">
                        <div class="about-feature-icon"><i class="bx bx-receipt fs-5"></i></div>
                        <div>
                            <h6 class="fw-bold mb-1">Lease & billing</h6>
                            <small class="text-muted">Manage your lease and payments in your dashboard.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="contact" class="py-5" style="background-color: #f0f0ed;">
        <div class="container-xxl py-5">
            <div class="section-title text-center mb-5">
                <p class="section-tag">Contact Us</p>
                <h2>Get in touch</h2>
                <p class="mb-0">Questions about stall leasing or your application? Send us a message.</p>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card contact-card">
                        <div class="card-body p-4 p-md-5">
                            <div id="contactFormAlert" class="alert d-none mb-3" role="alert"></div>
                            <form id="landingContactForm" action="{{ route('landing.contact.submit') }}" method="POST">
                                @csrf
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-500">Full name <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0"><i class="bx bx-user text-lease-muted"></i></span>
                                            <input type="text" name="name" class="form-control border-start-0" placeholder="Your name" value="{{ old('name') }}" required>
                                        </div>
                                        <div class="invalid-feedback d-block" data-error="name"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-500">Email <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0"><i class="bx bx-envelope text-lease-muted"></i></span>
                                            <input type="email" name="email" class="form-control border-start-0" placeholder="you@example.com" value="{{ old('email') }}" required>
                                        </div>
                                        <div class="invalid-feedback d-block" data-error="email"></div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-500">Message <span class="text-danger">*</span></label>
                                    <textarea name="message" class="form-control" rows="4" placeholder="Your message or question about stall leasing..." required>{{ old('message') }}</textarea>
                                    <div class="invalid-feedback d-block" data-error="message"></div>
                                </div>
                                <button type="submit" class="btn btn-lease w-100 py-3" id="contactSubmitBtn">Send message</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <div class="container-xxl">
            <div class="row g-4">
                <div class="col-lg-4">
                    <span class="brand fs-5">LeaseEase x StoreEdge</span>
                    <p class="mt-3 small mb-0">
                        Stall leasing and lease management for marketplaces. Apply, sign, and manage your stall in one place.
                    </p>
                    <div class="mt-3">
                        <a href="#" class="me-3" aria-label="Facebook"><i class="bx bxl-facebook-circle fs-4"></i></a>
                        <a href="#" class="me-3" aria-label="Twitter"><i class="bx bxl-twitter fs-4"></i></a>
                        <a href="#" aria-label="LinkedIn"><i class="bx bxl-linkedin-square fs-4"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4">
                    <h5>Menu</h5>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><a href="#home">Home</a></li>
                        <li class="mb-2"><a href="#about">About Us</a></li>
                        <li class="mb-2"><a href="#contact">Contact Us</a></li>
                        <li class="mb-2"><a href="{{ route('login') }}">Login</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-4">
                    <h5>Contact</h5>
                    <ul class="list-unstyled small">
                        <li class="mb-2 d-flex align-items-start gap-2">
                            <i class="bx bx-map mt-1 text-lease-accent"></i>
                            <span>Your marketplace address here</span>
                        </li>
                        <li class="mb-2 d-flex align-items-center gap-2">
                            <i class="bx bx-phone text-lease-accent"></i>
                            <span>+00 000 000 0000</span>
                        </li>
                        <li class="mb-2 d-flex align-items-center gap-2">
                            <i class="bx bx-envelope text-lease-accent"></i>
                            <span>leasing@example.com</span>
                        </li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-4">
                    <h5>Updates</h5>
                    <p class="small mb-2">Subscribe for stall and deadline updates.</p>
                    <div class="input-group input-group-sm">
                        <input type="email" class="form-control form-control-sm" placeholder="Your email">
                        <button class="btn btn-lease btn-sm" type="button"><i class="bx bx-send"></i></button>
                    </div>
                </div>
            </div>
            <hr class="my-4 border-secondary">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start small">
                    &copy; {{ date('Y') }} <strong class="text-white">{{ config('app.name') }}</strong>. All rights reserved.
                </div>
                <div class="col-md-6 text-center text-md-end small">
                    <a href="#" class="text-decoration-none me-3">Privacy</a>
                    <a href="#" class="text-decoration-none">Terms</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelectorAll('a[href^="#"]').forEach(function(anchor) {
            anchor.addEventListener('click', function(e) {
                var href = this.getAttribute('href');
                if (href === '#') return;
                e.preventDefault();
                var el = document.querySelector(href);
                if (el) {
                    var nav = document.querySelector('.navbar');
                    var offset = (nav && nav.offsetHeight) ? nav.offsetHeight : 72;
                    var top = el.getBoundingClientRect().top + window.pageYOffset - offset;
                    window.scrollTo({ top: top, behavior: 'smooth' });
                    var collapse = document.querySelector('#navbarNav');
                    if (collapse && collapse.classList.contains('show')) {
                        collapse.classList.remove('show');
                        var toggler = document.querySelector('[data-bs-target="#navbarNav"]');
                        if (toggler) toggler.setAttribute('aria-expanded', 'false');
                    }
                }
            });
        });

        (function() {
            var form = document.getElementById('landingContactForm');
            if (!form) return;
            var alertEl = document.getElementById('contactFormAlert');
            var submitBtn = document.getElementById('contactSubmitBtn');
            var sending = false;

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                if (sending) return;
                sending = true;
                alertEl.classList.add('d-none');
                alertEl.classList.remove('alert-success', 'alert-danger');
                [].forEach.call(form.querySelectorAll('[data-error]'), function(el) { el.textContent = ''; });
                [].forEach.call(form.querySelectorAll('.form-control.is-invalid'), function(el) { el.classList.remove('is-invalid'); });
                submitBtn.disabled = true;
                submitBtn.textContent = 'Sending…';

                var body = new FormData(form);
                var token = document.querySelector('meta[name="csrf-token"]');
                if (token) body.append('_token', token.getAttribute('content'));

                fetch(form.getAttribute('action'), {
                    method: 'POST',
                    body: body,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(function(r) { return r.json().then(function(data) { return { ok: r.ok, status: r.status, data: data }; }); })
                .then(function(result) {
                    sending = false;
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Send message';
                    if (result.ok && result.data.success) {
                        alertEl.classList.add('alert-success');
                        alertEl.textContent = result.data.message || 'Thank you! Your message has been sent.';
                        alertEl.classList.remove('d-none');
                        form.reset();
                    } else {
                        alertEl.classList.add('alert-danger');
                        alertEl.textContent = result.data.message || 'Something went wrong. Please try again.';
                        alertEl.classList.remove('d-none');
                        if (result.data.errors) {
                            Object.keys(result.data.errors).forEach(function(field) {
                                var input = form.querySelector('[name="' + field + '"]');
                                var errEl = form.querySelector('[data-error="' + field + '"]');
                                if (input) input.classList.add('is-invalid');
                                if (errEl) errEl.textContent = (result.data.errors[field] && result.data.errors[field][0]) || '';
                            });
                        }
                    }
                })
                .catch(function() {
                    sending = false;
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Send message';
                    alertEl.classList.add('alert-danger');
                    alertEl.textContent = 'Something went wrong. Please try again.';
                    alertEl.classList.remove('d-none');
                });
            });
        })();
    </script>
</body>
</html>
