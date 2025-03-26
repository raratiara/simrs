<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pasien_baru_menu extends MY_Controller
{
	/* Costs */
 	const  LABELMODULE				= "pendaftaran_pasien_baru_menu"; // identify menu
 	const  LABELMASTER				= "Pendaftaran Pasien Baru";
 	const  LABELFOLDER				= "pendaftaran"; // module folder
 	const  LABELPATH				= "Pasien_baru_menu"; // controller file (lowercase)
 	const  LABELNAVSEG1				= "Pendaftaran"; // adjusted 1st sub parent segment
 	const  LABELSUBPARENTSEG1		= ""; // 
 	const  LABELNAVSEG2				= ""; // adjusted 2nd sub parent segment
 	const  LABELSUBPARENTSEG2		= ""; // 
	
	/* View */
	public $icon 					= 'fa-database';
	public $tabel_header 			= ["ID","No RM","Nama Pasien","Tgl Lahir","Alamat","No HP","No BPJS"];
	
	/* Export */
	public $colnames 				= ["ID","No RM","Nama Pasien","Tgl Lahir","Alamat","No HP","No BPJS"];
	public $colfields 				= ["id","id","id","id","id","id","id"];

	/* Form Field Asset */
	public function form_field_asset()
	{
		// BOF Only for user role
		$sRole = $this->session->userdata('role'); // role
		$sWorker = $this->session->userdata('worker'); // worker data id
		// EOF Only for user role

		$field = [];

		$field['txtnorm'] 			= $this->self_model->return_build_txt('','no_rm','no_rm','','','readonly');
		$field['txtnamapasien'] 	= $this->self_model->return_build_txt('','nama_pasien','nama_pasien','','','');
		$field['txttempatlahir'] 	= $this->self_model->return_build_txt('','tempat_lahir','tempat_lahir','','','');
		$field['txttgllahir'] 		= $this->self_model->return_build_txtdate('','tgl_lahir','tgl_lahir','','');
		$field['rdojeniskelamin'] 	= $this->self_model->return_build_radio('', [['L','Laki-Laki'],['P','Perempuan']], 'jenis_kelamin', '', 'inline');
		$msagama 					= $this->db->query("select * from agama")->result(); 
		$field['selagama'] 			= $this->self_model->return_build_select2me($msagama,'','','','agama','agama','','','id','nama',' ','','','',3,'-');
		$mspendidikan 				= $this->db->query("select * from pendidikan")->result(); 
		$field['selpendidikan'] 	= $this->self_model->return_build_select2me($mspendidikan,'','','','pendidikan','pendidikan','','','id','nama',' ','','','',3,'-');
		$msstatuskawin 				= $this->db->query("select * from status_kawin")->result(); 
		$field['selstatuskawin'] 	= $this->self_model->return_build_select2me($msstatuskawin,'','','','status_kawin','status_kawin','','','id','nama',' ','','','',3,'-');
		$field['txtnohp'] 			= $this->self_model->return_build_txt('','no_hp','no_hp','','','');
		$field['txtpekerjaan'] 		= $this->self_model->return_build_txt('','pekerjaan','pekerjaan','','','');
		$field['txtemail'] 			= $this->self_model->return_build_txt('','email','email','','','');
		$field['txtalamattinggal'] 	= $this->self_model->return_build_txtarea('','alamat_tinggal','alamat_tinggal');
		$field['txtnamaayah'] 		= $this->self_model->return_build_txt('','nama_ayah','nama_ayah','','','');
		$field['txtnamapasangan'] 	= $this->self_model->return_build_txt('','nama_pasangan','nama_pasangan','','','');
		$field['txtnamapenanggung'] = $this->self_model->return_build_txt('','nama_penanggung','nama_penanggung','','','');
		$field['txtnamaibu'] 		= $this->self_model->return_build_txt('','nama_ibu','nama_ibu','','','');
		$field['txtnohppasangan'] 	= $this->self_model->return_build_txt('','nohp_pasangan','nohp_pasangan','','','');
		$field['txtnohppenanggung'] = $this->self_model->return_build_txt('','nohp_penanggung','nohp_penanggung','','','');
		$field['chksda'] 			= $this->self_model->return_build_checkbox('','','sda','sda','','','');
		$field['txtnobpjs'] 		= $this->self_model->return_build_txt('','no_bpjs','no_bpjs','','','');
		$mstipeiden = $this->db->query("select * from tipe_identitas")->result(); 
		$field['seltipeidentitas'] 	= $this->self_model->return_build_select2me($mstipeiden,'','','','tipe_identitas','tipe_identitas','','','id','nama',' ','','','',3,'-');
		$field['txtnoidentitas'] 	= $this->self_model->return_build_txt('','no_identitas','no_identitas','','','');
		$field['txtfoto_bpjs'] = $this->self_model->return_build_fileinput('foto_bpjs','foto_bpjs');
		$field['txtfoto_identitas'] = $this->self_model->return_build_fileinput('foto_identitas','foto_identitas');


		$field['txtalamattinggal_no'] 		= $this->self_model->return_build_txt('','alamat_tinggal_no','alamat_tinggal_no','','','');
		$field['txtalamattinggal_rt'] 		= $this->self_model->return_build_txt('','alamat_tinggal_rt','alamat_tinggal_rt','','','');
		$field['txtalamattinggal_rw'] 		= $this->self_model->return_build_txt('','alamat_tinggal_rw','alamat_tinggal_rw','','','');
		$msprov = $this->db->query("select * from provinsi")->result(); 
		$field['selprov_alamattinggal'] 	= $this->self_model->return_build_select2me($msprov,'','','','prov_tempattinggal','prov_tempattinggal','','','id','name',' ','','','',3,'-');
		$mskec = $this->db->query("select * from kec")->result(); 
		$field['selkec_alamattinggal'] 		= $this->self_model->return_build_select2me($mskec,'','','','kec_tempattinggal','kec_tempattinggal','','','id','name',' ','','','',3,'-');
		$mskab = $this->db->query("select * from kabkota")->result(); 
		$field['selkota_alamattinggal'] 	= $this->self_model->return_build_select2me($mskab,'','','','kota_tempattinggal','kota_tempattinggal','','','id','name',' ','','','',3,'-');
		$mskel = $this->db->query("select * from kelurahan")->result(); 
		$field['selkel_alamattinggal'] 		= $this->self_model->return_build_select2me($mskel,'','','','kel_tempattinggal','kel_tempattinggal','','','id','name',' ','','','',3,'-');
		$field['txtalamatidentitas'] 		= $this->self_model->return_build_txtarea('','alamat_identitas','alamat_identitas');
		$field['txtalamatidentitas_no'] 	= $this->self_model->return_build_txt('','alamat_identitas_no','alamat_identitas_no','','','');
		$field['txtalamatidentitas_rt'] 	= $this->self_model->return_build_txt('','alamat_identitas_rt','alamat_identitas_rt','','','');
		$field['txtalamatidentitas_rw'] 	= $this->self_model->return_build_txt('','alamat_identitas_rw','alamat_identitas_rw','','','');
		$field['selprov_alamatidentitas'] 	= $this->self_model->return_build_select2me($msprov,'','','','prov_tempatidentitas','prov_tempatidentitas','','','id','name',' ','','','',3,'-');
		$field['selkec_alamatidentitas'] 	= $this->self_model->return_build_select2me($mskec,'','','','kec_tempatidentitas','kec_tempatidentitas','','','id','name',' ','','','',3,'-');
		$field['selkota_alamatidentitas'] 	= $this->self_model->return_build_select2me($mskab,'','','','kota_tempatidentitas','kota_tempatidentitas','','','id','name',' ','','','',3,'-');
		$field['selkel_alamatidentitas'] 	= $this->self_model->return_build_select2me($mskel,'','','','kel_tempatidentitas','kel_tempatidentitas','','','id','name',' ','','','',3,'-');

		
		
		
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