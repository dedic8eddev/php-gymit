'use strict';

var MEDIA = MEDIA || (function () {
    var self;
    return {
        media_dropzone_el: '#js_media_dropzone',
        media_list: $("#js_media_list"),
        media_list_el: "#js_media_list",
        media_item_el_id: "#js_item_id_",
        media_item_el: '.js-media-item',
        media_item_template: $("#js_media_item_template"),
        media_form: $("#js_media_item_form"),
        media_item_delete_btn: $(".js-media-item-delete"),
        media_form_btn: $("#js_media_item_form_btn"),
        media_shuffle: null,
        selected_files: 0,
        number_upload_files: 0,

        droppedFiles: [],
        image_dropper: $('.mediaScrollContent'),

        init: function(params){
            self = this;

            // shuffle
            var shuffleElement = document.querySelector(this.media_list_el);
            this.media_shuffle = new Shuffle(shuffleElement,{
                itemSelector: this.media_item_el
            });

            this.fireEvents();
            
        },
        uploadDroppedFiles: function(){
            if (self.image_dropper.hasClass('is-uploading')) return false;
            self.image_dropper.addClass('is-uploading');

            if(self.droppedFiles){
                var total = self.droppedFiles.length,
                    x = 0;

                $.each( self.droppedFiles, function(i, file) {
                    var request_data = new FormData();
                    request_data.append('file', file);

                    GYM._upload('/admin/media/upload', request_data).done(function(res){
                        if(!res.error){
                            var data = res.data;
        
                            var template = null;
                                template = self.media_item_template.clone();
        
                                template.removeClass("hide");
                                template.addClass('js-media-item');
                                template.attr('data-groups','["'+data.type+'"]');
                                template.attr('data-name','["'+data.file+'"]');
                                
                                if(data.type == 'image'){
                                    template.addClass('media-item-type-image');
                                    template.find('img').attr('src',data.thumb);
                                    template.find('img').attr('title',data.file);
                                }else{
                                    template.addClass('media-item-type-default');
                                    template.find('img').after('<h5 class="text-white">'+data.type+'</h5>');
                                    template.find('img').remove();
                                }
        
                                template.attr("id",'js_item_id_'+data.id);
                                template.find('.media-item-toolbox__name').attr('title',data.file);
                                template.find('.media-item-toolbox__name').html(data.short_name);
                                template.find('.js-media-item-select').attr('data-id',data.id);
                                template.find('.js-media-item-edit').attr('data-id',data.id);
                                template.find('.js-media-item-delete').attr('data-id',data.id);
        
                                self.media_list.prepend(template);
                                self.media_shuffle.add(template);
        
                                if(x >= total){
                                    N.show('success', 'Nahrání proběhlo úspěšně');
                                    self.image_dropper.removeClass('is-uploading');
                                    self.droppedFiles = [];
                                }
                        }else{
                            N.show('error', res.error);
                            if(x >= total){
                                self.image_dropper.removeClass('is-uploading');
                                self.droppedFiles = [];
                            }
                        }
                    });

                    x++;
                });
            }
        },
        fireEvents: function(){
            var self = this;

            if(GYM._has_draggable()){
                self.image_dropper.on('drag dragstart dragend dragover dragenter dragleave drop', function(e){
                    e.preventDefault();
                    e.stopPropagation();
                })
                .on('dragover dragenter', function(){
                    self.image_dropper.addClass('is-dragover');
                })
                .on('dragleave dragend drop', function(){
                    self.image_dropper.removeClass('is-dragover');
                })
                .on('drop', function(e){
                     self.droppedFiles = e.originalEvent.dataTransfer.files;
                     self.uploadDroppedFiles();
                });
            }

            // update shuffle
            $(".switch-to-media").click(function(){
                setTimeout(function(){ self.media_shuffle.update(); self.media_shuffle.layout(); }, 100);
            });

            // shuffle options filter by group
            $(".js-media-options-filter-btn").click(function(){
                var group = $(this).data('group');
                $('#js_media_options_filter_text').val('');
                self.media_shuffle.filter(group);
            });

            // shuffle filter by text
            $('#js_media_options_filter_text').keyup(_.debounce(function(){
                var value = $(this).val().toLowerCase().trim();          

                self.media_shuffle.filter(function (element,shuffle) {

                    // groups
                    if(shuffle.group !== Shuffle.ALL_ITEMS){
                        var groups = JSON.parse(element.getAttribute('data-groups'));
                        var isElementInCurrentGroup = groups.indexOf(shuffle.group) !== -1;

                        if (!isElementInCurrentGroup) {
                            return false;
                        }
                    }

                    var text = element.getAttribute('data-name').toLowerCase().trim();
                    return (text.indexOf(value) >= 0);
                });
            } , 300));

            // edit file
            self.media_list.on('click','.js-media-item-edit, .js-media-item-select', function(e){

                // do not show edit info while click on picture in iFrame in ModalBox
                if (window.self !== window.top && $(this).hasClass("js-media-item-select")) {
                    return true; // stop this function
                }

                var itemId = $(this).data('id'),
                    url = $(this).data('url');

                $.ajax({
                    url: url,
                    data: {id:itemId},
                    type: "POST",
                    dataType: "json",
                    beforeSend: function(){
                        NProgress.start();
                    },
                    success: function (response) {
                        NProgress.done();
                        
                        $("#js_media_detail_box_placeholder").addClass('hide');
                        //$("#js_media_detail_box_content").removeClass("hide");
                        $("#js_media_detail_box_footer").removeClass("hide");
                        $("#js_media_item_form_btn").data('id',response.data.id);
                        $("#js_media_item_form .js-media-item-delete").data('id',response.data.id);

                        $("#js_media_detail_box_name").html(response.data.file);
                        $("#js_media_detail_box_mime").html(response.data.mime);
                        $("#js_media_detail_box_size").html(response.data.size);
                        $("#js_media_detail_box_date_created").html(response.data.date_created_wh_time);

                        $("#js_media_detail_box_dimensions").closest('tr').removeClass('hide');
                        if(response.data.type == 'image'){
                            $("#js_media_detail_box_dimensions").html(response.data.width+"x"+response.data.height);
                        }else{
                            $("#js_media_detail_box_dimensions").closest('tr').addClass('hide');
                        }

                        if(response.data.meta_tags){
                            self.media_form.find('input[name="meta_tags[title]"]').val(response.data.meta_tags.title);
                            self.media_form.find('input[name="meta_tags[alt]"]').val(response.data.meta_tags.alt);
                        }else{
                            self.media_form.find('input[name="meta_tags[title]"]').val('');
                            self.media_form.find('input[name="meta_tags[alt]"]').val('');
                        }

                        $("#js_media_detail_box_content").slideDown(300,function(){});
                    },
                    error: function(response)
                    {   
                        response = response.responseJSON;
        
                        if(response){
                            N.show('error', response.message);
                        }else{
                            N.show('error', notificationsMsg.error);
                        }
                        NProgress.done();
                    }
                });

            });

            // update
            self.media_form_btn.click(function(e){
                e.preventDefault();

                var url = $(this).data("url"),
                    itemId = $(this).data('id'),
                    inputs = self.media_form.find('input, select'),
                    empty_items = 0,
                    requiredInputs = self.media_form.find('input:required, select:required'),
                    formData = new FormData(self.media_form[0]);
                $.each(requiredInputs, function(i, input){
                    if($(input).val()!=""){
                        if($(input).hasClass('select2') ){
                            $(".select2.select2-container").removeClass('invalid');
                        }
                        $(input).removeClass("invalid");
                    }else{
                        if($(input).hasClass('select2') ){
                            $(input).parent().find(".select2.select2-container").addClass('invalid');
                        }
                        $(input).addClass("invalid");
                    }
                });

                $.each(inputs, function(i, input){
                    if($(input).val()=="") empty_items=1;          
                });             

                if( self.media_form.find(".invalid").length > 0){
                    N.show('error', notificationsMsg.required_fields);
                    return false;
                }

                $.ajax({
                    url: url+itemId,
                    data: formData,
                    processData: false,
                    contentType: false,
                    type: "POST",
                    dataType: "json",
                    beforeSend: function(){
                        NProgress.start();
                    },
                    success: function (response) {
                        N.show('success', response.message);
                        NProgress.done();

                        // empty values ?
                        if(empty_items) alert("empty items");
                        else alert("success");
                    },
                    error: function(response)
                    {   
                        response = response.responseJSON;

                        if(response){
                            N.show('error', response.message);
                        }else{
                            N.show('error', notificationsMsg.error);
                        }
                        NProgress.done();
                    }
                });

            });

            // remove file
            self.media_item_delete_btn.click(function(e){
                e.preventDefault();
                // confirm
                if(!GYM._confirm(false)){
                    return false;
                }

                var itemId = $(this).data('id'),
                    url = $(this).data('url');

                $.ajax({
                    url: url,
                    data: {id:itemId},
                    type: "POST",
                    dataType: "json",
                    beforeSend: function(){
                        NProgress.start();
                    },
                    success: function (response) {
                        N.show('success', response.message);
                        NProgress.done();
                        
                        // remove file
                        $(self.media_item_el_id+itemId).remove();
                        self.media_shuffle.update();
                        self.media_shuffle.layout();
                    },
                    error: function(response)
                    {   
                        response = response.responseJSON;
        
                        if(response){
                            N.show('error', response.message);
                        }else{
                            N.show('error', notificationsMsg.error);
                        }
                        NProgress.done();
                    }
                });

            });


        }
    }
}());

MEDIA.init();