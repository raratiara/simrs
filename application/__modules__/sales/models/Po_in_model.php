<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Po_in_model extends MY_Model
{
	/* Module */
 	protected $folder_name			= "sales/po_in";
 	protected $table_name 			= _PREFIX_TABLE."data_po";
 	protected $table_quotation 		= _PREFIX_TABLE."data_quotation";
 	protected $table_project 		= _PREFIX_TABLE."data_project";
 	protected $table_customer 		= _PREFIX_TABLE."data_customer";
 	protected $table_karyawan 		= _PREFIX_TABLE."data_karyawan";
 	protected $table_cabang 		= _PREFIX_TABLE."option_branch";
 	protected $table_kode_surat 	= _PREFIX_TABLE."option_mailingcode";
 	protected $table_uom 			= _PREFIX_TABLE."option_uom";
 	protected $table_term 			= _PREFIX_TABLE."option_term_of_payment";
 	protected $table_currency 		= _PREFIX_TABLE."option_currency";
 	protected $table_status 		= _PREFIX_TABLE."option_sales_status";
 	protected $primary_key 			= "id";
	/* upload */
 	protected $attachment_folder	= "./uploads/data/po_in";
	protected $allow_type			= "gif|jpeg|jpg|png|pdf|xls|xlsx|doc|docx|txt";
	protected $allow_size			= "0"; // 0 for limit by default php conf (in Kb)

	function __construct()
	{
		parent::__construct();
	}
	
	// Remove file
	public function remove_file($id = "", $filename= "") {
		if(!empty($id) && !empty($filename)){
			$filepath = $this->attachment_folder.'/'.$id.'/'.$filename;
			if(is_file($filepath)) {
				unlink($filepath);
			}
		}
	}
	
	// Upload file
	public function upload_file($id = "", $fieldname= "", $replace=FALSE, $oldfilename= "", $array=FALSE, $i=0) {
		$data = array();
		$data['status'] = FALSE;
		if(!empty($id) && !empty($fieldname)){
			// handling multiple upload (as array field)
			if($array){
				// Define new $_FILES array - $_FILES['file']
				$_FILES['file']['name'] = $_FILES[$fieldname]['name'][$i];
				$_FILES['file']['type'] = $_FILES[$fieldname]['type'][$i];
				$_FILES['file']['tmp_name'] = $_FILES[$fieldname]['tmp_name'][$i];
				$_FILES['file']['error'] = $_FILES[$fieldname]['error'][$i];
				$_FILES['file']['size'] = $_FILES[$fieldname]['size'][$i];
				// override field
				$fieldname = 'file';
			}
			// handling regular upload (as one field)
			if(isset($_FILES[$fieldname]) && !empty($_FILES[$fieldname]['name']))
			{
				$dir = $this->attachment_folder.'/'.$id;
				if(!is_dir($dir)) {
					mkdir($dir);
				}
				if($replace){
					$this->remove_file($id, $oldfilename);
				}
				$config['upload_path']   = $dir.'/';
				$config['allowed_types'] = $this->allow_type;
				$config['max_size'] 	 = $this->allow_size;
				$this->load->library('upload', $config);
				if(!$this->upload->do_upload($fieldname)){
					$err_msg = $this->upload->display_errors();
					$data['error_warning'] = strip_tags($err_msg);				
					$data['status'] = FALSE;
				} else {
					$fileData = $this->upload->data();
					$data['upload_file'] = $fileData['file_name'];
					$data['status'] = TRUE;
				}
			}
		}
		
		return $data;
	}

	// Generate item list
	public function get_list_data()
	{
		$aColumns = [
			NULL,
			NULL,
			'a.id',
			'a.po',
			'b.name as customer',
			'DATE_FORMAT(FROM_UNIXTIME(a.date_po), "%d-%m-%Y") as dpo',
			'DATE_FORMAT(FROM_UNIXTIME(a.date_due), "%d-%m-%Y") as ddue',
			'a.worth as wpo',
			'c.description as status'
		];

		$sIndexColumn = 'a.'.$this->primary_key;
		$sTable = ' '.$this->table_name.' a
					LEFT JOIN '.$this->table_customer.' b ON b.id=a.id_customer
					LEFT JOIN '.$this->table_status.' c ON c.id=a.id_status
					';

		/* Paging */
		$sLimit = "";
		if(isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1'){
			$sLimit = "LIMIT ".($_GET['iDisplayStart']).", ".
			($_GET['iDisplayLength']);
		}

		/* Ordering */
		$sOrder = "";
		if(isset($_GET['iSortCol_0'])) {
			$sOrder = "ORDER BY  ";
			for ($i=0 ; $i<intval($_GET['iSortingCols']) ; $i++){
				if($_GET['bSortable_'.intval($_GET['iSortCol_'.$i])] == "true"){
					$srcCol = $aColumns[ intval($_GET['iSortCol_'.$i])];
					$findme   = ' as ';
					$pos = strpos($srcCol, $findme);
					if ($pos !== false) {
						$pieces = explode($findme, trim($srcCol));
						$sOrder .= trim($pieces[0])."
						".($_GET['sSortDir_'.$i]) .", ";
					} else {
						$sOrder .= $aColumns[ intval($_GET['iSortCol_'.$i])]."
						".($_GET['sSortDir_'.$i]) .", ";
					}
				}
			}

			$sOrder = substr_replace($sOrder, "", -2);
			if($sOrder == "ORDER BY"){
				$sOrder = "";
			}
		}

		/* Filtering */
		$sWhere = " WHERE 1 = 1 ";
		if(isset($_GET['sSearch']) && $_GET['sSearch'] != ""){
			$sWhere .= "AND (";
			foreach ($aColumns as $c) {
				if($c !== NULL){
					$srcCol = $c;
					$findme   = ' as ';
					$pos = strpos($srcCol, $findme);
					if ($pos !== false) {
						$pieces = explode($findme, trim($srcCol));
						$sWhere .= trim($pieces[0])." LIKE '%".($_GET['sSearch'])."%' OR ";
					} else {
						$sWhere .= $c." LIKE '%".($_GET['sSearch'])."%' OR ";
					}
				}
			}

			$sWhere = substr_replace( $sWhere, "", -3);
			$sWhere .= ')';
		}

		/* Individual column filtering */
		for($i=0 ; $i<count($aColumns) ; $i++) {
			if(isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" && isset($_GET['sSearch_'.$i]) && $_GET['sSearch_'.$i] != ''){
				if($sWhere == ""){
					$sWhere = "WHERE ";
				} else {
					$sWhere .= " AND ";
				}
				$srcString = $_GET['sSearch_'.$i];
				$findme   = '|';
				$pos = strpos($srcString, $findme);
				if ($pos !== false) {
					$srcKey = "";
					$pieces = explode($findme, trim($srcString));
					foreach ($pieces as $value) {
						if(!empty($srcKey)){
							$srcKey .= ",";
						}
						$srcKey .= "'".$value."'";
					}
					
					$srcCol = $aColumns[$i];
					$findme   = ' as ';
					$pos = strpos($srcCol, $findme);
					if ($pos !== false) {
						$pieces = explode($findme, trim($srcCol));
						$sWhere .= trim($pieces[0])." IN (".$srcKey.") ";
					} else {
						$sWhere .= $aColumns[$i]." IN (".$srcKey.") ";
					}
				} else {
					$srcCol = $aColumns[$i];
					$findme   = ' as ';
					$pos = strpos($srcCol, $findme);
					if ($pos !== false) {
						$pieces = explode($findme, trim($srcCol));
						$sWhere .= trim($pieces[0])." LIKE '%".($srcString)."%' ";
					} else {
						$sWhere .= $aColumns[$i]." LIKE '%".($srcString)."%' ";
					}
				}
			}
		}

		/* Get data to display */
		$filtered_cols = array_filter($aColumns, [$this, 'is_not_null']); // Filtering NULL value
		$sQuery = "
		SELECT  SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $filtered_cols))."
		FROM $sTable
		$sWhere
		$sOrder
		$sLimit
		";
		# echo $sQuery;exit;
		$rResult = $this->db->query($sQuery)->result();

		/* Data set length after filtering */
		$sQuery = "
			SELECT FOUND_ROWS() AS filter_total
		";
		$aResultFilterTotal = $this->db->query($sQuery)->row();
		$iFilteredTotal = $aResultFilterTotal->filter_total;

		/* Total data set length */
		$sQuery = "
			SELECT COUNT(".$sIndexColumn.") AS total
			FROM $sTable
		";
		$aResultTotal = $this->db->query($sQuery)->row();
		$iTotal = $aResultTotal->total;

		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $iTotal,
			"iTotalDisplayRecords" => $iFilteredTotal,
			"aaData" => array()
		);

		foreach($rResult as $row)
		{
			$print = "";
			if (_USER_ACCESS_LEVEL_DETAIL == "1")  {
				$print = '<a class="btn btn-xs btn-success detail-btn" href="javascript:void(0);" onclick="printit('."'".$row->id."'".')" role="button"><i class="fa fa-print"></i></a>';
			}
			$detail = "";
			if (_USER_ACCESS_LEVEL_DETAIL == "1")  {
				$detail = '<a class="btn btn-xs btn-success detail-btn" href="javascript:void(0);" onclick="detail('."'".$row->id."'".')" role="button"><i class="fa fa-search-plus"></i></a>';
			}
			$edit = "";
			if (_USER_ACCESS_LEVEL_UPDATE == "1")  {
				$edit = '<a class="btn btn-xs btn-primary" href="javascript:void(0);" onclick="edit('."'".$row->id."'".')" role="button"><i class="fa fa-pencil"></i></a>';
			}
			$delete_bulk = "";
			$delete = "";
			if (_USER_ACCESS_LEVEL_DELETE == "1")  {
				$delete_bulk = '<input name="ids[]" type="checkbox" class="data-check" value="'.$row->id.'">';
				$delete = '<a class="btn btn-xs btn-danger" href="javascript:void(0);" onclick="deleting('."'".$row->id."'".')" role="button"><i class="fa fa-trash"></i></a>';
			}

			array_push($output["aaData"],array(
				$delete_bulk,
				'<div class="action-buttons">
					'.$print.'
					'.$detail.'
					'.$edit.'
					'.$delete.'
				</div>',
				$row->id,
				$row->po,
				$row->customer,
				$row->dpo,
				$row->ddue,
				number_format($row->wpo,0,",","."),
				$row->status
			));
		}

		echo json_encode($output);
	}

	// filltering null value from array
	public function is_not_null($val){
		return !is_null($val);
	}		

	// delete item action
	public function delete($id= "") {
		if (isset($id) && $id <> "") {
			// removing attachment file
			$files = $this->db->select('document')->where([$this->primary_key => $id])->get($this->table_name)->row();
			$fdoc = json_decode($files->document);
			if(!empty($fdoc)){
				foreach($fdoc as $k=>$v){
					if(!empty($v->file)){
						$this->remove_file($id,$v->file);
					}
				}
			}

			//$this->db->trans_off(); // Disable transaction
			$this->db->trans_start(); // set "True" for query will be rolled back
			$this->db->where([$this->primary_key => $id])->delete($this->table_name);
			$this->db->trans_complete();

			return $rs = $this->db->trans_status();
		} else return null;
	}  

	// delete multi items action
	public function bulk($id= "") {
		if (is_array($id) && count($id)) {
			$err = '';
			foreach ($id as $pid) {
				// removing attachment file
				$files = $this->db->select('document')->where([$this->primary_key => $pid])->get($this->table_name)->row();
				$fdoc = json_decode($files->document);
				if(!empty($fdoc)){
					foreach($fdoc as $k=>$v){
						if(!empty($v->file)){
							$this->remove_file($pid,$v->file);
						}
					}
				}

				//$this->db->trans_off(); // Disable transaction
				$this->db->trans_start(); // set "True" for query will be rolled back
				$this->db->where([$this->primary_key => $pid])->delete($this->table_name);
				$this->db->trans_complete();
				$deleted = $this->db->trans_status();
                if ($deleted == false) {
					if(!empty($err)) $err .= ", ";
                    $err .= $pid;
                }
			}
			
			$data = array();
			if(empty($err)){
				$data['status'] = TRUE;
			} else {
				$data['status'] = FALSE;
				$data['err'] = '<br/>ID : '.$err;
			}
			
			return $data;
		} else return null;
	}  

	// Get next number 
	public function getNextNumber($branch) { 
		$rs = $this->db->select_max('urutan')->where(['id_branch' => $branch,'tahun' => date('Y')])->get($this->table_name)->row();

		return $rs->urutan+1;
	} 

	// Get letter code 
	public function lettercode($mailingid) { 
		$rs = $this->db->select('code')->where([$this->primary_key => $mailingid])->get($this->table_kode_surat)->row();

		return $rs->code;
	} 

	// Get branch code 
	public function branchcode() { 
		$id_branch = $this->session->userdata('branch');
		$rs = $this->db->select('code')->where([$this->primary_key => $id_branch])->get($this->table_cabang)->row();

		return $rs->code;
	} 
	
	// adding data
	public function add_data($post) {
		// BOF auto numbering 
		$branch = $this->branchcode(); // branch code
		$lettercode = $this->lettercode('4'); // po code
		$yearcode = date('y'); // last 2 digit year code
		$runningnumber = $this->getNextNumber($branch); // next count number
		$nextnum 	= $branch.$lettercode.$yearcode.str_pad($runningnumber, 5, "0", STR_PAD_LEFT);
		// EOF auto numbering 
		$id_pic = (isset($post['id_pic']) && !empty($post['id_pic']))? trim($post['id_pic']):0;
		$id_customer = (isset($post['id_customer']) && !empty($post['id_customer']))? trim($post['id_customer']):0;
		$id_term = (isset($post['id_term']) && !empty($post['id_term']))? trim($post['id_term']):NULL;
		$id_currency = (isset($post['id_currency']) && !empty($post['id_currency']))? trim($post['id_currency']):NULL;
		$id_quotation = (isset($post['id_quotation']) && !empty($post['id_quotation']))? trim($post['id_quotation']):NULL;
		$id_project = (isset($post['id_project']) && !empty($post['id_project']))? trim($post['id_project']):NULL;
		$id_status = (isset($post['id_status']) && !empty($post['id_status']))? trim($post['id_status']):NULL;
		$data = [
			'date_po' 			=> $this->date_to_int(trim($post['date_po'])),
			'date_due' 			=> $this->date_to_int(trim($post['date_due'])),
			'po' 				=> trim($post['po']),
			'id_branch' 		=> $branch,
			'urutan' 			=> $runningnumber,
			'bulan' 			=> date('n'),
			'tahun' 			=> date('Y'),
			'worth' 			=> str_replace(",",".",str_replace(".","",trim($post['worth']))),
			'id_pic' 			=> $id_pic,
			'id_quotation' 		=> $id_quotation,
			'id_project' 		=> $id_project,
			'id_customer' 		=> $id_customer,
			'description' 		=> trim($post['description']),
			'notes' 			=> trim($post['notes']),
			'id_term' 			=> $id_term,
			'id_currency' 		=> $id_currency,
			'id_status' 		=> $id_status,
			'insert_by'			=> $_SESSION["username"]
		];

		//$this->db->trans_off(); // Disable transaction
		$this->db->trans_start(); // set "True" for query will be rolled back
		$this->db->insert($this->table_name, $data);
		$lastId = $this->db->insert_id();
		// Add po data
		$quote =[];
		// cek po item
		if(isset($post['qidescription'])){
			$item_num = count($post['qidescription']); // cek sum
			$item_len_min = min(array_keys($post['qidescription'])); // cek min key index
			$item_len = max(array_keys($post['qidescription'])); // cek max key index
		} else {
			$item_num = 0;
		}

		if($item_num>0){
			for($i=$item_len_min;$i<=$item_len;$i++) 
			{
				if(isset($post['qidescription'][$i])){
					$itemData = [
						'description' 	=> trim($post['qidescription'][$i]),
						'qty' 			=> str_replace(",",".",str_replace(".","",trim($post['qty'][$i]))),
						'satuan' 		=> trim($post['satuan'][$i]),
						'price' 		=> str_replace(",",".",str_replace(".","",trim($post['price'][$i]))),
						'subtotal' 		=> str_replace(",",".",str_replace(".","",trim($post['subtotal'][$i])))
						];

					$quote[] = $itemData;
				}
			}
		}
		$this->db->update($this->table_name, ['data_po' => json_encode($quote)], [$this->primary_key => $lastId]);

		// Add tax data
		$tax =['is_tax' => [],'tax_other' => trim($post['is_tax_other'])];
		if(isset($post['is_tax'])){
			$tax['is_tax'] = $post['is_tax'];
		}
		if(!in_array('other',$tax['is_tax'])){
			$tax['tax_other'] = '';
		}
		$this->db->update($this->table_name, ['tax' => json_encode($tax)], [$this->primary_key => $lastId]);

		// Add top data
		$top =[
				'top_dp' => trim($post['top_dp']),
				'top_t1' => trim($post['top_t1']),
				'top_t2' => trim($post['top_t2']),
				'top_t3' => trim($post['top_t3']),
				'top_tf' => trim($post['top_tf']),
				'top_rt' => trim($post['top_rt'])
		];
		$this->db->update($this->table_name, ['top' => json_encode($top)], [$this->primary_key => $lastId]);

		// Add attach data
		$attc =[];
		// cek item attach
		if(isset($post['attcdescription'])){
			$item_num = count($post['attcdescription']); // cek sum
			$item_len_min = min(array_keys($post['attcdescription'])); // cek min key index
			$item_len = max(array_keys($post['attcdescription'])); // cek max key index
		} else {
			$item_num = 0;
		}
		
		if($item_num>0){
			for($i=$item_len_min;$i<=$item_len;$i++) 
			{
				if(isset($post['attcdescription'][$i])){
					$itemData = [
						'description' => trim($post['attcdescription'][$i]),
						];
					$upload = $this->upload_file($lastId, 'attcfile', FALSE, '', TRUE, $i);
					$itemData['file'] = '';
					if($upload['status']){
						$itemData['file'] = $upload['upload_file'];
					} else if(isset($upload['error_warning'])){
						echo $upload['error_warning']; exit;
					}
					$attc[] = $itemData;
				}
			}
		}
		$this->db->update($this->table_name, ['document' => json_encode($attc)], [$this->primary_key => $lastId]);
		$this->db->trans_complete();

		$rs = $this->db->trans_status();
		return [$rs,$lastId];
	}  

	// update data
	public function edit_data($post) { 
		if(!empty($post['id'])){
			$uid = trim($post['id']);
			$files = $this->db->select('document')->where([$this->primary_key => $uid])->get($this->table_name)->row();
			$fdoc = json_decode($files->document);
			$id_pic = (isset($post['id_pic']) && !empty($post['id_pic']))? trim($post['id_pic']):0;
			$id_customer = (isset($post['id_customer']) && !empty($post['id_customer']))? trim($post['id_customer']):0;
			$id_term = (isset($post['id_term']) && !empty($post['id_term']))? trim($post['id_term']):NULL;
			$id_currency = (isset($post['id_currency']) && !empty($post['id_currency']))? trim($post['id_currency']):NULL;
			$id_quotation = (isset($post['id_quotation']) && !empty($post['id_quotation']))? trim($post['id_quotation']):NULL;
			$id_project = (isset($post['id_project']) && !empty($post['id_project']))? trim($post['id_project']):NULL;
			$id_status = (isset($post['id_status']) && !empty($post['id_status']))? trim($post['id_status']):NULL;
			$data = [
				'date_po' 			=> $this->date_to_int(trim($post['date_po'])),
				'date_due' 			=> $this->date_to_int(trim($post['date_due'])),
				'po' 				=> trim($post['po']),
				'worth' 			=> str_replace(",",".",str_replace(".","",trim($post['worth']))),
				'id_pic' 			=> $id_pic,
				'id_quotation' 		=> $id_quotation,
				'id_project' 		=> $id_project,
				'id_customer' 		=> $id_customer,
				'description' 		=> trim($post['description']),
				'notes' 			=> trim($post['notes']),
				'id_term' 			=> $id_term,
				'id_currency' 		=> $id_currency,
				'id_status' 		=> $id_status,
				'update_by'			=> $_SESSION["username"]
			];

			//$this->db->trans_off(); // Disable transaction
			$this->db->trans_start(); // set "True" for query will be rolled back
			$this->db->update($this->table_name, $data, [$this->primary_key => $uid]);
			// Update po data
			$quote =[];
			// cek po item
			if(isset($post['qidescription'])){
				$item_num = count($post['qidescription']); // cek sum
				$item_len_min = min(array_keys($post['qidescription'])); // cek min key index
				$item_len = max(array_keys($post['qidescription'])); // cek max key index
			} else {
				$item_num = 0;
			}

			if($item_num>0){
				for($i=$item_len_min;$i<=$item_len;$i++) 
				{
					if(isset($post['qidescription'][$i])){
						$itemData = [
							'description' 	=> trim($post['qidescription'][$i]),
							'qty' 			=> str_replace(",",".",str_replace(".","",trim($post['qty'][$i]))),
							'satuan' 		=> trim($post['satuan'][$i]),
							'price' 		=> str_replace(",",".",str_replace(".","",trim($post['price'][$i]))),
							'subtotal' 		=> str_replace(",",".",str_replace(".","",trim($post['subtotal'][$i])))
							];

						$quote[] = $itemData;
					}
				}
			}
			$this->db->update($this->table_name, ['data_po' => json_encode($quote)], [$this->primary_key => $uid]);

			// Update tax data
			$tax =['is_tax' => [],'tax_other' => trim($post['is_tax_other'])];
			if(isset($post['is_tax'])){
				$tax['is_tax'] = $post['is_tax'];
			}
			if(!in_array('other',$tax['is_tax'])){
				$tax['tax_other'] = '';
			}
			$this->db->update($this->table_name, ['tax' => json_encode($tax)], [$this->primary_key => $uid]);

			// Update top data
			$top =[
					'top_dp' => trim($post['top_dp']),
					'top_t1' => trim($post['top_t1']),
					'top_t2' => trim($post['top_t2']),
					'top_t3' => trim($post['top_t3']),
					'top_tf' => trim($post['top_tf']),
					'top_rt' => trim($post['top_rt'])
			];
			$this->db->update($this->table_name, ['top' => json_encode($top)], [$this->primary_key => $uid]);

			// update attach data
			$attc =[];
			// cek item attach
			if(isset($post['attcdescription'])){
				$item_num = count($post['attcdescription']); // cek sum
				$item_len_min = min(array_keys($post['attcdescription'])); // cek min key index
				$item_len = max(array_keys($post['attcdescription'])); // cek max key index
			} else {
				$item_num = 0;
			}
			
			if($item_num>0){
				for($i=$item_len_min;$i<=$item_len;$i++) 
				{
					if(isset($post['attcdescription'][$i])){
						$itemData = [
							'description' => trim($post['attcdescription'][$i]),
							];

						$itemData['file'] = '';
						if(isset($fdoc[$i]->file) && !empty($fdoc[$i]->file)){
							$itemData['file'] = $fdoc[$i]->file;
						}
						$upload = $this->upload_file($uid, 'attcfile', TRUE, $itemData['file'], TRUE, $i);
						if($upload['status']){
							$itemData['file'] = $upload['upload_file'];
						} else if(isset($upload['error_warning'])){
							echo $upload['error_warning']; exit;
						}
						$attc[] = $itemData;
					}
				}
			}
			$this->db->update($this->table_name, ['document' => json_encode($attc)], [$this->primary_key => $uid]);
			$this->db->trans_complete();

			$rs = $this->db->trans_status();
			return [$rs,$uid];
		} else return null;
	}  

	// getting row data for update / detail view
	public function getRowData($id) { 
		$rs = $this->db->select('*,DATE_FORMAT(FROM_UNIXTIME(date_po), "%d-%m-%Y") as dpo,DATE_FORMAT(FROM_UNIXTIME(date_due), "%d-%m-%Y") as ddue')->where([$this->primary_key => $id])->get($this->table_name)->row();
		if(!empty($rs->id_quotation)){
			$rd = $this->db->select('quotation')->where([$this->primary_key => $rs->id_quotation])->get($this->table_quotation)->row();
			$rs = (object) array_merge((array) $rs, (array) $rd);
		} else {
			$rs = (object) array_merge((array) $rs, ['quotation'=>'']);
		}
		if(!empty($rs->id_project)){
			$rd = $this->db->select('project,title as project_title')->where([$this->primary_key => $rs->id_project])->get($this->table_project)->row();
			$rs = (object) array_merge((array) $rs, (array) $rd);
		} else {
			$rs = (object) array_merge((array) $rs, ['project'=>'','title'=>'','nilai_proyek'=>'','total_worth'=>'0']);
		}
		if(!empty($rs->id_customer)){
			$rd = $this->getCustomerInfo($rs->id_customer);
			$rs = (object) array_merge((array) $rs, (array) $rd);
		} else {
			$rs = (object) array_merge((array) $rs, ['id_customer'=>'','customer_name'=>'','customer_address'=>'','customer_contact'=>'']);
		}
		if(!empty($rs->id_term)){
			$rd = $this->db->select('code as term')->where([$this->primary_key => $rs->id_term])->get($this->table_term)->row();
			$rs = (object) array_merge((array) $rs, (array) $rd);
		} else {
			$rs = (object) array_merge((array) $rs, ['term'=>'']);
		}
		if(!empty($rs->id_currency)){
			$rd = $this->db->select('code as currency')->where([$this->primary_key => $rs->id_currency])->get($this->table_currency)->row();
			$rs = (object) array_merge((array) $rs, (array) $rd);
		} else {
			$rs = (object) array_merge((array) $rs, ['currency'=>'']);
		}
		if(!empty($rs->id_pic)){
			$rd = $this->db->select('name as sales_pic')->where([$this->primary_key => $rs->id_pic])->get($this->table_karyawan)->row();
			$rs = (object) array_merge((array) $rs, (array) $rd);
		} else {
			$rs = (object) array_merge((array) $rs, ['sales_pic'=>'']);
		}
		if(!empty($rs->id_status)){
			$rd = $this->db->select('description as status')->where([$this->primary_key => $rs->id_status])->get($this->table_status)->row();
			$rs = (object) array_merge((array) $rs, (array) $rd);
		} else {
			$rs = (object) array_merge((array) $rs, ['status'=>'']);
		}
		unset($rs->date_insert);
		unset($rs->insert_by);
		unset($rs->date_update);
		unset($rs->update_by);
		
		return $rs;
	} 

	// importing data
	// TODO: Need fixing import function
	public function import_data($list_data)
	{
		$i = 0;
		$error = '';
		
		foreach ($list_data as $k => $v) {
			$i += 1;

			$id_pic = !empty($v["D"])? trim($v["D"]):0;
			$id_customer = !empty($v["D"])? trim($v["D"]):0;
			$id_term = !empty($v["H"])? trim($v["H"]):NULL;
			$id_currency = !empty($v["I"])? trim($v["I"]):NULL;
			$id_quotation = !empty($v["I"])? trim($v["I"]):NULL;
			$id_project = !empty($v["I"])? trim($v["I"]):NULL;
			$id_status = !empty($v["I"])? trim($v["I"]):NULL;
			$data = [
				'po' 				=> trim($v["B"]),
				'id_customer' 		=> $id_customer,
				'description' 		=> trim($v["F"]),
				'worth' 			=> trim($v["G"]),
				'id_term' 			=> $id_term,
				'id_karyawan' 		=> $id_karyawan,
				'id_quotation' 		=> $id_quotation,
				'id_project' 		=> $id_project,
				'id_status' 		=> $id_status,
				'insert_by'			=> $_SESSION["username"]
			];

			$dpo = trim($v["C"]);
			if(empty($dpo)){
					$data['date_po'] = NULL;
			} else {
					$data['date_po'] = strtotime($dpo);
			}

			$ddue = trim($v["D"]);
			if(empty($ddue)){
					$data['date_due'] = NULL;
			} else {
					$data['date_due'] = strtotime($ddue);
			}

			$rs = $this->db->insert($this->table_name, $data);
			if (!$rs) $error .=",baris ". $v["A"];
		}

		return $error;
	}

	// export data
	// Need use GET for accept parameter
	// TODO: Need fixing export function
	public function eksport_data()
	{
		$sql = "SELECT
					a.id,
					a.po,
					b.name as customer,
					DATE_FORMAT(FROM_UNIXTIME(a.date_po), '%d-%m-%Y') as dpo,
					DATE_FORMAT(FROM_UNIXTIME(a.date_due), '%d-%m-%Y') as ddue,
					a.description,
					a.worth as wpo,
					c.description as status
			FROM
	   	 		".$this->table_name." a 
			LEFT JOIN 
				".$this->table_customer." b ON b.id=a.id_customer 
			LEFT JOIN 
				".$this->table_status." c ON c.id=a.id_status 
	   		ORDER BY
	   			a.".$this->primary_key." ASC
		";

		$res = $this->db->query($sql);
		$rs = $res->result_array();
		return $rs;
	}

	//============================== For Additional Method ==============================//
	// Get customer info
	public function getCustomerInfo($id)
	{ 
		$rs = $this->db->select('id as id_customer,name as customer_name,address as customer_address,contact_name as customer_contact')->where([$this->primary_key => $id])->get($this->table_customer)->row();

		return $rs;
	} 

	// Get project info
	public function getProjectInfo($id)
	{ 
		$rs = $this->db->select('project,title as project_title,id_customer')->where([$this->primary_key => $id])->get($this->table_project)->row();
		if(!empty($rs->id_customer)){
			$rd = $this->getCustomerInfo($rs->id_customer);
			$rs = (object) array_merge((array) $rs, (array) $rd);
		} else {
			$rs = (object) array_merge((array) $rs, ['id_customer'=>'','customer_name'=>'','customer_address'=>'','customer_contact'=>'']);
		}

		return $rs;
	} 

	// Generate new po item row
	public function getNewPoItemRow($row,$id=0,$view=FALSE)
	{ 
		if($id > 0){
			$data = $this->getPoItemRows($id,$view);
		} else {
			$data = '';
			$no = $row+1;
			$oSatuan 	= $this->db->select('id,description')->order_by('idx_seq', 'ASC')->where("active='1'")->get($this->table_uom)->result();
			$data 	.= '<td>'.$no.'</td>';
			$data 	.= '<td>'.$this->return_build_txtarea('','qidescription['.$row.']','','','qidescription','','data-id="'.$row.'"').'</td>';
			$data 	.= '<td>'.$this->return_build_txt(number_format(0,0,',','.'),'qty['.$row.']','','qty','text-align: right;','data-id="'.$row.'"').'</td>';
			$data 	.= '<td>'.$this->return_build_chosenme($oSatuan,'','','','satuan['.$row.']','','satuan','','id','description','','','',' data-id="'.$row.'"').'</td>';
			$data 	.= '<td>'.$this->return_build_txt(number_format(0,0,',','.'),'price['.$row.']','','price','text-align: right;','data-id="'.$row.'"').'</td>';
			$data 	.= '<td>'.$this->return_build_txt(number_format(0,2,',','.'),'subtotal['.$row.']','','subtotal','text-align: right;','data-id="'.$row.'" readonly').'</td>';
			$data 	.= '<td><input type="button" class="ibtnDel btn btn-md btn-danger "  value="Delete"></td>';
		}

		return $data;
	} 
	
	// Generate po item rows for edit & view
	public function getPoItemRows($id,$view,$print=FALSE){
		$dt = '';
		$rs = $this->db->select('data_po')->where([$this->primary_key => $id])->get($this->table_name)->row();
		$rd = json_decode($rs->data_po);

		$row = 0;
		if(!empty($rd)){
			$rs_num = count($rd);
			$oSatuan 	= $this->db->select('id,description')->order_by('idx_seq', 'ASC')->where("active='1'")->get($this->table_uom)->result();
			if($view){
				$arrSat = json_decode(json_encode($oSatuan), true);
				$arrS = [];
				foreach($arrSat as $ai){
					$arrS[$ai['id']] = $ai;
				}
			}
			foreach ($rd as $f){
				$no = $row+1;
				if(!$view){
					$dt .= '<tr>';
					$dt .= '<td>'.$no.'</td>';
					$dt .= '<td>'.$this->return_build_txtarea($f->description,'qidescription['.$row.']','','','qidescription','','data-id="'.$row.'"').'</td>';
					$dt .= '<td>'.$this->return_build_txt(number_format($f->qty,0,',','.'),'qty['.$row.']','','qty','text-align: right;','data-id="'.$row.'"').'</td>';
					$dt .= '<td>'.$this->return_build_chosenme($oSatuan,'',$f->satuan,'','satuan['.$row.']','','satuan','','id','description','','','',' data-id="'.$row.'"').'</td>';
					$dt .= '<td>'.$this->return_build_txt(number_format($f->price,0,',','.'),'price['.$row.']','','price','text-align: right;','data-id="'.$row.'"').'</td>';
					$dt .= '<td>'.$this->return_build_txt(number_format($f->subtotal,2,',','.'),'subtotal['.$row.']','','subtotal','text-align: right;','data-id="'.$row.'" readonly').'</td>';
					$dt .= '<td><input type="button" class="ibtnDel btn btn-md btn-danger "  value="Delete"></td>';
					$dt .= '</tr>';
				} else {
					if($print){
						if($row == ($rs_num-1)){
							$dt .= '<tr class="item last">';
						} else {
							$dt .= '<tr class="item">';
						}
					} else {
						$dt .= '<tr>';
					}
					$dt .= '<td>'.$no.'</td>';
					$dt .= '<td>'.$f->description.'</td>';
					$dt .= '<td style="text-align: right;">'.number_format($f->qty,0,',','.').'</td>';
					$dt .= '<td style="text-align: right;">'.$arrS[(isset($f->satuan) && !empty($f->satuan))?$f->satuan:1]['description'].'</td>';
					$dt .= '<td style="text-align: right;">'.number_format($f->price,0,',','.').'</td>';
					$dt .= '<td style="text-align: right;">'.number_format($f->subtotal,0,',','.').'</td>';
					$dt .= '</tr>';
				}

				$row++;
			}
		}

		return [$dt,$row];
	}
	
	// Generate new attachment row
	public function getNewAttcRow($row,$id=0,$view=FALSE)
	{ 
		if($id > 0){
			$data = $this->getAttcRows($id,$view);
		} else {
			$data = '';
			$no = $row+1;
			$data 	.= '<td>'.$no.'</td>';
			$data 	.= '<td>'.$this->return_build_txtarea('','attcdescription['.$row.']','','','attcdescription','','data-id="'.$row.'"').'</td>';
			$data 	.= '<td>'.$this->return_build_fileinput('attcfile['.$row.']','attcfile_'.$row, '', '', '', 'data-id="'.$row.'"').'</td>';
			$data 	.= '<td><input type="button" class="pibtnDel btn btn-md btn-danger " data-id="'.$row.'" value="Delete"></td>';
		}

		return $data;
	} 
	
	// Generate attachment rows for edit & view
	public function getAttcRows($id,$view,$print=FALSE){
		$dt = '';
		$rs = $this->db->select('document')->where([$this->primary_key => $id])->get($this->table_name)->row();
		$rd = json_decode($rs->document);

		$row = 0;
		if(!empty($rd)){
			$rs_num = count($rd);
			foreach ($rd as $f){
				$no = $row+1;
				if(!$view){
					$dt .= '<tr>';
					$dt .= '<td>'.$no.'</td>';
					$dt .= '<td>'.$this->return_build_txtarea($f->description,'attcdescription['.$row.']','','','attcdescription','','data-id="'.$row.'"').'</td>';
					$dt .= '<td>'.$this->return_build_fileinput('attcfile['.$row.']','attcfile_'.$row, '', '', '', 'data-id="'.$row.'"').'<span>'.$this->setLink($id,$f->file,'attcfile_'.$row).'</span></td>';
					$dt .= '<td><input type="button" class="pibtnDel btn btn-md btn-danger " data-id="'.$row.'" value="Delete"></td>';
				} else {
					if($print){
						if($row == ($rs_num-1)){
							$dt .= '<tr class="item last">';
						} else {
							$dt .= '<tr class="item">';
						}
					} else {
						$dt .= '<tr>';
					}
					$dt .= '<td>'.$no.'</td>';
					$dt .= '<td>'.$f->description.'</td>';
					$dt .= '<td>'.$this->setLink($id,$f->file).'</td>';
					$dt .= '</tr>';
				}

				$row++;
			}
		}

		return [$dt,$row];
	}

	// Generate file link
	private function setLink($i,$f,$holder=''){
		$link = '';
		if($f){
			$link = '<a href="/uploads/data/po_in/'.$i.'/'.$f.'" class="'.$holder.'" target="_blank">'.$f.'</a>';
		}
		
		return $link;
	}

	// Removing file while using edit form
	public function rmAttc($id,$file){
		$this->remove_file($id,$file);
	}
}
