<html lang="zxx" class="applicationcache cors no-ie8compat history json postmessage strictmode devicemotion deviceorientation filereader localstorage sessionstorage hashchange cssgradients multiplebgs opacity cssremunit rgba fileinput formattribute placeholder hsla supports fontface generatedcontent cssscrollbar formvalidation textshadow fullscreen filesystem cssanimations backgroundsize borderradius borderimage boxshadow csscolumns csscolumns-width csscolumns-span csscolumns-fill csscolumns-gap csscolumns-rule csscolumns-rulecolor csscolumns-rulestyle csscolumns-rulewidth csscolumns-breakbefore csscolumns-breakafter csscolumns-breakinside flexbox flexboxlegacy no-overflowscrolling cssreflections csstransforms csstransforms3d csstransitions"><head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="assets/img/basic/favicon.ico" type="image/x-icon">
    <title><?php echo config_item('app')['site_title']; ?> - Pozvánka</title>
    <!-- CSS -->
    <?php $this->app->loadAssets('css'); ?>
    <link rel="stylesheet" href="/public/assets/css/libs/animate.css/animate.min.css" type="text/css">
    <link rel="stylesheet" href="/public/assets/css/libs/template/bootstrap.css" type="text/css">
    <link rel="stylesheet" href="/public/assets/css/libs/template/app.css" type="text/css">
    <link rel="stylesheet" href="/public/assets/css/libs/noty.css" type="text/css">
    <link rel="stylesheet" href="/public/assets/css/libs/noty.bs3.css" type="text/css">
    <link rel="stylesheet" href="/public/assets/css/libs/npg.css" type="text/css">
    <link rel="stylesheet" href="/public/assets/css/libs/tabulator.min.css" type="text/css">
    <link rel="stylesheet" href="/public/assets/css/libs/flatpickr.css" type="text/css">
    <style>
        .loader {
            position: fixed;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: #F5F8FA;
            z-index: 9998;
            text-align: center;
        }

        .plane-container {
            position: absolute;
            top: 50%;
            left: 50%;
        }
    </style>
    <!-- Js -->
    <!--
    --- Head Part - Use Jquery anywhere at page.
    --- http://writing.colin-gourlay.com/safely-using-ready-before-including-jquery/
    -->
    <script>(function(w,d,u){w.readyQ=[];w.bindReadyQ=[];function p(x,y){if(x=="ready"){w.bindReadyQ.push(y);}else{w.readyQ.push(x);}};var a={ready:p,bind:p};w.$=w.jQuery=function(f){if(f===d||f===u){return a}else{p(f)}}})(window,document)</script>
