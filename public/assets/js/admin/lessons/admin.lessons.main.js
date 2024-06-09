'use strict';

var LESSONS = LESSONS || (function () {
    var self;
    return {
        role: false,
        calendar_el: $('#lesson_cal'),
        calendar: null,

        add_event: $('#addeventsubmit'),
        add_event_modal: $('#addEventModal'),

        edit_event_modal: $('#editEventModal'),
        delete_event: $('#delete_event'),
        delete_repeating_events: $('#delete_all_repeating_events'),
        edit_event: $('#editeventsubmit'),

        lessons_table: '#lessonTable',
        lessons_table_url: $('#lessonTable').data("ajax"),

        image_dropper: $('#lesson_image_dropper'),
        image_input: $('#lesson_image'),
        del_image: $('lesson_image_dropper .delete-image'),
        lesson_image: null,

        image_dropper_edit: $('#lesson_image_dropper_edit'),
        image_input_edit: $('#lesson_image_edit'),
        del_image_edit: $('#lesson_image_dropper_edit .delete-image'),
        lesson_image_edit: null,

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
                    {title: 'NÁZEV', field: 'name', headerFilter:true, headerFilterPlaceholder:"Hledat podle názvu", formatter:this.returnName},
                    {title: 'Od', field: 'starting_on', headerFilter:true, headerFilterPlaceholder:"Hledat podle dne",editor:this.dateEditor,editable: false,formatter:"datetime",formatterParams:
                        {
                        inputFormat:"YYYY-MM-DD HH:mm",
                        outputFormat:"DD.MM.YYYY HH:mm",
                        invalidPlaceholder:"(nesprávné datum)"
                        }
                    },
                    {title: 'Do', field: 'ending_on', headerFilter:true, headerFilterPlaceholder:"Hledat podle dne",editor:this.dateEditor,editable: false,formatter:"datetime",formatterParams:
                        {
                        inputFormat:"YYYY-MM-DD HH:mm",
                        outputFormat:"DD.MM.YYYY HH:mm",
                        invalidPlaceholder:"(nesprávné datum)"
                        }
                    },
                    {title: '', align: 'right', field: '', formatter: this.rtb}
                ]
            });
            this.lessons_table.setLocale("cs-cs");
            this.lessons_table.setData(this.lessons_table_url);

            this.fireEvents();
        },
        returnName: function(cell, params, onRendered){
            var d = cell.getRow().getData();
            return '<a href="javascript:;" class="open-table-event" data-lesson="'+d.id+'" data-row='+"'"+' '+JSON.stringify(d)+' '+"''"+' >'+d.name+'</i></a>';
        },
        rtb: function(c){
            var d = c.getRow().getData();
            var isrecurring = '';
            if(typeof d.created_by == "undefined"){
                isrecurring = 'data-date="'+moment(d.ending_on).format('YYYY-MM-DD')+'"';
            }

            var edit_button = '<a href="javascript:;" class="open-table-event" data-lesson="'+d.id+'" data-row='+"'"+' '+JSON.stringify(d)+' '+"''"+' ><i class="icon-pencil"></i></a>&nbsp;&nbsp;';
            var delete_button = '<a href="javascript:;" class="cancel-table-event" data-lesson="'+d.id+'" '+isrecurring+'><i class="icon-delete text-red"></i></a>';

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
                    var file = e.originalEvent.dataTransfer.files[0],
                        url = window.URL.createObjectURL(file);

                    self.image_dropper.find('.preview').css('background-image', 'url('+url+')');
                    self.image_dropper.addClass('uploaded');
                    self.lesson_image = file;
                });
                self.image_dropper_edit.on('drag dragstart dragend dragover dragenter dragleave drop', function(e){
                    e.preventDefault();
                    e.stopPropagation();
                })
                .on('dragover dragenter', function(){
                    self.image_dropper_edit.addClass('is-dragover');
                })
                .on('dragleave dragend drop', function(){
                    self.image_dropper_edit.removeClass('is-dragover');
                })
                .on('drop', function(e){
                    var file = e.originalEvent.dataTransfer.files[0],
                        url = window.URL.createObjectURL(file);

                    self.image_dropper_edit.find('.preview').css('background-image', 'url('+url+')');
                    self.image_dropper_edit.addClass('uploaded');
                    self.lesson_image_edit = file;
                });
            }
            
            self.image_input.change(function(){
                var input = $(this)[0],
                    file = input.files[0],
                    url = window.URL.createObjectURL(file);

                    self.image_dropper.find('.preview').css('background-image', 'url('+url+')');
                    self.image_dropper.addClass('uploaded');
                    self.lesson_image = file;
            });
            self.image_input_edit.change(function(){
                var input = $(this)[0],
                    file = input.files[0],
                    url = window.URL.createObjectURL(file);

                    self.image_dropper_edit.find('.preview').css('background-image', 'url('+url+')');
                    self.image_dropper_edit.addClass('uploaded');
                    self.lesson_image_edit = file;
            });

            self.image_dropper.click(function(e){ if(!$(e.target).hasClass('delete-image') && !self.image_dropper.hasClass('uploaded')) self.image_input.click(); });
            self.image_dropper_edit.click(function(e){ if(!$(e.target).hasClass('delete-image') && !self.image_dropper_edit.hasClass('uploaded')) self.image_input_edit.click(); });

            self.del_image.click(function(e){
                e.preventDefault();

                var parent = $(this).parent();
                parent.find('.preview').css('background-image', 'none');
                self.lesson_image = null;
                self.image_dropper.removeClass('uploaded');
            });
            self.del_image_edit.click(function(e){
                e.preventDefault();

                var parent = $(this).parent();
                
                if($(this).data('lesson')){
                    GYM._post('/admin/lessons/remove_image_ajax', {lesson_id: $(this).data("lesson")}).done(function(res){
                        if(!res.error){
                            self.image_dropper_edit.find('.preview').css('background-image', 'none');
                            self.lesson_image_edit = null;
                            self.image_dropper_edit.removeClass('uploaded');
                            self.del_image_edit.removeAttr('lesson');

                            N.show('success', 'Obrázek byl úspěšně odstraněn!');
                        }else{
                            N.show('error', 'Obrázek se nepovedlo odstranit, zkuste to znovu.');
                        }
                    });
                }else{
                    parent.find('.preview').css('background-image', 'none');
                    self.lesson_image_edit = null;
                    self.image_dropper_edit.removeClass('uploaded');
                }
            });

            $('input[type="date"].dateonly').flatpickr({
                altInput: true,
                altFormat: "d.m.Y",
                dateFormat: "Y-m-d",
                enableTime: false,
                onClose: function(selectedDates, dateStr, instance){
                    // fill end date
                    if(/starting/.test($(instance.input).attr('id'))){
                        let end_date = moment(selectedDates[0]).format('YYYY-MM-DD'),
                            end_date_input = $(instance.input).attr('id').replace('starting','ending');

                        $(`#${end_date_input}`)[0]._flatpickr.setDate(end_date, true);  
                    }               
                }
            });

            $('input[type="date"].timeonly').flatpickr({
                altInput: true,
                altFormat: "H:i",
                dateFormat: "H:i:s",
                noCalendar: true,
                enableTime: true,
                time_24hr: true,
                onClose: function(selectedDates, dateStr, instance){
                    // fill end time
                    if(/from/.test($(instance.input).attr('id'))){
                        let template = $(instance.input).closest('form').find('[name="template_id"] option:selected'),
                            duration = moment.duration(template.data('duration')).asMinutes(),
                            end_time = moment(selectedDates[0]).add(duration,'minutes').format('HH:mm:ss'),
                            end_time_input = $(instance.input).attr('id').replace('from','to');

                        $(`#${end_time_input}`)[0]._flatpickr.setDate(end_time, true);
                    }
                }  
            });

            $("#repeating").change(function() {
                if($(this).is(":checked")) {
                    $(".repeat-form").slideDown();
                }else{
                    $(".repeat-form").slideUp();
                }
            });
            
            GYM._initAC({
                input: $('.vip-client-ac'),
                url: '/admin/clients/search_disposable_clients_ajax',
                select: function(evt, ui) {
                    evt.preventDefault();
                    $(evt.target).val(ui.item.first_name);
                    $(evt.target).closest('.input-group').find('.vip-client-surname').val(ui.item.last_name);
                }    
            });

            self.delete_event.click(function(){
                var id = self.edit_event_modal.find('#lesson_id').val();
                var agreed = confirm('Opravdu chcete vymazat tuto lekci? Akci neni možné vrátit.');

                var request_obj = {lesson_id: id};
                if($(this).data("date")){
                    request_obj.lesson_date = $(this).data("date");
                }

                if(agreed){
                    GYM._post('/admin/lessons/delete_event_ajax', request_obj).done(function(res){
                        if(!res.error){
                            N.show('success', 'Lekce byla úspešně smazána.');
                            self.calendar_el.fullCalendar('refetchEvents');
                            self.edit_event_modal.modal("hide");
                        }else{
                            N.show('error', 'Nepovedlo se smazat lekci, zkuste to znovu.');
                        }
                    });
                }
            });

            self.delete_repeating_events.click(function(){
                var id = self.edit_event_modal.find('#lesson_id').val();
                var agreed = confirm('Opravdu chcete vymazat tuto lekci?');
                if(agreed){
                    GYM._post('/admin/lessons/delete_all_repeating_events_ajax', {lesson_id: id}).done(function(res){
                        if(!res.error){
                            N.show('success', 'Lekce byla úspešně smazána.');
                            self.calendar_el.fullCalendar('refetchEvents');
                            self.edit_event_modal.modal("hide");
                        }else{
                            N.show('error', 'Nepovedlo se smazat lekci, zkuste to znovu.');
                        }
                    });
                }
            });

            self.edit_event.click(function(){
                var start = self.edit_event_modal.find("#starting_on_edit"),
                    end = self.edit_event_modal.find("#ending_on_edit"),
                    from = moment($('#time_from_edit')[0]._flatpickr.selectedDates[0]).format('HH:mm'),
                    to = moment($('#time_to_edit')[0]._flatpickr.selectedDates[0]).format('HH:mm'),
                    req_inputs = self.edit_event_modal.find('input:required, select:required, textarea:required');

                $.each(req_inputs, function(i, input){
                    if($(input).is(":visible") && ($(input).val() == "" || !$(input) || $(input).val() == "" || typeof $(input).val() == "object")){
                        $(input).addClass("invalid");
                    }else{
                        $(input).removeClass("invalid");
                    }
                });

                if(self.edit_event_modal.find(".invalid").length <= 0){
                    let start_datetime = moment(`${start.val()} ${from}`),
                        end_datetime = moment(`${end.val()} ${to}`),
                        duration = moment.duration(end_datetime.diff(start_datetime)).asMinutes(),
                        template_duration = moment.duration(self.edit_event_modal.find('[name="template_id"] option:selected').data('duration')).asMinutes();
                        
                    if(start_datetime > end_datetime){
                        N.show('error', 'Datum konce musí být starší než datum začátku.');
                    }else if(duration != template_duration){
                        N.show('error', `Trvání události (${duration} minut) se nerovná trvání lekce (${template_duration} minut)`);
                    }else{
                        var inputs = self.edit_event_modal.find('input, select, textarea');
                        var ajax_data = new FormData();

                        $.each(inputs, function(i, input){
                            if(typeof $(input).attr("name") == "undefined") return;
                            if($(input).attr("type") == "checkbox"){
                                ajax_data.append($(input).attr("name"), $(input).is(":checked"));
                            }else{
                                ajax_data.append($(input).attr("name"), $(input).val());
                            }
                        });

                        // VIP clients
                        $.each(self.edit_event_modal.find('.vip-clients tr'), function(i,tr){
                            ajax_data.append(`vip_clients[${i}][first_name]`, $(tr).find('td:eq(0)').text());
                            ajax_data.append(`vip_clients[${i}][last_name]`, $(tr).find('td:eq(1)').text());
                            ajax_data.append(`vip_clients[${i}][note]`, $(tr).find('td:eq(2)').text());
                        });                        

                        ajax_data.append('lesson_id', self.edit_event_modal.find('#lesson_id').val());

                        if(self.lesson_image_edit){
                            ajax_data.append('lesson_image', self.lesson_image_edit);
                        }
                        ajax_data.append('client_limit', self.edit_event_modal.find('[name="template_id"] option:selected').data('client-limit'));

                        ajax_data.set('time_from', from);
                        ajax_data.set('time_to', to);

                        GYM._upload('/admin/lessons/save_event_ajax', ajax_data).done(function(res){
                            if(!res.error){
                                N.show('success', 'Lekce byla úspešně uložena.');
                                self.showClientsReservationInfo(res.data.clients);
                                self.calendar_el.fullCalendar('refetchEvents');
                                self.edit_event_modal.modal("hide");
                            }else{
                                N.show('error', 'Nepovedlo se uložit lekci, zkuste to znovu.');
                            }
                        });
                    }
                }else{
                    N.show('error', GYM.general_form_error);
                }
            });

            self.add_event.click(function(){

                var start = self.add_event_modal.find("#starting_on"),
                    end = self.add_event_modal.find("#ending_on"),
                    from = moment($('#time_from')[0]._flatpickr.selectedDates[0]).format('HH:mm'),
                    to = moment($('#time_to')[0]._flatpickr.selectedDates[0]).format('HH:mm'),
                    req_inputs = self.add_event_modal.find('input:required, select:required, textarea:required');

                $.each(req_inputs, function(i, input){
                    if($(input).is(":visible") && ($(input).val() == "" || !$(input) || $(input).val() == "" || typeof $(input).val() == "object")){
                        $(input).addClass("invalid");
                    }else{
                        $(input).removeClass("invalid");
                    }
                });

                if(self.add_event_modal.find(".invalid").length <= 0){
                    let start_datetime = moment(`${start.val()} ${from}`),
                        end_datetime = moment(`${end.val()} ${to}`),
                        duration = moment.duration(end_datetime.diff(start_datetime)).asMinutes(),
                        template_duration = moment.duration(self.add_event_modal.find('[name="template_id"] option:selected').data('duration')).asMinutes();
                        
                    if(start_datetime > end_datetime){
                        N.show('error', 'Datum konce musí být starší než datum začátku.');
                    }else if(duration != template_duration){
                        N.show('error', `Trvání události (${duration} minut) se nerovná trvání lekce (${template_duration} minut)`);
                    }else{
                        var inputs = self.add_event_modal.find('input, select, textarea');
                        var ajax_data = new FormData();

                        $.each(inputs, function(i, input){
                            if(typeof $(input).attr("name") == "undefined") return;
                            if($(input).attr("type") == "checkbox"){
                                ajax_data.append($(input).attr("name"), $(input).is(":checked"));
                            }else{
                                ajax_data.append($(input).attr("name"), $(input).val());
                            }
                        });

                        if(self.lesson_image){
                            ajax_data.append('lesson_image', self.lesson_image);
                        }
                        ajax_data.append('client_limit', self.add_event_modal.find('[name="template_id"] option:selected').data('client-limit'));

                        ajax_data.set('time_from', from);
                        ajax_data.set('time_to', to);

                        GYM._upload('/admin/lessons/add_event_ajax', ajax_data).done(function(res){
                            if(!res.error){
                                N.show('success', 'Lekce byla úspešně přidána.');
                                self.showClientsReservationInfo(res.data.clients,true);
                                self.calendar_el.fullCalendar('refetchEvents');
                                self.add_event_modal.modal("hide");
                            }else{
                                N.show('error', 'Nepovedlo se uložit lekci, zkuste to znovu.');
                            }
                        });
                    }
                }else{
                    N.show('error', GYM.general_form_error);
                }
            });

            $('body').on('click', '.cancel-table-event', function(){
                var id = $(this).data("lesson");

                var request_obj = {lesson_id: id};
                if($(this).data("date")){
                    request_obj.lesson_date = $(this).data("date");
                }

                var agreed = confirm('Opravdu chcete vymazat tuto lekci? Akci neni možné vrátit.');
                if(agreed){
                    GYM._post('/admin/lessons/delete_event_ajax', request_obj).done(function(res){
                        if(!res.error){
                            N.show('success', 'Lekce byla úspešně smazána.');
                            self.calendar_el.fullCalendar('refetchEvents');
                            self.lessons_table.setData(self.lessons_table_url);
                            self.lessons_table.redraw(true);
                        }else{
                            N.show('error', 'Nepovedlo se smazat lekci, zkuste to znovu.');
                        }
                    });
                }
            });

            $('body').on('click', '.open-table-event', function(){
                var id = $(this).data("lesson");
                var info = GYM._json($(this).data("row"));

                var s_date = moment(info.starting_on).format('YYYY-MM-DD HH:mm'),
                        e_date = moment(info.ending_on).format('YYYY-MM-DD HH:mm'),
                        from_time = moment(info.starting_on).format("HH:mm:ss"),
                        to_time = moment(info.ending_on).format("HH:mm:ss");

                        $('#time_from_edit')[0]._flatpickr.setDate(from_time, true);
                        $('#time_to_edit')[0]._flatpickr.setDate(to_time, true);

                        $('#starting_on_edit')[0]._flatpickr.setDate(s_date, true);
                        $('#ending_on_edit')[0]._flatpickr.setDate(e_date, true);

                    GYM._post('/admin/lessons/get_event_data_ajax', {lesson_id: id, date: e_date}).done(function(res){
                        if(!res.error){
                            var edit_inputs = self.edit_event_modal.find("input, select, textarea");
                            $.each(edit_inputs, function(i, input){
                                var n = $(input).attr("name");
                                if(n == "undefined" || typeof n == "undefined") return true;

                                // Distinguishing repeating events by using the frontend dates
                                if(n == 'starting_on' || n == "ending_on"){
                                    return true;
                                }else if(n == "all_day"){
                                    if(res.data[n] == '1'){
                                        $(input).prop( "checked", true );
                                    }else{
                                        $(input).prop( "checked", false );
                                    }
                                }else{
                                    $(input).val( res.data[n] );
                                }
                            });

                            self.edit_event_modal.find('#lesson_id').val(res.data.id);

                            if(typeof res.data.recurring != "undefined"){
                                self.delete_event.data('date', moment(e_date).format("YYYY-MM-DD"));
                            }

                            if(res.data.repeating == 'TODO'){
                                self.edit_event_modal.find('#delete_all_repeating_events').show();
                            }

                            // client select
                            if(res.data.clients.length > 0){
                                self.edit_event_modal.find('#clients_edit').val(res.data.clients);
                                self.edit_event_modal.find('#clients_edit').select2().trigger("change");
                            }                    

                            // teacher select
                            if(res.data.teachers.length > 0){
                                self.edit_event_modal.find('#teachers_edit').val(res.data.teachers);
                                self.edit_event_modal.find('#teachers_edit').select2().trigger("change");
                            }

                            if(res.data.photo_url.length > 0){
                                self.image_dropper_edit.find('.preview').css('background-image', 'url('+encodeURI(res.data.photo_url)+')');
                                self.image_dropper_edit.addClass('uploaded');
                                self.del_image_edit.data('lesson', res.data.id);
                            }

                            self.edit_event_modal.modal("show");
                        }else{
                            N.show('error', 'Nepovedlo se získat data o lekci, zkuste to znovu.');
                        }
                    });
            });

            self.calendar = self.calendar_el.fullCalendar({
                locale: 'cs',
                header: {
                    left: 'title',
                    right: 'month,basicWeek,basicDay',
                    center: 'today prev,next'
                },
                eventColor: '#3f51b5',
                dayClick: function(date, jsEvent, view) {
                    $('#starting_on')[0]._flatpickr.setDate(date.format(), true);
                    $('#time_from')[0]._flatpickr.setDate('12:00:00', true);

                    self.add_event_modal.modal();
                },
                eventClick: function(info){
                    var id = info.id;
                    
                    var s_date = info.start.format('YYYY-MM-DD HH:mm'),
                        e_date = (info.end) ? info.end.format('YYYY-MM-DD HH:mm') : false,
                        from_time = info.start.format("HH:mm:ss"),
                        to_time = info.end.format("HH:mm:ss");

                        $('#time_from_edit')[0]._flatpickr.setDate(from_time, true);
                        $('#time_to_edit')[0]._flatpickr.setDate(to_time, true);

                        $('#starting_on_edit')[0]._flatpickr.setDate(s_date, true);
                        $('#ending_on_edit')[0]._flatpickr.setDate(e_date, true);

                    GYM._post('/admin/lessons/get_event_data_ajax', {lesson_id: id, date: e_date}).done(function(res){    
                        if(!res.error){
                            var edit_inputs = self.edit_event_modal.find("input, select, textarea");
                            $.each(edit_inputs, function(i, input){
                                var n = $(input).attr("name");
                                if(n == "undefined" || typeof n == "undefined") return true;

                                // Distinguishing repeating events by using the frontend dates
                                if(n == 'starting_on' || n == "ending_on"){
                                    return true;
                                }else if(n == "all_day"){
                                    if(res.data[n] == '1'){
                                        $(input).prop( "checked", true );
                                    }else{
                                        $(input).prop( "checked", false );
                                    }
                                }else{
                                    $(input).val( res.data[n] ).trigger('change');
                                }
                            });

                            self.edit_event_modal.find('#lesson_id').val(res.data.id);

                            if(typeof res.data.recurring != "undefined"){
                                self.delete_event.data('date', moment(e_date).format("YYYY-MM-DD"));
                            }

                            if(res.data.repeating == 'TODO'){
                                self.edit_event_modal.find('#delete_all_repeating_events').show();
                            }

                            // client select
                            if(res.data.clients.length > 0){
                                self.edit_event_modal.find('#clients_edit').val(res.data.clients);
                                self.edit_event_modal.find('#clients_edit').select2().trigger("change");
                            }
                            if(res.data.vipClients.length > 0){
                                let table = self.edit_event_modal.find('.vip-clients');                                    
                                $.each(res.data.vipClients, function(i,c){
                                    table.append($(`<tr><td class="font-weight-normal text-primary">${c.first_name}</td><td class="font-weight-normal text-primary pr-4">${c.last_name}</td><td>${c.note}</td><td class="pl-4"><a href="javascript:;" onclick="LESSONS.removeVIP(this);"><i class="icon-close text-danger"></i></a></td></tr>`).hide().fadeIn(200));                                
                                });
                            }                               
                            // teacher select
                            if(res.data.teachers.length > 0){
                                self.edit_event_modal.find('#teachers_edit').val(res.data.teachers);
                                self.edit_event_modal.find('#teachers_edit').select2().trigger("change");
                            }
                            
                            if(res.data.canceled === "1"){
                                self.delete_event.hide();
                            }else{
                                self.delete_event.show();
                            }

                            self.edit_event_modal.modal("show");
                        }else{
                            N.show('error', 'Nepovedlo se získat data o lekci, zkuste to znovu.');
                        }
                    });
                },
                events: function(start, end, timezone, callback){
                    $.ajax({
                        url: '/admin/lessons/calendar_get',
                        dataType: 'json',
                        type: "POST",
                        data: {
                            start: start.format(),
                            end: end.format()
                        },
                        success: function(res){
                            var events = [];
                            if(!res.error){
                                $.map( res.data, function( r ) {
                                    var evt = {
                                        id: r.id,
                                        title: r.name,
                                        start: r.start,
                                        canceled: r.canceled,
                                        substitute: r.substitute,
                                        end: r.end,
                                        allDay: r.allDay,
                                        clientCount: r.client_count,
                                        clientLimit: r.client_limit,
                                        teachers: r.teachers
                                    };

                                    events.push(evt);
                                });
                            }

                            callback(events);
                        }
                    });
                },
                eventRender: function(event, el, view) {
                    var content = $(el).find('.fc-content');
                    
                    if(event.canceled === "1") $(el).addClass("canceled");
                    if(event.substitute === "1" && event.canceled !== "1") $(el).addClass("substituted");

                    var html = '';
                        html += '<div class="row"><div class="col-md-12"><h5>'+event.title+'</h5></div></div>';
                        html += `<div class="row"><div class="col-md-2"><p class="s-15"><i class="icon-clock-o"></i>&nbsp;${moment(event.start).format('HH:mm')}</p></div><div class="col-md-1"><p class="s-15"><i class="icon-users"></i>&nbsp;${event.clientCount}/${event.clientLimit}</p></div><div class="col-md-1"><p class="s-15"><i class="icon-school"></i>&nbsp;${event.teachers}</p></div></div>`;
                        //html += '<a class="event-button">Více informací&nbsp;<i class="icon-arrow-circle-o-right"></i></a>';

                        if(event.canceled === "1"){
                            html += '<a class="event-info">Zrušeno</a>';
                        }

                        if(event.substitute === "1" && event.canceled !== "1"){
                            html += '<a class="event-info">Náhrada</a>';
                        }

                    content.html(html);
                }
            });

            self.add_event_modal.on('hidden.bs.modal', function(){
                var inputs = self.add_event_modal.find("input, select, textarea");
                $.each(inputs, function(i, input){
                    if($(input).attr('type') == 'checkbox'){
                        $(input).prop("checked", false);
                    }else{
                        $(input).val("");
                    }

                    $(input).removeClass("invalid");
                });

                self.image_dropper.find('.preview').css('background-image', 'none');
                self.image_dropper.removeClass('uploaded');
                self.lesson_image = null;
            });
            self.edit_event_modal.on('hidden.bs.modal', function(){
                var inputs = self.edit_event_modal.find("input, select, textarea");
                $.each(inputs, function(i, input){
                    if($(input).attr('type') == 'checkbox'){
                        $(input).prop("checked", false);
                    }else{
                        $(input).val("");
                    }
                    $(input).removeClass("invalid");
                });
                self.edit_event_modal.find(".vip-clients").html('');

                self.edit_event_modal.find('#delete_all_repeating_events').hide();
                self.edit_event_modal.find('#clients_edit').val([]);
                self.edit_event_modal.find('#clients_edit').select2().trigger("change");
                self.edit_event_modal.find('#teachers_edit').val([]);
                self.edit_event_modal.find('#teachers_edit').select2().trigger("change");

                self.image_dropper_edit.find('.preview').css('background-image', 'none');
                self.image_dropper_edit.removeClass('uploaded');
                self.lesson_image_edit = null;
                self.del_image_edit.removeAttr('data-lesson');

                self.delete_event.removeAttr('data-date');
            });

            $(".switch-to-table").click(function(){ setTimeout(function(){ self.lessons_table.redraw(true); }, 100); });
        },
        showClientsReservationInfo: function(clients,start){
            var clientsInfo='',
                clientsCount=0;
            if(start){
                $.each(clients, function(start,clients) {
                    clientsInfo += `<h5>${start}</h5><hr class="bg-primary text-primary my-2">`;
                    $.each(clients, function(i,v) {
                        let client_name = $(`#clients option[value="${v.client_id}"]`).text() || v.client_name,
                            icon = v.type == 'danger' ? 'close' : 'check';
                        clientsInfo += `<div class="alert alert-${v.type} mb-1 p-1"><i class="icon-${icon} mx-2"></i>${client_name} - ${v.msg}</div>`;
                        clientsCount++;
                    });
                });
            } else {
                $.each(clients, function(i,v) {
                    let client_name = $(`#clients option[value="${v.client_id}"]`).text() || v.client_name,
                        icon = v.type == 'danger' ? 'close' : 'check';
                    clientsInfo += `<div class="alert alert-${v.type} mb-1 p-1"><i class="icon-${icon} mx-2"></i>${client_name} - ${v.msg}</div>`;
                    clientsCount++;
                });
            }
            if(clientsCount>0){
                $("#dialog").html(clientsInfo);
                $("#dialog").dialog({
                    title: 'Informace o rezervaci klientů',
                    resizable: false,
                    width: "550px",
                    height: "auto",
                    buttons: {
                        "OK": function() {
                            $(this).dialog("close");
                        }
                    }
                });
            }  
        },
        removeVIP: function(el){
            $(el).closest('tr').fadeOut(200, function() { $(this).remove(); });
        },
        addVIP: function(el){
            let name = $(el).closest('.input-group').find('.vip-client-name'),
                surname = $(el).closest('.input-group').find('.vip-client-surname'),
                note = $(el).closest('.input-group').find('.vip-client-note'),
                table = $(el).closest('form').find('.vip-clients');
            if(!name.val() || !note.val()){
                N.show('error','Vyplňte prosím jméno, příjmení i poznámku');
                if(!name.val()) name.addClass('invalid'); else name.removeClass('invalid');
                if(!surname.val()) surname.addClass('invalid'); else surname.removeClass('invalid');                
                if(!note.val()) note.addClass('invalid'); else note.removeClass('invalid');
            } else {
                name.removeClass('invalid'); surname.removeClass('invalid'); note.removeClass('invalid');
                table.append($(`<tr><td class="font-weight-normal text-primary">${name.val()}</td><td class="font-weight-normal text-primary pr-4">${surname.val()}</td><td>${note.val()}</td><td class="pl-4"><a href="javascript:;" onclick="LESSONS.removeVIP(this);"><i class="icon-close text-danger"></i></a></td></tr>`).hide().fadeIn(200));
                // clear inputs
                name.val(''); surname.val(''); note.val('');                
            }
        },
    }
}());

LESSONS.init();