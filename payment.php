<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Generate unique reference number
$reference_number = 'CSCS-' . date('Ymd') . '-' . rand(1000, 9999);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>CSCS Test Payment</title>
<!-- Meta Pixel Code -->
<!-- Meta Pixel Code -->
<!-- End Meta Pixel Code --><!-- End Meta Pixel Code -->

<!-- Google Font -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<script src="https://www.paypal.com/sdk/js?client-id=BAAC1StmHjyFHWI5TChLYBDVdt00rYoDYm09GloLoF3rgJPdx16Tm4tEgJ7SVfNNjkchV5-wgIHwAbl7eU&currency=GBP"></script>

<style>
:root{
  --bg:#f5f7fb;
  --card:#ffffff;
  --accent:#1d70b8;
  --accent-soft:#e6f0fb;
  --muted:#6b7280;
  --border:#e5e7eb;
  --success:#00c37a;
}

*{
  margin:0;
  padding:0;
  box-sizing:border-box;
}

body{
  font-family:'Inter',Arial,Helvetica,sans-serif;
  background:var(--bg);
  color:#0b1220;
}

/* Header */
.header{
  background:#ffffff;
  padding:16px 24px;
  box-shadow:0 2px 12px rgba(15,23,42,0.06);
  font-size:20px;
  font-weight:700;
  color:var(--accent);
  text-align:center;
}

/* Main container */
.container{
  max-width:1100px;
  margin:32px auto 40px;
  display:grid;
  grid-template-columns:1fr 1fr;
  gap:24px;
  padding:0 16px;
}

/* Card */
.card{
  background:var(--card);
  padding:24px 24px 26px;
  border-radius:14px;
  box-shadow:0 10px 30px rgba(15,23,42,0.08);
  border:1px solid rgba(148,163,184,0.14);
}

.card h2{
  margin:0 0 12px;
  font-size:20px;
  font-weight:600;
}

.card p{
  font-size:14px;
  color:var(--muted);
  line-height:1.6;
}

/* Section titles */
.section-title{
  font-size:13px;
  font-weight:600;
  text-transform:uppercase;
  letter-spacing:0.05em;
  color:#64748b;
  margin-bottom:10px;
}

/* Order meta */
.order-meta{
  font-size:14px;
  margin:10px 0 16px;
}
.order-meta span{
  display:inline-flex;
  align-items:center;
  gap:6px;
  margin-right:14px;
}

/* Price box */
.price-box{
  padding:14px 16px;
  border-radius:12px;
  background:var(--accent-soft);
  font-size:22px;
  font-weight:700;
  text-align:center;
  color:#1f2937;
  margin:20px 0;
  display:flex;
  align-items:center;
  justify-content:space-between;
  gap:12px;
}
.price-box small{
  font-size:12px;
  font-weight:500;
  color:#6b7280;
}

