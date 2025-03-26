<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Office_rs_menu extends MY_Controller
{
	/* Module */
 	const  LABELMODULE				= "setup_office_rs_menu"; // identify menu
 	const  LABELMASTER				= "Menu Setup Rumah Sakit";
 	const  LABELFOLDER				= "setup"; // module folder
 	const  LABELPATH				= "office_rs_menu"; // controller file (lowercase)
 	const  LABELNAVSEG1				= "setup"; // adjusted 1st sub parent segment
 	const  LABELSUBPARENTSEG1		= "Office"; // 
 	const  LABELNAVSEG2				= ""; // adjusted 2nd sub parent segment
 	const  LABELSUBPARENTSEG2		= ""; // 
	
	/* View */
	public $icon 					= 'fa-database';
	public $tabel_header 			= ["ID","Rumah Sakit","Alamat","Provinsi","Kab/Kota","Kecamatan","Kode Pos","Telp","Fax","Email","Website"];
	
	/* Export */
	public $colnames 				= ["ID","Rumah Sakit","Alamat","Provinsi","Kab/Kota","Kecamatan","Kode Pos","Telp","Fax","Email","Website"];
	public $colfields 				= ["id","provinsi","kota","kecamatan"];

	/* Form Field Asset */
	public function form_field_asset()
	{
		$field = [];
		$field['txtnamars'] 		= $this->self_model->return_build_txt('','nama_rs','nama_rs');
		$field['txtalamat'] 		= $this->self_model->return_build_txt('','alamat','alamat');
		$field['txtkodepos'] 		= $this->self_model->return_build_txt('','kodepos','kodepos');
		$field['txttelp'] 		= $this->self_model->return_build_txt('','telp','telp');
		$field['txtfax'] 		= $this->self_model->return_build_txt('','fax','fax');
		$field['txtweb'] 		= $this->self_model->return_build_txt('','web','web');
		$field['txtlogo'] 		= $this->self_model->return_build_txt('','logo','logo');
		$field['txtemail'] 		= $this->self_model->return_build_txt('','email','email');

		
		$msprov = $this->db->query("select * from provinsi")->result(); 
		$field['selprovinsi'] 	= $this->self_model->return_build_select2me($msprov,'','','','provinsi','provinsi','','','id','name',' ','','','',3,'-');
		$mskota = $this->db->query("select * from kabkota")->result(); 
		$field['selkota'] 	= $this->self_model->return_build_select2me($mskota,'','','','kota','kota','','','id','name',' ','','','',3,'-');
		$mskec = $this->db->query("select * from kec")->result(); 
		$field['selkec'] 	= $this->self_model->return_build_select2me($mskec,'','','','kecamatan','kecamatan','','','id','name',' ','','','',3,'-');
		
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
}
