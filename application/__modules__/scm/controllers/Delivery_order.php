<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Delivery_order extends MY_Controller
{
	/* Costs */
 	const  LABELMODULE				= "delivery_order"; // identify menu
 	const  LABELMASTER				= "Delivery Order";
 	const  LABELFOLDER				= "scm"; // module folder
 	const  LABELPATH				= "delivery_order"; // controller file (lowercase)
 	const  LABELNAVSEG1				= ""; // adjusted 1st sub parent segment
 	const  LABELSUBPARENTSEG1		= ""; // 
 	const  LABELNAVSEG2				= ""; // adjusted 2nd sub parent segment
 	const  LABELSUBPARENTSEG2		= ""; // 
	
	/* View */
	public $icon 					= 'fa-database';
	public $tabel_header 			= ["ID","No DO","Date","PO","Note"];
	
	/* Export */
	public $colnames 				= ["ID","DO","Date","PO","Note"];
	public $colfields 				= ["id","do","dt","po","note"];

	/* Form Field Asset */
	public function form_field_asset()
	{
		$field = [];
		$field['txtdo'] 			= $this->self_model->return_build_txt('','do','do');
        $field['txtdate'] 			= $this->self_model->return_build_txtdate(date('d-m-Y'),'date','date');
		$oPo 					    = $this->db->order_by('id', 'ASC')->get(_PREFIX_TABLE."data_po")->result();
		$field['seloPo'] 		    = $this->self_model->return_build_select2me($oPo,'','','','id_po','id_po','','','id','','-','po');
        $oProject 					= $this->db->order_by('id', 'ASC')->get(_PREFIX_TABLE."data_project")->result();
		$field['seloProject'] 		= $this->self_model->return_build_select2me($oProject,'','','','id_project','id_project','','','id','project','-','title');
		$oKaryawan 					= $this->db->order_by('id', 'ASC')->get(_PREFIX_TABLE."data_karyawan")->result();
		$field['seloReceived'] 		= $this->self_model->return_build_select2me($oKaryawan,'','','','received_by','received_by','','','id','karyawan','-','name');
		$field['seloShipped'] 		= $this->self_model->return_build_select2me($oKaryawan,'','','','shipped_by','shipped_by','','','id','karyawan','-','name');
		$field['seloPrepared'] 		= $this->self_model->return_build_select2me($oKaryawan,'','','','prepared_by','prepared_by','','','id','karyawan','-','name');
		$field['seloAuthorized'] 	= $this->self_model->return_build_select2me($oKaryawan,'','','','authorized_by','authorized_by','','','id','karyawan','-','name');
		$field['txtnote'] 			= $this->self_model->return_build_txtarea('','note_order','note_order');

		
        $field['txtrecname'] 		= $this->self_model->return_build_txt('','rec_name','rec_name');
        $field['txtrecaddress'] 	= $this->self_model->return_build_txtarea('','rec_address','rec_address');
        $field['txtrectelp'] 		= $this->self_model->return_build_txt('','rec_telp','rec_telp');
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
	// Generate quote list row
	public function genitemrow()
	{
		if(_USER_ACCESS_LEVEL_VIEW == "1")
		{
			$post = $this->input->post(null, true);
			if(isset($post['count']))
			{
				$row = trim($post['count']);
				echo $this->self_model->getNewItemRow($row);
			} else if(isset($post['id'])) {
				$row = 0;
				$id = trim($post['id']);
				$view = (isset($post['view']) && $post['view'] == TRUE)? TRUE:FALSE;
				echo json_encode($this->self_model->getNewItemRow($row,$id,$view));
			}
		}
		else
		{
			$this->load->view('errors/html/error_hacks_401');
		}
	}

	public function getprint($id)
	{
		if(_USER_ACCESS_LEVEL_VIEW == "1" && _USER_ACCESS_LEVEL_DETAIL == "1") {
			$pid = $id;
			// Get main data
			$this->data['rs'] = $this->self_model->getRowData($pid);
			// Get item data
			$this->data['rs2'] 	= $this->self_model->getItemRows($pid,TRUE,TRUE);
			$this->load->view($this->folder_name.'/print' , $this->data);
		} else {
			$this->load->view('errors/html/error_hacks_401');
		}
	}

	// Print
	public function print()
	{
		if(_USER_ACCESS_LEVEL_VIEW == "1" && _USER_ACCESS_LEVEL_DETAIL == "1") {
			if ($post = $this->input->post(null, true))
			{
				if(isset($post['pid'])){
					$pid = $post['pid'];
					// Get main data
					$this->data['rs'] = $this->self_model->getRowData($pid);
					// Get item data
					$this->data['rs2'] 	= $this->self_model->getItemRows($pid,TRUE,TRUE);
					$this->load->view($this->folder_name.'/print' , $this->data);
				}
			}
		} else {
			$this->load->view('errors/html/error_hacks_401');
		}
	}

}