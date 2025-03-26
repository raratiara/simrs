<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reset extends CI_Controller { 

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
			$data['form'] = false;
			$data['message'] = '';
			
			$sql1 = "SELECT * FROM ".$this->user_tabel." WHERE email = ? AND approvekey = ? AND isaktif = 2 ORDER BY date_insert DESC LIMIT 1"; // cek aktif user
			$nuser = $this->db->query($sql1, [ $get['from'], $get['auth'] ])->num_rows();
			if($nuser > 0 ){
				$nuser = $this->db->query($sql1, [ $get['from'], $get['auth'] ])->row();
				$hours = $this->login_model->timetocurr($nuser->keygen);
				$time_limit = _RESET_ACCOUNT_PASSWORD_EXPIRE;
				if($hours < $time_limit){
					$data['form'] = true;
					$data['username'] = $nuser->username;
					$data['email'] = $get['from'];
					$data['auth'] = $get['auth'];
				} else {
					$data['message'] = '<p align="center"><label class="check">Failed !<br/>
					Your reset password link has been expire.</br>
					Please make new reset request through login page.</label></p>';
				}
			} else {
				$data['message'] = '<p align="center"><label class="check">Failed !<br/>
				Your reset password link is not valid.</label></p>';
			}

			$this->load->view('tpl/reset_password',$data);  
		}
	}
}
