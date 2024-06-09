'use strict';

var BLOG = BLOG || (function () {
    var self;
    return {
        posts_table: '#postsTable',
        posts_table_url: $('#postsTable').data("ajax"),
        posts_form: $('#addPostForm'),
        active_picker: {'1':'Aktivní','2':'Neaktivní'},
        dateFilterParamas: {
            inputFormat:"YYYY-MM-DD HH:mm:ss",
            outputFormat:tabulatorDateFormat+' '+tabulatorTimeFormat,
            invalidPlaceholder:"(invalid date)"
        },
        role: null,
        
        init: async function(params){
            self = this;
            this.role = await GYM._role();
            NProgress.configure({ parent: '#app', minimum: 0.1, showSpinner: false });            

            this.posts_table = new Tabulator(this.posts_table, {
                layout: 'fitColumns',
                placeholder:"Nebyly nalezeny žádné příspěvky",
                resizableColumns: false,
                pagination: 'remote',
                ajaxSorting:true,
                ajaxFiltering:true,
                paginationSize: 20,
                paginationSizeSelector:[10, 20, 30, 50, 100],
                langs: GYM.tabulator_czech,
                layoutColumnsOnNewData:true,
                headerFilterPlaceholder:"Hledat..",
                columns: [
                    {title: 'NÁZEV', field: 'name', headerFilter:true,formatter: this.returnName},
                    {title: 'AUTOR', field: 'author_name', headerFilter:true},
                    {title: 'PUBLIKOVÁNO OD', field: 'publish_from', headerFilter:true,formatter:"datetime",editor:this.dateEditor,formatterParams:BLOG.dateFilterParamas},
                    {title: 'PUBLIKOVÁNO DO', field: 'publish_to', headerFilter:true,formatter:"datetime",editor:this.dateEditor,formatterParams:BLOG.dateFilterParamas},
                    {title: 'STATUS', field: 'state', headerFilter:"select", headerFilterParams: BLOG.active_picker, formatter: this.returnStatus,editable:false},
                    {title: 'AKCE', align: 'right', headerSort:false, formatter: this.returnTableButtons, width:100}
                ]
            });
            this.posts_table.setLocale("cs-cs");
            this.posts_table.setData(this.posts_table_url);

            this.fireEvents();
        },
        returnName: function(cell, params, onRendered){
            var row_data = cell._cell.row.data;
            return '<a href="/admin/blog/edit-post/'+row_data.id+'">'+row_data.name+'</a>';
        },
        returnTableButtons: function(cell, params, onRendered){
            var row_data = cell._cell.row.data;
            var edit_button = '<a href="/admin/blog/edit-post/'+row_data.id+'" data-id="'+row_data.id+'"><i class="icon-pencil"></i></a>&nbsp;';
            var delete_button = '<a href="javascript:;" class="post-remove text-danger ml-2" data-id="'+row_data.id+'" data-ajax="/admin/blog/remove_post_ajax" title="Vymazat příspěvěk"><i class="icon-delete"></i></a>';
            
            if((parseInt(row_data.pin) || 0)==0){
                var pinIcon = 'thumb-tack';
                var pinTitle = 'Připnout příspěvek';
            } else { 
                var pinIcon = 'cancel';
                var pinTitle = 'Odepnout příspěvek';
            }
            var pin_button = '<a href="javascript:;" class="post-pin text-success ml-2" data-id="'+row_data.id+'" data-pinned="'+(parseInt(row_data.pin) || 0)+'" data-ajax="/admin/blog/pin_post_ajax" title="'+pinTitle+'"><i class="icon-'+pinIcon+'"></i></a>';

            return edit_button+pin_button+delete_button;
        },
        returnStatus: function(cell){
            var row_data = cell._cell.row.data;
            if(row_data.state != 1) return '<span class="icon icon-circle s-12 mr-2"></span> Neaktivní';
            else return '<span class="icon icon-circle s-12 mr-2 text-info"></span> Aktivní';
        },
        dateEditor: function(cell, onRendered, success, cancel, editorParams){
            //cell - the cell component for the editable cell
            //onRendered - function to call when the editor has been rendered
            //success - function to call to pass the successfuly updated value to Tabulator
            //cancel - function to call to abort the edit and return to a normal cell
            //editorParams - params object passed into the editorParams column definition property

            //create and style editor
            var editor = document.createElement("input");

            flatpickr(editor, {
                dateFormat: flatpickrDateFormat
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
                success(moment(editor.value, tabulatorDateFormat).format("YYYY-MM-DD"));
            }

            editor.addEventListener("change", successFunc);

            //return the editor element
            return editor;
        },        
        fireEvents: function(){

            GYM._media(); // init media
            $(document).on('change', '.js-media-input-target-id', function(){
                var img = $(this).attr('data-img');
                $('.image-preview').css('background-image', 'url(' + img + ')').addClass('uploaded');
            });  

            $("#js_posts_clear_filter").click(function(){
                self.posts_table.clearHeaderFilter();
                self.posts_table.clearSort();
            });         

            // flatpickr
            $(".js-flatpickr-date").flatpickr({
                minDate: "today",
                altInput: true,
                altFormat: flatpickrDateFormat,
                dateFormat: "Y-m-d",
                onOpen: function(selectedDates, dateStr, instance){
                    if($(instance.element).attr('id') == 'publish_date_to' && $("#publish_date_from").val()){
                        instance.set('minDate', $("#publish_date_from").val());
                    }
                }
            });

            // flatpickr
            $(".js-flatpickr-time").flatpickr({
                enableTime: true,
                noCalendar: true,
                dateFormat: flatpickrTimeFormat,
                time_24hr: flatpickr24hr,
            });             

            $('#postsTable').on('click', '.post-remove', function(){
                var url = $(this).data('ajax'),
                    id = $(this).data('id');

                var agreed = confirm('Opravdu chcete vymazat tento příspěvek?');
                if(agreed){
                    GYM._post(url, {'post_id':id}).done(function(res){
                        if(!res.error){
                            self.posts_table.setData(self.posts_table_url);
                            N.show('success', 'Příspěvek úspěšně smazán!');
                        }else N.show('error', 'Nepodařilo se smazat příspěvek, zkuste to znovu.');
                        NProgress.done();
                    });
                }
            });

            $('#postsTable').on('click', '.post-pin', function(){
                var url = $(this).data('ajax'),
                    id = $(this).data('id'),
                    pinned = $(this).data('pinned');

                GYM._post(url, {'post_id':id, 'pinned':pinned}).done(function(res){
                    if(!res.error){
                        self.posts_table.setData(self.posts_table_url);
                        if(pinned==0) N.show('success', 'Příspěvek úspěšně připnut!');
                        else N.show('success', 'Příspěvek úspěšně odepnut!');
                    }else {
                        if(pinned==0) N.show('error', 'Nepodařilo se připnout příspěvek, zkuste to znovu.');
                        else N.show('error', 'Nepodařilo se odepnout příspěvek, zkuste to znovu.'); 
                    }
                    NProgress.done();
                });
            });            

            this.posts_form.submit(function(e){
                e.preventDefault();
                var url = $(this).data("ajax"),
                    formData = new FormData(self.posts_form[0]);
                var inputs = $(this).find('input:required, select:required');

                $.each(inputs, function(i, input){
                    if($(input).val()){
                        $(input).removeClass("invalid");
                    }else{
                        if($(input).attr("type") != "checkbox") $(input).addClass("invalid");
                    }
                });

                if(!$('#image').val()){
                    $('.blog.image-preview').addClass('invalid');
                    return false;
                } else $('.blog.image-preview').removeClass("invalid");

                if($(this).find(".invalid").length <= 0){
                    GYM._upload(url, formData).done(function(res){
                        if(!res.error){
                            N.show('success', 'Příspěvěk byl úspěšně vytvořen!');
                            self.posts_form[0].reset();
                            self.posts_table.setData(self.posts_table_url);
                            $('#v-pills-list-tab').click();

                            self.article_image = null;
                            self.image_preview.removeClass('uploaded');
                            self.image_preview.css('background_image', 'inherit');
                        }else{
                            N.show('error', 'Nepodařilo se vytvořit příspěvek, zkontrolujte údaje nebo to zkuste znovu!');
                        }
                        
                        NProgress.done();
                    });
                } else N.show('error', 'Formulář obsahuje chyby nebo chybí povinné údaje, zkontrolujte červeně označená pole!');

            });
        }
    }
}());

BLOG.init();