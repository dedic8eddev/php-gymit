<form id="cancelLessonsForm" data-ajax="<?php echo $cancelLessonsUrl; ?>">
    <input type="hidden" name="teacher_id" value="<?php echo $teacher_id; ?>" />
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="cancel_from">Neúčast na lekcích OD <span class="required">*</span></label>
            <input type="text" name="cancel_from" class="form-control cancel-dates" placeholder="Neúčast na lekcích OD" required />
        </div>   
        <div class="form-group col-md-6">
            <label for="cancel_to">Neúčast na lekcích DO <span class="required">*</span></label>
            <input type="text" name="cancel_to" class="form-control cancel-dates" placeholder="Neúčast na lekcích DO" required />
        </div>                 
    </div>  
    <div class="form-row">
        <div class="form-group col-md-12">
            <label for="cancel_from">Akce <span class="required">*</span></label>
            <select name="action" id="cancel-action" class="form-control" required>
                <option value="" selected disabled>Vyberte akci</option>
                <option value="cancel">Zrušit lekce</option>
                <option value="substitute">Nahradit trenéra / instruktora</option>
            </select>
        </div>     
    </div>
    <div class="form-row d-none">
        <div class="form-group col-md-12">
            <label for="teacher_substitute">Náhradní trenér / instruktor</label>
            <?php $this->app_components->getSelect2Teachers(['input_name' => 'teacher_substitute','id' => 'teacher_substitute']); ?>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-12">
            <label for="reason">Důvod <span class="required">*</span></label>
            <textarea name="reason" rows="3" class="form-control" placeholder="Zadejte důvod" required></textarea>
        </div>
    </div>
</form>