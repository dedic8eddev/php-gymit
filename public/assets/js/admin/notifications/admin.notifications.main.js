'use strict';

var NOTIFICATIONS = NOTIFICATIONS || (function () {
    var self;
    return {
        role: null,
        limit_select: $('.limit-select'),
        type_select: $('.type-select'),
        notifications_container: $('.notifications-container'),
        pagination_container: $('.pagination'),
        limit: 10,
        type: 'ALL',
        page: 1,
        init: async function(){
            self = this;

            this.role = await GYM._role();
            NProgress.configure({ parent: '#app', minimum: 0.1, showSpinner: false });

            if(typeof this.getUrlVars()["t"] != "undefined"){
                this.type = this.getUrlVars()["t"];
                this.type_select.val(this.getUrlVars()["t"]);
            }

            this.fireEvents();
        },
        getUrlVars: function(){
            var vars = {};
            var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi,    
            function(m,key,value) {
              vars[key] = value;
            });
            return vars;
        },
        getNotifications: function(){
            $.ajax({
                type: "GET",
                url: "/admin/dashboard/get_all_notifications_ajax",
                data: {page: self.page, size: self.limit, type: self.type},
                dataType: "json",
                beforeSend: function(){
                    NProgress.start();
                },
                success: function(res){
                    if(!res.error){
                        self.notifications_container.html('');

                        if(res.data.length > 0){
                            $.each(res.data, function(i, n){
                                self.notifications_container.append(self.getNotifTemplate(n));
                            });

                            var pagination_data = self.getPaginationArray(self.page, res.last_page);
                            self.renderPagination(pagination_data);
                        }else{
                            // empty result
                        }
                    }else{

                    }

                    NProgress.done();
                }
            });
        },
        getNotifTemplate: function(data){
            var visibility = '';
            if(data.read == 1){
                visibility = 'opacity: .6;';
            }

            var html = '';
            html += '<div class="col-md-12 col-lg-12 my-3" style="'+visibility+'">';
            html += '<div class="card r-0 no-b shadow2 ">';
            html += '<div class="shadow p-4 bg-light ">';
                html += '<div class="d-flex align-items-center justify-content-between ">';
                    html += '<div><h5>'+data.title+'</h5> <small>'+data.message+'</small></div>';
                html += '<div><div class="float-right ">'+moment(data.created_on).format('DD. MM. YYYY H:m')+'</div></div>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
            html += '</div>';

            return html;
        },
        getPaginationArray: function(currentPage, nrOfPages){
            var delta = 2,
                range = [],
                rangeWithDots = [],
                l;

            range.push(1);  

            if (nrOfPages <= 1){
            return range;
            }

            for (let i = currentPage - delta; i <= currentPage + delta; i++) {
                if (i < nrOfPages && i > 1) {
                    range.push(i);
                }
            }  
            range.push(nrOfPages);

            for (let i of range) {
                if (l) {
                    if (i - l === 2) {
                        rangeWithDots.push(l + 1);
                    } else if (i - l !== 1) {
                        rangeWithDots.push('...');
                    }
                }
                rangeWithDots.push(i);
                l = i;
            }

            return rangeWithDots;
        },
        renderPagination: function(pagination){
            self.pagination_container.html('');
            
            if(pagination.length > 1){
                var first_page = pagination[0];
                var last_page = pagination[pagination.length - 1];

                if(self.page > first_page){
                    self.pagination_container.append('<li class="page-item pagination-prev" data-page="'+(self.page - 1)+'"><a class="page-link" href="javascript:;">«</a></li>');
                }

                for(var i = 0;i < pagination.length; i++){
                    if(pagination[i] != '...'){
                        if(pagination[i] == self.page){
                            self.pagination_container.append('<li class="page-item pagination-page active" data-page="'+pagination[i]+'"><a class="page-link" href="javascript:;">'+pagination[i]+'</a></li>');
                        }else{
                            self.pagination_container.append('<li class="page-item pagination-page" data-page="'+pagination[i]+'"><a class="page-link" href="javascript:;">'+pagination[i]+'</a></li>');
                        }
                    }else{
                        self.pagination_container.append('<li class="page-item pagination-dots disabled"><a class="page-link" href="javascript:;">'+pagination[i]+'</a></li>');
                    }
                }

                if(self.page < last_page){
                    self.pagination_container.append('<li class="page-item pagination-next" data-page="'+(self.page + 1)+'"><a class="page-link" href="javascript:;">»</a></li>');
                }
            }
        },
        fireEvents: function(){   
            this.getNotifications();

            // pagi
            $('.pagination').on('click', '.pagination-prev', function(){
                var pg = $(this).data("page");
                self.page = pg;
                self.getNotifications();
            });
            $('.pagination').on('click', '.pagination-page', function(){
                var pg = $(this).data("page");
                self.page = pg;
                self.getNotifications();
            });
            $('.pagination').on('click', '.pagination-next', function(){
                var pg = $(this).data("page");
                self.page = pg;
                self.getNotifications();
            });

            // limit change
            this.limit_select.change(function(){
                var l = $(this).val();
                self.limit = l;
                self.getNotifications();
            });
            // type change
            this.type_select.change(function(){
                var t = $(this).val();
                self.type = t;
                self.getNotifications();
            });

        }
    }
}());

NOTIFICATIONS.init();