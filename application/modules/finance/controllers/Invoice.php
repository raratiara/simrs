<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Invoice extends MYP_Controller
{
	/* Costs */
 	const  LABELMODULE				= "finance_invoice"; // identify menu
 	const  LABELMASTER				= "Invoice";
 	const  LABELFOLDER				= "finance"; // module folder
 	const  LABELPATH				= "invoice"; // controller file (lowercase)
 	const  LABELNAVSEG1				= ""; // adjusted 1st sub parent segment
 	const  LABELSUBPARENTSEG1		= ""; // 
 	const  LABELNAVSEG2				= ""; // adjusted 2nd sub parent segment
 	const  LABELSUBPARENTSEG2		= ""; // 
	
	/* View */
	public $icon 					= 'fa-database';
	public $tabel_header 			= ["ID","No Invoice","Tanggal","Term","Due Date","Bill To","Grand Total","Status"];
	
	/* Export */
	public $colnames 				= ["ID","No Invoice","Tanggal","PO","Term","Due Date","Bill To","Sub Total","Ppn","Pph","Grand Total","Status"];
	public $colfields 				= ["id","invoice","dinvoice","po","term","ddue","customer","wsubtotal","wppn","wpph","wgrandtotal","status"];

	/* Form Field Asset */
	public function form_field_asset()
	{
		if (date('Y-m-d') > '2024-12-31') { $vCoretax = '0.12'; } else { $vCoretax = '0';}

		$field = [];
		$oTemplateInvoice 				= $this->db->select('id,template_name')->where("id_status IS NOT NULL AND id_status IN ('1')")->get(_PREFIX_TABLE."data_invoice_template")->result();
		$field['seloTemplateInvoice'] 	= $this->self_model->return_build_select2me($oTemplateInvoice,'','','','id_tinvoice','id_tinvoice','','','id','template_name');
		$oProInvoice 				= $this->db->select('id,pinvoice')->where("id_status IS NOT NULL AND id_status IN ('2,3')")->get(_PREFIX_TABLE."data_proforma_invoice")->result();
		$field['seloProInvoice'] 	= $this->self_model->return_build_select2me($oProInvoice,'','','','id_proinvoice','id_proinvoice','','','id','pinvoice');
		$field['txtdateinvoice'] 	= $this->self_model->return_build_txtdate(date('d-m-Y'),'date_invoice','date_invoice');
		$field['txtdatedue'] 		= $this->self_model->return_build_txtdate(date('d-m-Y'),'date_due','date_due');
		$field['txtinvoice'] 		= $this->self_model->return_build_txt('Auto','invoice','invoice','','','readonly');
		$field['txtfaktur'] 		= $this->self_model->return_build_txt('','faktur','faktur');
		$field['worth'] 			= $this->self_model->return_build_txt('0','grandtotal','grandtotal','','text-align: right;','readonly').$this->self_model->return_build_txthidden('0','ppn','ppn').$this->self_model->return_build_txthidden('0','dpp','dpp').$this->self_model->return_build_txthidden('0','pph','pph').$this->self_model->return_build_txthidden('0','other_tax','other_tax').$this->self_model->return_build_txthidden('0','worth','worth').$this->self_model->return_build_txthidden($vCoretax,'coretax','coretax');
		$oPO 						= $this->db->select('id,po')->get(_PREFIX_TABLE."data_po")->result();
		$field['seloPO'] 			= $this->self_model->return_build_select2me($oPO,'','','','id_po','id_po','','','id','po');
		$oProject 					= $this->db->select('id,project,title')->get(_PREFIX_TABLE."data_project")->result();
		$field['seloProject'] 		= $this->self_model->return_build_select2me($oProject,'','','','id_project','id_project','','','id','project','-','title');
		$oCustomer 					= $this->db->select('id,code,name')->order_by('code', 'ASC')->get(_PREFIX_TABLE."data_customer")->result();
		$field['seloCustomer'] 		= $this->self_model->return_build_select2me($oCustomer,'','','','id_customer','id_customer','','','id','code','-','name');
		$oTerm 						= $this->db->select('id,code')->order_by('idx_seq', 'ASC')->where("active='1'")->get(_PREFIX_TABLE."option_term_of_payment")->result();
		$field['seloTerm'] 			= $this->self_model->return_build_radioselect($oTerm,'4','','id_term','','inline','id','code');
		$oCurrency 					= $this->db->select('id,code')->where("active='1'")->get(_PREFIX_TABLE."option_currency")->result();
		$field['seloCurrency'] 		= $this->self_model->return_build_radioselect($oCurrency,'1','','id_currency','','inline','id','code');
		$oBankAccount 				= $this->db->select('a.id,b.description as bank,a.rekening')->join(_PREFIX_TABLE.'option_bank b', 'b.id = a.id_bank')->order_by('b.description', 'ASC')->where("a.active='1'")->get(_PREFIX_TABLE."option_bank_account a")->result();
		$field['seloBankAccount'] 	= $this->self_model->return_build_select2me($oBankAccount,'','','','id_bank_account','id_bank_account','','','id','bank','-','rekening');
		$field['txttax'] 			= $this->self_model->return_build_checkbox('ppn', [['ppn','PPN'],['pph','PPH'],['other','Other Tax']], 'is_tax[]', '', 'inline','is_tax');
		$field['txttaxother'] 		= $this->self_model->return_build_txt('','is_tax_other','is_tax_other','','');
		$field['txtdescription'] 	= $this->self_model->return_build_txtarea('','description','description');
		$field['txtketentuan'] 		= $this->self_model->return_build_txtarea('','notes','notes');
		$field['txtterbilang'] 		= $this->self_model->return_build_txt('','terbilang','terbilang');
		$oStatus 					= $this->db->select('id,description')->order_by('idx_seq', 'ASC')->where("active='1'")->get(_PREFIX_TABLE."option_sales_status")->result();
		$field['seloStatus'] 		= $this->self_model->return_build_select2me($oStatus,'','','','id_status','id_status','','','id','description');
		// readonly auto populate by select option - no data save
		$field['txtnamacustomer'] 	= $this->self_model->return_build_txt('','customer_name','customer_name','','','readonly');
		$field['txtalamatcustomer'] = $this->self_model->return_build_txtarea('','customer_address','customer_address','4','','','readonly');
		
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

		$this->load->model('sales/po_in_model','po_model');
		$this->load->model('proforma_invoice_model','proforma_model');
		$this->load->model('invoice_template_model','template_model');
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
	// Get ppn tarif
	public function getppntarrif()
	{
		if(_USER_ACCESS_LEVEL_VIEW == "1" && (_USER_ACCESS_LEVEL_ADD == "1" || _USER_ACCESS_LEVEL_UPDATE == "1")) {
			if ($post = $this->input->post(null, true))
			{
				if(isset($post['udate'])){
					$udate = $post['udate'];
					echo $this->self_model->get_ppn_value($udate);;
				}
			} else {
				echo '0.1';
			}
		} else {
			$this->load->view('errors/html/error_hacks_401');
		}
	}

	public function getprint($id)
	{
		if(_USER_ACCESS_LEVEL_VIEW == "1" && _USER_ACCESS_LEVEL_DETAIL == "1") {
			$pid = $id;
			// Get main data
			$this->data['rs'] = $this->self_model->getRowData($pid);
			// Get item data
			$this->data['rs2'] 	= $this->self_model->getInvoiceItemRows($pid,TRUE,TRUE);
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
					$this->data['rs2'] 	= $this->self_model->getInvoiceItemRows($pid,TRUE,TRUE);
					$this->load->view($this->folder_name.'/print' , $this->data);
				}
			}
		} else {
			$this->load->view('errors/html/error_hacks_401');
		}
	}

	// Get proforma invoice info
	public function getproinvoiceinfo()
	{
		if(_USER_ACCESS_LEVEL_VIEW == "1" && (_USER_ACCESS_LEVEL_ADD == "1" || _USER_ACCESS_LEVEL_UPDATE == "1")) {
			if ($post = $this->input->post(null, true))
			{
				if(isset($post['sel'])){
					$pid = $post['sel'];
					echo json_encode($this->proforma_model->getRowData($pid));
				}
			}
		} else {
			$this->load->view('errors/html/error_hacks_401');
		}
	}

	// Get po info
	public function getpoinfo()
	{
		if(_USER_ACCESS_LEVEL_VIEW == "1" && (_USER_ACCESS_LEVEL_ADD == "1" || _USER_ACCESS_LEVEL_UPDATE == "1")) {
			if ($post = $this->input->post(null, true))
			{
				if(isset($post['sel'])){
					$pid = $post['sel'];
					echo json_encode($this->po_model->getRowData($pid));
				}
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

	// Get project info
	public function getprojectinfo()
	{
		if(_USER_ACCESS_LEVEL_VIEW == "1") {
			$post = $this->input->post();
			if(isset($post['sel'])){
				$id = trim($post['sel']);
				echo json_encode($this->self_model->getProjectInfo($id));
			}
		} else {
			$this->load->view('errors/html/error_hacks_401');
		}
	}

	// Get terbilang
	public function getterbilang()
	{
		if(_USER_ACCESS_LEVEL_VIEW == "1") {
			$post = $this->input->post();
			if(isset($post['val'])){
				$v = trim($post['val']);
				if(!empty($v)){
					echo ucwords($this->self_model->terbilang($v).'rupiah');
				}
			}
		} else {
			$this->load->view('errors/html/error_hacks_401');
		}
	}

	// Generate invoice list row
	public function geninvoiceitemrow()
	{
		if(_USER_ACCESS_LEVEL_VIEW == "1")
		{
			$post = $this->input->post(null, true);
			if(isset($post['count']))
			{
				$row = trim($post['count']);
				echo $this->self_model->getNewInvoiceItemRow($row);
			} else if(isset($post['id'])) {
				$row = 0;
				$id = trim($post['id']);
				$view = (isset($post['view']) && $post['view'] == TRUE)? TRUE:FALSE;
				echo json_encode($this->self_model->getNewInvoiceItemRow($row,$id,$view));
			}
		}
		else
		{
			$this->load->view('errors/html/error_hacks_401');
		}
	}

	// Generate po list row
	public function genpoitemrow()
	{
		if(_USER_ACCESS_LEVEL_VIEW == "1")
		{
			$post = $this->input->post(null, true);
			if(isset($post['id'])) {
				$row = 0;
				$id = trim($post['id']);
				$view = (isset($post['view']) && $post['view'] == TRUE)? TRUE:FALSE;
				echo json_encode($this->po_model->getNewPoItemRow($row,$id,$view));
			}
		}
		else
		{
			$this->load->view('errors/html/error_hacks_401');
		}
	}

	// Generate proforma invoice list row
	public function genpinvoiceitemrow()
	{
		if(_USER_ACCESS_LEVEL_VIEW == "1")
		{
			$post = $this->input->post(null, true);
			if(isset($post['id'])) {
				$row = 0;
				$id = trim($post['id']);
				$view = (isset($post['view']) && $post['view'] == TRUE)? TRUE:FALSE;
				echo json_encode($this->proforma_model->getNewPInvoiceItemRow($row,$id,$view));
			}
		}
		else
		{
			$this->load->view('errors/html/error_hacks_401');
		}
	}

	// Get template invoice info
	public function gettinvoiceinfo()
	{
		if(_USER_ACCESS_LEVEL_VIEW == "1" && (_USER_ACCESS_LEVEL_ADD == "1" || _USER_ACCESS_LEVEL_UPDATE == "1")) {
			if ($post = $this->input->post(null, true))
			{
				if(isset($post['sel'])){
					$pid = $post['sel'];
					echo json_encode($this->template_model->getRowData($pid));
				}
			}
		} else {
			$this->load->view('errors/html/error_hacks_401');
		}
	}

	// Generate template invoice list row
	public function gentinvoiceitemrow()
	{
		if(_USER_ACCESS_LEVEL_VIEW == "1")
		{
			$post = $this->input->post(null, true);
			if(isset($post['id'])) {
				$row = 0;
				$id = trim($post['id']);
				$view = (isset($post['view']) && $post['view'] == TRUE)? TRUE:FALSE;
				echo json_encode($this->template_model->getNewTInvoiceItemRow($row,$id,$view));
			}
		}
		else
		{
			$this->load->view('errors/html/error_hacks_401');
		}
	}
}