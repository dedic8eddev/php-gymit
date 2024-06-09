<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Depot_model extends CI_Model
{
	public function __construct(){
        parent::__construct();
        $this->gymdb->init(get_db());
    }

    // Destock
    public function takeDepotItemStock($data = NULL, $type = NULL){
        $p = (!is_null($data)) ? $data : $_POST;

        // data
        $depot_id = $p['depot_id'];
        $item_id = $p['item_id'];
        $quantity = $p['quantity'];
        $note = $p['note'];

        // compose log array
        $log = [
            'gymId' => current_gym_id(),
            'depotId' => $depot_id,
            'itemId' => $item_id,
            'amount' => $quantity,
            'note' => (strlen($note) > 0) ? $note : '',
            'direction' => (!is_null($type)) ? $type : 'from',
            'loggedBy' => gym_userid()
        ];

        if($type == "sale"){
            $log["salePrice"] = $data['sale_price'];
        }
        
        if($this->API->depots->log_depot_event($log)){
            $stock = $this->db->where('item_id', $item_id)->where('depot_id', $depot_id)->get('depots_stocks')->row();
            if($this->db->where('item_id', $item_id)->where('depot_id', $depot_id)->update('depots_stocks', ['stock' => ($stock->stock - $quantity)])){
                return TRUE;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    public function getStatistic(){
        $g = $_GET;

        $from = $g["from"];
        $to = $g["to"];
        $item_id = $g["item_id"];

        // Pagination and filtering
        $page = (isset($g['page'])) ? $g['page'] : null;
        $limit = (isset($g['size'])) ? $g['size'] : null;
        $sorter = (isset($g['sorters'])) ? $g['sorters'] : false;
        $filter = (isset($g['filters'])) ? $g['filters'] : false;

        $logs = $this->API->depots->get_depot_logs([
            'itemId' => $item_id,
            'from' => $from,
            'to' => $to,
            'direction' => 'sale',
            'limit' => $limit,
            'offset' => $page
        ]);

        if($logs){

            $return = ["data" => [], "last_page" => round($logs->total / $limit)];
            foreach($logs->data as $log){
                $item = $this->db->where("id", $log->itemId)->get("depot_items")->row();
                $log->item_name = $item->name;
                $log->unit = $item->unit;
                $log->item_name = $item->name;

                $return["data"][] = $log;
            }

            return $return;
        }else{
            return FALSE;
        }

    }

    public function getInventory(){
        $g = $_GET;

        $day = $g["day"];
        $depot = isset($g["depot_id"]) ? $g["depot_id"] : FALSE;
        $logs = FALSE;

        // Get logs if history
        if($day < date('Y-m-d')){
            $logs = $this->API->depots->get_depot_logs([
                'depotId' => $depot,
                'from' => $day,
                'to' => date("c")
            ]);
        }

        $stocks = [];
        if($depot){
            $stocks = $this->db->where("depot_id", $depot)->get("depots_stocks")->result_array();
        }else{
            $depots = $this->getAllDepots();
            $depot_ids = [];
            foreach($depots as $depot){ $depot_ids[] = $depot->id; }
            $stocks = $this->db->where_in("depot_id", $depot_ids)->get("depots_stocks")->result_array();
        }

        if(!empty($stocks)){

            $products = [];
            foreach($stocks as $stock){
                $depot = $this->db->where("id", $stock["depot_id"])->get("depots")->row_array();
                $item = $this->db->where('id' ,$stock["item_id"])->get('depot_items')->row_array();
                $item["stock"] = $stock["stock"];
                $item["reserved"] = $stock["reserved"];
                $item["depot_name"] = $depot["name"];
                $item["depot_id"] = $stock["depot_id"];

                /*
                $item["reserved"] = 0;
                if($stocks = $this->getDepotItemStocks($item["id"])){
                    foreach($stocks as $s){
                        $item["stock"] += ($s->stock - $s->reserved);
                        $item["reserved"] += $s->reserved;
                    }
                }*/

                if( $logs ){
                    foreach($logs as $log){
                        if($log->itemId == $stock["item_id"]){
                            switch ($log->direction) {
                                case 
                                    'new': 
                                        $item["stock"] -= $log->amount;
                                    break;
                                case 
                                    'from': 
                                        $item["stock"] += $log->amount;
                                    break;
                                case 
                                    'to': 
                                        $item["stock"] -= $log->amount;
                                    break;
                                case 
                                    'sale': 
                                        $item["stock"] -= $log->amount;
                                    break;
                                case 
                                    'reservation': 
                                        $item["reserved"] -= $log->amount;
                                    break;
                            }
                        }
                    }
                }

                $products[] = $item;
            }

            return $products;
        }else{
            return FALSE;
        }
    }

    public function getInvoiceHistory () {
        $g = $_GET;
        $reply = [];

        // Pagination and filtering
        $page = (isset($g['page'])) ? $g['page'] : null;
        $limit = (isset($g['size'])) ? $g['size'] : null;
        $sorter = (isset($g['sorters'])) ? $g['sorters'] : false;
        $filter = (isset($g['filters'])) ? $g['filters'] : false;

        $this->gymdb->init(get_db());
        $this->db->select('*')->from('depot_invoices');

        if($sorter){
            foreach($sorter as $s){
                $order_field = $s['field'];
                $direction = $s['dir'];

                $this->db->order_by('depot_invoices.'.$order_field, $direction);
            }
        }
        if($filter){
            foreach($filter as $f){
				$fieldname = 'depot_invoices.'.$f["field"];
				$this->db->like($fieldname, $f['value']);
            }
        }

        $countRes = $this->db->count_all_results(null,FALSE);

        if($page != NULL && $limit != NULL){
            $offset = 0;
            if($page > 1){
                $offset = ($page - 1) * $limit;
            }
            $this->db->limit($limit, $offset);
        }

        $this->db->order_by("created_on", "desc");
        $result = $this->db->get()->result();

        if($result){
            foreach($result as $invoice){
                // created_by names
                $user = $this->db->where("user_id", $invoice->created_by)->get("users_data")->row();
                if($user) $invoice->created_by_name = $user->first_name . " " . $user->last_name;
                else $invoice->created_by_name = '--';
            }
        }

        $reply["data"] = $result;
        if($limit != NULL) $reply['last_page'] = ceil( $countRes / $limit );
        return $reply;
    }

    public function stockInvoiceItems(){
        $p = $_POST;

        $note = $p['note'];
        $invoice_id = $p['invoice'];
        $invoice_name = $p['invoice_name'];
        $items = $p['items'];
        $stock_errors = 0;

        $save_invoice = $this->db->insert("depot_invoices", [
            "invoice_number" => $invoice_id,
            "invoice_name" => $invoice_name,
            "note" => $note,
            "items" => json_encode($items),
            "created_by" => gym_userid()
        ]);

        if ($save_invoice) {
            foreach($items as $item){

                if(isset($item["new_product"]) && !isset($item["id"])){
                    // completely new depot item
                    if($item["id"] = $this->add([
                        'name' => $item['name'],
                        'unit' => $item['unit'],
                        'sale_price' => $item['sale_price'],
                        'sale_price_vat' => $item['sale_price_vat'],
                        'vat_value' => $item['vat_value']
                    ])){
    
                        // added, time to log a new stockup
                        foreach($item as $param => $value){
                            if(strpos($param, 'depotid_') !== FALSE){
                                $depot_id = explode('_', $param)[1];
                                if($value <= 0) continue; // skip empty values
    
                                if( $this->moveDepotItemStock([
                                    'movement_type' => 1,
                                    'to_depot_id' => $depot_id,
                                    'item_id' => $item["id"],
                                    'buy_price' => $item['buyPrice'],
                                    'quantity' => $value,
                                    'note' => $note,
                                    'invoice_id' => $invoice_id,
                                    'invoice_name' => $invoice_name
                                ]) ){
                                    continue;
                                }else{
                                    $stock_errors++;
                                }
                            }
                        }
    
                    }else{
                        return FALSE;
                    }
                }else{
                    // existing depot item, just log stockup
                    foreach($item as $param => $value){
                        if(strpos($param, 'depotid_') !== FALSE){
                            $depot_id = explode('_', $param)[1];
                            if($value <= 0) continue; // skip empty values
    
                            if( $this->moveDepotItemStock([
                                'movement_type' => 1,
                                'to_depot_id' => $depot_id,
                                'item_id' => $item['id'],
                                'buy_price' => $item['buyPrice'],
                                'quantity' => $value,
                                'note' => $note,
                                'invoice_id' => $invoice_id
                            ]) ){
                                continue;
                            }else{
                                $stock_errors++;
                            }
                        }
                    }
                }
    
            }
    
            if($stock_errors <= 0){
                return TRUE;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }

    }

    // Reserve an item stock
    public function reserveDepotItemStock($data = NULL){
        $p = (!is_null($data)) ? $data : $_POST;

        // data
        $depot_id = $p['depot_id'];
        $item_id = $p['item_id'];
        $quantity = $p['quantity'];
        $note = $p['note'];

        // compose log array
        $log = [
            'gymId' => current_gym_id(),
            'depotId' => $depot_id,
            'itemId' => $item_id,
            'amount' => $quantity,
            'note' => (strlen($note) > 0) ? $note : '',
            'direction' => 'reservation',
            'loggedBy' => gym_userid()
        ];

        $stock = $this->db->where('item_id', $item_id)->where('depot_id', $depot_id)->get('depots_stocks')->row();

        if($stock->stock >= $quantity){
            if($this->API->depots->log_depot_event($log)){
                if($this->db->where('item_id', $item_id)->where('depot_id', $depot_id)->update('depots_stocks', ['reserved' => ($stock->reserved + $quantity)])){
                    return TRUE;
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }
        }
    }
    // Release a stock reservation
    public function releaseDepotItemStock($data = NULL){
        $p = (!is_null($data)) ? $data : $_POST;

        // data
        $depot_id = $p['depot_id'];
        $item_id = $p['item_id'];
        $quantity = $p['quantity'];
        $note = $p['note'];

        // compose log array
        $log = [
            'gymId' => current_gym_id(),
            'depotId' => $depot_id,
            'itemId' => $item_id,
            'amount' => $quantity,
            'note' => (strlen($note) > 0) ? $note : '',
            'direction' => 'release',
            'loggedBy' => gym_userid()
        ];

        $stock = $this->db->where('item_id', $item_id)->where('depot_id', $depot_id)->get('depots_stocks')->row();
        if($stock->reserved >= $quantity){
            if($this->API->depots->log_depot_event($log)){
                if($this->db->where('item_id', $item_id)->where('depot_id', $depot_id)->update('depots_stocks', ['reserved' => ($stock->reserved - $quantity)])){
                    return TRUE;
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    // Move a stock
    public function moveDepotItemStock($data = FALSE){

        if(!$data) $p = $_POST;
        else $p = $data;

        $type = $p['movement_type'];

        // New stock
        if($type == 1){

            // data
            $target_stock = $p['to_depot_id'];
            $item_id = $p['item_id'];
            $buy_price = $p['buy_price'];
            $quantity = $p['quantity'];
            $note = $p['note'];
            $invoice_id = isset($p['invoice_id']) ? $p['invoice_id'] : FALSE;
            $invoice_name = isset($p['invoice_name']) ? $p['invoice_name'] : FALSE;

            // compose log array
            $log = [
                'gymId' => current_gym_id(),
                'depotId' => $target_stock,
                'itemId' => $item_id,
                'amount' => $quantity,
                'buyPrice' => $buy_price,
                'note' => (strlen($note) > 0) ? $note : '',
                'direction' => 'new',
                'loggedBy' => gym_userid()
            ];

            // add invoice ID if supplied
            if($invoice_id) $log["invoiceId"] = $invoice_id;
            if($invoice_name) $log['invoiceName'] = $invoice_name;

            // log it in Mongo
            if($this->API->depots->log_depot_event($log)){
                $stock = $this->db->where('item_id', $item_id)->where('depot_id', $target_stock)->get('depots_stocks')->row();
                if($stock){
                    if($this->db->where('item_id', $item_id)->where('depot_id', $target_stock)->update('depots_stocks', ['stock' => ($stock->stock + $quantity)])){
                        return TRUE;
                    }else{
                        return FALSE;
                    }
                }else{
                    if( $this->db->insert('depots_stocks', ['item_id' => $item_id, 'depot_id' => $target_stock, 'stock' => $quantity]) ){
                        return TRUE;
                    }else{
                        return FALSE;
                    }
                }
            }
        
        // Moving stock between depots
        }else if($type == 2){

            // data
            $origin_stock = $p['from_depot_id'];
            $target_stock = $p['to_depot_id'];
            $item_id = $p['item_id'];
            $quantity = $p['quantity'];
            $note = $p['note'];

            // compose log array, two logs for this transaction => from old depot => to new depot
            $logs = [[
                'gymId' => current_gym_id(),
                'depotId' => $origin_stock,
                'itemId' => $item_id,
                'amount' => $quantity,
                'note' => (strlen($note) > 0) ? $note : '',
                'direction' => 'from',
                'loggedBy' => gym_userid()
            ],[
                'gymId' => current_gym_id(),
                'depotId' => $target_stock,
                'itemId' => $item_id,
                'amount' => $quantity,
                'note' => (strlen($note) > 0) ? $note : '',
                'direction' => 'to',
                'loggedBy' => gym_userid()
            ]];

            // log it in Mongo and return
            if($this->API->depots->log_depot_event($logs[0]) && $this->API->depots->log_depot_event($logs[1])){
                $old_stock = $this->db->where('item_id', $item_id)->where('depot_id', $origin_stock)->get('depots_stocks')->row();
                $new_stock = $this->db->where('item_id', $item_id)->where('depot_id', $target_stock)->get('depots_stocks')->row();
                if($new_stock){
                    $update_old = $this->db->where('item_id', $item_id)->where('depot_id', $origin_stock)->update('depots_stocks', ['stock' => ($old_stock->stock - $quantity)]);
                    $update_new = $this->db->where('item_id', $item_id)->where('depot_id', $target_stock)->update('depots_stocks', ['stock' => ($new_stock->stock + $quantity)]);
                    if($update_old && $update_new){
                        return TRUE;
                    }else{
                        return FALSE;
                    }
                }else{
                    $update_old = $this->db->where('item_id', $item_id)->where('depot_id', $origin_stock)->update('depots_stocks', ['stock' => ($old_stock->stock - $quantity)]);
                    if( $update_old && $this->db->insert('depots_stocks', ['item_id' => $item_id, 'depot_id' => $target_stock, 'stock' => $quantity]) ){
                        return TRUE;
                    }else{
                        return FALSE;
                    }
                }
            }

        }else if($type == 3){
            return $this->reserveDepotItemStock($p);
        }else if($type == 4){
            return $this->releaseDepotItemStock($p);
        }

    }

    public function getDepotItemLogs($item_id, $params = []){
        $logs = $this->API->depots->get_log_by_item_id($item_id, $params);
        if($logs){
            foreach($logs->data as $log){
                $depot = $this->db->where('id', $log->depotId)->get('depots')->row();
                $item = $this->db->where('id', $log->itemId)->get('depot_items')->row();
                $log->depot_name = (isset($depot->name)) ? $depot->name : '--';
                $log->unit = (isset($item->unit)) ? $item->unit : '';
            }

            return $logs;
        }else{
            return FALSE;
        }
    }
    
    public function getDepotItemStocks($item_id, $depot_id = NULL){
        $this->db->select('*, depots.name, depot_items.unit')->from('depots_stocks');
        $this->db->join('depots', 'depots.id = depots_stocks.depot_id');
        $this->db->join('depot_items', 'depot_items.id = depots_stocks.item_id');
        $this->db->where('depots_stocks.item_id', $item_id);

        if($depot_id){
            $this->db->where('depots.id', $depot_id);
        }

        $results = $this->db->get()->result();
        if($results){
            return $results;
        }else{
            return FALSE;
        }
    }
    
    public function getAllDepots(){
        $depots = $this->db->get('depots')->result();
        return $depots;
    }

    /** @todo in get_all_depots_ajax */
    public function getAllDepotsBySection(?string $section = null)
    {
        return $this->db
            ->where('for_section', $section)
            ->get('depots')
            ->result()
            ;
    }

    // Získej položky z vybraného skladu
    public function getDepotItemsByDepotId(){
        $depot_id = $_POST["depot_id"];
        $reply = [];

        $stocks = $this->db->where("depot_id", $depot_id)->get("depots_stocks")->result();
        if(!empty($stocks)){
            foreach($stocks as $stock){
                $depot_item = $this->db->where("id", $stock->item_id)->get("depot_items")->row();
                $depot_item->stock = ($stock->stock - $stock->reserved);

                $reply[] = $depot_item;
            }
        }

        return $reply;
    }

    /**
     * Získej všechny skladové položky
     */
    public function getAllDepotItems($s2 = false) : array
    {		
        $g = $_GET;
        $reply = [];

        // Pagination and filtering
        $page = (isset($g['page'])) ? $g['page'] : null;
        $limit = (isset($g['size'])) ? $g['size'] : null;
        $sorter = (isset($g['sorters'])) ? $g['sorters'] : false;
        $filter = (isset($g['filters'])) ? $g['filters'] : false;

        $this->gymdb->init(get_db());
        $this->db->select('*')->from('depot_items');

        if($sorter){
            foreach($sorter as $s){
                $order_field = $s['field'];
                $direction = $s['dir'];

                $this->db->order_by('depot_items.'.$order_field, $direction);
            }
        }
        if($filter){
            foreach($filter as $f){
				$fieldname = 'depot_items.'.$f["field"];
				$this->db->like($fieldname, $f['value']);
            }
        }

        $countRes = $this->db->count_all_results(null,FALSE);

        if($page != NULL && $limit != NULL){
            $offset = 0;
            if($page > 1){
                $offset = ($page - 1) * $limit;
            }
            $this->db->limit($limit, $offset);
        }

        if($s2){
            $result = $this->db->get()->result_array();

            // Stock values
            $reply = [];
            if($result){
                foreach($result as $item){
                    $item["stock"] = 0;
                    $item["reserved"] = 0;
                    if($stocks = $this->getDepotItemStocks($item["id"])){
                        foreach($stocks as $s){
                            $item["stock"] += ($s->stock - $s->reserved);
                            $item["reserved"] += $s->reserved;
                        }
                    }
                    $item["logs"] = $this->getDepotItemLogs($item["id"]);
                    $reply[] = $item;
                }
            }

            return $reply;
        }else{
            $result = $this->db->get()->result();

            // Stock values
            if($result){
                foreach($result as $item){
                    $item->stock = 0;
                    $item->reserved = 0;
                    if($stocks = $this->getDepotItemStocks($item->id)){
                        foreach($stocks as $s){
                            $item->stock += ($s->stock - $s->reserved);
                            $item->reserved += $s->reserved;
                        }
                    }
                    $item->logs = $this->getDepotItemLogs($item->id);
                }
            }

            $reply["data"] = $result;
            if($limit != NULL) $reply['last_page'] = ceil( $countRes / $limit );
            return $reply;
        }

    }

    public function searchDepotItems($q, $only_stock = FALSE){
        $this->gymdb->init(get_db());
        $this->db->like('name', $q, 'both');

        if($only_stock){
            // Only items that are on stock (quantity bigger than 0)
            $this->db->where('quantity >', 0);
        }

        $items = $this->db->get("depot_items")->result_array();

        if($items){
            return $items;
        }else{
            return FALSE;
        }
    }

    /**
     * Získat data skladové položky
     *
     * @param integer $id
     * @return void
     */
    public function getDepotItem( int $id, bool $simple = false )
    {
        $return = array();

        $this->db->trans_start();

        $this->gymdb->init(get_db());
            $query = $this->db->where('id',$id)->get('depot_items');

        $this->db->trans_complete();

        if($this->db->trans_status()){
            $return = $query->row_array();
        }

        if(!empty($return)){
            if(!$simple) $return['logs'] = $this->getDepotItemLogs($id); // no logs for simple
            $return['stock'] = 0;
            if($stocks = $this->getDepotItemStocks($id)){
                foreach($stocks as $s){
                    $return['stock'] += ($s->stock - $s->reserved);
                }
                $return['stocks'] = $stocks;
            }
        }

        return $return;
    }

    /**
     * Editace skladové položky
     *
     * @param integer $id id skladové položky
     * @param array $data data skladové položky
     * @return void
     */
    public function edit( int $id , array $data )
    {
        $this->db->trans_start();

        // vyloučit quantity
        unset($data['quantity']);

        $this->gymdb->init(get_db());
        $this->db->update('depot_items',$data,['id' => $id]);

        $this->db->trans_complete();

        if($this->db->trans_status()){
            return TRUE;
        }else{
            return FALSE;
        }
    }

    /**
     * Přidat novou položku
     *
     * @param array $data
     * @return void
     */
    public function add( array $data)
    {

        $depots = $this->db->get('depots')->result();

        $this->db->trans_start();

        // ulož položku
        $this->gymdb->init(get_db());
            $this->db->insert('depot_items',$data);
            $item_id = $this->db->insert_id();
            
            // add 0 depot stocks
            foreach($depots as $depot){
                $this->db->insert('depots_stocks', ['item_id' => $item_id, 'depot_id' => $depot->id, 'stock' => 0]);
            }

        $this->db->trans_complete();

        if($this->db->trans_status()){
            return $item_id;
        }else{
            return FALSE;
        }
    }

    public function removeItem($item_id){
        $this->gymdb->init(get_db());

        $delete = $this->db->where('id', $item_id)->delete('depot_items');
        if($delete){
            $this->API->depots->delete_depot_item_logs($item_id);
            return TRUE;
        }else{
            return FALSE;
        }
    }

}