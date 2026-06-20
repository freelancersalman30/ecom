@php
    use App\Http\Controllers\Frontend\ShoppingController;
    use Gloudemans\Shoppingcart\Facades\Cart;

    // Cart subtotal
    $subtotal = Cart::instance('shopping')->subtotal();
    $subtotal = str_replace(',', '', $subtotal);
    $subtotal = str_replace('.00', '', $subtotal);
    $subtotal = (float) $subtotal;

    // Shipping + Discount (Session থেকে)
    $shippingFromSession = Session::get('shipping', 0);
    $discount            = Session::get('discount', 0);

    // Grand total
    $grand_total = $subtotal + $shippingFromSession - $discount;

    // Advance amount (cart থেকে)
    $advance_amount = ShoppingController::getCartAdvanceAmount();
    $hasAdvance     = $advance_amount > 0;
    $due_amount     = $hasAdvance ? ($grand_total - $advance_amount) : 0;

    // Digital product আছে কি না
    $hasDigital = ShoppingController::hasDigitalProductInCart();

    // ডিফল্ট শিপিং (UI এর জন্য, JS পরে আপডেট করবে)
    $defaultShipping = optional($shippingcharge->first())->amount ?? 0;
    $generalsetting = \App\Models\GeneralSetting::first();
@endphp

