'use strict';

var LESSONS = LESSONS || (function () {
    var self;
    return {
        role: false,

        add_template_form: '#addTemplateForm',
        edit_template_form: '#editTemplateForm',

        remote_modal: $('#modal, #tagsModal'),
        btn_modal_submit: $('#modalSubmit'),      

        delete_template: '.deleteLessonTemplate',

        lessons_table: '#lessonTable',
        lessons_table_url: $('#lessonTable').data("ajax"),

        init: async function(params){
            self = this;

            this.role = await GYM._role();
            NProgress.configure({ parent: '#app', minimum: 0.1, showSpinner: false });

            this.lessons_table = new Tabulator(this.lessons_table, {
                layout: 'fitColumns',
                placeholder:"Nebyli nalezeny žádné lekce",
                resizableColumns: false,
                pagination: 'remote',
                ajaxSorting:true,
                ajaxFiltering:true,
                paginationSize: 20,
                paginationSizeSelector:[10, 20, 30, 50, 100],
                langs: GYM.tabulator_czech,
                layoutColumnsOnNewData:true,
                columns: [
                    {title: 'NÁZEV', field: 'name', headerFilter:true, headerFilterPlaceholder:"Hledat podle názvu", formatter:this.returnName },
                    {title: '', align: 'right', field: '', formatter: this.rtb}
                ]
            });
            this.lessons_table.setLocale("cs-cs");
            this.lessons_table.setData(this.lessons_table_url);

            this.fireEvents();
        },
        returnName: function(cell, params, onRendered){
            var d = cell.getRow().getData();
            return '<a href="javascript:;" data-toggle="modal" data-remote="/admin/lessons/edit_template/'+d.id+'" data-target="#modal" data-modal-title="Editace položky" data-modal-submit="Uložit">'+d.name+'</a>&nbsp;';

        },
        rtb: function(c){
            var d = c.getRow().getData();
            var isrecurring = '';
            if(typeof d.created_by == "undefined"){
                isrecurring = 'data-date="'+moment(d.ending_on).format('YYYY-MM-DD')+'"';
            }

            var edit_button = '<a href="javascript:;" data-toggle="modal" data-remote="/admin/lessons/edit_template/'+d.id+'" data-target="#modal" data-modal-title="Editace položky" data-modal-submit="Uložit"><i class="icon-pencil"></i></a>&nbsp;&nbsp;';
            var delete_button = '<a href="javascript:;" class="deleteLessonTemplate" data-lesson="'+d.id+'" '+isrecurring+'><i class="icon-delete text-red"></i></a>';

            return edit_button + delete_button;
        },
        dateEditor: function(cell, onRendered, success, cancel, editorParams){
            //create and style editor
            var editor = document.createElement("input");

            flatpickr(editor, {
                dateFormat: "d.m.Y"
            });

            //create and style input
            editor.style.padding = "4px";
            editor.style.width = "100%";
            editor.style.boxSizing = "border-box";

            //set focus on the select box when the editor is selected (timeout allows for editor to be added to DOM)
            onRendered(function(){
                editor.focus();
                editor.style.css = "100%";
            });

            //when the value has been set, trigger the cell to update
            function successFunc(){
                success(moment(editor.value, "DD.MM.YYYY").format("YYYY-MM-DD"));
            }

            editor.addEventListener("change", successFunc);

            //return the editor element
            return editor;
        },
        fireEvents: function(){

            TRUMBOWYG.init();
            GYM._media(); // init media
            $(document).on('change', '.js-media-input-target-id', function(){
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
                self.btn_modal_submit.text($(e.relatedTarget).data('modal-submit'));
            });

            self.remote_modal.on('shown.bs.modal', function(e){
                // init tabulator tags
                if($(e.target).is("#tagsModal")) setTimeout(function(){ self.tagsTabulator().init(); }, 100);
            });   
            
            self.remote_modal.on('hide.bs.modal', function(e){
                if($(e.target).is("#tagsModal")){
                    let type = $(e.target).find('#tag_table').data('type');
                    GYM._post('/admin/lessons/get-template-tags-ajax').done(function(res){
                        $.each(res.data, function(i,v){ // rename key name => text (for select2)
                            delete Object.assign(v, {['text']: v['name'] })['name'];
                        });
                        var values = $('#tags').val();
                        $('#tags').select2().empty();
                        $('#tags').select2({
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

            $('#lessonsTemplatesPage').on('click', self.delete_template, function(e){
                var id = $(this).data('lesson');
                console.log(id);
                var agreed = confirm('Opravdu chcete vymazat tuto lekci? Akci neni možné vrátit.');

                if(agreed){
                    GYM._post('/admin/lessons/delete_template_ajax', {template_id: id}).done(function(res){
                        if(!res.error){
                            N.show('success', 'Lekce byla úspešně smazána.');
                            self.lessons_table.setData(self.lessons_table_url);
                        }else{
                            N.show('error', 'Nepovedlo se smazat lekci, zkuste to znovu.');
                        }
                    });
                }
            });

            $('#lessonsTemplatesPage').on('change', '#pricelist_id', function(e){
                let duration = $('#pricelist_id option:selected').data('duration');
                $('#lesson_duration').val(duration);
            });

            $('#lessonsTemplatesPage').on('submit', self.add_template_form, function(e){
                e.preventDefault();
                GYM._submitForm({
                    url: $(this).data('ajax'),
                    form:$(this)[0],
                    succes_text: 'Lekce byla úspešně přidána.',
                    error_text: 'Nepovedlo se uložit lekci, zkuste to znovu.',
                    success_function: function(){
                        self.remote_modal.modal('hide');
                        self.lessons_table.setData(self.lessons_table_url);
                    }
                });
            });

            $('#lessonsTemplatesPage').on('submit', self.edit_template_form, function(e){
                e.preventDefault();
                GYM._submitForm({
                    url: $(this).data('ajax'),
                    form:$(this)[0],
                    succes_text: 'Lekce byla úspešně uložena.',
                    error_text: 'Nepovedlo se uložit lekci, zkuste to znovu.',
                    success_function: function(){
                        self.remote_modal.modal('hide');
                        self.lessons_table.setData(self.lessons_table_url);
                    }
                });
            });            

            $(".switch-to-table").click(function(){ setTimeout(function(){ self.lessons_table.redraw(true); }, 100); });
        },
        tagsTabulator: function(){
            return {
                tags_table: '#tags_table',
                tags_table_url: $('#tags_table').data("ajax"),
                tags_table_type: $('#tags_table').data("type"),
                init: async function(params){
                    NProgress.configure({ parent: '#app', minimum: 0.1, showSpinner: false });  
                    this.tags_table = new Tabulator(this.tags_table, {
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
                    this.tags_table.setLocale("cs-cs");
                    this.tags_table.setData(this.tags_table_url);
                    this.fireEvents();
                },
                returnTableButtons: function(cell, params, onRendered){
                    var row_data = cell._cell.row.data;
                    if(row_data.id>0){
                        var save_button = '<a href="javascript:;" class="btn btn-xs btn-primary js-save-btn" data-url="/admin/lessons/save-template-tag-ajax" data-id="'+row_data.id+'">Uložit</a>&nbsp;';
                        var delete_button = '<a href="javascript:;" class="btn btn-xs btn-danger js-delete-btn" data-url="/admin/lessons/delete-template-tag-ajax" data-id="'+row_data.id+'" title="Vymazat"><i class="icon-trash"></i></a>&nbsp;';
                        return save_button+delete_button;
                    } else return '<a href="javascript:;" class="btn btn-xs btn-primary js-save-btn" data-url="/admin/lessons/save-template-tag-ajax" data-id="'+row_data.id+'">Uložit</a>&nbsp;';
                    
                },
                fireEvents: function(){                    
                    var self = this;
        
                    $("#js_add_tag").on("click", function(e) { 
                        self.tags_table.addData([{name:"Nový",actions:""}], true);
                        $("#tags_table").find('.tabulator-row:first-child .tabulator-cell:first-child').click();
                    });            
        
                    // clear filter
                    $(".js-clear-filter").click(function(){
                        self.tags_table.clearHeaderFilter();
                        self.tags_table.clearSort();
                    });                                
        
                    $("#tags_table").on('click','.js-save-btn',function(e){
                        e.preventDefault();
                        var url = $(this).data('url'),
                            item_id = $(this).data('id'),
                            item_name = $(this).closest('.tabulator-row').find('.tabulator-cell:first-child').text();

                        GYM._post(url, {item_id:item_id, item_name:item_name}).done(function(res){
                            if(!res.error){
                                N.show('success', 'Položka uložena!');
                                self.tags_table.setData(self.tags_table_url);
                            }else N.show('error', 'Nepovedlo se uložit položku, zkuste to prosím znovu.');
                        });                                         
                    }); 
                    $("#tags_table").on('click','.js-delete-btn',function(e){
                        e.preventDefault();

                        var url = $(this).data('url'),
                            item_id = $(this).data('id'),
                            item_name = $(this).closest('.tabulator-row').find('.tabulator-cell:first-child').text();

                        GYM._post(url, {item_id:item_id}).done(function(res){
                            if(!res.error){
                                N.show('success', 'Položka smazána!');
                                self.tags_table.setData(self.tags_table_url);
                            }else N.show('error', 'Nepovedlo se smazat položku, zkuste to prosím znovu.');
                        });                                             
                    });                        
                }            
            }
        }
    }
}());

LESSONS.init();