<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Data_ca_request_model extends MY_Model
{
	/* Module */
 	protected $folder_name			= "finance/data_ca_request";
 	protected $table_name 			= _PREFIX_TABLE."data_ca_request";
 	protected $table_project 		= _PREFIX_TABLE."data_project";
 	protected $table_wbs 			= _PREFIX_TABLE."option_wbs";
 	protected $table_karyawan 		= _PREFIX_TABLE."data_karyawan";
 	protected $table_departemen 	= _PREFIX_TABLE."option_departemen";
 	protected $table_uom 			= _PREFIX_TABLE."option_uom";
 	protected $table_approval 		= _PREFIX_TABLE."option_approval";
 	protected $table_bank 			= _PREFIX_TABLE."option_bank";
 	protected $table_view_rab 		= _PREFIX_TABLE."data_rab_project";
 	protected $primary_key 			= "id";

	function __construct()
	{
		parent::__construct();
	}

	// Generate item list
	public function get_list_data()
	{
		$aColumns = [
			NULL,
			NULL,
			'a.id',
			'a.ca',
			'b.title as project_title',
			'c.description as wbs',
			'DATE_FORMAT(FROM_UNIXTIME(a.date_request), "%d-%m-%Y") as drequest',
			'a.trequest as wrequest',
			'(SELECT sisa_anggaran FROM '.$this->table_view_rab.' WHERE id=b.id ) as remaining',
			'd.name as requestor',
			'e.description as departemen',
			'f.description as status',
			'a.last_status',
		];

		$sIndexColumn = 'a.'.$this->primary_key;
		$sTable = ' '.$this->table_name.' a
					LEFT JOIN '.$this->table_project.' b ON b.id=a.id_project
					LEFT JOIN '.$this->table_wbs.' c ON c.id=a.id_wbs
					LEFT JOIN '.$this->table_karyawan.' d ON d.id=a.id_requestor
					LEFT JOIN '.$this->table_departemen.' e ON e.id=a.id_dept
					LEFT JOIN '.$this->table_approval.' f ON f.id=a.last_status
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
		$sRole = $this->session->userdata('role'); // role
		$sUser = $this->session->userdata('username'); // username
		$sWorker = $this->session->userdata('worker'); // worker data id
		$sTrict = "";
		if($sRole == 3 && !empty($sWorker)) $sTrict = " AND (a.insert_by = '".$sUser."' OR a.id_requestor = '".$sWorker."')"; // filtering role, if user then only show it's own
		if($sRole == 3 && empty($sWorker)) $sTrict = " AND a.insert_by = '".$sUser."'"; // filtering role, if user then only show it's own
		$sWhere = " WHERE 1 = 1".$sTrict." ";
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
		#var_dump($_SESSION);
		#echo '<br/>';
		#echo '<br/>';
		#echo $sQuery;exit;
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
				if($sRole < 3 || ($sRole == 3 && $row->last_status<2)){ // not role user or role user but still with draft status
					$edit = '<a class="btn btn-xs btn-primary" href="javascript:void(0);" onclick="edit('."'".$row->id."'".')" role="button"><i class="fa fa-pencil"></i></a>';
				}
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
				$row->ca,
				$row->project_title,
				$row->wbs,
				$row->drequest,
				number_format($row->wrequest,0,",","."),
				number_format($row->remaining,0,",","."),
				$row->requestor,
				$row->departemen,
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
		$lettercode = ('CA'); // ca code
		$yearcode = date('y'); // last 2 digit year code
		$monthcode = date('n'); // 2 digit month code
		$runningnumber = $this->getNextNumber(); // next count number
		$nextnum 	= $lettercode.$yearcode.$monthcode.str_pad($runningnumber, 4, "0", STR_PAD_LEFT);
		// EOF auto numbering 
		$last_status = (isset($post['last_status']) && !empty($post['last_status']))? trim($post['last_status']):'1';
		$id_project = (isset($post['id_project']) && !empty($post['id_project']))? trim($post['id_project']):NULL;
		$id_wbs = (isset($post['id_wbs']) && !empty($post['id_wbs']))? trim($post['id_wbs']):NULL;
		$id_requestor = (isset($post['id_requestor']) && !empty($post['id_requestor']))? trim($post['id_requestor']):NULL;
		$id_dept = (isset($post['id_dept']) && !empty($post['id_dept']))? trim($post['id_dept']):NULL;
		if(isset($post['as_draft']) && (!empty($post['as_draft']) && $post['as_draft']=='1')){
			$last_status == '1';
		} else {
			if($last_status == '1'){
				//$last_status++;
				$last_status = '7';
				$post['date_request'] = date("Y-m-d H:i:s");
			}
		}
		// Only for user role
		$sRole = $this->session->userdata('role'); // role
		$sWorker = $this->session->userdata('worker'); // worker data id
		if($sRole == 3 && !empty($sWorker)){
			$id_requestor = $sWorker;
		}		
		$data = [
			'ca' 				=> $nextnum,
			'urutan' 			=> $runningnumber,
			'bulan' 			=> date('n'),
			'tahun' 			=> date('Y'),
			'id_project' 		=> $id_project,
			'id_wbs' 			=> $id_wbs,
			'description' 		=> trim($post['description']),
			'last_status' 		=> $last_status,
			'date_request' 		=> $this->date_to_int(trim($post['date_request'])),
			'id_requestor' 		=> $id_requestor,
			'id_dept' 			=> $id_dept,
			'trf_type' 			=> trim($post['trf_type']),
			'trequest' 			=> str_replace(",",".",str_replace(".","",trim($post['total_ca']))),
			'insert_by'			=> $_SESSION["username"]
		];

		//$this->db->trans_off(); // Disable transaction
		$this->db->trans_start(); // set "True" for query will be rolled back
		$this->db->insert($this->table_name, $data);
		$lastId = $this->db->insert_id();
		// Add CA Request data
		$caitem =[];
		// cek CA Request item
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
						'jumlah' 		=> str_replace(",",".",str_replace(".","",trim($post['jumlah'][$i]))),
						'post_budget' 	=> trim($post['post_budget'][$i])
						];

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
				$id_project = (isset($post['id_project']) && !empty($post['id_project']))? trim($post['id_project']):NULL;
				$id_wbs = (isset($post['id_wbs']) && !empty($post['id_wbs']))? trim($post['id_wbs']):NULL;
				$id_requestor = (isset($post['id_requestor']) && !empty($post['id_requestor']))? trim($post['id_requestor']):NULL;
				$id_dept = (isset($post['id_dept']) && !empty($post['id_dept']))? trim($post['id_dept']):NULL;
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
				// Only for user role
				$sRole = $this->session->userdata('role'); // role
				$sWorker = $this->session->userdata('worker'); // worker data id
				if($sRole == 3 && !empty($sWorker)){
					$id_requestor = $sWorker;
				}		
				$data = [
					//'ca' 				=> trim($post['ca']),
					'id_project' 		=> $id_project,
					'id_wbs' 			=> $id_wbs,
					'description' 		=> trim($post['description']),
					'last_status' 		=> $last_status,
					'date_request' 		=> $this->date_to_int(trim($post['date_request'])),
					'id_requestor' 		=> $id_requestor,
					'id_dept' 			=> $id_dept,
					'trf_type' 			=> trim($post['trf_type']),
					'trequest' 			=> str_replace(",",".",str_replace(".","",trim($post['total_ca']))),
					'update_by'			=> $_SESSION["username"]
				];

				//$this->db->trans_off(); // Disable transaction
				$this->db->trans_start(); // set "True" for query will be rolled back
				$this->db->update($this->table_name, $data, [$this->primary_key => $uid]);
				// Update CA Request
				$caitem =[];
				// cek CA Request item
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
								'jumlah' 		=> str_replace(",",".",str_replace(".","",trim($post['jumlah'][$i]))),
								'post_budget' 	=> trim($post['post_budget'][$i])
								];

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
		$rs = $this->db->select('*,DATE_FORMAT(FROM_UNIXTIME(date_request), "%d-%m-%Y") as drequest')->where([$this->primary_key => $id])->get($this->table_name)->row();
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
		if(!empty($rs->id_requestor)){
			$getData = 'a.nik,a.name as requestor,a.name as namarek,a.id_bank,CONCAT(b.description," - ",a.rek) as norek';
			$rd = $this->db->select($getData)->join($this->table_bank.' b','b.id=a.id_bank','left')->where(['a.'.$this->primary_key => $rs->id_requestor])->get($this->table_karyawan.' a')->row();
			$rs = (object) array_merge((array) $rs, (array) $rd);
		} else {
			$rs = (object) array_merge((array) $rs, ['nik'=>'','requestor'=>'','namarek'=>'','id_bank'=>'','norek'=>'']);
		}
		if(!empty($rs->id_dept)){
			$rd = $this->db->select('description as departemen')->where([$this->primary_key => $rs->id_dept])->get($this->table_departemen)->row();
			$rs = (object) array_merge((array) $rs, (array) $rd);
		} else {
			$rs = (object) array_merge((array) $rs, ['departemen'=>'']);
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
				a.ca,
				b.title as project_title,
				c.description as wbs,
				DATE_FORMAT(FROM_UNIXTIME(a.date_request), '%d-%m-%Y') as drequest,
				a.trequest as wrequest,
				(SELECT sisa_anggaran FROM ".$this->table_view_rab." WHERE id=b.id ) as remaining,
				d.name as requestor,
				e.description as departemen,
				f.description as status
			FROM
	   	 		".$this->table_name." a 
			LEFT JOIN 
				".$this->table_project." b ON b.id=a.id_project 
			LEFT JOIN 
				".$this->table_wbs." c ON c.id=a.id_wbs 
			LEFT JOIN 
				".$this->table_karyawan." d ON d.id=a.id_requestor 
			LEFT JOIN 
				".$this->table_departemen." e ON e.id=a.id_dept 
			LEFT JOIN 
				".$this->table_approval." f ON f.id=a.last_status 
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

	// Get project info
	public function getProjectInfo($id)
	{ 
		$rs = $this->db->select('project,title as project_title,id_pic,id_dept')->where([$this->primary_key => $id])->get($this->table_project)->row();
		if(!empty($rs->id_pic)){
			if(!empty($rs->id_dept)){
				$getData = 'a.nik,a.name as namarek,a.id_bank,CONCAT(b.description," - ",a.rek) as norek';
			} else {
				$getData = 'a.nik,a.name as namarek,a.id_bank,CONCAT(b.description," - ",a.rek) as norek,a.id_departemen as id_dept';
			}
			$rd = $this->db->select($getData)->join($this->table_bank.' b','b.id=a.id_bank','left')->where(['a.'.$this->primary_key => $rs->id_pic])->get($this->table_karyawan.' a')->row();
			$rs = (object) array_merge((array) $rs, (array) $rd);
		} else {
			$rs = (object) array_merge((array) $rs, ['nik'=>'','namarek'=>'','id_bank'=>'','norek'=>'']);
		}
		if(!empty($rs->id_dept)){
			$rd = $this->db->select('description as departemen')->where([$this->primary_key => $rs->id_dept])->get($this->table_departemen)->row();
			$rs = (object) array_merge((array) $rs, (array) $rd);
		} else {
			$rs = (object) array_merge((array) $rs, ['departemen'=>'']);
		}

		// Only for user role
		$sRole = $this->session->userdata('role'); // role
		$sWorker = $this->session->userdata('worker'); // worker data id
		if($sRole == 3 && !empty($sWorker)){
			$getData = 'a.nik,a.name as namarek,a.id_bank,CONCAT(b.description," - ",a.rek) as norek';
			$rd = $this->db->select($getData)->join($this->table_bank.' b','b.id=a.id_bank','left')->where(['a.'.$this->primary_key => $sWorker])->get($this->table_karyawan.' a')->row();
			$rk = ['id_pic' => $sWorker];
			$rs = (object) array_merge((array) $rs, (array) $rd, $rk);
		}
		if($sRole == 3 && empty($sWorker)){
			$rd = ['nik' => '', 'namarek' => '', 'norek' => '', 'id_pic' => ''];
			$rs = (object) array_merge((array) $rs, $rd);
		}

		return $rs;
	} 

	// Get requestor info
	public function getRequestorInfo($id)
	{ 
		$getData = 'a.nik,a.name as namarek,a.id_bank,CONCAT(b.description," - ",a.rek) as norek';
		$rs = $this->db->select($getData)->join($this->table_bank.' b','b.id=a.id_bank','left')->where(['a.'.$this->primary_key => $id])->get($this->table_karyawan.' a')->row();

		return $rs;
	} 

	// Generate new expenses item row
	public function getNewExpensesRow($row,$id=0,$view=FALSE)
	{ 
		if($id > 0){
			$data = $this->getExpensesRows($id,$view);
		} else {
			$data = '';
			$no = $row+1;
			$oSatuan 		= $this->db->select('id,description')->order_by('idx_seq', 'ASC')->where("active='1'")->get($this->table_uom)->result();
			$oPostBudget 	= ['Transport','Meals','Materials','Acomodations','Tools','Other'];
			$data 	.= '<td>'.$no.'</td>';
			$data 	.= '<td>'.$this->return_build_txtarea('','qidescription['.$row.']','','','qidescription','','data-id="'.$row.'"').'</td>';
			$data 	.= '<td>'.$this->return_build_txt(number_format(0,0,',','.'),'qty['.$row.']','','qty','text-align: right;','data-id="'.$row.'"').'</td>';
			$data 	.= '<td>'.$this->return_build_chosenme($oSatuan,'','','','satuan['.$row.']','','satuan','','id','description','','','',' data-id="'.$row.'"').'</td>';
			$data 	.= '<td>'.$this->return_build_txt(number_format(0,0,',','.'),'price['.$row.']','','price','text-align: right;','data-id="'.$row.'"').'</td>';
			$data 	.= '<td>'.$this->return_build_txt(number_format(0,0,',','.'),'jumlah['.$row.']','','jumlah','text-align: right;','data-id="'.$row.'" readonly').'</td>';
			$data 	.= '<td>'.$this->return_build_simple_select($oPostBudget,'','','post_budget['.$row.']','','post_budget','','',' data-id="'.$row.'"').'</td>';
			$data 	.= '<td><input type="button" class="ibtnDel btn btn-md btn-danger "  value="Delete"></td>';
		}

		return $data;
	} 
	
	// Generate expenses item rows for edit & view
	public function getExpensesRows($id,$view,$print=FALSE){
		$dt = '';
		$rs = $this->db->select('data_request')->where([$this->primary_key => $id])->get($this->table_name)->row();
		$rd = json_decode($rs->data_request);

		$row = 0;
		if(!empty($rd)){
			$rs_num = count($rd);
			$oSatuan 		= $this->db->select('id,description')->order_by('idx_seq', 'ASC')->where("active='1'")->get($this->table_uom)->result();
			$oPostBudget 	= ['Transport','Meals','Materials','Acomodations','Tools','Other'];
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
					$dt .= '<td>'.$this->return_build_txt(number_format(isset($f->qty)?$f->qty:1,0,',','.'),'qty['.$row.']','','qty','text-align: right;','data-id="'.$row.'"').'</td>';
					$dt .= '<td>'.$this->return_build_chosenme($oSatuan,'',isset($f->satuan)?$f->satuan:1,'','satuan['.$row.']','','satuan','','id','description','','','',' data-id="'.$row.'"').'</td>';
					$dt .= '<td>'.$this->return_build_txt(number_format(isset($f->price)?$f->price:$f->jumlah,0,',','.'),'price['.$row.']','','price','text-align: right;','data-id="'.$row.'"').'</td>';
					$dt .= '<td>'.$this->return_build_txt(number_format($f->jumlah,0,',','.'),'jumlah['.$row.']','','jumlah','text-align: right;','data-id="'.$row.'" readonly').'</td>';
					$dt .= '<td>'.$this->return_build_simple_select($oPostBudget,'',$f->post_budget,'post_budget['.$row.']','','post_budget','','',' data-id="'.$row.'"').'</td>';
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
					$dt .= '<td style="text-align: right;">'.number_format(isset($f->qty)?$f->qty:1,0,',','.').'</td>';
					$dt .= '<td style="text-align: right;">'.$arrS[isset($f->satuan)?$f->satuan:1]['description'].'</td>';
					$dt .= '<td style="text-align: right;">'.number_format(isset($f->price)?$f->price:$f->jumlah,0,',','.').'</td>';
					$dt .= '<td style="text-align: right;">'.number_format($f->jumlah,0,',','.').'</td>';
					$dt .= '<td>'.$f->post_budget.'</td>';
					$dt .= '</tr>';
				}

				$row++;
			}
		}

		return [$dt,$row];
	}
}
