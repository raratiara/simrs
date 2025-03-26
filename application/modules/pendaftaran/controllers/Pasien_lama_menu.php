<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pasien_lama_menu extends MY_Controller
{
	/* Costs */
 	const  LABELMODULE				= "pendaftaran_pasien_lama_menu"; // identify menu
 	const  LABELMASTER				= "Pendaftaran Pasien Lama";
 	const  LABELFOLDER				= "pendaftaran"; // module folder
 	const  LABELPATH				= "Pasien_lama_menu"; // controller file (lowercase)
 	const  LABELNAVSEG1				= "Pendaftaran"; // adjusted 1st sub parent segment
 	const  LABELSUBPARENTSEG1		= ""; // 
 	const  LABELNAVSEG2				= ""; // adjusted 2nd sub parent segment
 	const  LABELSUBPARENTSEG2		= ""; // 
	
	/* View */
	public $icon 					= 'fa-database';
	public $tabel_header 			= ["ID","Tgl Pendaftaran","Tgl Jadwal Pemeriksaan","Jam Jadwal Pemeriksaan","No Urut","Poli","Dokter","Jenis Pembayaran","Status","Pasien"];
	
	/* Export */
	public $colnames 				= ["ID","Tgl Pendaftaran","Tgl Jadwal Pemeriksaan","Jam Jadwal Pemeriksaan","No Urut","Poli","Dokter","Jenis Pembayaran","Status","Pasien"];
	public $colfields 				= ["id","id","id","id","id","id","id","id","id","id"];

	/* Form Field Asset */
	public function form_field_asset()
	{
		// BOF Only for user role
		$sRole = $this->session->userdata('role'); // role
		$sWorker = $this->session->userdata('worker'); // worker data id
		// EOF Only for user role

		$field = [];

		$field['txtnorm'] 			= $this->self_model->return_build_txt('','no_rm','no_rm','','','');
		$field['txtnamapasien'] 	= $this->self_model->return_build_txt('','nama_pasien','nama_pasien','','','readonly');
		$field['txttgllahir'] 		= $this->self_model->return_build_txtdate('','tgl_lahir','tgl_lahir','','');
		$field['rdojenispembayaran']= $this->self_model->return_build_radio('', [['Pribadi','Pribadi'],['BPJS','BPJS'],['Asuransi','Asuransi']], 'jenis_pembayaran', '', 'inline');
		$field['txtalamat'] 		= $this->self_model->return_build_txtarea('','alamat','alamat','','','','readonly');
		$field['txtalamat_no'] 		= $this->self_model->return_build_txt('','alamat_no','alamat_no','','','readonly');
		$field['txtalamat_rt'] 		= $this->self_model->return_build_txt('','alamat_rt','alamat_rt','','','readonly');
		$field['txtalamat_rw'] 		= $this->self_model->return_build_txt('','alamat_rw','alamat_rw','','','readonly');

		$field['txtprov'] 		= $this->self_model->return_build_txt('','prov','prov','','','readonly');
		$field['txtkec'] 		= $this->self_model->return_build_txt('','kec','kec','','','readonly');
		$field['txtkota'] 		= $this->self_model->return_build_txt('','kota','kota','','','readonly');
		$field['txtkel'] 		= $this->self_model->return_build_txt('','kel','kel','','','readonly');

		$msprov 			= $this->db->query("select * from provinsi")->result(); 
		$field['selprov'] 	= $this->self_model->return_build_select2me($msprov,'','','','prov','prov','','','id','name',' ','','','',3,'-');
		$mskec 				= $this->db->query("select * from kec")->result(); 
		$field['selkec'] 	= $this->self_model->return_build_select2me($mskec,'','','','kec','kec','','','id','name',' ','','','',3,'-');
		$mskab 				= $this->db->query("select * from kabkota")->result(); 
		$field['selkota'] 	= $this->self_model->return_build_select2me($mskab,'','','','kota','kota','','','id','name',' ','','','',3,'-');
		$mskel 				= $this->db->query("select * from kelurahan")->result(); 
		$field['selkel'] 	= $this->self_model->return_build_select2me($mskel,'','','','kel','kel','','','id','name',' ','','','',3,'-');
		$mspoli 			= $this->db->query("select * from poli")->result(); 
		$field['selpoli'] 	= $this->self_model->return_build_select2me($mspoli,'','','','poli','poli','','','id','nama',' ','','','',3,'-');
		$msdokter 			= $this->db->query("select * from dokter")->result(); 
		$field['seldokter'] = $this->self_model->return_build_select2me($msdokter,'','','','dokter','dokter','','','id','nama',' ','','','',3,'-');

		
		
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

	// Get Row Data
	public function get_data_by_norm()
	{ 
		if(_USER_ACCESS_LEVEL_VIEW == "1") {
			$post = $this->input->post();
			$no_rm = trim($post['no_rm']);
			$data = FALSE;
			if ($no_rm != '')
			{ 
				$data = $this->self_model->getRowData_bynorm($no_rm);
			}

			echo json_encode($data);
		} else {
			$this->load->view('errors/html/error_hacks_401');
		}
	}
	

}