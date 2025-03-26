<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sadmin_tools_app_menu extends MY_Controller
{
	/* Module */
 	const  LABELMODULE				= "general_system_sadmin_tools_app_menu"; // identify menu
 	const  LABELMASTER				= "Application Menu";
 	const  LABELFOLDER				= "general_system"; // module folder
 	const  LABELPATH				= "sadmin_tools_app_menu"; // controller file (lowercase)
 	const  LABELNAVSEG1				= "general_system_sadmin"; // adjusted 1st sub parent segment
 	const  LABELSUBPARENTSEG1		= "Super Admin Tools"; // 
 	const  LABELNAVSEG2				= ""; // adjusted 2nd sub parent segment
 	const  LABELSUBPARENTSEG2		= ""; // 
	
	/* View */
	public $icon 					= 'fa-database';
	public $tabel_header 			= ["ID","Title Menu","Module Name","Url","Parent ID","Is Parent","Show Menu","Order"];
	
	/* Export */
	public $colnames 				= ["ID","Title Menu","Link Type","Module Name","Url","Parent ID","Is Parent","Show Menu","Icon Class","Order"];
	public $colfields 				= ["user_menu_id","title","link_type","module_name","url","parent_id","isparent","isshow","um_class","um_order"];

	/* Form Field Asset */
	public function form_field_asset()
	{
		$field = [];
		$field['txttitle'] 		= $this->self_model->return_build_txt('','title','title');
		$field['radiolink'] 	= $this->self_model->return_build_radio('uri', [['page','page'],['uri','uri']], 'link_type', 'link_type', 'inline');
		$field['txtmodule'] 	= $this->self_model->return_build_txt('','module_name','module_name');
		$field['txturl'] 		= $this->self_model->return_build_txt('','url','url');
		$oMenu 					= $this->self_model->return_build_tree(_PREFIX_TABLE."user_menu", 'user_menu_id', 'title', 'parent_id','', 'um_order');
		$field['selparent'] 	= $this->self_model->return_build_select2me($oMenu,'','','','parent_id','parent_id','','','user_menu_id','user_menu_id',' ','title','','',3,'-');
		$field['radioparent'] 	= $this->self_model->return_build_radio('0', [['0','No'],['1','Yes']], 'is_parent', 'is_parent', 'inline');
		$field['radioshow'] 	= $this->self_model->return_build_radio('1', [['1','Yes'],['0','No']], 'show_menu', 'show_menu', 'inline');
		$oUmClass				= ['fa-dashboard','fa-tachometer','fa-exchange','fa-shopping-cart','fa-archive','fa-money','fa-list','fa-list-alt','fa-gavel','fa-handshake-o'];
		$field['selum_class'] 	= $this->self_model->return_build_simple_select2me($oUmClass,'',[],'um_class','um_class');
		$field['txtorder'] 		= $this->self_model->return_build_txt('0','um_order','um_order');
		
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
