<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Gymit_Databases{
    
    protected $ci;

    public function __construct(){
        $this->ci =& get_instance(); // CI instance
        $this->ci->load->dbforge(); // db forge
        $this->ci->load->dbutil(); // db util
    }

    /**
     * Select a database to query from, this overrides the default database for current active record query
     * @param string $db_name the name of the database
     */
    public function init($db_name = FALSE){
        if(!$db_name) $db_name = config_item('api')['default_gym_db'];
        return $this->ci->db->db_select($db_name);
    }

    /**
     * Returns available db name for a new db
     */
    public function get_available_db_name(){
        $existing_dbs = $this->ci->dbutil->list_databases();
        $names = [];

        foreach($existing_dbs as $db){
            if(strpos($db, 'gymit') !== false){
                $names[] = $db; // if our db, add to arr (ignore other dbs to be safe)
            }
        }

        $total = count($names);
        return 'gymit' . ($total + 1);
    }

    /**
     * Create a new DB
     * @param string $name name of the database (usually defaults to gymitX)
     * @param array $settings an array of different settings (TODO)
     */
    public function create_new_database($name, $settings = array()){

        if(!gym_in_group(1)) die('Permission denied');

        $tables = $this->ci->db->list_tables();

        if ($this->ci->dbforge->create_database($name))
        {
                $failed_tables = [];
                foreach($tables as $table){
                    if(in_array($table, config_item('app')['global_tables'])) continue;

                    if($this->ci->db->query('CREATE TABLE '.$name.'.'.$table.' LIKE '.config_item('api')['default_database'].'.'.$table.';')){
                        continue;
                    }else{
                        $failed_tables[] = $table;
                    }
                }

                if(empty($failed_tables)){
                    return TRUE;
                }else{
                    return $failed_tables; // return array of failed tables (best to rerun this)
                }
        }else{
            return FALSE;
        }
    }

    /**
     * Remove an existing DB
     * @param string $name name of the database
     */
    public function delete_database($name){

        if(!gym_in_group(1)) die('Permission denied');

        if(strpos($name, 'gymit') !== false){
            if($name == config_item('api')['default_database']){
                // Cannot remove default DB
                return FALSE;
            }else{
                if ($this->ci->dbforge->drop_database($name))
                {
                    return TRUE;
                }else{
                    return FALSE;
                }
            }
        }else{
            exit("Not permitted.");
        }
    }

}