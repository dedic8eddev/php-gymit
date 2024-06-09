<aside id="js_main_sidebar" class="main-sidebar fixed offcanvas b-r sidebar-tabs" data-toggle='offcanvas'>
<div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: 1209px;"><div class="sidebar" style="height: 1209px; overflow: hidden; width: auto;">
        <div class="d-flex hv-100 align-items-stretch">
            <div class="indigo text-white">
                <div class="nav mt-5 pt-5 flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <a class="nav-link<?php if(in_array($this->router->fetch_class(),['payments','dashboard','users','lessons','profile','coaches','clients','depot','blog','settings','cms']) || in_array($this->router->fetch_method(),['footer'])) echo ' active show'; ?>" id="v-pills-home-tab" data-toggle="pill" href="#v-pills-home" role="tab" aria-controls="v-pills-home" aria-selected="true"><i class="icon-stars3"></i></a>
                    <?php if(hasReadPermissionAtLeastInOneSection([SECTION_GYMS, SECTION_CUSTOM_FIELDS, SECTION_CARD_MANAGEMENT, SECTION_CASH_REGISTER])): ?>
                        <a class="nav-link<?php if(in_array($this->router->fetch_class(),['gyms','custom_fields','cards']) && !in_array($this->router->fetch_method(),['footer'])) echo ' active show'; ?>" id="v-pills-settings-tab" data-toggle="pill" href="#v-pills-settings" role="tab" aria-controls="v-pills-settings" aria-selected="false"><i class="icon-cog"></i></a>
                    <?php endif; ?>

                    <a href="<?php echo base_url('admin/profile'); ?>" class="nav-link <?php if($this->router->fetch_class() == 'profile') echo "active show"; ?>">
                        <figure class="avatar">
                            <i class="icon-user" style="color: #3f51b5!important;"></i>
                            <span class="avatar-badge online"></span>
                        </figure>
                    </a>
                </div>
            </div>
            <div class="tab-content flex-grow-1" id="v-pills-tabContent">
                
                <div class="tab-pane fade<?php if(in_array($this->router->fetch_class(),['lockers','payments','dashboard','users','lessons','profile','coaches','clients','depot','blog','settings', 'media', 'cms', 'pricelist', 'reporting','vouchers']) || in_array($this->router->fetch_method(),['footer'])) echo ' active show'; ?>" id="v-pills-home" role="tabpanel" aria-labelledby="v-pills-home-tab">
                    <div class="relative sticky b-b">
                        <div class="p-2 text-center">
                            <img src="<?php echo site_url(config_item('app')['img_folder'].'svg/logo_gymit.svg'); ?>" alt="Gymit" width="150px" />
                        </div>
                    </div>
                    <div class="relative brand-wrapper sticky b-b">
                        <div class="d-flex justify-content-between align-items-center p-3">
                            <div class="text-xs-center">
                                <span class="font-weight-lighter s-18">Menu</span>
                            </div>
                        </div>
                    </div>
                    <ul class="sidebar-menu">
                        <?php if (hasReadPermission(SECTION_DASHBOARDS)): ?>
                        <li class="treeview<?php if($this->router->fetch_class() == 'dashboard') echo ' active'; ?>">
                            <a href="<?php echo base_url('admin/dashboard/'); ?>">
                                <i class="icon icon-dashboard2 s-24"></i> <span>Home</span>
                            </a>
                        </li>
                        <?php endif; ?>

                        <?php if (hasReadPermission(SECTION_LOCKERS)): ?>
                        <li class="treeview<?php if($this->router->fetch_class() == 'lockers') echo ' active'; ?>">
                            <a href="<?php echo base_url('admin/lockers/'); ?>">
                                <i class="icon icon-lock3 s-24"></i> <span>Skříňky</span>
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <?php if (hasReadPermission(SECTION_USERS)): ?>
                        <li class="treeview<?php if($this->router->fetch_class() == 'users') echo ' active'; ?>">
                            <a href="<?php echo base_url('admin/users/'); ?>">
                                <i class="icon icon-account_box s-24"></i> <span>Uživatelé</span>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if (hasReadPermission(SECTION_COACHES)): ?>
                        <li class="treeview<?php if($this->router->fetch_class() == 'coaches') echo ' active'; ?>">
                            <a href="<?php echo base_url('admin/coaches/'); ?>">
                                <i class="icon icon-accessibility s-24"></i> <span>Trenéři a instruktoři</span>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if (hasReadPermission(SECTION_CLIENTS)): ?>
                        <li class="treeview<?php if($this->router->fetch_class() == 'clients') echo ' active'; ?>">
                            <a href="<?php echo base_url('admin/clients/'); ?>">
                                <i class="icon icon-people s-24"></i> <span>Zákazníci</span>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if (hasReadPermission(SECTION_LESSONS)): ?>
                        <li class="treeview<?php if($this->router->fetch_class() == 'lessons' && $this->router->fetch_method() != 'templates') echo ' active'; ?>">
                            <a href="<?php echo base_url('admin/lessons/'); ?>">
                                <i class="icon icon-calendar s-24"></i> <span>Kalendář</span>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if (hasReadPermission(SECTION_DEPOT)): ?>
                        <li class="treeview<?php if($this->router->fetch_class() == 'depot') echo ' active'; ?>">
                            <a href="<?php echo base_url('admin/depot/'); ?>">
                                <i class="icon icon-box6 s-24"></i> <span>Sklad</span>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if (hasReadPermission(SECTION_PRICE_LIST)): ?>
                        <li class="<?php if($this->router->fetch_method() == 'pricelist') echo 'active'; ?>">
                            <a href="<?php echo base_url('admin/pricelist'); ?>">
                                <i class="icon icon-card_membership s-24"></i> <span>Členství a ceník služeb</span>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if (hasReadPermission(SECTION_PAYMENTS)): ?>
                        <li class="treeview<?php if($this->router->fetch_class() == 'payments') echo ' active'; ?>">
                            <a href="<?php echo base_url('admin/payments/'); ?>">
                                <i class="icon icon-payment s-24"></i> <span>Pokladna</span>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if (hasReadPermission(SECTION_REPORTING)): ?>
                        <li class="treeview<?php if($this->router->fetch_class() == 'reporting') echo ' active'; ?>">
                            <a href="<?php echo base_url('admin/reporting/'); ?>">
                                <i class="icon icon-info2 s-24"></i> <span>Reporting</span>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if (hasReadPermission(SECTION_VOUCHERS)): ?>
                        <li class="treeview<?php if($this->router->fetch_class() == 'vouchers') echo ' active'; ?>">
                            <a href="<?php echo base_url('admin/vouchers/'); ?>">
                                <i class="icon icon-gift s-24"></i> <span>Vouchery</span>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if (hasReadPermission(SECTION_CMS)): ?>
                        <li class="treeview<?php if(in_array($this->router->fetch_class(),['blog', 'media', 'gyms', 'lessons', 'cms']) && !($this->router->fetch_class() == 'lessons' && $this->router->fetch_method() != 'templates') ) echo ' active'; ?>">
                            <a href="#">
                                <i class="icon icon-list-alt s-24"></i> <span>CMS</span> <i class="icon icon-angle-left s-18 pull-right"></i>
                            </a>
                            <ul class="treeview-menu">                             
                                <li class="<?php if($this->router->fetch_method() == 'pages') echo 'active'; ?>">
                                    <a href="<?php echo base_url('admin/cms/pages/index'); ?>">
                                        <i class="icon icon-circle-o"></i> <span>Stránky</span>
                                    </a>
                                </li>                               
                                <li class="<?php if($this->router->fetch_method() == 'contact') echo 'active'; ?>">
                                    <a href="<?php echo base_url('admin/cms/contact'); ?>">
                                        <i class="icon icon-circle-o"></i> <span>Kontakt</span>
                                    </a>
                                </li>                                                                                      
                                <li class="<?php if($this->router->fetch_class() == 'lessons' && $this->router->fetch_method() == 'templates') echo 'active'; ?>">
                                    <a href="<?php echo base_url('admin/lessons/templates'); ?>">
                                        <i class="icon icon-circle-o"></i> <span>Lekce</span>
                                    </a>
                                </li>
                                <li class="<?php if($this->router->fetch_class() == 'blog' && $this->router->fetch_method() == 'index') echo 'active'; ?>">
                                    <a href="<?php echo base_url('admin/blog/'); ?>">
                                        <i class="icon icon-circle-o"></i> <span>Blog</span>
                                    </a>
                                </li>
                                <li class="<?php if($this->router->fetch_class() == 'jobs' && $this->router->fetch_method() == 'index') echo 'active'; ?>">
                                    <a href="<?php echo base_url('admin/cms/jobs'); ?>">
                                        <i class="icon icon-circle-o"></i> <span>Nabídky práce</span>
                                    </a>
                                </li>                                
                                <li class="<?php if($this->router->fetch_class() == 'media') echo 'active'; ?>">
                                    <a href="<?php echo base_url('admin/media/'); ?>">
                                        <i class="icon icon-circle-o"></i> <span>Galerie</span>
                                    </a>
                                </li>
                                <li class="<?php if($this->router->fetch_method() == 'menu') echo 'active'; ?>">
                                    <a href="<?php echo base_url('admin/cms/menu'); ?>">
                                        <i class="icon icon-circle-o"></i> <span>Menu</span>
                                    </a>
                                </li>                                
                                <li class="<?php if($this->router->fetch_method() == 'footer') echo 'active'; ?>">
                                    <a href="<?php echo base_url('admin/cms/footer'); ?>">
                                        <i class="icon icon-circle-o"></i> <span>Patička</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <?php endif; ?>

                        <li class="treeview" id="menu_switchers">
                            <ul>
                                <li class="header"><strong>VÝBĚR UŽIVATELE</strong></li>

                                <li class="">
                                    <?php $users = $this->db->get('users')->result(); ?>
                                    <select id="fakeUserPicker" class="form-control">
                                        <?php foreach($users as $u): ?>
                                            <option <?php if(gym_userid() == $u->id){echo "selected";} ?> value="<?php echo $u->id; ?>"><?php echo $u->email; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </li>
                            </ul>
                        </li>
                        
                    </ul>
                </div>

