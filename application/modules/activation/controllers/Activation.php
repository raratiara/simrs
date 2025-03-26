<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Activation extends CI_Controller { 

	protected $user_tabel = _PREFIX_TABLE.'user';

	public function __construct()
	{
		parent::__construct(); 
		
		$this->load->model("login/login_model");  
	}

	public function index()
	{
		// xss filter = true
		$get = $this->input->get(null, true);

		if(!empty($get)){
			$data = array();
			
			$sql1 = "SELECT * FROM ".$this->user_tabel." WHERE email = ? AND approvekey = ? AND isaktif = 1 ORDER BY date_insert DESC LIMIT 1"; // cek aktif user
			$nuser = $this->db->query($sql1, [ $get['from'], $get['auth'] ])->num_rows();
			if($nuser > 0 ){
				$nuser = $this->db->query($sql1, [ $get['from'], $get['auth'] ])->row();
				$hours = $this->login_model->timetocurr($nuser->keygen);
				$time_limit = _NEW_ACCOUNT_EXPIRE;
				if($hours < $time_limit){
					$data['message'] = '<p align="center"><label class="check">Thank You !<br/>
					Your <?=_ACCOUNT_TITLE;?> account has been activated.</br>
					You may now log in and begin using it.</label></p>';
					$key = random_string('alnum', _ACCOUNT_KEYLENGTH); // for disable the old key
					$udata = array (
						'isaktif' 		=> '2',
						'approvekey' 	=> $key
						);
					$this->db->update($this->user_tabel, $udata, array('user_id' => $nuser->user_id));
				} else {
					$data['message'] = '<p align="center"><label class="check">Failed !<br/>
					Your verification link has been expire.</br>
					Please re-create your account on login page.</label></p>';
				}
			} else {
				$data['message'] = '<p align="center"><label class="check">Failed !<br/>
				Your verification link is not valid.</br>
				Please create an account through login page.</label></p>';
			}

			$this->load->view('tpl/activation',$data);  
		}
	}
}
