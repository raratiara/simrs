<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Data_payment_request extends MY_Controller
{
	/* Costs */
 	const  LABELMODULE				= "finance_data_payment_request"; // identify menu
 	const  LABELMASTER				= "Payment Request";
 	const  LABELFOLDER				= "finance"; // module folder
 	const  LABELPATH				= "data_payment_request"; // controller file (lowercase)
 	const  LABELNAVSEG1				= ""; // adjusted 1st sub parent segment
 	const  LABELSUBPARENTSEG1		= ""; // 
 	const  LABELNAVSEG2				= ""; // adjusted 2nd sub parent segment
 	const  LABELSUBPARENTSEG2		= ""; // 
	
	/* View */
	public $icon 					= 'fa-database';
	public $tabel_header 			= ["ID","PR","Project","WBS","No Referensi","Total Request","Purposed","Note","Tgl Request","Requestor","Last Status"];
	
	/* Export */
	public $colnames 				= ["ID","PR","Project","WBS","No Referensi","Total Request","Purposed","Note","Tgl Request","Requestor","Last Status"];
	public $colfields 				= ["id","pr","project_title","wbs","noref","wrequest","description","notes","drequest","requestor","status"];

	/* Form Field Asset */
	public function form_field_asset()
	{
		$field = [];
		$field['txtpr'] 			= $this->self_model->return_build_txt('','pr','pr','','','readonly');
		$oforType 					= json_decode(json_encode([['forop'=>'CA'],['forop'=>'REIMBURSE'],['forop'=>'INVOICE']]), false);
		$field['seloforType'] 		= $this->self_model->return_build_select2me($oforType,'','','','for_type','for_type','','','forop','forop');
		$onoRef 					= json_decode(json_encode([]), false);
		$field['selonoRef'] 		= $this->self_model->return_build_select2me($onoRef,'','','','id_for','id_for','','','refop','refop');
		$oProject 					= $this->db->select('id,project,title')->where("id_status IS NOT NULL")->get(_PREFIX_TABLE."data_project")->result();
		$field['seloProject'] 		= $this->self_model->return_build_select2me($oProject,'','','','id_project_select','id_project_select','','','id','project','-','title','disabled');
		$field['txtseloProject'] 	= $this->self_model->return_build_txthidden('','id_project','id_project');
		$field['txtproject'] 		= $this->self_model->return_build_txt('','project_title','project_title','','','readonly');
		$oWbs 						= $this->db->select('id,code,description')->order_by('idx_seq', 'ASC')->where("active='1'")->get(_PREFIX_TABLE."option_wbs")->result();
		$field['seloWbs'] 			= $this->self_model->return_build_select2me($oWbs,'','','','id_wbs','id_wbs','','','id','code','-','description');
		$field['txtdescription'] 	= $this->self_model->return_build_txtarea('','description','description');
		$field['txtnotes'] 			= $this->self_model->return_build_txtarea('','notes','notes');
		$field['txtasdraft'] 		= $this->self_model->return_build_txthidden('0','as_draft','as_draft');
		$field['txtasoldstat'] 		= $this->self_model->return_build_txthidden('1','old_status','old_status');
		$oLastStatus 				= $this->db->select('id,description')->where("active='1'")->get(_PREFIX_TABLE."option_approval")->result();
		$field['seloLastStatus'] 	= $this->self_model->return_build_select2me($oLastStatus,'','1','','last_status','last_status','','','id','description','','','disabled');
		$field['txtdateca'] 		= $this->self_model->return_build_txt('','date_request','date_request','','','readonly');
		$oRequestor 				= [];
		$field['seloRequestor'] 	= $this->self_model->return_build_select2me($oRequestor,'','','','id_payto','id_payto','','','id','name');
		$field['txtsuppid'] 		= $this->self_model->return_build_txt('','id_supplier','id_supplier','','','readonly');
		$field['txtremainingpr'] 	= $this->self_model->return_build_txt('0','total_remaining','total_remaining','','','readonly');
		$field['txttotalpr'] 		= $this->self_model->return_build_txt('0','total_pr','total_pr','','','readonly');
		$field['radioactive'] 		= $this->self_model->return_build_radio('Bank', [['Bank','Bank'],['Cash','Cash'],['Check','Check'],['Other','Other']], 'trf_type');
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

	// Get for id info
	public function getforidinfo()
	{
		if(_USER_ACCESS_LEVEL_VIEW == "1") {
			$post = $this->input->post();
			if(isset($post['sel'])){
				$id = trim($post['sel']);
				echo $this->self_model->getForIdInfo($id);
			}
		} else {
			$this->load->view('errors/html/error_hacks_401');
		}
	}

	// Get payto info
	public function getpaytoinfo()
	{
		if(_USER_ACCESS_LEVEL_VIEW == "1") {
			$post = $this->input->post();
			if(isset($post['sel'])){
				$id = trim($post['sel']);
				echo $this->self_model->getPayToInfo($id);
			}
		} else {
			$this->load->view('errors/html/error_hacks_401');
		}
	}

	// Get for id detail info
	public function getforiddetailinfo()
	{
		if(_USER_ACCESS_LEVEL_VIEW == "1") {
			$post = $this->input->post();
			if(isset($post['sel']) && isset($post['type'])){
				$id = trim($post['sel']);
				$type = trim($post['type']);
				echo json_encode($this->self_model->getForIdDetailInfo($type,$id));
			}
		} else {
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

	// Get remaining CA budget info
	public function getremainingcabudget()
	{
		if(_USER_ACCESS_LEVEL_VIEW == "1") {
			$post = $this->input->post();
			if(isset($post['sel'])){
				$id = trim($post['sel']);
				echo json_encode($this->self_model->getRemainingCABudget($id));
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
			if(isset($post['sel']) && isset($post['type'])){
				$id = trim($post['sel']);
				$type = trim($post['type']);
				echo json_encode($this->self_model->getRequestorInfo($type,$id));
			}
		} else {
			$this->load->view('errors/html/error_hacks_401');
		}
	}

	// Generate payment request attach list row
	public function genpaymentreqrow()
	{
		if(_USER_ACCESS_LEVEL_VIEW == "1")
		{
			$post = $this->input->post(null, true);
			if(isset($post['count']))
			{
				$row = trim($post['count']);
				echo $this->self_model->getNewPaymentReqRow($row);
			} else if(isset($post['id'])) {
				$row = 0;
				$id = trim($post['id']);
				$view = (isset($post['view']) && $post['view'] == TRUE)? TRUE:FALSE;
				echo json_encode($this->self_model->getNewPaymentReqRow($row,$id,$view));
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