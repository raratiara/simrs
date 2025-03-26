<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Waiting_list_kasir_menu extends MY_Controller
{
	/* Costs */
 	const  LABELMODULE				= "pendaftaran_wl_kasir_menu"; // identify menu
 	const  LABELMASTER				= "Waiting List Kasir";
 	const  LABELFOLDER				= "pendaftaran"; // module folder
 	const  LABELPATH				= "Waiting_list_kasir_menu"; // controller file (lowercase)
 	const  LABELNAVSEG1				= "Pendaftaran"; // adjusted 1st sub parent segment
 	const  LABELSUBPARENTSEG1		= ""; // 
 	const  LABELNAVSEG2				= ""; // adjusted 2nd sub parent segment
 	const  LABELSUBPARENTSEG2		= ""; // 
	
	/* View */
	public $icon 					= 'fa-database';
	public $tabel_header 			= ["No","No Urut","No RM","Nama Pasien","Poli","Dokter","Status"];
	
	/* Export */
	public $colnames 				= ["No","No Urut","No RM","Nama Pasien","Poli","Dokter","Status"];
	public $colfields 				= ["id","id","id","id","id","id","id","id"];

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
		
		$field['txttinggibadan'] = $this->self_model->return_build_txt('','tinggi_badan','tinggi_badan','','','readonly');
		$field['txtsuhutubuh'] 		= $this->self_model->return_build_txt('','suhu_tubuh','suhu_tubuh','','','readonly');
		$field['txttekanandarah'] 	= $this->self_model->return_build_txt('','tekanan_darah','tekanan_darah','','','readonly');
		$field['txtsaturasi'] 		= $this->self_model->return_build_txt('','saturasi','saturasi','','','readonly');
		
		$field['txtberatbadan'] 	= $this->self_model->return_build_txt('','berat_badan','berat_badan','','','readonly');
		$field['txtdenyutnadi'] 	= $this->self_model->return_build_txt('','denyut_nadi','denyut_nadi','','','readonly');
		$field['txtfrekuensinapas'] = $this->self_model->return_build_txt('','frekuensi_napas','frekuensi_napas','','','readonly');
		$field['txttingkatnyeri'] 	= $this->self_model->return_build_txt('','tingkat_nyeri','tingkat_nyeri','','','readonly');
		$field['rdometodepembayaran'] 	= $this->self_model->return_build_radio('', [['Tunai','Tunai'],['Kartu Kredit/Debit','Kartu Kredit/Debit'],['Transfer Bank','Transfer Bank'],['BPJS/Asuransi','BPJS/Asuransi']], 'metode_pembayaran', '', 'inline');

		$field['txtwawancara'] 	= $this->self_model->return_build_txtarea('','wawancara','wawancara','','','');
		$field['txtdiagnosa'] = $this->self_model->return_build_txtarea('','diagnosa','diagnosa','','','');
		
		/*$field['txtwawancara'] 	= $this->self_model->return_build_tetarea('','tgl_lahir','tgl_lahir','','','readonly');*/
		
		
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

	// Print
	public function print()
	{ 
		$pid = 1; 

		$this->data['rs'] = $this->self_model->getRowData_print($pid); 
		// Get item data
		//$this->data['rs2'] 	= $this->self_model->getInvoiceItemRows($pid,TRUE,TRUE);
		//$this->load->view($this->folder_name.'/print' , $this->data);
		echo $this->folder_name; die();
		$this->load->view($this->folder_name , $this->data);

		/*if(_USER_ACCESS_LEVEL_VIEW == "1" && _USER_ACCESS_LEVEL_DETAIL == "1") {
			if ($post = $this->input->post(null, true))
			{
				if(isset($post['pid'])){
					$pid = $post['pid'];
					// Get main data
					$this->data['rs'] = $this->self_model->getRowData($pid);
					// Get item data
					$this->data['rs2'] 	= $this->self_model->getInvoiceItemRows($pid,TRUE,TRUE);
					$this->load->view($this->folder_name.'/print' , $this->data);
				}
			}
		} else {
			$this->load->view('errors/html/error_hacks_401');
		}*/
	}
	

}