<?php $__env->startSection('title', $seo->meta_title ?? 'Home'); ?>

<?php $__env->startPush('seo'); ?>
<meta name="app-url" content="<?php echo e(url('/')); ?>" />
<meta name="robots" content="index, follow" />

<meta name="description" content="<?php echo e($seo->meta_description ?? ''); ?>" />
<meta name="keywords" content="<?php echo e($seo->meta_tags ?? ''); ?>" />

<!-- Open Graph data -->
<meta property="og:title" content="<?php echo e($seo->meta_title ?? ''); ?>" />
<meta property="og:type" content="website" />
<meta property="og:url" content="<?php echo e(url()->current()); ?>" />
<meta property="og:image" content="<?php echo e(asset($generalsetting->og_baner ?? 'public/logo.png')); ?>" />
<meta property="og:description" content="<?php echo e($seo->meta_description ?? ''); ?>" />
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<section class="slider-section">
    <div class="container">
        <div class="row">

            
            <div class="col-sm-3 hidetosm">
                <div class="sidebar-menu">
                    <ul class="hideshow">
                        <?php $__currentLoopData = $menucategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li>
                                <a href="<?php echo e(route('category', $category->slug)); ?>" style="text-decoration: none;">
                                    <img src="<?php echo e(asset($category->icon)); ?>"
                                         alt="<?php echo e($category->name); ?>"
                                         class="side_cat_img"
                                         loading="lazy" />
                                    <span style="color: #000;"><?php echo e($category->name); ?></span>
                                    <i class="fa-solid fa-chevron-right" style="color: #000;"></i>
                                </a>

                                <?php if($category->subcategories && $category->subcategories->count() > 0): ?>
                                <ul class="sidebar-submenu">
                                    <?php $__currentLoopData = $category->subcategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subcategory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li>
                                            <a href="<?php echo e(route('subcategory', $subcategory->slug)); ?>"
                                               style="color: #000; text-decoration: none;">
                                                <?php echo e($subcategory->subcategoryName); ?>

                                                <i class="fa-solid fa-chevron-right"></i>
                                            </a>
                                            <?php if($subcategory->childcategories && $subcategory->childcategories->count() > 0): ?>
                                            <ul class="sidebar-childmenu">
                                                <?php $__currentLoopData = $subcategory->childcategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $childcat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <li>
                                                        <a href="<?php echo e(route('products', $childcat->slug)); ?>"
                                                           style="color: #000; text-decoration: none;">
                                                            <?php echo e($childcat->childcategoryName); ?>

                                                        </a>
                                                    </li>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </ul>
                                            <?php endif; ?>
                                        </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            </div>

            
            <div class="col-sm-9">
                <div class="home-slider-container">
                    <div class="main_slider owl-carousel">
                        <?php $__currentLoopData = $sliders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="slider-item">
                                <img src="<?php echo e(asset($value->image)); ?>"
                                     alt="Slider"
                                     class="img-fluid w-100" />
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
<!-- slider end -->


<section class="bottoads_area">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="bottoads_inner">
                    <?php $__currentLoopData = $sliderbottomads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="ads_item">
                            <a href="<?php echo e($value->link); ?>">
                                <img src="<?php echo e(asset($value->image)); ?>"
                                     alt="Ads"
                                     class="img-fluid"
                                     loading="lazy" />
                            </a>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="homeproduct">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="sec_title">
                    <h3 class="section-title-header">
                        <div class="timer_inner">
                            <div>
                                <span class="section-title-name"> Categories </span>
                            </div>
                        </div>
                    </h3>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="category-slider owl-carousel">
                    <?php $__currentLoopData = $menucategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="cat_item">
                            <div class="cat_img">
                                <a href="<?php echo e(route('category', $value->slug)); ?>">
                                    <img src="<?php echo e(asset($value->image)); ?>"
                                         alt="<?php echo e($value->name); ?>"
                                         class="img-fluid"
                                         loading="lazy" />
                                </a>
                            </div>
                            <div class="cat_name">
                                <a href="<?php echo e(route('category', $value->slug)); ?>"
                                   style="color: #000; text-decoration: none;">
                                    <?php echo e($value->name); ?>

                                </a>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </div>
