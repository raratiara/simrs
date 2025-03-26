<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cost_model extends MY_Model
{
	/* Module */
 	protected $folder_name			= "finance/cost";
 	protected $table_name 			= _PREFIX_TABLE."data_project_cost";
 	protected $table_project_scope 	= _PREFIX_TABLE."option_project_scope";
 	protected $table_customer 		= _PREFIX_TABLE."data_customer";
 	protected $table_spk 			= _PREFIX_TABLE."data_spk";
 	protected $table_po		 		= _PREFIX_TABLE."data_po";
 	protected $table_karyawan 		= _PREFIX_TABLE."data_karyawan";
 	protected $table_status 		= _PREFIX_TABLE."option_project_status";
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
			'a.po',
			'b.name as customer',
			'DATE_FORMAT(FROM_UNIXTIME(a.date_po), "%d-%m-%Y") as dpo',
			'DATE_FORMAT(FROM_UNIXTIME(a.date_due), "%d-%m-%Y") as ddue',
			'format(a.worth,0,"id_ID") as wpo',
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
					'.$detail.'
					'.$edit.'
					'.$delete.'
				</div>',
				$row->id,
				$row->po,
				$row->customer,
				$row->dpo,
				$row->ddue,
				$row->wpo,
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

	// adding data
	public function add_data($post) {
		$id_customer 	= (isset($post['id_customer']) && !empty($post['id_customer']))? trim($post['id_customer']):NULL;
		$id_term 		= (isset($post['id_term']) && !empty($post['id_term']))? trim($post['id_term']):NULL;
		$id_karyawan 	= (isset($post['id_karyawan']) && !empty($post['id_karyawan']))? trim($post['id_karyawan']):NULL;
		$id_quotation 	= (isset($post['id_quotation']) && !empty($post['id_quotation']))? trim($post['id_quotation']):NULL;
		$id_project 	= (isset($post['id_project']) && !empty($post['id_project']))? trim($post['id_project']):NULL;
		$id_status 		= (isset($post['id_status']) && !empty($post['id_status']))? trim($post['id_status']):NULL;
		$data = [
			'po' 				=> trim($post['po']),
			'id_customer' 		=> $id_customer,
			'description' 		=> trim($post['description']),
			'worth' 			=> str_replace(",",".",str_replace(".","",trim($post['worth']))),
			'id_term' 			=> $id_term,
			'id_karyawan' 		=> $id_karyawan,
			'id_quotation' 		=> $id_quotation,
			'id_project' 		=> $id_project,
			'id_status' 		=> $id_status,
			'date_po' 			=> $this->date_to_int(trim($post['date_po'])),
			'date_due' 			=> $this->date_to_int(trim($post['date_due'])),
			'insert_by'			=> $_SESSION["username"]
		];

		return $rs = $this->db->insert($this->table_name, $data);
	}  

	// update data
	public function edit_data($post) { 
		if(!empty($post['id'])){
			$id_customer 	= (isset($post['id_customer']) && !empty($post['id_customer']))? trim($post['id_customer']):NULL;
			$id_term 		= (isset($post['id_term']) && !empty($post['id_term']))? trim($post['id_term']):NULL;
			$id_karyawan 	= (isset($post['id_karyawan']) && !empty($post['id_karyawan']))? trim($post['id_karyawan']):NULL;
			$id_quotation 	= (isset($post['id_quotation']) && !empty($post['id_quotation']))? trim($post['id_quotation']):NULL;
			$id_project 	= (isset($post['id_project']) && !empty($post['id_project']))? trim($post['id_project']):NULL;
			$id_status 		= (isset($post['id_status']) && !empty($post['id_status']))? trim($post['id_status']):NULL;
			$data = [
				'po' 				=> trim($post['po']),
				'id_customer' 		=> $id_customer,
				'description' 		=> trim($post['description']),
				'worth' 			=> str_replace(",",".",str_replace(".","",trim($post['worth']))),
				'id_term' 			=> $id_term,
				'id_karyawan' 		=> $id_karyawan,
				'id_quotation' 		=> $id_quotation,
				'id_project' 		=> $id_project,
				'id_status' 		=> $id_status,
				'date_po' 			=> $this->date_to_int(trim($post['date_po'])),
				'date_due' 			=> $this->date_to_int(trim($post['date_due'])),
				'update_by'			=> $_SESSION["username"]
			];

			return  $rs = $this->db->update($this->table_name, $data, [$this->primary_key => trim($post['id'])]);
		} else return null;
	}  

	// getting row data for update / detail view
	public function getRowData($id) { 
		$rs = $this->db->select('*,DATE_FORMAT(FROM_UNIXTIME(date_po), "%d-%m-%Y") as dpo,DATE_FORMAT(FROM_UNIXTIME(date_due), "%d-%m-%Y") as ddue')->where([$this->primary_key => $id])->get($this->table_name)->row();
		if(!empty($rs->id_customer)){
			$rd = $this->db->select('name as customer')->where([$this->primary_key => $rs->id_customer])->get($this->table_customer)->row();
			$rs = (object) array_merge((array) $rs, (array) $rd);
		} else {
			$rs = (object) array_merge((array) $rs, ['customer'=>'']);
		}
		unset($rs->date_insert);
		unset($rs->insert_by);
		unset($rs->date_update);
		unset($rs->update_by);
		
		return $rs;
	} 

	// importing data
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
	public function eksport_data()
	{
		$sql = "SELECT
					a.id,
					a.po,
					b.name as customer,
					DATE_FORMAT(FROM_UNIXTIME(a.date_po), '%d-%m-%Y') as dpo,
					DATE_FORMAT(FROM_UNIXTIME(a.date_due), '%d-%m-%Y') as ddue,
					a.description,
					format(a.worth,0,'id_ID') as wpo,
					c.description as status
			FROM
	   	 		".$this->table_name." a 
			LEFT JOIN 
				".$this->table_customer." b ON a.id_customer = b.id 
			LEFT JOIN 
				".$this->table_status." c ON a.id_status = c.id 
	   		ORDER BY
	   			".$this->primary_key." ASC
		";

		$res = $this->db->query($sql);
		$rs = $res->result_array();
		return $rs;
	}

}
