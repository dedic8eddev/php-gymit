<div class="container-fluid relative animatedParent animateOnce">
        <div class="row">
<?php if($this->session->flashdata('success')): ?>
        <div class="col-md-12 mt-3">
            <div class="alert alert-success" role="alert">
                <span><?php echo $this->session->flashdata('success'); ?></span>
            </div>
        </div>
<?php endif; ?>

<?php if($this->session->flashdata('error')): ?>
        <div class="col-md-12 mt-3">
            <div class="alert alert-danger" role="alert">
                <span><?php echo $this->session->flashdata('error'); ?></span>
            </div>
        </div>
<?php endif; ?>

<?php echo validation_errors('<div class="col-md-12 my-3"><div class="alert alert-danger" role="alert"><span>', '</span></div></div>'); ?>

        </div>
</div>