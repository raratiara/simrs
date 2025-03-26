<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Data_ca_closing extends MY_Controller
{
	/* Costs */
 	const  LABELMODULE				= "finance_data_ca_closing"; // identify menu
 	const  LABELMASTER				= "Cash Advance - Closing";
 	const  LABELFOLDER				= "finance"; // module folder
 	const  LABELPATH				= "data_ca_closing"; // controller file (lowercase)
 	const  LABELNAVSEG1				= ""; // adjusted 1st sub parent segment
 	const  LABELSUBPARENTSEG1		= ""; // 
 	const  LABELNAVSEG2				= ""; // adjusted 2nd sub parent segment
 	const  LABELSUBPARENTSEG2		= ""; // 
	
	/* View */
	public $icon 					= 'fa-database';
	public $tabel_header 			= ["ID","PR","Project","WBS","Tgl Closing","Total PR","Total Adjustment","Total Closing","Balance Closing","Requestor","Departemen","Last Status"];
	
	/* Export */
	public $colnames 				= ["ID","PR","Project","WBS","Tgl Closing","Total PR","Total Adjustment","Total Closing","Balance Closing","Requestor","Departemen","Last Status"];
	public $colfields 				= ["id","pr","project_title","wbs","dclosing","wpr","wadj","wclosing","wbalance","requestor","departemen","status"];

	/* Form Field Asset */
	public function form_field_asset()
	{
		$field = [];
		$oPRca 						= [];
		$field['seloPRca'] 			= $this->self_model->return_build_select2me($oPRca,'','','','id_ca_req','id_ca_req','','','id','pr');
		$field['txtnoproject'] 		= $this->self_model->return_build_txt('','project','project','','','readonly');
		$field['txtproject'] 		= $this->self_model->return_build_txt('','project_title','project_title','','','readonly');
		$field['txtwbs'] 			= $this->self_model->return_build_txt('','wbs','wbs','','','readonly');
		$field['txtdescription'] 	= $this->self_model->return_build_txtarea('','description','description','4','','','disabled');
		$field['txtasdraft'] 		= $this->self_model->return_build_txthidden('0','as_draft','as_draft');
		$field['txtasoldstat'] 		= $this->self_model->return_build_txthidden('1','old_status','old_status');
		$oLastStatus 				= $this->db->select('id,description')->where("active='1'")->get(_PREFIX_TABLE."option_approval")->result();
		$field['seloLastStatus'] 	= $this->self_model->return_build_select2me($oLastStatus,'','1',[1,8],'last_status','last_status','','','id','description','','','disabled');
		$field['txtdateclosing'] 	= $this->self_model->return_build_txt('','date_closing','date_closing','','','readonly');
		$field['txtrequestor'] 		= $this->self_model->return_build_txt('','requestor','requestor','','','readonly');
		$field['txtnik'] 			= $this->self_model->return_build_txt('','nik','nik','','','readonly');
		$field['txtdept'] 			= $this->self_model->return_build_txthidden('','id_dept','id_dept');
		$field['txtdepartemen'] 	= $this->self_model->return_build_txt('','departemen','departemen','','','readonly');
		$field['txttotalpr'] 		= $this->self_model->return_build_txt('0','total_pr','total_pr','','','readonly');
		$field['txttotaladj'] 		= $this->self_model->return_build_txt('0','total_adj','total_adj','','','readonly');
		$field['txttotalclosing'] 	= $this->self_model->return_build_txt('0','total_closing','total_closing','','','readonly');
		$field['txtbalanceclosing'] = $this->self_model->return_build_txt('0','balance_closing','balance_closing','','','readonly');
		
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
		define('_USER_ACCESS_LEVEL_EKSPORT',$akses["eksport"]);
		//define('_USER_ACCESS_LEVEL_EKSPORT',0);
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

	// Get PR selector item
	public function getprsel()
	{
		if(_USER_ACCESS_LEVEL_VIEW == "1") {
			$post = $this->input->post();
			if(isset($post['sel'])){
				$id = trim($post['sel']);
				echo $this->self_model->getPRsel($id);
			}
		} else {
			$this->load->view('errors/html/error_hacks_401');
		}
	}

	// Get CA Request Payment info
	public function getcapaymentinfo()
	{
		if(_USER_ACCESS_LEVEL_VIEW == "1") {
			$post = $this->input->post();
			if(isset($post['sel'])){
				$id = trim($post['sel']);
				echo json_encode($this->self_model->getCaPaymentInfo($id));
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

	// Generate adjustment expenses list row
	public function genadjexpensesrow()
	{
		if(_USER_ACCESS_LEVEL_VIEW == "1")
		{
			$post = $this->input->post(null, true);
			if(isset($post['count']))
			{
				$row = trim($post['count']);
				echo $this->self_model->getNewAdjExpensesRow($row);
			} else if(isset($post['id'])) {
				$row = 0;
				$id = trim($post['id']);
				$view = (isset($post['view']) && $post['view'] == TRUE)? TRUE:FALSE;
				echo json_encode($this->self_model->getNewAdjExpensesRow($row,$id,$view));
			}
		}
		else
		{
			$this->load->view('errors/html/error_hacks_401');
		}
	}
	// Populate CA expenses list row
	public function gencaexpensesrow()
	{
		if(_USER_ACCESS_LEVEL_VIEW == "1")
		{
			$post = $this->input->post(null, true);
			if(isset($post['id']) && !empty($post['id'])) {
				$id = trim($post['id']);
				echo json_encode($this->self_model->getCAExpensesRows($id));
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
	
	// Removing Adj Attach file
	public function rmadjattc()
	{
		if(_USER_ACCESS_LEVEL_ADD == "1" || _USER_ACCESS_LEVEL_UPDATE == "1")
		{
			$post = $this->input->post(null, true);
			if(isset($post['id']) && isset($post['file']))
			{
				$id = trim($post['id']);
				$file = trim($post['file']);
				$this->self_model->rmAdjAttc($id,$file);
			}
		}
		else
		{
			$this->load->view('errors/html/error_hacks_401');
		}
	}
}