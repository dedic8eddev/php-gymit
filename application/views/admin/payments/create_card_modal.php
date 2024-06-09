<div style="min-height:125px;">
    <div id="createCardModalBtns">
        <button id="btn-pair-card" class="btn btn-block btn-default py-3 shadow-none">Spárovat kartu s poplatkem<br /><small><i>Karta bude spárována až po uhrazení transakce</i></small></button>
        <ul class="list-group mt-2">
            <li class="list-group-item p-0">
                <button id="btn-pair-card-immediately" class="btn btn-block btn-default py-3 shadow-none b-0">Spárovat kartu bez poplatku<br /><small><i>Karta bude spárována okamžitě bez uhrazení transakce</i></small></button>
            </li>
            <li class="list-group-item">
                Multisport
                <div class="material-switch float-right">
                    <input id="multisport" name="multisport" type="checkbox" <?php echo @$user->group_id==21 ? 'checked' : ''; ?>>
                    <label for="multisport" class="bg-primary"></label>
                </div>
            </li>            
            <li class="list-group-item">
                VIP
                <div class="material-switch float-right">
                    <input class="lessRequired" id="vip" name="vip" type="checkbox" />
                    <label for="vip" class="bg-success"></label>
                </div>
            </li>
            <li class="list-group-item">
                Dailypass
                <div class="material-switch float-right">
                    <input class="lessRequired" id="dailypass" name="dailypass" type="checkbox" />
                    <label for="dailypass" class="bg-secondary"></label>
                </div>
            </li>                                       
        </ul>
    </div>
    <div id="createCardModalInstructions" style="display:none;">
        <h3 class="text-center py-5">Přiložte kartu ke čtečce<span class="loader__dot">.</span><span class="loader__dot">.</span><span class="loader__dot">.</span></h3>
    </div>
</div>
