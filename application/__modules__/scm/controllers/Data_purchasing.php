<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Data_purchasing extends MY_Controller
{
	/* Costs */
 	const  LABELMODULE				= "scm_data_purchasing"; // identify menu
 	const  LABELMASTER				= "Data Purchasing";
 	const  LABELFOLDER				= "scm"; // module folder
 	const  LABELPATH				= "data_purchasing"; // controller file (lowercase)
 	const  LABELNAVSEG1				= ""; // adjusted 1st sub parent segment
 	const  LABELSUBPARENTSEG1		= ""; // 
 	const  LABELNAVSEG2				= ""; // adjusted 2nd sub parent segment
 	const  LABELSUBPARENTSEG2		= ""; // 
	
	/* View */
	public $icon 					= 'fa-database';
	public $tabel_header 			= ["ID","PO","Porject","No SPK","Total Harga","Approval"];
	
	/* Export */
	public $colnames 				= ["ID","PO","Project","No SPK","Total Harga","Approval"];
	public $colfields 				= ["id","po","project","spk","wpu","approval"];

	/* Form Field Asset */
	public function form_field_asset()
	{
		$field = [];
		$oPO 						= $this->db->select('id,po')->get(_PREFIX_TABLE."data_po")->result();
		$field['seloPO'] 			= $this->self_model->return_build_select2me($oPO,'','','','id_po','id_po','','','id','po');
		$oProject 					= $this->db->select('id,project,title')->get(_PREFIX_TABLE."data_project")->result();
		$field['seloProject'] 		= $this->self_model->return_build_select2me($oProject,'','','','id_project','id_project','','','id','project','-','title');
		$oCurrency 					= $this->db->select('id,code')->where("active='1'")->get(_PREFIX_TABLE."option_currency")->result();
		$field['seloCurrency'] 		= $this->self_model->return_build_radioselect($oCurrency,'1','','id_currency','','inline','id','code');
		$field['txtdate'] 			= $this->self_model->return_build_txtdate(date('d-m-Y'),'date','date');
		$field['txtdatedelivery'] 	= $this->self_model->return_build_txtdate(date('d-m-Y'),'delivery_date','delivery_date');
		$oSpk 						= $this->db->select('id,spk,description')->get(_PREFIX_TABLE."data_spk")->result();
		$field['seloSpk'] 			= $this->self_model->return_build_select2me($oSpk,'','','','id_spk','id_spk','','','id','spk','-','description');
		$oSupplier 					= $this->db->select('id,code')->get(_PREFIX_TABLE."data_supplier")->result();
		$field['seloSupplier'] 		= $this->self_model->return_build_select2me($oSupplier,'','','','id_supplier','id_supplier','','','id','','','code');
		$field['txtremark'] 		= $this->self_model->return_build_txtarea('','remark','remark','2','','','');
		$oWarehouse 				= $this->db->select('id,description,kodepos')->get(_PREFIX_TABLE."data_warehouse_address")->result();
		$field['seloWarehouse'] 	= $this->self_model->return_build_select2me($oWarehouse,'','','','id_warehouse','id_warehouse','','','id','description','-','kodepos');
		$field['txttopdp'] 			= $this->self_model->return_build_txt('','top_dp','top_dp','','');
		$field['txttopt1'] 			= $this->self_model->return_build_txt('','top_t1','top_t1','','');
		$field['txttopt2'] 			= $this->self_model->return_build_txt('','top_t2','top_t2','','');
		$field['txttopt3'] 			= $this->self_model->return_build_txt('','top_t3','top_t3','','');
		$field['txttoptf'] 			= $this->self_model->return_build_txt('','top_tf','top_tf','','');
		$field['txttoprt'] 			= $this->self_model->return_build_txt('','top_rt','top_rt','','');
		$field['radioapproval'] 	= $this->self_model->return_build_radio('0', [[0,'Prepared'],[1,'Approved']], 'approval', '', 'inline');
		// $field['txtterm'] 			= $this->self_model->return_build_txtarea('','term_condition','term_condition','4','ckeditor','','data-error-container="#term_condition"');
		$field['txtterm'] 			= $this->self_model->return_build_txtarea('','term_condition','term_condition','4','txteditor','','');
		$field['worth'] 			= $this->self_model->return_build_txt('0','worth','worth','','','readonly');
		// readonly auto populate by select option - no data save
		$field['txtnamasupplier'] 	= $this->self_model->return_build_txt('','supplier_name','supplier_name','','','readonly');
		$field['txtalamatsupplier'] = $this->self_model->return_build_txtarea('','supplier_address','supplier_address','4','','','readonly');
		$field['txtpic'] 			= $this->self_model->return_build_txt('','pic','pic','','','readonly');
		$field['txtalamatwarehouse'] 		= $this->self_model->return_build_txtarea('','warehouse_address','warehouse_address','4','','','readonly');
		
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

	// Get po info
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

	// Get warehouse info
	public function getwarehouseinfo()
	{
		if(_USER_ACCESS_LEVEL_VIEW == "1") {
			$post = $this->input->post();
			if(isset($post['sel'])){
				$id = trim($post['sel']);
				echo json_encode($this->self_model->getWarehouseInfo($id));
			}
		} else {
			$this->load->view('errors/html/error_hacks_401');
		}
	}

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

}