<?php if(hasReadPermissionAtLeastInOneSection([SECTION_GYMS, SECTION_CUSTOM_FIELDS, SECTION_CARD_MANAGEMENT, SECTION_CASH_REGISTER])): ?>
                <div class="tab-pane fade<?php if(in_array($this->router->fetch_class(),['gyms','custom_fields','cards','eetapp','site_settings'])) echo ' active show'; ?>" id="v-pills-settings" role="tabpanel" aria-labelledby="v-pills-settings-tab">
                    <div class="relative sticky b-b">
                        <div class="p-2 text-center">
                            <img src="<?php echo site_url(config_item('app')['img_folder'].'svg/logo_gymit.svg'); ?>" alt="Gymit" width="150px" />
                        </div>
                    </div>
                    <div class="relative brand-wrapper sticky b-b">
                        <div class="d-flex justify-content-between align-items-center p-3">
                            <div class="text-xs-center">
                                <span class="font-weight-lighter s-18">Nastavení</span>
                            </div>
                        </div>
                    </div>
                    <ul class="sidebar-menu">
                        <?php if (hasReadPermission(SECTION_GYMS)): ?>
                        <li class="treeview<?php if($this->router->fetch_class() == 'gyms') echo ' active'; ?>">
                            <a href="<?php echo base_url('admin/gyms/'); ?>">
                                <i class="icon icon-building2 s-24"></i> <span>Provozovny</span>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if (hasReadPermission(SECTION_CARD_MANAGEMENT)): ?>
                        <li class="treeview<?php if($this->router->fetch_class() == 'cards') echo ' active'; ?>">
                            <a href="<?php echo base_url('admin/cards/'); ?>">
                                <i class="icon icon-address-card s-24"></i> <span>Karty</span>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if (hasReadPermission(SECTION_CASH_REGISTER)): ?>
                        <li class="treeview<?php if($this->router->fetch_class() == 'eetapp') echo ' active'; ?>">
                            <a href="<?php echo base_url('admin/eetapp/'); ?>">
                                <i class="icon icon-cash-register s-24"></i> <span>EET</span>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if (hasReadPermission(SECTION_SITE_SETTINGS)): ?>
                        <li class="treeview<?php if($this->router->fetch_class() == 'site_settings') echo ' active'; ?>">
                            <a href="<?php echo base_url('admin/site-settings/'); ?>">
                                <i class="icon icon-settings_applications s-24"></i> <span>Nastavení stránky</span>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php //if (hasReadPermission(SECTION_CUSTOM_FIELDS)): ?>
                        <!--<li class="treeview<?php //if($this->router->fetch_class() == 'custom_fields') echo ' active'; ?>">
                            <a href="<?php //echo base_url('admin/custom-fields/'); ?>">
                                <i class="icon icon-star s-24"></i> <span>Vlastní pole</span>
                            </a>
                        </li>-->
                        <?php //endif; ?>
                        <li class="treeview" id="menu_switchers">
                            <ul>
                                <li class="header"><strong>VÝBĚR PROVOZOVNY</strong></li>

                                <li class="">
                                    <?php 
                                        $gyms = $this->gyms->getAllGyms(); 
                                    ?>
                                    <select id="gymDbPicker" class="form-control">
                                        <?php foreach($gyms as $gym): ?>
                                            <option <?php if(current_gym_db() == $gym['dbname']){echo "selected";} ?> value="<?php echo $gym['dbname']; ?>"><?php echo $gym["name"]; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
