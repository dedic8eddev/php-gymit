<!-- SUB MODAL -->
<div class="modal fade" id="addSubModal" tabindex="-1" role="dialog" aria-labelledby="addSubModal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">

    <div class="fader"></div>

      <div class="modal-header">
        <div class="select-container float-left" style="width: 50%;">
            Založení členství
        </div>
      </div>
      <div class="modal-body">
            <form id="addSubForm">
                <div class="form-row">
                    <label for="sub_type">Druh členství <span class="required">*</span></label>
                    <select class="form-control" name="sub_type" id="subTypeSelect" required>
                        <option disabled selected>Vyberte druh členství..</option>
                        <?php foreach ($this->pricelist->getAllMemberships(true) as $m): ?>
                        <option value="<?php echo $m->code;?>"><?php echo $m->name;?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-row sub-period-container mt-2" style="display: none;">
                    <label for="sub_period">Periodika <span class="required">*</span></label>
                    <select class="form-control" name="sub_period" required>
                        <option disabled selected>Vyberte periodicitu členství..</option>

                        <option value="month">Měsíčně</option>
                        <option value="year">Ročně</option>
                    </select>
                </div>
                <div class="form-row mt-2">
                    <label for="start">Start členství <span class="required">*</span></label>
                    <input type="date" id="add_sub_datepick" name="start" class="form-control" required>
                </div>

            </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Zrušit</button>
        <button type="button" class="btn btn-primary" id="saveSubModal">Založit členství</button>
      </div>
    </div>
  </div>
</div>