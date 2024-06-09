<div class="card r-0 shadow my-3">
    <div class="card-header white">
        <h6 class="float-left">Vybavení</h6>
        <button class="btn btn-danger btn-xs ml-1 float-right js-clear-filter" data-table="categories_table">Vymazat filtr</button>
        <button id="js_add_equipment" class="btn btn-primary btn-xs float-right ml-2">Přidat vybavení</button>
    </div>
    <div class="table-responsive">
        <table id="equipment_table" class="table table-striped table-hover r-0" data-ajax="<?php echo $equipmentUrl; ?>"></table>
    </div>
</div>