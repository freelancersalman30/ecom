<?php $__env->startSection('title','Product Manage'); ?>
<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="<?php echo e(route('products.create')); ?>" class="btn btn-danger rounded-pill"><i class="fe-shopping-cart"></i> Add Product</a>
                </div>
                <h4 class="page-title">Product Manage</h4>
            </div>
        </div>
    </div>       
    <!-- end page title --> 
   <div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-8">
                        <ul class="action2-btn">
                            <li><a href="<?php echo e(route('products.update_deals',['status'=>1])); ?>" class="btn rounded-pill btn-success hotdeal_update"><i class="fe-thumbs-up"></i> Deal</a></li>
                            <li><a href="<?php echo e(route('products.update_deals',['status'=>0])); ?>" class="btn  rounded-pill btn-danger hotdeal_update"><i class="fe-thumbs-down"></i> Deal</a></li>
                            
                            <li><a href="<?php echo e(route('products.update_status',['status'=>1])); ?>" class="btn rounded-pill btn-primary update_status"><i class="fe-thumbs-up"></i> Active</a></li>
                            <li><a href="<?php echo e(route('products.update_status',['status'=>0])); ?>" class="btn  rounded-pill btn-warning update_status"><i class="fe-thumbs-down"></i> Inactive</a></li>
                        </ul>
                    </div>
                    <div class="col-sm-4">
                        <form class="custom_form">
                            <div class="form-group">
                                <input type="text" name="keyword" placeholder="Search">
                                <button class="btn  rounded-pill btn-info">Search</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table nowrap w-100">
                    <thead>
                        <tr>
                            <th style="width:2%"><div class="form-check"><label class="form-check-label"><input type="checkbox" class="form-check-input checkall" value=""></label>
                            <th style="width:2%">SL</th>
                                    </div></th>
                            <th style="width:10%">Action</th>
                            <th style="width:20%">Name</th>
                            <th style="width:10%">Category</th>
							<th style="width:10%">Type</th>
                            <th style="width:10%">Image</th>
                            <th style="width:10%">Price</th>
                            <th style="width:8%">Stock</th>
                            <th style="width:14%">Deal & Feature</th>
                            <th style="width:8%">Status</th>
                        </tr>
                    </thead>               
                
                    <tbody>
                        <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><input type="checkbox" class="checkbox" value="<?php echo e($value->id); ?>"></td>
                            <td><?php echo e($loop->iteration); ?></td>
                            <td>
                                <div class="button-list custom-btn-list">
                                    <?php if($value->status == 1): ?>
                                    <form method="post" action="<?php echo e(route('products.inactive')); ?>" class="d-inline"> 
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" value="<?php echo e($value->id); ?>" name="hidden_id">       
                                    <button type="button" class="change-confirm" title="Active"><i class="fe-thumbs-down"></i></button></form>
                                    <?php else: ?>
                                    <form method="post" action="<?php echo e(route('products.active')); ?>" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                    <input type="hidden" value="<?php echo e($value->id); ?>" name="hidden_id">        
                                    <button type="button" class="change-confirm" title="Inactive"><i class="fe-thumbs-up"></i></button></form>
                                    <?php endif; ?>

                                    <a href="<?php echo e(route('products.edit',$value->id)); ?>" title="Edit"><i class="fe-edit"></i></a>

                                    <form method="post" action="<?php echo e(route('products.destroy')); ?>" class="d-inline">        
                                        <?php echo csrf_field(); ?>
                                    <input type="hidden" value="<?php echo e($value->id); ?>" name="hidden_id">
                                    <button type="submit" class="delete-confirm" title="Delete"><i class="fe-trash-2"></i></button></form>
                                     <!-- <a href="<?php echo e(route('products.edit',$value->id)); ?>" title="Copy"><i class="fe-copy"></i></a> -->
                                </div>
                            </td>
                            <td><?php echo e($value->name); ?></td>
                            <td><?php echo e($value->category?$value->category->name:''); ?></td>
							 <td>    <?php
        if (isset($value->is_digital)) {
            $isDigital = (bool) $value->is_digital;
        } elseif (isset($value->product_type)) {
            $isDigital = $value->product_type === 'digital';
        } else {
            $isDigital = false;
        }
    ?>

    <?php if($isDigital): ?>
        <span class="badge bg-soft-primary text-primary">Digital</span>
    <?php else: ?>
        <span class="badge bg-soft-info text-info">Physical</span>
    <?php endif; ?></td>
                            <td><img src="<?php echo e(asset($value->image?$value->image->image:'')); ?>" class="backend-image" alt=""></td>
                            <td><?php echo e($value->new_price); ?></td>
                            <td><?php echo e($value->stock); ?></td>
                            <td><p class="m-0">Hot Deals : <?php echo e($value->topsale==1?'Yes':'No'); ?></p>
                                <p class="m-0">Top Feature : <?php echo e($value->feature_product==1?'Yes':'No'); ?></p></td>
                            <td><?php if($value->status==1): ?><span class="badge bg-soft-success text-success">Active</span> <?php else: ?> <span class="badge bg-soft-danger text-danger">Inactive</span> <?php endif; ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                     </tbody>
                    </table>
                </div>
                <div class="custom-paginate">
                    <?php echo e($data->links('pagination::bootstrap-4')); ?>

                </div>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
   </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){
    $(".checkall").on('change',function(){
      $(".checkbox").prop('checked',$(this).is(":checked"));
    });
    
    $(document).on('click', '.hotdeal_update', function(e){
        e.preventDefault();
        var url = $(this).attr('href');
        console.log('url',url);
        var product = $('input.checkbox:checked').map(function(){
          return $(this).val();
        });
        var product_ids=product.get();
        if(product_ids.length ==0){
            toastr.error('Please Select A Product First !');
            return ;
        }
        $.ajax({
           type:'GET',
           url:url,
           data:{product_ids},
           success:function(res){
               if(res.status=='success'){
                toastr.success(res.message);
                window.location.reload();
            }else{
                toastr.error('Failed something wrong');
            }
           }
        });
    });
    $(document).on('click', '.update_status', function(e){
        e.preventDefault();
        var url = $(this).attr('href');
        var product = $('input.checkbox:checked').map(function(){
          return $(this).val();
        });
        var product_ids=product.get();
        if(product_ids.length ==0){
            toastr.error('Please Select A Product First !');
            return ;
        }
        $.ajax({
           type:'GET',
           url:url,
           data:{product_ids},
           success:function(res){
               if(res.status=='success'){
                toastr.success(res.message);
                window.location.reload();
            }else{
                toastr.error('Failed something wrong');
            }
           }
        });
    });
    $(document).on('click', '.update_status', function(e){
        e.preventDefault();
        var url = $(this).attr('href');
        var product = $('input.checkbox:checked').map(function(){
          return $(this).val();
        });
        var product_ids=product.get();
        if(product_ids.length ==0){
            toastr.error('Please Select A Product First !');
            return ;
        }
        $.ajax({
           type:'GET',
           url:url,
           data:{product_ids},
           success:function(res){
               if(res.status=='success'){
                toastr.success(res.message);
                window.location.reload();
            }else{
                toastr.error('Failed something wrong');
            }
           }
        });
    });
    
    
})
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('backEnd.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\ecom2\resources\views/backEnd/product/index.blade.php ENDPATH**/ ?>