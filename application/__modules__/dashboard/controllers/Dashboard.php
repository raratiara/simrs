<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller
{
	/* Module */
 	private $folder_name			= "dashboard";
 	private $module_name			= "dashboard";
 	private $table_name 			= "";
 	private $primary_key 			= "id";
 	private $model_name				= "dashboard_model";
	/* Navigation */
 	private $parent_menu			= "";
 	private $subparent_menu			= "";
 	private $sub_menu 				= "dashboard";
	/* Label */
 	private $label_parent_modul		= "";
 	private $label_subparent_modul	= "";
 	private $label_modul			= "Dashboard";
 	private $label_list_data		= "Dashboard";
 	private $label_add_data			= "Tambah Data Dashboard";
 	private $label_update_data		= "Edit Data Dashboard";
 	private $label_sukses_disimpan 	= "Data berhasil disimpan";
 	private $label_gagal_disimpan 	= "Data gagal disimpan";
 	private $label_delete_data		= "Hapus Data Dashboard";
 	private $label_sukses_dihapus 	= "Data berhasil dihapus";
 	private $label_gagal_dihapus 	= "Data gagal dihapus";
 	private $label_import_data		= "Import Data Dashboard";
 	private $label_sukses_diimport 	= "Data berhasil diimport";
 	private $label_gagal_diimport 	= "Import data di baris : ";
 	private $label_export_data		= "Export";

   	public function __construct()
	{
      	parent::__construct();
		$this->load->model("login/login_model","auth");
		$this->auth->hassession();

		$this->data['breadcrumb'] = '<li>
								<i class="fa fa-home"></i>
								<a href="'._URL.'">Home</a>
								<i class="fa fa-circle"></i>
							</li>';
		if(!empty($this->label_parent_modul)) $this->data['breadcrumb'] .= '<li><span>'.$this->label_parent_modul.'</span><i class="fa fa-circle"></i></li>';
		if(!empty($this->label_subparent_modul)) $this->data['breadcrumb'] .= '<li><span>'.$this->label_subparent_modul.'</span><i class="fa fa-circle"></i></li>';
		$this->data['breadcrumb'] .= '<li><span>'.$this->label_modul.'</span></li>';

		$menu["parent_menu"] 		= $this->parent_menu;
		$menu["subparent_menu"] 	= $this->subparent_menu;
		$menu["sub_menu"] 			= $this->sub_menu;
      	$this->data["folder_name"]	= $this->folder_name;
		$this->data['check_menu']	= $menu;

		$this->load->model($this->model_name);

		# akses level
		$akses = $this->dashboard_model->user_akses($this->module_name);
		define('_USER_ACCESS_LEVEL_VIEW',$akses["view"]);
		define('_USER_ACCESS_LEVEL_ADD',$akses["add"]);
		define('_USER_ACCESS_LEVEL_UPDATE',$akses["edit"]);
		define('_USER_ACCESS_LEVEL_DELETE',$akses["del"]);
		define('_USER_ACCESS_LEVEL_DETAIL','');
		define('_USER_ACCESS_LEVEL_IMPORT',$akses["import"]);
		define('_USER_ACCESS_LEVEL_EKSPORT',$akses["eksport"]);
   	}

	public function index()
	{
		$this->view();
	}

	public function view()
	{
		$this->data['title'] 		= $this->label_list_data;
		$this->data['js'] 			= $this->folder_name.'/js_view';
		$this->data['sview'] 		= $this->folder_name.'/view';
		$this->data['sfolder'] 		= $this->folder_name;
		$this->data['smodul'] 		= $this->label_modul;
		$this->load->view(_TEMPLATE , $this->data);
	}
}
