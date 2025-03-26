<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Data_project extends MY_Controller
{
	/* Costs */
 	const  LABELMODULE				= "sales_data_project"; // identify menu
 	const  LABELMASTER				= "Project";
 	const  LABELFOLDER				= "sales"; // module folder
 	const  LABELPATH				= "data_project"; // controller file (lowercase)
 	const  LABELNAVSEG1				= ""; // adjusted 1st sub parent segment
 	const  LABELSUBPARENTSEG1		= ""; // 
 	const  LABELNAVSEG2				= ""; // adjusted 2nd sub parent segment
 	const  LABELSUBPARENTSEG2		= ""; // 
	
	/* View */
	public $icon 					= 'fa-database';
	public $tabel_header 			= ["ID","No Project","Nama Project","Kode Customer","Prepared By","Tipe","Plan Tgl Start","Plan Tgl Finish","Actual Tgl Start","Actual Tgl Finish","Status"];
	
	/* Export */
	public $colnames 				= ["ID","No Project","Nama Project","Project Scope","Kode Customer","Prepared By","Tipe","No SPK/Kontrak","No PO/WO","Plan Tgl Start","Plan Tgl Finish","Actual Tgl Start","Actual Tgl Finish","Member","Status"];
	public $colfields 				= ["id","project","title","pscope","ccustomer","spk","dept","tipe","po","dpstart","dpfinish","dastart","dafinish","member","status"];

	/* Form Field Asset */
	public function form_field_asset()
	{
		$field = [];
		$field['txtproject'] 		= $this->self_model->return_build_txt('','project','project');
		$field['txttitle'] 			= $this->self_model->return_build_txt('','title','title');
		$oCustomer 					= $this->db->select('id,code,name')->order_by('code', 'ASC')->get(_PREFIX_TABLE."data_customer")->result();
		$field['seloCustomer'] 		= $this->self_model->return_build_select2me($oCustomer,'','','','id_customer','id_customer','','','id','code','-','name');
		$oSpk 						= $this->db->select('id,spk,description')->order_by('spk', 'ASC')->get(_PREFIX_TABLE."data_spk")->result();
		$field['seloSpk'] 			= $this->self_model->return_build_select2me($oSpk,'','','','id_spk','id_spk','','','id','spk');
		//$oPO 						= $this->db->select('id,po,description')->order_by('po', 'ASC')->get(_PREFIX_TABLE."data_po")->result();
		//$field['seloPO'] 			= $this->self_model->return_build_checkboxselect($oPO,'','','id_po_wo[]','','','id','po','-','description');
		$field['txtdatepstart'] 	= $this->self_model->return_build_txtdate('','date_plan_start','date_plan_start');
		$field['txtdatepfinish'] 	= $this->self_model->return_build_txtdate('','date_plan_finish','date_plan_finish');
		$field['txtdateastart'] 	= $this->self_model->return_build_txtdate('','date_actual_start','date_actual_start');
		$field['txtdateafinish'] 	= $this->self_model->return_build_txtdate('','date_actual_finish','date_actual_finish');
		$oProjectScope 				= $this->db->select('id,description')->order_by('idx_seq', 'ASC')->where("active='1'")->get(_PREFIX_TABLE."option_project_scope")->result();
		$field['seloProjectScope'] 	= $this->self_model->return_build_checkboxselect($oProjectScope,'','','project_scope[]','','','id','description');
		$oSla 						= $this->db->select('id,description')->order_by('idx_seq', 'ASC')->where("active='1'")->get(_PREFIX_TABLE."option_sla")->result();
		$field['seloSla'] 			= $this->self_model->return_build_radioselect($oSla,'','','id_sla','','','id','description');
		$oStatus 					= $this->db->select('id,description')->order_by('idx_seq', 'ASC')->where("active='1'")->get(_PREFIX_TABLE."option_project_status")->result();
		$field['seloStatus'] 		= $this->self_model->return_build_select2me($oStatus,'','','','id_status','id_status','','','id','description');
		$oKaryawan 					= $this->db->select('id,name')->where("id_status='1'")->get(_PREFIX_TABLE."data_karyawan")->result();
		$field['seloPic'] 			= $this->self_model->return_build_select2me($oKaryawan,'','','','id_pic','id_pic','','','id','name');
		$field['seloPM'] 			= $this->self_model->return_build_select2me($oKaryawan,'','','','id_pm','id_pm','','','id','name');
		$field['seloGM'] 			= $this->self_model->return_build_select2me($oKaryawan,'','','','id_gm','id_gm','','','id','name');
		$field['seloADM'] 			= $this->self_model->return_build_select2me($oKaryawan,'','','','id_adm','id_adm','','','id','name');
		$field['radiotype'] 		= $this->self_model->return_build_radio('1', [[1,'External'],[0,'Internal']], 'type', '', 'inline');
		$oDepartemen 				= $this->db->select('id,description')->where("active='1'")->get(_PREFIX_TABLE."option_departemen")->result();
		$field['seloDept'] 			= $this->self_model->return_build_select2me($oDepartemen,'','','','id_dept','id_dept','','','id','description');
		
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
	// Generate member list row
	public function genmemberrow()
	{
		if(_USER_ACCESS_LEVEL_VIEW == "1")
		{
			$post = $this->input->post(null, true);
			if(isset($post['count']))
			{
				$row = trim($post['count']);
				echo $this->self_model->getNewMemberRow($row);
			} else if(isset($post['id'])) {
				$row = 0;
				$id = trim($post['id']);
				$view = (isset($post['view']) && $post['view'] == TRUE)? TRUE:FALSE;
				echo json_encode($this->self_model->getNewMemberRow($row,$id,$view));
			}
		}
		else
		{
			$this->load->view('errors/html/error_hacks_401');
		}
	}
	
	// Get member detail info
	public function getmemberinfo()
	{
		if(_USER_ACCESS_LEVEL_VIEW == "1") {
			$post = $this->input->post();
			if(isset($post['sel'])){
				$id = trim($post['sel']);
				echo json_encode($this->self_model->getMemberInfo($id));
			}
		} else {
			$this->load->view('errors/html/error_hacks_401');
		}
	}

	// Generate po list row
	public function genporow()
	{
		if(_USER_ACCESS_LEVEL_VIEW == "1")
		{
			$post = $this->input->post(null, true);
			if(isset($post['count']))
			{
				$row = trim($post['count']);
				echo $this->self_model->getNewPoRow($row);
			} else if(isset($post['id'])) {
				$row = 0;
				$id = trim($post['id']);
				$view = (isset($post['view']) && $post['view'] == TRUE)? TRUE:FALSE;
				echo json_encode($this->self_model->getNewPoRow($row,$id,$view));
			}
		}
		else
		{
			$this->load->view('errors/html/error_hacks_401');
		}
	}
	
	// Get po detail info
	public function getpoinfo()
	{
		if(_USER_ACCESS_LEVEL_VIEW == "1") {
			$post = $this->input->post();
			if(isset($post['sel'])){
				$id = trim($post['sel']);
				echo json_encode($this->self_model->getPoInfo($id));
			}
		} else {
			$this->load->view('errors/html/error_hacks_401');
		}
	}
}