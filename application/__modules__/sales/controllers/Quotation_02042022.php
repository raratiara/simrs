<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Quotation extends MYP_Controller
{
	/* Costs */
 	const  LABELMODULE				= "sales_quotation"; // identify menu
 	const  LABELMASTER				= "Quotation";
 	const  LABELFOLDER				= "sales"; // module folder
 	const  LABELPATH				= "quotation"; // controller file (lowercase)
 	const  LABELNAVSEG1				= ""; // adjusted 1st sub parent segment
 	const  LABELSUBPARENTSEG1		= ""; // 
 	const  LABELNAVSEG2				= ""; // adjusted 2nd sub parent segment
 	const  LABELSUBPARENTSEG2		= ""; // 
	
	/* View */
	public $icon 					= 'fa-database';
	public $tabel_header 			= ["ID","No","Tanggal","Tujuan","Deskripsi","RAB","TTD"];
	
	/* Export */
	public $colnames 				= ["ID","No","Tanggal","Tujuan","Deskripsi","RAB","TTD","Ketentuan"];
	public $colfields 				= ["id","quotation","dquotation","customer","description","project_title","ttd","ketentuan"];

	/* Form Field Asset */
	public function form_field_asset()
	{
		$field = [];
		$field['txtdatequotation'] 	= $this->self_model->return_build_txtdate(date('d-m-Y'),'date_quotation','date_quotation');
		$field['txtquotation'] 		= $this->self_model->return_build_txt('Auto','quotation','quotation','','','readonly');
		$field['txttotalquote'] 	= $this->self_model->return_build_txt('0','total_quote','total_quote','','text-align: right;','readonly').$this->self_model->return_build_txthidden('0','ppn','ppn').$this->self_model->return_build_txthidden('0','pph','pph').$this->self_model->return_build_txthidden('0','other_tax','other_tax').$this->self_model->return_build_txthidden('0','grandtotal','grandtotal');
		$oRab 						= $this->db->select('a.id,b.project,b.title as project_title')
										->join(_PREFIX_TABLE.'data_project b', 'b.id = a.id_project')
										->order_by('a.id', 'ASC')->get(_PREFIX_TABLE."data_rab a")->result();
		$field['seloRab'] 			= $this->self_model->return_build_select2me($oRab,'','','','id_rab','id_rab','','','id','project');
		$oCustomer 					= $this->db->select('id,code,name')->order_by('code', 'ASC')->get(_PREFIX_TABLE."data_customer")->result();
		$field['seloCustomer'] 		= $this->self_model->return_build_select2me($oCustomer,'','','','id_customer','id_customer','','','id','code','-','name');

		$oTerm 						= $this->db->select('id,code')->order_by('idx_seq', 'ASC')->where("active='1'")->get(_PREFIX_TABLE."option_term_of_payment")->result();
		$field['seloTerm'] 			= $this->self_model->return_build_radioselect($oTerm,'4','','id_term','','inline','id','code');
		$oCurrency 					= $this->db->select('id,code')->where("active='1'")->get(_PREFIX_TABLE."option_currency")->result();
		$field['seloCurrency'] 		= $this->self_model->return_build_radioselect($oCurrency,'1','','id_currency','','inline','id','code');
		$field['txttax'] 			= $this->self_model->return_build_checkbox('ppn', [['ppn','PPN'],['pph','PPH'],['other','Other Tax']], 'is_tax[]', '', 'inline','is_tax');
		$field['txttaxother'] 		= $this->self_model->return_build_txt('','is_tax_other','is_tax_other','','');
		$field['txttopdp'] 			= $this->self_model->return_build_txt('','top_dp','top_dp','','');
		$field['txttopt1'] 			= $this->self_model->return_build_txt('','top_t1','top_t1','','');
		$field['txttopt2'] 			= $this->self_model->return_build_txt('','top_t2','top_t2','','');
		$field['txttopt3'] 			= $this->self_model->return_build_txt('','top_t3','top_t3','','');
		$field['txttoptf'] 			= $this->self_model->return_build_txt('','top_tf','top_tf','','');
		$field['txttoprt'] 			= $this->self_model->return_build_txt('','top_rt','top_rt','','');
		$field['txtdescription'] 	= $this->self_model->return_build_txtarea('','description','description');
		$field['txtketentuan'] 		= $this->self_model->return_build_txtarea('','notes','notes');
		$oValid 					= $this->db->select('id,code')->order_by('idx_seq', 'ASC')->where("active='1'")->get(_PREFIX_TABLE."option_term_of_validity")->result();
		$field['seloValid'] 		= $this->self_model->return_build_radioselect($oValid,'2','','id_valid','','inline','id','code');
		$field['txttocontact'] 		= $this->self_model->return_build_txt('','to_contact','to_contact','','');
		$oTtd						= $this->db->select('id,name')->where("id_status='1'")->get(_PREFIX_TABLE."data_karyawan")->result();
		$field['seloTtd'] 			= $this->self_model->return_build_select2me($oTtd,'','','','id_ttd','id_ttd','','','id','name');
		// readonly auto populate by select option - no data save
		$field['txtnamacustomer'] 	= $this->self_model->return_build_txt('','customer_name','customer_name','','','readonly');
		$field['txtalamatcustomer'] = $this->self_model->return_build_txtarea('','customer_address','customer_address','4','','','readonly');
		$field['txtrab'] 			= $this->self_model->return_build_txt('0','rab','rab','','text-align: right;','readonly');
		$field['txtmarginplan'] 	= $this->self_model->return_build_txt('0','margin_plan','margin_plan','','text-align: right;','readonly');
		
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
		//define('_USER_ACCESS_LEVEL_EKSPORT',$akses["eksport"]);
		define('_USER_ACCESS_LEVEL_EKSPORT',0);
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
	public function getprint($id)
	{
		if(_USER_ACCESS_LEVEL_VIEW == "1" && _USER_ACCESS_LEVEL_DETAIL == "1") {
			$pid = $id;
			// Get main data
			$this->data['rs'] = $this->self_model->getRowData($pid);
			// Get item data
			$this->data['rs2'] 	= $this->self_model->getQuoteItemRows($pid,TRUE,TRUE);
			$this->load->view($this->folder_name.'/print' , $this->data);
		} else {
			$this->load->view('errors/html/error_hacks_401');
		}
	}

	// Print
	public function print()
	{
		if(_USER_ACCESS_LEVEL_VIEW == "1" && _USER_ACCESS_LEVEL_DETAIL == "1") {
			if ($post = $this->input->post(null, true))
			{
				if(isset($post['pid'])){
					$pid = $post['pid'];
					// Get main data
					$this->data['rs'] = $this->self_model->getRowData($pid);
					// Get item data
					$this->data['rs2'] 	= $this->self_model->getQuoteItemRows($pid,TRUE,TRUE);
					$this->load->view($this->folder_name.'/print' , $this->data);
				}
			}
		} else {
			$this->load->view('errors/html/error_hacks_401');
		}
	}

	// Get rab info
	public function getrabinfo()
	{
		if(_USER_ACCESS_LEVEL_VIEW == "1") {
			$post = $this->input->post();
			if(isset($post['sel'])){
				$id = trim($post['sel']);
				echo json_encode($this->self_model->getRabInfo($id));
			}
		} else {
			$this->load->view('errors/html/error_hacks_401');
		}
	}

	// Get customer info
	public function getcustomerinfo()
	{
		if(_USER_ACCESS_LEVEL_VIEW == "1") {
			$post = $this->input->post();
			if(isset($post['sel'])){
				$id = trim($post['sel']);
				echo json_encode($this->self_model->getCustomerInfo($id));
			}
		} else {
			$this->load->view('errors/html/error_hacks_401');
		}
	}

	// Generate quote list row
	public function genquoteitemrow()
	{
		if(_USER_ACCESS_LEVEL_VIEW == "1")
		{
			$post = $this->input->post(null, true);
			if(isset($post['count']))
			{
				$row = trim($post['count']);
				echo $this->self_model->getNewQuoteItemRow($row);
			} else if(isset($post['id'])) {
				$row = 0;
				$id = trim($post['id']);
				$view = (isset($post['view']) && $post['view'] == TRUE)? TRUE:FALSE;
				echo json_encode($this->self_model->getNewQuoteItemRow($row,$id,$view));
			}
		}
		else
		{
			$this->load->view('errors/html/error_hacks_401');
		}
	}

}