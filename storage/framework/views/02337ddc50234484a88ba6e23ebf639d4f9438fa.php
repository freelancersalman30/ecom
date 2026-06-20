<?php $__env->startSection('title','Stock Report'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">

    <div class="d-flex align-items-center mb-3">
        <h4 class="mb-0">Stock Report (Live)</h4>
    </div>

    
    <div class="row mb-3">
       <style>
    .card.bg-primary *,
    .card.bg-info *,
    .card.bg-success * {
        color: white !important;
    }
</style>

<div class="col-md-4 mb-2">
    <div class="card bg-primary">
        <div class="card-body">
            <h6 class="mb-1">Total Products</h6>
            <h3 class="mb-0"><?php echo e($products->count()); ?></h3>
        </div>
    </div>
</div>

<div class="col-md-4 mb-2">
    <div class="card bg-info">
        <div class="card-body">
            <h6 class="mb-1">Total Stock Qty</h6>
            <h3 class="mb-0"><?php echo e($totalStockQty); ?></h3>
        </div>
    </div>
</div>

<div class="col-md-4 mb-2">
    <div class="card bg-success">
        <div class="card-body">
            <h6 class="mb-1">Total Stock Value</h6>
            <h3 class="mb-0"><?php echo e(number_format($totalStockValue, 2)); ?> ৳</h3>
        </div>
    </div>
</div>

    </div>

    
    <div class="mb-3">
        <a href="<?php echo e(route('admin.reports.stock',['export'=>'csv'])); ?>" class="btn btn-outline-success">
            ⬇ Export CSV
        </a>
    </div>

    
    <div class="card">
        <div class="card-header">
            <strong>Current Stock</strong>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Product</th>
                    <th class="text-end">Stock Qty</th>
                    <th class="text-end">Purchase Price</th>
                    <th class="text-end">Sale Price</th>
                    <th class="text-end">Stock Value (৳)</th>
                </tr>
                </thead>
                <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $purchasePrice = $p->purchase_price ?? 0;
                        $salePrice     = $p->new_price ?? $p->old_price ?? 0;
                        $stock         = $p->stock ?? 0;
                        $stockValue    = $purchasePrice * $stock;
                    ?>
                    <tr>
                        <td><?php echo e($loop->iteration); ?></td>
                        <td><?php echo e($p->name); ?></td>
                        <td class="text-end"><?php echo e($stock); ?></td>
                        <td class="text-end"><?php echo e(number_format($purchasePrice, 2)); ?></td>
                        <td class="text-end"><?php echo e(number_format($salePrice, 2)); ?></td>
                        <td class="text-end"><?php echo e(number_format($stockValue, 2)); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted">
                            কোনো প্রোডাক্ট পাওয়া যায়নি।
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('backEnd.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\ecom2\resources\views/backEnd/reports/stock.blade.php ENDPATH**/ ?>