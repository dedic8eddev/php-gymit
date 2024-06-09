'use strict';

var LOCKERS = LOCKERS || (function () {
    var self;

    return {
        locker_container: $("#lockerContainer"),
        locker_statuses: {"locked": "badge-danger", "unlocked": "badge-success", "intrusion": "badge-warning"},
        status_translate: {"locked":"Uzamčeno", "unlocked":"Odemčeno", "intrusion":"Vniknutí!"},
        locker_template: function (name, slug, status, full_data) {
            var html = '<div class="col-md-4 locker-box" data-slug="'+slug+'" data-locker="'+"'"+JSON.stringify(full_data)+"'"+'>';
                    html += '<div class="row">';
                        html += '<div class="col-md-6 locker-name">'+name+'</div>';
                        html += '<div class="col-md-6 locker-status text-right"><span class="badge '+self.locker_statuses[status]+'">'+self.status_translate[status]+'</span></div>';
                    html += '</div>';
                html += '</div>';
            
            return html;
        },
        unlocked_lockers: 0,
        total_lockers: 0,
        periodic: null,
        init: function(params){
            self = this;

            this.fireEvents();
        },
        pullLockers: function() {
            return GYM._get("/admin/lockers/get_locker_data_ajax");
        },
        renderLockers: function(data) {
            var total_unlocked_male = 0,
                total_unlocked_female = 0,
                total_locked_female = 0,
                total_locked_male = 0;

            $.each(data, function(i, locker){
                if(typeof locker.lockerStatus == "undefined") locker.lockerStatus = {"status":"unlocked"};

                var locker_name = "Skřínka #"+locker.lockerNumber,
                    locker_slug = "skrinka_"+locker.lockerRoom+"_"+locker.lockerNumber;

                var exists = self.locker_container.find('.locker-box[data-slug="'+locker_slug+'"]');

                if(exists.length > 0){
                    exists.find(".locker-status").html('<div class="col-md-6 locker-status"><span class="badge '+self.locker_statuses[locker.lockerStatus.status]+'">'+self.status_translate[locker.lockerStatus.status]+'</span></div>');
                    exists.data("locker", locker);
                }else{
                    self.locker_container.find('.room-row[data-room="'+locker.lockerRoom+'"]').append(self.locker_template(locker_name, locker_slug, locker.lockerStatus.status, locker));
                }

                if(locker.lockerStatus.status == "unlocked" && locker.lockerRoom == "male") total_unlocked_male++;
                if(locker.lockerStatus.status == "unlocked" && locker.lockerRoom == "female") total_unlocked_female++;

                if(locker.lockerStatus.status == "locked" && locker.lockerRoom == "male") total_locked_male++;
                if(locker.lockerStatus.status == "locked" && locker.lockerRoom == "female") total_locked_female++;
            });

            $(".locked-total-female").text(total_locked_female);
            $(".unlocked-total-female").text(total_unlocked_female);

            $(".locked-total-male").text(total_locked_male);
            $(".unlocked-total-male").text(total_unlocked_male);
        },
        fireEvents: function(){
            self.pullLockers().done(function(res){
                if(!res.error) {
                    self.renderLockers(res.data);

                    self.periodic = setInterval(function(){
                        self.pullLockers().done(function(res){
                            if(!res.error) {
                                self.renderLockers(res.data);
                            }
                            else N.show("error", "Nebylo možné získat data o skřínkách!");
                        });
                    }, 5000);
                }
                else N.show("error", "Nebylo možné získat data o skřínkách!");
            });

            $("body").on("click", ".locker-box", function () {
                var locker = $(this).data("locker");

                console.log(locker);

                $("#lockerModal").modal("show");
                $("#lockerModal").find(".modal-title > span").text(locker.lockerNumber);
                $(".save-locker-cards").data("lockerid", locker._id);

                if (typeof locker.vipCards != "undefined") $("#lockerModal").find("#vipCards").val(locker.vipCards).trigger("change");
                if (typeof locker.masterCards != "undefined") $("#lockerModal").find("#masterCards").val(locker.masterCards).trigger("change");
            });

            $("body").on("click", ".save-locker-cards", function(){
                var locker_id = $(this).data("lockerid"),
                    vipCards = $("#lockerModal").find("#vipCards").val(),
                    masterCards = $("#lockerModal").find("#masterCards").val();

                GYM._post("/admin/lockers/update_locker_cards", {lockerId: locker_id, vipCards: vipCards, masterCards: masterCards}).done(function(res){
                    if (!res.error) {
                        self.pullLockers().done(function(res){
                            if(!res.error) {
                                self.renderLockers(res.data);
                                N.show("success", "Skřínka byla upravena!");
                                $("#lockerModal").modal("hide");
                                $("#lockerModal").find("#vipCards").val([]).trigger("change");
                                $("#lockerModal").find("#masterCards").val([]).trigger("change");
                            }
                            else N.show("error", "Nebylo možné získat data o skřínkách!");
                        });
                    }else{
                        N.show("error", "Nepovedlo se kontaktovat skřínku, zkuste to znovu později!")
                    }
                });
            });

            $("body").on("click", ".remote-open-locker", function(){
                var locker_id = $(".save-locker-cards").data("lockerid");

                GYM._post("/admin/lockers/remote_open_locker", {lockerId: locker_id}).done(function(res){
                    if (!res.error) {
                        self.pullLockers().done(function(res){
                            if(!res.error) {
                                self.renderLockers(res.data);
                                N.show("success", "Skřínka byla odemčena!");
                                $("#lockerModal").modal("hide");
                                $("#lockerModal").find("#vipCards").val([]).trigger("change");
                                $("#lockerModal").find("#masterCards").val([]).trigger("change");
                            }
                            else N.show("error", "Nebylo možné získat data o skřínkách!");
                        });
                    }else{
                        N.show("error", "Nepovedlo se odemknout skřínku, zkuste to znovu později!")
                    }
                });
            });

            /*
            Commented over: this actually can be done with just making the input empty
            $("body").on("click", ".remote-unblock-locker", function(){
                var locker_id = $(".save-locker-cards").data("lockerid");

                GYM._post("/admin/lockers/remote_unblock_locker", {lockerId: locker_id}).done(function(res){
                    if (!res.error) {
                        self.pullLockers().done(function(res){
                            if(!res.error) {
                                self.renderLockers(res.data);
                                N.show("success", "Skřínka byla odblokována!");
                                $("#lockerModal").modal("hide");
                                $("#lockerModal").find("#vipCards").val([]).trigger("change");
                                $("#lockerModal").find("#masterCards").val([]).trigger("change");
                            }
                            else N.show("error", "Nebylo možné získat data o skřínkách!");
                        });
                    }else{
                        N.show("error", "Nepovedlo se odblokovat skřínku, zkuste to znovu později!")
                    }
                });
            });

            
            Commented over: This is probably totally unnecessary for the end user
            $("body").on("click", ".remote-reset-locker", function(){
                var locker_id = $(".save-locker-cards").data("lockerid");

                GYM._post("/admin/lockers/remote_reset_locker", {lockerId: locker_id}).done(function(res){
                    if (!res.error) {

                        self.pullLockers().done(function(res){
                            if(!res.error) {
                                self.renderLockers(res.data);
                                N.show("success", "Skřínka byla resetována!");
                                $("#lockerModal").modal("hide");
                                $("#lockerModal").find("#vipCards").val([]).trigger("change");
                                $("#lockerModal").find("#masterCards").val([]).trigger("change");
                            }
                            else N.show("error", "Nebylo možné získat data o skřínkách!");
                        });
                    }else{
                        N.show("error", "Nepovedlo se resetovat skřínku, zkuste to znovu později!")
                    }
                });
            });
            */
        }
    }
}());

LOCKERS.init();