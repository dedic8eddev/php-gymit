<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends Public_Controller
{
    function __construct()
	{
        parent::__construct();
    }

    /**
     * Reset hesla
     */
    public function reset(){
        // All good
        $data['pageTitle'] = 'Reset hesla';
        $data["site_settings"] = $this->gyms->getSiteSettings();
        $data["sendResetLink"] = base_url('login/send_reset_link');

        $this->app->assets(['front.reset.js'], 'js');
        $this->app->assets(['front.account.css'], 'css');

        $this->load->view('frontend/layout/header', $data);
        $this->load->view('frontend/layout/menu');
        $this->load->view('public/reset');
        $this->load->view('frontend/layout/footer');
    }

    /**
     * Reset magic link
     */
    public function reset_password($user_id, $token){

        $token = urldecode($token);

        $user = $this->ion_auth->user($user_id)->row();
        if($user){
            $db_token = $this->db->where('user_id', $user_id)->where('type', 'password_reset')->get('users_tokens')->row();
            if($db_token){

                $time_hour_ago = strtotime('-1 hours');
                if(strtotime($db_token->created_on) < $time_hour_ago){
                    // expired
                    $this->session->set_flashdata('error', 'Odkaz pro reset hesla vypršel, zažádejte si prosím o nový!');
                    redirect('login');
                }

                if($db_token->token == $token){
                    // All good
                    $data["site_settings"] = $this->gyms->getSiteSettings();
                    $data['pageTitle'] = 'Reset hesla';
                    $data["user"] = $user;
                    $data["token"] = $token;
                    $data["finishReset"] = base_url('login/finish_reset');
            
                    $this->app->assets(['front.reset.js'], 'js');
                    $this->app->assets(['front.account.css'], 'css');
            
                    $this->load->view('frontend/layout/header', $data);
                    $this->load->view('frontend/layout/menu');
                    $this->load->view('public/reset_finish');
                    $this->load->view('frontend/layout/footer');

                }else{
                    $this->session->set_flashdata('error', 'Neplatný odkaz pro reset hesla!');
                    redirect('login');
                }
            }else{
                $this->session->set_flashdata('error', 'Neexistující odkaz pro reset hesla!');
                redirect('login');
            }
        }else{
            $this->session->set_flashdata('error', 'Účet u kterého se snažíte změnit heslo již neexistuje!');
            redirect('login');
        }
    }

    public function finish_reset(){
        $token = $this->input->post("token");
        $password = $this->input->post("password");

        $db_token = $this->db->where('type', 'password_reset')->where('token', $token)->get('users_tokens')->row();
        if($db_token){
            $user = $this->ion_auth->user($db_token->user_id)->row();
            if($this->ion_auth->update($user->id, [
                'password' => $password
            ])){
                // Succesfuly updated -> login
                if($this->ion_auth->login($user->email, $password)){
                    $this->db->where('id', $db_token->id)->delete("users_tokens"); // remove the token (invalidate)
                    echo json_encode(["success" => "true"]);
                }else{
                    echo json_encode(["error" => "true"]);
                }
            }else{
                echo json_encode(["error" => "true"]);
            }
        }else{
            echo json_encode(["error" => "true"]);
        }
    }

    /**
     * Send the magic link to the users email
     */
    public function send_reset_link(){
        $this->load->model('users_model', 'users');
        $email = $this->input->post("email");
        if($this->ion_auth->email_check($email)){

            $user = $this->db->where('email', $email)->get("users")->row();
            $existing_reset = $this->db->where('type', 'password_reset')->where('user_id', $user->id)->get('users_tokens')->row();
            $time_hour_ago = strtotime('-1 hours');

            // Prevent sending too many reset links
            if(!$existing_reset){
                if($this->users->resetPassword($email)){
                    // Sent out
                    echo json_encode(["success" => "true"]);
                }
            }else{
                if(strtotime($existing_reset->created_on) >= $time_hour_ago){
                    echo json_encode(["error" => "Již jste si vyžádali reset hesla, zkontrolujte svoji e-mailovou schránku nebo to zkuste později."]);
                }else{
                    // Token is older than an hour (should be deleted with a CRON or someshit, but if thats the case => send the link)
                    if($this->users->resetPassword($email)){
                        // Sent out
                        echo json_encode(["success" => "true"]);
                    }
                }
            }
        }else{
            echo json_encode(["error" => "Neexistující e-mailová adresa, zkontrolujte zadané údaje!"]);
        }
    }

    /**
     * Invitation /W token
     */
    public function invite($user_id, $token){
        $token = urldecode($token);
        $data["site_settings"] = $this->gyms->getSiteSettings();

        $user = $this->ion_auth->user($user_id)->row();
        if($user){
            $db_token = $this->db->where('user_id', $user_id)->where('type', 'invitation')->get('users_tokens')->row();
            if($db_token){
                if($db_token->token == $token){
                    // All good
                    $data["user"] = $user;
                    $data["token"] = $token;
                    $data["finishRegistration"] = base_url('login/finish_registration');
                    $this->load->view('public/invite', $data);
                }else{
                    $this->session->set_flashdata('error', 'Neplatná pozvánka!');
                    redirect('login');
                }
            }else{
                $this->session->set_flashdata('error', 'Neexistující pozvánka!');
                redirect('login');
            }
        }else{
            $this->session->set_flashdata('error', 'Účet ke kterému byla vytvořena pozvánka již neexistuje!');
            redirect('login');
        }
    }

    public function finish_registration(){
        $data["site_settings"] = $this->gyms->getSiteSettings();

        $token = $this->input->post("token");
        $first_name = $this->input->post("first_name");
        $last_name = $this->input->post("last_name");
        $password = $this->input->post("password");

        $db_token = $this->db->where('type', 'invitation')->where('token', $token)->get('users_tokens')->row();
        if($db_token){
            $user = $this->ion_auth->user($db_token->user_id)->row();
            if($this->db->insert('users_data', [
                'user_id' => $user->id,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $user->email,
                'country' => 'CZ',
                'gdpr' => 1
            ]) && $this->ion_auth->update($user->id, ['password' => $password]) ){
                // Succesfuly updated and created user_data object -> login
                if($this->ion_auth->login($user->email, $password)){
                    $this->db->where('id', $db_token->id)->delete("users_tokens"); // remove the token (invalidate)
                    echo json_encode(["success" => "true"]);
                }else{
                    echo json_encode(["error" => "true"]);
                }
            }else{
                echo json_encode(["error" => "true"]);
            }
        }else{
            echo json_encode(["error" => "true"]);
        }
    }

	public function index()	{

        // kontrola validačních pravidel
        if($this->form_validation->run('validation_login')){

			if($this->ion_auth->login($this->input->post('email'), $this->input->post('password'), $this->input->post('remember'))) {

                // Setup session for auth purposes
                $u = $this->ion_auth->user()->row();
                setLoginSession($u->id, $this->ion_auth->get_users_groups()->result()[0]->id);

                $this->session->set_flashdata('success', 'Byli jste přihlášeni jako '.$this->input->post('email').'');

                $this->redirect_after_login();
			}else{
				$this->session->set_flashdata('error', 'Zadané jméno nebo heslo nejsou správné!');
				redirect(current_url());
			}

		}else{
			// ověření zda je uživatel přihlášený
            if($this->ion_auth->logged_in()){
				$this->redirect_after_login();
            }else{
                // načtení pohledů
                $data['pageTitle'] = 'Přihlášení';

                $data["site_settings"] = $this->gyms->getSiteSettings();

                $this->app->assets(['front.account.main.js'], 'js');
                $this->app->assets(['front.account.css'], 'css');

                $this->load->view('frontend/layout/header', $data);
                $this->load->view('frontend/layout/menu');
                $this->load->view('public/login');
                $this->load->view('frontend/layout/footer');
            }
		}
	}

    private function redirect_after_login(): void
    {
        foreach ($this->ion_auth->get_users_groups()->result() as $group) {
            redirect(base_url($group->location));
        }
	}
}