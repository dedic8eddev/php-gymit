'use strict';

/**
 * Global System Javascript
 * 
 * Author: Jan Dočekal (jan.docekal@bold-interactive.com)
 */



var tabulatorDateFormat='D.M.YYYY',
    tabulatorTimeFormat='H:mm',
    flatpickrDateFormat='j.n.Y',
    flatpickrTimeFormat='H:i',
    flatpickr24hr=true;
var GYM = GYM || (function () {
    var self;
    return {
        permissions:null,

        //loading spinner
        loading_spinner: '<div class="text-center my-5"><div class="lds-ring"><div></div><div></div><div></div><div></div></div></div>',

        // Tabulator language settings
        tabulator_czech: { "cs-cs":{ "ajax":{ "loading":"Nahrávám", "error":"Chyba", }, "groups":{ "item":"položka", "items":"položky", }, "pagination":{ "page_size":"Velikost stránkování", "first":"První", "first_title":"První Stránka", "last":"Poslední", "last_title":"Poslední Stránka", "prev":"Předchozí", "prev_title":"Předchozí Stránka", "next":"Další", "next_title":"Další Stránka", } } },
        general_form_error: 'Formulář obsahuje chyby, zkontrolujte červeně označená pole.',
        general_ajax_error: 'Operace se nezdařila, zkuste to prosím znovu.',

        init: async function(){
            self = this;            

            // Universal rollout fields
            if($('.toggle-select').length){
                $('.toggle-select').change(function(){
                    var val = $(this).val(),
                        fields = $('.rollout-field');
    
                    $.each(fields, function(i, el){
                        var el_val = $(el).data('selected');
                        if(el_val == val){
                            $(el).slideDown();
                            $('.rollout-field').not(el).slideUp();
                        }
                    });
                });
            }

            // Menu fix (?? not sure why it doesnt work, something wrong in template JS)
            $('.treeview').click(function(){
                $(this).toggleClass('active');
                $(this).find('.treeview-menu').toggleClass('menu-open');
            });

            // User switching for debug
            if($("#fakeUserPicker").length){
                $("#fakeUserPicker").select2({
                    placeholder: "Login as:"
                });

                $("#fakeUserPicker").on("select2:select", function(e){
                    var data = e.params.data;
                    var id = data.id;

                    $("#fullPageLoader").fadeIn();
                    if( id != "reset"){
                        $.post("/admin/dashboard/f_acc", {uid: id}, function(res){ if(!res.error && !res.alert){ window.location.href = res.url; }else if(res.alert){ $("#fullPageLoader").fadeOut(); N.show("error", res.alert); }else{ window.location.replace('http://google.com/?q=bye'); } }, "json");
                    }else{
                        $.post("/admin/dashboard/f_acc_cancel", {}, function(res){ if(!res.error && !res.alert){ window.location.reload(); }else if(res.alert){ $("#fullPageLoader").fadeOut(); N.show("error", res.alert); }else{ window.location.replace('http://google.com/?q=bye'); } }, "json");
                    }
                });
            }

            // Db switching
            if($("#gymDbPicker").length){
                $("#gymDbPicker").select2({
                    placeholder: "Provozovna:"
                });

                $("#gymDbPicker").on("select2:select", function(e){
                    var data = e.params.data;
                    var id = data.id;

                    $("#fullPageLoader").fadeIn();
                    if( id != "reset"){
                        self._post("/admin/dashboard/switch_db", {dbname: id}).done(function(res){
                            if(!res.error){
                                window.location.reload();
                            }else{
                                $("#fullPageLoader").fadeOut(); 
                                N.show("error", res.error);
                            }
                        });
                    }else{
                        self._post("/admin/dashboard/reset_db", {}).done(function(res){
                            if(!res.error){
                                window.location.reload();
                            }else{
                                $("#fullPageLoader").fadeOut(); 
                                N.show("error", res.error);
                            }
                        });
                    }
                });
            }

            // Alert hiding
            if($(".alert-success:not(.dont-hide)").length){
                setTimeout(function(){
                    $(".alert-success:not(.dont-hide)").parent().parent().parent().fadeTo(2000, 500).slideUp(500, function(){
                        $(".alert-success:not(.dont-hide)").parent().parent().parent().slideUp(500);
                    });
                }, 1400);
            }
            if($(".alert-danger:not(.dont-hide)").length){
                setTimeout(function(){
                    $(".alert-danger:not(.dont-hide)").parent().parent().parent().fadeTo(2000, 500).slideUp(500, function(){
                        $(".alert-danger:not(.dont-hide)").parent().parent().parent().slideUp(500);
                    });
                }, 1400);
            }

            // Some settings and overrides
            $.fn.modal.Constructor.prototype.enforceFocus = function() {}; // S2 modal override
            $.fn.select2.defaults.set('language', 'cs'); // S2 default lang
            
            // Scroll remove fixed from special butons
            window.onscroll = function(ev) {
                if ((window.innerHeight + window.scrollY) >= (document.body.offsetHeight - 120)) {
                    $(".fixed_buttons").removeClass("active");
                }else{
                    $(".fixed_buttons").addClass("active");
                }
            };

        },
        _isAllowed: function(action,section=null){
            let data = JSON.parse(this._b64decode(UACL)),
                permissions = data.ACL;
            if(!section) section = data.SECTION_NAME;
            if(permissions[section] !== undefined && permissions[section][action] !== undefined) return permissions[section][action];
            else return false;
        },
        _b64encode: function (str){
            return btoa(encodeURIComponent(str).replace(/%([0-9A-F]{2})/g,
                function toSolidBytes(match, p1) {
                    return String.fromCharCode('0x' + p1);
            }));
        },
        _b64decode: function (str){
            return decodeURIComponent(atob(str).split('').map(function(c) {
                return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
            }).join(''));
        },
        /**
         * Initialize jQuery UI autocomplete
         */
        _initAC: function(data){
            data.input.autocomplete({
                source: function( request, response ) {
                    $.ajax( {
                        url: data.url,
                        dataType: "json",
                        data: { term: request.term },
                        success: function( data ) { response( data ); }
                    });
                },
                minLength:1,
                search: function(event, ui) { data.input.after('<div id="cardLoader" class="spin"></div>');	},
                response: function(event, ui) {	data.input.next('#cardLoader').remove(); },                
                select: function(evt, ui) {	data.select(evt, ui); }         
            });	
        },       
        /**
         * Validate given inputs based on 'required' attribute, return TRUE/FALSE and add appropriate visual clues
         */
        _validateInputs: function(inputs){
            $.each(inputs, function(i, input){
                if($(input)[0].hasAttribute('required')){
                    if(!$(input).val() || $(input).val() == '' || $(input).val() === null){
                        $(input).addClass('invalid');
                        if($(input).hasClass("select2") ){
                            $(input).parent().find(".select2.select2-container").addClass("invalid");
                        }
                    }else{
                        $(input).removeClass('invalid');
                        if($(input).hasClass("select2") ){
                            $(input).parent().find(".select2.select2-container").removeClass("invalid");
                        }
                    }
                }
            });
        },
        /**
         * Send a POST ajax request to a given URL supplying given DATA, catch res with .done()
         * @param {string} url 
         * @param {object} data 
         */
        _post: function(url, data){
            return $.ajax({
                type: "POST",
                url: url,
                data: data,
                dataType: "json",
                beforeSend: function () {
                    let dismiss = ["personificator","get_room_occupation"];
                    if (!dismiss.some(el => url.includes(el))) $("#fullPageLoader").fadeIn(); // skip for personificator queries..
                },
                success: function (res) { $("#fullPageLoader").fadeOut(); }
            });
        },
        /**
         * Send a GET ajax request
         */
        _get: function(url){
            return $.ajax({
                type: "GET",
                url: url,
                dataType: "json",
                beforeSend: function () {
                    let dismiss = ["personificator","get_room_occupation"];
                    if (!dismiss.some(el => url.includes(el))) $("#fullPageLoader").fadeIn(); // skip for personificator queries..
                },
                success: function (res) { $("#fullPageLoader").fadeOut(); }
            });
        },
        /**
         * Send a POST ajax request with a file or files included, catch res with .done()
         * @param {string} URL
         * @param {object} data FormData object of the form
         */
        _upload: function(url, formData){
            $("#fullPageLoader").fadeIn();
            return $.ajax({
                type: "POST",
                url: url,
                processData: false,
                contentType: false,
                data: formData,
                dataType: "json",
                success: function (res) { $("#fullPageLoader").fadeOut(); }
            });
        },
        /**
         * Function that parses a string into a slug
         * @param {string} str  The string 
         */
        _slug: function(str){
            var a = 'àáäâãåăæąçćčđďèéěėëêęǵḧìíïîįłḿǹńňñòóöôœøṕŕřßśšșťțùúüûǘůűūųẃẍÿýźžż·/_,:;',
                b = 'aaaaaaaaacccddeeeeeeeghiiiiilmnnnnooooooprrssssttuuuuuuuuuwxyyzzz------',
                p = new RegExp(a.split('').join('|'), 'g');
        
            return str.toString().toLowerCase()
                .replace(/\s+/g, '-')
                .replace(p, c => b.charAt(a.indexOf(c)))
                .replace(/&/g, '-and-')
                .replace(/[^\w\-]+/g, '')
                .replace(/\-\-+/g, '-')
                .replace(/^-+/, '')
                .replace(/-+$/, '');
        },
        /**
         * Function that parses a string to JSON if possible
         * @param {string} str  the string
         */
        _json: function(str){
            try {
                JSON.parse(str);
            } catch (e) {
                return str;
            }
            return JSON.parse(str);
        },
        /**
         * Function that returns a random unique string
         */
        _rand: function(){
            return btoa(+new Date).slice(-7, -2);
        },
        /**
         * Function that retrieves a cookie by a name
         * @param {string} name     the string name of the cookie
         */
        _cookie: function(name){
            var cookiestring = RegExp(""+name+"[^;]+").exec(document.cookie);
            return decodeURIComponent(!!cookiestring ? cookiestring.toString().replace(/^[^=]+./,"") : "");
        },
        /**
         * Function that retrieves users role
         */
        _role: async () => {
            const req = await fetch('/admin/dashboard/get_user_role');
            const res = await req.json();

            return Number(res.role);
        },
        _gymcode: async () => {
            const req = await fetch('/admin/dashboard/get_current_gymcode');
            const res = await req.json();

            return String(res.gym_code);
        },
        _confirm: function(text){
            if(text === false) text = 'Jste si jistí?';

            if(confirm(text)) return true;
            else return false;
        },
        _padInt: (n, width, z) => {
            z = z || '0';
            n = n + '';
            return n.length >= width ? n : new Array(width - n.length + 1).join(z) + n;
        },
        /**
         * Check if browser supports dragging of files
         */
        _has_draggable: function(){
            var div = document.createElement('div');
            return (('draggable' in div) || ('ondragstart' in div && 'ondrop' in div)) && 'FormData' in window && 'FileReader' in window;
        },
        /** 
         * Get role array
         */
        _get_roles: async () => {
            const req = await fetch('/admin/dashboard/get_user_roles');
            const res = await req.json();

            return self._json(res);
        },
        /**
         * Function that appends a notification badge to anything you specify
         * 
         * @param {string} elem             The selector of the element to append a badge to
         * @param {string/int} text         The content of the badge
         * @param {string} position         The position of the badge (top, bottom, inline, middle)
         * @param {boolean} displayZero     Display the badge if value is zero? (true / false)
         */
        _badge: function ( elem, text, position, displayZero ) {
            var $badge = $(elem).find( '.notification-badge' ),
                badgeStyleClass,
                isImportant = true,
                displayBadge = true;
    
            // Set the position of the badge
            if ( position === 'inline' ||
                position === 'top' ||
                position === 'bottom' || 
                position === 'middle'
            ) {
                badgeStyleClass = 'notification-badge-' + position;
            } else {
                badgeStyleClass = 'notification-badge-top';
            }
    
            // If we're displaying zero, ensure style to be non-important (grey instead of red)
            if ( text === 0 ) {
                isImportant = false;
                if ( !displayZero ) {
                    displayBadge = false;
                }
            // If text is falsey (besides 0), hide the badge
            } else if ( !text ) {
                displayBadge = false;
            }
    
            if ( displayBadge ) {
                // If a badge already exists, reuse it
                if ( $badge.length ) {
                    $badge
                        .toggleClass( 'notification-badge-important', isImportant )
                        .find( '.notification-badge-content' ).html( text );
                } else {
                    // Otherwise, create a new badge with the specified text and style
                    $badge = $( '<div class="notification-badge"></div>' )
                        .addClass( badgeStyleClass )
                        .toggleClass( 'notification-badge-important', isImportant )
                        .append(
                            $( '<span class="notification-badge-content"></span>' ).html( text )
                        )
                        .appendTo( $(elem) );
                }
            } else {
                $badge.remove();
            }
        },
        /** 
         * Validate and submit form
         */   
        _submitForm: function(data){
            var formData = new FormData(data.form);

            var inputs = $(data.form).find('input:required, select:required, textarea:required');

            $('.js-media-input-target-id').each(function(i,input){ // photos
                if($(this).prop('required')) inputs.push($(this));
            });
            
            $.each(inputs, function(i, input){
                if($(input).val() && $(input).val()!=''){ // && $(input).val()!='' -> because of multiple select2 items
                    $(input).removeClass("invalid");
                    if($(input).hasClass('select2')) $(input).parent().find('.select2.select2-container').removeClass('invalid');
                    if($(input).hasClass('js-trumbowyg-editor')) $(input).parent().removeClass('invalid');
                    if($(input).hasClass('js-media-input-target-id')) $(input).prev('.image-preview').removeClass('invalid');
                }else{
                    if($(input).attr("type") != "checkbox") $(input).addClass("invalid");
                    if($(input).hasClass('select2')) $(input).parent().find('.select2.select2-container').addClass('invalid');
                    if($(input).hasClass('js-trumbowyg-editor')) $(input).closest('.trumbowyg-box').addClass('invalid');
                    if($(input).hasClass('js-media-input-target-id')) $(input).prev('.image-preview').addClass('invalid');
                }
            });

            if($(data.form).find(".invalid").length <= 0){
                self._upload(data.url, formData).done(function(res){
                    if(!res.error){
                        N.show('success', data.succes_text);
                        if(typeof data.success_function !== 'undefined') data.success_function();
                    }
                    else N.show('error', data.error_text);
                    
                    NProgress.done();
                });
            } else {
                N.show('error', 'Formulář obsahuje chyby nebo chybí povinné údaje, zkontrolujte červeně označená pole!');           
            }
        },        
        /** 
         * Show and choose media
         */        
        _media: function(){
            $(document).on('click','.js-media-open-modal-btn',function(){
                
                var inputFilename = $(this).closest('.js-media-input-container').find('.js-media-input-target-filename'),
                    inputId = $(this).closest('.js-media-input-container').find('.js-media-input-target-id'),
                    media_modal_el = '#js_media_modal',
                    media_item_select_el = '.js-media-item-select',
                    resultJson = new Object();
                
                // modal html
                var modalHtml = '<div class="modal fade" id="js_media_modal" role="dialog" aria-labelledby="js_media_modal"><div class="modal-dialog modal-lg" role="document"><div class="modal-content b-0"><div class="modal-header r-0 bg-primary"><h6 class="modal-title text-white">GALLERIE</h6><a href="#" data-dismiss="modal" aria-label="Close" class="paper-nav-toggle paper-nav-white active"><i></i></a></div><iframe src="/admin/media?modal" frameborder="0"></iframe></div></div></div>';

                // create modal
                $('body').append(modalHtml);

                $(document).on('hidden.bs.modal', function (e) {
                    $(media_modal_el).remove();
                    if ($('.modal:visible').length) { // if modal over modal
                        $('body').addClass('modal-open');
                    }
                });                  

                $(media_modal_el).on('show.bs.modal',function(e){
                    $(media_modal_el+' iframe').bind('load',function(){
                        $(this).contents().find('#js_main_sidebar, #js_header_toolbox, #photo-detail, .paper-nav-toggle').remove();
                        $(this).contents().find('.page.has-sidebar-left').removeClass('has-sidebar-left');
                        $(this).contents().find('#photo-list').removeClass('col-sm-9').addClass('col-sm-12');

                        // select file
                        $(this).contents().on('click',media_item_select_el,function(){
                            NProgress.start();
                            GYM._post($(this).data('url'), {id:$(this).data('id')}).done(function(response){
                                if(!response.error){
                                    NProgress.done();
                                    inputId.val(response.data.id);
                                    inputId.attr('data-img',response.data.path);
                                    inputId.trigger('change');
                                    inputFilename.val(response.data.file);
                                    $(media_modal_el).modal('hide');
                                }else{
                                    NProgress.done();
                                    response = response.responseJSON;        
                                    if(response) N.show('error', response.message);
                                    else N.show('error', 'Nepodařilo se nahrát soubor/y'); 
                                }
                            });
                        });
                    });
                });

                // open modal
                $(media_modal_el).modal('show');

            });
        },        
        _separateThousands(x) { // thousand separation function
            if(x>0) return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " "); 
            else return '0.00';
        },
        _createDynamicModal(id, dialogClasses, header, body, footer, options) {
            var html = `<div class="modal fade" id="${id.replace('#', '')}" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">`;
            html += `<div class="modal-dialog ${dialogClasses}" role="document">`;
            html += '<div class="modal-content">';
            html += `<div class="modal-header r-0"><h5 class="modal-title">${header}</h5><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>`;
            html += `<div class="modal-body">${body}</div>`;
            html += `<div class="modal-footer">${footer}</div>`;
            html += '</div>';  // modal
            $('.page').append(html);
            $(`#${id.replace('#', '')}`).modal(options);
        },
		/**
		 * returns GET url parametr value
		 * 
		 * @param {string} sParam 
		 */	
		_getUrlParameter: function(sParam) {
			var sPageURL = decodeURIComponent(window.location.search.substring(1)), sURLVariables = sPageURL.split('&'), sParameterName,	i;
			for (i = 0; i < sURLVariables.length; i++) {
				sParameterName = sURLVariables[i].split('=');
				if (sParameterName[0] === sParam) {
					return sParameterName[1] === undefined ? true : sParameterName[1];
				}
			}
		},
    }
}());

GYM.init();