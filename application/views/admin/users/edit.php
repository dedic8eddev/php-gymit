<div class="page has-sidebar-left has-sidebar-tabs height-full">
    <?php $this->load->view('layout/alerts'); ?>
    <div class="container-fluid relative animatedParent animateOnce my-3">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header white">
                        <h6>Editace uživatele</h6>
                    </div>
                    <div class="card-body b-b">
                        <form id="saveUserForm">

                            <div class="form-row">
                                <div class="form-group focused col-md-6" data-children-count="1">
                                    <label for="email">Email</label>
                                    <input class="form-control" type="email" name="email" value="<?php echo $user->email; ?>" placeholder="E-mailová adresa" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="phone">Telefon</label>
                                    <input class="form-control" type="number" name="phone" value="<?php echo $user_data->phone; ?>" placeholder="Telefonní číslo">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group focused col-md-6" data-children-count="1">
                                    <label for="email">Datum registrace</label>
                                    <input class="form-control" disabled type="text" name="date_created" value="<?php echo date('d.m.Y H:i', strtotime($user->date_created)); ?>">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="phone">Poslední přihlášení</label>
                                    <input class="form-control" disabled type="text" name="last_login" value="<?php if($user->last_login != NULL) echo date('d.m.Y H:i', strtotime($user->last_login)); ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="first_name">Křestní Jméno</label>
                                    <input class="form-control" type="text" name="first_name" value="<?php echo $user_data->first_name; ?>" placeholder="Jméno" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="last_name">Příjmení</label>
                                    <input class="form-control" type="text" name="last_name" value="<?php echo $user_data->last_name; ?>" placeholder="Příjmení" required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="phone">IČ</label>
                                    <input class="form-control" type="number" name="company_id" value="<?php echo $user_data->company_id; ?>" placeholder="IČ">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="phone">DIČ</label>
                                    <input class="form-control" type="text" name="vat_id" value="<?php echo $user_data->vat_id; ?>" placeholder="DIČ">
                                </div>     
                            </div>

                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="address">Ulice</label>
                                            <input class="form-control" type="text" name="street" value="<?php echo $user_data->street; ?>" placeholder="Ulice">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="address">Město</label>
                                            <input class="form-control" type="text" name="city" value="<?php echo $user_data->city; ?>" placeholder="Město">
                                        </div>                                        
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="address">PSČ</label>
                                            <input class="form-control" type="text" name="zip" value="<?php echo $user_data->zip; ?>" placeholder="PSČ">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="country">Země</label>
                                            <?php $this->app_components->getSelect2Country(['input_name' => 'country','id' => 'country_id', 'selected' => 'CZ', 'selected' => $user_data->country, 'required' => true]); ?>
                                         </div>                                          
                                    </div>

                            <?php $this->app_components->getCustomFields($custom_fields, [], $custom_fields_values); ?>

                            <div class="form-row">
                                <div class="form-group focused col-md-6" data-children-count="1">
                                    <label for="role">Uživatelská role</label>
                                    <?php $this->app_components->getSelect2Groups(['input_name' => 'role','id' => 'role', 'required' => true, 'selected' => $user->group_id]); ?>
                                </div>
                            </div>
                            <?php if(hasEditPermission() || hasDeletePermission()): ?>             
                                <button type="submit" class="btn btn-sm btn-primary save-user-submit" data-ajax="<?php echo $saveDetail; ?>" data-id="<?php echo $user->id; ?>">Uložit uživatele</button>&nbsp;
                                <?php if($user->active): ?>
                                    <button class="btn btn-sm btn-danger remove-user" data-ajax="<?php echo $removeUser; ?>" data-id="<?php echo $user->id; ?>">Deaktivovat uživatele</button>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-success activate-user" data-ajax="<?php echo $activateUser; ?>" data-id="<?php echo $user->id; ?>">Aktivovat uživatele</button>
                                <?php endif; ?>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-12">
                <a href="/admin/users" class="btn btn-primary btn-sm">
                    <i class="icon icon-chevron-left"></i>
                    Zpět na přehled
                </a>
            </div>
        </div>
    </div>
</div>