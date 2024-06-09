<div class="page has-sidebar-left has-sidebar-tabs height-full">
    <?php $this->load->view('layout/alerts'); ?>
    
    <div class="container-fluid relative animatedParent animateOnce my-3">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body b-b">
                        <form id="profileForm">
                            <div class="form-group">
                                <label for="email">E-mailová adresa</label>
                                <input required type="email" name="email" class="form-control" placeholder="E-mailová adresa" value="<?php echo $user->email; ?>">
                            </div>
                            <div class="form-group">
                                <label for="first_name">Křestní jméno</label>
                                <input required type="text" name="first_name" class="form-control" placeholder="Jméno" value="<?php echo $user_data->first_name; ?>">
                            </div>
                            <div class="form-group">
                                <label for="last_name">Příjmení</label>
                                <input required type="text" name="last_name" class="form-control" placeholder="Příjmení" value="<?php echo $user_data->last_name; ?>">
                            </div>

                            <div class="form-group">
                                <label for="phone">Telefon</label>
                                <input type="number" name="phone" class="form-control" placeholder="Telefoní číslo" value="<?php echo $user_data->phone; ?>">
                            </div>

                            <hr>
                            <button class="btn btn-sm btn-primary" data-url="<?php echo $saveProfileUrl; ?>">Uložit</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-body b-b">
                        <form id="passwordForm">
                            <div class="form-group">
                                <label for="first_name">Nové heslo</label>
                                <input required type="password" name="password" class="form-control" id="password" placeholder="">
                            </div>
                            <div class="form-group">
                                <label for="last_name">Nové heslo znovu</label>
                                <input required type="password" name="password again" class="form-control" placeholder="">
                            </div>

                            <div class="progress"><div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 0%"><span class="sr-only"></span></div></div>
                                                    
                            <hr>
                            <button class="btn btn-sm btn-primary" data-url="<?php echo $changePasswordUrl; ?>">Změnit heslo</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>