'use strict';

var JOBS = JOBS || (function () {
    var self;
    return {
        jobs_table: '#gymJobsTable',
        jobs_table_url: $('#gymJobsTable').data("ajax"),
        job_form: '#gymJobForm',
        
        remote_modal: $('#modal, #requirementModal'),
        btn_modal_submit: $('#modalSubmit'),        
        
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

            this.jobs_table = new Tabulator(this.jobs_table, {
                layout: 'fitColumns',
                placeholder:"Nebyly nalezeny žádné pracovní pozice",
                resizableColumns: false,
                pagination: 'remote',
                ajaxSorting:true,
                ajaxFiltering:true,
                paginationSize: 20,
                paginationSizeSelector:[10, 20, 30, 50, 100],
                langs: GYM.tabulator_czech,
                layoutColumnsOnNewData:true,
                columns: [
                    {title: 'NÁZEV', field: 'name', headerFilter:true, headerFilterPlaceholder:"Hledat podle názvu",formatter: this.returnName},
                    {title: 'STATUS', field: 'state', headerFilter:"select", headerFilterFunc:this.statusFilter, headerFilterParams:this.active_picker, headerFilterPlaceholder: 'Aktivní/neaktivní', formatter: this.returnStatus},
                    {title: '', align: 'right', headerSort:false, formatter: this.returnTableButtons, width: 100}
                ]
            });
            this.jobs_table.setLocale("cs-cs");
            this.jobs_table.setData(this.jobs_table_url);

            this.fireEvents();
        },
        returnName: function(cell, params, onRendered){
            var row_data = cell._cell.row.data;
            return '<a href="javascript:;" data-toggle="modal" data-remote="/admin/cms/edit-gym-job/'+row_data.id+'" data-target="#modal" data-modal-title="Editace položky" data-modal-submit="Uložit">'+row_data.name+'</a>&nbsp;';
        },
        returnStatus: function(cell){
            if(cell._cell.row.data.state != 1) return '<span class="icon icon-circle s-12 mr-2"></span> Neaktivní';
            else return '<span class="icon icon-circle s-12 mr-2 text-success"></span> Aktivní';
        },        
        returnTableButtons: function(cell, params, onRendered){
            var row_data = cell._cell.row.data;
            var edit_button = '<a href="javascript:;" data-toggle="modal" data-remote="/admin/cms/edit-gym-job/'+row_data.id+'" data-target="#modal" data-modal-title="Editace položky" data-modal-submit="Uložit"><i class="icon-pencil"></i></a>&nbsp;';
            var delete_button = '<a href="javascript:;" class="job-remove text-danger ml-2" data-id="'+row_data.id+'" data-ajax="/admin/cms/delete-gym-job-ajax"><i class="icon-delete"></i></a>';            
            return edit_button+delete_button;
        },         
        fireEvents: function(){
            TRUMBOWYG.init();

            $("#js_jobs_clear_filter").click(function(){
                self.jobs_table.clearHeaderFilter();
                self.jobs_table.clearSort();
            });

            $('body').on('click', '[data-toggle="modal"]', function(){
                if($(this).data("target") == '#modal') $($(this).data("target")+' .modal-lg').css('max-width','1200px'); // modal-xl
                $($(this).data("target")+' .modal-body').load($(this).data("remote"), function(){
                    $('.select2').select2();
                    $('.dropdown-menu li').on('click', function(){    
                        $(this).parent().prev('.dropdown-toggle').html($(this).html());   
                        var itemId = $(this).data('id');
                        $(this).parents('.row').find('.selectedDropDownItem').val(itemId);
                        $(this).parents('.row').find('.itemText').prop('disabled',itemId == '');
                    });                     
                    if(self.remote_modal.find('.js-trumbowyg-editor').length) TRUMBOWYG.init();
                });
            });              

            self.remote_modal.on('show.bs.modal', function(e){
                $(e.target).find('.modal-title').text($(e.relatedTarget).data('modal-title'));
                self.btn_modal_submit.text($(e.relatedTarget).data('modal-submit'));
            });

            self.remote_modal.on('shown.bs.modal', function(e){
                // init tabulator requirement
                if($(e.target).is("#requirementModal")) setTimeout(function(){ self.requirementTabulator().init(); }, 100);
            });

            self.remote_modal.on('hide.bs.modal', function(e){
                if($(e.target).is("#requirementModal")){
                    let type = $(e.target).find('#requirement_table').data('type');
                    GYM._post('/admin/cms/get_jobs_requirements_ajax/'+type).done(function(res){
                        $.each(res.data, function(i,v){ // rename key name => text (for select2)
                            delete Object.assign(v, {['text']: v['name'] })['name'];
                        });
                        var values = $('#requirement_'+type).val();
                        $('#requirement_'+type).select2().empty();
                        $('#requirement_'+type).select2({
                            data: res.data
                        }).val(values).trigger('change');                
                    });                
                }
            });               
            
            self.remote_modal.on('hidden.bs.modal', function (event) {
                if ($('.modal:visible').length) { // modal over modal
                    $('body').addClass('modal-open');
                }
            });              

            self.btn_modal_submit.click(function(){
                $(this).closest('.modal-content').find('form').submit();
            });       

            $('#gymJobsTable').on('click', '.job-remove, .gym_service-remove', function(){
                var url = $(this).data('ajax'),
                    id = $(this).data('id'),
                    agreed = confirm('Opravdu chcete vymazat tuto položku?');
                if(agreed){
                    GYM._post(url, {'job_id':id}).done(function(res){
                        if(!res.error){
                            self.jobs_table.setData(self.jobs_table_url);
                            N.show('success', 'Položka úspěšně smazána!');
                        }else N.show('error', 'Nepodařilo se smazat položku, zkuste to znovu.');
                        NProgress.done();
                    });
                }
            });        

            $('#cmsJobsPage').on('submit', self.job_form, function(e){
                e.preventDefault();
                GYM._submitForm({
                    url: $(this).data('ajax'),
                    form:$(this)[0],
                    succes_text: 'Položka byla úspěšně uložena!',
                    error_text: 'Nepodařilo se uložit položku, zkontrolujte údaje nebo to zkuste znovu!',
                    success_function: function(){
                        self.remote_modal.modal('hide');
                        self.jobs_table.setData(self.jobs_table_url);
                    }
                });
            });
        },
        requirementTabulator: function(){
            return {
                requirement_table: '#requirement_table',
                requirement_table_url: $('#requirement_table').data("ajax"),
                requirement_table_type: $('#requirement_table').data("type"),
                init: async function(params){
                    NProgress.configure({ parent: '#app', minimum: 0.1, showSpinner: false });  
                    this.requirement_table = new Tabulator(this.requirement_table, {
                        layout: 'fitColumns',
                        placeholder:"Žádna data nenalezena",
                        resizableColumns: false,
                        pagination: 'remote',
                        paginationSize: 20,
                        paginationSizeSelector:[10, 20, 30, 50, 100],
                        layoutColumnsOnNewData:true,
                        langs: GYM.tabulator_czech,
                        columns: [
                            {title: 'Název', field: 'name', headerSort:true, headerFilter:true, editor:"input", editable:true, widthGrow:2 },
                            {title: 'Akce', field: 'actions', align: 'right', headerSort:false, editable:false, formatter: this.returnTableButtons},
                        ]
                    });
                    this.requirement_table.setLocale("cs-cs");
                    this.requirement_table.setData(this.requirement_table_url);
                    this.fireEvents();
                },
                returnTableButtons: function(cell, params, onRendered){
                    var row_data = cell._cell.row.data;
                    if(row_data.id>0){
                        var save_button = '<a href="javascript:;" class="btn btn-xs btn-primary js-save-btn" data-url="/admin/cms/save-job-requirement-ajax/" data-id="'+row_data.id+'">Uložit</a>&nbsp;';
                        var delete_button = '<a href="javascript:;" class="btn btn-xs btn-danger js-delete-btn" data-url="/admin/cms/delete-job-requirement-ajax/" data-id="'+row_data.id+'" title="Vymazat"><i class="icon-trash"></i></a>&nbsp;';
                        return save_button+delete_button;
                    } else return '<a href="javascript:;" class="btn btn-xs btn-primary js-save-btn" data-url="/admin/cms/save-job-requirement-ajax/" data-id="'+row_data.id+'">Uložit</a>&nbsp;';
                    
                },
                fireEvents: function(){                    
                    var self = this;
        
                    $("#js_add_requirement").on("click", function(e) { 
                        self.requirement_table.addData([{name:"Nový",actions:""}], true);
                        $("#requirement_table").find('.tabulator-row:first-child .tabulator-cell:first-child').click();
                    });            
        
                    // clear filter
                    $(".js-clear-filter").click(function(){
                        self.requirement_table.clearHeaderFilter();
                        self.requirement_table.clearSort();
                    });                                
        
                    $("#requirement_table").on('click','.js-save-btn',function(e){
                        e.preventDefault();
                        var url = $(this).data('url'),
                            item_id = $(this).data('id'),
                            item_name = $(this).closest('.tabulator-row').find('.tabulator-cell:first-child').text();

                        GYM._post(url, {item_id:item_id, item_name:item_name, item_type:self.requirement_table_type}).done(function(res){
                            if(!res.error){
                                N.show('success', 'Položka uložena!');
                                self.requirement_table.setData(self.requirement_table_url);
                            }else N.show('error', 'Nepovedlo se uložit položku, zkuste to prosím znovu.');
                        });                                         
                    }); 
                    $("#requirement_table").on('click','.js-delete-btn',function(e){
                        e.preventDefault();

                        var url = $(this).data('url'),
                            item_id = $(this).data('id'),
                            item_name = $(this).closest('.tabulator-row').find('.tabulator-cell:first-child').text();

                        GYM._post(url, {item_id:item_id}).done(function(res){
                            if(!res.error){
                                N.show('success', 'Položka smazána!');
                                self.requirement_table.setData(self.requirement_table_url);
                            }else N.show('error', 'Nepovedlo se smazat položku, zkuste to prosím znovu.');
                        });                                             
                    });                        
                }            
            }
        }             
    }
}());

JOBS.init();