<div class="page has-sidebar-left has-sidebar-tabs height-full">
    <header class="white relative nav-sticky">
        <div class="container-fluid text-primary">
            <div class="row justify-content-between">
                <ul class="nav nav-material nav-material-white responsive-tab" id="v-pills-tab" role="tablist">
                    <li>
                        <a class="nav-link active switch-to-users" id="v-pills-all-tab" data-toggle="pill" href="#v-pills-all" role="tab" aria-controls="v-pills-all"><i class="icon icon-users"></i>Přehled uživatelů</a>
                    </li>
                    <li>
                        <a class="nav-link switch-to-inactive-users" id="v-pills-inactive-tab" data-toggle="pill" href="#v-pills-inactive" role="tab" aria-controls="v-pills-inactive"><i class="icon icon-user-times"></i>Neaktivní uživatelé</a>
                    </li>
                    <?php if (hasCreatePermission()): ?>
                    <li>
                        <a class="nav-link" id="v-pills-buyers-tab" data-toggle="pill" href="#v-pills-buyers" role="tab" aria-controls="v-pills-buyers"><i class="icon icon-plus-circle"></i> Nový uživatel</a>
                    </li>
                    <li>
                        <a class="nav-link switch-to-invites" id="v-pills-sellers-tab" data-toggle="pill" href="#v-pills-sellers" role="tab" aria-controls="v-pills-sellers"><i class="icon icon-contact_mail"></i> Poslat pozvánku</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </header>

    <?php $this->load->view('layout/alerts'); ?>

    <div class="container-fluid animatedParent animateOnce">
        <div class="tab-content my-3" id="v-pills-tabContent">
            
            <div class="tab-pane animated fadeInUpShort show active go" id="v-pills-all" role="tabpanel" aria-labelledby="v-pills-all-tab">
                <div class="row my-3">
                    <div class="col-md-12">
                        <div class="card r-0 shadow">
                            <div class="card-header white">
                                <h6 class="pull-left">Seznam všech uživatelů</h6>
                                <button class="btn btn-danger btn-xs float-right" id="js_depot_home_clear_filter">Zrušit filtr</button>
                            </div>
                            <div class="table-responsive">
                                    <table id="usersTable" class="table table-striped table-hover r-0" data-ajax="<?php echo $usersUrl; ?>">
                                    </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane animated fadeInUpShort go" id="v-pills-inactive" role="tabpanel" aria-labelledby="v-pills-inactive-tab">
                <div class="row my-3">
                    <div class="col-md-12">
                        <div class="card r-0 shadow">
                            <div class="card-header white">
                                <h6 class="pull-left">Seznam deaktivovaných uživatelů</h6>
                                <button class="btn btn-danger btn-xs float-right" id="js_depot_home_clear_filter">Zrušit filtr</button>
                            </div>
                            <div class="table-responsive">
                                    <table id="inactiveTable" class="table table-striped table-hover r-0" data-ajax="<?php echo $inactivesUrl; ?>">
                                    </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane animated fadeInUpShort go" id="v-pills-buyers" role="tabpanel" aria-labelledby="v-pills-buyers-tab">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header white">
                                <h6>Vytvoření nového účtu</h6>
                            </div>
                            <div class="card-body b-b">
                                <form id="addUserForm">

                                    <div class="form-row">
                                        <div class="form-group focused col-md-6" data-children-count="1">
                                            <label for="email">Email</label>
                                            <input class="form-control" type="email" name="email" placeholder="E-mailová adresa" required>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="phone">Telefon</label>
                                            <input class="form-control" type="number" name="phone" placeholder="Telefonní číslo">
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="first_name">Křestní Jméno</label>
                                            <input class="form-control" type="text" name="first_name" placeholder="Jméno" required>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="last_name">Příjmení</label>
                                            <input class="form-control" type="text" name="last_name" placeholder="Příjmení" required>
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="phone">IČ</label>
                                            <input class="form-control" type="number" name="company_id" placeholder="IČ">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="phone">DIČ</label>
                                            <input class="form-control" type="text" name="vat_id" placeholder="DIČ">
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="address">Ulice</label>
                                            <input class="form-control" type="text" name="street" placeholder="Ulice">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="address">Město</label>
                                            <input class="form-control" type="text" name="city" placeholder="Město">
                                        </div>                                        
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="address">PSČ</label>
                                            <input class="form-control" type="text" name="zip" placeholder="PSČ">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="country">Země</label>
                                            <?php $this->app_components->getSelect2Country(['input_name' => 'country','id' => 'country_id', 'selected' => 'CZ', 'required' => true]); ?>
                                         </div>                                          
                                    </div>

                                    <?php $this->app_components->getCustomFields($custom_fields); ?>

                                    <div class="form-row">
                                        <div class="form-group focused col-md-6" data-children-count="1">
                                            <label for="role">Uživatelská role</label>
                                            <?php $this->app_components->getSelect2Groups(['input_name' => 'role','id' => 'role', 'required' => true]); ?>
                                        </div>
                                        <div class="form-group focused col-md-6" data-children-count="1">
                                            <label for="active">Aktivní</label>
                                            <select class="form-control" name="active">
                                                <option value="1">Ano</option>
                                                <option value="0">Ne</option>
                                            </select>
                                        </div>
                                    </div>


                                    <div class="form-row">
                                        <div class="form-group col-md-12">
                                                    <label for="card_id">ID Karty</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">
                                                                <?php $this->app_components->getSelectPersonificators(['input_name' => 'reader_id','id' => 'inputGroupSelect01']); ?>
                                                            </span>
                                                        </div>
                                                        <input class="form-control" name="card_id" id="readerInput">

                                                        <div id="cardLoader"></div>
                                                    </div>
                                            </div>
                                    </div>      

                                    <hr>

                                        <div class="form-row">
                                            <div class="form-group focused col-md-12" data-children-count="1">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value="" id="agreement" name="agreement" required>
                                                    <label class="form-check-label" for="agreement">
                                                        Souhlas se zpracováním osobních údajů.
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                    <button type="submit" class="btn btn-sm btn-primary add-user-submit" data-ajax="<?php echo $addUrl; ?>">Přidat uživatele</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane animated fadeInUpShort go" id="v-pills-sellers" role="tabpanel" aria-labelledby="v-pills-sellers-tab">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header white">
                                <h6>Odeslání pozvánky</h6>
                            </div>
                            <div class="card-body b-b">
                                <div class="form-group focused" data-children-count="1">
                                    <label for="email">Email</label>
                                    <input class="form-control invitation-mail" type="email" name="email" placeholder="E-mailová adresa na kterou přijde pozvánka.." required>
                                </div>
                                <div class="form-group focused" data-children-count="1">
                                    <label for="role">Uživatelská role</label>
                                    <?php $this->app_components->getSelect2Groups(['input_name' => 'role','id' => 'role', 'required' => true]); ?>
                                </div>

                                <button type="submit" class="btn btn-sm btn-primary send-user-invitation" data-ajax="<?php echo $inviteUser; ?>">Odeslat pozvánku</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row my-3">
                    <div class="col-md-12">
                        <div class="card r-0 shadow">
                            <div class="card-header white">
                                <h6>Čekající pozvánky</h6>
                            </div>
                                <div class="table-responsive">
                                        <table id="inviteTable" class="table table-striped table-hover r-0" data-ajax="<?php echo $invitesUrl; ?>">
                                        </table>
                                </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>