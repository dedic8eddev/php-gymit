<div class="page has-sidebar-left has-sidebar-tabs height-full">
    <?php $this->load->view('layout/alerts'); ?>

    <div class="container-fluid relative">
        <div class="row">
            <div class="col-md-12 mt-3">
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="card r-0 shadow">
                            <div class="card-header white">
                                <h6 class="pull-left">Nastavení stránky</h6>
                            </div>
                            
                            <div class="card-body white">
                                <label for="current_site">Zobrazení hlavní stránky</label>
                                <select name="current_site" id="current_site" class="form-control mt-1 mb-3">
                                    <option <?php echo ($current_site == NULL) ? "selected" : ""; ?> value="NULL">Klasické</option>
                                    <option <?php echo ($current_site == "predprodej") ? "selected" : ""; ?> value="predprodej">Předprodej</option>
                                    <option <?php echo ($current_site == "maintenance") ? "selected" : ""; ?> value="maintenance">Údržba</option>
                                </select>
                            </div>

                            <div class="card-footer white">
                            <a class="btn btn-primary save-site-settings">Uložit</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
