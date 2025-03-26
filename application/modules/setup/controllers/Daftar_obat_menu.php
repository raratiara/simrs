<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Daftar_obat_menu extends MY_Controller
{
	/* Module */
 	const  LABELMODULE				= "setup_daftar_obat_menu"; // identify menu
 	const  LABELMASTER				= "Menu Setup Daftar Obat";
 	const  LABELFOLDER				= "setup"; // module folder
 	const  LABELPATH				= "daftar_obat_menu"; // controller file (lowercase)
 	const  LABELNAVSEG1				= "setup"; // adjusted 1st sub parent segment
 	const  LABELSUBPARENTSEG1		= "Daftar Obat"; // 
 	const  LABELNAVSEG2				= ""; // adjusted 2nd sub parent segment
 	const  LABELSUBPARENTSEG2		= ""; // 
	
	/* View */
	public $icon 					= 'fa-database';
	public $tabel_header 			= ["ID","Kategori","Kode","Satuan","Harga","Qty Awal","Qty On Hand","Min Qty"];
	
	/* Export */
	public $colnames 				= ["ID","Kategori","Kode","Satuan","Harga","Qty Awal","Qty On Hand","Min Qty"];
	public $colfields 				= ["id","nama","id"];

	/* Form Field Asset */
	public function form_field_asset()
	{
		$field = [];
		$field['txtkode'] = $this->self_model->return_build_txt('','kode','kode');
		$field['txtnama'] 	= $this->self_model->return_build_txt('','nama','nama');
		$field['txtharga'] = $this->self_model->return_build_txt('','harga','harga');
		$field['txtqtyawal'] = $this->self_model->return_build_txt('','qty_awal','qty_awal');
		$field['txtqtyonhand'] = $this->self_model->return_build_txt('','qty_onhand','qty_onhand');
		$field['txtminqty'] = $this->self_model->return_build_txt('','min_qty','min_qty');

		$mskategori = $this->db->query("select * from option_kategori_obat")->result(); 
		$field['selkategori'] 	= $this->self_model->return_build_select2me($mskategori,'','','','kategori','kategori','','','id','nama',' ','','','',3,'-');
		$mssatuan = $this->db->query("select * from option_uom")->result(); 
		$field['selsatuan'] 	= $this->self_model->return_build_select2me($mssatuan,'','','','satuan','satuan','','','id','description',' ','','','',3,'-');
		
		
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
