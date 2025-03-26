<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Data_spk extends MY_Controller
{
	/* Costs */
 	const  LABELMODULE				= "sales_data_spk"; // identify menu
 	const  LABELMASTER				= "SPK";
 	const  LABELFOLDER				= "sales"; // module folder
 	const  LABELPATH				= "data_spk"; // controller file (lowercase)
 	const  LABELNAVSEG1				= ""; // adjusted 1st sub parent segment
 	const  LABELSUBPARENTSEG1		= ""; // 
 	const  LABELNAVSEG2				= ""; // adjusted 2nd sub parent segment
 	const  LABELSUBPARENTSEG2		= ""; // 
	
	/* View */
	public $icon 					= 'fa-database';
	public $tabel_header 			= ["ID","SPK","Tanggal SPK","Due Date","Customer","Nilai SPK"];
	
	/* Export */
	public $colnames 				= ["ID","SPK","Tanggal SPK","Due Date","Customer","Deskripsi","Nilai SPK"];
	public $colfields 				= ["id","spk","dspk","ddue","customer","description","wspk"];

	/* Form Field Asset */
	public function form_field_asset()
	{
		$field = [];
		$field['txtspk'] 			= $this->self_model->return_build_txt('','spk','spk');
		$field['txtdatespk'] 		= $this->self_model->return_build_txtdate(date('d-m-Y'),'date_spk','date_spk');
		$field['txtdatedue'] 		= $this->self_model->return_build_txtdate(date('d-m-Y'),'date_due','date_due');
		$oCustomer 					= $this->db->select('id,code,name')->order_by('code', 'ASC')->get(_PREFIX_TABLE."data_customer")->result();
		$field['seloCustomer'] 		= $this->self_model->return_build_select2me($oCustomer,'','','','id_customer','id_customer','','','id','code','-','name');
		$field['txtdescription'] 	= $this->self_model->return_build_txtarea('','description','description');
		$field['worth'] 			= $this->self_model->return_build_txt('0','worth','worth');
		//$oStatus 					= $this->db->select('id,description')->order_by('idx_seq', 'ASC')->where("active='1'")->get(_PREFIX_TABLE."option_general_status")->result();
		//$field['seloStatus'] 		= $this->self_model->return_build_select($oStatus,'','','','id_status','id_status','','','id','description');
		
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
	// Generate Attach list row
	public function genattcrow()
	{
		if(_USER_ACCESS_LEVEL_VIEW == "1")
		{
			$post = $this->input->post(null, true);
			if(isset($post['count']))
			{
				$row = trim($post['count']);
				echo $this->self_model->getNewAttcRow($row);
			} else if(isset($post['id'])) {
				$row = 0;
				$id = trim($post['id']);
				$view = (isset($post['view']) && $post['view'] == TRUE)? TRUE:FALSE;
				echo json_encode($this->self_model->getNewAttcRow($row,$id,$view));
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