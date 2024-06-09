<div class="page has-sidebar-left has-sidebar-tabs height-full">

    <header class="white relative nav-sticky">
        <div class="container-fluid text-primary">
            <div class="row justify-content-between">
                <ul class="nav nav-material nav-material-white responsive-tab" id="v-pills-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="v-pills-all-tab" data-toggle="tab" href="#v-pills-all" role="tab" aria-controls="v-pills-all"><i class="icon-box6"></i>&nbsp;Detail</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="v-pills-grn-tab" data-toggle="tab" href="#v-pills-grn" role="tab" aria-controls="v-pills-grn"><i class="icon-arrow-circle-o-down"></i>&nbsp;Příjemky</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="v-pills-gdn-tab" data-toggle="tab" href="#v-pills-gdn" role="tab" aria-controls="v-pills-gdn"><i class="icon-arrow-circle-o-up"></i>&nbsp;Výdejky</a>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <?php $this->load->view('layout/alerts'); ?>

    <div class="container-fluid relative">

        <div class="tab-content my-3" id="v-pills-tabContent">
        
            <div class="tab-pane animated fadeInUpShort go" id="v-pills-gdn" role="tabpanel" aria-labelledby="v-pills-gdn-tab">
                <div class="d-flex row">
                    <div class="col-md-12">
                        <div class="card mb-3 shadow no-b r-0">
                            <div class="card-header white">
                                <h6>Přehled výdejek</h6>
                            </div>
                            <div class="card-body">
                                <table id="depot_item_gdns" data-ajax="<?php echo $gdnsUrl; ?>"></table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane animated fadeInUpShort go" id="v-pills-grn" role="tabpanel" aria-labelledby="v-pills-grn-tab">
                <div class="d-flex row">
                    <div class="col-md-12">
                        <div class="card mb-3 shadow no-b r-0">
                            <div class="card-header white">
                                <h6>Přehled příjemek</h6>
                            </div>
                            <div class="card-body">
                                <table id="depot_item_grns" data-ajax="<?php echo $grnsUrl; ?>"></table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane animated fadeInUpShort show active go" id="v-pills-all" role="tabpanel" aria-labelledby="v-pills-all-tab">
        <div class="d-flex row">
            <div class="col-md-12">
                <div class="card mb-3 shadow no-b r-0">
                    <div class="card-header white">
                        <h6>Informace o položce</h6>
                    </div>
                    <div class="card-body">
                        <form id="js_depot_item_edit_form">
                            <div class="form-row">
                                <div class="col-md-6 mb-3">
                                    <label for="name">Název</label>
                                    <input type="text" name="name" class="form-control" id="name" placeholder="Název" value="<?php echo $item['name']; ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="number">Číslo položky</label>
                                    <input type="text" name="number" class="form-control" id="number" placeholder="Číslo položky" value="<?php echo $item['number']; ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-md-4 mb-3">
                                        <label for="price">Cena za jednotku (bez DPH)</label>
                                        <input type="number" min="0" step="0.01" name="price" id="price" class="form-control" placeholder="Cena za jednotku" value="<?php echo $item['price']; ?>" required>
                                    </div>
                                <div class="col-md-4 mb-3">
                                    <label for="unit">Jednotka</label>
                                    <input type="text" name="unit" class="form-control" id="unit" placeholder="Jednotka" required value="<?php echo $item['unit']; ?>">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="stock">Množství</label>
                                    <input type="number" min="0" step="0.01" name="stock" id="stock" class="form-control" placeholder="Množství" required readonly value="<?php echo $item['stock']; ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-md-12 mb-3">
                                    <label for="description">Popis</label>
                                    <textarea name="description" id="description" class="form-control" rows="3" placeholder="Popis"><?php echo $item['description']; ?></textarea>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-md-12 mb-3">
                                    <label for="note">Poznámka</label>
                                    <textarea name="note" id="note" class="form-control" rows="2" placeholder="Poznámka"><?php echo $item['note']; ?></textarea>
                                </div>
                            </div>

                            <?php $this->app_components->getCustomFields($custom_fields, [], $custom_fields_values); ?>

                            <div class="form-row">
                                <div class="col-md-6 mb-3">
                                    <label for="active">Aktivní</label>
                                    <select name="active" id="active" class="form-control" required>
                                        <option value="1" <?php echo ($item['active'] == 1) ? 'selected' : ''; ?>>Ano</option>
                                        <option value="0" <?php echo ($item['active'] == 0) ? 'selected' : ''; ?>>Ne</option>
                                    </select>
                                </div>
                            </div>
                            
                            <button class="btn btn-primary" id="js_depot_item_edit_form_btn" data-ajax="<?php echo $editItem; ?>" type="submit">Uložit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
            </div>

        </div>

    </div>

</div>