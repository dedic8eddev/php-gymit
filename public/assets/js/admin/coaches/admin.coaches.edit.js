'use strict';

var COACH_DETAIL = COACH_DETAIL || (function () {
    var self;
    return {
        save_submit_btn: $('.save-user-submit'),
        user_id: $('#user_id').val(),
        user_form: $('#saveUserForm'),

        comingLessons_table:'#comingLessons_table',
        endedLessons_table:'#endedLessons_table',
        lessons_table_url: $('#comingLessons_table').data('ajax'),
        cancelLesson_form: '#cancelLessonsForm',

        remove_btn: $('.remove-user'),
        activate_btn: $('.activate-user'),

        remote_modal: $('#modal'),
        btn_modal_submit: $('#modalSubmit'),                

        role: null,
        init: async function(params){
            self = this;
            this.role = await GYM._role();

            this.initComingLessonsTable();
            this.initEndedLessonsTable();

            this.fireEvents();
        },
        fireEvents: function(){
            GYM._media(); // init media
            TRUMBOWYG.init();

            // flatpickr
            $("#birth_date").flatpickr({
                minDate: '1900-01-01',
                altInput: true,
                altFormat: flatpickrDateFormat,
                dateFormat: "Y-m-d",
            });               

            $('#v-pills-ended-lessons-tab').click(function(){ self.endedLessons_table.setData(self.lessons_table_url,{coach_id:self.user_id,past:true}); });
            $('#v-pills-coming-lessons-tab').click(function(){ self.comingLessons_table.setData(self.lessons_table_url,{coach_id:self.user_id,past:false}); });         

            // clear filter
            $(".js-clear-filter").click(function(){
                let table = eval('COACH_DETAIL.' + $(this).data('table'));
                table.clearHeaderFilter();
                table.clearSort();
                table.clearFilter();
            });            

            $('.js-media-input-target-id').change(function(){
                var img = $(this).attr('data-img');
                $('.image-preview').css('background-image', 'url(' + img + ')').addClass('uploaded');
            });

            $('body').on('click', '[data-toggle="modal"]', function(){
                $($(this).data("target")+' .modal-body').load($(this).data("remote"), function(){
                    $('.select2').select2();
                    if(self.remote_modal.find('.js-trumbowyg-editor').length) TRUMBOWYG.init();
                    if(self.remote_modal.find('.cancel-dates').length){
                        // flatpickr
                        $(".cancel-dates").flatpickr({
                            minDate: 'today',
                            altInput: true,
                            altFormat: flatpickrDateFormat,
                            dateFormat: "Y-m-d",
                        });        
                    }             
                });
            });  

            self.remote_modal.on('show.bs.modal', function(e){
                $(e.target).find('.modal-title').text($(e.relatedTarget).data('modal-title'));
                if($(e.relatedTarget).is('#btnCancelModal')) self.btn_modal_submit.text($(e.relatedTarget).data('modal-submit')).show();
                else self.btn_modal_submit.hide();
            });

            self.remote_modal.on('shown.bs.modal', function(e){
                // init tabulator specializations
                if($(e.relatedTarget).is('#btnSpecializationsModal')) setTimeout(function(){ self.specializationTabulator().init(); }, 100);
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

            self.btn_modal_submit.click(function(){
                $(this).closest('.modal-content').find('form').submit();
            }); 
            
            self.remote_modal.on('change', '#cancel-action', function(){
                let action = $(this).val();
                if(action=='substitute') $('#teacher_substitute').prop('required',true).attr("disabled", false).closest('.form-row').removeClass('d-none');
                else $('#teacher_substitute').prop('required',false).attr("disabled", true).closest('.form-row').addClass('d-none');            
            });   

            $("#coachEditPage").on("click", ".remove-user", function(e){
                e.preventDefault();
                var url = $(this).data('ajax'),
                    id = $(this).data('id');

                var agreed = confirm('Opravdu chcete deaktivovat tohoto uživatele?');
                if(agreed){
                    GYM._post(url, {'user_id':id}).done(function(res){
                        if(!res.error){
                            N.show('success', 'Uživatel byl deaktivován!');
                            $('.remove-user').data('ajax','/admin/users/activate_user_ajax').text("Aktivovat uživatele").removeClass("btn-danger").addClass("btn-success").removeClass("remove-user").addClass("activate-user");
                        }else N.show('error', 'Nepovedlo se deaktivovat uživatele, zkuste to prosím znovu.');
                    });
                }
            });
            $("#coachEditPage").on("click", ".activate-user", function(e){
                e.preventDefault();
                var url = $(this).data('ajax'),
                    id = $(this).data('id');

                var agreed = confirm('Opravdu chcete znovu aktivovat tohoto uživatele?');
                if(agreed){
                    GYM._post(url, {'user_id':id}).done(function(res){
                        if(!res.error){
                            N.show('success', 'Uživatel byl aktivován!');
                            $('.activate-user').data('ajax','/admin/users/remove_user_ajax').text("Deaktivovat uživatele").addClass("btn-danger").removeClass("btn-success").addClass("remove-user").removeClass("activate-user");
                        }else N.show('error', 'Nepovedlo se aktivovat uživatele, zkuste to prosím znovu.');
                    });
                }
            });

            self.save_submit_btn.click(function(e){
                e.preventDefault();
                GYM._submitForm({
                    url: $(this).data('ajax'),
                    form: $(self.user_form)[0],
                    succes_text: 'Uživatelský účet byl úspěšně upraven!',
                    error_text: 'Nepovedlo se uložit změny, zkuste to prosím znovu.',
                });

            });

            self.remote_modal.on('submit', self.cancelLesson_form, function(e){
                e.preventDefault();
                GYM._submitForm({
                    url: $(this).data('ajax'),
                    form:$(this)[0],
                    succes_text: 'Úprava lekce byla zaznamenána!',
                    error_text: 'Nepodařilo se upravit lekci, zkontrolujte údaje nebo to zkuste znovu!',
                    success_function: function(){
                        self.remote_modal.modal('hide');
                        self.comingLessons_table.setData(self.lessons_table_url,{coach_id:self.user_id,past:false});
                    }
                });
            });            

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
        },
        initComingLessonsTable: function(){
            this.comingLessons_table = new Tabulator(this.comingLessons_table, {
                layout: 'fitColumns',
                placeholder:"Nebyly nalezeny žádné nadcházející lekce",
                resizableColumns: false,
                pagination: 'remote',
                ajaxSorting:true,
                ajaxFiltering:true,
                paginationSize: 20,
                paginationSizeSelector:[10, 20, 30, 50, 100],
                langs: GYM.tabulator_czech,
                headerFilterPlaceholder: "Hledat..",
                layoutColumnsOnNewData:true,
                columns: [
                    {title: 'NÁZEV LEKCE', field: 'lesson_name', headerFilter:true},
                    {title: 'ZAČÁTEK LEKCE', field: 'starting_on', headerFilter:true, editable:false, editor:this.dateEditor, formatter:"datetime",formatterParams:this.dateFormatterParams },
                    {title: 'KONEC LEKCE', field: 'ending_on', headerFilter:true, editable:false, formatter:this.returnEndingOn},
                    {title: 'KLIENTŮ', field: 'registered_clients', align:'right', headerFilter:true, formatter:this.returnRegClients},
                    {title: '', align: 'right', headerSort:false, formatter: this.returnTableButtons, width: 100}
                ],
                rowFormatter:function(row){
                    var rowData = row.getData();
                    if(rowData.canceled == 1){
                        let popoverContent = '<b>Lekce zrušena s důvodem:</b> '+rowData.cancel_reason;
                        $(row.getElement()).addClass('canceled-red').attr('data-toggle','popover').attr('data-content',popoverContent).attr('data-placement','bottom').attr('data-trigger','hover').attr('data-htm','true');
                        $('[data-toggle="popover"]').popover(); 
                    } else if(rowData.participate == 0){
                        let popoverContent = '<b>Lekce nahrazena trenérem / instruktorem:</b> '+rowData.teacher_substitute;
                        $(row.getElement()).addClass('canceled-green').attr('data-toggle','popover').attr('data-content',popoverContent).attr('data-placement','bottom').attr('data-trigger','hover').attr('data-htm','true');
                        $('[data-toggle="popover"]').popover(); 
                    }
                }
            });
            this.comingLessons_table.setLocale("cs-cs");
            this.comingLessons_table.setData(this.lessons_table_url,{coach_id:this.user_id,past:false});        
        }, 
        initEndedLessonsTable: function(){
            this.endedLessons_table = new Tabulator(this.endedLessons_table, {
                layout: 'fitColumns',
                placeholder:"Nebyly nalezeny žádné proběhlé lekce",
                resizableColumns: false,
                pagination: 'remote',
                ajaxSorting:true,
                ajaxFiltering:true,
                paginationSize: 20,
                paginationSizeSelector:[10, 20, 30, 50, 100],
                langs: GYM.tabulator_czech,
                headerFilterPlaceholder: "Hledat..",
                layoutColumnsOnNewData:true,
                columns: [
                    {title: 'NÁZEV LEKCE', field: 'lesson_name', headerFilter:true},
                    {title: 'ZAČÁTEK LEKCE', field: 'starting_on', headerFilter:true, editable:false, editor:this.dateEditor, formatter:"datetime",formatterParams:this.dateFormatterParams },
                    {title: 'KONEC LEKCE', field: 'ending_on', headerFilter:true, editable:false, formatter:this.returnEndingOn},
                    {title: 'KLIENTŮ', field: 'registered_clients', align:'right', headerFilter:true, formatter:this.returnRegClients},
                    {title: '', align: 'right', headerSort:false, formatter: this.returnTableButtons, width: 100}
                ],
                rowFormatter:function(row){
                    var rowData = row.getData();
                    if(rowData.canceled == 1){
                        let popoverContent = '<b>Lekce zrušena s důvodem:</b> '+rowData.cancel_reason;
                        $(row.getElement()).addClass('canceled-red').attr('data-toggle','popover').attr('data-content',popoverContent).attr('data-placement','bottom').attr('data-trigger','hover').attr('data-htm','true');
                        $('[data-toggle="popover"]').popover(); 
                    } else if(rowData.participate == 0){
                        let popoverContent = '<b>Lekce nahrazena trenérem / instruktorem:</b> '+rowData.teacher_substitute;
                        $(row.getElement()).addClass('canceled-green').attr('data-toggle','popover').attr('data-content',popoverContent).attr('data-placement','bottom').attr('data-trigger','hover').attr('data-htm','true');
                        $('[data-toggle="popover"]').popover(); 
                    }
                }
            });
            this.endedLessons_table.setLocale("cs-cs");         
        },         
        dateFormatterParams: {
            inputFormat:"YYYY-MM-DD HH:mm:ss",
            outputFormat:tabulatorDateFormat+' '+tabulatorTimeFormat, // variable from main.js
            invalidPlaceholder:"(invalid date)"
        }, 
        returnEndingOn: function(cell){
            var row_data = cell._cell.row.data;
            return moment(row_data.ending_on, "YYYY-MM-DD h:m:s").format(tabulatorDateFormat+' '+tabulatorTimeFormat);
        }, 
        returnRegClients: function(cell){
            var row_data = cell._cell.row.data;
            return row_data.registered_clients+' / '+row_data.client_limit;
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
    }
}());

COACH_DETAIL.init();