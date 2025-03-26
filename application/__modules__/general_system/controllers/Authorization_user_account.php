<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Authorization_user_account extends MY_Controller
{
	/* Costs */
 	const  LABELMODULE				= "general_system_authorization_user_account"; // identify menu
 	const  LABELMASTER				= "User Account";
 	const  LABELFOLDER				= "general_system"; // module folder
 	const  LABELPATH				= "authorization_user_account"; // controller file (lowercase)
 	const  LABELNAVSEG1				= "general_system_authorization"; // adjusted 1st sub parent segment
 	const  LABELSUBPARENTSEG1		= "Authorization"; // 
 	const  LABELNAVSEG2				= ""; // adjusted 2nd sub parent segment
 	const  LABELSUBPARENTSEG2		= ""; // 
	
	/* View */
	public $icon 					= 'fa-database';
	public $tabel_header 			= ["ID","Name","Username","Email","Role","Base Menu","Active"];
	
	/* Export */
	public $colnames 				= ["ID","Name","Username","Email","Role","Base Menu","Active"];
	public $colfields 				= ["user_id","name","username","email","role","bmenu","is_aktif"];

	/* Form Field Asset */
	public function form_field_asset()
	{
		$field = [];
		$field['txtname'] 		= $this->self_model->return_build_txt('','name','name');
		$oRole 					= $this->db->select('id,name')->order_by('name', 'ASC')->get(_PREFIX_TABLE."user_group")->result();
		$field['selrole'] 		= $this->self_model->return_build_select2me($oRole,'','','','id_groups','id_groups','','','id','name');
		$oKaryawan 				= $this->db->select('a.id,a.name,b.description')->join(_PREFIX_TABLE."option_jabatan".' b','b.id=a.id_jabatan','left')->order_by('a.name', 'ASC')->get(_PREFIX_TABLE."data_karyawan a")->result();
		$field['selkaryawan'] 	= $this->self_model->return_build_select2me($oKaryawan,'','','','id_karyawan','id_karyawan','','','id','name','-','description');
		$field['txtusername'] 	= $this->self_model->return_build_txt('','username','username');
		$field['txtemail'] 		= $this->self_model->return_build_txt('','email','email');
		$field['txtpasswd'] 	= $this->self_model->return_build_txtpasswd('','passwd','passwd');
		$field['txtrepasswd'] 	= $this->self_model->return_build_txtpasswd('','repasswd','repasswd');
		$field['radioactive'] 	= $this->self_model->return_build_radio('2', [[2,'Aktif'],[3,'Non Aktif']], 'isaktif');
		$field['radiomenu'] 	= $this->self_model->return_build_radio('role', [['role','Role'],['custom','User']], 'base_menu');
		
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
	// For register new username
	public function check_user()
	{
		$output = false;
		if(_USER_ACCESS_LEVEL_VIEW == "1")
		{
			$post = $this->input->get(null, true);
			if(isset($post['username']))
			{
				$sql = "SELECT * FROM "._PREFIX_TABLE."user WHERE username = ?"; // cek user
				$auser = $this->db->query($sql, [ $post['username'] ])->num_rows();
				if($auser == 0){
					$output = true;
				}
			}
		} else {
			$this->load->view('errors/html/error_hacks_401');
		}

		echo json_encode($output);
	}   	
}