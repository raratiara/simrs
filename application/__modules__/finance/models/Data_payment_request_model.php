<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Data_payment_request_model extends MY_Model
{
	/* Module */
 	protected $folder_name			= "finance/data_payment_request";
 	protected $table_name 			= _PREFIX_TABLE."data_payment_request";
 	protected $table_ca 			= _PREFIX_TABLE."data_ca_request";
 	protected $table_reimburse 		= _PREFIX_TABLE."data_reimburse_request";
 	protected $table_project 		= _PREFIX_TABLE."data_project";
 	protected $table_wbs 			= _PREFIX_TABLE."option_wbs";
 	protected $table_karyawan 		= _PREFIX_TABLE."data_karyawan";
 	protected $table_approval 		= _PREFIX_TABLE."option_approval";
 	protected $table_bank 			= _PREFIX_TABLE."option_bank";
 	protected $primary_key 			= "id";
	/* upload */
 	protected $attachment_folder	= "./uploads/data/paymentreq";
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
			'a.pr',
			'b.title as project_title',
			'c.description as wbs',
			'if(a.for_type = "CA",(SELECT CONCAT(ca) FROM '.$this->table_ca.' WHERE id=a.id_for),if(a.for_type = "REIMBURSE",(SELECT GROUP_CONCAT(reim) FROM '.$this->table_reimburse.' WHERE id=a.id_for),a.id_for)) as noref',
			'a.trequest as wrequest',
			'a.description',
			'a.notes',
			'DATE_FORMAT(FROM_UNIXTIME(a.date_request), "%d-%m-%Y") as drequest',
			'if(a.for_type != "INVOICE",(SELECT CONCAT(name) FROM '.$this->table_karyawan.' WHERE id=a.id_payto),a.id_payto) as requestor',
			'd.description as status',
			'a.last_status'
		];

		$sIndexColumn = 'a.'.$this->primary_key;
		$sTable = ' '.$this->table_name.' a
					LEFT JOIN '.$this->table_project.' b ON b.id=a.id_project
					LEFT JOIN '.$this->table_wbs.' c ON c.id=a.id_wbs
					LEFT JOIN '.$this->table_approval.' d ON d.id=a.last_status
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
				if($row->last_status<2){
					$delete_bulk = '<input name="ids[]" type="checkbox" class="data-check" value="'.$row->id.'">';
					$delete = '<a class="btn btn-xs btn-danger" href="javascript:void(0);" onclick="deleting('."'".$row->id."'".')" role="button"><i class="fa fa-trash"></i></a>';
				}
			}

			array_push($output["aaData"],array(
				$delete_bulk,
				'<div class="action-buttons">
					'.$detail.'
					'.$edit.'
					'.$delete.'
				</div>',
				$row->id,
				$row->pr,
				$row->project_title,
				$row->wbs,
				$row->noref,
				number_format($row->wrequest,0,",","."),
				$row->description,
				$row->notes,
				$row->drequest,
				$row->requestor,
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
			$files = $this->db->select('data_request as document')->where([$this->primary_key => $id])->get($this->table_name)->row();
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
				$files = $this->db->select('data_request as document')->where([$this->primary_key => $pid])->get($this->table_name)->row();
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
	public function getNextNumber() { 
		$rs = $this->db->select_max('urutan')->where(['bulan' => date('n'),'tahun' => date('Y')])->get($this->table_name)->row();

		return $rs->urutan+1;
	} 

	// adding data
	public function add_data($post) {
		// BOF auto numbering 
		$lettercode = ('PR'); // reimburse code
		$yearcode = date('y'); // last 2 digit year code
		$monthcode = date('n'); // 2 digit month code
		$runningnumber = $this->getNextNumber(); // next count number
		$nextnum 	= $lettercode.$yearcode.$monthcode.str_pad($runningnumber, 4, "0", STR_PAD_LEFT);
		// EOF auto numbering 
		$last_status = (isset($post['last_status']) && !empty($post['last_status']))? trim($post['last_status']):'1';
		$id_project = (isset($post['id_project']) && !empty($post['id_project']))? trim($post['id_project']):NULL;
		$id_for = (isset($post['id_for']) && !empty($post['id_for']))? trim($post['id_for']):NULL;
		$id_wbs = (isset($post['id_wbs']) && !empty($post['id_wbs']))? trim($post['id_wbs']):NULL;
		$id_payto = (isset($post['id_payto']) && !empty($post['id_payto']))? trim($post['id_payto']):NULL;
		if(isset($post['as_draft']) && (!empty($post['as_draft']) && $post['as_draft']=='1')){
			$last_status == '1';
		} else {
			if($last_status == '1'){
				//$last_status++;
				$last_status = '7';
				$post['date_request'] = date("Y-m-d H:i:s");
			}
		}
		$data = [
			'pr' 				=> $nextnum,
			'urutan' 			=> $runningnumber,
			'bulan' 			=> date('n'),
			'tahun' 			=> date('Y'),
			'id_project' 		=> $id_project,
			'for_type' 			=> trim($post['for_type']),
			'id_for' 			=> $id_for,
			'id_wbs' 			=> $id_wbs,
			'description' 		=> trim($post['description']),
			'notes' 			=> trim($post['notes']),
			'last_status' 		=> $last_status,
			'date_request' 		=> $this->date_to_int(trim($post['date_request'])),
			'id_payto' 			=> $id_payto,
			'trf_type' 			=> trim($post['trf_type']),
			'trequest' 			=> str_replace(",",".",str_replace(".","",trim($post['total_pr']))),
			'insert_by'			=> $_SESSION["username"]
		];

		//$this->db->trans_off(); // Disable transaction
		$this->db->trans_start(); // set "True" for query will be rolled back
		$this->db->insert($this->table_name, $data);
		$lastId = $this->db->insert_id();
		// Add Reimburse Request data
		$caitem =[];
		// cek Reimburse Request item
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
						'description' 	=> trim($post['qidescription'][$i])
						];
					$upload = $this->upload_file($lastId, 'attcfile', FALSE, '', TRUE, $i);
					$itemData['file'] = '';
					if($upload['status']){
						$itemData['file'] = $upload['upload_file'];
					} else if(isset($upload['error_warning'])){
						echo $upload['error_warning']; exit;
					}
					$caitem[] = $itemData;
				}
			}
		}
		$this->db->update($this->table_name, ['data_request' => json_encode($caitem)], [$this->primary_key => $lastId]);
		$this->db->trans_complete();

		$rs = $this->db->trans_status();
		return $rs;
	}  

	// update data
	public function edit_data($post) { 
		if(!empty($post['id'])){
			$uid = trim($post['id']);
			$last_status = (isset($post['last_status']) && !empty($post['last_status']))? trim($post['last_status']):'1';
			$old_status = (isset($post['old_status']) && !empty($post['old_status']))? trim($post['old_status']):'1';
			if($last_status == '1' && $old_status == '1'){
				$files = $this->db->select('data_request as document')->where([$this->primary_key => $uid])->get($this->table_name)->row();
				$fdoc = json_decode($files->document);
				$id_project = (isset($post['id_project']) && !empty($post['id_project']))? trim($post['id_project']):NULL;
				$id_for = (isset($post['id_for']) && !empty($post['id_for']))? trim($post['id_for']):NULL;
				$id_wbs = (isset($post['id_wbs']) && !empty($post['id_wbs']))? trim($post['id_wbs']):NULL;
				$id_payto = (isset($post['id_payto']) && !empty($post['id_payto']))? trim($post['id_payto']):NULL;
				if(isset($post['as_draft']) && (!empty($post['as_draft']) && $post['as_draft']=='1')){
					$last_status == '1';
				} else {
					if($last_status == '1'){
						//$last_status++;
						$last_status = '7';
						if(empty($post['date_request'])){
							$post['date_request'] = date("Y-m-d H:i:s");
						}
					}
				}
				$data = [
					//'pr' 				=> trim($post['pr']),
					'id_project' 		=> $id_project,
					'for_type' 			=> trim($post['for_type']),
					'id_for' 			=> $id_for,
					'id_wbs' 			=> $id_wbs,
					'description' 		=> trim($post['description']),
					'notes' 			=> trim($post['notes']),
					'last_status' 		=> $last_status,
					'date_request' 		=> $this->date_to_int(trim($post['date_request'])),
					'id_payto' 			=> $id_payto,
					'trf_type' 			=> trim($post['trf_type']),
					'trequest' 			=> str_replace(",",".",str_replace(".","",trim($post['total_pr']))),
					'update_by'			=> $_SESSION["username"]
				];

				//$this->db->trans_off(); // Disable transaction
				$this->db->trans_start(); // set "True" for query will be rolled back
				$this->db->update($this->table_name, $data, [$this->primary_key => $uid]);
				// Update Reimburse Request
				$caitem =[];
				// cek Reimburse Request item
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
								'description' 	=> trim($post['qidescription'][$i])
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
							$caitem[] = $itemData;
						}
					}
				}
				$this->db->update($this->table_name, ['data_request' => json_encode($caitem)], [$this->primary_key => $uid]);
				$this->db->trans_complete();

				$rs = $this->db->trans_status();
			} else {
				$data = [
					'last_status' 		=> $last_status,
					'update_by'			=> $_SESSION["username"]
				];
				//$this->db->trans_off(); // Disable transaction
				$this->db->trans_start(); // set "True" for query will be rolled back
				$this->db->update($this->table_name, $data, [$this->primary_key => $uid]);
				$this->db->trans_complete();

				$rs = $this->db->trans_status();
			}
			return $rs;
		} else return null;
	}  

	// getting row data for update / detail view
	public function getRowData($id) { 
		$rs = $this->db->select('*,if(for_type = "CA",(SELECT CONCAT(ca) FROM '.$this->table_ca.' WHERE id=id_for),if(for_type = "REIMBURSE",(SELECT GROUP_CONCAT(reim) FROM '.$this->table_reimburse.' WHERE id=id_for),id_for)) as noref,DATE_FORMAT(FROM_UNIXTIME(date_request), "%d-%m-%Y") as drequest')->where([$this->primary_key => $id])->get($this->table_name)->row();
		if(!empty($rs->id_project)){
			$rd = $this->db->select('project,title as project_title')->where([$this->primary_key => $rs->id_project])->get($this->table_project)->row();
			$rs = (object) array_merge((array) $rs, (array) $rd);
		} else {
			$rs = (object) array_merge((array) $rs, ['project'=>'','project_title'=>'']);
		}
		if(!empty($rs->id_wbs)){
			$rd = $this->db->select('description as wbs')->where([$this->primary_key => $rs->id_wbs])->get($this->table_wbs)->row();
			$rs = (object) array_merge((array) $rs, (array) $rd);
		} else {
			$rs = (object) array_merge((array) $rs, ['wbs'=>'']);
		}
		if(!empty($rs->for_type) && $rs->for_type !== 'INVOICE'){
			if(!empty($rs->id_payto)){
				$getData = 'a.nik,a.name as requestor,a.name as namarek,a.id_bank,CONCAT(b.description," - ",a.rek) as norek';
				$rd = $this->db->select($getData)->join($this->table_bank.' b','b.id=a.id_bank','left')->where(['a.'.$this->primary_key => $rs->id_payto])->get($this->table_karyawan.' a')->row();
				$rs = (object) array_merge((array) $rs, (array) $rd);
			} else {
				$rs = (object) array_merge((array) $rs, ['nik'=>'','requestor'=>'','namarek'=>'','id_bank'=>'','norek'=>'']);
			}
		} else {
			// for invoice payment request
		}
		if(!empty($rs->last_status)){
			$rd = $this->db->select('description as status')->where([$this->primary_key => $rs->last_status])->get($this->table_approval)->row();
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

			$id_customer = trim($v["E"]);
			if(empty($id_customer)) $id_customer = NULL;
			$id_term = trim($v["H"]);
			if(empty($id_term)) $id_term = NULL;
			$id_karyawan = trim($v["I"]);
			if(empty($id_karyawan)) $id_karyawan = NULL;
			$id_quotation = trim($v["J"]);
			if(empty($id_quotation)) $id_quotation = NULL;
			$id_project = trim($v["K"]);
			if(empty($id_project)) $id_project = NULL;
			$id_status = trim($v["L"]);
			if(empty($id_status)) $id_status = NULL;
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
				a.pr,
				b.title as project_title,
				c.description as wbs,
				if(a.for_type = 'CA',(SELECT CONCAT(ca) FROM ".$this->table_ca." WHERE id=a.id_for),if(a.for_type = 'REIMBURSE',(SELECT GROUP_CONCAT(reim) FROM ".$this->table_reimburse." WHERE id=a.id_for),a.id_for)) as noref,
				a.trequest as wrequest,
				a.description,
				a.notes,
				DATE_FORMAT(FROM_UNIXTIME(a.date_request), '%d-%m-%Y') as drequest,
				if(a.for_type != 'INVOICE',(SELECT CONCAT(name) FROM ".$this->table_karyawan." WHERE id=a.id_payto),a.id_payto) as requestor,
				d.description as status
			FROM
	   	 		".$this->table_name." a 
			LEFT JOIN 
				".$this->table_project." b ON b.id=a.id_project 
			LEFT JOIN 
				".$this->table_wbs." c ON c.id=a.id_wbs 
			LEFT JOIN 
				".$this->table_approval." d ON d.id=a.last_status 
	   		ORDER BY
	   			a.".$this->primary_key." ASC
		";

		$res = $this->db->query($sql);
		$rs = $res->result_array();
		return $rs;
	}

	//============================== For Additional Method ==============================//
	// Get appropriate action button
	public function getActButton($id)
	{ 
		$button = '';
		$button .= '<button class="btn btn-info" id="submit-draft-data" onclick="savedraft()">
										<i class="fa fa-list"></i>
										Save Draft
									</button>';
		$button .= '<button class="btn btn-info" id="submit-data" onclick="save()">
										<i class="fa fa-check"></i>
										Save for Approval
									</button>';
		if(!empty($id)){
			$rs = $this->db->select('last_status')->where([$this->primary_key => $id])->get($this->table_name)->row();
			if($rs->last_status > 1){
				$button = '<button class="btn btn-info" id="submit-data" onclick="save()">
										<i class="fa fa-check"></i>
										Save
									</button>';
			}
			
		}

		return $button;
	} 

	// Get for id info
	public function getForIdInfo($id)
	{ 
		$data = '';
		if($id == 'CA'){
			$rs = $this->db->select('id,ca as description')->where("last_status IS NOT NULL AND last_status IN ('7')")->get($this->table_ca)->result();
		} else if($id == 'REIMBURSE') {
			$rs = $this->db->select('id,reim as description')->where("last_status IS NOT NULL AND last_status IN ('7')")->get($this->table_reimburse)->result();
		} else if($id == 'INVOICE') {
			$rs = (object) [];
		}
		$data = $this->return_select_option($rs, 'id','description');

		return $data;
	} 

	// Get payto info
	public function getPayToInfo($id)
	{ 
		$data = '';
		if($id == 'CA' || $id == 'REIMBURSE'){
			$rs = $this->db->select('id,name as description')->where("id_status IN ('1','2')")->get($this->table_karyawan)->result();
		} else if($id == 'INVOICE') {
			$rs = (object) [];
		}
		$data = $this->return_select_option($rs, 'id','description');

		return $data;
	} 

	// Get for id detail info
	public function getForIdDetailInfo($type,$id)
	{ 
		$rs = (object) [];
		if($type == 'CA'){
			$rs = $this->db->select('id,ca as description,id_project,id_wbs,trequest as wrequest')->where([$this->primary_key => $id])->get($this->table_ca)->row();
		} else if($type == 'REIMBURSE') {
			$rs = $this->db->select('id,reim as description,id_project,id_wbs,trequest as wrequest')->where([$this->primary_key => $id])->get($this->table_reimburse)->row();
		} else if($type == 'INVOICE') {
		}

		if(!empty($rs->id_project)){
			$rd = $this->db->select('project,title as project_title')->where([$this->primary_key => $rs->id_project])->get($this->table_project)->row();
			$rs = (object) array_merge((array) $rs, (array) $rd);
		} else {
			$rs = (object) array_merge((array) $rs, ['project'=>'','project_title'=>'']);
		}

		return $rs;
	} 

	// Get project info
	public function getProjectInfo($id)
	{ 
		$rs = $this->db->select('project,title as project_title')->where([$this->primary_key => $id])->get($this->table_project)->row();

		return $rs;
	} 

	// Get remaining CA budget info
	public function getRemainingCABudget($id)
	{ 
		$rs = $this->db->select('trequest,(SELECT sum(trequest) FROM '.$this->table_name.' WHERE for_type="CA" AND id_for="'.$id.'" AND last_status = "7") as hasbeenrequested')->where([$this->primary_key => $id])->get($this->table_ca)->row();
		$rd = [];
		if(empty($rs->hasbeenrequested)){
			$rd['hasbeenrequested'] = 0;
			$rd['remainingbudget'] = $rs->trequest;
		} else {
			$rd['remainingbudget'] = $rs->trequest - $rs->hasbeenrequested;
		}
		$rs = (object) array_merge((array) $rs, (array) $rd);

		return $rs;
	} 

	// Get requestor info
	public function getRequestorInfo($type,$id)
	{ 
		$rs = (object) [];
		if($type == 'CA' || $type == 'REIMBURSE'){
			$getData = 'a.nik,a.name as namarek,a.id_bank,CONCAT(b.description," - ",a.rek) as norek';
			$rs = $this->db->select($getData)->join($this->table_bank.' b','b.id=a.id_bank','left')->where(['a.'.$this->primary_key => $id])->get($this->table_karyawan.' a')->row();
		} else {
		}

		return $rs;
	} 

	// Generate new payment request attach item row
	public function getNewPaymentReqRow($row,$id=0,$view=FALSE)
	{ 
		if($id > 0){
			$data = $this->getPaymentReqRow($id,$view);
		} else {
			$data = '';
			$no = $row+1;
			$data 	.= '<td>'.$no.'</td>';
			$data 	.= '<td>'.$this->return_build_txtarea('','qidescription['.$row.']','','','qidescription','','data-id="'.$row.'"').'</td>';
			$data 	.= '<td>'.$this->return_build_fileinput('attcfile['.$row.']','attcfile_'.$row, '', '', '', 'data-id="'.$row.'"').'</td>';
			$data 	.= '<td><input type="button" class="ibtnDel btn btn-md btn-danger "  value="Delete"></td>';
		}

		return $data;
	} 
	
	// Generate payment request attach item rows for edit & view
	public function getPaymentReqRow($id,$view,$print=FALSE){
		$dt = '';
		$rs = $this->db->select('data_request')->where([$this->primary_key => $id])->get($this->table_name)->row();
		$rd = json_decode($rs->data_request);

		$row = 0;
		if(!empty($rd)){
			$rs_num = count($rd);
			foreach ($rd as $f){
				$no = $row+1;
				if(!$view){
					$dt .= '<tr>';
					$dt .= '<td>'.$no.'</td>';
					$dt .= '<td>'.$this->return_build_txtarea($f->description,'qidescription['.$row.']','','','qidescription','','data-id="'.$row.'"').'</td>';
					$dt .= '<td>'.$this->return_build_fileinput('attcfile['.$row.']','attcfile_'.$row, '', 'attcfile', '', 'data-id="'.$row.'"').'<span>'.$this->setLink($id,$f->file,'attcfile_'.$row).'</span></td>';
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
			$link = '<a href="/uploads/data/paymentreq/'.$i.'/'.$f.'" class="'.$holder.'" target="_blank">'.$f.'</a>';
		}
		
		return $link;
	}

	// Removing file while using edit form
	public function rmAttc($id,$file){
		$this->remove_file($id,$file);
	}
}