</section>


<section>
    <div class="container">
        <div class="row">
            <?php $__currentLoopData = $hitdealsbaner; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hotads): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-md-12">
                <a href="<?php echo e($hotads->link); ?>?sold=show">
                    <img class="img-fluid w-100"
                         src="<?php echo e(asset($hotads->image)); ?>"
                         alt="Hot Deals Banner"
                         loading="lazy" />
                </a>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>


<section class="homeproduct">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="sec_title">
                    <h3 class="section-title-header">
                        <div class="timer_inner">
                            <div>
                                <span class="section-title-name"> Hot Deal </span>
                            </div>
                            <div>
                                <div class="offer_timer" id="simple_timer"></div>
                            </div>
                        </div>
                    </h3>
                </div>
            </div>

            <div class="col-sm-12">
                <div class="product_slider owl-carousel">
                    <?php $__currentLoopData = $hotdeal_top; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="product_item wist_item wow zoomIn"
                             data-wow-duration="1.5s"
                             data-wow-delay="0.<?php echo e($key); ?>s">
                            <div class="product_item_inner">
                                <?php if($value->old_price): ?>
                                <div class="sale-badge">
                                    <div class="sale-badge-inner">
                                        <div class="sale-badge-box">
                                            <span class="sale-badge-text">
                                                <p>
                                                    <?php
                                                        $discount = ((($value->old_price - $value->new_price) * 100) / $value->old_price);
                                                    ?>
                                                    <?php echo e(number_format($discount, 0)); ?>%
                                                </p>
                                                ছাড়
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <div class="pro_img">
                                    <a href="<?php echo e(route('product', $value->slug)); ?>">
                                        <img src="<?php echo e(asset($value->image ? $value->image->image : '')); ?>"
                                             alt="<?php echo e($value->name); ?>"
                                             class="img-fluid"
                                             loading="lazy" />
                                    </a>
                                </div>

                                <div class="pro_des">
                                    <div class="pro_name">
                                        <a href="<?php echo e(route('product', $value->slug)); ?>">
                                            <?php echo e(Str::limit($value->name, 35)); ?>

                                        </a>
                                    </div>
                                </div>
                            </div>

                            <?php
                                $averageRating = $value->reviews->avg('ratting');
                                $filledStars   = floor($averageRating);
                                $hasHalfStar   = $averageRating - $filledStars >= 0.5;
                                $emptyStars    = 5 - $filledStars - ($hasHalfStar ? 1 : 0);
                            ?>

                            <?php if($averageRating >= 0 && $averageRating <= 5): ?>
                                <?php for($i = 0; $i < $filledStars; $i++): ?>
                                    <i class="fas fa-star"></i>
                                <?php endfor; ?>
                                <?php if($hasHalfStar): ?>
                                    <i class="fas fa-star-half-alt"></i>
                                <?php endif; ?>
                                <?php for($i = 0; $i < $emptyStars; $i++): ?>
                                    <i class="far fa-star"></i>
                                <?php endfor; ?>
                            <?php else: ?>
                                <span>Invalid rating range</span>
                            <?php endif; ?>

                            <div class="pro_price">
                                <p>
                                    <?php if($value->old_price): ?>
                                        <del>৳ <?php echo e($value->old_price); ?></del>
                                    <?php endif; ?>
                                    ৳ <?php echo e($value->new_price); ?>

                                </p>
                            </div>

                            
                            <?php if(!$value->prosizes->isEmpty() || !$value->procolors->isEmpty()): ?>
                                
                                <div class="pro_btn">
                                    <a href="<?php echo e(route('product', $value->slug)); ?>" class="order-btn-link">
                                        অর্ডার করুন
                                    </a>
                                    <a href="<?php echo e(route('product', $value->slug)); ?>" class="cart-icon-link">
                                        <i class="fa-solid fa-cart-shopping"></i>
                                    </a>
                                </div>
                            <?php else: ?>
                                
                                <div class="pro_btn">
                                    <form action="<?php echo e(route('cart.store')); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="id" value="<?php echo e($value->id); ?>" />
                                        <input type="hidden" name="qty" value="1" />
                                        <input type="hidden" name="order_now" value="1">
                                        <button type="submit" class="order-btn">অর্ডার করুন</button>
                                    </form>

                                    <form action="<?php echo e(route('cart.store')); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="id" value="<?php echo e($value->id); ?>" />
                                        <input type="hidden" name="qty" value="1" />
                                        <button type="submit" class="cart-icon-btn">
                                            <i class="fa-solid fa-cart-shopping"></i>
                                        </button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

        </div>
    </div>
