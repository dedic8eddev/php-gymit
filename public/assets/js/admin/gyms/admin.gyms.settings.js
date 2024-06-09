'use strict';

var GYM_SETTING = GYM_SETTING || (function () {
    var self;
    return {
        rooms_table: '#roomsTable',
        rooms_table_url: $('#roomsTable').data("ajax"),
        roomModal: $('#addRoomModal'),
        roomSubmit: $('#addRoomSubmit'),
        roomEditModal: $('#editRoomModal'),
        roomSave: $('#roomSave'),
        
        depots_table: '#depotsTable',
        depots_table_url: $('#depotsTable').data("ajax"),
        depotModal: $('#addDepotModal'),
        depotSubmit: $('#addDepotSubmit'),
        depotEditModal: $('#editDepotModal'),
        depotSave: $('#depotSave'),

        solariums_table: '#solariumsTable',
        solariums_table_url: $('#solariumsTable').data("ajax"),
        solariumMaintenanceForm: $('#solariumMaintenanceForm'),
        solariumMaintenanceModal: $('#solariumMaintenanceModal'),
        solariumEditForm: $('#solariumEditForm'),  
        solariumEditModal: $('#editSolariumModal'),
        
        active_picker: {'1':'Aktivní','2':'Neaktivní'},
        dateFilterParamas: {
            inputFormat:"YYYY-MM-DD HH:mm:ss",
            outputFormat:tabulatorDateFormat+' '+tabulatorTimeFormat,
            invalidPlaceholder:"(invalid date)"
        },             

        role: false,
        init: async function(params){
            self = this;

            this.role = await GYM._role();
            NProgress.configure({ parent: '#app', minimum: 0.1, showSpinner: false });

            this.rooms_table = new Tabulator(this.rooms_table, {
                layout: 'fitColumns',
                placeholder:"Nebyly nalezeny žádné místnosti",
                resizableColumns: false,
                pagination: 'remote',
                ajaxSorting:true,
                ajaxFiltering:true,
                paginationSize: 20,
                paginationSizeSelector:[10, 20, 30, 50, 100],
                langs: GYM.tabulator_czech,
                layoutColumnsOnNewData:true,
                columns: [
                    {title: 'NÁZEV', field: 'name', headerFilter:true},
                    {title: 'POPIS', field: 'description'},
                    {title: '', align: 'right', headerSort:false, formatter: this.returnRoomTableButtons}
                ]
            });
            this.rooms_table.setLocale("cs-cs");
            this.rooms_table.setData(this.rooms_table_url);

            this.depots_table = new Tabulator(this.depots_table, {
                layout: 'fitColumns',
                placeholder:"Nebyly nalezeny žádné místnosti",
                resizableColumns: false,
                pagination: 'remote',
                ajaxSorting:true,
                ajaxFiltering:true,
                paginationSize: 20,
                paginationSizeSelector:[10, 20, 30, 50, 100],
                langs: GYM.tabulator_czech,
                layoutColumnsOnNewData:true,
                columns: [
                    {title: 'NÁZEV', field: 'name', headerFilter:true},
                    {title: 'POPIS', field: 'description'},
                    {title: '', align: 'right', headerSort:false, formatter: this.returnDepotTableButtons}
                ]
            });
            this.depots_table.setLocale("cs-cs");
            this.depots_table.setData(this.depots_table_url);

            this.solariums_table = new Tabulator(this.solariums_table, {
                layout: 'fitColumns',
                placeholder:"Nebyly nalezeny žádná solária",
                resizableColumns: false,
                pagination: 'remote',
                ajaxSorting:true,
                ajaxFiltering:true,
                paginationSize: 20,
                paginationSizeSelector:[10, 20, 30, 50, 100],
                langs: GYM.tabulator_czech,
                layoutColumnsOnNewData:true,
                columns: [
                    {title: 'NÁZEV', field: 'name', headerFilter:true},
                    {title: 'LIMIT TRUBIC', field: 'usage_minutes_limit', headerFilter:true, formatter: function(c){ return c.getValue()/60+ ` hodin (${c.getValue()} minut)`; } },
                    {title: 'SPOTŘEBOVÁNO', field: 'used', headerFilter:false, formatter: self.returnSolariumUsage},
                    {title: 'DATUM POSLEDNÍ ÚDRŽBY', field: 'last_maintenance', formatter:"datetime", headerFilter: true, headerFilterPlaceholder:"Hledat podle dne", editor: self.dateEditor, editable: false, formatterParams:this.dateFilterParamas},
                    {title: '', align: 'right', headerSort:false, formatter: this.returnSolariumTableButtons}
                ]
            });
            this.solariums_table.setLocale("cs-cs");
            this.solariums_table.setData(this.solariums_table_url);            

            this.fireEvents();
        },
        returnRoomTableButtons: function(c){
            var d = c.getRow().getData();
                var edit_button = '<a href="javascript:;" class="edit-room" data-row='+"'"+' '+JSON.stringify(d)+' '+"'"+'><i class="icon-pencil"></i></a>&nbsp;';
                var delete_button = '<a href="javascript:;" class="remove-room text-danger ml-2" data-id="'+d.id+'" data-url="'+d.delete_url+'"><i class="icon-close"></i></a>';            
            return edit_button+delete_button;
        },
        returnDepotTableButtons: function(c){
            var d = c.getRow().getData();
                var edit_button = '<a href="javascript:;" class="edit-depot" data-row='+"'"+' '+JSON.stringify(d)+' '+"'"+'><i class="icon-pencil"></i></a>&nbsp;';
                var delete_button = '<a href="javascript:;" class="remove-depot text-danger ml-2" data-id="'+d.id+'" data-url="'+d.delete_url+'"><i class="icon-close"></i></a>';            
            return edit_button+delete_button;
        }, 
        returnSolariumTableButtons: function(c){
            var d = c.getRow().getData();
                var edit_button = '<a href="javascript:;" class="edit-solarium" data-row='+"'"+' '+JSON.stringify(d)+' '+"'"+' title="Upravit solarium"><i class="icon-pencil"></i></a>&nbsp;';
                var maintenance_button = '<a href="javascript:;" class="add-solarium-maintenance text-warning" data-row='+"'"+' '+JSON.stringify(d)+' '+"'"+' title="Přidat záznam o údržbě"><i class="icon-build"></i></a>&nbsp;';
                var log_button = '<a href="'+d.logs_url+'" class="text-success" title="Zobrazit logy"><i class="icon-clipboard-list2"></i></a>&nbsp;';    
            return edit_button+maintenance_button+log_button;
        },
        returnSolariumUsage: function(c){
            let d = c.getRow().getData(),
                percentage = d.used/d.usage_minutes_limit*100;
            return percentage.toFixed(2) + ` % (${d.used} minut)`;
        },
        dateEditor: function(cell, onRendered, success, cancel, editorParams){
            var editor = document.createElement("input");
            flatpickr(editor, { dateFormat: flatpickrDateFormat });
            onRendered(function(){ editor.focus(); editor.style.css = "100%"; });
            editor.style.padding = "4px";
            editor.style.width = "100%";
            editor.style.boxSizing = "border-box";
            editor.addEventListener("change", successFunc);
            return editor;
            function successFunc(){ success(moment(editor.value, tabulatorDateFormat).format("YYYY-MM-DD")); }
        }, 
        fireEvents: function(){

            // clear filter
            $(".js-clear-filter").click(function(){
                let table = eval('GYM_SETTING.' + $(this).data('table'));
                table.clearFilter(true);
                table.clearSort();
            });              

            $('select[name="personificator"]').change(function(){
                var c = $(this).parents().eq(2);
                var val = $(this).val();

                if(val == '1'){
                    c.find('select[name="address"]').prop('disabled', true);
                    c.find('select[name="entrance"]').prop('disabled', true);
                    c.find('select[name="priority"]').prop('disabled', true);
                    c.find('select[name="exit"]').prop('disabled', true);
                    c.find('select[name="wellness"]').prop('disabled', true);
                    c.find('select[name="exercise_room"]').prop('disabled', true);

                    c.find('select[name="entrance"]').val('0');
                    c.find('select[name="priority"]').val('0').trigger('change');
                    c.find('select[name="exit"]').val('0').trigger('change');
                    c.find('select[name="address"]').val('0').trigger('change');
                    c.find('select[name="wellness"]').val('0').trigger('change');
                    c.find('select[name="exercise_room"]').val('0').trigger('change');
                }else{
                    c.find('select[name="address"]').removeAttr('disabled');
                    c.find('select[name="entrance"]').removeAttr('disabled');
                    c.find('select[name="priority"]').removeAttr('disabled');
                    c.find('select[name="exit"]').removeAttr('disabled');
                    c.find('select[name="wellness"]').removeAttr('disabled');
                    c.find('select[name="exercise_room"]').removeAttr('disabled');
                }
            });
            $('select[name="entrance"]').change(function(){
                var c = $(this).parents().eq(2);
                var val = $(this).val();

                if(val == '1'){
                    c.find('select[name="personificator"]').prop('disabled', true);
                    c.find('select[name="wellness"]').prop('disabled', true);
                    c.find('select[name="exit"]').prop('disabled', true);
                    c.find('select[name="exercise_room"]').prop('disabled', true);
                    
                    c.find('select[name="personificator"]').val('0');
                    c.find('select[name="exit"]').val('0');
                    c.find('select[name="wellness"]').val('0').trigger('change');
                    c.find('select[name="exercise_room"]').val('0').trigger('change');
                }else{
                    c.find('select[name="personificator"]').removeAttr('disabled');
                    c.find('select[name="wellness"]').removeAttr('disabled');
                    c.find('select[name="exercise_room"]').removeAttr('disabled');
                    c.find('select[name="exit"]').removeAttr('disabled');
                }
            });

            $('select[name="exit"]').change(function(){
                var c = $(this).parents().eq(2);
                var val = $(this).val();

                if(val == '1'){
                    c.find('select[name="personificator"]').prop('disabled', true);
                    c.find('select[name="wellness"]').prop('disabled', true);
                    c.find('select[name="entrance"]').prop('disabled', true);
                    c.find('select[name="exercise_room"]').prop('disabled', true);
                    
                    c.find('select[name="personificator"]').val('0');
                    c.find('select[name="entrance"]').val('0');
                    c.find('select[name="wellness"]').val('0').trigger('change');
                    c.find('select[name="exercise_room"]').val('0').trigger('change');
                }else{
                    c.find('select[name="personificator"]').removeAttr('disabled');
                    c.find('select[name="wellness"]').removeAttr('disabled');
                    c.find('select[name="exercise_room"]').removeAttr('disabled');
                    c.find('select[name="entrance"]').removeAttr('disabled');
                }
            });

            $('select[name="pin_code_bool"]').change(function(){
                var c = $(this).parents().eq(2);
                var val = $(this).val();

                if(val == "1"){
                    c.find('input[name="pin_code"]').removeAttr('disabled');
                }else{
                    c.find('input[name="pin_code"]').prop('disabled', true);
                    c.find('input[name="pin_code"]').val("");
                }
            });

            // Rooms
            $('body').on('click', '.edit-room', function(){
                var data = GYM._json($(this).data("row"));
                
                self.roomEditModal.find('input[name="name"]').val(data.name);
                self.roomEditModal.find('textarea[name="description"]').val(data.description);
                self.roomEditModal.find('input[name="reader_id"]').val(data.settings.readerId);
                
                if(typeof data.settings.isPersonificator != 'undefined') self.roomEditModal.find('select[name="personificator"]').val(Number(data.settings.isPersonificator)).trigger("change");
                else self.roomEditModal.find('select[name="personificator"]').val(0).trigger("change");

                if(typeof data.settings.isBuildingEntrance != 'undefined') self.roomEditModal.find('select[name="entrance"]').val(Number(data.settings.isBuildingEntrance)).trigger("change");
                else self.roomEditModal.find('select[name="entrance"]').val(0).trigger("change");

                if(typeof data.settings.isBuildingExit != 'undefined') self.roomEditModal.find('select[name="exit"]').val(Number(data.settings.isBuildingExit)).trigger("change");
                else self.roomEditModal.find('select[name="exit"]').val(0).trigger("change");

                if(typeof data.settings.isWellness != 'undefined') self.roomEditModal.find('select[name="wellness"]').val(Number(data.settings.isWellness)).trigger("change");
                else self.roomEditModal.find('select[name="wellness"]').val(0).trigger("change");

                if(typeof data.settings.isExerciseRoom != 'undefined') self.roomEditModal.find('select[name="exercise_room"]').val(Number(data.settings.isExerciseRoom)).trigger("change");
                else self.roomEditModal.find('select[name="exercise_room"]').val(0).trigger("change");

                if(typeof data.settings.roomPriority != 'undefined') self.roomEditModal.find('select[name="priority"]').val(Number(data.settings.roomPriority)).trigger("change");
                else self.roomEditModal.find('select[name="priority"]').val(0).trigger("change");

                if(typeof data.settings.pinCode != 'undefined') {
                    self.roomEditModal.find('select[name="pin_code_bool"]').val(1).trigger("change");
                    self.roomEditModal.find('input[name="pin_code"]').val(Number(data.settings.pinCode));
                }
                else {
                    self.roomEditModal.find('select[name="pin_code_bool"]').val(0).trigger("change");
                    self.roomEditModal.find('input[name="pin_code"]').val("");
                }
                
                self.roomEditModal.find('select[name="category_id"]').val(data.category);
                self.roomEditModal.find('select[name="address"]').val(data.address);

                self.roomEditModal.find('select[name="rooms_users"]').val(data.rooms_users);
                self.roomEditModal.find('select[name="rooms_groups"]').val(data.rooms_groups);

                self.roomSave.data('ajax', data.edit_url);

                self.roomEditModal.modal("show");
            });
            self.roomEditModal.on('hidden.bs.modal', function(){
                var inputs = self.roomEditModal.find('input, textarea, select');
                $.each(inputs, function(i, input){ $(input).val(''); });
                self.roomSave.data('ajax', '');
            });
            $('body').on('click', '.remove-room', function(){
                var agreed = confirm('Opravdu chcete vymazat místnost?');
                if(agreed){
                    var room_id = $(this).data('id'),
                        url = $(this).data('url');
                    GYM._post(url, {}).done(function(res){
                        if(!res.error){
                            N.show('success', 'Místnost byla vymazána!');
                            self.rooms_table.setData(self.rooms_table_url);
                        }else{
                            N.show('error', 'Místnost se nepovedlo vymazat, zkuste to znovu!');
                        }
                    });
                }
            });
            self.roomSave.click(function(e){
                e.preventDefault();
                var inputs = self.roomEditModal.find('input, textarea, select');
                var url = $(this).data("ajax");

                $.each(inputs, function (i, input){
                    if($(input).attr("required") && ($(input).val == "" || !$(input).val())){
                        $(input).addClass('invalid');
                    }else{
                        $(input).removeClass('invalid');
                    }
                });

                if(self.roomEditModal.find('.invalid').length <= 0){

                    var request_obj = {};
                    $.each(inputs, function(i, input){ request_obj[$(input).attr("name")] = $(input).val(); })

                    GYM._post(url, request_obj).done(function(res){
                        if(!res.error){
                            N.show('success', 'Místnost úspěšně uložena.');
                            self.rooms_table.setData(self.rooms_table_url);
                            self.roomEditModal.modal("hide");
                            
                            $.each(inputs, function(i, input){ $(input).val(''); });
                        }else{
                            N.show('error', 'Nepovedlo se uložit místnost, zkuste to znovu!');
                        }
                    });
                }else{
                    N.show('error', 'Formulář obsahuje chyby nebo chybí povinné údaje, zkontrolujte červeně označená pole!');
                }
            });
            self.roomSubmit.click(function(e){
                e.preventDefault();
                var inputs = self.roomModal.find('input, textarea, select');
                var url = $(this).data("ajax");

                $.each(inputs, function (i, input){
                    if($(input).attr("required") && ($(input).val == "" || !$(input).val())){
                        $(input).addClass('invalid');
                    }else{
                        $(input).removeClass('invalid');
                    }
                });

                if(self.roomModal.find('.invalid').length <= 0){

                    var request_obj = {};
                    $.each(inputs, function(i, input){ request_obj[$(input).attr("name")] = $(input).val(); })

                    GYM._post(url, request_obj).done(function(res){
                        if(!res.error){
                            N.show('success', 'Místnost úspěšně přidána.');
                            self.rooms_table.setData(self.rooms_table_url);
                            self.roomModal.modal("hide");
                            
                            $.each(inputs, function(i, input){ $(input).val(''); });
                        }else{
                            N.show('error', 'Nepovedlo se přidat místnost, zkuste to znovu!');
                        }
                    });
                }else{
                    N.show('error', 'Formulář obsahuje chyby nebo chybí povinné údaje, zkontrolujte červeně označená pole!');
                }
            });
            // Rooms end 

            // Solariums

            $('body').on('click', '.edit-solarium', function(){
                var data = GYM._json($(this).data("row"));
                
                self.solariumEditModal.find('input[name="name"]').val(data.name);
                self.solariumEditModal.find('input[name="usage_minutes_limit"]').val(data.usage_minutes_limit/60);
                self.solariumEditForm.data('ajax', data.edit_url);
                self.solariumEditModal.modal("show");
            });
            self.solariumEditModal.on('hidden.bs.modal', function(){
                var inputs = self.solariumEditModal.find('input, textarea, select');
                $.each(inputs, function(i, input){ $(input).val(''); });
                self.solariumEditForm.data('ajax', '');
            });
            self.solariumEditForm.submit(function(e){
                e.preventDefault();
                GYM._submitForm({
                    url: $(this).data('ajax'),
                    form:$(this)[0],
                    succes_text: 'Solárium bylo úspěšně uložena!',
                    error_text: 'Nepodařilo se uložit solárium, zkontrolujte údaje nebo to zkuste znovu!',
                    success_function: function(){
                        self.solariumEditModal.modal('hide');
                        self.solariums_table.setData(self.solariums_table_url);
                    }                       
                });
            });            

            $('body').on('click', '.add-solarium-maintenance', function(){
                var data = GYM._json($(this).data("row"));
                self.solariumMaintenanceForm.data('ajax', data.maintenance_url);
                self.solariumMaintenanceModal.modal("show");
            });
            self.solariumMaintenanceModal.on('hidden.bs.modal', function(){
                var inputs = self.solariumMaintenanceModal.find('input, textarea, select');
                $.each(inputs, function(i, input){ $(input).val(''); });
                self.solariumMaintenanceForm.find('input[name="change_pipes"]').prop('checked',false);

                self.solariumMaintenanceForm.data('ajax', '');
            });
            self.solariumMaintenanceForm.submit(function(e){
                e.preventDefault();
                GYM._submitForm({
                    url: $(this).data('ajax'),
                    form:$(this)[0],
                    succes_text: 'Záznam o údržbě byl zaznamenán!',
                    error_text: 'Nepodařilo se uložit záznam o údržbě, zkontrolujte údaje nebo to zkuste znovu!',
                    success_function: function(){
                        self.solariumMaintenanceModal.modal('hide');
                        self.solariums_table.setData(self.solariums_table_url);
                    }                       
                });
            });               

            // Solariums end

            // Depots
            self.depotSubmit.click(function(e){
                e.preventDefault();
                var inputs = self.depotModal.find('input, textarea');
                var url = $(this).data("ajax");

                $.each(inputs, function (i, input){
                    if($(input).attr("required") && ($(input).val == "" || !$(input).val())){
                        $(input).addClass('invalid');
                    }else{
                        $(input).removeClass('invalid');
                    }
                });

                if(self.roomModal.find('.invalid').length <= 0){

                    var request_obj = {};
                    $.each(inputs, function(i, input){ request_obj[$(input).attr("name")] = $(input).val(); })

                    GYM._post(url, request_obj).done(function(res){
                        if(!res.error){
                            N.show('success', 'Sklad úspěšně přidán.');
                            self.depots_table.setData(self.depots_table_url);
                            self.depotModal.modal("hide");
                            
                            $.each(inputs, function(i, input){ $(input).val(''); });
                        }else{
                            N.show('error', 'Nepovedlo se přidat sklad, zkuste to znovu!');
                        }
                    });
                }else{
                    N.show('error', 'Formulář obsahuje chyby nebo chybí povinné údaje, zkontrolujte červeně označená pole!');
                }
            });
            self.depotSave.click(function(e){
                e.preventDefault();
                var inputs = self.depotEditModal.find('input, textarea');
                var url = $(this).data("ajax");

                $.each(inputs, function (i, input){
                    if($(input).attr("required") && ($(input).val == "" || !$(input).val())){
                        $(input).addClass('invalid');
                    }else{
                        $(input).removeClass('invalid');
                    }
                });

                if(self.depotEditModal.find('.invalid').length <= 0){

                    var request_obj = {};
                    $.each(inputs, function(i, input){ request_obj[$(input).attr("name")] = $(input).val(); })

                    GYM._post(url, request_obj).done(function(res){
                        if(!res.error){
                            N.show('success', 'Místnost úspěšně uložena.');
                            self.depots_table.setData(self.depots_table_url);
                            self.depotEditModal.modal("hide");
                            
                            $.each(inputs, function(i, input){ $(input).val(''); });
                        }else{
                            N.show('error', 'Nepovedlo se uložit sklad, zkuste to znovu!');
                        }
                    });
                }else{
                    N.show('error', 'Formulář obsahuje chyby nebo chybí povinné údaje, zkontrolujte červeně označená pole!');
                }
            });

            $('body').on('click', '.edit-depot', function(){
                var data = GYM._json($(this).data("row"));
                
                self.depotEditModal.find('input[name="name"]').val(data.name);
                self.depotEditModal.find('textarea[name="description"]').val(data.description);
                self.depotEditModal.find('textarea[name="reader_id"]').val(data.reader_id);
                self.depotSave.data('ajax', data.edit_url);

                self.depotEditModal.modal("show");
            });
            self.depotEditModal.on('hidden.bs.modal', function(){
                var inputs = self.depotEditModal.find('input, textarea');
                $.each(inputs, function(i, input){ $(input).val(''); });
                self.depotSave.data('ajax', '');
            });
            $('body').on('click', '.remove-depot', function(){
                var agreed = confirm('Opravdu chcete vymazat sklad?');
                if(agreed){
                    var url = $(this).data('url');
                    GYM._post(url, {}).done(function(res){
                        if(!res.error){
                            N.show('success', 'Sklad byl vymazán!');
                            self.depots_table.setData(self.depots_table_url);
                        }else{
                            N.show('error', 'Sklad se nepovedlo vymazat, zkuste to znovu!');
                        }
                    });
                }
            });
            // end depots
            
            
            $(".switch-to-rooms").click(function(){ setTimeout(function(){ self.rooms_table.redraw(true); }, 100); });
            $(".switch-to-depots").click(function(){ setTimeout(function(){ self.depots_table.redraw(true); }, 100); });
            $(".switch-to-solariums").click(function(){ setTimeout(function(){ self.solariums_table.redraw(true); }, 100); });

        }
    }
}());

GYM_SETTING.init();