<style type="text/css">/* Chart.js */
@-webkit-keyframes chartjs-render-animation{from{opacity:0.99}to{opacity:1}}@keyframes chartjs-render-animation{from{opacity:0.99}to{opacity:1}}.chartjs-render-monitor{-webkit-animation:chartjs-render-animation 0.001s;animation:chartjs-render-animation 0.001s;}</style><style type="text/css">.jqstooltip { position: absolute;left: 0px;top: 0px;visibility: hidden;background: rgb(0, 0, 0) transparent;background-color: rgba(0,0,0,0.6);filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=#99000000, endColorstr=#99000000);-ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr=#99000000, endColorstr=#99000000)";color: white;font: 10px arial, san serif;text-align: left;white-space: nowrap;padding: 5px;border: 1px solid white;box-sizing: content-box;z-index: 10000;}.jqsfield { color: white;font: 10px arial, san serif;text-align: left;}</style></head>
<body class="light loaded">
<!-- Pre loader -->
<div id="loader" class="loader loader-fade">
    <div class="plane-container">
        <div class="preloader-wrapper small active">
            <div class="spinner-layer spinner-blue">
                <div class="circle-clipper left">
                    <div class="circle"></div>
                </div><div class="gap-patch">
                <div class="circle"></div>
            </div><div class="circle-clipper right">
                <div class="circle"></div>
            </div>
            </div>

            <div class="spinner-layer spinner-red">
                <div class="circle-clipper left">
                    <div class="circle"></div>
                </div><div class="gap-patch">
                <div class="circle"></div>
            </div><div class="circle-clipper right">
                <div class="circle"></div>
            </div>
            </div>

            <div class="spinner-layer spinner-yellow">
                <div class="circle-clipper left">
                    <div class="circle"></div>
                </div><div class="gap-patch">
                <div class="circle"></div>
            </div><div class="circle-clipper right">
                <div class="circle"></div>
            </div>
            </div>

            <div class="spinner-layer spinner-green">
                <div class="circle-clipper left">
                    <div class="circle"></div>
                </div><div class="gap-patch">
                <div class="circle"></div>
            </div><div class="circle-clipper right">
                <div class="circle"></div>
            </div>
            </div>
        </div>
    </div>
</div>
<div id="app">
<main>
    <div id="primary" class="p-t-b-100 height-full ">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mx-md-auto">
                    <div class="text-center p-t-b-20">
						<img src="/public/assets/img/logo_gymit_premium.svg" alt="" width="160">
                    </div>
						<?php echo $this->session->flashdata('success'); ?>
						<?php echo $this->session->flashdata('error'); ?>
					<form id="invitationForm">
                        <div class="form-group has-icon"><i class="icon-envelope-o"></i>
                            <input type="text" class="form-control form-control-lg" name="email" placeholder="Emailová adresa" data-kwimpalastatus="alive" value="<?php echo $user->email; ?>" disabled>
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control form-control-lg" name="first_name" placeholder="Křestní jméno" data-kwimpalastatus="alive" value="" required>
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control form-control-lg" name="last_name" placeholder="Příjmení" data-kwimpalastatus="alive" value="" required>
                        </div>
                        <div class="form-group">
                            <input type="password" class="form-control form-control-lg" name="password" placeholder="Heslo" data-kwimpalastatus="alive" data-kwimpalaid="1554725474832-3" required>
                        </div>

                        <hr>

                        <div class="form-group focused" data-children-count="1">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="agreement" name="agreement" required>
                                <label class="form-check-label" for="agreement">
                                    Souhlas se zpracováním osobních údajů.
                                </label>
                            </div>
                        </div>

                        <input type="text" class="hidden" style="display: none;" value="<?php echo $token; ?>" name="token">
                        <input type="submit" class="btn btn-primary btn-lg btn-block" id="submitRegistration" data-ajax="<?php echo $finishRegistration; ?>" value="Dokončit registraci">
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- #primary -->
</main>
</div>
<!--/#app -->
<script src="/public/assets/js/libs/jquery/dist/jquery.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.9.1/underscore-min.js"></script>
<script src="/public/assets/js/libs/jquery.badge.js"></script>
<script src="/public/assets/js/libs/template/app.js"></script>
<script src="/public/assets/js/libs/moment/moment.js"></script>
<script src="/public/assets/js/libs/noty.min.js"></script>
<script src="/public/assets/js/libs/select2.min.js"></script>
<script src="/public/assets/js/libs/select2.cs.js"></script>
<script src="/public/assets/js/libs/npg.js"></script>
<script src="/public/assets/js/admin/admin._main.js"></script>
<script src="/public/assets/js/admin/admin._notifications.js"></script>
<script src="/public/assets/js/libs/flatpickr.js"></script>
<script src="/public/assets/js/libs/flatpickr.cs.js"></script>
<script src="/public/assets/js/libs/flatpickr.cs.js"></script>
<script src="/public/assets/js/front/front.invitation.js"></script>


<!--
--- Footer Part - Use Jquery anywhere at page.
--- http://writing.colin-gourlay.com/safely-using-ready-before-including-jquery/
-->
<script>(function($,d){$.each(readyQ,function(i,f){$(f)});$.each(bindReadyQ,function(i,f){$(d).bind("ready",f)})})(jQuery,document)</script>

<div class="tooltip-inner" id="line-chart-tooltip" style="position: absolute; display: none; opacity: 0.8;"></div></body></html>