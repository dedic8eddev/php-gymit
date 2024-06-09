<?php defined('BASEPATH') OR exit('No direct script access allowed');

class EETApp_api{
    
    protected $ci;

    public function __construct(){
        $this->ci =& get_instance();
	}
	
    /**
     * Vytvoření spojení do EETApp API
     */	
	public function connect(){
		$client = new SimpleJsonRpcClient(config_item('app')['eetapp']['api']);
		try {
			$result = $client->{'system.login'}([
				'username' => config_item('app')['eetapp']['username'],
				'password' => config_item('app')['eetapp']['password']
			]);
			$client->setAuthToken($result->token);
			return $client;
		} catch (Exception $e) {
			print_r($e->getMessage()); // incorrect username or password
			return false;
		}
	}

    /**
     * Vytvoření účtenky
     */
	public function newReceipt(array $data){
		$client = $this->connect();
		$receipt['data'] = [
			'device' => $data['device'], // ID pokladny
			'payment' => $data['payment'], // 1 => hotove, 2 => kartou
			'reference' => $data['reference'], // cislo objednavky eshopu
			'priceWithVat' => TRUE, // ceny jsou vcetne dane => cena bez DPH bude dopocitana
			'items' => $data['items']
			/*[  // polozky uctenky
				['title' => 'Polozka 1', 'amount' => 1, 'dph' => 21, 'price' => 200, 'discount' => 0],
				['title' => 'Polozka 2', 'amount' => 10, 'dph' => 21, 'price' => 10, 'discount' => 0],
			]*/
		];

		try {
			$result = $client->{'receipt.save'}($receipt);
			//var_dump($result);
			return TRUE;
		}
		catch (Exception $e) {
			print_r($e->getMessage());
			return FALSE;
		}
	}

	/**
     * Otevření pokladny
     */
	public function openCheckout(array $data){
		$client = $this->connect();
		try {
			$result = $client->{'device.open'}(['id' => $data['checkout_id'], 'amount' => $data['amount'], 'notice' => $data['note']]);
			return TRUE;
		}
		catch (Exception $e) {
			//print_r($e->getMessage());
			return FALSE;
		}		
	}

	/**
     * Uzavření pokladny
     */
	public function closeCheckout(array $data){
		$client = $this->connect();
		try {
			$result = $client->{'device.close'}(['id' => $data['checkout_id'], 'amount' => $data['amount'], 'notice' => $data['note']]);
			return TRUE;
		}
		catch (Exception $e) {
			//print_r($e->getMessage());
			return FALSE;
		}			
	}
	
    /**
     * Vytvoření / editace pokladny
     */
	public function setCheckout(array $data){
		$client = $this->connect();
		$deviceData = [
			'id' => $data['checkout_id'], // pro editaci existujici
			'title' => $data['name'], // Nazev
			
			// nastaveni uctenky
			'header' => 'Company name, s.r.o.', // Nadpis
			'address' => 'Adresa 1, Město ASD', // Podnadpis
			'address_font' => 1, // Písmo podnadpisu (1 => standardní, 2 => malé)
			'footer1' => NULL, // Patička I
			'footer1_align' => 3, // Zarovnání I (1 => vlevo, 2 => vpravo, 3 => na střed}
			'footer1_font' => 1, // Písmo I (1 => standardní, 2 => malé)
			'footer2' => NULL, // Patička II
			'footer2_align' => 3, // Zarovnání II (1 => vlevo, 2 => vpravo, 3 => na střed}
			'footer2_font' => 1, // Písmo II (1 => standardní, 2 => malé)
			// nastaveni tiskarny
			'mode' => 0, // Režim (1 => aktivní 0 => pasivní)
			'connector' => NULL, // Connector ID
			'type' => 1, // Typ (1 => účtenková, 2 => klasická)
			'ip' => NULL, // IP adresa tiskárny (v aktivním režimu)
			'port' => 9100, // Port
			'fontA' => 42, // Font A [znaku na radek]
			'fontB' => 60, // Font B [znaku na radek]
			'printer_width' => 512, // Šířka obrázku [bodu na radek]
			'printer_image_command' => 1, // Příkaz pro obrázky (1 => ESC *, 2 => GS v0)
			'cashdrawer' => '27,112,0,50,250', // Pokladní zásuvka (ESC POS prikaz k otevreni pokladni zasuvky)
			// EET
			'eet_prod' => 0,  // EET prostředí (0 => Testovací, 1 => Ostré)
			'eet_dic_popl' => 'CZ00000019', // DIČ poplatníka
			'eet_dic_poverujiciho' => NULL, // DIČ pověřujícího poplatníka
			'eet_id_provoz' => 8, // Označení provozovny
			'eet_id_pokl' => 48, // Označení pokladního zařízení
			'eet_cert_base64' => NULL, // Podpisový certifikát ve formatu .PK12 zakodovany pres Base64 // base64_encode(file_get_contents('certificate.p12'))
			'eet_pass' => NULL, // Heslo k podpisovemu certifikatu
		];

		try {
			$device = $client->{'device.save'}(['data' => $deviceData]);
			return $device->id;	
		}
		catch (Exception $e) {
			print_r($e->getMessage());
			return FALSE;
		}		

	}
}

class SimpleJsonRpcClient
{
    private $id;
    protected $endpoint;
    private $authToken;
    public function __construct($endpointUrl) {
        $this->endpoint  = $endpointUrl;
        $this->id = 0;
    }
    public function setAuthToken($authToken)
    {
        $this->authToken = $authToken;
    }
    protected function _requestFactory($method, $args) {
        $request = new stdClass;
        $request->jsonrpc = '2.0';
        $request->method = $method;
        $request->params = $args;
        $request->id = $this->id++;
        return json_encode($request);
    }
    protected function _curlFactory($data) {
        $options = array(
            CURLOPT_FRESH_CONNECT => false,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => $data,
        );
        if ($this->authToken) {
            $options[CURLOPT_HTTPHEADER] = ['X-Auth-Token: '.$this->authToken];
        }
        $curl = curl_init($this->endpoint);
        curl_setopt_array($curl, $options);
        return $curl;
    }
    public function __call($method, $args) {
        $request = $this->_requestFactory($method, (object) $args[0]);
        $curl    = $this->_curlFactory(json_encode($request));
        $raw     = curl_exec($curl);
        $return  = json_decode($raw);
        curl_close($curl);
        if(isset($return->error)) {
            throw new Exception($return->error->message, $return->error->code);
        }
        return $return->result;
    }
}