</section>


<section>
    <div class="container">
        <div class="row">
            <?php $__currentLoopData = $homepageads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $homeads): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-md-12">
                <a href="<?php echo e($homeads->link); ?>?sold=show">
                    <img class="img-fluid w-100"
                         src="<?php echo e(asset($homeads->image)); ?>"
                         alt="Homepage Ads"
                         loading="lazy" />
                </a>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>


<?php if($homeproducts && $homeproducts->count() > 0): ?>
    <?php $__currentLoopData = $homeproducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $homecat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <section class="homeproduct">
            <div class="container">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="sec_title">
                            <h3 class="section-title-header">
                                <span class="section-title-name"><?php echo e($homecat->name); ?></span>
                                <a href="<?php echo e(route('category', $homecat->slug)); ?>" class="view_more_btn">
                                    View More
                                </a>
                            </h3>
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="product_slider owl-carousel">
                            <?php $__currentLoopData = $homecat->products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="product_item wist_item wow zoomIn"
                                     data-wow-duration="1.5s"
                                     data-wow-delay="0.<?php echo e($key); ?>s">
                                    <div class="product_item_inner">
                                        <?php if($value->old_price): ?>
                                        <div class="sale-badge">
                                            <div class="sale-badge-inner">
                                                <div class="sale-badge-box">
                                                    <span class="sale-badge-text">
                                                        <p>
                                                            <?php
                                                                $discount = ((($value->old_price - $value->new_price) * 100) / $value->old_price);
                                                            ?>
                                                            <?php echo e(number_format($discount, 0)); ?>%
                                                        </p>
                                                        ছাড়
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endif; ?>

                                        <div class="pro_img">
                                            <a href="<?php echo e(route('product', $value->slug)); ?>">
                                                <img src="<?php echo e(asset($value->image ? $value->image->image : '')); ?>"
                                                     alt="<?php echo e($value->name); ?>"
                                                     class="img-fluid"
                                                     loading="lazy" />
                                            </a>
                                        </div>

                                        <div class="pro_des">
                                            <div class="pro_name">
                                                <a href="<?php echo e(route('product', $value->slug)); ?>">
                                                    <?php echo e(Str::limit($value->name, 35)); ?>

                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    <?php
                                        $averageRating = $value->reviews->avg('ratting');
                                        $filledStars   = floor($averageRating);
                                        $hasHalfStar   = $averageRating - $filledStars >= 0.5;
                                        $emptyStars    = 5 - $filledStars - ($hasHalfStar ? 1 : 0);
                                    ?>

                                    <?php if($averageRating >= 0 && $averageRating <= 5): ?>
                                        <?php for($i = 0; $i < $filledStars; $i++): ?>
                                            <i class="fas fa-star"></i>
                                        <?php endfor; ?>
                                        <?php if($hasHalfStar): ?>
                                            <i class="fas fa-star-half-alt"></i>
                                        <?php endif; ?>
                                        <?php for($i = 0; $i < $emptyStars; $i++): ?>
                                            <i class="far fa-star"></i>
                                        <?php endfor; ?>
                                    <?php else: ?>
                                        <span>Invalid rating range</span>
                                    <?php endif; ?>

                                    <div class="pro_price">
                                        <p>
                                            <?php if($value->old_price): ?>
                                                <del>৳ <?php echo e($value->old_price); ?></del>
                                            <?php endif; ?>
                                            ৳ <?php echo e($value->new_price); ?>

                                        </p>
                                    </div>

                                    
                                    <?php if(!$value->prosizes->isEmpty() || !$value->procolors->isEmpty()): ?>
                                        <div class="pro_btn">
                                            <a href="<?php echo e(route('product', $value->slug)); ?>" class="order-btn-link">
                                                অর্ডার করুন
                                            </a>
                                            <a href="<?php echo e(route('product', $value->slug)); ?>" class="cart-icon-link">
                                                <i class="fa-solid fa-cart-shopping"></i>
                                            </a>
                                        </div>
                                    <?php else: ?>
                                        <div class="pro_btn">
                                            <form action="<?php echo e(route('cart.store')); ?>" method="POST">
                                                <?php echo csrf_field(); ?>
                                                <input type="hidden" name="id" value="<?php echo e($value->id); ?>" />
                                                <input type="hidden" name="qty" value="1" />
                                                <input type="hidden" name="order_now" value="1">
                                                <button type="submit" class="order-btn">অর্ডার করুন</button>
                                            </form>

                                            <form action="<?php echo e(route('cart.store')); ?>" method="POST">
                                                <?php echo csrf_field(); ?>
                                                <input type="hidden" name="id" value="<?php echo e($value->id); ?>" />
                                                <input type="hidden" name="qty" value="1" />
                                                <button type="submit" class="cart-icon-btn">
                                                    <i class="fa-solid fa-cart-shopping"></i>
                                                </button>
                                            </form>
                                        </div>
                                    <?php endif; ?>

                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>

                </div>
            </div>
        </section>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>


