'use strict';

var COACHES = COACHES || (function () {
    var self;
    return {
        coach_roles: [9,10],
        instructor_roles: [11],
        all_roles: [9,10,11],
        coaches_table: '#coachesTable',
        coaches_table_url: $('#coachesTable').data("ajax"), // used for coaches and instructors both
        instructors_table: '#instructorsTable', 
        inactive_table: '#inactiveTable',    
        coach_form: $('#addCoachForm'),
        coach_submit: $('.add-coach-submit'),
        role: null,
        active_picker: {'1':'Aktivní', '0':'Neaktivní'},
        role_picker: {'10':'Trenéři', '11':'Instruktoři'},

        remote_modal: $('#modal'),
        btn_modal_submit: $('#modalSubmit'),        

        init: async function(params){
            self = this;
            this.role = await GYM._role();
            NProgress.configure({ parent: '#app', minimum: 0.1, showSpinner: false });   

            this.initCoachesTable();
            this.initInstructorsTable();
            this.initInactiveTable();       

            this.fireEvents();
        },
        dateFormatterParams: {
            inputFormat:"YYYY-MM-DD HH:mm:ss",
            outputFormat:tabulatorDateFormat, // variable from main.js
            invalidPlaceholder:"-&nbsp;-&nbsp;-"
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
        statusFilter: function(headerValue, rowValue, rowData, filterParams){
            var compare = 0;
            if(headerValue == "Aktivní") compare = 1;

            return rowData.active == compare;
        },
        returnStatus: function(cell){
            var row_data = cell._cell.row.data;
            if(row_data.active != 1){
                return '<span class="icon icon-circle s-12 mr-2"></span> Neaktivní';
            }else{
                return '<span class="icon icon-circle s-12 mr-2 text-success"></span> Aktivní';
            }
        },
        returnNum: function(){
            return 0;
        },
        returnName: function(cell, params, onRendered){
            var row_data = cell._cell.row.data;
            return '<a href="/admin/coaches/edit/'+row_data.id+'">'+row_data.full_name+'</a>';
        },
        returnVisible: function(cell, params, onRendered) {
            var row_data = cell._cell.row.data;
            console.log(row_data);
            return row_data.visible == 0 ? '<i class="fa fa-exclamation" title="Nemáte vyplněné všechny povinné položky pro zobrazení na webu!"></i>' : '';
        },
        returnTableButtons: function(cell, params, onRendered){
            var row_data = cell._cell.row.data;
            var edit_button = GYM._isAllowed('edit') ? '<a href="/admin/coaches/edit/'+row_data.id+'" data-id="'+row_data.id+'"><i class="icon-pencil"></i></a>&nbsp;' :'';
            var delete_button = '<a href="javascript:;" class="btn btn-xs btn-danger coach-remove" data-id="'+row_data.id+'" data-ajax="/admin/users/remove_user_ajax">Odstranit</a>';            
            return edit_button;
        },
        validEmail: function(email) {
            var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(String(email).toLowerCase());
        },
        fireEvents: function(){
            GYM._media(); // init media

            // flatpickr
            $("#birth_date").flatpickr({
                minDate: '1900-01-01',
                altInput: true,
                altFormat: flatpickrDateFormat,
                dateFormat: "Y-m-d",
            });                

            $("#js-clear-coaches-filter").click(function(){
                COACHES.coaches_table.clearHeaderFilter();
                COACHES.coaches_table.clearSort();
            });
            $("#js-clear-instructors-filter").click(function(){
                COACHES.instructors_table.clearHeaderFilter();
                COACHES.instructors_table.clearSort();
            });
            $("#js-clear-inactive-filter").click(function(){
                COACHES.inactive_table.clearHeaderFilter();
                COACHES.inactive_table.clearSort();
            });              
            
            $('.switch-to-coaches').click(function(){ self.coaches_table.setData(self.coaches_table_url,{role:self.coach_roles}); });
            $('.switch-to-instructors').click(function(){ self.instructors_table.setData(self.coaches_table_url,{role:self.instructor_roles}); });
            $('.switch-to-inactive').click(function(){ self.inactive_table.setData(self.coaches_table_url,{role:self.all_roles,active:0}); });

            $('.js-media-input-target-id').change(function(){
                var img = $(this).attr('data-img');
                $('.image-preview').css('background-image', 'url(' + img + ')').addClass('uploaded');
            });  
            
            $('body').on('click', '[data-toggle="modal"]', function(){
                $($(this).data("target")+' .modal-body').load($(this).data("remote"), function(){
                    $('.select2').select2();
                    if(self.remote_modal.find('.js-trumbowyg-editor').length) TRUMBOWYG.init();
                });
            });  

            self.remote_modal.on('show.bs.modal', function(e){
                $(e.target).find('.modal-title').text($(e.relatedTarget).data('modal-title'));
                self.btn_modal_submit.remove();
            });

            self.remote_modal.on('shown.bs.modal', function(e){
                // init tabulator specializations
                setTimeout(function(){ self.specializationTabulator().init(); }, 100);
            });  
            
            self.remote_modal.on('hide.bs.modal', function(e){
                GYM._post('/admin/coaches/get-specializations-ajax').done(function(res){
                    $.each(res.data, function(i,v){ // rename key name => text (for select2)
                        delete Object.assign(v, {['text']: v['name'] })['name'];
                    });
                    var values = $('#specializations').val();
                    $('#specializations').select2().empty();
                    $('#specializations').select2({
                        data: res.data
                    }).val(values).trigger('change');                
                });                
            });            

            $('#coachesTable').on('click', '.coach-remove', function(){
                var url = $(this).data('ajax'),
                    id = $(this).data('id');

                var agreed = confirm('Opravdu chcete vymazat tento účet?');
                if(agreed){
                    GYM._post(url, {'user_id':id}).done(function(res){
                        if(!res.error){
                            self.coaches_table.setData(self.coaches_table_url);
                            N.show('success', 'Účet úspěšně smazán!');
                        }else{
                            N.show('error', 'Nepovedlo se smazat účet, zkuste to znovu.');
                        }

                        NProgress.done();
                    });
                }
            });

            self.coach_submit.click(function(e){
                e.preventDefault();

                if(!$('#agreement').is(':checked')){
                    $('#agreement').parent().find("label").addClass('invalid');
                }else{
                    $('#agreement').parent().find("label").removeClass('invalid');
                }

                GYM._submitForm({
                    url: $(this).data('ajax'),
                    form: $(self.coach_form)[0],
                    succes_text: 'Uživatelský účet byl úspěšně vytvořen!',
                    error_text: 'Nepodařilo se vytvořit účet, zkontrolujte údaje nebo to zkuste znovu!',
                    success_function: function(){
                        self.coach_form[0].reset();
                        self.coaches_table.setData(self.coaches_table_url);
                        $('.switch-to-coaches').click();
                    }
                });
            });
        },
        initCoachesTable: function(){
            this.coaches_table = new Tabulator(this.coaches_table, {
                layout: 'fitColumns',
                placeholder:"Nebyli nalezeni žádní trenéři",
                resizableColumns: false,
                pagination: 'remote',
                ajaxSorting:true,
                ajaxFiltering:true,
                paginationSize: 20,
                paginationSizeSelector:[10, 20, 30, 50, 100],
                langs: GYM.tabulator_czech,
                layoutColumnsOnNewData:true,
                columns: [
                    {title: 'TRENÉR', field: 'full_name', headerFilter:true, formatter: this.returnName},
                    {title: 'E-MAIL', field: 'email', headerFilter:true},
                    {title: 'TELEFON', field: 'phone', headerFilter:true},
                    {title: 'DATUM NAROZENÍ', field: 'birth_date', headerFilter:true, editable:false, editor:this.dateEditor, formatter:"datetime",formatterParams:this.dateFormatterParams },
                    {title: '', align: 'center', headerSort:false, formatter: this.returnVisible, width: 10},
                    {title: '', align: 'right', headerSort:false, formatter: this.returnTableButtons, width: 100}
                ]
            });
            this.coaches_table.setLocale("cs-cs");
            this.coaches_table.setData(this.coaches_table_url,{role:self.coach_roles});
        },
        initInstructorsTable: function(){
            this.instructors_table = new Tabulator(this.instructors_table, {
                layout: 'fitColumns',
                placeholder:"Nebyli nalezeni žádní instruktoři",
                headerFilterPlaceholder:"Hledat..",
                resizableColumns: false,
                pagination: 'remote',
                ajaxSorting:true,
                ajaxFiltering:true,
                paginationSize: 20,
                paginationSizeSelector:[10, 20, 30, 50, 100],
                langs: GYM.tabulator_czech,
                layoutColumnsOnNewData:true,
                columns: [
                    {title: 'INSTRUKTOR', field: 'full_name', headerFilter:true, formatter: this.returnName},
                    {title: 'E-MAIL', field: 'email', headerFilter:true},
                    {title: 'TELEFON', field: 'phone', headerFilter:true},
                    {title: 'DATUM NAROZENÍ', field: 'birth_date', headerFilter:true, editable:false, editor:this.dateEditor, formatter:"datetime",formatterParams:this.dateFormatterParams },
                    {title: '', align: 'center', headerSort:false, formatter: this.returnVisible, width: 10},
                    {title: '', align: 'right', headerSort:false, formatter: this.returnTableButtons, width: 100}
                ]
            });
            this.instructors_table.setLocale("cs-cs");
            this.instructors_table.setData(this.instructors_table,{role:self.instructor_roles});
        },
        initInactiveTable: function(){
            this.inactive_table = new Tabulator(this.inactive_table, {
                layout: 'fitColumns',
                placeholder:"Nebyli nalezeni žádní trenéři ani instruktoři",
                headerFilterPlaceholder:"Hledat..",
                resizableColumns: false,
                pagination: 'remote',
                ajaxSorting:true,
                ajaxFiltering:true,
                paginationSize: 20,
                paginationSizeSelector:[10, 20, 30, 50, 100],
                langs: GYM.tabulator_czech,
                layoutColumnsOnNewData:true,
                columns: [
                    {title: 'ÚČET', field: 'role', headerFilter:"select", headerFilterParams: COACHES.role_picker, headerFilterPlaceholder:"Trenér / instruktor"},
                    {title: 'UŽIVATEL', field: 'full_name', headerFilter:true, formatter: this.returnName},
                    {title: 'E-MAIL', field: 'email', headerFilter:true},
                    {title: 'TELEFON', field: 'phone', headerFilter:true},
                    {title: 'DATUM NAROZENÍ', field: 'birth_date', headerFilter:true, editable:false, editor:this.dateEditor, formatter:"datetime",formatterParams:this.dateFormatterParams },
                    {title: '', align: 'right', headerSort:false, formatter: this.returnTableButtons, width: 100}
                ]
            });
            this.inactive_table.setLocale("cs-cs");         
        },                
        specializationTabulator: function(){
            return {
                specializations_table: '#specializations_table',
                specializations_table_url: $('#specializations_table').data("ajax"),
                init: async function(params){
                    NProgress.configure({ parent: '#app', minimum: 0.1, showSpinner: false });  
                    this.specializations_table = new Tabulator(this.specializations_table, {
                        layout: 'fitColumns',
                        placeholder:"Žádné specializace nenalezeny",
                        headerFilterPlaceholder:"Hledat..",
                        resizableColumns: false,
                        pagination: 'remote',
                        paginationSize: 20,
                        paginationSizeSelector:[10, 20, 30, 50, 100],
                        layoutColumnsOnNewData:true,
                        langs: GYM.tabulator_czech,
                        columns: [
                            {title: 'Specializace', field: 'name', headerSort:true, headerFilter:true, editor:"input", editable:true, widthGrow:2 },
                            {title: 'Akce', field: 'actions', align: 'right', headerSort:false, editable:false, formatter: this.returnTableButtons},
                        ]
                    });
                    this.specializations_table.setLocale("cs-cs");
                    this.specializations_table.setData(this.specializations_table_url);
                    this.fireEvents();
                },
                returnTableButtons: function(cell, params, onRendered){
                    var row_data = cell._cell.row.data;
                    if(row_data.id>0){
                        var save_button = '<a href="javascript:;" class="btn btn-xs btn-primary js-save-btn" data-url="/admin/coaches/save-specialization-ajax/" data-id="'+row_data.id+'">Uložit</a>&nbsp;';
                        var delete_button = '<a href="javascript:;" class="btn btn-xs btn-danger js-delete-btn" data-url="/admin/coaches/delete-specialization-ajax/" data-id="'+row_data.id+'" title="Vymazat"><i class="icon-trash"></i></a>&nbsp;';
                        return save_button+delete_button;
                    } else return '<a href="javascript:;" class="btn btn-xs btn-primary js-save-btn" data-url="/admin/coaches/save-specialization-ajax/" data-id="'+row_data.id+'">Uložit specializaci</a>&nbsp;';
                    
                },
                fireEvents: function(){                    
                    var self = this;
        
                    $("#js_add_specialization").on("click", function(e) { 
                        self.specializations_table.addData([{name:"Nová specializace",actions:""}], true);
                        $("#specializations_table").find('.tabulator-row:first-child .tabulator-cell:first-child').click();
                    });            
        
                    // clear filter
                    $(".js-clear-filter").click(function(){
                        self.specializations_table.clearHeaderFilter();
                        self.specializations_table.clearSort();
                    });                                
        
                    $("#specializations_table").on('click','.js-save-btn',function(e){
                        e.preventDefault();
                        var url = $(this).data('url'),
                            item_id = $(this).data('id'),
                            item_name = $(this).closest('.tabulator-row').find('.tabulator-cell:first-child').text();

                        GYM._post(url, {item_id:item_id, item_name:item_name}).done(function(res){
                            if(!res.error){
                                N.show('success', 'Specializace uložena!');
                                self.specializations_table.setData(self.specializations_table_url);
                            }else N.show('error', 'Nepovedlo se uložit specializaci, zkuste to prosím znovu.');
                        });                                         
                    }); 
                    $("#specializations_table").on('click','.js-delete-btn',function(e){
                        e.preventDefault();

                        var url = $(this).data('url'),
                            item_id = $(this).data('id'),
                            item_name = $(this).closest('.tabulator-row').find('.tabulator-cell:first-child').text();

                        GYM._post(url, {item_id:item_id}).done(function(res){
                            if(!res.error){
                                N.show('success', 'Specializace smazána!');
                                self.specializations_table.setData(self.specializations_table_url);
                            }else N.show('error', 'Nepovedlo se smazat specializaci, zkuste to prosím znovu.');
                        });                                             
                    });                        
                }            
            }
        }
    }
}());

COACHES.init();