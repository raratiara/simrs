<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Po extends MYP_Controller
{
	/* Costs */
 	const  LABELMODULE				= "purchasing_po"; // identify menu
 	const  LABELMASTER				= "PO";
 	const  LABELFOLDER				= "purchasing"; // module folder
 	const  LABELPATH				= "po"; // controller file (lowercase)
 	const  LABELNAVSEG1				= ""; // adjusted 1st sub parent segment
 	const  LABELSUBPARENTSEG1		= ""; // 
 	const  LABELNAVSEG2				= ""; // adjusted 2nd sub parent segment
 	const  LABELSUBPARENTSEG2		= ""; // 
	
	/* View */
	public $icon 					= 'fa-database';
	public $tabel_header 			= ["ID","PO","#SPK/Contract","Supplier","Date","Delivery Date","Nilai PO","Status"];
	
	/* Export */
	public $colnames 				= ["ID","PO","#SPK/Contract","Supplier","Date","Delivery Date","Deskripsi","Nilai PO","Status"];
	public $colfields 				= ["id","po","spk","supplier","dpo","ddue","description","wpo","status"];

	/* Form Field Asset */
	public function form_field_asset()
	{
		$field = [];
		$field['txtdatepo'] 		= $this->self_model->return_build_txtdate(date('d-m-Y'),'date_po','date_po');
		$field['txtdatedue'] 		= $this->self_model->return_build_txtdate(date('d-m-Y'),'date_due','date_due');
		$field['txtpo'] 			= $this->self_model->return_build_txt('','po','po');
		$field['txtspk'] 			= $this->self_model->return_build_txt('','spk','spk');
		$field['worth'] 			= $this->self_model->return_build_txt('0','worth','worth','','','readonly');
		$oPic 						= $this->db->select('id,name')->where("id_status='1'")->get(_PREFIX_TABLE."data_karyawan")->result();
		$field['seloPic'] 			= $this->self_model->return_build_select2me($oPic,'','','','id_pic','id_pic','','','id','name');
		$oStatus 					= $this->db->select('id,description')->order_by('idx_seq', 'ASC')->where("active='1'")->get(_PREFIX_TABLE."option_purchasing_status")->result();
		$field['seloStatus'] 		= $this->self_model->return_build_select2me($oStatus,'','','','id_status','id_status','','','id','description');
		$oSupplier 					= $this->db->select('id,code,name')->order_by('code', 'ASC')->get(_PREFIX_TABLE."data_supplier")->result();
		$field['seloSupplier'] 		= $this->self_model->return_build_select2me($oSupplier,'','','','id_supplier','id_supplier','','','id','code','-','name');
		$oTerm 						= $this->db->select('id,code')->order_by('idx_seq', 'ASC')->where("active='1'")->get(_PREFIX_TABLE."option_term_of_ppayment")->result();
		$field['seloTerm'] 			= $this->self_model->return_build_radioselect($oTerm,'4','','id_term','','inline','id','code');
		$oCurrency 					= $this->db->select('id,code')->where("active='1'")->get(_PREFIX_TABLE."option_currency")->result();
		$field['seloCurrency'] 		= $this->self_model->return_build_radioselect($oCurrency,'1','','id_currency','','inline','id','code');
		$field['txtdescription'] 	= $this->self_model->return_build_txtarea('','description','description');
		$field['txtketentuan'] 		= $this->self_model->return_build_txtarea('','notes','notes');
		// readonly auto populate by select option - no data save
		$field['txtnamasupplier'] 	= $this->self_model->return_build_txt('','supplier_name','supplier_name','','','readonly');
		$field['txtalamatsupplier'] = $this->self_model->return_build_txtarea('','supplier_address','supplier_address','4','','','readonly');
		
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
	public function getprint($id)
	{
		if(_USER_ACCESS_LEVEL_VIEW == "1" && _USER_ACCESS_LEVEL_DETAIL == "1") {
			$pid = $id;
			// Get main data
			$this->data['rs'] = $this->self_model->getRowData($pid);
			// Get item data
			$this->data['rs2'] 	= $this->self_model->getPoItemRows($pid,TRUE,TRUE);
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
					$this->data['rs2'] 	= $this->self_model->getPoItemRows($pid,TRUE,TRUE);
					$this->load->view($this->folder_name.'/print' , $this->data);
				}
			}
		} else {
			$this->load->view('errors/html/error_hacks_401');
		}
	}

	// Get supplier info
	public function getsupplierinfo()
	{
		if(_USER_ACCESS_LEVEL_VIEW == "1") {
			$post = $this->input->post();
			if(isset($post['sel'])){
				$id = trim($post['sel']);
				echo json_encode($this->self_model->getSupplierInfo($id));
			}
		} else {
			$this->load->view('errors/html/error_hacks_401');
		}
	}

	// Generate po list row
	public function genpoitemrow()
	{
		if(_USER_ACCESS_LEVEL_VIEW == "1")
		{
			$post = $this->input->post(null, true);
			if(isset($post['count']))
			{
				$row = trim($post['count']);
				echo $this->self_model->getNewPoItemRow($row);
			} else if(isset($post['id'])) {
				$row = 0;
				$id = trim($post['id']);
				$view = (isset($post['view']) && $post['view'] == TRUE)? TRUE:FALSE;
				echo json_encode($this->self_model->getNewPoItemRow($row,$id,$view));
			}
		}
		else
		{
			$this->load->view('errors/html/error_hacks_401');
		}
	}

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