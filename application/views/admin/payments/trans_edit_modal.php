<div class="modal fade" id="editTransactionPaymentMethodModal" tabindex="-1" role="dialog" aria-labelledby="editTransactionModal" aria-hidden="true" style="z-index: 1051;">
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
        <div class="modal-header">Editace platební metody<span class="edit-trans-number"></span></div>
            <div class="modal-body">
                <select id="editPaymentMethodSelect" class="select2 form-control">
                    <option value="1">Hotově</option>
                    <option value="2">Kartou</option>
                    <option value="3">Kredit</option>
                    <option value="5">Voucher</option>
                    <option value="6">E-ticket</option>
                    <option value="7">Sodexo (poukázky)</option>
                    <option value="11">Endered (poukázky)</option>
                    <option value="8">Benefit plus - Objednávka</option>
                    <option value="10">Benefit plus - Platební karta</option>
                    <option value="4">Multisport (karta)</option>
                    <option value="9">Benefit plus (karta)</option>
                    <option value="12">Benefity (karta)</option>
                </select>
            </div>

            <div class="modal-footer">
                <button class="btn btn-primary btn-sm" id="submitNewPaymentMethodEdit">Uložit</button>
            </div>
        </div>
    </div>
</div>

<!-- CHECKOUT MODAL -->
<div class="modal fade" id="editTransactionModal" tabindex="-1" role="dialog" aria-labelledby="editTransactionModal" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">

    <div class="fader"></div>

      <div class="modal-header">
        <div class="float-left">Editace transakce</div>
        <div class="float-right"><span class="edit-trans-number"></span><a class="btn-print-receipt" href="javascript:;" onclick="PAYMENTS.printReceipt(this);" title="Tisk dokladu"><i class="icon-print2 s-18 ml-2"></i></a></div>
      </div>
      <form id="editTransactionForm" data-ajax="/admin/payments/edit-transaction-ajax">
        <div class="modal-body">

                                    <table id="summaryTableEdit" class="w-100">
                                        <thead>
                                            <tr>
                                                <th>Položka</th>
                                                <th class='text-right' style="width:80px;">Počet</th>
                                                <th class='text-right' style="width:80px;">Sleva (%)</th>
                                                <th class='text-right'>Cena <small>(bez DPH)</small></th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                    <table id="purchaseTypeTableEdit" class="w-100 mt-4">
                                        <thead>
                                            <tr>
                                                <th>Typ platby</th>
                                                <th class='text-right' style="width:110px;">Cena <small>(bez DPH)</small></th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>    

                                    <table id="totalResultTableEdit" class="w-100 mt-4">
                                        <tbody>
                                            <tr>
                                                <td>Celkem</td>
                                                <td id="price_total_edit"></td>
                                            </tr>
                                        </tbody>
                                    </table>    

                                    <textarea id="note_edit" class="form-control mt-3" placeholder="Poznámka..."></textarea>

        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary btn-sm">Uložit změny</button>
        </div>
      </form>
    </div>
  </div>
</div>