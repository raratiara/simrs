<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Data_reimburse extends MY_Controller
{
	/* Costs */
 	const  LABELMODULE				= "finance_data_reimburse"; // identify menu
 	const  LABELMASTER				= "Reimburse Payment - Request";
 	const  LABELFOLDER				= "finance"; // module folder
 	const  LABELPATH				= "data_reimburse"; // controller file (lowercase)
 	const  LABELNAVSEG1				= ""; // adjusted 1st sub parent segment
 	const  LABELSUBPARENTSEG1		= ""; // 
 	const  LABELNAVSEG2				= ""; // adjusted 2nd sub parent segment
 	const  LABELSUBPARENTSEG2		= ""; // 
	
	/* View */
	public $icon 					= 'fa-database';
	public $tabel_header 			= ["ID","RB","Project","WBS","Tgl Request","Total Request","Remaining Balance","Requestor","Departemen","Last Status"];
	
	/* Export */
	public $colnames 				= ["ID","RB","Project","WBS","Tgl Request","Total Request","Requestor","Departemen","Last Status"];
	public $colfields 				= ["id","reim","project","wbs","drequest","wrequest","requestor","departemen","status"];

	/* Form Field Asset */
	public function form_field_asset()
	{
		$field = [];
		$field['txtreim'] 			= $this->self_model->return_build_txt('','reim','reim','','','readonly');
		$oProject 					= $this->db->select('id,project,title')->where("id_status IS NOT NULL AND id_status <= '3'")->get(_PREFIX_TABLE."data_project")->result();
		$field['seloProject'] 		= $this->self_model->return_build_select2me($oProject,'','','','id_project','id_project','','','id','project','-','title');
		$field['txtproject'] 		= $this->self_model->return_build_txt('','project_title','project_title','','','readonly');
		$oWbs 						= $this->db->select('id,code,description')->order_by('idx_seq', 'ASC')->where("active='1'")->get(_PREFIX_TABLE."option_wbs")->result();
		$field['seloWbs'] 			= $this->self_model->return_build_select2me($oWbs,'','','','id_wbs','id_wbs','','','id','code','-','description');
		$field['txtdescription'] 	= $this->self_model->return_build_txtarea('','description','description');
		$field['txtasdraft'] 		= $this->self_model->return_build_txthidden('0','as_draft','as_draft');
		$field['txtasoldstat'] 		= $this->self_model->return_build_txthidden('1','old_status','old_status');
		$oLastStatus 				= $this->db->select('id,description')->where("active='1'")->get(_PREFIX_TABLE."option_approval")->result();
		$field['seloLastStatus'] 	= $this->self_model->return_build_select2me($oLastStatus,'','1','','last_status','last_status','','','id','description','','','disabled');
		$field['txtdateca'] 		= $this->self_model->return_build_txt('','date_request','date_request','','','readonly');
		$oRequestor 				= $this->db->select('id,name')->where("id_status='1'")->get(_PREFIX_TABLE."data_karyawan")->result();
		$field['seloRequestor'] 	= $this->self_model->return_build_select2me($oRequestor,'','','','id_requestor','id_requestor','','','id','name');
		$field['txtnik'] 			= $this->self_model->return_build_txt('','nik','nik','','','readonly');
		$field['txtdept'] 			= $this->self_model->return_build_txthidden('','id_dept','id_dept');
		$field['txtdepartemen'] 	= $this->self_model->return_build_txt('','departemen','departemen','','','readonly');
		$field['txttotalreim'] 		= $this->self_model->return_build_txt('0','total_reim','total_reim','','','readonly');
		$field['radioactive'] 		= $this->self_model->return_build_radio('Bank', [['Bank','Bank'],['Cash','Cash']], 'trf_type', '', 'inline');
		$field['txtnorek'] 			= $this->self_model->return_build_txt('','norek','norek','','','readonly');
		$field['txtnamarek'] 		= $this->self_model->return_build_txt('','namarek','namarek','','','readonly');
		
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
	// Generate appropriate action button
	public function genactbutton()
	{
		if(_USER_ACCESS_LEVEL_VIEW == "1")
		{
			$post = $this->input->post();
			if(isset($post['sel'])){
				$id = trim($post['sel']);
				echo json_encode($this->self_model->getActButton($id));
			}
		}
		else
		{
			$this->load->view('errors/html/error_hacks_401');
		}
	}

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

	// Get requestor info
	public function getrequestorinfo()
	{
		if(_USER_ACCESS_LEVEL_VIEW == "1") {
			$post = $this->input->post();
			if(isset($post['sel'])){
				$id = trim($post['sel']);
				echo json_encode($this->self_model->getRequestorInfo($id));
			}
		} else {
			$this->load->view('errors/html/error_hacks_401');
		}
	}

	// Generate expenses list row
	public function genexpensesrow()
	{
		if(_USER_ACCESS_LEVEL_VIEW == "1")
		{
			$post = $this->input->post(null, true);
			if(isset($post['count']))
			{
				$row = trim($post['count']);
				echo $this->self_model->getNewExpensesRow($row);
			} else if(isset($post['id'])) {
				$row = 0;
				$id = trim($post['id']);
				$view = (isset($post['view']) && $post['view'] == TRUE)? TRUE:FALSE;
				echo json_encode($this->self_model->getNewExpensesRow($row,$id,$view));
			}
		}
		else
		{
			$this->load->view('errors/html/error_hacks_401');
		}
	}
	
	// Removing Attach file
	public function rmattc()
	{
		if(_USER_ACCESS_LEVEL_ADD == "1" || _USER_ACCESS_LEVEL_UPDATE == "1")
		{
			$post = $this->input->post(null, true);
			if(isset($post['id']) && isset($post['file']))
			{
				$id = trim($post['id']);
				$file = trim($post['file']);
				$this->self_model->rmAttc($id,$file);
			}
		}
		else
		{
			$this->load->view('errors/html/error_hacks_401');
		}
	}
}