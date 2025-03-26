<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Data_supplier extends MY_Controller
{
	/* Costs */
 	const  LABELMODULE				= "purchasing_data_supplier"; // identify menu
 	const  LABELMASTER				= "Supplier";
 	const  LABELFOLDER				= "purchasing"; // module folder
 	const  LABELPATH				= "data_supplier"; // controller file (lowercase)
 	const  LABELNAVSEG1				= ""; // adjusted 1st sub parent segment
 	const  LABELSUBPARENTSEG1		= ""; // 
 	const  LABELNAVSEG2				= ""; // adjusted 2nd sub parent segment
 	const  LABELSUBPARENTSEG2		= ""; // 
	
	/* View */
	public $icon 					= 'fa-database';
	public $tabel_header 			= ["ID","ID Supplier","Nama","Contact","Telp","Email","Status"];
	
	/* Export */
	public $colnames 				= ["ID","ID Supplier","Nama","Alamat","Contact","Telp","Email","NIB","NPWP","Bank","Rekening","Status"];
	public $colfields 				= ["id","code","name","address","contact_name","contact_phone","contact_email","nib","npwp","bank","rek","status"];

	/* Form Field Asset */
	public function form_field_asset()
	{
		$field = [];
		$field['txtcode'] 			= $this->self_model->return_build_txt('','code','code');
		$field['txtname'] 			= $this->self_model->return_build_txt('','name','name');
		$field['txtaddress'] 		= $this->self_model->return_build_txtarea('','address','address');
		// BOF contact
		$field['datacontact'] 		= '';
		$field['txtcontactname'] 	= $this->self_model->return_build_txt('','contact_name','contact_name');
		$field['txtcontactnik'] 	= $this->self_model->return_build_txt('','contact_nik','contact_nik');
		$field['filecontactnik'] 	= $this->self_model->return_build_fileinput('contact_filenik','contact_filenik', 'contact_filenik', '', '', '');
		$field['txtcontactaddress'] = $this->self_model->return_build_txtarea('','contact_address','contact_address');
		$field['txtcontactphone'] 	= $this->self_model->return_build_txt('','contact_phone','contact_phone');
		$field['txtcontactemail'] 	= $this->self_model->return_build_txt('','contact_email','contact_email');
		// EOF contact
		// BOF pic
		$field['datapic'] 			= '';
		$field['txtpicname'] 		= $this->self_model->return_build_txt('','pic_name','pic_name');
		$field['txtpicnik'] 		= $this->self_model->return_build_txt('','pic_nik','pic_nik');
		$field['filepicnik'] 		= $this->self_model->return_build_fileinput('pic_filenik','pic_filenik', 'pic_filenik', '', '', '');
		$field['txtpicaddress'] 	= $this->self_model->return_build_txtarea('','pic_address','pic_address');
		$field['txtpicphone'] 		= $this->self_model->return_build_txt('','pic_phone','pic_phone');
		$field['txtpicemail'] 		= $this->self_model->return_build_txt('','pic_email','pic_email');
		// EOF pic
		$field['txtnib'] 			= $this->self_model->return_build_txt('','nib','nib');
		$oIndustry 					= $this->db->select('id,description')->order_by('idx_seq', 'ASC')->where("active='1'")->get(_PREFIX_TABLE."option_industrial_type")->result();
		$field['seloIndustry'] 		= $this->self_model->return_build_checkboxselect($oIndustry,'','','industry[]','','','id','description');
		$oBank 						= $this->db->select('id,code,description')->order_by('code', 'ASC')->where("active='1'")->get(_PREFIX_TABLE."option_bank")->result();
		$field['seloBank'] 			= $this->self_model->return_build_select($oBank,'','','','id_bank','id_bank','','','id','code','-','description');
		$field['txtrek'] 			= $this->self_model->return_build_txt('','rek','rek');
		$field['txtnpwp'] 			= $this->self_model->return_build_txt('','npwp','npwp');
		$oDocattach					= $this->db->select('id,description')->order_by('idx_seq', 'ASC')->where("active='1'")->get(_PREFIX_TABLE."option_doc_attachment")->result();
		$field['datadocument'] 		= $this->self_model->return_build_filesetselect($oDocattach,'','','doc','','id','description');
		$oStatus 					= $this->db->select('id,description')->order_by('idx_seq', 'ASC')->where("active='1'")->get(_PREFIX_TABLE."option_general_status")->result();
		$field['seloStatus'] 		= $this->self_model->return_build_select($oStatus,'','','','id_status','id_status','','','id','description');
		
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
}