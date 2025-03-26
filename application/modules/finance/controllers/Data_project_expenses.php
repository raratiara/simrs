<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Data_project_expenses extends MY_Controller
{
	/* Costs */
 	const  LABELMODULE				= "finance_data_project_expenses"; // identify menu
 	const  LABELMASTER				= "Project Expenses";
 	const  LABELFOLDER				= "finance"; // module folder
 	const  LABELPATH				= "data_project_expenses"; // controller file (lowercase)
 	const  LABELNAVSEG1				= ""; // adjusted 1st sub parent segment
 	const  LABELSUBPARENTSEG1		= ""; // 
 	const  LABELNAVSEG2				= ""; // adjusted 2nd sub parent segment
 	const  LABELSUBPARENTSEG2		= ""; // 
	
	/* View */
	public $icon 					= 'fa-database';
	public $tabel_header 			= ["ID","No CA","No Project","PIC","Tgl CA","Total CA","Tgl Closing","Total Closing","Status"];
	
	/* Export */
	public $colnames 				= ["ID","No CA","No Project","PIC","Tgl CA","Total CA","Tgl Closing","Total Closing","Status"];
	public $colfields 				= ["id","ca","project","pic","dca","wca","dclose","wclose","status"];

	/* Form Field Asset */
	public function form_field_asset()
	{
		$field = [];
		$field['txtca'] 			= $this->self_model->return_build_txt('','ca','ca');
		$oProject 					= $this->db->select('id,project,title')->where("id_status IS NOT NULL AND id_status <= '3'")->get(_PREFIX_TABLE."data_project")->result();
		$field['seloProject'] 		= $this->self_model->return_build_select2me($oProject,'','','','id_project','id_project','','','id','project','-','title');
		$oPic 						= $this->db->select('id,name')->where("id_status='1'")->get(_PREFIX_TABLE."data_karyawan")->result();
		$field['seloPic'] 			= $this->self_model->return_build_select2me($oPic,'','','','id_pic','id_pic','','','id','name');
		//$field['txtdateca'] 		= $this->self_model->return_build_txtdate('','date_ca','date_ca');
		$field['txtdateca'] 		= $this->self_model->return_build_txt('','date_ca','date_ca','','','readonly');
		$field['txttotalca'] 		= $this->self_model->return_build_txt('0','total_ca','total_ca','','','readonly');
		//$field['txtdateclose'] 		= $this->self_model->return_build_txtdate('','date_close','date_close');
		$field['txtdateclose'] 		= $this->self_model->return_build_txt('','date_close','date_close','','','readonly');
		$field['txttotalclose'] 	= $this->self_model->return_build_txt('0','total_close','total_close','','','readonly');
		$oStatus 					= $this->db->select('id,description')->order_by('idx_seq', 'ASC')->where("active='1'")->get(_PREFIX_TABLE."option_project_status")->result();
		$field['seloStatus'] 		= $this->self_model->return_build_select2me($oStatus,'','','','id_status','id_status','','','id','description');
		
		return $field;
	}

	//========================== Considering Already Fixed =======================//
 	/* Construct */
	public function __construct() {
        parent::__construct();
		# akses level
		$akses = $this->self_model->user_akses($this->module_name);
		define('_USER_ACCESS_LEVEL_VIEW',$akses["view"]);
		define('_USER_ACCESS_LEVEL_ADD',$akses["add"]);
		define('_USER_ACCESS_LEVEL_UPDATE',$akses["edit"]);
		define('_USER_ACCESS_LEVEL_DELETE',$akses["del"]);
		define('_USER_ACCESS_LEVEL_DETAIL',$akses["detail"]);
		//define('_USER_ACCESS_LEVEL_IMPORT',$akses["import"]);
		define('_USER_ACCESS_LEVEL_IMPORT',0);
		//define('_USER_ACCESS_LEVEL_EKSPORT',$akses["eksport"]);
		define('_USER_ACCESS_LEVEL_EKSPORT',0);
    }

	/* Module */
 	public $folder_name				= self::LABELFOLDER."/".self::LABELPATH; // module path
 	public $module_name				= self::LABELMODULE;
 	public $model_name				= self::LABELPATH."_model";

	/* Navigation */
 	public $parent_menu				= self::LABELFOLDER;
 	public $subparent_menu			= self::LABELNAVSEG1;
 	public $subparentitem_menu		= self::LABELNAVSEG2;
 	public $sub_menu 				= self::LABELMODULE;

	/* Label */
 	public $label_parent_modul		= self::LABELFOLDER;
 	public $label_subparent_modul	= self::LABELSUBPARENTSEG1;
 	public $label_subparentitem_modul	= self::LABELSUBPARENTSEG2;
 	public $label_modul				= "Data ".self::LABELMASTER;
 	public $label_list_data			= "Daftar Data ".self::LABELMASTER;
 	public $label_add_data			= "Tambah Data ".self::LABELMASTER;
 	public $label_update_data		= "Edit Data ".self::LABELMASTER;
 	public $label_sukses_disimpan 	= "Data berhasil disimpan";
 	public $label_gagal_disimpan 	= "Data gagal disimpan";
 	public $label_delete_data		= "Hapus Data ".self::LABELMASTER;
 	public $label_sukses_dihapus 	= "Data berhasil dihapus";
 	public $label_gagal_dihapus 	= "Data gagal dihapus";
 	public $label_detail_data		= "Datail Data ".self::LABELMASTER;
 	public $label_import_data		= "Import Data ".self::LABELMASTER;
 	public $label_sukses_diimport 	= "Data berhasil diimport";
 	public $label_gagal_diimport 	= "Import data di baris : ";
 	public $label_export_data		= "Export";
 	public $label_gagal_eksekusi 	= "Eksekusi gagal karena ketiadaan data";

	//============================== Additional Method ==============================//
	// Get project info
	public function getprojectinfo()
	{
		if(_USER_ACCESS_LEVEL_VIEW == "1") {
			$post = $this->input->post();
			if(isset($post['sel'])){
				$id = trim($post['sel']);
				echo json_encode($this->self_model->getProjectInfo($id));
			}
		} else {
			$this->load->view('errors/html/error_hacks_401');
		}
	}
}