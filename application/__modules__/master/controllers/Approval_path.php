<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Approval_path extends MY_Controller
{
	/* Costs */
 	const  LABELMODULE				= "master_approval_path"; // identify menu
 	const  LABELMASTER				= "Approval Path";
 	const  LABELFOLDER				= "master"; // module folder
 	const  LABELPATH				= "approval_path"; // controller file (lowercase)
 	const  LABELNAVSEG1				= ""; // adjusted 1st sub parent segment
 	const  LABELSUBPARENTSEG1		= ""; // 
 	const  LABELNAVSEG2				= ""; // adjusted 2nd sub parent segment
 	const  LABELSUBPARENTSEG2		= ""; // 
	
	/* View */
	public $icon 					= 'fa-database';
	public $tabel_header 			= ["ID","Menu","Approval","Member","Active"];
	
	/* Export */
	public $colnames 				= ["ID","Menu","Approval","Member","Active"];
	public $colfields 				= ["id","menu","approval","member","is_active"];

	/* Form Field Asset */
	public function form_field_asset()
	{
		$field = [];
		$oMenu 						= $this->db->select('user_menu_id,title')->order_by('title', 'ASC')->where("show_menu='1' AND user_menu_id IN ('69','71','72')")->get(_PREFIX_TABLE."user_menu")->result();
		$field['seloMenu'] 			= $this->self_model->return_build_select2me($oMenu,'','','','id_menu','id_menu','','','user_menu_id','title');
		$oApproval 					= [];
		$field['seloApproval'] 		= $this->self_model->return_build_select2me($oApproval,'','','','id_approval','id_approval','','','id','description');
		$field['radioactive'] 		= $this->self_model->return_build_radio('1', [[1,'Yes'],[0,'No']], 'active', '', 'inline');
		
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
}