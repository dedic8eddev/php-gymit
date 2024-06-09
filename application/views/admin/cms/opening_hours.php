
<form id="openingHoursForm" class="col-12" data-ajax="<?php echo $saveOHUrl; ?>">
    <input type="hidden" name="id" value="<?php echo @$id; ?>">
    <div class="form-group row">
        <label for="" class="col-form-label pl-3">Pondělí - Pátek <span class="required">*</span></label>
        <input type="text" name="monday[from]" class="form-control js-time-input text-center " value="<?php echo @$data['monday']['from']; ?>" placeholder="Od.." required>
        <label for="" class="col-form-label mx-3">-</label>
        <input type="text" name="monday[to]" class="form-control js-time-input text-center" value="<?php echo @$data['monday']['to']; ?>" placeholder="Do.." required>
        <div class="material-switch float-right mt-2 ml-4">
            Zavřeno: <input id="mondayClose" name="monday[closed]" type="checkbox" <?php echo @$data['monday']['closed']=='on' ? 'checked' : ''; ?>>
            <label for="mondayClose" class="ml-3 bg-info"></label>
        </div>
    </div>   
    <div class="form-group row">
        <label for="" class="col-form-label pl-3">Sobota - Neděle <span class="required">*</span></label>
        <input type="text" name="saturday[from]" class="form-control js-time-input text-center" value="<?php echo @$data['saturday']['from']; ?>" placeholder="Od.." required>
        <label for="" class="col-form-label mx-3">-</label>
        <input type="text" name="saturday[to]" class="form-control js-time-input text-center" value="<?php echo @$data['saturday']['to']; ?>" placeholder="Do.." required>
        <div class="material-switch float-right mt-2 ml-4">
            Zavřeno: <input id="saturdayClose" name="saturday[closed]" type="checkbox" <?php echo @$data['saturday']['closed']=='on' ? 'checked' : ''; ?>>
            <label for="saturdayClose" class="ml-3 bg-info"></label>
        </div>                                
    </div> 
    <div class="form-group row">
        <label for="" class="col-form-label pl-3">Svátky <span class="required">*</span></label>
        <input type="text" name="holiday[from]" class="form-control js-time-input text-center" value="<?php echo @$data['holiday']['from']; ?>" placeholder="Od.." required>
        <label for="" class="col-form-label mx-3">-</label>
        <input type="text" name="holiday[to]" class="form-control js-time-input text-center" value="<?php echo @$data['holiday']['to']; ?>" placeholder="Do.." required>
        <div class="material-switch float-right mt-2 ml-4">
            Zavřeno: <input id="holidayClose" name="holiday[closed]" type="checkbox" <?php echo @$data['holiday']['closed']=='on' ? 'checked' : ''; ?>>
            <label for="holidayClose" class="ml-3 bg-info"></label>
        </div>                                
    </div>         
    <!--<div class="form-group row">
        <label for="" class="col-form-label pl-3">Pondělí *</label>
        <input type="text" name="monday[from]" class="form-control js-time-input text-center " value="<?php echo @$data['monday']['from']; ?>" placeholder="Od.." required>
        <label for="" class="col-form-label mx-3">-</label>
        <input type="text" name="monday[to]" class="form-control js-time-input text-center" value="<?php echo @$data['monday']['to']; ?>" placeholder="Do.." required>
        <div class="material-switch float-right mt-2 ml-4">
            Zavřeno: <input id="mondayClose" name="monday[closed]" type="checkbox" <?php echo @$data['monday']['closed']=='on' ? 'checked' : ''; ?>>
            <label for="mondayClose" class="ml-3 bg-info"></label>
        </div>
    </div>
    <div class="form-group row">
        <label for="" class="col-form-label pl-3">Úterý *</label>
        <input type="text" name="tuesday[from]" class="form-control js-time-input text-center" value="<?php echo @$data['tuesday']['from']; ?>" placeholder="Od.." required>
        <label for="" class="col-form-label mx-3">-</label>
        <input type="text" name="tuesday[to]" class="form-control js-time-input text-center" value="<?php echo @$data['tuesday']['to']; ?>" placeholder="Do.." required>
        <div class="material-switch float-right mt-2 ml-4">
            Zavřeno: <input id="tuesdayClose" name="tuesday[closed]" type="checkbox" <?php echo @$data['tuesday']['closed']=='on' ? 'checked' : ''; ?>>
            <label for="tuesdayClose" class="ml-3 bg-info"></label>
        </div>                                
    </div>
    <div class="form-group row">
        <label for="" class="col-form-label pl-3">Středa *</label>
        <input type="text" name="wednesday[from]" class="form-control js-time-input text-center" value="<?php echo @$data['wednesday']['from']; ?>" placeholder="Od.." required>
        <label for="" class="col-form-label mx-3">-</label>
        <input type="text" name="wednesday[to]" class="form-control js-time-input text-center" value="<?php echo @$data['wednesday']['to']; ?>" placeholder="Do.." required>
        <div class="material-switch float-right mt-2 ml-4">
            Zavřeno: <input id="wednesdayClose" name="wednesday[closed]" type="checkbox" <?php echo @$data['wednesday']['closed']=='on' ? 'checked' : ''; ?>>
            <label for="wednesdayClose" class="ml-3 bg-info"></label>
        </div>                                
    </div>
    <div class="form-group row">
        <label for="" class="col-form-label pl-3">Čtvrtek *</label>
        <input type="text" name="thursday[from]" class="form-control js-time-input text-center" value="<?php echo @$data['thursday']['from']; ?>" placeholder="Od.." required>
        <label for="" class="col-form-label mx-3">-</label>
        <input type="text" name="thursday[to]" class="form-control js-time-input text-center" value="<?php echo @$data['thursday']['to']; ?>" placeholder="Do.." required>
        <div class="material-switch float-right mt-2 ml-4">
            Zavřeno: <input id="thursdayClose" name="thursday[closed]" type="checkbox" <?php echo @$data['thursday']['closed']=='on' ? 'checked' : ''; ?>>
            <label for="thursdayClose" class="ml-3 bg-info"></label>
        </div>                                
    </div>
    <div class="form-group row">
        <label for="" class="col-form-label pl-3">Pátek *</label>
        <input type="text" name="friday[from]" class="form-control js-time-input text-center" value="<?php echo @$data['friday']['from']; ?>" placeholder="Od.." required>
        <label for="" class="col-form-label mx-3">-</label>
        <input type="text" name="friday[to]" class="form-control js-time-input text-center" value="<?php echo @$data['friday']['to']; ?>" placeholder="Do.." required>
        <div class="material-switch float-right mt-2 ml-4">
            Zavřeno: <input id="fridayClose" name="friday[closed]" type="checkbox" <?php echo @$data['friday']['closed']=='on' ? 'checked' : ''; ?>>
            <label for="fridayClose" class="ml-3 bg-info"></label>
        </div>                                
    </div>
    <div class="form-group row">
        <label for="" class="col-form-label pl-3">Sobota *</label>
        <input type="text" name="saturday[from]" class="form-control js-time-input text-center" value="<?php echo @$data['saturday']['from']; ?>" placeholder="Od.." required>
        <label for="" class="col-form-label mx-3">-</label>
        <input type="text" name="saturday[to]" class="form-control js-time-input text-center" value="<?php echo @$data['saturday']['to']; ?>" placeholder="Do.." required>
        <div class="material-switch float-right mt-2 ml-4">
            Zavřeno: <input id="saturdayClose" name="saturday[closed]" type="checkbox" <?php echo @$data['saturday']['closed']=='on' ? 'checked' : ''; ?>>
            <label for="saturdayClose" class="ml-3 bg-info"></label>
        </div>                                
    </div>
    <div class="form-group row">
        <label for="" class="col-form-label pl-3">Neděle *</label>
        <input type="text" name="sunday[from]" class="form-control js-time-input text-center" value="<?php echo @$data['sunday']['from']; ?>" placeholder="Od.." required>
        <label for="" class="col-form-label mx-3">-</label>
        <input type="text" name="sunday[to]" class="form-control js-time-input text-center" value="<?php echo @$data['sunday']['to']; ?>" placeholder="Do.." required>
        <div class="material-switch float-right mt-2 ml-4">
            Zavřeno: <input id="sundayClose" name="sunday[closed]" type="checkbox" <?php echo @$data['sunday']['closed']=='on' ? 'checked' : ''; ?>>
            <label for="sundayClose" class="ml-3 bg-info"></label>
        </div>                                
    </div>--> 
</form>