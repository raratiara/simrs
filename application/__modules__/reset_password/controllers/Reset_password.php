<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reset_password extends CI_Controller { 

	protected $user_tabel = _PREFIX_TABLE.'user';

	public function __construct()
	{
		parent::__construct(); 
	}

	public function index()
	{
		$sId = $this->session->userdata('id');
		if($sId){
			$data = array();
			$data['form'] = false;
			$data['message'] = '';
			$sql1 = "SELECT * FROM ".$this->user_tabel." WHERE user_id = ?";
			$nuser = $this->db->query($sql1, [ $sId ])->num_rows();
			if($nuser > 0 ){
				$nuser = $this->db->query($sql1, [ $sId ])->row();
				$data['form'] = true;
				$data['username'] = $nuser->username;
				$data['email'] = '-*-';
				$data['auth'] = '-*-';
			} else {
				$data['message'] = '<p align="center"><label class="check">Failed !<br/>
				User is not valid.</label></p>';
			}

			$this->load->view('tpl/reset_password',$data);  
		} else {
			redirect('login/');
		}
	}	
}