<?php endif; ?>

            </div>
        </div>
    </div><div class="slimScrollBar" style="background: rgba(0, 0, 0, 0.3); width: 5px; position: absolute; top: 0px; opacity: 0.4; display: none; border-radius: 7px; z-index: 99; right: 1px; height: 1209px;"></div><div class="slimScrollRail" style="width: 5px; height: 100%; position: absolute; top: 0px; display: none; border-radius: 7px; background: rgb(51, 51, 51); opacity: 0.2; z-index: 90; right: 1px;"></div></div>
</aside>
<!--Sidebar End-->
<div id="js_header_toolbox" class="has-sidebar-left has-sidebar-tabs">

<div class="pos-f-t">
    <div class="collapse" id="navbarToggleExternalContent">
        <div class="bg-dark pt-2 pb-2 pl-4 pr-2">
            <div class="search-bar" data-children-count="1">
                <input class="transparent s-24 text-white b-0 font-weight-lighter w-128 height-50" type="text" placeholder="start typing...">
            </div>
            <a href="#" data-toggle="collapse" data-target="#navbarToggleExternalContent" aria-expanded="false" aria-label="Toggle navigation" class="paper-nav-toggle paper-nav-white active "><i></i></a>
        </div>
    </div>
</div>

<div class="sticky">
        <div class="navbar navbar-expand d-flex justify-content-between bd-navbar white">
            <div class="relative">
                <div class="d-flex">
                    <div class="d-none d-md-block">
                        <h1 class="nav-title text-primary"><?php echo $pageTitle; ?></h1>
                    </div>
                </div>
            </div>
            <!--Top Menu Start -->
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <?php 
                        // notifications (with collections (2+))
                        $notifications = $this->collected_notifications; 

                        // 1: úkoly, 2: zakázky, 3: sklad, 4: fakturace, 5: Ostatní
                        $types = ['', 'General'];
                        $total = 0;
                        if(!empty($notifications)){
                            foreach($notifications as $n){
                                $total += $n->total;
                            }
                        }
                    ?>

                    <!-- Messages-->
                    <li class="dropdown custom-dropdown messages-menu">
                        <a href="#" class="nav-link" data-toggle="dropdown">
                                <i class="icon-message "></i>
                                <?php if($total > 0): ?>
                                    <span class="badge badge-success badge-mini rounded-circle"><?php echo $total; ?></span>
                                <?php endif; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li>
                                <!-- inner menu: contains the actual data -->
                                <ul class="menu pl-2 pr-2">

                                    <?php if($total > 0): ?>
                                        <?php foreach($notifications as $n): ?>
                                            <?php if($n->total == 1): ?>
                                            <!-- start message -->
                                            <li>
                                                <a href="<?php echo base_url('admin/dashboard/notifications/?t=' . $n->type); ?>">
                                                    <h4 style="margin-left: 0px;">
                                                        <?php echo $n->notification->title; ?>
                                                    </h4>
                                                    <p style="margin-left: 0px;"><?php echo substrwords($n->notification->message, 30); ?>.</p>
                                                </a>
                                            </li>
                                            <!-- end message -->
                                            <?php else: ?>
                                            <!-- start message -->
                                            <li>
                                                <a href="<?php echo base_url('admin/dashboard/notifications/?t=' . $n->type); ?>">
                                                    <h4 style="margin-left: 0px;">
                                                        <strong><?php echo $n->total; ?></strong> <?php if($n->total >= 2 && $n->total <= 4){ echo 'notifikace'; }else if($n->total >= 5){ echo 'notifikací'; } ?> z kategorie <strong><?php echo $types[$n->type]; ?></strong>
                                                    </h4>
                                                    <p style="margin-left: 0px;">Klikněte pro přehled notifikací.</p>
                                                </a>
                                            </li>
                                            <!-- end message -->
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    <?php endif; ?>

                                </ul>
                            </li>
                            <li class="footer s-12 p-2 text-center"><a href="/admin/dashboard/notifications/">Zobrazit všechny notifikace</a></li>
                        </ul>
                    </li>
                    <!-- User Account-->
                    <li class="dropdown custom-dropdown user user-menu ">
                        <a href="#" class="nav-link" data-toggle="dropdown">
                            <i class="icon-user"></i>
                        </a>
                        <div class="dropdown-menu p-4 dropdown-menu-right">
                            <div><h5 class="font-weight-light mt-2 mb-1"><strong><?php echoEscapedHtml(gym_users_name()); ?></strong></h5></div>
                            <div class="row box justify-content-between my-4">
                                <div class="col">
                                    <a href="/admin/profile">
                                        <i class="icon-user purple lighten-2 avatar  r-5"></i>
                                        <div class="pt-1">Profil</div>
                                    </a>
                                </div>
                                <div class="col">
                                    <a href="/logout">
                                        <i class="icon-exit_to_app pink lighten-1 avatar  r-5"></i>
                                        <div class="pt-1">Odhlásit se</div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
</div>
</div>