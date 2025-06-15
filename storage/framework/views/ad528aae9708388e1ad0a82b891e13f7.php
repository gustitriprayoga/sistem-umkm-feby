<?php $__env->startSection('title', 'Setting Website'); ?>

<?php $__env->startSection('content'); ?>

    <div class="card bg-info-subtle shadow-none position-relative overflow-hidden mb-4">
        <div class="card-body px-4 py-3">
            <div class="row align-items-center">
                <div class="col-9">
                    <h4 class="fw-semibold mb-8">Website Setting</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a class="text-muted text-decoration-none" href="<?php echo e(route('dashboard.index')); ?>">Home</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">Website Setting</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-3">
                    <div class="text-center mb-n5">
                        <img src="<?php echo e(asset('backend/dist/assets/images/breadcrumb/ChatBc.png')); ?>" alt="modernize-img"
                            class="img-fluid mb-n4" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-info-subtle shadow-none position-relative overflow-hidden mb-4">
        <div class="card-body px-4 py-3">
            <h5>Logo Website</h5>
            <div class="row">
                <div class="col-md-6">
                    <img src="<?php echo e(asset('storage/' . $setting->logo)); ?>" alt="logo" class="img-fluid">
                </div>
                <form action="<?php echo e(route('dashboard.setting.logo')); ?>" method="POST" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="logo" class="form-label">Logo</label>
                                <input class="form-control" type="file" id="logo" name="logo">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </div>
    </div>

    <div class="card bg-info-subtle shadow-none position-relative overflow-hidden mb-4">
        <div class="card-body px-4 py-3">
            <h5>Name Website</h5>

            <form action="<?php echo e(route('dashboard.setting.name')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo e($setting->name); ?>">
                    
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.backend.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Joki\sistem-manajemen-tugas\resources\views/backend/setting/index.blade.php ENDPATH**/ ?>