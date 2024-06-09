'use strict';

var SERVICES = SERVICES || (function () {
    var self;
    return {        
        gym_services_table: '#gymServicesTable',
        gym_services_table_url: $('#gymServicesTable').data("ajax"),
        gym_service_form: '#gymServiceForm',
        
        remote_modal: $('#modal'),
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

            this.gym_services_table = new Tabulator(this.gym_services_table, {
                layout: 'fitColumns',
                placeholder:"Nebyly nalezeny žádné služby",
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
                    {title: 'AUTOR', field: 'author_name', headerFilter: true},
                    {title: 'PEREX', field: 'perex', headerFilter: true},
                    {title: 'VYTVOŘENO', field: 'created_date', headerFilter: true, formatter:"datetime",editor:this.dateEditor,formatterParams:SERVICES.dateFilterParamas},
                    {title: 'STATUS', field: 'state', headerFilter:"select", headerFilterParams: SERVICES.active_picker, formatter: this.returnStatus,editable:false},
                    {title: '', align: 'right', headerSort:false, formatter: this.returnTableButtons}
                ]
            });
            this.gym_services_table.setLocale("cs-cs");            
            this.gym_services_table.setData(this.gym_services_table_url);
            this.fireEvents();
        },
        returnStatus: function(cell){
            var row_data = cell._cell.row.data;
            if(row_data.state != 1) return '<span class="icon icon-circle s-12 mr-2"></span> Neaktivní';
            else return '<span class="icon icon-circle s-12 mr-2 text-info"></span> Aktivní';
        },        
        returnName: function(cell, params, onRendered){
            var row_data = cell._cell.row.data;
            return '<a href="javascript:;" data-toggle="modal" data-remote="/admin/cms/edit_gym_service/'+row_data.id+'" data-target="#modal" data-modal-title="Editace položky" data-modal-submit="Uložit">'+row_data.name+'</a>';
        },
        returnTableButtons: function(cell, params, onRendered){
            var row_data = cell._cell.row.data;
            var edit_button = '<a href="javascript:;" data-toggle="modal" data-remote="/admin/cms/edit_gym_service/'+row_data.id+'" data-target="#modal" data-modal-title="Editace položky" data-modal-submit="Uložit"><i class="icon-pencil"></i></a>&nbsp;';
            var delete_button = '<a href="javascript:;" class="gym_service-remove text-danger ml-2" data-id="'+row_data.id+'" data-ajax="/admin/cms/remove_gym_service_ajax"><i class="icon-close"></i></a>';            
            return edit_button+delete_button;
        },  
        dateEditor: function(cell, onRendered, success, cancel, editorParams){
            var editor = document.createElement("input");
            flatpickr(editor, { dateFormat: flatpickrDateFormat });

            //create and style input
            editor.style.padding = "4px";
            editor.style.width = "100%";
            editor.style.boxSizing = "border-box";

            //set focus on the select box when the editor is selected (timeout allows for editor to be added to DOM)
            onRendered(function(){
                editor.focus();
                editor.style.css = "100%";
            });
            function successFunc(){
                success(moment(editor.value, tabulatorDateFormat).format("YYYY-MM-DD"));
            }
            editor.addEventListener("change", successFunc);
            return editor;
        },              
        fireEvents: function(){

            GYM._media(); // init media
            $(document).on('change', '.js-media-input-target-id', function(){
                var img = $(this).attr('data-img');
                $(this).prev('.image-preview').css('background-image', 'url(' + img + ')').addClass('uploaded');
            });                      

            $("#js_services_clear_filter").click(function(){
                self.gym_services_table.clearHeaderFilter();
                self.gym_services_table.clearSort();
            });

            $('body').on('click', '[data-toggle="modal"]', function(){
                $($(this).data("target")+' .modal-body').load($(this).data("remote"), function(){
                    $('.select2').select2({
                        height: '25px',
                        minimumResultsForSearch: -1,
                        templateResult: select2ServiceIcons,
                        templateSelection: select2ServiceIconsSelected
                    });
                    if(self.remote_modal.find('.js-trumbowyg-editor').length) TRUMBOWYG.init();
                });
            });  

            function select2ServiceIcons (option) {
                var img_src = $(option.element).data('thumbnail');
                return $('<span><img class="p-1" src='+img_src+' style="background:#d9c37c; width:30px; height:30px;" /></span>');;
            }; 

            function select2ServiceIconsSelected (option) {
                var img_src = $(option.element).data('thumbnail');
                return $('<span><img src='+img_src+' style="background:#d9c37c; width:20px; height:20px; padding:1px;" /></span>');;
            };            

            self.remote_modal.on('show.bs.modal', function(e){
                $(e.target).find('.modal-title').text($(e.relatedTarget).data('modal-title'));
                self.btn_modal_submit.text($(e.relatedTarget).data('modal-submit'));
            });

            self.btn_modal_submit.click(function(){
                $(this).closest('.modal-content').find('form').submit();
            });       

            // flatpickr
            $(".js-time-input").flatpickr({
                enableTime: true,
                noCalendar: true,
                dateFormat: flatpickrTimeFormat,
                time_24hr: flatpickr24hr
            });               

            $('#gymServicesTable').on('click', '.gym_service-remove', function(){
                var url = $(this).data('ajax'),
                    id = $(this).data('id'),
                    agreed = confirm('Opravdu chcete vymazat tuto položku?');
                if(agreed){
                    GYM._post(url, {'service_id':id}).done(function(res){
                        if(!res.error){
                            self.gym_services_table.setData(self.gym_services_table_url);
                            N.show('success', 'Položka úspěšně smazána!');
                        }else N.show('error', 'Nepodařilo se smazat položku, zkuste to znovu.');
                        NProgress.done();
                    });
                }
            });            
            
            $('#cmsServicesPage').on('submit', self.gym_service_form, function(e){
                e.preventDefault();
                GYM._submitForm({
                    url: $(this).data('ajax'),
                    form:$(this)[0],
                    succes_text: 'Služba byla úspěšně uložena!',
                    error_text: 'Nepodařilo se uložit službu, zkontrolujte údaje nebo to zkuste znovu!',
                    success_function: function(){
                        self.remote_modal.modal('hide');
                        self.gym_services_table.setData(self.gym_services_table_url);
                    }                       
                });
            });            

        }
    }
}());

SERVICES.init();