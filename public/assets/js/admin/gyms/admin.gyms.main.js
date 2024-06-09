'use strict';

var GYMS = GYMS || (function () {
    var self;
    return {
        role: false,
        
        gyms_table: '#gymsTable',
        gyms_table_data: $('#gymsTable').data('ajax'),

        submit_gym: $('.add-gym-submit'),
        new_gym_name_input: $('#gymname_input'),
        new_gym_slug_input: $('#slug_input'),
        new_gym_dbname_input: $('#dbname_input'),

        init: async function(params){
            self = this;

            this.role = await GYM._role();
            NProgress.configure({ parent: '#app', minimum: 0.1, showSpinner: false });

            this.gyms_table = new Tabulator(this.gyms_table, {
                layout: 'fitColumns',
                placeholder:"Nebyly nalezeny žádné provozovny",
                resizableColumns: false,
                paginationSize: 20,
                paginationSizeSelector:[10, 20, 30, 50, 100],
                langs: GYM.tabulator_czech,
                layoutColumnsOnNewData:true,
                columns: [
                    {title: 'NAME', field: 'name', headerFilter:true},
                    {title: 'ZKRATKA', field: 'slug', headerFilter: true},
                    {title: '', align: 'right', headerSort:false, formatter: this.returnTableButtons}
                ]
            });
            this.gyms_table.setLocale("cs-cs");
            this.gyms_table.setData(this.gyms_table_data);

            this.fireEvents();
        },
        returnTableButtons: function(cell){
            var d = cell.getRow().getData();
            var delete_btn = '<a href="javascript:;" class="btn-fab btn-fab-sm shadow btn-danger remove-gym" data-id="'+d._id.$id+'"><i class="icon-delete"></i></a></a>';
            var edit_btn = '<a href="/admin/gyms/settings/'+d._id.$id+'/" class="btn-fab btn-fab-sm shadow btn-primary"><i class="icon-mode_edit"></i></a></a>';
            
            if(!d.primary){
                return edit_btn + '&nbsp;' + delete_btn;
            }else{
                return edit_btn;
            }
        },
        fireEvents: function(){
            self.new_gym_name_input.change(function(){
                var n = $(this).val();
                self.new_gym_slug_input.val(GYM._slug(n));
            });

            self.submit_gym.click(function(e){
                e.preventDefault();

                var n = self.new_gym_name_input.val(),
                    s = self.new_gym_slug_input.val(),
                    d = self.new_gym_dbname_input.val(),
                    url = $(this).data('ajax');

                // slugify if not
                if( ! /^[a-z](-?[a-z])*$/.test(s) ){
                    s = GYM._slug(s);
                    self.new_gym_slug_input.val( GYM._slug(s) );
                }

                if(n.length > 0 && s.length > 0){
                    GYM._post(url, {'name':n, 'slug':s, 'dbname':d}).done(function(res){
                        if( ! res.error){
                            N.show('success', 'Provozovna byla úspěšně přidána!', false, true); // reload
                        }else{
                            N.show('error', 'Nepovedlo se přidat provozovnu, zkuste to znovu nebo později.');
                        }
                    });
                }
            });

            $('body').on('click', '.remove-gym', function(e){
                var agree = confirm('Opravdu chcete vymazat tuto provozovnu? Všechna její data budou ztracena.');
                if( agree ){
                    var id = $(this).data('id');
                    GYM._post('/admin/gyms/delete_gym_ajax', {'id':id}).done(function(res){
                        if( ! res.error){
                            N.show('success', 'Provozovna byla úspěšně odstraněna!', false, true); // reload
                        }else{
                            N.show('error', 'Nepovedlo se odstranit provozovnu, zkuste to znovu nebo později.');
                        }
                    });
                }
            });
        }
    }
}());

GYMS.init();