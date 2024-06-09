<div class="page has-sidebar-left has-sidebar-tabs height-full">
    <?php $this->load->view('layout/alerts'); ?>

    <div class="container-fluid relative animatedParent animateOnce">
        <div class="row">
            <?php $this->load->view('layout/alerts'); ?>
        </div>

        <div class="row my-3">
                <div class="row col-12 justify-content-between">
                    <div class="col-md-5">
                        <form class="form-inline ml-auto">
                            <select class="form-control type-select">
                                <option value="ALL">Všechny</option>
                            </select>
                            <select class="custom-select my-1 mr-sm-2 ml-2 form-control limit-select">
                                <option value="10">10</option> 
                                <option value="20">20</option> 
                                <option value="50">50</option> 
                                <option value="100">100</option>
                            </select>
                        </form>
                    </div> 
                </div>

                <div class="row col-12 mb-3 my-3 notifications-container">
                    <!-- Dynamic -->
                </div>

                <div class="row col-12 mb-3 my-3">
                    <ul class="pagination" style="padding-left: 15px;">
                        <!-- dynamic -->
                    </ul>
                </div>
        </div>
    </div>
</div>
