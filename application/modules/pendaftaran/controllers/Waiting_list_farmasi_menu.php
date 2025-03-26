<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Waiting_list_farmasi_menu extends MY_Controller
{
	/* Costs */
 	const  LABELMODULE				= "pendaftaran_wl_farmasi_menu"; // identify menu
 	const  LABELMASTER				= "Waiting List Farmasi";
 	const  LABELFOLDER				= "pendaftaran"; // module folder
 	const  LABELPATH				= "Waiting_list_farmasi_menu"; // controller file (lowercase)
 	const  LABELNAVSEG1				= "Pendaftaran"; // adjusted 1st sub parent segment
 	const  LABELSUBPARENTSEG1		= ""; // 
 	const  LABELNAVSEG2				= ""; // adjusted 2nd sub parent segment
 	const  LABELSUBPARENTSEG2		= ""; // 
	
	/* View */
	public $icon 					= 'fa-database';
	public $tabel_header 			= ["No","No Urut","No RM","Nama Pasien","Poli","Dokter","Status"];
	
	/* Export */
	public $colnames 				= ["No Urut","No RM","Nama Pasien","Poli","Dokter","Status"];
	public $colfields 				= ["no_urut","no_rekam_medis","pasien","poli","dokter","status_name"];

	/* Form Field Asset */
	public function form_field_asset()
	{
		// BOF Only for user role
		$sRole = $this->session->userdata('role'); // role
		$sWorker = $this->session->userdata('worker'); // worker data id
		// EOF Only for user role

		$field = [];

		$field['txtnorm'] 		= $this->self_model->return_build_txt('','no_rm','no_rm','','','readonly');
		$field['txtnamapasien'] = $this->self_model->return_build_txt('','nama_pasien','nama_pasien','','','readonly');
		$field['txttgllahir'] 	= $this->self_model->return_build_txt('','tgl_lahir','tgl_lahir','','','readonly');
		$msstatusfarmasi 		= $this->db->query("select * from status_farmasi")->result(); 
		$field['selstatus'] 	= $this->self_model->return_build_select2me($msstatusfarmasi,'','','','status_farmasi','status_farmasi','','','id','nama',' ','','','',3,'-');
		$field["data_resep"] = $this->db->query("select b.kode as kode_obat, b.nama as nama_obat, a.qty, b.satuan_id from resep a left join obat b on b.id = a.obat_id where a.diagnosa_id = 1")->result(); 

		
		
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
		//define('_USER_ACCESS_LEVEL_EKSPORT',0);
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