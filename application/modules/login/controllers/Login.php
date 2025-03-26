<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller { 

	protected $user_tabel = _PREFIX_TABLE.'user';

	public function __construct()
	{
		parent::__construct(); 		
		$this->load->model("login_model");
	}

	public function index()
	{
		$session = $this->session->userdata('id');
        if (!empty($session)){
			redirect('/');
		}
		$this->load->view('tpl/login');  
	}

	// For user/password auth
	public function auth()
	{
		// tidak perlu dilakukan validasi karena sudah di lakukan di client side

		// xss filter = true
		$post = $this->input->post(null, true);
		
		if(!empty($post)){
			// jangan khawatir. sudah di escape
			// tidak perlu pake model. KIS = keep it simple
			$sql1 = "SELECT * FROM ".$this->user_tabel." WHERE username = ? AND isaktif = 1 ORDER BY date_insert DESC LIMIT 1"; // cek new user
			$nuser = $this->db->query($sql1, [ $post['username'] ])->num_rows();

			$sql2 = "SELECT * FROM ".$this->user_tabel." WHERE username = ? AND isaktif = 2 ORDER BY date_insert DESC LIMIT 1"; // cek aktif user
			$auser = $this->db->query($sql2, [ $post['username'] ])->num_rows();
			if($nuser > 0 && $auser < 1){
				$nuser = $this->db->query($sql1, [ $post['username'] ])->row();
				$hours = $this->login_model->timetocurr($nuser->keygen);
				$time_limit = _NEW_ACCOUNT_EXPIRE;
				if($hours !== false && $hours <= $time_limit){
					echo '<button class="close" data-close="alert"></button>
								<span><center>Account found.<br/>Please check your email for activate.</center></span>';
				} else {
					/*
					echo '<button class="close" data-close="alert"></button>
								<span><center>Account not found.<br/>Please create an account.</center></span>';
					*/
					echo '<button class="close" data-close="alert"></button>
								<span><center>Account not found.<br/>Please contact Administrator.</center></span>';
				}
			} else {
				if($auser < 1){
					/*
					echo '<button class="close" data-close="alert"></button>
								<span><center>Account not found.<br/>Please create an account.</center></span>';
					*/
					echo '<button class="close" data-close="alert"></button>
								<span><center>Account not found.<br/>Please contact Administrator.</center></span>';
				} else {
					$sql = "SELECT * FROM ".$this->user_tabel." WHERE username = ? AND passwd = ? AND isaktif = 2 ORDER BY date_insert DESC LIMIT 1";
					// username & password harus case sensitive untuk keamanan. jadi tidak perlu di strtolowercase
					$user = $this->db->query($sql, [ $post['username'], md5($post['userpasswd']) ])->row();
					//var_dump($user); exit;
					if ($user) 
					{
						// keep it simple
						$this->session->set_userdata([
							'id' 		=> $user->user_id,
							'isaktif' 	=> TRUE,
							'username' 	=> $user->username,
							'name' 		=> $user->name,
							'role' 		=> $user->id_groups,
							'branch' 	=> $user->id_branch,
							'worker' 	=> $user->id_karyawan,
							'base_menu' => $user->base_menu,
							'ppFile' 	=> $user->ppFile
						]);
						
						$updating = array();
						if(isset($post['remember']) && $post['remember'] == 1){
							$key = random_string('alnum', 64);
							$cookie_time = _COOKIES_EXPIRE;
							set_cookie(_COOKIES_NAME, $key, 3600*24*$cookie_time); // set expired 30 hari kedepan
							// simpan key di database
							$updating['cookie'] = $key;
						}
						// Updating last login & cookie if set
						$updating['last_update_login'] = date('Y-m-d H:i:s');
						$this->db->update($this->user_tabel, $updating, array('user_id' => $user->user_id));
						
						echo 'Welcome';
					}
					else {
						echo '<button class="close" data-close="alert"></button>
								<span>Wrong Username Or Password</span>';
					}
				}
			}
		}
	} 

	// For register new account
	public function check_user()
	{
		$output = true;
		// xss filter = true
		$get = $this->input->get(null, true);

		if(!empty($get)){
			$sql1 = "SELECT * FROM ".$this->user_tabel." WHERE username = ? AND isaktif = 1 ORDER BY date_insert DESC LIMIT 1"; // cek new user
			$nuser = $this->db->query($sql1, [ $get['username'] ])->num_rows();
			if($nuser > 0 ){
				$nuser = $this->db->query($sql1, [ $get['username'] ])->row();
				$hours = $this->login_model->timetocurr($nuser->keygen);
				$time_limit = _NEW_ACCOUNT_EXPIRE;
				if($hours < $time_limit){
					$output = false;
				}
			}

			$sql2 = "SELECT * FROM ".$this->user_tabel." WHERE username = ? AND isaktif = 2 ORDER BY date_insert DESC LIMIT 1"; // cek aktif user
			$auser = $this->db->query($sql2, [ $get['username'] ])->num_rows();
			if($auser > 0){
				$output = false;
			}
		}

		echo json_encode($output);
	}   	

	// For register new account
	public function check_email_use()
	{
		$output = true;
		// xss filter = true
		$get = $this->input->get(null, true);

		if(!empty($get)){
			$sql1 = "SELECT * FROM ".$this->user_tabel." WHERE email = ? AND isaktif = 1 ORDER BY date_insert DESC LIMIT 1"; // cek aktif user
			$nuser = $this->db->query($sql1, [ $get['email'] ])->num_rows();
			if($nuser > 0 ){
				$nuser = $this->db->query($sql1, [ $get['email'] ])->row();
				$hours = $this->login_model->timetocurr($nuser->keygen);
				$time_limit = _NEW_ACCOUNT_EXPIRE;
				if($hours < $time_limit){
					$output = false;
				}
			}

			$sql1 = "SELECT * FROM ".$this->user_tabel." WHERE email = ? AND isaktif = 2 ORDER BY date_insert DESC LIMIT 1"; // cek aktif user
			$nuser = $this->db->query($sql1, [ $get['email'] ])->num_rows();
			if($nuser > 0 ){
				$output = false;
			}
		}

		echo json_encode($output);
	}

	// For forgot account password
	public function check_email()
	{
		$output = false;
		// xss filter = true
		$get = $this->input->get(null, true);

		if(!empty($get)){
			$sql1 = "SELECT * FROM ".$this->user_tabel." WHERE email = ? AND isaktif = 2 ORDER BY date_insert DESC LIMIT 1"; // cek aktif user
			$nuser = $this->db->query($sql1, [ $get['email'] ])->num_rows();
			if($nuser > 0 ){
				$output = true;
			}
		}

		echo json_encode($output);
	}

	// For sending account activation link
	public function activation()
	{
		$output = false;

		// xss filter = true
		$post = $this->input->post(null, true);
		
		if(!empty($post)){
			$key = random_string('alnum', _ACCOUNT_KEYLENGTH);
			
			$mail = array();
			$mail['subject'] = 'Account Confirmation';
			$mail['preheader'] = '';
			$mail['from_name'] = _MAIL_SYSTEM_NAME;
			$mail['from_email'] = _MAIL_SYSTEM_EMAIL;
			$mail['to_name'] = $post['fullname'];
			$mail['to_email'] = $post['email'];
			$mail['template'] = 'account-confirmation';
			$mail['key'] = $key;

			$output = $this->sendmail($mail);
			if($output){
				$now = date('Y-m-d H:i:s');
				$data = array (
					'name' 			=> $post['fullname'],
					'email' 		=> $post['email'],
					'username' 		=> $post['username'],
					'passwd' 		=> md5($post['password']),
					'approvekey' 	=> $key,
					'keygen' 		=> $now,
					'insert_by' 	=> 'websys',
					'date_insert'	=> $now
					);
				$this->db->insert($this->user_tabel, $data); // Inserting new account
			}
		}

		echo json_encode($output);
	}

	// For sending reset password link
	public function renew_password()
	{
		$output = false;

		// xss filter = true
		$post = $this->input->post(null, true);
		
		if(!empty($post)){
			$sql1 = "SELECT user_id, name, email FROM ".$this->user_tabel." WHERE email = ? AND isaktif = 2 ORDER BY date_insert DESC LIMIT 1"; // cek aktif user
			$nuser = $this->db->query($sql1, [ $post['email'] ])->row();

			$key = random_string('alnum', _ACCOUNT_KEYLENGTH);
			
			$mail = array();
			$mail['subject'] = 'Password Reset';
			$mail['preheader'] = '';
			$mail['from_name'] = _MAIL_SYSTEM_NAME;
			$mail['from_email'] = _MAIL_SYSTEM_EMAIL;
			$mail['to_name'] = $nuser->name;
			$mail['to_email'] = $nuser->email;
			$mail['template'] = 'password-reset';
			$mail['key'] = $key;

			$output = $this->sendmail($mail);
			if($output){
				$data = array (
					'approvekey' 	=> $key,
					'keygen' 		=> date('Y-m-d H:i:s')
					);
				$this->db->update($this->user_tabel, $data, array('user_id' => $nuser->user_id));
			}
		}

		echo json_encode($output);
	}

	// For account password reset
	public function reset_password()
	{
		$output = false;

		// xss filter = true
		$post = $this->input->post(null, true);
		
		if(!empty($post)){
			if($post['email'] == '-*-' && $post['auth'] == '-*-'){
				$sId = $this->session->userdata('id');
				$sql1 = "SELECT * FROM ".$this->user_tabel." WHERE user_id = ?";
				$nuser = $this->db->query($sql1, [ $sId ])->num_rows();
				if($nuser > 0 ){
					$output = true;
					$data = array (
						'passwd' 		=> md5($post['password'])
						);
					$this->db->update($this->user_tabel, $data, array('user_id' => $sId));
					delete_cookie(_COOKIES_NAME);
					$this->session->unset_userdata(['id','isaktif','username','nama','role','branch','worker','base_menu','ppFile']);
				}
			} else {
				$sql1 = "SELECT * FROM ".$this->user_tabel." WHERE email = ? AND approvekey = ? AND isaktif = 2 ORDER BY date_insert DESC LIMIT 1"; // cek aktif user
				$nuser = $this->db->query($sql1, [ $post['email'], $post['auth'] ])->num_rows();
				if($nuser > 0 ){
					$output = true;
					$nuser = $this->db->query($sql1, [ $post['email'], $post['auth'] ])->row();
					$key = random_string('alnum', _ACCOUNT_KEYLENGTH); // for disable the old key
					$data = array (
						'passwd' 		=> md5($post['password']),
						'approvekey' 	=> $key
						);
					$this->db->update($this->user_tabel, $data, array('user_id' => $nuser->user_id));
				}
			}
		}

		echo json_encode($output);
	}

	// For sending email
	private function sendmail($mail)
	{
		//Load email library 
		$this->load->library('email');

		$data = array();
		$data['preheader'] = $mail['preheader'];
		$data['corp'] = _COMPANY_NAME;
		$data['account_title'] = _ACCOUNT_TITLE;
		$data['link_site'] = _URL;
		$data['link_logo'] = _ASSET_LOGO_FRONT;
		if($mail['template'] == 'account-confirmation'){
			$data['link_confirm'] = _URL.'activation?from='.$mail['to_email'].'&auth='.$mail['key'];
			$data['hour_confirm_expire'] = _NEW_ACCOUNT_EXPIRE;
		}
		if($mail['template'] == 'password-reset'){
			$data['link_reset'] = _URL.'reset?from='.$mail['to_email'].'&auth='.$mail['key'];
			$data['hour_reset_expire'] = _RESET_ACCOUNT_PASSWORD_EXPIRE;
		}

		$message = $this->load->view(_TEMPLATE_EMAIL.$mail['template'],$data,TRUE); // load email message using view template

		$this->email->from($mail['from_email'], $mail['from_name']); 
		$this->email->to($mail['to_email'], $mail['to_name']);
		$this->email->subject($mail['subject']); 
		$this->email->message($message); 
	   
		 //Send mail 
		 if($this->email->send()) {
			return true; 
		 } else {
			return false; 
			//show_error($this->email->print_debugger());
		 }
	}

	// For check if session is still exist, accessing with ajax
	public function hassession()
	{
		$output = false;

		// xss filter = true
		$post = $this->input->post(null, true);	// formality value
		if(!empty($post)){
			// cek session
			$session = $this->session->userdata('id');
			if (!empty($session)){
				$output = true;
			}
		}

		echo json_encode($output);
	}
	
	// For logout
	public function logout()
	{
		delete_cookie(_COOKIES_NAME);
		$this->session->unset_userdata(['id','isaktif','username','nama','role','branch','worker','base_menu','ppFile']);
		redirect('login/');
	}
}
