'use strict';

var USERS = USERS || (function () {
    var self;
    return {
        users_table: '#usersTable',
        users_table_url: $('#usersTable').data("ajax"),
        inactive_table: '#inactiveTable',
        inactive_table_url: $('#inactiveTable').data("ajax"),
        invite_table: '#inviteTable',
        invite_table_url: $('#inviteTable').data('ajax'),
        invitation_submit: $('.send-user-invitation'),
        user_form: $('#addUserForm'),
        user_submit: $('.add-user-submit'),

        role: null,
        users_roles: null,
        roleClasses: ['indigo darken-3', 'indigo accent-2', 'indigo lighten-2', 'light-blue lighten-3', 'light-blue darken-1', 'light-blue darken-4', 'teal darken-3', 'teal', 'teal lighten-2'],
        
        active_picker: {'1':'Aktivní', '0':'Neaktivní'},
        init: async function(params){
            self = this;

            this.role = await GYM._role();
            this.users_roles = await GYM._get_roles();

            NProgress.configure({ parent: '#app', minimum: 0.1, showSpinner: false });   

            this.users_table = new Tabulator(this.users_table, {
                layout: 'fitColumns',
                placeholder:"Nebyli nalezeni žádní uživatelé",
                resizableColumns: false,
                pagination: 'remote',
                ajaxSorting:true,
                ajaxFiltering:true,
                paginationSize: 20,
                paginationSizeSelector:[10, 20, 30, 50, 100],
                langs: GYM.tabulator_czech,
                layoutColumnsOnNewData:true,
                columns: [
                    {title: 'UŽIVATEL', field: 'email', headerFilter:true, headerFilterPlaceholder:"Hledat podle emailu", formatter: this.returnName},
                    {title: 'STATUS', field: 'active', headerFilter:"select", headerFilterFunc:USERS.statusFilter, headerFilterParams: USERS.active_picker, headerFilterPlaceholder: 'Aktivní/neaktivní', formatter: this.returnStatus},
                    {title: 'ROLE', field: 'role', headerFilter:"select", headerFilterParams: USERS.users_roles, headerFilterPlaceholder: 'Výběr role', formatter: this.returnRoleBadge},
                    {title: '', align: 'right', headerSort:false, formatter: this.returnTableButtons, width: 100}
                ]
            });
            this.users_table.setLocale("cs-cs");
            this.users_table.setData(this.users_table_url);

            this.inactive_table = new Tabulator(this.inactive_table, {
                layout: 'fitColumns',
                placeholder:"Nebyli nalezeni žádní uživatelé",
                resizableColumns: false,
                pagination: 'remote',
                ajaxSorting:true,
                ajaxFiltering:true,
                paginationSize: 20,
                paginationSizeSelector:[10, 20, 30, 50, 100],
                langs: GYM.tabulator_czech,
                layoutColumnsOnNewData:true,
                columns: [
                    {title: 'UŽIVATEL', field: 'email', headerFilter:true, headerFilterPlaceholder:"Hledat podle emailu", formatter: this.returnName},
                    {title: 'STATUS', field: 'active', headerFilter:"select", headerFilterFunc:USERS.statusFilter, headerFilterParams: USERS.active_picker, headerFilterPlaceholder: 'Aktivní/neaktivní', formatter: this.returnStatus},
                    {title: 'ROLE', field: 'role', headerFilter:"select", headerFilterParams: USERS.users_roles, headerFilterPlaceholder: 'Výběr role', formatter: this.returnRoleBadge},
                    {title: '', align: 'right', headerSort:false, formatter: this.returnTableButtons, width: 100}
                ]
            });
            this.inactive_table.setLocale("cs-cs");

            this.invite_table = new Tabulator(this.invite_table, {
                layout: 'fitColumns',
                placeholder:"Nebyly nalezeny žádné pozvánky",
                resizableColumns: false,
                pagination: 'local',
                paginationSize: 20,
                langs: GYM.tabulator_czech,
                layoutColumnsOnNewData:true,
                columns: [
                    {title: 'UŽIVATEL', field: 'email', headerFilter:true, headerFilterPlaceholder:"Hledat podle emailu"},
                    {title: 'ROLE', field: 'role', headerFilter:"select", headerFilterParams: USERS.users_roles, headerFilterPlaceholder: 'Výběr role', formatter: this.returnRoleBadge},
                    {title: 'ODESLÁNO', field: 'date_created'},
                    {title: '', align: 'right', headerSort:false, formatter: this.returnInviteButtons}
                ]
            });
            this.invite_table.setLocale("cs-cs");

            this.fireEvents();
        },
        statusFilter: function(headerValue, rowValue, rowData, filterParams){
            var compare = 0;
            if(headerValue == "Aktivní") compare = 1;

            return rowData.active == compare;
        },
        returnRoleBadge: function(cell){
            var row_data = cell._cell.row.data;
            var group_id = row_data.group_id;

            return '<span class="badge badge-primary '+USERS.roleClasses[group_id]+'">' + USERS.users_roles[group_id] + '</span>';
        },
        returnStatus: function(cell){
            var row_data = cell._cell.row.data;
            if(row_data.active != 1){
                return '<span class="icon icon-circle s-12 mr-2"></span> Neaktivní';
            }else{
                return '<span class="icon icon-circle s-12 mr-2 text-success"></span> Aktivní';
            }
        },
        returnNum: function(){
            return 0;
        },
        returnName: function(cell, params, onRendered){
            var row_data = cell._cell.row.data;
            return '<a href="/admin/users/edit/'+row_data.id+'">'+row_data.first_name+' '+row_data.last_name+'</a><br /><small>'+row_data.email+'</small>';
        },
        returnInviteButtons: function(cell){
            var row_data = cell._cell.row.data;
            var delete_button = '<a href="javascript:;" class="btn btn-xs btn-danger invite-remove" data-id="'+row_data.id+'" data-ajax="/admin/users/remove_invite_ajax">Zrušit pozvánku</a>';

            return delete_button;
        },
        returnTableButtons: function(cell, params, onRendered){
            var row_data = cell._cell.row.data;
            var edit_button = GYM._isAllowed('edit') ? '<a href="/admin/users/edit/'+row_data.id+'" data-id="'+row_data.id+'"><i class="icon-pencil"></i></a>&nbsp;' : '';
            var delete_button = '<a href="javascript:;" class="btn btn-xs btn-danger user-remove" data-id="'+row_data.id+'" data-ajax="/admin/users/remove_user_ajax">Odstranit</a>';

            return edit_button;
        },
        validEmail: function(email) {
            var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(String(email).toLowerCase());
        },
        fireEvents: function(){

            $("#js_depot_home_clear_filter").click(function(){
                USERS.users_table.clearHeaderFilter();
                USERS.users_table.clearSort();
            });

            $('.switch-to-users').click(function(){
                self.users_table.setData(self.users_table_url);
            });
            $('.switch-to-inactive-users').click(function(){
                self.inactive_table.setData(self.inactive_table_url);
            });
            $('.switch-to-invites').click(function(){
                self.invite_table.setData(self.invite_table_url);
            });

            $('#usersTable').on('click', '.user-remove', function(){
                var url = $(this).data('ajax'),
                    id = $(this).data('id');

                var agreed = confirm('Opravdu chcete vymazat tohoto uživatele?');
                if(agreed){
                    GYM._post(url, {'user_id':id}).done(function(res){
                        if(!res.error){
                            self.users_table.setData(self.users_table_url);
                            N.show('success', 'Uživatel úspěšně smazán!');
                        }else{
                            N.show('error', 'Nepovedlo se smazat uživatele, zkuste to znovu.');
                        }

                        NProgress.done();
                    });
                }
            });

            $('#inviteTable').on("click", '.invite-remove', function(){
                var url = $(this).data('ajax'),
                    id = $(this).data('id');

                var agreed = confirm('Opravdu chcete vymazat tuto pozvánku? Uživatel nebude již schopný dokončit registraci.');
                if(agreed){
                    GYM._post(url, {'user_id':id}).done(function(res){
                        if(!res.error){
                            self.invite_table.setData(self.invite_table_url);
                            N.show('success', 'Pozvánka úspěšně smazána!');
                        }else{
                            N.show('error', 'Nepovedlo se smazat pozvánku, zkuste to znovu.');
                        }

                        NProgress.done();
                    });
                }
            });

            this.user_submit.click(function(e){
                e.preventDefault();
                var url = $(this).data("ajax");

                var inputs = self.user_form.find('input:required, select:required');

                $.each(inputs, function(i, input){
                    if($(input).val()){
                        $(input).removeClass("invalid");
                    }else{
                        if($(input).attr("type") != "checkbox") $(input).addClass("invalid");
                    }
                });

                if(!$('#agreement').is(':checked')){
                    $('#agreement').parent().find("label").addClass('invalid');
                }else{
                    $('#agreement').parent().find("label").removeClass('invalid');
                }

                if(self.user_form.find(".invalid").length <= 0){

                    var formData = new FormData(),
                        form_inputs = self.user_form.find('input, select');

                    $.each(form_inputs, function(i, input){
                        formData.append($(input).attr('name'), $(input).val());
                    });

                    GYM._upload(url, formData).done(function(res){
                        if(!res.error){
                            N.show('success', 'Uživatelský účet byl úspěšně vytvořen!');
                            self.user_form[0].reset();
                            self.users_table.setData(self.users_table_url);
                        }else{
                            N.show('error', 'Nepodařilo se vytvořit účet, zkontrolujte údaje nebo to zkuste znovu!');
                        }
                        
                        NProgress.done();
                    });

                }

            });

            this.invitation_submit.click(function(){
                if(!$(this).prop("disabled")){
                    var url = $(this).data("ajax"),
                    email = $('.invitation-mail').val(),
                    role = $('.invitation-role').val();

                    if(email.length > 0 && self.validEmail(email)){
                        $('.invitation-mail').removeClass("invalid");
                        self.invitation_submit.attr("disabled", true);
                        
                        GYM._post(url, {'email': email, 'role': role}).done(function(res){
                            if(!res.error){
                                $('.invitation-mail').val('');
                                $('.invitation-role').val(1);

                                N.show('success', 'Pozvánka byla odeslána!');
                            }else{
                                N.show('error', 'Pozvánku se nepodařilo odeslat, zkuste to znovu!');
                            }

                            self.invitation_submit.removeAttr("disabled");
                            self.invite_table.setData(self.invite_table_url);
                            NProgress.done();
                        });
                    }else{
                        $('.invitation-mail').addClass("invalid");
                        N.show('error', 'Chybí e-mail pozvánky nebo má nesprávný formát!');
                    }   
                }
            });
        }
    }
}());

USERS.init();