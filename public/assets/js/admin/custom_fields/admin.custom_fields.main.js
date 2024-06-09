'use strict';

var CUSTOM_FIELDS = CUSTOM_FIELDS || (function () {
    var self;
    return {
        param_template: $('#selectTemplate'),
        add_param_modal: $('#addFieldModal'),
        edit_param_modal: $('#editFieldModal'),
        submit_field: $('#addfieldsubmit'),
        save_field: $('#savefieldsubmit'),
        field_table: '#fieldTable',
        field_table_url: $('#fieldTable').data("ajax"),
        sectionNames: {'users': 'Uživatelé', 'depot_items':'Sklad'},
        typeNames: {'text': 'Text', 'number':'Číslo', 'select':'Výběr'},
        init: async function(){
            self = this;

            this.user_role = await GYM._role();
            NProgress.configure({ parent: '.tool-bar .widget', minimum: 0.1, showSpinner: false });

            this.field_table = new Tabulator(this.field_table, {
                layout: 'fitColumns',
                placeholder:"Nebyli nalezena žádná vlastní pole",
                resizableColumns: false,
                pagination: 'remote',
                ajaxSorting:true,
                ajaxFiltering:true,
                paginationSize: 20,
                paginationSizeSelector:[10, 20, 30, 50, 100],
                langs: GYM.tabulator_czech,
                layoutColumnsOnNewData:true,
                columns: [
                    {title: 'Název', field: 'name', headerFilter:true, headerFilterPlaceholder:"Hledat podle názvu"},
                    {title: 'Druh', field: 'type', headerFilter:"select", headerFilterPlaceholder:"Filtr podle typu", headerFilterParams: {values: { "text":"Text", "number":"Číslo", "select":"Výběr" }}, formatter: self.returnTypeName},
                    {title: 'Sekce', field: 'section', headerFilter:"select", headerFilterPlaceholder:"Filtr podle sekce", headerFilterParams: {values: { "users":"Uživatelé", "depot_items":"Sklad"}}, formatter: self.returnSectionName},
                    {title: 'Skryté', field: 'hidden', headerFilter:true, headerFilterPlaceholder:"Filtr podle viditelnosti", headerFilterParams: {values: { "0":"Viditelné", "1":"Skryté" }}, formatter: self.returnVisibility},
                    {title: '', field: '', formatter: self.returnTableButtons, align: 'right'}
                ]
            });
            this.field_table.setLocale("cs-cs");
            this.field_table.setData(this.field_table_url);

            this.fireEvents();
        },
        returnSectionName: function(cell){
            var d = cell.getValue();
            return self.sectionNames[d];
        },
        returnTypeName: function(cell){
            var d = cell.getValue();
            return self.typeNames[d];
        },
        returnVisibility: function(cell){
            var d = cell.getValue();
            if(d == 1){
                return 'Skryté';
            }else{
                return 'Viditelné';
            }
        },
        returnTableButtons: function(cell){
            var d = cell.getRow().getData();

            var edit_button = '<a class="btn-fab btn-fab-sm shadow btn-primary float-right edit-field" data-id="'+d.id+'"><i class="icon-mode_edit"></i></a>&nbsp;';
            var delete_button = '<a class="btn-fab btn-fab-sm shadow btn-danger float-right delete-field" data-id="'+d.id+'"><i class="icon-delete"></i></a>';

            return edit_button + delete_button;
        },
        fireEvents: function(){

            $('body').on( 'click', '.edit-field', function(){
                var field_id = $(this).data("id");
                GYM._post('/admin/custom-fields/get-field-ajax', {field_id: field_id}).done(function(res){
                    if(!res.error){

                        self.edit_param_modal.find('input[name="name"]').val(res.name);
                        self.edit_param_modal.find('input[name="description"]').val(res.description);
                        self.edit_param_modal.find('select[name="section"]').val(res.section);
                        self.edit_param_modal.find('select[name="type"]').val(res.type);
                        self.edit_param_modal.find('select[name="type"]').trigger('change');

                        if(res.type == 'select'){
                            var params = JSON.parse(res.type_params);
                            self.edit_param_modal.find('.params_rows').html('');
                            $.each(params, function(i, param){
                                var temp = self.param_template.clone();
                                    temp.find('input').attr('value', param);

                                self.edit_param_modal.find('.params_rows').append(temp.html());
                            });
                        }

                        if(res.required == "1"){
                            self.edit_param_modal.find('input[name="required"]').prop("checked", true);
                        }
                        if(res.hidden == "1"){
                            self.edit_param_modal.find('input[name="hidden"]').prop("checked", true);
                        }

                        self.edit_param_modal.modal("show");
                        self.save_field.data('fieldid', field_id);
                    }else{
                        N.show('error', 'Nepovedlo se získat data, zkuste to prosím znovu.');
                    }
                });
            } );

            $('body').on( 'click', '.delete-field', function(){
                var field_id = $(this).data("id");
                var agreed = confirm('Opravdu chcete vymazat toto vlastní pole? Pokud je někde vyplněné tak přijdete od vyplněná data.');
                if(agreed){
                    GYM._post('/admin/custom-fields/delete-field-ajax', {field_id: field_id}).done(function(res){
                        if(!res.error){
                            N.show('success', 'Vlastní pole bylo vymazáno!');
                            self.field_table.redraw(true);
                        }else{
                            N.show('error', 'Nepovedlo se vymazat vlastní pole, zkuste to prosím znovu.');
                        }
                    });
                }
            } );

            self.add_param_modal.find('select[name="type"]').change(function(){
                var v = $(this).val();
                if(v == 'select'){
                    self.add_param_modal.find('.select-params').slideDown();
                }else{
                    self.add_param_modal.find('.select-params').slideUp();
                }
            });
            self.edit_param_modal.find('select[name="type"]').change(function(){
                var v = $(this).val();
                if(v == 'select'){
                    self.edit_param_modal.find('.select-params').slideDown();
                }else{
                    self.edit_param_modal.find('.select-params').slideUp();
                }
            });

            self.add_param_modal.on('click', '.add-param-row', function(e){
                e.preventDefault();
                var template = self.param_template.clone();
                self.add_param_modal.find('.params_rows').append(template.html());
            });

            self.add_param_modal.on('hidden.bs.modal', function(e){
                self.add_param_modal.find('.params_rows').html('');
                var template = self.param_template.clone();
                self.add_param_modal.find('.params_rows').append(template.html());

                self.add_param_modal.find('input, select, textarea').removeClass('invalid');
                $('select[name="type"]').val('text').trigger('change');

                self.add_param_modal.find('input[name="hidden"]').prop("checked", false);
                self.add_param_modal.find('input[name="required"]').prop("checked", false);
            });
        
            self.edit_param_modal.on('click', '.add-param-row', function(e){
                e.preventDefault();
                var template = self.param_template.clone();
                self.edit_param_modal.find('.params_rows').append(template.html());
            });

            self.edit_param_modal.on('hidden.bs.modal', function(e){
                self.edit_param_modal.find('.params_rows').html('');
                var template = self.param_template.clone();
                self.edit_param_modal.find('.params_rows').append(template.html());

                self.edit_param_modal.find('input, select, textarea').removeClass('invalid');
                $('select[name="type"]').val('text').trigger('change');

                self.edit_param_modal.find('input[name="hidden"]').prop("checked", false);
                self.edit_param_modal.find('input[name="required"]').prop("checked", false);
            });
        
            self.submit_field.click(function(){

                var all_inputs = self.add_param_modal.find('input:visible, textarea:visible, select:visible');
                $.each(all_inputs, function(i, input){ 
                    if($(input).val() == "" && $(input).attr('required')){
                        $(input).addClass("invalid");
                    }else{
                        $(input).removeClass("invalid");
                    }
                });

                var options = [];
                if($('select[name="type"]').val() == 'select'){
                    var params = self.add_param_modal.find('.params_rows input');
                    console.log(params);
                    $.each(params, function(i, param){
                        if($(param).val() && $(param).val() != ""){
                            options.push($(param).val());
                        }
                    });
                }

                if(self.add_param_modal.find('.invalid').length <= 0){
                    var all_inputs = self.add_param_modal.find('input:visible, textarea:visible, select:visible');

                    var data = {};
                    $.each(all_inputs, function(i, input){ 
                        data[$(input).attr("name")] = $(input).val();
                    });

                    var is_required = self.add_param_modal.find('input[name="is_required"]').is(":checked");
                    var is_hidden = self.add_param_modal.find('input[name="hidden"]').is(":checked");

                    data.option = options;
                    data.required = is_required;
                    data.hidden = is_hidden;

                    GYM._post('/admin/custom-fields/add-field-ajax', data).done(function(res){
                        if(!res.error){
                            N.show('success', 'Vlastní pole bylo přidáno!');
                            self.add_param_modal.modal("hide");
                            self.field_table.redraw(true);
                        }else{
                            N.show('error', 'Nepovedlo se přidat vlastní pole, zkuste to znovu.');
                        }
                    });

                }else{
                    N.show('error', 'Zkontrolujte si povinné údaje ve formuláři a doplňte je nebo opravte!');
                }
            });

            self.save_field.click(function(){

                var field_id = $(this).data("fieldid");

                var all_inputs = self.edit_param_modal.find('input:visible, textarea:visible, select:visible');
                $.each(all_inputs, function(i, input){ 
                    if($(input).val() == "" && $(input).attr('required')){
                        $(input).addClass("invalid");
                    }else{
                        $(input).removeClass("invalid");
                    }
                });

                var options = [];
                if($('select[name="type"]').val() == 'select'){
                    var params = self.edit_param_modal.find('.params_rows input');
                    console.log(params);
                    $.each(params, function(i, param){
                        if($(param).val() && $(param).val() != ""){
                            options.push($(param).val());
                        }
                    });
                }

                if(self.edit_param_modal.find('.invalid').length <= 0){
                    var all_inputs = self.edit_param_modal.find('input:visible, textarea:visible, select:visible');

                    var data = {};
                    $.each(all_inputs, function(i, input){ 
                        data[$(input).attr("name")] = $(input).val();
                    });

                    var is_required = self.edit_param_modal.find('input[name="is_required"]').is(":checked");
                    var is_hidden = self.edit_param_modal.find('input[name="hidden"]').is(":checked");

                    data.option = options;
                    data.required = is_required;
                    data.hidden = is_hidden;
                    data.field_id = field_id;

                    GYM._post('/admin/custom-fields/save-field-ajax', data).done(function(res){
                        if(!res.error){
                            N.show('success', 'Vlastní pole bylo uloženo!');
                            self.edit_param_modal.modal("hide");
                            self.field_table.redraw(true);
                        }else{
                            N.show('error', 'Nepovedlo se uložit vlastní pole, zkuste to znovu.');
                        }
                    });

                }else{
                    N.show('error', 'Zkontrolujte si povinné údaje ve formuláři a doplňte je nebo opravte!');
                }
            });
        }
    }
}());

CUSTOM_FIELDS.init();