<section>
    <div class="container">
        <div class="row">
            <?php $__currentLoopData = $homepageads2; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $homeads2): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-md-12">
                <a href="<?php echo e($homeads2->link); ?>?sold=show">
                    <img class="img-fluid w-100"
                         src="<?php echo e(asset($homeads2->image)); ?>"
                         alt="Homepage Ads 2"
                         loading="lazy" />
                </a>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>


<?php if(($generalsetting->show_reviews ?? 1) && $reviews->count() > 0): ?>
<section class="homeproduct">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="sec_title">
                    <h5 class="text-center text-light py-2"
                        style="background-color: <?php echo e($generalsetting->primary_color); ?>">
                        Positive reviews from valued customers
                    </h5>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="customer-review owl-carousel">
                    <?php $__currentLoopData = $reviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="border rounded">
                        <img class="img-fluid w-100"
                             src="<?php echo e(asset($review->image)); ?>"
                             alt="Customer Review"
                             loading="lazy" />
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>


<section class="footer_top_ads_area">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="footertop_ads_inner">
                    <?php $__currentLoopData = $footertopads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="footertop_ads_item">
                            <a href="<?php echo e($value->link); ?>">
                                <img src="<?php echo e(asset($value->image)); ?>"
                                     alt="Footer Ads"
                                     class="img-fluid"
                                     loading="lazy" />
                            </a>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('script'); ?>
<script src="<?php echo e(asset('frontEnd/js/jquery.syotimer.min.js')); ?>"></script>
<script>
    $("#simple_timer").syotimer({
        date: new Date(2015, 0, 1),
        layout: "hms",
        doubleNumbers: false,
        effectType: "opacity",
        periodUnit: "d",
        periodic: true,
        periodInterval: 1,
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('frontEnd.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\ecom2\resources\views/frontEnd/layouts/pages/index.blade.php ENDPATH**/ ?>