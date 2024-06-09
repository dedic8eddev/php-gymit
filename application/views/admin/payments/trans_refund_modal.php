<!-- CHECKOUT MODAL -->
<div class="modal fade" id="refundTransactionModal" tabindex="-1" role="dialog" aria-labelledby="refundTransactionModal" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">

    <div class="fader"></div>

      <div class="modal-header">Storno transakce<span class="refund-trans-number"></span></div>
      <form id="refundTransactionForm" data-ajax="/admin/payments/refund-transaction-ajax">
        <div class="modal-body">

                                    <table id="summaryTableRefund" class="w-100">
                                        <thead>
                                            <tr>
                                                <th>Položka</th>
                                                <th class='text-right' style="width:80px;">Počet</th>
                                                <th class='text-right' style="width:80px;">Sleva (%)</th>
                                                <th class='text-right'>Cena</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                    <table id="purchaseTypeTableRefund" class="w-100 mt-4">
                                        <thead>
                                            <tr>
                                                <th>Typ platby</th>
                                                <th class='text-right' style="width:110px;">Cena</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>    

                                    <table id="totalResultTableRefund" class="w-100 mt-4">
                                        <tbody>
                                            <tr>
                                                <td>Celkem</td>
                                                <td id="price_total_refund"></td>
                                            </tr>
                                        </tbody>
                                    </table>    

                                    <textarea id="note_refund" class="form-control mt-3" placeholder="Poznámka..."></textarea>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-danger btn-sm">Stornovat transakci</button>
        </div>
      </form>
    </div>
  </div>
</div>