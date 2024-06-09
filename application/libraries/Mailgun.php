<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Simple Mailgun library
 * Send emails through the mailgun API service,
 * don't forget to change the credentials.
 * 
 * $email_body = $this->load->view('test/test', $data, TRUE);
 * $att = curl_file_create($path);
 * $this->mailgun::send([
            'from' => "Gymit <no-reply@gymit.cz>",
            'to' => "target@email.cz",
            'subject' => "subject",
            'html' => $email_body,
            'attachment' => $att
        ])
 * 
 */
class Mailgun {

  protected $CI;
  private static $api_key;
  private static $api_base_url;

  public function __construct() {
    $this->CI =& get_instance();
    self::$api_key = "64b81e6af587989020cbb89f5fce25ca-0a4b0c40-c6a28133";
    self::$api_base_url = "https://api.eu.mailgun.net/v3/mg.gymit.cz";
  }

  /**
   * $mail = array(from, to, subject, text/html)
   * IF HTML : $this->load->veiw(path+name, vars);
   */
  public static function send($mail) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, 'api:' . self::$api_key);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_URL, self::$api_base_url . '/messages');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $mail);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
  }

}