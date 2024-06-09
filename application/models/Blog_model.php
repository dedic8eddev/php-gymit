<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Blog_model extends CI_Model
{
    public function __construct(){
        parent::__construct();
        $this->gymdb->init(get_db());

        // dir for media
        if(!is_dir(config_item('app')['blog_images'])){
            mkdir(config_item('app')['blog_images']);
        }
    }

    public function savePost(array $data){

        $f = $_FILES;

        // merge date & time
        $data['publish_from']=$data['publish_date_from']." ".$data['publish_time_from'];
        unset($data['publish_date_from'],$data['publish_time_from']);
        $data['publish_to']=$data['publish_date_to']." ".$data['publish_time_to'];
        unset($data['publish_date_to'],$data['publish_time_to']); 

        $data['author_id']=gym_userid();
        $gyms=$data['gym_id']; unset($data['gym_id']);

        // image upload
        if(isset($f['image'])){
            $img = $f['image'];

            $file_name = time().'_'.$img["name"];
            $tmp = $img["tmp_name"];

            if(move_uploaded_file($tmp, config_item('app')['blog_images'] . $file_name)){
                $data['image'] = $file_name;
            }
        }

        $this->db->trans_start();
        if (@$data['id']>0){ // update or insert?
            $id=$data['id']; unset($data['id']);
            $this->db->update('blog',$data,['id' => $id]);
        } else {
            $this->db->insert('blog',$data);
            $id=$this->db->insert_id();
        }

        // gyms
        $gyms = array_map(function($gym_id) use ($id) {
            return [ 'blog_id' => $id, 'gym_id' => $gym_id ];
        }, $gyms);

        $this->db->delete('blog_gyms',['blog_id' => $id]);
        if(!empty($gyms)){
            $this->db->insert_batch('blog_gyms',$gyms);
        }

        $this->db->trans_complete();

        if($this->db->trans_status()) return true;
        else return false;
    }

    public function removePost(int $id){
        $this->db->trans_start();
        $this->db->delete('blog_gyms',['blog_id' => $id]);
        $this->db->delete('blog',['id' => $id]);
        $this->db->trans_complete();

        if($this->db->trans_status()) return true;
        else return false;
    }
    
    public function pinPost(int $id, bool $pin){
        $this->db->trans_start();
        $pin = $pin == 0 ? null : $pin;
        $this->db->update('blog',['pin' => $pin],['id' => $id]);
        $this->db->trans_complete();

        if($this->db->trans_status()) return true;
        else return false;
    }          

    public function getPost(int $id){
        $return = $this->db->select('blog.*, media.file photo_src, media.meta_tags photo_meta')->from('blog')->join('media','blog.image = media.id','LEFT')->where('blog.id',$id)->get()->row_array();
        $qGyms = $this->db->select('gym_id')->where('blog_id',$id)->get('blog_gyms');
        $return['gyms'] = array_map (function($value){
            return $value['gym_id'];
        }, $qGyms->result_array());   
        return $return;
    }

    public function getBlogPostsByGym($gym_id = FALSE, $pinned = FALSE){
        if(!$gym_id) $gym_id = current_gym_id();

        $g = $_GET;
        $reply = [];

        // Pagination and filtering
        $page = (isset($g['page'])) ? $g['page'] : null;
        $limit = (isset($g['size'])) ? $g['size'] : null;
        $sorter = (isset($g['sorters'])) ? $g['sorters'] : false;
        $filter = (isset($g['filters'])) ? $g['filters'] : false;

        $this->db->select('blog.*,blog_gyms.gym_id,concat(users_data.first_name," ",users_data.last_name) as author_name, media.file photo_src, media.meta_tags photo_meta')->from('blog')
                ->join('blog_gyms', 'blog_gyms.blog_id = blog.id','left')
                ->join('users_data', 'users_data.user_id = blog.author_id','left')
                ->join('media','blog.image = media.id','LEFT');

        $this->db->where('blog_gyms.gym_id', $gym_id);

        if($pinned){
            $this->db->where('blog.pin > 0');
            $this->db->limit(4);
        }

        if($sorter){
            foreach($sorter as $s){
                $order_field = $s['field'];
                $direction = $s['dir'];

                if($order_field == "gym_name"){
                    $this->db->order_by('blog_gyms', $direction);
                } else if($order_field == 'author_name'){
                    $this->db->order_by('users_data.last_name', $direction);
                    $this->db->order_by('users_data.first_name', $direction);
                } else{
                    $this->db->order_by('blog.'.$order_field, $direction);
                }
            }
        }
        if($filter){
            foreach($filter as $f){
                if($f['field'] == 'gym_name'){
                    $this->db->like('lessons.name', $f['value']);
                } else if($f['field'] == 'author_name'){
                    $this->db->like('concat(users_data.first_name," ",users_data.last_name)', $f['value']);
                } else{
                    $fieldname = 'blog.'.$f["field"];
                    $this->db->like($fieldname, $f['value']);
                }
            }
        }

        $countRes = $this->db->count_all_results(null,FALSE);

        if($page != NULL && $limit != NULL){
            $offset = $page > 1 ? $offset = ($page - 1) * $limit : 0;
            $this->db->limit($limit, $offset);
        }

        $result = $this->db->get()->result();
        $reply["data"] = $result;
        if($limit != NULL) $reply['last_page'] = ceil( $countRes / $limit );
        return $reply; 
    }

    public function getAllBlogPosts($s2 = false){
        $g = $_GET;
        $reply = [];

        // Pagination and filtering
        $page = (isset($g['page'])) ? $g['page'] : null;
        $limit = (isset($g['size'])) ? $g['size'] : null;
        $sorter = (isset($g['sorters'])) ? $g['sorters'] : false;
        $filter = (isset($g['filters'])) ? $g['filters'] : false;

        $this->db->select('blog.*,blog_gyms.gym_id,concat(users_data.last_name," ",users_data.first_name) as author_name')->from('blog')
                ->join('blog_gyms', 'blog_gyms.id = blog.id','left')
                ->join('users_data', 'users_data.user_id = blog.author_id','left');

        if($sorter){
            foreach($sorter as $s){
                $order_field = $s['field'];
                $direction = $s['dir'];

                if($order_field == "gym_name"){
                    $this->db->order_by('blog_gyms', $direction);
                } else if($order_field == 'author_name'){
                    $this->db->order_by('users_data.last_name', $direction);
                    $this->db->order_by('users_data.first_name', $direction);
                } else{
                    $this->db->order_by('blog.'.$order_field, $direction);
                }
            }
        }
        if($filter){
            foreach($filter as $f){
                if($f['field'] == 'gym_name'){
                    $this->db->like('lessons.name', $f['value']);
                } else if($f['field'] == 'author_name'){
                    $this->db->like('concat(users_data.last_name," ",users_data.first_name)', $f['value']);
                } else{
                    $fieldname = 'blog.'.$f["field"];
                    $this->db->like($fieldname, $f['value']);
                }
            }
        }

        $countRes = $this->db->count_all_results(null,FALSE);

        if($page != NULL && $limit != NULL){
            $offset = $page > 1 ? $offset = ($page - 1) * $limit : 0;
            $this->db->limit($limit, $offset);
        }

        if($s2){
            $result = $this->db->get()->result_array();
            return $result;
        }else{
            $result = $this->db->get()->result();
            $reply["data"] = $result;
            if($limit != NULL) $reply['last_page'] = ceil( $countRes / $limit );
            return $reply;
        }        
    }
}