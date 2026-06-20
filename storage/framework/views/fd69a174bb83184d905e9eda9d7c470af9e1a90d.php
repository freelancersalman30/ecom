<?php $__env->startSection('title','General Setting Update'); ?>
<?php $__env->startSection('css'); ?>
<link href="<?php echo e(asset('backEnd')); ?>/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo e(asset('backEnd')); ?>/assets/libs/summernote/summernote-lite.min.css" rel="stylesheet" type="text/css" />
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="<?php echo e(route('settings.index')); ?>" class="btn btn-primary rounded-pill">Manage</a>
                </div>
                <h4 class="page-title">General Setting Update</h4>
            </div>
        </div>
    </div>       
    <!-- end page title --> 
   <div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <form action="<?php echo e(route('settings.update')); ?>" method="POST" class=row data-parsley-validate=""  enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="id" value="<?php echo e($edit_data->id); ?>">
                    <div class="col-sm-12">
                        <div class="form-group mb-3">
                            <label for="name" class="form-label">Name *</label>
                            <input type="text" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="name" value="<?php echo e($edit_data->name); ?>" id="name" required="">
                            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="invalid-feedback" role="alert">
                                    <strong><?php echo e($message); ?></strong>
                                </span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
					

					
					<div class="col-sm-12">
                        <div class="form-group mb-3">
                            <label for="facebook_page_username" class="form-label">Facebook Page Username
