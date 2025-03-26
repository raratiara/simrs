<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Project_attendance_activity extends MY_Controller
{
	/* Costs */
 	const  LABELMODULE				= "project_attendance_activity"; // identify menu
 	const  LABELMASTER				= "Project Attendance Activity";
 	const  LABELFOLDER				= "project"; // module folder
 	const  LABELPATH				= "project_attendance_activity"; // controller file (lowercase)
 	const  LABELNAVSEG1				= ""; // adjusted 1st sub parent segment
 	const  LABELSUBPARENTSEG1		= ""; // 
 	const  LABELNAVSEG2				= ""; // adjusted 2nd sub parent segment
 	const  LABELSUBPARENTSEG2		= ""; // 
	
	/* View */
	public $icon 					= 'fa-database';
	public $tabel_header 			= ["ID","Project","Engineer","Activity","Location","Date","Approval"];
	
	/* Export */
	public $colnames 				= ["ID","Project","Engineer","Activity","Location","Date","Approval"];
	public $colfields 				= ["id","project","engineer","activity","location","dt","approval"];

	/* Form Field Asset */
	public function form_field_asset()
	{
		$field = [];
		$oProject 					= $this->db->order_by('id', 'ASC')->get(_PREFIX_TABLE."data_project")->result();
		$field['seloProject'] 		= $this->self_model->return_build_select2me($oProject,'','','','id_project','id_project','','','id','project','-','title');
		$field['txtEngineer'] 		= $this->self_model->return_build_txt($_SESSION["name"],'engineer_name','engineer_name','','','readonly');
		$oEngineer 					= $this->db->order_by('id', 'ASC')->get(_PREFIX_TABLE."data_karyawan")->result();
		$field['txtLocation'] 		= $this->self_model->return_build_txt('','location','location');
		$field['txtdate'] 			= $this->self_model->return_build_txtdate(date('d-m-Y'),'date','date');
		$field['txttimein'] 		= $this->self_model->return_build_txttime("07:00",'time_in','time_in');
		$field['txttimeout'] 		= $this->self_model->return_build_txttime("17:00",'time_out','time_out');
		$field['txtactivity'] 		= $this->self_model->return_build_txtarea('','activity','activity');
		$field['txtremark'] 		= $this->self_model->return_build_txt('','remark','remark');
		$field['txtnote'] 			= $this->self_model->return_build_txtarea('','note','note');
		$field['radioapproval'] 	= $this->self_model->return_build_radio('0', [[0,'Prepared'],[1,'Approver']], 'approval', '', 'inline');
		//Filter
		$field['txtLocationFilter'] 		= $this->self_model->return_build_txt('','location_filter','location_filter');
		$field['seloProjectFilter'] 		= $this->self_model->return_build_select2me($oProject,'','','','id_project_filter','id_project_filter','','','title','project','-','title');
		$field['seloEngineerFilter'] 		= $this->self_model->return_build_select2me($oEngineer,'','','','id_engineer_filter','id_engineer_filter','','','name','engineer','-','name');
		$field['txtperiodstart'] 			= $this->self_model->return_build_txtdate("1-1-2015",'periodstart','periodstart'); //nonaktif
		$field['txtperiodend'] 				= $this->self_model->return_build_txtdate(date('d-m-Y'),'periodend','periodend'); //nonaktif
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
		define('_USER_ACCESS_LEVEL_IMPORT',$akses["import"]);
		define('_USER_ACCESS_LEVEL_EKSPORT',$akses["eksport"]);
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
 	public $label_modul				= self::LABELMASTER;
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
	// Get Approval selector item
	public function getapprsel()
	{
		if(_USER_ACCESS_LEVEL_VIEW == "1") {
			$post = $this->input->post();
			if(isset($post['smn'])&&isset($post['sel'])){
				$mn = trim($post['smn']);
				$id = trim($post['sel']);
				echo $this->self_model->getApprsel($mn,$id);
			}
		} else {
			$this->load->view('errors/html/error_hacks_401');
		}
	}
	
	// Generate location list row
	public function genlocationrow()
	{
		if(_USER_ACCESS_LEVEL_VIEW == "1")
		{
			$post = $this->input->post(null, true);
			if(isset($post['count']))
			{
				$row = trim($post['count']);
				echo $this->self_model->getNewLocationRow($row);
			} else if(isset($post['id'])) {
				$row = 0;
				$id = trim($post['id']);
				$view = (isset($post['view']) && $post['view'] == TRUE)? TRUE:FALSE;
				echo json_encode($this->self_model->getNewLocationRow($row,$id,$view));
			}
		}
		else
		{
			$this->load->view('errors/html/error_hacks_401');
		}
	}

	// Get location detail info
	public function getlocationinfo()
	{
		if(_USER_ACCESS_LEVEL_VIEW == "1") {
			$post = $this->input->post();
			if(isset($post['sel'])){
				$id = trim($post['sel']);
				echo json_encode($this->self_model->getLocationInfo($id));
			}
		} else {
			$this->load->view('errors/html/error_hacks_401');
		}
	}
}