<!DOCTYPE html>
<html lang="bn">
<head>
  <meta charset="UTF-8" />
  <title>{{ $campaign_data->name }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="shortcut icon" href="{{asset($generalsetting->favicon)}}" alt="Super Ecommerce Favicon" />
  <style>
    *{margin:0;padding:0;box-sizing:border-box}
    body{
      font-family:"Hind Siliguri",system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;
      background:#f5f8f5;
      color:#1f2933;
      line-height:1.6;
    }
    img{max-width:100%;display:block}
    a{text-decoration:none;color:inherit}

    .container{
      width:min(1140px,100% - 32px);
      margin:0 auto;
    }

    /* HEADER */
    header{
      position:sticky;top:0;z-index:100;
      background:#fff;
      border-bottom:1px solid #e5efe7;
    }
    .nav{
      display:flex;
      align-items:center;
      justify-content:space-between;
      padding:10px 0;
      gap:12px;
    }
    .logo{
      display:flex;
      align-items:center;
      gap:8px;
      font-weight:700;
      color:#16a34a;
      font-size:18px;
    }
    .logo-circle{
      width:32px;height:32px;
      border-radius:999px;
      background:#16a34a;
      color:#fff;
      display:flex;
      align-items:center;
      justify-content:center;
      font-size:18px;
      overflow:hidden;
    }
    .logo-circle img {
        width: 100%;
        height: 100%;
        border-radius: 999px;
        object-fit: cover;
    }
    .nav-links{
      display:flex;
      gap:18px;
      font-size:14px;
    }
    .nav-links a{
      padding-bottom:4px;
      border-bottom:2px solid transparent;
    }
    .nav-links a:hover{
      color:#16a34a;
      border-color:#16a34a;
    }
    .nav-actions{
      display:flex;
      gap:8px;
    }
    .btn{
      border-radius:999px;
      border:none;
      padding:7px 16px;
      font-size:13px;
      cursor:pointer;
      display:inline-flex;
      align-items:center;
      justify-content:center;
      gap:6px;
      transition:.2s;
      white-space:nowrap;
    }
    .btn-primary{
      background:#16a34a;
      color:#fff;
    }
    .btn-primary:hover{background:#15803d}
    .btn-outline{
      background:#ecfdf3;
      color:#16a34a;
      border:1px solid #16a34a;
    }
    .btn-outline:hover{background:#d1f8df}

    /* HERO */
    .hero{padding:22px 0 30px;}
    .hero-grid{
      display:grid;
      grid-template-columns:minmax(0,1.2fr) minmax(0,1fr);
      gap:26px;
      align-items:center;
    }
    .badge{
      display:inline-flex;
      align-items:center;
      gap:4px;
      padding:3px 10px;
      border-radius:999px;
      background:#e9f5ff;
      font-size:11px;
      color:#2563eb;
      margin-bottom:8px;
    }
    h1{
      font-size:30px;
      color:#15803d;
      margin-bottom:8px;
    }
    .hero-subtitle{
      font-size:14px;
      color:#4b5563;
      margin-bottom:12px;
    }
    .hero-lists{
      display:flex;
      flex-wrap:wrap;
      gap:20px;
      font-size:13px;
      margin:14px 0 16px;
    }
    .hero-lists ul{list-style:none}
    .hero-lists li::before{
      content:"✔";
      font-size:11px;
      color:#16a34a;
      margin-right:5px;
    }
    .hero-bottom{
      display:flex;
      flex-wrap:wrap;
      gap:14px;
      align-items:center;
      font-size:13px;
      margin-top:8px;
    }
    .rating{display:flex;align-items:center;gap:6px}
    .stars{color:#f59e0b;font-size:13px}

    .hero-video-card{
      border-radius:18px;
      overflow:hidden;
      box-shadow:0 18px 35px rgba(15,118,110,.25);
      background:#000;
      position:relative;
    }
    .hero-video-card iframe{
      width:100%;
      height:100%;
      display:block;
      aspect-ratio:16/10;
    }
    .hero-tag{
      position:absolute;
      right:10px;
      bottom:10px;
      background:rgba(22,163,74,.95);
      color:#fff;
      font-size:11px;
      padding:5px 9px;
      border-radius:10px;
    }

    /* COMMON SECTIONS */
    section{padding:26px 0;}
    .section-title{
      font-size:22px;
      text-align:center;
      color:#15803d;
      margin-bottom:18px;
    }

    /* FEATURES */
    .two-col{
      display:grid;
      grid-template-columns:repeat(2,minmax(0,1fr));
      gap:20px;
    }
    .feature-card{
      background:#fff;
      border-radius:16px;
      display:grid;
      grid-template-columns:145px minmax(0,1fr);
      gap:16px;
      padding:16px;
      box-shadow:0 12px 26px rgba(15,118,110,.06);
    }
    .feature-card img{
      height:135px;
      object-fit:cover;
      border-radius:12px;
    }
    .feature-card h3{
      font-size:16px;
      color:#14532d;
      margin-bottom:6px;
    }
    .feature-card p{font-size:13px;color:#4b5563}

    /* WHY */
    .why-grid{
      display:grid;
      grid-template-columns:repeat(4,minmax(0,1fr));
      gap:18px;
      margin-top:8px;
    }
    .why-item{
      background:#fff;
      border-radius:14px;
      padding:16px 12px;
      text-align:center;
      font-size:13px;
      box-shadow:0 10px 22px rgba(15,118,110,.06);
    }
    .why-icon{
      width:40px;height:40px;
      border-radius:999px;
      background:#ecfdf3;
      display:flex;
      align-items:center;
      justify-content:center;
      font-size:20px;
      color:#16a34a;
      margin:0 auto 6px;
    }
    .why-item h4{margin-bottom:4px;color:#14532d;font-size:14px}

    /* BANNER */
    .banner{
      margin-top:18px;
      border-radius:18px;
      padding:16px;
      background:linear-gradient(120deg,#ffe4e6,#fff7ed);
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:12px;
    }
    .banner-text{font-size:20px;color:#b91c1c;font-weight:600}
    .banner-text small{display:block;font-size:12px;color:#4b5563;margin-top:2px}
    .banner-img{display:flex;gap:10px}
    .banner-img img{
      width:80px;height:80px;border-radius:999px;object-fit:cover;
      border:4px solid rgba(255,255,255,.9);
    }

    /* REVIEWS */
    .review-grid{
      display:grid;
      grid-template-columns:repeat(3,minmax(0,1fr));
      gap:18px;
    }
    .review-card{
      background:#fff;
      border-radius:16px;
      padding:15px;
      font-size:13px;
      box-shadow:0 10px 24px rgba(15,118,110,.06);
    }
    .review-meta{
      margin-top:10px;
      display:flex;
      justify-content:space-between;
      font-size:11px;
      color:#6b7280;
      align-items:center;
    }
    .review-author{display:flex;align-items:center;gap:6px}
    .avatar{
      width:26px;height:26px;border-radius:999px;
      background:url("https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEjdHlnC3NTt-CWv0Phw8RXj8kKpdVTxW_xKVdAVZnwiwOyDJB258GeIw96ExljNvsNMKxeqpcReOvy_5QLSCmS5de535N7xXZunjyVs8i7oXi0Nom090a-wPODTzEI2pPLhekufWSmt_tsZTXIigeoV_yXA-IooIHDQhIcf9XmSo3Wbvwtf3tFzn4diBBvo/s320/149071.png") center/cover no-repeat;
    }

    /* FAQ */
    .faq{
      background:#fff;
      border-radius:20px;
      padding:16px 16px 8px;
      box-shadow:0 16px 32px rgba(15,118,110,.05);
    }
    .faq-item{border-bottom:1px solid #e5e7eb;}
    .faq-item:last-child{border-bottom:none}
    .faq-question{
      padding:10px 0;
      display:flex;
      justify-content:space-between;
      align-items:center;
      cursor:pointer;
      font-size:14px;
    }
    .faq-question span.toggle{font-size:18px;color:#9ca3af}
    .faq-answer{
      max-height:0;overflow:hidden;
      font-size:13px;color:#4b5563;
      transition:max-height .25s ease;
      padding-right:12px;
    }
    .faq-item.open .faq-answer{max-height:140px;padding-bottom:8px;}

    /* GALLERY */
    .gallery-grid{
      display:grid;
      grid-template-columns:repeat(4,minmax(0,1fr));
      gap:16px;
    }
    .gallery-item{
      background:#ffffff;
      border-radius:18px;
      padding:6px;
      box-shadow:0 10px 24px rgba(15,118,110,.08);
    }
    .gallery-item img{
      width:100%;
      height:180px;
      object-fit:cover;
      border-radius:14px;
    }

    /* ORDER SECTION */
    .order-section{
      background:#fff;
      border-radius:20px;
      padding:18px;
      box-shadow:0 18px 36px rgba(15,118,110,.06);
    }
    .order-grid{
      display:grid;
      grid-template-columns:minmax(0,1.6fr) minmax(0,1fr);
      gap:20px;
      align-items:flex-start;
    }
    .product-list{display:flex;flex-direction:column;gap:12px}

    .product-card{
      border-radius:16px;
      border:1px solid #e5e7eb;
      padding:10px 12px;
      display:grid;
      grid-template-columns:72px minmax(0,1.4fr) auto;
      gap:12px;
      align-items:center;
      background:#fff;
    }
    .product-card.selected{
      border:2px solid #16a34a;
      background:#f0fdf4;
    }
    .product-card img{
      width:72px;height:72px;border-radius:10px;object-fit:cover;
    }
    .product-title{font-size:14px;font-weight:600;color:#111827}
    .product-meta{font-size:12px;color:#6b7280;margin-top:2px}
    .product-price{text-align:right;font-size:13px;color:#16a34a}
    .product-price small{display:block;font-size:11px;color:#6b7280;margin-top:1px}

    .select-row{
      font-size:12px;color:#059669;
      display:flex;align-items:center;gap:6px;margin-bottom:2px;
    }

    .qty-control{
      display:inline-flex;
      margin-top:4px;
      border-radius:999px;
      border:1px solid #d1d5db;
      overflow:hidden;
    }
    .qty-control button{
      width:24px;height:24px;
      border:none;
      background:#f3f4f6;
      cursor:pointer;
      font-size:16px;
    }
    .qty-control input{
      width:32px;
      border:none;
      text-align:center;
      font-size:12px;
      outline:none;
    }

    .cart-box{
      background:#f9fafb;
      border-radius:16px;
      padding:12px 14px;
      border:1px solid #e5e7eb;
      font-size:13px;
    }
    .cart-header{
      display:flex;
      justify-content:space-between;
      align-items:center;
      margin-bottom:6px;
    }
    .cart-header h3{font-size:15px;color:#14532d}
    .cart-row{
      display:flex;
      justify-content:space-between;
      margin:3px 0;
    }
    .cart-row.total{
      margin-top:6px;
      padding-top:5px;
      border-top:1px dashed #d1d5db;
      font-weight:600;
    }
    .cart-actions{
      margin-top:9px;
      display:flex;
      flex-direction:column;
      gap:5px;
    }
    .cart-actions small{font-size:11px;color:#6b7280}
    .order-btn{
      width:100%;
      background:#16a34a;
      color:#fff;
      border:none;
      padding:9px 12px;
      border-radius:999px;
      cursor:pointer;
      font-size:14px;
    }

    .summary-table{width:100%;border-collapse:collapse;font-size:12px;margin-bottom:6px}
    .summary-table td{padding:3px 0}
    .mini-qty{
      display:inline-flex;
      border-radius:999px;
      border:1px solid #cbd5e1;
      overflow:hidden;
    }
    .mini-qty button{
      width:18px;height:18px;border:none;background:#e5e7eb;font-size:12px;cursor:pointer;
    }
    .mini-qty input{
      width:20px;border:none;text-align:center;font-size:11px;background:transparent;
    }

    /* CHECKOUT FORM – আগের মত লুক */
    .checkout{
      margin-top:16px;
      display:grid;
      grid-template-columns:repeat(2, minmax(0,1fr));
      column-gap:20px;
      row-gap:12px;
    }
    .checkout .full,
    .checkout-footer{
      grid-column:1 / 3;
    }
    .checkout label{
      display:inline-block;
      margin-bottom:4px;
      font-size:13px;
      color:#374151;
    }
    .checkout input,
    .checkout select{
      width:100%;
      border-radius:999px;
      border:1px solid #d1d5db;
      padding:10px 14px;
      font-size:14px;
      outline:none;
      background:#fff;
    }
    .checkout textarea{
      width:100%;
      border-radius:14px;
      border:1px solid #d1d5db;
      padding:10px 14px;
      font-size:14px;
      outline:none;
      background:#fff;
      resize:vertical;
      min-height:70px;
    }
    .checkout-footer{
      display:flex;
      justify-content:flex-end;
      margin-top:4px;
    }

    /* PAYMENT METHODS */
    .form-check {
        background: #f9f9f9;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 10px 14px;
        margin-bottom: 10px;
        transition: all 0.3s ease;
    }
    .form-check:hover {
        background: #eefaf0;
        border-color: #4DBC60;
    }
    .form-check-input {
        transform: scale(1.1);
        margin-right: 10px;
    }
    .form-check-input:checked {
        background-color: #4DBC60;
        border-color: #4DBC60;
    }
    .form-check-label {
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .form-check-label img {
        object-fit: contain;
    }
    @media (max-width: 767px) {
        .form-check-label span {
            font-size: 14px;
        }
    }
    .advance-alert{
        background:#e0f2fe;
        border:1px solid #93c5fd;
        color:#1e40af;
        border-radius:8px;
        padding:8px 10px;
        font-size:13px;
        margin-bottom:8px;
    }

    /* HELP SECTION */
    .help-section{
      margin:26px auto 18px;
      border-radius:20px;
      background:#ecfdf3;
      padding:16px 14px;
      text-align:center;
    }
    .help-section h3{font-size:18px;color:#14532d;margin-bottom:4px}
    .help-actions{
      display:flex;
      justify-content:center;
      gap:10px;
      margin-top:8px;
    }
    .help-actions button{
      border-radius:999px;
      border:1px solid #16a34a;
      background:#fff;
      padding:6px 14px;
      font-size:13px;
      cursor:pointer;
    }

    /* FOOTER */
    footer{
      background:#020617;
      color:#e5e7eb;
      padding-top:26px;
      margin-top:30px;
    }
    .footer-grid{
      display:grid;
      grid-template-columns:1.3fr 1fr 1fr;
      gap:20px;
      font-size:12px;
    }
    .footer-title{font-weight:600;margin-bottom:6px;font-size:13px}
    .footer-links a{
      display:block;
      margin-bottom:4px;
      color:#9ca3af;
    }
    .footer-bottom{
      border-top:1px solid #111827;
      margin-top:14px;
      padding:9px 0 12px;
      font-size:11px;
      display:flex;
      justify-content:space-between;
      color:#6b7280;
    }

    /* RESPONSIVE */
    @media(max-width:960px){
      .hero-grid{grid-template-columns:1fr}
      .two-col{grid-template-columns:1fr}
      .why-grid{grid-template-columns:repeat(2,minmax(0,1fr))}
      .review-grid{grid-template-columns:repeat(2,minmax(0,1fr))}
      .order-grid{grid-template-columns:1fr}
    }
    @media(max-width:768px){
      .checkout{
        grid-template-columns:1fr;
      }
      .checkout .full,
      .checkout-footer{
        grid-column:1 / -1;
      }
      /* 768px এর নিচে গ্যালারিতে ২ কলাম */
      .gallery-grid{
        grid-template-columns:repeat(2,minmax(0,1fr));
      }
    }
    @media(max-width:720px){
      .nav-links{display:none}
      h1{font-size:26px}
      .why-grid,
      .review-grid{
        grid-template-columns:1fr 1fr;
      }
      /* এখানে আগেও ১–১ ছিল, এখন ২টা করলাম */
      .gallery-grid{
        grid-template-columns:repeat(2,minmax(0,1fr));
      }
      .footer-grid{grid-template-columns:1fr}
    }
    @media(max-width:480px){
      .why-grid,
      .review-grid{
        grid-template-columns:1fr;
      }
      /* মোবাইল ছোট স্ক্রিনেও ২টা ইমেজ */
      .gallery-grid{
        grid-template-columns:repeat(2,minmax(0,1fr));
      }
    }
  </style>
</head>
<body>

<header>
  <div class="container nav">
    <div class="logo">
      <div class="logo-circle">
        <img src="{{asset($generalsetting->favicon)}}" alt="logo">
      </div>
      <span>{{ $generalsetting->name }}</span>
    </div>
    <nav class="nav-links">
      <a href="#features">বৈশিষ্ট্য</a>
      <a href="#why">কেন সেরা</a>
      <a href="#reviews">রিভিউ</a>
      <a href="#faq">প্রশ্নোত্তর</a>
      <a href="#order">অর্ডার</a>
    </nav>
    <div class="nav-actions">
      @if(auth('customer')->check())
        <a href="{{ route('customer.account') }}" class="btn btn-outline">
          {{ auth('customer')->user()->name }} এর অ্যাকাউন্ট
        </a>
      @else
        <a href="{{ route('customer.login') }}" class="btn btn-outline">
          লগইন
        </a>
      @endif
      <a href="#order" class="btn btn-primary">অর্ডার করুন</a>
    </div>
  </div>
</header>

<main>
  <!-- HERO -->
  <section class="hero">
    <div class="container hero-grid">
      <div>
        <div class="badge">{{ $campaign_data->hero_badge_text ?? '✅ খুলনার অরিজিনাল চুইঝাল' }}</div>
        <h1>{{ $campaign_data->hero_title ?? 'খুলনার বিখ্যাত চুইঝাল!' }}</h1>
        <p class="hero-subtitle">
          {{ $campaign_data->hero_subtitle
            ?? 'নিজ হাতে প্রস্তুত ঘানি ভাঙ্গা সরিষার তেল আর বিশেষ মশলার মিশেলে তৈরি আমাদের চুইঝাল। গরু, খাসি, হাঁস কিংবা মাছ – যেকোনো মাংসের সাথে রান্না করে পেয়ে যান খুলনার আসল স্বাদ।' }}
        </p>

        <div class="hero-lists">
          <ul>
            <li>{{ $campaign_data->hero_list_1 ?? 'হোমমেড – কোন প্রিজারভেটিভ নেই' }}</li>
            <li>{{ $campaign_data->hero_list_2 ?? 'খাঁটি চুই গাছ থেকে তৈরি' }}</li>
            <li>{{ $campaign_data->hero_list_3 ?? 'ফুড গ্রেড প্যাকেট' }}</li>
          </ul>

          <ul>
            <li>{{ $campaign_data->hero_list_4 ?? 'ঘানি ভাঙ্গা সরিষার তেল' }}</li>
            <li>{{ $campaign_data->hero_list_5 ?? 'দেশব্যাপী কুরিয়ার ডেলিভারি' }}</li>
            <li>{{ $campaign_data->hero_list_6 ?? 'ক্যাশ অন ডেলিভারি' }}</li>
          </ul>
        </div>

        <div class="hero-bottom">
          <div class="rating">
            <span class="stars">★★★★★</span>
            <span>{{ $campaign_data->hero_rating_text ?? '৪.৯/৫ (৪৮৯+ কাস্টমার)' }}</span>
          </div>
          <a href="#order" class="btn btn-primary">{{ $campaign_data->primary_btn_text ?? 'এখনই অর্ডার করুন' }}</a>
          @if($campaign_data->video)
            <a href="#video" class="btn btn-outline">{{ $campaign_data->secondary_btn_text ?? 'লাইভ রান্না ভিডিও' }}</a>
          @endif
        </div>
      </div>

      @if($campaign_data->video)
      <!-- ভিডিও -->
      <div class="hero-video-card" id="video">
        <iframe src="https://www.youtube.com/embed/{{$campaign_data->video}}"
                title="চুইঝাল রান্না ভিডিও" frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen></iframe>
        <div class="hero-tag">{{ $campaign_data->hero_badge_text ?? '✅ খুলনার অরিজিনাল চুইঝাল' }}</div>
      </div>
      @endif
    </div>
  </section>

  <!-- FEATURES -->
  <section id="features">
    <div class="container">
      <h2 class="section-title">
        {{ $campaign_data->feature_section_title ?? 'চুইঝালের বৈশিষ্ট্যসমূহ' }}
      </h2>

      <div class="two-col">
        {{-- Feature 1 --}}
        <article class="feature-card">
          <img src="{{ $campaign_data->feature1_image ? asset($campaign_data->feature1_image) : 'https://images.pexels.com/photos/4113832/pexels-photo-4113832.jpeg?auto=compress&cs=tinysrgb&w=500' }}" alt="">
          <div>
            <h3>{{ $campaign_data->feature1_title ?? 'কাঁচা চুইঝালের আসল ঝাঁজ' }}</h3>
            <p>{{ $campaign_data->feature1_text ?? 'খুলনা অঞ্চলের নির্বাচিত চুই গাছের নরম অংশ সংগ্রহ করে...' }}</p>
          </div>
        </article>

        {{-- Feature 2 --}}
        <article class="feature-card">
          <img src="{{ $campaign_data->feature2_image ? asset($campaign_data->feature2_image) : 'https://images.pexels.com/photos/4199091/pexels-photo-4199091.jpeg?auto=compress&cs=tinysrgb&w=500' }}" alt="">
          <div>
            <h3>{{ $campaign_data->feature2_title ?? 'দীর্ঘদিন ভালো থাকে' }}</h3>
            <p>{{ $campaign_data->feature2_text ?? 'ভ্যাকুয়াম প্যাকিং এবং সঠিক ডিহাইড্রেশন থাকার কারণে...' }}</p>
          </div>
        </article>
      </div>
    </div>
  </section>

  <!-- WHY -->
  <section id="why">
    <div class="container">
      <h2 class="section-title">{{ $campaign_data->heading_2 ?? 'কেন আমাদের প্রোডাক্ট সেরা?' }}</h2>

      <div class="why-grid">
        @if($campaign_data->why1_title)
          <div class="why-item">
            <div class="why-icon">{{ $campaign_data->why1_icon ?? '🏠' }}</div>
            <h4>{{ $campaign_data->why1_title }}</h4>
            <p>{{ $campaign_data->why1_text }}</p>
          </div>
        @endif

        @if($campaign_data->why2_title)
          <div class="why-item">
            <div class="why-icon">{{ $campaign_data->why2_icon ?? '🌿' }}</div>
            <h4>{{ $campaign_data->why2_title }}</h4>
            <p>{{ $campaign_data->why2_text }}</p>
          </div>
        @endif

        @if($campaign_data->why3_title)
          <div class="why-item">
            <div class="why-icon">{{ $campaign_data->why3_icon ?? '🚚' }}</div>
            <h4>{{ $campaign_data->why3_title }}</h4>
            <p>{{ $campaign_data->why3_text }}।</p>
          </div>
        @endif

        @if($campaign_data->why4_title)
          <div class="why-item">
            <div class="why-icon">{{ $campaign_data->why4_icon ?? '💬' }}</div>
            <h4>{{ $campaign_data->why4_title }}</h4>
            <p>{{ $campaign_data->why4_text }}</p>
          </div>
        @endif
      </div>

      <div class="banner">
        <div class="banner-text">
          {{ $campaign_data->banner_quote ?? '“এমন ঝাঁজে নেই তো তুলনা!”' }}
          <small>{{ $campaign_data->banner_subtext ?? 'খুলনার অরিজিনাল চুইঝাল – একবার খেলেই বুঝবেন পার্থক্য' }}</small>
        </div>
        <div class="banner-img">
          @if($campaign_data->banner_image1)
            <img src="{{ asset($campaign_data->banner_image1) }}" alt="">
          @endif
          @if($campaign_data->banner_image2)
            <img src="{{ asset($campaign_data->banner_image2) }}" alt="">
          @endif
        </div>
      </div>
    </div>
  </section>

@if($campaign_data->show_product==1)
{{-- CAMPAIGN PRODUCT CARD GRID --}}
<section class="campaign-product-section">
  <div class="container">
    <h2 class="section-title">{{ $campaign_data->billing_details ?? 'আমাদের প্যাকগুলো' }}</h2>

    <div class="campaign-product-grid">
      @foreach($products as $product)
        @php
          $oldPrice   = $product->selling_price;
          $current    = $product->new_price ?? $oldPrice;
          $discount   = $product->new_price
                        ? round((($oldPrice - $current) / max($oldPrice,1)) * 100)
                        : 0;
          $img        = optional($product->image)->image ?? '';
        @endphp

        <div class="campaign-product-card">
          <div class="cp-card-top">
            <img src="{{ $img ? asset($img) : 'https://via.placeholder.com/300x300' }}"
                 alt="{{ $product->name }}">
            @if($discount > 0)
              <span class="cp-discount-badge">-{{ $discount }}%</span>
            @endif
          </div>

          <div class="cp-title">
            {{ \Illuminate\Support\Str::limit($product->name, 40) }}
          </div>

          <div class="cp-rating">
            ★★★★★
          </div>

          <div class="cp-price-row">
            <span class="cp-price-new">৳ {{ number_format($current,0) }}</span>
            @if($discount > 0)
              <span class="cp-price-old">৳ {{ number_format($oldPrice,0) }}</span>
            @endif
          </div>

          <div class="cp-card-footer">
            <a href="#order"
               class="btn btn-primary cp-order-btn"
               data-product-id="{{ $product->id }}">
              অর্ডার করুন
            </a>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>
@endif

<style>
/* CAMPAIGN PRODUCT CARD GRID */
.campaign-product-section{
  padding: 26px 0 10px;
}
.campaign-product-grid{
  display:grid;
  grid-template-columns:repeat(5,minmax(0,1fr));
  gap:16px;
}
@media(max-width:1024px){
  .campaign-product-grid{
    grid-template-columns:repeat(4,minmax(0,1fr));
  }
}
@media(max-width:768px){
  .campaign-product-grid{
    grid-template-columns:repeat(2,minmax(0,1fr));
  }
}
@media(max-width:480px){
  .campaign-product-grid{
    grid-template-columns:repeat(2,minmax(0,1fr));
  }
}
.campaign-product-card{
  border:1px solid #e11d8f33;
  border-radius:10px;
  padding:10px 10px 12px;
  background:#fff;
  display:flex;
  flex-direction:column;
  gap:6px;
  box-shadow:0 6px 14px rgba(0,0,0,.04);
}
.campaign-product-card img{
  width:100%;
  height:150px;
  object-fit:cover;
  border-radius:8px;
}
.cp-title{
  font-size:13px;
  font-weight:600;
  color:#111827;
  min-height:34px;
}
.cp-price-row{
  display:flex;
  align-items:center;
  gap:6px;
  font-size:13px;
}
.cp-price-new{
  font-weight:700;
  color:#111827;
}
.cp-price-old{
  text-decoration:line-through;
  color:#9ca3af;
  font-size:12px;
}
.cp-discount-badge{
  position:absolute;
  top:8px;
  right:8px;
  background:#e11d8f;
  color:#fff;
  font-size:11px;
  padding:3px 6px;
  border-radius:999px;
}
.cp-card-top{
  position:relative;
}
.cp-rating{
  font-size:11px;
  color:#f97316;
}
.cp-card-footer{
  margin-top:6px;
  display:flex;
  justify-content:space-between;
  align-items:center;
  gap:6px;
}
.cp-order-btn{
  flex:1;
  border:none;
  background:#7c3aed;
  color:#fff;
  font-size:13px;
  padding:6px 8px;
  border-radius:999px;
  cursor:pointer;
}
.cp-order-btn:hover{
  background:#5b21b6;
}
.cp-cart-icon{
  width:32px;
  height:32px;
  border-radius:8px;
  border:none;
  background:#7c3aed;
  color:#fff;
  display:flex;
  align-items:center;
  justify-content:center;
  cursor:pointer;
  font-size:15px;
}
</style>

<section id="reviews">
  <div class="container">
    <h2 class="section-title">
        {{ $campaign_data->review_section_title ?? 'কাস্টমার রিভিউ' }}
    </h2>

    <div class="review-grid">
      @if($campaign_data->review1_text)
      <article class="review-card">
        <div class="stars">{{ $campaign_data->review1_stars ?? '★★★★★' }}</div>
        <p>{{ $campaign_data->review1_text }}</p>
        <div class="review-meta">
          <div class="review-author">
            <div class="avatar"></div>
            <span>{{ $campaign_data->review1_name ?? '' }}</span>
          </div>
          <span>{{ $campaign_data->review1_city ?? '' }}</span>
        </div>
      </article>
      @endif

      @if($campaign_data->review2_text)
      <article class="review-card">
        <div class="stars">{{ $campaign_data->review2_stars ?? '★★★★★' }}</div>
        <p>{{ $campaign_data->review2_text }}</p>
        <div class="review-meta">
          <div class="review-author">
            <div class="avatar"></div>
            <span>{{ $campaign_data->review2_name ?? '' }}</span>
          </div>
          <span>{{ $campaign_data->review2_city ?? '' }}</span>
        </div>
      </article>
      @endif

      @if($campaign_data->review3_text)
      <article class="review-card">
        <div class="stars">{{ $campaign_data->review3_stars ?? '★★★★☆' }}</div>
        <p>{{ $campaign_data->review3_text }}</p>
        <div class="review-meta">
          <div class="review-author">
            <div class="avatar"></div>
            <span>{{ $campaign_data->review3_name ?? '' }}</span>
          </div>
          <span>{{ $campaign_data->review3_city ?? '' }}</span>
        </div>
      </article>
      @endif
    </div>
  </div>
</section>

<!-- FAQ -->
<section id="faq">
  <div class="container">
    <h2 class="section-title">সাধারণ জিজ্ঞাসা (FAQ)</h2>

    <div class="faq">
      @if($campaign_data->faq_q1)
      <div class="faq-item">
        <div class="faq-question">
          <span>{{ $campaign_data->faq_q1 }}</span>
          <span class="toggle">+</span>
        </div>
        <div class="faq-answer">
          {!! nl2br(e($campaign_data->faq_a1)) !!}
        </div>
      </div>
      @endif

      @if($campaign_data->faq_q2)
      <div class="faq-item">
        <div class="faq-question">
          <span>{{ $campaign_data->faq_q2 }}</span>
          <span class="toggle">+</span>
        </div>
        <div class="faq-answer">
          {!! nl2br(e($campaign_data->faq_a2)) !!}
        </div>
      </div>
      @endif

      @if($campaign_data->faq_q3)
      <div class="faq-item">
        <div class="faq-question">
          <span>{{ $campaign_data->faq_q3 }}</span>
          <span class="toggle">+</span>
        </div>
        <div class="faq-answer">
          {!! nl2br(e($campaign_data->faq_a3)) !!}
        </div>
      </div>
      @endif

      @if($campaign_data->faq_q4)
      <div class="faq-item">
        <div class="faq-question">
          <span>{{ $campaign_data->faq_q4 }}</span>
          <span class="toggle">+</span>
        </div>
        <div class="faq-answer">
          {!! nl2br(e($campaign_data->faq_a4)) !!}
        </div>
      </div>
      @endif
    </div>
  </div>
</section>

<!-- GALLERY -->
<section>
  <div class="container">
    <h2 class="section-title">চুইঝাল গ্যালারি</h2>
    <div class="gallery-grid">
      @for($i = 1; $i <= 8; $i++)
        @php $field = "gallery_image{$i}"; @endphp
        @if(!empty($campaign_data->$field))
          <div class="gallery-item">
            <img src="{{ asset($campaign_data->$field) }}" alt="Gallery {{ $i }}">
          </div>
        @endif
      @endfor
    </div>
  </div>
</section>

<!-- ORDER SECTION -->
<section id="order">
  <div class="container">
    <h2 class="section-title">অর্ডার করুন এখনই</h2>

    <div class="order-section">
      <div class="order-grid">
        <!-- PRODUCTS -->
        <div class="product-list">
          @foreach($products as $product)
            @php
              $price = $product->new_price ?? $product->selling_price;
              $img   = optional($product->image)->image ?? '';
              $meta  = \Illuminate\Support\Str::limit(strip_tags($product->short_description ?? ''), 70);
            @endphp
            <div class="product-card"
                 data-id="{{ $product->id }}"
                 data-name="{{ e($product->name) }}"
                 data-price="{{ $price }}"
                 data-image="{{ $img ? asset($img) : '' }}">
              <img src="{{ $img ? asset($img) : 'https://via.placeholder.com/72' }}" alt="{{ $product->name }}">
              <div>
                <label class="select-row">
                  <input type="checkbox" class="prod-check">
                  <span>এই প্যাকটি সিলেক্ট করুন</span>
                </label>
                <div class="product-title">{{ $product->name }}</div>
                @if($meta)<div class="product-meta">{{ $meta }}</div>@endif
                <div class="qty-control">
                  <button type="button" class="qty-minus">-</button>
                  <input type="text" class="qty-input" value="0" readonly>
                  <button type="button" class="qty-plus">+</button>
                </div>

                <!-- hidden for form -->
                <input type="hidden" name="items[{{ $product->id }}][qty]"
                       class="hidden-qty" form="checkoutForm" value="0">
                <input type="hidden" name="items[{{ $product->id }}][price]"
                       form="checkoutForm" value="{{ $price }}">
                <input type="hidden" name="items[{{ $product->id }}][name]"
                       form="checkoutForm" value="{{ $product->name }}">
              </div>
              <div class="product-price">
                ৳ <span class="line-total">0</span>
                <small>ডেলিভারি চার্জ প্রযোজ্য</small>
              </div>
            </div>
          @endforeach
        </div>

        <!-- CART SUMMARY -->
        <aside class="cart-box">
          <div class="cart-header">
            <h3>অর্ডার সারাংশ</h3>
            <span style="font-size:11px;color:#6b7280;">
              @if($hasDigital)
                শুধুমাত্র অনলাইন পেমেন্ট
              @elseif($hasAdvance)
                অগ্রিম + ডেলিভারিতে বাকি
              @else
                ক্যাশ অন ডেলিভারি
              @endif
            </span>
          </div>

          <table class="summary-table" id="summaryTableBody">
            <!-- JS দিয়ে ভরবে -->
          </table>

          <div class="cart-row">
            <span>পণ্যের মূল্য</span>
            <span>৳ <span id="subtotal">0</span></span>
          </div>
          <div class="cart-row">
            <span>ডেলিভারি চার্জ</span>
            <span>৳ <span id="shipping" data-amount="{{ $defaultShipping }}">{{ $defaultShipping }}</span></span>
          </div>
          <div class="cart-row total">
            <span>মোট দিতে হবে</span>
            <span>৳ <span id="total">{{ $defaultShipping }}</span></span>
          </div>

          @if($hasAdvance)
            <div class="cart-row">
              <span>এখন অগ্রিম পরিশোধ করবেন</span>
              <span>৳ <span id="advance_now">{{ number_format($advance_amount,2) }}</span></span>
            </div>
            <div class="cart-row">
              <span>ডেলিভারির সময় দিতে হবে</span>
              <span>৳ <span id="advance_due">{{ number_format($due_amount,2) }}</span></span>
            </div>
          @endif

          <div class="cart-actions">
            <button type="submit" class="order-btn" form="checkoutForm">
              অর্ডার কনফার্ম করুন
            </button>
            <small>আমাদের প্রতিনিধি অর্ডার কনফার্মেশনের জন্য আপনাকে ফোন করবে।</small>
          </div>
        </aside>
      </div>

      <!-- CHECKOUT FORM -->
      <form class="checkout" id="checkoutForm" autocomplete="off" action="{{ route('customer.ordersave') }}" method="POST">
        @csrf
        <div>
          <label>আপনার নাম *</label>
          <input type="text" name="name" placeholder="পূর্ণ নাম লিখুন" required>
        </div>
        <div>
          <label>মোবাইল নাম্বার *</label>
          <input type="text" name="phone" placeholder="১১ ডিজিট মোবাইল নাম্বার" required>
        </div>

        <div class="full">
          <label>পূর্ণ ডেলিভারি ঠিকানা *</label>
          <textarea name="address" placeholder="বাড়ি/ফ্ল্যাট, রাস্তার নাম, এরিয়া ইত্যাদি লিখুন" required></textarea>
        </div>
        <div class="full">
          <label>অতিরিক্ত নোট (যদি থাকে)</label>
          <textarea name="note" placeholder="যেমন: সন্ধ্যায় ডেলিভারি, অফিসে ডেলিভারি ইত্যাদি"></textarea>
        </div>

        <div>
          <label>শিপিং এরিয়া *</label>
          <select name="area" id="area" required>
            @foreach($shippingcharge as $area)
              <option value="{{ $area->id }}" data-amount="{{ $area->amount }}">
                {{ $area->name }} (৳ {{ $area->amount }})
              </option>
            @endforeach
          </select>
        </div>

        <div>
          <label for="payment_method">পেমেন্ট মেথড নির্বাচন করুন *</label>
          <select name="payment_method" id="payment_method" required>
              <option value="cod" selected>
                  ক্যাশ অন ডেলিভারি (ডেলিভারির সময় পেমেন্ট)
              </option>

              @isset($bkash_gateway)
                <option value="bkash">বিকাশ পেমেন্ট</option>
              @endisset

              @isset($shurjopay_gateway)
                <option value="shurjopay">ShurjoPay (অনলাইন পেমেন্ট)</option>
              @endisset

              @isset($uddoktapay_gateway)
                <option value="uddoktapay">UddoktaPay (অনলাইন পেমেন্ট)</option>
              @endisset
          </select>
        </div>

        <div class="checkout-footer">
          <button type="submit" class="btn btn-primary">অর্ডার সাবমিট করুন</button>
        </div>
      </form>
    </div>
  </div>
</section>

<!-- HELP -->
<section>
  <div class="container help-section">
    <h3>সহায়তা লাগছে?</h3>
    <p>অর্ডার করতে সমস্যা হলে বা প্রোডাক্ট সম্পর্কে জানতে আমাদের সাথে যোগাযোগ করুন।</p>
    <div class="help-actions">
      <a href="https://m.me/{{$generalsetting->facebook_page_username}}" target="_blank">
        <button>মেসেঞ্জার</button>
      </a>
      <a href="https://wa.me/{{ $contact->whatsapp }}" target="_blank">
        <button>হোয়াটসঅ্যাপ</button>
      </a>
      <a href="tel:{{$contact->hotline}}">
        <button>সরাসরি কল</button>
      </a>
    </div>
  </div>
</section>
</main>

<!-- FOOTER -->
<footer>
  <div class="container">
    <div class="footer-grid">
      <div>
        <div class="logo" style="margin-bottom:6px;">
          <div class="logo-circle">
            <img src="{{asset($generalsetting->favicon)}}" alt="logo">
          </div>
          <span>{{ $generalsetting->name }}</span>
        </div>
        <p>
          {!! $campaign_data->short_description ?? 'আমরা খুলনার আসল চুইঝাল সারাদেশে পৌঁছে দিই। গুণগত মানে শতভাগ নিশ্চয়তা দিচ্ছি – একবার ট্রাই করলে পার্থক্য নিজেই বুঝবেন।' !!}
        </p>
      </div>
      <div>
        <div class="footer-title">দ্রুত লিংক</div>
        <div class="footer-links">
          <a href="#features">বৈশিষ্ট্য</a>
          <a href="#reviews">কাস্টমার রিভিউ</a>
          <a href="#faq">প্রশ্নোত্তর</a>
          <a href="#order">অর্ডার ফর্ম</a>
        </div>
      </div>
      <div>
        <div class="footer-title">যোগাযোগ</div>
        <div class="footer-links">
          <a href="#">মোবাইল: {{$contact->hotline}}</a>
          <a href="#">ইমেইল: {{$contact->email}}</a>
          <a href="#">ঠিকানাঃ {{$contact->address}}</a>
          <a href="https://facebook.com/{{$generalsetting->facebook_page_username}}">ফেইস বুক পেইজ</a>
        </div>
      </div>
    </div>

    <div class="footer-bottom">
      <span>© সকল কিছুর স্বত্বাধিকার {{ $generalsetting->name }} | সকল কারিগরি সহযোগিতায়ঃ ক্রিয়েটিভ ডিজাইন</span>
    </div>
  </div>
</footer>

<script>
  // smooth scroll
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener("click", function(e) {
          const target = document.querySelector(this.getAttribute("href"));
          if(!target) return;
          e.preventDefault();
          target.scrollIntoView({ behavior: "smooth" });
      });
  });

  // FAQ টগল
  document.querySelectorAll(".faq-item").forEach(function(item){
    item.querySelector(".faq-question").addEventListener("click",function(){
      item.classList.toggle("open");
      item.querySelector(".toggle").textContent = item.classList.contains("open") ? "−" : "+";
    });
  });

  // উপরের product grid থেকে অর্ডার বাটনে ক্লিক করলে নিচের order সেকশনে select + scroll
  document.querySelectorAll(".cp-order-btn, .cp-cart-icon").forEach(btn => {
    btn.addEventListener('click', function(e){
      e.preventDefault();
      const pid  = this.dataset.productId;
      const card = document.querySelector('.product-card[data-id="'+pid+'"]');
      if(!card) return;

      const qtyInput = card.querySelector(".qty-input");
      const checkbox = card.querySelector(".prod-check");

      const currentQty = parseInt(qtyInput.value || "0");
      qtyInput.value   = currentQty > 0 ? currentQty : 1;
      checkbox.checked = true;

      checkbox.dispatchEvent(new Event('change'));

      const orderSection = document.getElementById('order');
      if(orderSection){
        orderSection.scrollIntoView({behavior:'smooth'});
      }
    });
  });

  // ORDER + CART JS
  (function(){
    let shippingSpan   = document.getElementById("shipping");
    let shipping       = parseInt(shippingSpan.dataset.amount || "0");
    const subtotalEl   = document.getElementById("subtotal");
    const totalEl      = document.getElementById("total");
    const tbl          = document.getElementById("summaryTableBody");
    const shippingSelect = document.getElementById("area");

    function refreshSummary(){
      tbl.innerHTML = "";
      let subtotal = 0;

      document.querySelectorAll(".product-card").forEach(card => {
        const id    = card.dataset.id;
        const name  = card.dataset.name;
        const price = parseInt(card.dataset.price);
        const qty   = parseInt(card.querySelector(".qty-input").value || "0");
        const line  = qty * price;

        card.querySelector(".line-total").textContent = line;
        card.querySelector(".hidden-qty").value = qty;

        if(qty > 0){
          card.classList.add("selected");
          card.querySelector(".prod-check").checked = true;

          tbl.insertAdjacentHTML('beforeend', `
            <tr>
              <td>${name}</td>
              <td style="text-align:center">
                <div class="mini-qty" data-id="${id}">
                  <button type="button" class="mini-minus">-</button>
                  <input readonly value="${qty}">
                  <button type="button" class="mini-plus">+</button>
                </div>
              </td>
              <td style="text-align:right">৳ ${line}</td>
            </tr>
          `);

          subtotal += line;
        }else{
          card.classList.remove("selected");
          card.querySelector(".prod-check").checked = false;
        }
      });

      subtotalEl.textContent = subtotal;
      shippingSpan.textContent = shipping;
      totalEl.textContent = subtotal > 0 ? (subtotal + shipping) : 0;

      bindMiniButtons();
    }

    function bindQtyButtons(){
      document.querySelectorAll(".qty-plus").forEach(btn=>{
        btn.onclick = function(){
          const input = this.parentNode.querySelector(".qty-input");
          input.value = parseInt(input.value || "0") + 1;
          refreshSummary();
        };
      });
      document.querySelectorAll(".qty-minus").forEach(btn=>{
        btn.onclick = function(){
          const input = this.parentNode.querySelector(".qty-input");
          const v = parseInt(input.value || "0");
          if(v>0) input.value = v-1;
          refreshSummary();
        };
      });
    }

    function bindMiniButtons(){
      document.querySelectorAll(".mini-plus").forEach(btn=>{
        btn.onclick = function(){
          const id   = this.parentNode.dataset.id;
          const card = document.querySelector(`.product-card[data-id="${id}"]`);
          const input= card.querySelector(".qty-input");
          input.value = parseInt(input.value || "0") + 1;
          refreshSummary();
        };
      });
      document.querySelectorAll(".mini-minus").forEach(btn=>{
        btn.onclick = function(){
          const id   = this.parentNode.dataset.id;
          const card = document.querySelector(`.product-card[data-id="${id}"]`);
          const input= card.querySelector(".qty-input");
          const v    = parseInt(input.value || "0");
          if(v>0) input.value = v-1;
          refreshSummary();
        };
      });
    }

    // checkbox change → qty toggle
    document.querySelectorAll(".prod-check").forEach(ch=>{
      ch.onchange = function(){
        const card  = this.closest(".product-card");
        const input = card.querySelector(".qty-input");

        if(this.checked && parseInt(input.value || "0")===0){
          input.value = 1;
        }
        if(!this.checked){
          input.value = 0;
        }
        refreshSummary();
      };
    });

    // shipping area select → shipping amount change + backend session update
    if (shippingSelect) {
      shippingSelect.addEventListener('change', function(){
        const opt = this.options[this.selectedIndex];
        shipping = parseInt(opt.dataset.amount || "0");
        refreshSummary();

        fetch('{{ route("shipping.charge") }}?id=' + this.value, {
          method: 'GET'
        }).catch(err => console.error(err));
      });
    }

    // ফর্ম সাবমিটের আগে কমপক্ষে ১টা প্রোডাক্ট চেক করো
    document.getElementById('checkoutForm').addEventListener('submit', function(e){
      let any = false;
      document.querySelectorAll(".hidden-qty").forEach(el=>{
        if(parseInt(el.value || "0") > 0) any = true;
      });

      if(!any){
        e.preventDefault();
        alert("⚠️ অন্তত একটি পণ্য সিলেক্ট করুন!");
      }
    });

    bindQtyButtons();
    refreshSummary();
  })();
</script>
</body>
</html>
