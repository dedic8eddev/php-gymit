<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Base_Controller extends CI_Controller
{
    function __construct()
    {
        parent::__construct();

        // data aktuálně přihlášeného uživatele
        $this->__userData = $this->app->getUserData();
        if($this->__userData) $this->__userData->card = $this->app->getUserCardId(); // Karta

        $this->__settings = [];
        $this->collected_notifications = $this->n->getUsersNotificationsCollected();

        if($this->ion_auth->logged_in()){
            
            if($this->ion_auth->in_group('admin')){
                $this->__settings['admin_url'] = site_url('admin/dashboard');
            }elseif($this->ion_auth->in_group('user')){
                $this->__settings['admin_url'] = site_url('account');
            }else{
                $this->__settings['admin_url'] = '#';
            }
        }

    }
}


class Public_Controller extends Base_Controller
{
    function __construct()
    {
        parent::__construct();

        $site_settings = $this->gyms->getSiteSettings();

        // Check for changes in current_site settings and redirect if not trying to log in!
        if ($site_settings->current_site !== null && !in_array($this->router->fetch_class(), ["login","logout"])) {
            redirect("/p"."/".$site_settings->current_site);
            exit;
        }else if($site_settings->current_site !== null && $this->router->fetch_class() === "login"){
            $ip = $this->input->ip_address();
            if(!in_array($ip, config_item("app")["maintenance_ip_addresses"])){
                // If current client IP is not in config as a permitted maintenance IP, then go to hell
                redirect("/p"."/".$site_settings->current_site);
                exit;
            }
        }

        // ENVIRONMENT
        $this->__appEnv = 'frontend';
        
        // header & footer settings
        foreach ($this->gyms->getGymSettings(['opening_hours','general_info','footer','front_menu_items']) as $k => $v){
            $this->gymSettings[$v['type']]['id']=$v['id'];
            $this->gymSettings[$v['type']]['data']=json_decode($v['data'],true);
        } 	  
        
        // Open status
        $dayNum = date('N');
        $now = new Datetime("now");
        if((in_array($dayNum,range(1,5)))){
            $from = new DateTime($this->gymSettings['opening_hours']['data']['monday']['from']);
            $to = new DateTime($this->gymSettings['opening_hours']['data']['monday']['to']);
        } else {
            $from = new DateTime($this->gymSettings['opening_hours']['data']['saturday']['from']);
            $to = new DateTime($this->gymSettings['opening_hours']['data']['saturday']['to']);            
        }

        if($now >= $from && $now <= $to){
            $this->openStatus['text'] = 'Otevřeno';
            $this->openStatus['class'] = 'open';
        } else {
            $this->openStatus['text'] = 'Zavřeno';
            $this->openStatus['class'] = 'closed';
        }
    }
}

class Override_Controller extends Base_Controller
{
    function __construct()
    {
        parent::__construct();

        $site_settings = $this->gyms->getSiteSettings();
        $this->__appEnv = "frontend";

        /**
         * Do anything else in this override controller honestly
         */
    }
}

class Account_Controller extends Public_Controller
{
    function __construct()
    {
        parent::__construct();

        // Allow CLI access
        if(!isCLI()){
            if(! $this->ion_auth->logged_in()) {
                redirect('login');
            }
        }
    }

    protected static function ajaxResponse(bool $result): void
    {
        $result ? self::ajaxSuccessResponse() : self::ajaxErrorResponse();
    }

    protected static function ajaxSuccessResponse(string $message = ''): void
    {
        echo json_encode(['success' => 'true', 'message' => $message,]);
        exit;
    }

    protected static function ajaxErrorResponse(string $message = ''): void
    {
        echo json_encode(['error' => 'true', 'message' => $message,]);
        exit;
    }
}

abstract class Backend_Controller extends Base_Controller
{
    protected CONST PERMISSION_MESSAGE = 'Pod tímto účtem nemáte dostatečná oprávnění!';

    /** @var Permissions_model */
    public $permissions;

