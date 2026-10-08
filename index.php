<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ICTSAT · TEKU Contribution</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome 6 (Pro icons for that premium feel) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts: Inter + Space Grotesk for that modern tech look -->
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        display: flex;
        align-items: center;
        position: relative;
        overflow-x: hidden;
    }

    /* Animated background elements */
    body::before {
        content: '';
        position: absolute;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
        animation: rotate 30s linear infinite;
        top: -50%;
        left: -50%;
        z-index: 0;
    }

    @keyframes rotate {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    /* Floating orbs */
    .orb {
        position: absolute;
        border-radius: 50%;
        background: radial-gradient(circle at 30% 30%, rgba(255, 255, 255, 0.2), rgba(255, 255, 255, 0.05));
        backdrop-filter: blur(5px);
        z-index: 0;
    }

    .orb-1 {
        width: 300px;
        height: 300px;
        top: -150px;
        right: -150px;
        animation: float 8s ease-in-out infinite;
    }

    .orb-2 {
        width: 200px;
        height: 200px;
        bottom: -100px;
        left: -100px;
        animation: float 12s ease-in-out infinite reverse;
    }

    @keyframes float {

        0%,
        100% {
            transform: translateY(0) scale(1);
        }

        50% {
            transform: translateY(-30px) scale(1.05);
        }
    }

    .container {
        position: relative;
        z-index: 10;
    }

    /* Main card styling */
    .contribution-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 40px;
        box-shadow:
            0 25px 50px -12px rgba(0, 0, 0, 0.25),
            0 0 0 1px rgba(255, 255, 255, 0.1) inset;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
    }

    .contribution-card:hover {
        transform: translateY(-5px);
        box-shadow:
            0 35px 60px -15px rgba(0, 0, 0, 0.3),
            0 0 0 1px rgba(255, 255, 255, 0.2) inset;
    }

    /* Club header section with gradient */
    .club-header {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
        margin: -1.5rem -1.5rem 1.5rem -1.5rem;
        padding: 2.5rem 1.5rem;
        border-radius: 40px 40px 30px 30px;
        position: relative;
        overflow: hidden;
    }

    .club-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0) 100%);
        pointer-events: none;
    }

    .club-header::after {
        content: '';
        position: absolute;
        width: 200px;
        height: 200px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.2) 0%, transparent 70%);
        top: -100px;
        right: -100px;
        border-radius: 50%;
    }

    .club-title {
        font-family: 'Space Grotesk', sans-serif;
        font-weight: 800;
        font-size: 2.5rem;
        letter-spacing: -0.02em;
        color: white;
        text-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        margin: 0;
        position: relative;
    }

    .club-subtitle {
        font-size: 1rem;
        font-weight: 500;
        letter-spacing: 0.5px;
        color: rgba(255, 255, 255, 0.9);
        margin: 0.5rem 0 0 0;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .club-subtitle i {
        font-size: 0.875rem;
        opacity: 0.9;
    }

    /* Form styling */
    .form-floating-custom {
        position: relative;
        margin-bottom: 1.5rem;
    }

    .form-floating-custom .form-control {
        height: 70px;
        padding: 1.5rem 1rem 0.5rem 1rem;
        font-size: 1rem;
        border: 2px solid #e9ecef;
        border-radius: 20px;
        background: white;
        transition: all 0.2s ease;
    }

    .form-floating-custom .form-control:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1);
        outline: none;
    }

    .form-floating-custom label {
        position: absolute;
        top: 0;
        left: 0;
        height: 100%;
        padding: 1rem 1rem;
        pointer-events: none;
        border: 1px solid transparent;
        transform-origin: 0 0;
        transition: all 0.2s ease;
        color: #6c757d;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-floating-custom .form-control:focus~label,
    .form-floating-custom .form-control:not(:placeholder-shown)~label {
        transform: scale(0.85) translateY(-0.75rem) translateX(0.15rem);
        color: #0d6efd;
        font-weight: 600;
    }

    .form-floating-custom i {
        color: #0d6efd;
        font-size: 1.1rem;
    }

    /* Phone input specific */
    .phone-input-wrapper {
        position: relative;
    }

    .phone-prefix {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
        font-weight: 500;
        z-index: 10;
        background: #f8f9fa;
        padding: 0.25rem 0.5rem;
        border-radius: 10px;
        font-size: 0.875rem;
    }

    .phone-input-wrapper .form-control {
        padding-left: 5rem;
    }

    /* Amount display */
    .amount-display {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 20px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        text-align: center;
        border: 2px solid rgba(13, 110, 253, 0.1);
        position: relative;
        overflow: hidden;
    }

    .amount-display::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #0d6efd, #0a58ca, #0d6efd);
        background-size: 200% 100%;
        animation: shimmer 2s infinite;
    }

    @keyframes shimmer {
        0% {
            background-position: 200% 0;
        }

        100% {
            background-position: -200% 0;
        }
    }

    .amount-label {
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 2px;
        color: #6c757d;
        margin-bottom: 0.5rem;
        font-weight: 600;
    }

    .amount-value {
        font-size: 3.5rem;
        font-weight: 800;
        color: #0d6efd;
        line-height: 1;
        font-family: 'Space Grotesk', sans-serif;
    }

    .amount-value small {
        font-size: 1.5rem;
        font-weight: 500;
        color: #6c757d;
    }

    /* Submit button */
    .btn-contribute {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
        border: none;
        border-radius: 20px;
        padding: 1.25rem;
        font-weight: 700;
        font-size: 1.25rem;
        letter-spacing: 0.5px;
        color: white;
        width: 100%;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 10px 20px -5px rgba(13, 110, 253, 0.3);
    }

    .btn-contribute:hover {
        transform: scale(1.02);
        box-shadow: 0 15px 30px -5px rgba(13, 110, 253, 0.4);
        background: linear-gradient(135deg, #0a58ca 0%, #0850b8 100%);
    }

    .btn-contribute:active {
        transform: scale(0.98);
    }

    .btn-contribute i {
        margin-right: 0.75rem;
        font-size: 1.1rem;
    }

    /* Trust indicators */
    .trust-indicators {
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 2px solid rgba(0, 0, 0, 0.05);
        display: flex;
        justify-content: center;
        gap: 2rem;
    }

    .trust-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #6c757d;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .trust-item i {
        color: #0d6efd;
        font-size: 1rem;
    }

    /* Footer note */
    .footer-note {
        text-align: center;
        margin-top: 2rem;
        color: rgba(255, 255, 255, 0.8);
        font-size: 0.875rem;
        font-weight: 500;
        letter-spacing: 0.3px;
    }

    .footer-note i {
        margin: 0 0.25rem;
        font-size: 0.75rem;
    }

    /* Animation for card entrance */
    .card-entrance {
        animation: cardEntrance 0.6s ease-out;
    }

    @keyframes cardEntrance {
        from {
            opacity: 0;
            transform: scale(0.9) translateY(20px);
        }

        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }

    /* Refined, responsive portal styling */
    :root {
        --ink: #14263d;
        --muted: #68778a;
        --brand: #087e8b;
        --brand-dark: #075c67;
        --line: #e2e9ee;
    }

    body {
        color: var(--ink);
        background: #f2f6f8;
        background-image:
            radial-gradient(ellipse at 12% 12%, rgba(8, 126, 139, .10), transparent 38%),
            radial-gradient(ellipse at 90% 88%, rgba(20, 38, 61, .08), transparent 36%);
        padding: 2.5rem 0;
    }

    body::before,
    .orb { display: none; }

    .container { max-width: 1120px; }

    .contribution-card {
        max-width: 510px;
        margin: 0 auto;
        padding: 2rem !important;
        border: 1px solid rgba(20, 38, 61, .08);
        border-radius: 24px;
        background: #fff;
        box-shadow: 0 24px 70px rgba(20, 38, 61, .12);
        transition: box-shadow .2s ease;
    }

    .contribution-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 28px 76px rgba(20, 38, 61, .15);
    }

    .club-header {
        margin: -2rem -2rem 1.75rem;
        padding: 2rem 1.5rem 1.75rem;
        border-radius: 24px 24px 0 0;
        background: linear-gradient(140deg, #14263d, #1c4055);
    }

    .club-header::before { background: radial-gradient(circle at 85% 0, rgba(69, 199, 192, .22), transparent 48%); }
    .club-header::after { display: none; }

    .club-title {
        font-size: clamp(1.8rem, 6vw, 2.35rem);
        letter-spacing: -.055em;
    }

    .club-subtitle {
        margin-top: .65rem;
        color: rgba(255,255,255,.78);
        font-size: .9rem;
        letter-spacing: .02em;
    }

    .club-subtitle .fa-chevron-right { display: none; }
    .club-subtitle .fa-map-marker-alt { color: #62d0c9; }

    .amount-display {
        padding: 1.2rem 1rem;
        margin-bottom: 1.5rem;
        border: 1px solid #dcecee;
        border-radius: 16px;
        background: #f4faf9;
    }

    .amount-display::before { height: 3px; background: var(--brand); animation: none; }
    .amount-label { color: var(--muted); font-size: .75rem; letter-spacing: .12em; }
    .amount-value { color: var(--brand-dark); font-size: 2.8rem; }
    .amount-value small { font-size: 1.05rem; color: var(--muted); }

    .form-floating-custom { margin-bottom: 1.1rem; }
    .form-floating-custom .form-control {
        height: 62px;
        padding-top: 1.35rem;
        border: 1px solid var(--line);
        border-radius: 13px;
        color: var(--ink);
    }

    .form-floating-custom .form-control:focus {
        border-color: var(--brand);
        box-shadow: 0 0 0 3px rgba(8, 126, 139, .12);
    }

    .form-floating-custom label { color: #778596; font-size: .93rem; }
    .form-floating-custom i { color: var(--brand); }
    .amount-field .form-control { font-size: 1.25rem; font-weight: 700; color: var(--brand-dark); }
    .amount-field label { font-size: .88rem; }
    .phone-prefix { color: var(--ink); background: #f1f5f7; border-radius: 8px; }
    .text-muted { color: var(--muted) !important; }

    .btn-contribute {
        min-height: 58px;
        padding: .95rem 1.25rem;
        border-radius: 13px;
        background: var(--brand);
        box-shadow: 0 8px 18px rgba(8, 126, 139, .2);
        font-size: 1rem;
        letter-spacing: .01em;
    }

    .btn-contribute:hover {
        transform: translateY(-1px);
        background: var(--brand-dark);
        box-shadow: 0 10px 22px rgba(8, 126, 139, .25);
    }

    .btn-contribute:active { transform: translateY(0); }
    .trust-indicators { gap: 1.5rem; margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid var(--line); }
    .trust-item { color: var(--muted); font-size: .8rem; }
    .trust-item i { color: var(--brand); }
    .footer-note { color: #627286; margin-top: 1.25rem; font-size: .8rem; }

    @media (max-width: 575.98px) {
        body { align-items: flex-start; padding: 1rem 0; }
        .contribution-card { padding: 1.25rem !important; border-radius: 19px; }
        .club-header { margin: -1.25rem -1.25rem 1.25rem; padding: 1.6rem 1rem 1.4rem; border-radius: 19px 19px 0 0; }
        .amount-display { margin-bottom: 1.2rem; }
        .trust-indicators { gap: .85rem; }
        .trust-item { gap: .35rem; font-size: .72rem; }
    }

    @media (prefers-reduced-motion: reduce) {
        *, *::before, *::after { animation-duration: .01ms !important; animation-iteration-count: 1 !important; scroll-behavior: auto !important; }
    }
    </style>
</head>

<body>
    <!-- Animated background orbs -->
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <!-- Main Contribution Card -->
                <div class="contribution-card p-4 p-xl-5 card-entrance">
                    <!-- Club Header -->
                    <div class="club-header text-center">
                        <h1 class="club-title">
                            ICTSAT<span style="font-weight: 400; opacity: 0.9;">·TEKU</span>
                        </h1>
                        <div class="club-subtitle">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>Member Contribution Portal</span>
                            <i class="fas fa-chevron-right"></i>
                        </div>
                    </div>

                    <!-- Contribution Form -->
                    <form action="process_payment.php" method="POST" id="contributionForm">
                        <div class="form-floating-custom amount-field">
                            <input type="number" class="form-control" id="contributionAmount" name="amount"
                                placeholder=" " min="1" max="2147483647" step="1" value="1000" required>
                            <label for="contributionAmount">
                                <i class="fas fa-coins"></i>
                                Contribution amount (TZS)
                            </label>
                        </div>

                        <!-- Contributor Name Field -->
                        <div class="form-floating-custom">
                            <input type="text" class="form-control" id="contributorName" name="name" placeholder=" "
                                pattern="\S+\s+\S+.*" title="Enter your first and last name" required
                                autocomplete="name">
                            <label for="contributorName">
                                <i class="fas fa-user-circle"></i>
                                Full name
                            </label>
                        </div>

                        <!-- Phone Number Field -->
                        <div class="form-floating-custom">
                            <div class="phone-input-wrapper">
                                <span class="phone-prefix">
                                    <i class="fas fa-phone-alt" style="margin-right: 0.25rem;"></i>
                                    TZ
                                </span>
                                <input type="tel" class="form-control" id="phoneNumber" name="phone" placeholder=" "
                                    inputmode="numeric" pattern="[0-9]{10}" minlength="10" maxlength="10"
                                    title="Enter exactly 10 digits (e.g., 0712345678)" required autocomplete="off">
                            </div>
                            <label for="phoneNumber">
                                <i class="fas fa-mobile-alt"></i>
                                Phone number
                            </label>
                            <small class="text-muted d-block mt-1" style="font-size: 0.75rem; margin-left: 0.5rem;">
                                <i class="fas fa-info-circle"></i>
                                Enter 10 digits including the leading 0 (e.g., 0693662424)
                            </small>
                        </div>

                        <!-- Hidden fields (fixed data) -->
                        <input type="hidden" name="email" value="contributor@ictsat.teku.ac.tz">
                        <input type="hidden" name="address" value="Mbeya">
                        <input type="hidden" name="postcode" value="53000">

                        <!-- Submit Button -->
                        <button type="submit" class="btn-contribute mt-4">
                            <i class="fas fa-lock"></i>
                            Continue to payment
                        </button>
                    </form>

                    <!-- Trust Indicators -->
                    <div class="trust-indicators">
                        <div class="trust-item">
                            <i class="fas fa-shield-alt"></i>
                            <span>Secured</span>
                        </div>
                        <div class="trust-item">
                            <i class="fas fa-bolt"></i>
                            <span>Instant</span>
                        </div>
                        <div class="trust-item">
                            <i class="fas fa-clock"></i>
                            <span>24/7</span>
                        </div>
                    </div>

                    <!-- Additional Info -->
                    <div class="text-center mt-4">
                        <span class="badge bg-light text-dark px-3 py-2 rounded-pill">
                            <i class="fas fa-check-circle text-success me-1"></i>
                            ICTSAT Member Portal · TEKU
                        </span>
                    </div>
                </div>

                <!-- Footer Note -->
                <div class="footer-note">
                    <i class="fas fa-crown"></i>
                    Official contribution portal · Powered by PALMPESA
                    <i class="fas fa-crown"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS (for proper form validation) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Optional: Form validation enhancement -->
    <script>
    (function() {
        'use strict';

        // Phone number formatting
        const phoneInput = document.getElementById('phoneNumber');
        if (phoneInput) {
            phoneInput.addEventListener('input', function(e) {
                // Remove any non-digit characters
                this.value = this.value.replace(/\D/g, '').slice(0, 10);


            });
        }

        // Form validation
        const form = document.getElementById('contributionForm');
        if (form) {
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }

                // Additional phone validation
                const phone = phoneInput.value;
                if (!/^\d{10}$/.test(phone)) {
                    event.preventDefault();
                    alert('Please enter a valid 10-digit phone number');
                    return;
                }

                form.classList.add('was-validated');
            }, false);
        }
    })();
    </script>
</body>

</html>
