'use strict';

var BLOG = BLOG || (function () {
    var self;
    return {
        post_form: $('#savePostForm'),
        role: null,
        
        init: async function(params){
            self = this;
            this.role = await GYM._role();
            NProgress.configure({ parent: '#app', minimum: 0.1, showSpinner: false });   

            this.fireEvents();
        },
        fireEvents: function(){

            GYM._media(); // init media
            $(document).on('change', '.js-media-input-target-id', function(){
                var img = $(this).attr('data-img');
                $('.image-preview').css('background-image', 'url(' + img + ')').addClass('uploaded');
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

            this.post_form.submit(function(e){
                e.preventDefault();
                var url = $(this).data("ajax"),
                    formData = new FormData(self.post_form[0]);

                var inputs = $(this).find('input:required, select:required');

                $.each(inputs, function(i, input){
                    if($(input).val()){
                        $(input).removeClass("invalid");
                    }else{
                        if($(input).attr("type") != "checkbox") $(input).addClass("invalid");
                    }
                });

                // Article image
                if(self.article_image != null){
                    formData.append('image', self.article_image);
                }

                if($(this).find(".invalid").length <= 0){
                    GYM._upload(url, formData).done(function(res){
                        if(!res.error){
                            N.show('success', 'Příspěvek byl úspěšně uložen!');
                        }else{
                            N.show('error', 'Nepodařilo se uložit příspěvek, zkontrolujte údaje nebo to zkuste znovu!');
                        }
                        NProgress.done();
                    });
                }

            });
        }
    }
}());

BLOG.init();