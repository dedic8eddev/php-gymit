<?php defined('BASEPATH') OR exit('No direct script access allowed');

class GPwebpay{
    
    protected $ci;

    public function __construct(){
        $this->ci =& get_instance();
        if (!extension_loaded('openssl')) die ("PHP extension OpenSSL is not loaded.");
    }

    public function createRequest($webPayOrderNum,$internOrderNum,$price,$responseUrl){
		$request = new WebPayRequest ();
		$request->setPrivateKey (getenv('GP_WEBPAY_PRIVATE_KEY'), getenv('GP_WEBPAY_PRIVATE_KEY_PASS'));
        $request->setWebPayUrl (getenv('GP_WEBPAY_URL'));
        $request->setMerchantNumber (getenv('GP_WEBPAY_MERCHANT_NUMBER')); // filled from webpay ui
		$request->setResponseUrl ($responseUrl);
        $request->setOrderInfo ($webPayOrderNum,$internOrderNum,$price);
        return $request->requestUrl();
	}

	public function proccessResponse(){
		$response = new WebPayResponse ();
		$response->setPublicKey (getenv('GP_WEBPAY_PUBLIC_KEY'));
		$response->setResponseParams ($_GET);
		$result = $response->verify ();
		if ($result) return $response; // paid
		else { // fail
            $this->ci->db->update('gpwebpay_log',['prcode'=>$response->responseParams['PRCODE'],'srcode'=>$response->responseParams['SRCODE']],['id'=>$response->responseParams['MERORDERNUM']]);
            return false;
        }
	}

}

class WebPayRequest{
    var $privateKey;
    var $webPayUrl;
    var $responseUrl;
    var $merchantNumber;
    var $webPayOrder;
    var $merchantOrder;
    var $amount;

    public function setPrivateKey ($key, $passphrase){
        //$fp = fopen ($file, "r");
        //$key = fread ($fp, filesize($file));
        //fclose ($fp);
        if (!($this->privateKey = openssl_pkey_get_private ($key, $passphrase))) {
            echo "'$key' is not valid PEM private key (or passphrase is incorrect).";
            die;
        }
    }
    public function setOrderInfo ($webPayOrder, $merchantOrder, $price){
        $this->webPayOrder = $webPayOrder;
        $this->merchantOrder = $merchantOrder;
        $this->amount = $price * 100;
    }
    public function setWebPayUrl ($url){
        $this->webPayUrl = $url;
    }
    public function setResponseUrl ($responseUrl){
        $this->responseUrl = $responseUrl;
    }
    public function setMerchantNumber ($merchantNumber){
        $this->merchantNumber = $merchantNumber;
    }
    public function getParams (){
        $params = array ();
        $params ['MERCHANTNUMBER'] = $this->merchantNumber;
        $params ['OPERATION'] = 'CREATE_ORDER';
        $params ['ORDERNUMBER'] = $this->webPayOrder;
        $params ['AMOUNT'] = $this->amount;
        $params ['CURRENCY'] = 203;
        $params ['DEPOSITFLAG'] = 1;
        $params ['MERORDERNUM'] = $this->merchantOrder;
        //$params ['MD'] = '';
        $params ['URL'] = $this->responseUrl;
        
        $digestText = implode ('|', $params);
        openssl_sign ($digestText, $signature, $this->privateKey);
        $signature = base64_encode ($signature);
        $params ['DIGEST'] = $signature;
        return $params;
    }
    public function requestUrl (){
        $params = $this->getParams ();
        $url = $this->webPayUrl . '?' . http_build_query ($params);
        return $url;
    }
} // WebPayRequest

class WebPayResponse {
    var $publicKey;
    var $responseParams = array ();
    var $digest;
    public function setPublicKey ($key){
        //$fp = fopen($file, "r");
        //$key = fread($fp, filesize ($file));
        //fclose ($fp);
        if (!($this->publicKey = openssl_get_publickey($key))) {
            echo "'$file' is not valid PEM public key (or passphrase is incorrect).";
            die;
        }
    }
    public function setResponseParams ($params){
        $this->responseParams ['OPERATION'] = isset ($params ['OPERATION']) ? $params ['OPERATION'] : '';
        $this->responseParams ['ORDERNUMBER'] = isset ($params ['ORDERNUMBER']) ? $params ['ORDERNUMBER'] : '';
        $this->responseParams ['MERORDERNUM'] = isset ($params ['MERORDERNUM']) ? $params ['MERORDERNUM'] : ''; 
        //$this->responseParams ['MD'] = isset ($params ['MD']) ? $params['MD'] : '';
        $this->responseParams ['PRCODE'] = isset ($params ['PRCODE']) ? $params ['PRCODE'] : '';  
        $this->responseParams ['SRCODE'] = isset ($params ['SRCODE']) ? $params ['SRCODE'] : '';
        $this->responseParams ['RESULTTEXT'] = isset ($params ['RESULTTEXT']) ? $params ['RESULTTEXT'] : '';
        $this->digest = isset ($params ['DIGEST']) ? $params ['DIGEST'] : '';
    }
    public function verify (){
        $data = implode('|', $this->responseParams);
        $digest = base64_decode ($this->digest);
        $ok = openssl_verify ($data, $digest, $this->publicKey);
        return (($ok == 1) && ($this->responseParams ['PRCODE'] == 0) && ($this->responseParams ['SRCODE'] == 0)) ? true : false;
    }
    public function orderWebpay () {return $this->responseParams ['ORDERNUMBER'];}
    public function orderMerchant () {return $this->responseParams ['MERORDERNUM'];}
} // WebPayResponse
