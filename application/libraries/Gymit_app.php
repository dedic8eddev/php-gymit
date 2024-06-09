<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Gymit_App{
    
    protected $ci;

    public function __construct(){
        $this->ci =& get_instance();
    }
    
    /**
     * Registrace assetů
     * @param array $assets             pole s názvy souborů (soubory s "admin." v názvu jsou automaticky odděleny, URL jsou také automaticky odděleny)
     * @param string $type              string s typem souborů ( js / css )
     * 
     * Eg.: $this->app->assets( ['jquery.js', 'admin.settings.js', 'http://google.com/adsense.js'], 'js' );
     */
    public function assets( array $assets, string $type)
    {
        if(!empty($assets)){
            foreach($assets as $asset){
                $folder = '';
                $src = '';
                
                // Check if URL or local file and grab folder location for $type
                if( !preg_match('/\b(([\w-]+:\/\/?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|\/)))/', $asset) ){
                    if(strpos($asset, 'admin.') !== false){
                        $parts = explode('.', $asset);
                        if(count($parts) >= 4){
                            if($type == 'js') $folder = config_item('app')['js_folder'] . 'admin/' . $parts[1] . '/';
                            else if($type == 'css') $folder = config_item('app')['css_folder'] . 'admin/' . $parts[1] . '/';
                        }else{
                            if($type == 'js') $folder = config_item('app')['js_folder'] . 'admin/';
                            else if($type == 'css') $folder = config_item('app')['css_folder'] . 'admin/';
                        }
                    }else if(strpos($asset, 'front.') !== false){
                        $parts = explode('.', $asset);
                        if(count($parts) >= 4){
                            if($type == 'js') $folder = config_item('app')['js_folder'] . 'front/' . $parts[1] . '/';
                            else if($type == 'css') $folder = config_item('app')['css_folder'] . 'front/' . $parts[1] . '/';
                        }else{
                            if($type == 'js') $folder = config_item('app')['js_folder'] . 'front/';
                            else if($type == 'css') $folder = config_item('app')['css_folder'] . 'front/';
                        }
                    }else{
                        if($type == 'js') $folder = config_item('app')['js_lib_folder'];
                        else if($type == 'css') $folder = config_item('app')['css_lib_folder'];
                    }
                }

                // Crete the URI
                if(strlen($folder) <= 0) $src = $asset;
                else $src = base_url( $folder . $asset );

                // Add to global array
                if(!in_array($src, $this->ci->appAssets[$type])){
                    $this->ci->appAssets[$type][] = $src;
                }
            }
        }
    }

    /**
     * Načtení assetů
     */
    public function loadAssets(string $type)
    {
        $return = '';
        switch ($type){
            case 'css':
                foreach($this->ci->appAssets[$type] as $asset){
                    $return .= '<link rel="stylesheet" href="'.$asset.'" type="text/css">';
                }
            break;
            case 'js':
                foreach($this->ci->appAssets[$type] as $asset){
                    $return .= '<script src="'.$asset.'"></script>';
                }
            break;
        }

        echo $return;
    }
    public function getMedia($file = NULL,$meta = NULL,$separately=false){
        if($file=='placeholder' or !$file){
            $file = base_url(config_item('app')['img_folder'].'img_placeholder.png');
            $meta = json_encode(['title' => 'portrait placeholder', 'alt' => 'portrait placeholder']);
        } else $file = base_url(config_item('app')['media']['folder'].$file); 
        
        $meta = json_decode($meta);
        $return = [
            'src' => $file,
            'title' => @$meta->title,
            'alt' => @$meta->alt
        ];
        if($separately) return $return;
        return "src='$return[src]' title='$return[title]' alt='$return[alt]'";
    }

    /**
     * Získání dat uživatele
     *
     * @param int $userId id uživatele
     * @return void
     */
    public function getUserData($userId = null){
        $this->ci->load->model('Users_model','usersModel');

        if(is_null($userId)){
            $userId = ( isset($_SESSION["user_id"]) ) ? $_SESSION["user_id"] : FALSE;
        }

        if ($userId) return $this->ci->usersModel->getUserData($userId);
    }

    public function getUserCardId($userId = null){
        $this->ci->load->model('Cards_model','cards');

        if(is_null($userId)){
            $userId = ( isset($_SESSION["user_id"]) ) ? $_SESSION["user_id"] : FALSE;
        }

        if ($userId) return $this->ci->cards->getUserCard($userId);
    }

    public function setHttpReponse( int $headerStatus, string $contentType, $response){
        return $this->ci->output->set_status_header($headerStatus)->set_content_type($contentType)->set_output($response);
    }
}