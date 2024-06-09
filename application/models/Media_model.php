<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Media_model extends CI_Model{
    
    public function __construct(){
        parent::__construct();

        $this->load->library('upload');
        $this->load->library('image_lib');
        
        $this->media_folder = config_item('app')['media']['folder'];
        $this->thumbs_folder = config_item('app')['media']['thumbs'];
        $this->thumbs = [
            'width' => 122,
            'height' => 91
        ];
    }

    public function getThumbSettings(){
        return $this->thumbs;
    }

    /**
     * Získej všechny media
     *
     * @return array
     */
    public function getAllMedia() : array
    {
        $return = [];

        $this->db->trans_start();

        $query = $this->db->select('media.*')
                         ->from('media')
                         ->order_by('media.created_date','desc')
                         ->get(); 

        $this->db->trans_complete();
        
        if($this->db->trans_status()){
            $return = $query->result_array();                         

            foreach($return as $key => $item){
                $return[$key]['type'] = $this->_getFileType($this->media_folder.$item['file']);
                
                // Ověření vyplnění všech položek
                $metaTags = json_decode($return[$key]['meta_tags'], true);
                if(empty($metaTags['title']) or empty($metaTags['alt'])){
                    $return[$key]['unfilled']=1;
                } else $return[$key]['unfilled']=0; // všechny položky jsou vyplněné
                
            }
        }

        return $return;
    }

    /**
     * Získej soubor
     *
     * @param integer $id
     * @return array
     */
    public function getMedia(int $id ) : array
    {
        $return = [];

        $this->db->trans_start();

        $query = $this->db->select('media.*')
                          ->from('media')
                          ->where('media.id',$id)
                          ->get();

        $this->db->trans_complete();
        
        if($this->db->trans_status()){
            $return = $query->row_array();
            $fileDimensions = list($fileWidth, $fileHeight) = getimagesize($this->media_folder.$return['file']);

            $return['meta_tags'] = json_decode($return['meta_tags']);
            $return['date_created_wh_time'] = nice_date($return['created_date'],'Y-m-d');
            $return['size'] = number_format(filesize($this->media_folder.$return['file'])/pow(1024, 2),2).' MB';
            $return['width'] = $fileWidth;
            $return['height'] = $fileHeight;
            $return['type'] = $this->_getFileType($this->media_folder.$return['file']);
            $return['mime'] = mime_content_type($this->media_folder.$return['file']);
            $return['path'] = base_url($this->media_folder.$return['file']);
        }

        return $return;
    }

    /**
     * Nahraj soubor
     *
     * @return boolean
     */
    public function upload()
    {

        if(!is_dir($this->media_folder)){
            if(!mkdir($this->media_folder)){
                return FALSE;
            }
        }

        if(!is_dir($this->thumbs_folder)){
            if(!mkdir($this->thumbs_folder)){
                return FALSE;
            }
        }

        $configUpload['upload_path']                  = $this->media_folder;
        $configUpload['allowed_types']                = array('dib','webp','jpeg','svgz','gif','jpg','ico','png','svg','tif','xbm','bmp','jfif','pjpeg','pjp','tiff','ogv','avi','mp4','m4v','mpeg','wmv','mov','ogm','webm','asx','mpg');
        $configUpload['max_size']                     = 25600; //25MB
        $configUpload['max_filename_increment']       = 999999;

        $this->upload->initialize($configUpload);

        if($this->upload->do_upload('file')){

            $uploadData = $this->upload->data();
            $fileData = [
                'name' => $uploadData['raw_name'],
                'file' => $uploadData['file_name'],
                'file_ext' => $uploadData['file_ext'],
                'created_user' => gym_userid()
            ];
            
            // create thumb
            $configImageLib['image_library']    = 'gd2';
            $configImageLib['source_image']     = $this->media_folder.$uploadData['file_name'];
            $configImageLib['new_image']        = $this->thumbs_folder;
            $configImageLib['create_thumb']     = TRUE;
            $configImageLib['maintain_ratio']   = TRUE;
            $configImageLib['thumb_marker']     = '';
            $configImageLib['width']            = $this->thumbs['width'];
            $configImageLib['height']           = $this->thumbs['height'];

            $this->image_lib->initialize($configImageLib);
            $this->image_lib->resize();

            // save file
            if( ($id = $this->_saveFile($fileData)) !== FALSE){

                $return = [
                    'id' => $id,
                    'file' => $uploadData['file_name'],
                    'name' => $uploadData['raw_name'],
                    'short_name' => ellipsize($uploadData['raw_name'],10),
                    'thumb' => base_url($this->thumbs_folder.$uploadData['file_name']),
                    'type' => $this->_getFileType($this->media_folder.$uploadData['file_name'])
                ];

                return $return;

            }else{
                return false;
            }

        }else{
            return false;
        }

    }

    /**
     * Aktualizace
     *
     * @param integer $id
     * @param array $data
     * @return boolean
     */
    public function update( int $id, array $data ) : bool
    {

        $this->db->trans_start();
        $this->db->update('media', ['meta_tags' => json_encode($data['meta_tags'])],['id' => $id]);
        $this->db->trans_complete();

        if($this->db->trans_status()){
            return true;
        }else{
            return false;
        }
        
    }

    /**
     * Smaž souor
     *
     * @param integer $id
     * @return boolean
     */
    public function delete( int $id ) : bool
    {
        $mediaData = $this->getMedia($id);
        $this->db->trans_start();

        $this->db->delete('media', ['id' => $id]);

        $this->db->trans_complete();

        if($this->db->trans_status()){
            // remove file
            unlink($this->media_folder.$mediaData['file']);
            if($mediaData['type'] == 'image'){
                unlink($this->thumbs_folder.$mediaData['file']);
            }

            return true;
        }else{
            return false;
        }
    }

    /**
     * Ulož soubor do DB
     *
     * @param array $data
     * @return mixed
     */
    protected function _saveFile( array $data )
    {
        $this->db->trans_start();

        $this->db->insert('media',$data);
        $id = $this->db->insert_id();

        $this->db->trans_complete();
        
        if($this->db->trans_status()){
            return $id;
        }else{
            return false;
        }
    }

    protected function _getFileType( string $file ) : string
    {
        if(file_exists($file)){
            $mime = mime_content_type($file);
            if(strstr($mime, "video/")){
                return 'video';
            }else if(strstr($mime, "image/")){
                return 'image';
            }
        }else{
            return 'image';
        }
    }
}