<div class="modal fade" id="refundSubPaymentModal" tabindex="-1" role="dialog" aria-labelledby="refundSubPaymentModal" aria-hidden="true" style="z-index: 1051;">
  <div class="modal-dialog modal-md modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          Storno<span class="refund-sub-date"></span>
        </div>
        <div class="modal-body">
            <div class="refundtab" id="paid_history" style="display: none;">
              <label for="compensation">Kompenzace</label>
              <select class="form-control" name="compensation">
                <option value="0">Pozastavení</option>
                <option value="1">Posunutí členství</option>
              </select>
            </div>
            <div class="refundtab" id="paid_future" style="display: none;">
              <label for="compensation">Kompenzace</label>
              <select class="form-control" name="compensation">
                <option value="1">Posunutí členství</option>
              </select>
            </div>
            <div class="refundtab" id="unpaid_history" style="display: none;">
              <label for="compensation">Kompenzace</label>
              <select class="form-control" name="compensation">
                <option value="0">Pozastavení</option>
                <option value="1">Posunutí členství</option>
              </select>
            </div>
            <div class="refundtab" id="unpaid_future" style="display: none;">
              <label for="refund_type">Druh storna</label>
              <select class="form-control" name="refund_type">
                <option value="0">Vynechání </option>
                <option value="2">Pozastavení </option> <!-- TODO -->
                <option value="1">Zrušení členství</option>
              </select>
              <small>Vynechání = Dojde pouze k označení vybraného rozsahu jako přeskočeného<br />Zrušení členství = dojde ke kompletnímu zrušení k tomuto datu a návratu na předplacenou kartu.</small>
            </div>

            <div class="form-row mt-4">
                <div class="col-md-12 mb-3">
                    <label for="note">Poznámka (důvod,..)</label>
                    <textarea name="note" id="note" class="form-control" rows="2" placeholder="Poznámka"></textarea>
                 </div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Zrušit</button>
          <button type="button" class="btn btn-primary" id="confirmSubRefund">Potvrdit</button>
        </div>
      </div>
    </div>
</div>

<!-- SUB MODAL -->
<div class="modal fade" id="subModal" tabindex="-1" role="dialog" aria-labelledby="subModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">

    <div class="fader"></div>

      <div class="modal-header">
        <div class="select-container float-left" style="width: 50%;">
            <?php $this->app_components->getSelect2Clients(['input_name' => 'sub_client_id','id' => 'sub_client_id', 'empty' => TRUE]); ?>
        </div>
        <div class="align-self-end float-right">
            <ul class="nav nav-pills" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active show" id="buttonstate" data-toggle="tab" href="#statetab" role="tab" aria-controls="stocktab" aria-expanded="true" aria-selected="true">Stav</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link disabled" id="buttonpayments" data-toggle="tab" href="#paymenttab" role="tab" aria-controls="logtab" aria-selected="false">Platby</a>
                </li>
            </ul>
        </div>
      </div>
      <div class="modal-body">
        <div class="tab-content">
            <div class="tab-pane fade show active" id="statetab" role="tabpanel" aria-labelledby="statetab">
                <p class="text-muted text-center mt-3 select-client-placeholder">Vyberte klienta</p>
            </div>
            <div class="tab-pane fade" id="paymenttab" role="tabpanel" aria-labelledby="paymenttab">
                <table id="subscriptionPaymentTable" class="table table-striped table-hover r-0"></table>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Zavřít</button>
        <button type="button" class="btn btn-primary" id="saveSubDetail">Uložit</button>
      </div>
    </div>
  </div>
</div>