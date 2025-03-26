<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller { 
 
   	public function __construct() {
      	parent::__construct();
		$this->load->model("login/login_model","auth");
		$this->auth->hassession();

		$this->data['breadcrumb'] = '<li>
								<i class="fa fa-home"></i>
								<a href="'._URL.'">Home</a>
								<i class="fa fa-circle"></i>
							</li>
							<li><span>Welcome</span></li>';

		$menu["parent_menu"] 		= "";
		$menu["subparent_menu"] 	= "";
		$menu["sub_menu"] 			= ""; 
		$this->data['check_menu']	= $menu;
   	} 

	public function index()
	{ 
			$this->view();
	}

	public function view()
	{
		$this->data['title'] 		= "Selamat Datang";  
		$this->data['sview'] 		= 'welcome'; 
		$this->load->view(_TEMPLATE , $this->data);
	}
 
}
