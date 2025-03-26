<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dokter_menu extends MY_Controller
{
	/* Module */
 	const  LABELMODULE				= "setup_dokter_menu"; // identify menu
 	const  LABELMASTER				= "Menu Setup Dokter";
 	const  LABELFOLDER				= "setup"; // module folder
 	const  LABELPATH				= "dokter_menu"; // controller file (lowercase)
 	const  LABELNAVSEG1				= "setup"; // adjusted 1st sub parent segment
 	const  LABELSUBPARENTSEG1		= "Dokter"; // 
 	const  LABELNAVSEG2				= ""; // adjusted 2nd sub parent segment
 	const  LABELSUBPARENTSEG2		= ""; // 
	
	/* View */
	public $icon 					= 'fa-database';
	public $tabel_header 			= ["ID","Poli","Spesialis","Nama","Tgl Join"];
	
	/* Export */
	public $colnames 				= ["ID","Poli","Spesialis","Nama","Tgl Join"];
	public $colfields 				= ["id","nama","id"];

	/* Form Field Asset */
	public function form_field_asset()
	{
		$field = [];
		$field['txtspesialis'] = $this->self_model->return_build_txt('','spesialis','spesialis');
		$field['txtnama'] 	= $this->self_model->return_build_txt('','nama','nama');
		$field['txttgljoin'] = $this->self_model->return_build_txtdate('','tgljoin','tgljoin');
		$field['txtfoto'] = $this->self_model->return_build_fileinput('foto','foto');

		$mspoli = $this->db->query("select * from poli")->result(); 
		$field['selpoli'] 	= $this->self_model->return_build_select2me($mspoli,'','','','poli','poli','','','id','nama',' ','','','',3,'-');
		
		
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


	public function delrowJadwal(){ 
		$post = $this->input->post(); 
		$id = trim($post['id']); 
		
		if($id != ''){
			$rs = $this->db->delete('jadwal_dokter',"id = '".$id."'");
		}
		
	}









}


