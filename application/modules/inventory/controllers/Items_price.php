<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Items_price extends MY_Controller
{
	/* Costs */
 	const  LABELMODULE				= "inventory_items_price"; // identify menu
 	const  LABELMASTER				= "Item Pricing";
 	const  LABELFOLDER				= "inventory"; // module folder
 	const  LABELPATH				= "items_price"; // controller file (lowercase)
 	const  LABELNAVSEG1				= ""; // adjusted 1st sub parent segment
 	const  LABELSUBPARENTSEG1		= ""; // 
 	const  LABELNAVSEG2				= ""; // adjusted 2nd sub parent segment
 	const  LABELSUBPARENTSEG2		= ""; // 
	
	/* View */
	public $icon 					= 'fa-database';
	public $tabel_header 			= ["ID","SKU","Name","Type","Effective Date","Purchase Price","Sell Price"];
	
	/* Export */
	public $colnames 				= ["ID","SKU","Name","Type","Effective Date","Purchase Price","Sell Price"];
	public $colfields 				= ["id","sku","name","type","deffective","wpurchase","wsell"];

	/* Form Field Asset */
	public function form_field_asset()
	{
		$field = [];
		$oItem 						= $this->db->order_by('id', 'ASC')->where("active='1'")->get(_PREFIX_TABLE."data_inventory_item")->result();
		$field['seloItem'] 			= $this->self_model->return_build_select2me($oItem,'','','','id_item','id_item','','','id','sku','-','name');
		$field['txtdateeffective'] 	= $this->self_model->return_build_txtdate('','date_effective','date_effective');
		$field['txtpricepurchase'] 	= $this->self_model->return_build_txt('0,00','price_purchase','price_purchase');
		$field['txtpricesell'] 		= $this->self_model->return_build_txt('0,00','price_sell','price_sell');
		$field['txtareanote'] 		= $this->self_model->return_build_txtarea('','note','note');
		//$field['radiocancel'] 		= $this->self_model->return_build_radio('0', [[1,'Yes'],[0,'No']], 'canceled', '', 'inline');
		
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