/* PayPal direct link button */
.paypal-direct-btn{
  display:flex;
  align-items:center;
  justify-content:center;
  width:100%;
  padding:15px 20px;
  font-size:16px;
  font-weight:600;
  border-radius:10px;
  border:none;
  cursor:pointer;
  margin:20px 0 10px;
  transition:all .2s;
  background:linear-gradient(135deg, #003087 0%, #009cde 100%);
  color:#ffffff;
  text-decoration:none;
  text-align:center;
  gap:10px;
  box-shadow:0 8px 20px rgba(0, 48, 135, 0.25);
}
.paypal-direct-btn:hover{
  transform:translateY(-2px);
  box-shadow:0 12px 25px rgba(0, 48, 135, 0.35);
  background:linear-gradient(135deg, #002469 0%, #0089c7 100%);
}

.paypal-card h2{
  display:flex;
  align-items:center;
  gap:8px;
}

/* Trust section */
.trust{
  display:flex;
  justify-content:space-between;
  margin:14px 0 18px;
  font-size:13px;
  color:#10b981;
}
.trust span{
  display:inline-flex;
  align-items:center;
  gap:6px;
}
.trust i{font-size:14px}

/* Highlight / info text */
.highlight{
  font-size:13px;
  color:#111827;
  margin:14px 0;
  text-align:center;
}

/* Methods logos */
.methods{
  display:flex;
  justify-content:center;
  gap:10px;
  margin:12px 0 10px;
  flex-wrap:wrap;
}
.methods img{
  height:26px;
  vertical-align:middle;
  filter:grayscale(20%);
}

/* Guarantee & support */
.guarantee{
  font-size:13px;
  color:#166534;
  margin:10px 0 4px;
  text-align:center;
}
.support{
  font-size:13px;
  color:#4b5563;
  margin-top:8px;
  text-align:center;
}
.support a{
  color:#1d70b8;
  text-decoration:none;
  font-weight:500;
}
.support a:hover{
  text-decoration:underline;
}

/* Features list */
.features-list{
  margin:20px 0;
  padding:0;
  list-style:none;
}
.features-list li{
  display:flex;
  align-items:center;
  gap:10px;
  margin-bottom:12px;
  font-size:14px;
}
.features-list i{
  color:var(--success);
  font-size:16px;
}

/* Responsive */
@media(max-width:900px){
  .container{
    grid-template-columns:1fr;
    margin:20px auto 30px;
  }
  .card{
    padding:20px 16px 22px;
  }
  .trust{
    flex-direction:column;
    gap:10px;
    align-items:center;
  }
}
</style>
</head>
<body>

<div class="header">CSCS Test Booking Payment</div>

<div class="container">

  <!-- LEFT: Order Summary -->
  <div class="card">
    <div class="section-title">Order Summary</div>
    <h2>CSCS Test Booking</h2>
    <p>Securely pay online to confirm your CITB Health, Safety &amp; Environment test booking.</p>

    <div class="order-meta">
      <span><i class="far fa-file-alt"></i> Ref: <strong>#<?php echo $reference_number; ?></strong></span>
      <span><i class="far fa-clock"></i> Same-day confirmation</span>
    </div>

    <div class="price-box">
      <span>£39.00</span>
      <small>Includes CITB exam fee &amp; booking service</small>
    </div>
    <div id="paypal-button-container"></div>

    <ul class="features-list">
      <li><i class="fas fa-check-circle"></i> Same-day booking confirmation</li>
      <li><i class="fas fa-check-circle"></i> Instant email & SMS notification</li>
      <li><i class="fas fa-check-circle"></i> 24/7 customer support</li>
      <li><i class="fas fa-check-circle"></i> Free rescheduling option</li>
    </ul>

    
    <div class="support">
      <i class="fas fa-lock"></i> 100% Secure Payment · SSL Encrypted
    </div>


<div id="paypal-button-container"></div>
<script src="https://www.paypal.com/sdk/js?client-id=BAAC1StmHjyFHWI5TChLYBDVdt00rYoDYm09GloLoF3rgJPdx16Tm4tEgJ7SVfNNjkchV5-wgIHwAbl7eU&currency=GBP&enable-funding=applepay"></script>


  </div>

  <!-- RIGHT: PayPal / Trust -->
  <div class="card paypal-card">
    <div class="section-title">Secure Checkout</div>
    <h2><i class="fas fa-lock"></i> 100% Protected Payment</h2>
<p>Trusted by construction workers across the UK. Your details are fully encrypted and your CSCS test booking is securely processed.</p>    <div class="trust">
      <span><i class="fas fa-shield-alt"></i> SSL Secured</span>
      <span><i class="fas fa-user-check"></i> Verified Partner</span>
      <span><i class="fas fa-check-circle"></i> Safe Checkout</span>
    </div>
    <a href="https://www.paypal.com/ncp/payment/TKKPWDQUW4QTU" 
       target="_blank" 
       rel="noopener noreferrer"
       class="paypal-direct-btn">
      <i class="fab fa-paypal"></i> Pay Now with PAYPAL - £39.00
    </a>
    <a href="https://wise.com/pay/r/QGw4RFFT4yzQSFY" 
       target="_blank" 
       rel="noopener noreferrer"
       class="paypal-direct-btn">
      <i class="fab fa-paypal"></i> Pay Now with WISE - £39.00
    </a>

    <!-- PayPal Button Container -->

<div class="highlight">
  <strong>Why Choose Our Service?</strong><br>
  • Simple and hassle-free application process<br>
  • Fast confirmation within the same day<br>
  • Secure and trusted payment system<br>
  • Dedicated support for every applicant<br>
  • Designed for UK construction workers<br>
  • Quick and easy online process<br>
  • Transparent pricing with no hidden fees<br>
  • Reliable service trusted across the UK
</div>
    <div class="methods">
      <img src="https://upload.wikimedia.org/wikipedia/commons/a/a4/Mastercard_2019_logo.svg" alt="MasterCard">
      <img src="https://upload.wikimedia.org/wikipedia/commons/b/b5/PayPal.svg" alt="PayPal">
      <img src="https://upload.wikimedia.org/wikipedia/commons/f/fa/Apple_logo_black.svg" alt="Apple Pay">
    </div>
    
    <div class="guarantee">
      <i class="fas fa-shield-check"></i> Your CSCS test booking is guaranteed once payment is completed.
    </div>

    <div class="support">
      Need help? Our UK support team is online 24/7.<br>
      <a href="mailto:info@costructionscert.co.uk">Email Us</a> · <a href="#">Live Chat</a>
    </div>
  </div>

</div>

<!-- PayPal SDK -->
<script>
// PayPal Integration
paypal.Buttons({
  style: { 
    layout: 'vertical',
    color: 'gold', 
    shape: 'rect', 
    label: 'paypal',
    height: 44
  },
  createOrder: function(data, actions) {
    return actions.order.create({
      purchase_units: [{
        amount: { 
          value: '39.00', 
          currency_code: 'GBP' 
        },
        description: "CSCS Test Booking - Reference #<?php echo $reference_number; ?>"
      }]
    });
  },
  onApprove: function(data, actions) {
    return actions.order.capture().then(function(details) {
      alert('✅ Thank you, ' + details.payer.name.given_name + '! Your CSCS test booking is confirmed.');
      window.location.href = "success.php?reference=<?php echo $reference_number; ?>&payment_id=" + data.orderID;
    });
  },
  onError: function(err) {
    console.error('PayPal error:', err);
    alert('There was an error processing your PayPal payment. Please try again or use the direct PayPal link.');
  },
  onCancel: function(data) {
    console.log('PayPal payment cancelled by user');
  }
}).render('#paypal-button-container');

// Track direct PayPal link click
document.querySelector('.paypal-direct-btn').addEventListener('click', function() {
  fbq('track', 'InitiateCheckout', {
    value: 39.00,
    currency: 'GBP',
    content_type: 'product',
    content_ids: ['cscs_test_booking'],
    content_name: 'CSCS Test Booking'
  });
  
  console.log('Direct PayPal link clicked for reference: <?php echo $reference_number; ?>');
});
</script>

</body>
</html>                                                    