    function __construct()
    {
        parent::__construct();
        $this->__appEnv = 'backend';
        $this->load->model('permissions_model', 'permissions');
        
        // Allow CLI access
        if(!isCLI()){
            if(!$this->ion_auth->logged_in() OR !gym_userid()){
                if(!gym_userid()) redirect('logout');
			    else if($this->router->fetch_class() !== "login") redirect('login');
            }
        }
    }

    /**
     * Methods helps to mark child controller as section
     *
     * @return string
     */
    public abstract function sectionName(): string;

    /**
     * Methods check logged user permissions to read on current (child) controller
     *
     * Call this method at the beginning of a child controller action
     *
     * @uses $this->checkReadPermission();
     */
    protected function checkReadPermission(bool $isAjax = false, ?string $sectionName = null)
    {
        if ($sectionName === null) {
            $sectionName = $this->sectionName();
        }

        if (!$this->permissions->hasReadPermission($sectionName) && !$this->permissions->hasReadPermissionForSectionDataOnly($sectionName)) {
            self::dontHavePermissionMessage($isAjax);
        }
    }

    /**
     * Methods check logged user permissions to create (new record) on current (child) controller
     *
     * Call this method at the beginning of a child controller action
     *
     * @uses $this->checkCreatePermission();
     */
    protected function checkCreatePermission(bool $isAjax = false, ?string $sectionName = null)
    {
        if ($sectionName === null) {
            $sectionName = $this->sectionName();
        }

        if (! $this->permissions->hasCreatePermission($sectionName)) {
            self::dontHavePermissionMessage($isAjax);
        }
    }

    /**
     * Methods check logged user permissions to edit (record) on current (child) controller
     *
     * Call this method at the beginning of a child controller action
     *
     * @uses $this->checkEditPermission();
     */
    protected function checkEditPermission(bool $isAjax = false, ?string $sectionName = null)
    {
        if ($sectionName === null) {
            $sectionName = $this->sectionName();
        }

        if (! $this->permissions->hasEditPermission($sectionName)
            &&
            ! $this->permissions->hasEditAndSendToApprovalPermission($sectionName)
        ) {
            self::dontHavePermissionMessage($isAjax);
        }
    }

    protected function checkEditAndSendToApprovalPermission(bool $isAjax = false)
    {
        if (! $this->permissions->hasEditAndSendToApprovalPermission($this->sectionName())) {
            self::dontHavePermissionMessage($isAjax);
        }
    }

    /**
     * Methods check logged user permissions to delete (record) on current (child) controller
     *
     * Call this method at the beginning of a child controller action
     *
     * @uses $this->checkDeletePermission();
     */
    protected function checkDeletePermission(bool $isAjax = false, ?string $sectionName = null)
    {
        if ($sectionName === null) {
            $sectionName = $this->sectionName();
        }

        if (! $this->permissions->hasDeletePermission($sectionName)) {
            self::dontHavePermissionMessage($isAjax);
        }
    }

    protected static function dontHavePermissionMessage(bool $isAjax = false): void
    {
        if ($isAjax) {
            self::ajaxErrorResponse(['message' => self::PERMISSION_MESSAGE]);
            exit;
        }

        /**
         * Basic behavior of this permission message method is to print message about missing permissions
         * and exit script (to denied access to this section)
         *
         * In case of somebody is logged (in admin) as another user, by default will see just error message and no content
         *
         * This code changes this default behaviour, it will not exit script, just alert error message and shows the page
         * (user/tester will be able to see users select-box and will be able to log as another user)
         */
        if (gym_is_fake() and $isAjax === false) {
            echo '<script>alert("'. self::PERMISSION_MESSAGE .'");</script>';
            return;
        }

        exit(self::PERMISSION_MESSAGE);
    }

    protected static function ajaxResponse(bool $result): void
    {
        $result ? self::ajaxSuccessResponse() : self::ajaxErrorResponse();
    }

    protected static function ajaxSuccessResponse(array $data = []): void
    {
        echo json_encode(array_merge(['success' => 'true'], $data));
    }

    protected static function ajaxErrorResponse(array $data = []): void
    {
        echo json_encode(array_merge(['error' => 'true'],$data));
    }
}