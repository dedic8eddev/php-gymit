'use strict';

var FOOTER = FOOTER || (function () {
    var self;
    return {
        btn_footer_submit: $('#footerSubmit'),
        remote_modal: $('#modal'),
        btn_modal_submit: $('#modalSubmit'),          
        link_popover_element: '.footer-link',
        btn_remove_footer_link: '.rm-footer-link',
        btn_add_footer_link: $('#add-footer-link'),
        general_info_form: '#generalInfoForm',
        opening_hours_form: '#openingHoursForm',
        sortable_cols: [],
        sortable_options: {
            group: 'shared',
            animation: 150
        },
        link_popover_options: {
            title: '<span>Nastavení odkazu</span><a href="javascript:;" class="close" onclick="$(\'.footer-link\').popover(\'hide\');">&times;</a>',
            placement: 'bottom',
            html: true,
            content: function() { return $('#footer-link-popover').html(); }
        },        
        role: null,
        init: async function(params){
            self = this;
            this.role = await GYM._role();
            NProgress.configure({ parent: '#app', minimum: 0.1, showSpinner: false });   
            N.settings.killer = false; // We do not want to kill notification after show another

            this.initPopover($(this.link_popover_element));

            this.fireEvents();
        },
        fireEvents: function(){
            $('#setSortable').change(function(){
                var set = $(this).is(':checked');
                $('.switch-note, .rm-footer-link').toggle();
                self.setSortable(set);
                self.setEditable(set);
                if(set){
                    $(self.link_popover_element).popover('disable');
                } else {
                    $(self.link_popover_element).popover('enable');
                }
            });

            $('body').on('click', '[data-toggle="modal"]', function(e){
                if($('#setSortable').is(':checked')) e.stopPropagation();
                $(self.link_popover_element).popover('hide');
                $($(this).data("target")+' .modal-body').load($(this).data("remote"), function(){
                    $('.select2').select2();
                    // flatpickr
                    $(".js-time-input").flatpickr({
                        enableTime: true,
                        noCalendar: true,
                        dateFormat: flatpickrTimeFormat,
                        time_24hr: flatpickr24hr
                    });                      
                });
            });            

            self.remote_modal.on('show.bs.modal', function(e){
                $(e.target).find('.modal-title').text($(e.relatedTarget).data('modal-title'));
                self.btn_modal_submit.text($(e.relatedTarget).data('modal-submit'));
            });

            self.btn_modal_submit.click(function(){
                $(this).closest('.modal-content').find('form').submit();
            });                  

            $('#footerPage').on('click', self.link_popover_element, function (e) { // Close all other popovers while open
                $(self.link_popover_element).not(this).popover('hide');
            });

            self.btn_add_footer_link.click(function(){
                var lastFooterLink = $(self.link_popover_element).last();
                var clone = lastFooterLink.clone();
                $(clone).data('link','').find('span').text('Nový link');
                self.initPopover($(clone));
                lastFooterLink.after(clone);    
                if($(self.btn_remove_footer_link).length===2) $(self.btn_remove_footer_link).show();         
            });

            $('#footerPage').on('click', self.btn_remove_footer_link, function(e){
                $(self.link_popover_element).popover('hide');
                $(this).closest('.footer-link').fadeOut(300, function() { 
                    $(this).remove(); 
                    if($(self.btn_remove_footer_link).length==1) $(self.btn_remove_footer_link).hide();
                });
                
                console.log($(self.btn_remove_footer_link).length==1);                
                e.stopPropagation(); // do not show popover
            });

            $(document).on('click','.btn-save-footer-link',function(e){ // save data from popover
                var popover = $(this).closest('.popover');
                var popoverInitiator = $("[aria-describedby='"+popover.attr('id')+"']"),
                    linkName = popover.find('.link-name').val(),
                    linkHref = popover.find('.link-href').val();                    
                popoverInitiator.find('span').text(linkName);
                popoverInitiator.data('link',linkHref);
                popoverInitiator.popover('hide'); 
            });

            $('#footerPage').on('submit', self.general_info_form, function(e){
                e.preventDefault();
                self.submitForm({
                    url: $(this).data('ajax'),
                    form:$(this)[0],
                    succes_text: 'Základní informace byly úspěšně uloženy!',
                    error_text: 'Nepodařilo se uložit základní informace, zkontrolujte údaje nebo to zkuste znovu!',
                    success_function: function(formData){
                        $('#street').text(formData.get('street'));
                        $('#city').text(formData.get('city'));
                        $('#email').text(formData.get('email'));
                        $('#phone').text(formData.get('phone'));
                    }
                });
            });              

            $('#footerPage').on('submit', self.opening_hours_form, function(e){
                e.preventDefault();
                self.submitForm({
                    url: $(this).data('ajax'),
                    form:$(this)[0],
                    succes_text: 'Otevírací hodiny byly úspěšně uloženy!',
                    error_text: 'Nepodařilo se uložit otevírací hodiny, zkontrolujte údaje nebo to zkuste znovu!',
                    success_function: function(formData){
                        $('#monday-from').text(formData.get('monday[from]'));
                        $('#monday-to').text(formData.get('monday[to]'));
                        $('#saturday-from').text(formData.get('saturday[from]'));
                        $('#saturday-to').text(formData.get('saturday[to]'));
                        self.remote_modal.modal('hide');                        
                    }
                });
            }); 

            self.btn_footer_submit.click(function(){
                var footer=[];
                $('#footer-cols .col').each(function(i){
                    var col={}, links=[];
                    $(this).find('.list-group-item').each(function(){
                        var type = $(this).data('type');
                        switch(type) {
                            case 'links':
                                links.push({link:$(this).data('link'),text:$(this).find('span').text()});
                                col[type] = links;
                                break;
                            case 'text':
                                col[type] = $(this).find('textarea').length ? $(this).find('textarea').val() : $(this).find('p').text();
                                break;
                            case 'address':
                                let address = $(this).find('#street').text()+', '+$(this).find('#city').text(),
                                    opening_hours = $(this).find('small').text().replace(/\n/g,'').replace(/\s{2,}/g,' ');                                
                                col['address'] = `${address}<br />${opening_hours}`;
                                break;
                            default:
                                col[type] = '';
                        }
                    });
                    footer.push(col);
                });

                var formData = new FormData();
                formData.append('id',$('#footerId').val());
                formData.append('data',JSON.stringify(footer));
                GYM._upload('save_footer_ajax', formData).done(function(res){
                    if(!res.error) N.show('success', 'Patička byla úspěšně uložena!');
                    else N.show('error', 'Nepodařilo se uložit patičku, zkuste to znovu!');

                    NProgress.done();
                });
            });

        },
        setSortable: function(set){
            if(set){ // enable sort
                $('#footer-cols .list-group').each(function(i){
                    self.sortable_cols[i] = new Sortable(this, self.sortable_options);   
                }); 
            } else { // disable sort
                $.each(self.sortable_cols, function(){
                    this.destroy();
                });
            }
        },
        setEditable: function(set){
            if(set){ // disable edit
                var footerTextbox = $('<p class="footer-textbox">'+$('.footer-textbox').val()+'</p>');
                $('.footer-textbox').replaceWith(footerTextbox);                 
            } else { // enable edit
                var footerTextbox = $('<textarea rows="8" class="footer-textbox form-control p-0 s-14">'+$('.footer-textbox').text()+'</textarea>');
                $('.footer-textbox').replaceWith(footerTextbox);  
            }            
        },
        initPopover: function(el){
            el.popover(FOOTER.link_popover_options)
            .on("insert.bs.popover", function(e){
                console.log(e);
            })
            .on("inserted.bs.popover", function(e){
                $('.link-name').val($(e.currentTarget).find('span').text());
                $('.link-href').val($(e.currentTarget).data('link'));
                //this.link_popover_element = $(e.currentTarget);
            });
        },
        submitForm: function(data){
            var url = $(data.form).data("ajax"),
                formData = new FormData(data.form);

            var inputs = $(data.form).find('input:required, select:required');

            $.each(inputs, function(i, input){
                if($(input).val()){
                    $(input).removeClass("invalid");
                }else{
                    if($(input).attr("type") != "checkbox") $(input).addClass("invalid");
                }
            });

            if($(data.form).find(".invalid").length <= 0){
                GYM._upload(data.url, formData).done(function(res){
                    if(!res.error){
                        N.show('success', data.succes_text);
                        if(typeof data.success_function !== 'undefined') data.success_function(formData);
                    }
                    else N.show('error', data.error_text);
                    
                    NProgress.done();
                });
            }            
        }
    }
}());

FOOTER.init();