<!DOCTYPE html>
<html lang="en" dir="ltr" data-bs-theme="dark" data-color-theme="Blue_Theme" data-layout="vertical">

<head>
    <!-- Required meta tags -->
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title><?php echo e($settings->name); ?> | <?php echo $__env->yieldContent('title'); ?></title>

    <!-- Favicon icon-->
    <link rel="shortcut icon" type="image/png" href="<?php echo e('backend/dist/assets/images/logos/favicon.png'); ?>" />

    <?php echo $__env->make('layouts.backend.styles', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</head>

<body>
    <?php echo $__env->make('layouts.backend.partial.toast', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <!-- Preloader -->
    <div class="preloader">
        <img src="<?php echo e(asset('backend/dist/assets/images/logos/favicon.png')); ?>" alt="loader"
            class="lds-ripple img-fluid" />
    </div>
    <div id="main-wrapper">
        <!-- Sidebar Start -->
        <?php echo $__env->make('layouts.backend.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <!--  Sidebar End -->
        <div class="page-wrapper">
            <!--  Header Start -->
            <?php echo $__env->make('layouts.backend.partial.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <!--  Header End -->

            <?php echo $__env->make('layouts.backend.left-sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

            <div class="body-wrapper">
                <div class="container-fluid">
                    <?php echo $__env->yieldContent('content'); ?>
                </div>
            </div>
            <?php echo $__env->make('layouts.backend.partial.script', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

            <?php echo $__env->make('layouts.backend.partial.canvas', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>

        <!--  Search Bar -->
        <?php echo $__env->make('layouts.backend.partial.search', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <!--  Shopping Cart -->
        <?php echo $__env->make('layouts.backend.partial.cart', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>
    <div class="dark-transparent sidebartoggler"></div>

    <?php echo $__env->make('layouts.backend.scripts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</body>

</html>
<?php /**PATH C:\Joki\sistem-manajemen-tugas\resources\views/layouts/backend/master.blade.php ENDPATH**/ ?>