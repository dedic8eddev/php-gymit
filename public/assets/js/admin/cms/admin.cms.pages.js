'use strict';

var PAGES = PAGES || (function () {
    var self;
    return {        
        pages_table: '#pagesTable',
        pages_table_url: $('#pagesTable').data("ajax"),
        page_form: '#pageForm',
        
        remote_modal: $('#modal, #equipmentModal'),
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

            this.pages_table = new Tabulator(this.pages_table, {
                layout: 'fitColumns',
                placeholder:"Nebyly nalezeny žádné stránky",
                resizableColumns: false,
                pagination: 'remote',
                ajaxSorting:true,
                ajaxFiltering:true,
                paginationSize: 20,
                paginationSizeSelector:[10, 20, 30, 50, 100],
                langs: GYM.tabulator_czech,
                layoutColumnsOnNewData:true,
                columns: [
                    {title: 'NÁZEV', field: 'name', headerFilter:true, formatter: this.returnName},
                    {title: '', align: 'right', headerSort:false, formatter: this.returnTableButtons}
                ]
            });
            this.pages_table.setLocale("cs-cs");            
            this.pages_table.setData(this.pages_table_url);
            this.fireEvents();
        },       
        returnName: function(cell, params, onRendered){
            var row_data = cell._cell.row.data;
            var data = JSON.parse(row_data.data);         
            return '<a href="javascript:;" data-toggle="modal" data-remote="/admin/cms/edit_page/'+row_data.type+'?'+self.queryString4PHP(data.blocks,'blocks')+'" data-target="#modal" data-modal-title="Editace stránky '+row_data.name+'" data-modal-submit="Uložit">'+row_data.name+'</a>';
        },
        returnTableButtons: function(cell, params, onRendered){
            var row_data = cell._cell.row.data;
            var data = JSON.parse(row_data.data);
            var edit_button = '<a href="javascript:;" data-toggle="modal" data-remote="/admin/cms/edit_page/'+row_data.type+'?'+self.queryString4PHP(data.blocks,'blocks')+'" data-target="#modal" data-modal-title="Editace stránky '+row_data.name+'" data-modal-submit="Uložit"><i class="icon-pencil"></i></a>&nbsp;';
            //var delete_button = '<a href="javascript:;" class="page-remove text-danger ml-2" data-id="'+row_data.id+'" data-ajax="/admin/cms/remove_page_ajax"><i class="icon-close"></i></a>';            
            return edit_button;
        },            
        fireEvents: function(){

            GYM._media(); // init media
            $(document).on('change', '.js-media-input-target-id', function(){
                var img = $(this).attr('data-img'),
                    imgPreview = $(this).prev('.image-preview');
                if(imgPreview.hasClass('equipment')){ // equipment images
                    let rmIcon = '<div class="equipmentRemoveIcon"><i onclick="PAGES.rmEquipmentImg(this);" class="icon-remove s-18"></i></div>',
                        input = `<input type="hidden" name="${$(this).data('input-name')}[images][]" value="${img}" />`;
                    let equipmentImg = $(`<div class='imgCol col-md-2'><div class="aspect16_15 image-preview uploaded" style="min-height:0; cursor:default; background-image:url('${img}');"></div>${rmIcon}${input}</div>`);
                    imgPreview.closest('.imgCol').before(equipmentImg);
                } else imgPreview.css('background-image', 'url(' + img + ')').addClass('uploaded'); // classic images
                $(this).val(img); // change
            });                      

            $("#js_pages_clear_filter").click(function(){
                self.pages_table.clearHeaderFilter();
                self.pages_table.clearSort();
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
            
            self.remote_modal.on('hide.bs.modal', function(e){
                if($(e.target).is("#equipmentModal")){
                    GYM._post('/admin/gyms/get-equipment-ajax').done(function(res){
                        $.each(res.data, function(i,v){ // rename key name => text (for select2)
                            delete Object.assign(v, {['text']: v['name'] })['name'];
                        });
                        var values = $('#equipment').val();
                        $('#equipment').select2().empty();
                        $('#equipment').select2({
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

            self.remote_modal.on('show.bs.modal', function(e){
                $(e.target).find('.modal-title').text($(e.relatedTarget).data('modal-title'));
                self.btn_modal_submit.text($(e.relatedTarget).data('modal-submit'));
            });

            self.remote_modal.on('shown.bs.modal', function(e){
                // init tabulator equipment
                if($(e.target).is("#equipmentModal")) setTimeout(function(){ self.equipmentTabulator().init(); }, 100);
            });              

            self.btn_modal_submit.click(function(){
                $(this).closest('.modal-content').find('form').submit();
            });                          
            
            $('#cmsPagesPage').on('submit', self.page_form, function(e){
                e.preventDefault();
                GYM._submitForm({
                    url: $(this).data('ajax'),
                    form:$(this)[0],
                    succes_text: 'Stránka byla úspěšně uložena!',
                    error_text: 'Nepodařilo se uložit stránku, zkontrolujte údaje nebo to zkuste znovu!',
                    success_function: function(){
                        self.remote_modal.modal('hide');
                        self.pages_table.setData(self.pages_table_url);
                    }                       
                });
            }); 
        },
        queryString4PHP: function(obj, prefix) { // returns url encoded string easy readable for PHP
            var str = [];
            for (var p in obj) {
                if (obj.hasOwnProperty(p)) {
                    var k = prefix ? prefix + "[" + p + "]" : p,
                        v = obj[p];
                    str.push((v !== null && typeof v === "object") ? self.queryString4PHP(v, k) : encodeURIComponent(k) + "=" + encodeURIComponent(v));
              }
            }
            return str.join("&");
        },
        rmEquipmentImg: function(el) {
            $(el).closest('.imgCol').fadeOut(300, function() { $(this).remove(); });
        },
        equipmentTabulator: function(){
            return {
                equipment_table: '#equipment_table',
                equipment_table_url: $('#equipment_table').data("ajax"),
                init: async function(params){
                    NProgress.configure({ parent: '#app', minimum: 0.1, showSpinner: false });  
                    this.equipment_table = new Tabulator(this.equipment_table, {
                        layout: 'fitColumns',
                        placeholder:"Žádné vybavení nenalezeno",
                        resizableColumns: false,
                        pagination: 'remote',
                        paginationSize: 20,
                        paginationSizeSelector:[10, 20, 30, 50, 100],
                        layoutColumnsOnNewData:true,
                        langs: GYM.tabulator_czech,
                        columns: [
                            {title: 'Vybavení', field: 'name', headerSort:true, headerFilter:true, editor:"input", editable:true, widthGrow:2 },
                            {title: 'Akce', field: 'actions', align: 'right', headerSort:false, editable:false, formatter: this.returnTableButtons},
                        ]
                    });
                    this.equipment_table.setLocale("cs-cs");
                    this.equipment_table.setData(this.equipment_table_url);
                    this.fireEvents();
                },
                returnTableButtons: function(cell, params, onRendered){
                    var row_data = cell._cell.row.data;
                    if(row_data.id>0){
                        var save_button = '<a href="javascript:;" class="btn btn-xs btn-primary js-save-btn" data-url="/admin/gyms/save-equipment-ajax/" data-id="'+row_data.id+'">Uložit</a>&nbsp;';
                        var delete_button = '<a href="javascript:;" class="btn btn-xs btn-danger js-delete-btn" data-url="/admin/gyms/delete-equipment-ajax/" data-id="'+row_data.id+'" title="Vymazat"><i class="icon-trash"></i></a>&nbsp;';
                        return save_button+delete_button;
                    } else return '<a href="javascript:;" class="btn btn-xs btn-primary js-save-btn" data-url="/admin/gyms/save-equipment-ajax/" data-id="'+row_data.id+'">Uložit vybavení</a>&nbsp;';
                    
                },
                fireEvents: function(){                    
                    var self = this;
        
                    $("#js_add_equipment").on("click", function(e) { 
                        self.equipment_table.addData([{name:"Nové vybavení",actions:""}], true);
                        $("#equipment_table").find('.tabulator-row:first-child .tabulator-cell:first-child').click();
                    });            
        
                    // clear filter
                    $(".js-clear-filter").click(function(){
                        self.equipment_table.clearHeaderFilter();
                        self.equipment_table.clearSort();
                    });                                
        
                    $("#equipment_table").on('click','.js-save-btn',function(e){
                        e.preventDefault();
                        var url = $(this).data('url'),
                            item_id = $(this).data('id'),
                            item_name = $(this).closest('.tabulator-row').find('.tabulator-cell:first-child').text();

                        GYM._post(url, {item_id:item_id, item_name:item_name}).done(function(res){
                            if(!res.error){
                                N.show('success', 'Vybavení uloženo!');
                                self.equipment_table.setData(self.equipment_table_url);
                            }else N.show('error', 'Nepovedlo se uložit vybavení, zkuste to prosím znovu.');
                        });                                         
                    }); 
                    $("#equipment_table").on('click','.js-delete-btn',function(e){
                        e.preventDefault();

                        var url = $(this).data('url'),
                            item_id = $(this).data('id'),
                            item_name = $(this).closest('.tabulator-row').find('.tabulator-cell:first-child').text();

                        GYM._post(url, {item_id:item_id}).done(function(res){
                            if(!res.error){
                                N.show('success', 'Vybavení smazáno!');
                                self.equipment_table.setData(self.equipment_table_url);
                            }else N.show('error', 'Nepovedlo se smazat vybavení, zkuste to prosím znovu.');
                        });                                             
                    });                        
                }            
            }
        }        
    }
}());

PAGES.init();