
<a href="#" data-toggle="push-menu" class="paper-nav-toggle left ml-2 fixed">
        <i></i>
    </a>
</div>

<div id="fullPageLoader">
    <div class="loader">
        <div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
    </div>
</div>

<!--/#app -->
<script>const UACL='<?php echo base64_encode(json_encode(['SECTION_NAME' => get_instance()->sectionName(),'ACL' => $this->permissions->getAllUserPermissions(gym_userid())])); ?>';</script>
<?php $this->app->loadAssets('js'); ?>
<!--
--- Footer Part - Use Jquery anywhere at page.
--- http://writing.colin-gourlay.com/safely-using-ready-before-including-jquery/
-->
<script>(function($,d){$.each(readyQ,function(i,f){$(f)});$.each(bindReadyQ,function(i,f){$(d).bind("ready",f)})})(jQuery,document)</script>
</body>
</html>