*</label>
                            <input type="text" class="form-control <?php $__errorArgs = ['facebook_page_username'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="facebook_page_username" value="<?php echo e($edit_data->facebook_page_username); ?>" id="facebook_page_username" required="">
                            <?php $__errorArgs = ['facebook_page_username'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="invalid-feedback" role="alert">
                                    <strong><?php echo e($message); ?></strong>
                                </span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
					
					<div class="row mt-3">
    <!-- Primary Color -->
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="form-group">
            <label for="primary_color" class="form-label">Primary Color</label>
            <div class="d-flex align-items-center gap-2">
                <input type="color" 
                       name="primary_color" 
                       id="primary_color"
                       value="<?php echo e(old('primary_color', $edit_data->primary_color ?? '#0d6efd')); ?>"
                       class="form-control form-control-color" 
                       style="width: 60px; height: 38px; cursor: pointer;">
                <input type="text"
                       value="<?php echo e(old('primary_color', $edit_data->primary_color ?? '#0d6efd')); ?>"
                       class="form-control"
                       oninput="document.getElementById('primary_color').value=this.value;">
            </div>
        </div>
    </div>

    <!-- Secondary Color -->
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="form-group">
            <label for="secodery_color" class="form-label">Secondary Color</label>
            <div class="d-flex align-items-center gap-2">
                <input type="color" 
                       name="secodery_color" 
                       id="secodery_color"
                       value="<?php echo e(old('secodery_color', $edit_data->secodery_color ?? '#198754')); ?>"
                       class="form-control form-control-color" 
                       style="width: 60px; height: 38px; cursor: pointer;">
                <input type="text"
                       value="<?php echo e(old('secodery_color', $edit_data->secodery_color ?? '#198754')); ?>"
                       class="form-control"
                       oninput="document.getElementById('secodery_color').value=this.value;">
            </div>
        </div>
    </div>

    <!-- Footer Color -->
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="form-group">
            <label for="footer_color" class="form-label">Footer Color</label>
            <div class="d-flex align-items-center gap-2">
                <input type="color" 
                       name="footer_color" 
                       id="footer_color"
                       value="<?php echo e(old('footer_color', $edit_data->footer_color ?? '#222222')); ?>"
                       class="form-control form-control-color" 
                       style="width: 60px; height: 38px; cursor: pointer;">
                <input type="text"
                       value="<?php echo e(old('footer_color', $edit_data->footer_color ?? '#222222')); ?>"
                       class="form-control"
                       oninput="document.getElementById('footer_color').value=this.value;">
            </div>
        </div>
    </div>

    <!-- Copyright Color -->
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="form-group">
            <label for="copyright_color" class="form-label">Copyright Color</label>
            <div class="d-flex align-items-center gap-2">
                <input type="color" 
                       name="copyright_color" 
                       id="copyright_color"
                       value="<?php echo e(old('copyright_color', $edit_data->copyright_color ?? '#111111')); ?>"
                       class="form-control form-control-color" 
                       style="width: 60px; height: 38px; cursor: pointer;">
                <input type="text"
                       value="<?php echo e(old('copyright_color', $edit_data->copyright_color ?? '#111111')); ?>"
                       class="form-control"
                       oninput="document.getElementById('copyright_color').value=this.value;">
            </div>
        </div>
    </div>
</div>

					
					
					
                    <!-- col-end -->
                    <div class="col-sm-6 mb-3">
                        <div class="form-group">
                            <label for="white_logo" class="form-label">White Logo *</label>
                            <input type="file" class="form-control <?php $__errorArgs = ['white_logo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="white_logo" value="<?php echo e(old('white_logo')); ?>"  id="white_logo" >
                            <img src="<?php echo e(asset($edit_data->white_logo)); ?>" class="edit-image" alt="">
                            <?php $__errorArgs = ['white_logo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="invalid-feedback" role="alert">
                                    <strong><?php echo e($message); ?></strong>
                                </span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    <!-- col end -->
                    <div class="col-sm-6 mb-3">
                        <div class="form-group">
                            <label for="dark_logo" class="form-label">Dark Logo *</label>
                            <input type="file" class="form-control <?php $__errorArgs = ['dark_logo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="dark_logo" value="<?php echo e(old('dark_logo')); ?>"  id="dark_logo" >
                            <img src="<?php echo e(asset($edit_data->dark_logo)); ?>" class="edit-image" alt="">
                            <?php $__errorArgs = ['dark_logo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="invalid-feedback" role="alert">
                                    <strong><?php echo e($message); ?></strong>
                                </span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    <!-- col end -->
                    
                    <div class="col-sm-6 mb-3">
                        <div class="form-group">
                            <label for="favicon" class="form-label">Favicon Logo *</label>
                            <input type="file" class="form-control <?php $__errorArgs = ['favicon'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="favicon" value="<?php echo e(old('favicon')); ?>"  id="favicon" >
                            <img src="<?php echo e(asset($edit_data->favicon)); ?>" class="edit-image" alt="">
                            <?php $__errorArgs = ['favicon'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="invalid-feedback" role="alert">
                                    <strong><?php echo e($message); ?></strong>
                                </span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    <!-- col end -->
					
					                    <div class="col-sm-6 mb-3">
                        <div class="form-group">
                            <label for="og_baner" class="form-label">OG Baner Images *</label>
                            <input type="file" class="form-control <?php $__errorArgs = ['og_baner'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="og_baner" value="<?php echo e(old('og_baner')); ?>"  id="og_baner" >
                            <img src="<?php echo e(asset($edit_data->og_baner)); ?>" class="edit-image" alt="">
                            <?php $__errorArgs = ['og_baner'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="invalid-feedback" role="alert">
                                    <strong><?php echo e($message); ?></strong>
                                </span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    <!-- col end -->
					
                    <div class="col-sm-6 mb-3">
                        <div class="form-group">
                            <label for="app_download_image" class="form-label">App Download Image</label>
                            <input type="file" class="form-control <?php $__errorArgs = ['app_download_image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="app_download_image" value="<?php echo e(old('app_download_image')); ?>"  id="app_download_image" >
                            <?php if($edit_data->app_download_image): ?>
                                <img src="<?php echo e(asset($edit_data->app_download_image)); ?>" class="edit-image" alt="">
                            <?php endif; ?>
                            <?php $__errorArgs = ['app_download_image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="invalid-feedback" role="alert">
                                    <strong><?php echo e($message); ?></strong>
                                </span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    <div class="col-sm-6 mb-3">
                        <div class="form-group">
                            <label for="app_download_link" class="form-label">App Download Link</label>
                            <input type="url" class="form-control <?php $__errorArgs = ['app_download_link'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="app_download_link" value="<?php echo e($edit_data->app_download_link); ?>" id="app_download_link">
                            <?php $__errorArgs = ['app_download_link'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="invalid-feedback" role="alert">
                                    <strong><?php echo e($message); ?></strong>
                                </span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
					
					
					
					
                    <div class="col-sm-6">
                        <div class="form-group mb-3">
                            <label for="hot_deal_end_date" class="form-label">Hot Deal End Date *</label>
                            <input type="date" class="form-control <?php $__errorArgs = ['hot_deal_end_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="hot_deal_end_date" value="<?php echo e($edit_data->hot_deal_end_date); ?>" id="hot_deal_end_date" required="">
                            <?php $__errorArgs = ['hot_deal_end_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="invalid-feedback" role="alert">
                                    <strong><?php echo e($message); ?></strong>
                                </span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group mb-3">
                            <label for="flash_sale_end_date" class="form-label">Flash Sale End Date *</label>
                            <input type="date" class="form-control <?php $__errorArgs = ['flash_sale_end_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="flash_sale_end_date" value="<?php echo e($edit_data->flash_sale_end_date); ?>" id="flash_sale_end_date" required="">
                            <?php $__errorArgs = ['flash_sale_end_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="invalid-feedback" role="alert">
                                    <strong><?php echo e($message); ?></strong>
                                </span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group mb-3">
                            <label for="copyright" class="form-label">Copyright Text</label>
                            <input type="text" class="form-control <?php $__errorArgs = ['copyright'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="copyright" value="<?php echo e($edit_data->copyright); ?>" id="copyright">
                            <?php $__errorArgs = ['copyright'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="invalid-feedback" role="alert">
                                    <strong><?php echo e($message); ?></strong>
                                </span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group mb-3">
                            <label for="designed_by" class="form-label">Designed By (HTML allowed)</label>
                            <input type="text" class="form-control <?php $__errorArgs = ['designed_by'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="designed_by" value="<?php echo e($edit_data->designed_by); ?>" id="designed_by">
                            <?php $__errorArgs = ['designed_by'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="invalid-feedback" role="alert">
                                    <strong><?php echo e($message); ?></strong>
                                </span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group mb-3">
                            <label for="top_headline" class="form-label">Top Headline</label>
                            <textarea class="form-control <?php $__errorArgs = ['top_headline'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="top_headline"  id="top_headline">
                                <?php echo e($edit_data->top_headline); ?>

                            </textarea>
                            <?php $__errorArgs = ['top_headline'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="invalid-feedback" role="alert">
                                    <strong><?php echo e($message); ?></strong>
                                </span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group mb-3">
                            <label for="checkout_note" class="form-label">Checkout Note</label>
                            <textarea class="summernote form-control <?php $__errorArgs = ['checkout_note'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="checkout_note"  id="checkout_note">
                                <?php echo e($edit_data->checkout_note); ?>

                            </textarea>
                            <?php $__errorArgs = ['checkout_note'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="invalid-feedback" role="alert">
                                    <strong><?php echo e($message); ?></strong>
                                </span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group mb-3">
                            <label for="order_policy" class="form-label">Order Policy</label>
                            <textarea class="summernote form-control <?php $__errorArgs = ['order_policy'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="order_policy"  id="order_policy">
                                <?php echo e($edit_data->order_policy); ?>

                            </textarea>
                            <?php $__errorArgs = ['order_policy'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="invalid-feedback" role="alert">
                                    <strong><?php echo e($message); ?></strong>
                                </span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 mb-3">
                        <div class="form-group">
                            <label for="show_all_products" class="d-block">All Products at Home</label>
                            <select class="form-control" name="show_all_products" id="show_all_products">
                                <option value="1" <?php if($edit_data->show_all_products==1): ?>selected <?php endif; ?> >Show</option>
                                <option value="0" <?php if($edit_data->show_all_products==0): ?>selected <?php endif; ?> >Hide</option>
                            </select>
                           
                            <?php $__errorArgs = ['show_all_products'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="invalid-feedback" role="alert">
                                    <strong><?php echo e($message); ?></strong>
                                </span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 mb-3">
                        <div class="form-group">
                            <label for="show_category_wise_products" class="d-block">Categories Wise Products at Home</label>
                            <select class="form-control" name="show_category_wise_products" id="show_category_wise_products">
                                <option value="1" <?php if($edit_data->show_category_wise_products==1): ?>selected <?php endif; ?> >Show</option>
                                <option value="0" <?php if($edit_data->show_category_wise_products==0): ?>selected <?php endif; ?> >Hide</option>
                            </select>
                          
                            <?php $__errorArgs = ['show_category_wise_products'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="invalid-feedback" role="alert">
                                    <strong><?php echo e($message); ?></strong>
                                </span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 mb-3">
                        <div class="form-group">
                            <label for="show_reviews" class="d-block">Customer Reviews at Home</label>
                            <select class="form-control" name="show_reviews" id="show_reviews">
                                <option value="1" <?php if($edit_data->show_reviews==1): ?>selected <?php endif; ?> >Show</option>
                                <option value="0" <?php if($edit_data->show_reviews==0): ?>selected <?php endif; ?> >Hide</option>
                            </select>
                          
                            <?php $__errorArgs = ['show_reviews'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="invalid-feedback" role="alert">
                                    <strong><?php echo e($message); ?></strong>
                                </span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 mb-3">
                        <div class="form-group">
                            <label for="status" class="d-block">Status</label>
                            <label class="switch">
                              <input type="checkbox" disabled value="1" name="status" <?php if($edit_data->status==1): ?>checked <?php endif; ?>>
                              <span class="slider round"></span>
                            </label>
                            <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="invalid-feedback" role="alert">
                                    <strong><?php echo e($message); ?></strong>
                                </span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    <!-- col end -->
                    <div>
                        <input type="submit" class="btn btn-success" value="Submit">
                    </div>

                </form>

            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div> <!-- end col-->
   </div>
</div>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('script'); ?>
<script src="<?php echo e(asset('backEnd/')); ?>/assets/libs/parsleyjs/parsley.min.js"></script>
<script src="<?php echo e(asset('backEnd/')); ?>/assets/js/pages/form-validation.init.js"></script>
<script src="<?php echo e(asset('backEnd/')); ?>/assets/libs/select2/js/select2.min.js"></script>
<script src="<?php echo e(asset('backEnd/')); ?>/assets/js/pages/form-advanced.init.js"></script>
<!-- Plugins js -->
<script src="<?php echo e(asset('backEnd/')); ?>/assets/libs//summernote/summernote-lite.min.js"></script>
<script>
    $(".summernote").summernote({
        placeholder: "Enter Your Text Here",
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('backEnd.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\ecom2\resources\views/backEnd/settings/edit.blade.php ENDPATH**/ ?>