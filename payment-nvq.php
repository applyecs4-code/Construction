<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Generate unique reference number for NVQ course
$reference_number = 'NVQ-' . date('Ymd') . '-' . rand(1000, 9999);
?>
<!DOCTYPE html>
<html lang="en-GB">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>NVQ Course Payment · Pay with Wise</title>

<!-- Google Font & Font Awesome -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }

  :root {
    --bg: #f5f7fb;
    --card: #ffffff;
    --accent: #184113;
    --accent-soft: #e6f0fb;
    --muted: #6b7280;
    --border: #e5e7eb;
    --success: #00c37a;
    --wise: #1d4e89; /* Wise brand colour */
  }

  body {
    font-family: 'Inter', Arial, Helvetica, sans-serif;
    background: var(--bg);
    color: #0b1220;
    line-height: 1.5;
  }

  /* Top bar */
  .topbar {
    background: #0b1120;
    color: #e5e7eb;
    font-size: 0.75rem;
    padding: 0.5rem 1rem;
    display: flex;
    justify-content: center;
    gap: 1.5rem;
    flex-wrap: wrap;
  }

  .topbar span {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
  }

  /* Header */
  .header {
    background: #ffffff;
    padding: 0.9rem 1.5rem;
    box-shadow: 0 2px 12px rgba(15,23,42,0.06);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
  }

  .header-left {
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }

  .header-logo {
    width: 2.4rem;
    height: 2.4rem;
    border-radius: 12px;
    background: linear-gradient(145deg, #184113, #22c55e);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 1.2rem;
  }

  .header-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #0f172a;
  }

  .header-sub {
    font-size: 0.75rem;
    color: #6b7280;
  }

  .header-right {
    display: flex;
    gap: 1.2rem;
    align-items: center;
    font-size: 0.85rem;
  }

  .header-right a {
    color: #184113;
    text-decoration: none;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
  }

  /* Main container */
  .container {
    max-width: 1120px;
    margin: 2rem auto 3rem;
    padding: 0 1.5rem;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.8rem;
  }

  @media (max-width: 800px) {
    .container {
      grid-template-columns: 1fr;
    }
  }

  /* Cards */
  .card {
    background: var(--card);
    border-radius: 24px;
    padding: 1.8rem 2rem 2.2rem;
    box-shadow: 0 15px 35px -8px rgba(15,23,42,0.08);
    border: 1px solid rgba(148,163,184,0.16);
  }

  .card-title {
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #64748b;
    margin-bottom: 0.5rem;
  }

  .card h2 {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 0.4rem;
    color: #0f172a;
  }

  .card p {
    font-size: 0.9rem;
    color: #4b5563;
    margin-bottom: 1.5rem;
  }

  /* Order meta */
  .order-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 1.2rem 2rem;
    font-size: 0.9rem;
    background: #f8fafc;
    padding: 1rem 1.2rem;
    border-radius: 18px;
    margin: 1.5rem 0 1.8rem;
    border: 1px solid #e9edf2;
  }

  .order-meta span {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
  }

  /* Price box */
  .price-box {
    background: var(--accent-soft);
    padding: 1.4rem 1.8rem;
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 0.8rem;
    margin-bottom: 2rem;
  }

  .price-main {
    font-size: 2.1rem;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.2;
  }

  .price-sub {
    font-size: 0.8rem;
    color: #4b5563;
  }

  /* Features list */
  .features-list {
    list-style: none;
    margin: 1.2rem 0 1.8rem;
  }

  .features-list li {
    font-size: 0.9rem;
    color: #2d3a4a;
    display: flex;
    align-items: flex-start;
    gap: 0.7rem;
    margin-bottom: 0.7rem;
  }

  .features-list i {
    color: #22c55e;
    font-size: 1rem;
    margin-top: 0.1rem;
  }

  /* Wise button */
  .btn-wise {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.8rem;
    width: 100%;
    background: var(--wise);
    color: white;
    font-weight: 700;
    font-size: 1.2rem;
    padding: 1.1rem 1.5rem;
    border-radius: 60px;
    border: none;
    box-shadow: 0 15px 25px -8px rgba(29,78,137,0.3);
    transition: all 0.2s ease;
    text-decoration: none;
    cursor: pointer;
    margin: 1.5rem 0 1rem;
  }

  .btn-wise:hover {
    background: #0f3b5c;
    transform: translateY(-2px);
    box-shadow: 0 22px 30px -8px rgba(29,78,137,0.4);
  }

  .btn-wise i {
    font-size: 1.2rem;
  }

  .btn-wise small {
    font-size: 0.85rem;
    font-weight: 400;
    opacity: 0.9;
  }

  /* Trust badges */
  .trust-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 0.8rem 1.5rem;
    margin: 1.5rem 0 1.2rem;
    font-size: 0.9rem;
    color: #2d3a4a;
  }

  .trust-badges span {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
  }

  .trust-badges i {
    color: #10b981;
  }

  /* Payment methods (via Wise) */
  .methods {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 1rem;
    margin: 1.2rem 0;
  }

  .methods img {
    height: 28px;
    filter: grayscale(0%);
    opacity: 0.8;
    transition: opacity 0.2s;
  }

  .methods img:hover {
    opacity: 1;
  }

  /* Highlight / info text */
  .highlight {
    font-size: 0.9rem;
    color: #111827;
    margin: 1rem 0;
    padding: 1rem;
    background: #f9fafb;
    border-radius: 16px;
  }

  .guarantee {
    font-size: 0.85rem;
    color: #166534;
    margin: 1rem 0 0.5rem;
    text-align: center;
  }

  .support {
    font-size: 0.85rem;
    color: #4b5563;
    margin-top: 1.2rem;
    text-align: center;
    border-top: 1px dashed #d1d5db;
    padding-top: 1.2rem;
  }

  .support a {
    color: #184113;
    font-weight: 500;
    text-decoration: none;
  }

  .support a:hover {
    text-decoration: underline;
  }

  .flag-uk {
    display: inline-block;
    width: 1.2rem;
    height: 0.9rem;
    background: linear-gradient(180deg, #012169 0%, #012169 33%, #fff 33%, #fff 66%, #c8102e 66%, #c8102e 100%);
    border-radius: 2px;
    margin-right: 0.2rem;
    vertical-align: middle;
  }

  .official-badge {
    display: inline-block;
    background: #0f3b5c;
    color: white;
    font-size: 0.7rem;
    font-weight: 600;
    padding: 0.2rem 0.8rem;
    border-radius: 30px;
    letter-spacing: 0.02em;
    margin-left: 0.8rem;
    vertical-align: middle;
    text-transform: uppercase;
  }
</style>
</head>
<body>

<header class="header">
  <div class="header-left">
    <div class="header-logo">NVQ</div>
    <div>
      <div class="header-title">NVQ Level 2 · UK <span class="official-badge">CITB Approved</span></div>
      <div class="header-sub">Construction & Built Environment</div>
    </div>
  </div>
  <div class="header-right">
    <a href="mailto:info@constructionscert.co.uk"><i class="fas fa-envelope"></i> info@constructionscert.co.uk</a>
    <!-- Phone number removed for clean pro look -->
  </div>
</header>

<!-- Main container -->
<div class="container">

  <!-- LEFT: Order Summary & Wise Payment -->
  <div class="card">
    <div class="card-title">Order Summary</div>
    <h2>Level 2 NVQ Course Booking</h2>
    <p>Pay securely with Wise – the trusted way to send money in the UK. Your NVQ enrolment is confirmed instantly after payment.</p>

    <div class="order-meta">
      <span><i class="far fa-file-alt"></i> Ref: <strong>#<?php echo $reference_number; ?></strong></span>
      <span><i class="far fa-clock"></i> Fast-track available</span>
      <span><i class="fas fa-map-marker-alt"></i> Remote assessment</span>
    </div>

    <!-- Price box -->
    <div class="price-box">
      <div>
        <div class="price-main">£599.00</div>
        <div class="price-sub">Includes NVQ assessment & certification</div>
      </div>
    </div>

    <!-- Features list -->
    <ul class="features-list">
      <li><i class="fas fa-check-circle"></i> CITB‑approved NVQ Level 2</li>
      <li><i class="fas fa-check-circle"></i> Remote assessment – no college attendance</li>
      <li><i class="fas fa-check-circle"></i> Dedicated assessor support</li>
      <li><i class="fas fa-check-circle"></i> Blue CSCS card eligibility</li>
    </ul>

    <!-- Primary Wise button with your link -->
    <a href="https://wise.com/pay/r/rg9RsqqMoYnS3Ag" 
       target="_blank" 
       rel="noopener noreferrer" 
       class="btn-wise"
       onclick="trackWiseClick()">
      <i class="fas fa-shield-alt"></i> Pay £599.00 with Wise
      <small>✔ secure redirect</small>
    </a>

    <div style="margin-top: 0.8rem; font-size:0.8rem; color:#4b5563; background:#f2f5f9; padding:0.6rem; border-radius:12px; text-align:center;">
      <i class="fas fa-credit-card" style="margin-right:0.4rem;"></i> Wise accepts Visa, Mastercard, Apple Pay & bank transfer
    </div>

    <div class="support" style="border:none; margin-top:0.8rem;">
      <i class="fas fa-lock"></i> 100% Secure · SSL Encrypted
    </div>
  </div>

  <!-- RIGHT: Trust & Support -->
  <div class="card">
    <div class="card-title">Why UK learners choose us</div>
    <h2 style="font-size:1.3rem;">Safe, simple & UK‑based</h2>
    <p>We’ve helped over 5,000 construction workers gain their NVQ and CSCS card.</p>

    <!-- Trust badges -->
    <div class="trust-badges">
      <span><i class="fas fa-shield-alt"></i> Encrypted & secure</span>
      <span><i class="fas fa-user-check"></i> Verified Trustpilot</span>
      <span><i class="fas fa-pound-sign"></i> No hidden fees</span>
    </div>

    <!-- Payment methods accepted via Wise -->
    <div class="methods">
      <img src="https://upload.wikimedia.org/wikipedia/commons/a/a4/Mastercard_2019_logo.svg" alt="Mastercard">
      <img src="https://upload.wikimedia.org/wikipedia/commons/b/b5/PayPal.svg" alt="PayPal (via Wise)">
      <img src="https://upload.wikimedia.org/wikipedia/commons/f/fa/Apple_logo_black.svg" alt="Apple Pay">
      <img src="https://cdn.worldvectorlogo.com/logos/wise-2.svg" alt="Wise" style="height:32px;">
    </div>

    <!-- Why Wise? -->
    <div class="highlight">
      <p style="font-weight:600; margin-bottom:0.3rem;"><i class="fas fa-check-circle" style="color:#1d4e89;"></i> Why we use Wise</p>
      <ul style="font-size:0.85rem; color:#3a4a5c; list-style:none; padding-left:0; margin:0;">
        <li style="margin-bottom:0.5rem;">✓ Trusted by millions in the UK</li>
        <li style="margin-bottom:0.5rem;">✓ No currency exchange fees (GBP only)</li>
        <li style="margin-bottom:0.5rem;">✓ Instant confirmation back to us</li>
        <li style="margin-bottom:0.5rem;">✓ 256‑bit SSL & FCA regulated</li>
      </ul>
    </div>

    <!-- Guarantee -->
    <div class="guarantee">
      <i class="fas fa-shield-check"></i> Your NVQ enrolment is guaranteed once payment is completed.
    </div>

    <!-- Support (email & chat) -->
    <div class="support">
      Need help? Our UK support team is online 24/7.<br>
      <a href="mailto:info@constructionscert.co.uk">Email Us</a> · <a href="#">Live Chat</a>
    </div>
  </div>

</div>

<!-- Additional trust message -->
<div style="max-width:760px; margin:0 auto 2.5rem; text-align:center; font-size:0.85rem; background:#f2f7fb; padding:1rem 2rem; border-radius:60px; border:1px solid #cbd5e1;">
  <i class="fas fa-lock" style="color:#184113; margin-right:0.5rem;"></i> 
  Your payment is processed by Wise (TransferWise Ltd), an FCA‑authorised payment institution. We never see your card details.
</div>

<!-- Optional tracking script -->
<script>
function trackWiseClick() {
  console.log('User clicked Wise button for order <?php echo $reference_number; ?>');
  // You can add Google Analytics or other tracking here
}
</script>

<!-- No PayPal scripts – only Wise remains -->
</body>
</html>        