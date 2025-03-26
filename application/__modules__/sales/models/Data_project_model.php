<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Data_project_model extends MY_Model
{
	/* Module */
 	protected $folder_name			= "sales/data_project";
 	protected $table_name 			= _PREFIX_TABLE."data_project";
 	protected $table_customer 		= _PREFIX_TABLE."data_customer";
 	protected $table_spk 			= _PREFIX_TABLE."data_spk";
 	protected $table_po		 		= _PREFIX_TABLE."data_po";
 	protected $table_project_scope 	= _PREFIX_TABLE."option_project_scope";
 	protected $table_sla 			= _PREFIX_TABLE."option_sla";
 	protected $table_karyawan 		= _PREFIX_TABLE."data_karyawan";
 	protected $table_status 		= _PREFIX_TABLE."option_project_status";
 	protected $table_jabatan 		= _PREFIX_TABLE."option_jabatan";
 	protected $table_departemen 	= _PREFIX_TABLE."option_departemen";
 	protected $primary_key 			= "id";
 	protected $project_key 			= "id_project";

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
			'a.project',
			'a.title',
			'b.code as ccustomer',
			'c.description as dept',
			'IF(a.type="1","External","Internal") as tipe',
			'IF(a.date_plan_start IS NULL,"",DATE_FORMAT(FROM_UNIXTIME(a.date_plan_start), "%d-%m-%Y")) as dpstart',
			'IF(a.date_plan_finish IS NULL,"",DATE_FORMAT(FROM_UNIXTIME(a.date_plan_finish), "%d-%m-%Y")) as dpfinish',
			'IF(a.date_actual_start IS NULL,"",DATE_FORMAT(FROM_UNIXTIME(a.date_actual_start), "%d-%m-%Y")) as dastart',
			'IF(a.date_actual_finish IS NULL,"",DATE_FORMAT(FROM_UNIXTIME(a.date_actual_finish), "%d-%m-%Y")) as dafinish',
			'd.description as status'
		];

		$sIndexColumn = 'a.'.$this->primary_key;
		$sTable = ' '.$this->table_name.' a
					LEFT JOIN '.$this->table_customer.' b ON b.id=a.id_customer
					LEFT JOIN '.$this->table_departemen.' c ON c.id=a.id_dept
					LEFT JOIN '.$this->table_status.' d ON d.id=a.id_status
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
				$row->project,
				$row->title,
				$row->ccustomer,
				$row->dept,
				$row->tipe,
				$row->dpstart,
				$row->dpfinish,
				$row->dastart,
				$row->dafinish,
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
		$id_customer = (isset($post['id_customer']) && !empty($post['id_customer']))? trim($post['id_customer']):0;
		$id_spk = (isset($post['id_spk']) && !empty($post['id_spk']))? trim($post['id_spk']):0;
		$id_pic = (isset($post['id_pic']) && !empty($post['id_pic']))? trim($post['id_pic']):0;
		$id_pm = (isset($post['id_pm']) && !empty($post['id_pm']))? trim($post['id_pm']):0;
		$id_gm = (isset($post['id_gm']) && !empty($post['id_gm']))? trim($post['id_gm']):0;
		$id_adm = (isset($post['id_adm']) && !empty($post['id_adm']))? trim($post['id_adm']):0;
		$id_sla = (isset($post['id_sla']) && !empty($post['id_sla']))? trim($post['id_sla']):0;
		$id_dept = (isset($post['id_dept']) && !empty($post['id_dept']))? trim($post['id_dept']):NULL;
		$type = (isset($post['type']) && !empty($post['type']))? trim($post['type']):0;
		$id_status = (isset($post['id_status']) && !empty($post['id_status']))? trim($post['id_status']):NULL;
		$data = [
			'project' 				=> trim($post['project']),
			'title' 				=> trim($post['title']),
			'id_customer' 			=> $id_customer,
			'id_spk' 				=> $id_spk,
			'project_scope' 		=> json_encode((isset($post['project_scope']) && !empty($post['project_scope']))? $post['project_scope']:[]),
			'id_sla' 				=> $id_sla,
			'id_member' 			=> json_encode((isset($post['id_member']) && !empty($post['id_member']))? $post['id_member']:[]),
			'id_pic' 				=> $id_pic,
			'id_pm' 				=> $id_pm,
			'id_gm' 				=> $id_gm,
			'id_adm' 				=> $id_adm,
			'id_dept' 				=> $id_dept,
			'type' 					=> $type,
			'date_plan_start' 		=> $this->date_to_int(trim($post['date_plan_start'])),
			'date_plan_finish' 		=> $this->date_to_int(trim($post['date_plan_finish'])),
			'date_actual_start' 	=> $this->date_to_int(trim($post['date_actual_start'])),
			'date_actual_finish'	=> $this->date_to_int(trim($post['date_actual_finish'])),
			'id_status' 			=> $id_status,
			'insert_by'				=> $_SESSION["username"]
		];

		return $rs = $this->db->insert($this->table_name, $data);
	}  

	// update data
	public function edit_data($post) { 
		if(!empty($post['id'])){
			$id_customer = (isset($post['id_customer']) && !empty($post['id_customer']))? trim($post['id_customer']):0;
			$id_spk = (isset($post['id_spk']) && !empty($post['id_spk']))? trim($post['id_spk']):0;
			$id_pic = (isset($post['id_pic']) && !empty($post['id_pic']))? trim($post['id_pic']):0;
			$id_pm = (isset($post['id_pm']) && !empty($post['id_pm']))? trim($post['id_pm']):0;
			$id_gm = (isset($post['id_gm']) && !empty($post['id_gm']))? trim($post['id_gm']):0;
			$id_adm = (isset($post['id_adm']) && !empty($post['id_adm']))? trim($post['id_adm']):0;
			$id_sla = (isset($post['id_sla']) && !empty($post['id_sla']))? trim($post['id_sla']):0;
			$id_dept = (isset($post['id_dept']) && !empty($post['id_dept']))? trim($post['id_dept']):NULL;
			$type = (isset($post['type']) && !empty($post['type']))? trim($post['type']):0;
			$id_status = (isset($post['id_status']) && !empty($post['id_status']))? trim($post['id_status']):NULL;
			$data = [
				'project' 				=> trim($post['project']),
				'title' 				=> trim($post['title']),
				'id_customer' 			=> $id_customer,
				'id_spk' 				=> $id_spk,
				'project_scope' 		=> json_encode((isset($post['project_scope']) && !empty($post['project_scope']))? $post['project_scope']:[]),
				'id_sla' 				=> $id_sla,
				'id_member' 			=> json_encode((isset($post['id_member']) && !empty($post['id_member']))? $post['id_member']:[]),
				'id_pic' 				=> $id_pic,
				'id_pm' 				=> $id_pm,
				'id_gm' 				=> $id_gm,
				'id_adm' 				=> $id_adm,
				'id_dept' 				=> $id_dept,
				'type' 					=> $type,
				'date_plan_start' 		=> $this->date_to_int(trim($post['date_plan_start'])),
				'date_plan_finish' 		=> $this->date_to_int(trim($post['date_plan_finish'])),
				'date_actual_start' 	=> $this->date_to_int(trim($post['date_actual_start'])),
				'date_actual_finish'	=> $this->date_to_int(trim($post['date_actual_finish'])),
				'id_status' 			=> $id_status,
				'update_by'				=> $_SESSION["username"]
			];

			return  $rs = $this->db->update($this->table_name, $data, [$this->primary_key => trim($post['id'])]);
		} else return null;
	}  

	// getting row data for update / detail view
	public function getRowData($id) { 
		$rs = $this->db->select('*,IF(date_plan_start IS NULL,"",DATE_FORMAT(FROM_UNIXTIME(date_plan_start), "%d-%m-%Y")) as dpstart, IF(date_plan_finish IS NULL,"",DATE_FORMAT(FROM_UNIXTIME(date_plan_finish), "%d-%m-%Y")) as dpfinish, IF(date_actual_start IS NULL,"",DATE_FORMAT(FROM_UNIXTIME(date_actual_start), "%d-%m-%Y")) as dastart, IF(date_actual_finish IS NULL,"",DATE_FORMAT(FROM_UNIXTIME(date_actual_finish), "%d-%m-%Y")) as dafinish')->where([$this->primary_key => $id])->get($this->table_name)->row();
		if(!empty($rs->project_scope)){
			$scp['scope'] = $this->build_project_scope_info($rs->project_scope);
			$rs = (object) array_merge((array) $rs, $scp);
		} else {
			$rs = (object) array_merge((array) $rs, ['scope'=>'']);
		}
		if(!empty($rs->id_customer)){
			$rd = $this->db->select('code as ccustomer')->where([$this->primary_key => $rs->id_customer])->get($this->table_customer)->row();
			$rs = (object) array_merge((array) $rs, (array) $rd);
		} else {
			$rs = (object) array_merge((array) $rs, ['ccustomer'=>'']);
		}
		if(!empty($rs->id_spk)){
			$rd = $this->db->select('spk')->where([$this->primary_key => $rs->id_spk])->get($this->table_spk)->row();
			$rs = (object) array_merge((array) $rs, (array) $rd);
		} else {
			$rs = (object) array_merge((array) $rs, ['spk'=>'']);
		}
		if(!empty($rs->id_pic)){
			$rd = $this->db->select('name as pic')->where([$this->primary_key => $rs->id_pic])->get($this->table_karyawan)->row();
			$rs = (object) array_merge((array) $rs, (array) $rd);
		} else {
			$rs = (object) array_merge((array) $rs, ['pic'=>'']);
		}
		if(!empty($rs->id_pm)){
			$rd = $this->db->select('name as pm')->where([$this->primary_key => $rs->id_pm])->get($this->table_karyawan)->row();
			$rs = (object) array_merge((array) $rs, (array) $rd);
		} else {
			$rs = (object) array_merge((array) $rs, ['pm'=>'']);
		}
		if(!empty($rs->id_gm)){
			$rd = $this->db->select('name as gm')->where([$this->primary_key => $rs->id_gm])->get($this->table_karyawan)->row();
			$rs = (object) array_merge((array) $rs, (array) $rd);
		} else {
			$rs = (object) array_merge((array) $rs, ['gm'=>'']);
		}
		if(!empty($rs->id_adm)){
			$rd = $this->db->select('name as adm')->where([$this->primary_key => $rs->id_adm])->get($this->table_karyawan)->row();
			$rs = (object) array_merge((array) $rs, (array) $rd);
		} else {
			$rs = (object) array_merge((array) $rs, ['adm'=>'']);
		}
		if(!empty($rs->id_sla)){
			$rd = $this->db->select('description as sla')->where([$this->primary_key => $rs->id_sla])->get($this->table_sla)->row();
			$rs = (object) array_merge((array) $rs, (array) $rd);
		} else {
			$rs = (object) array_merge((array) $rs, ['sla'=>'']);
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
	
	// build project_scope name from json set data id
	public function build_project_scope_info($id){
		$data = '';
		$rc = json_decode($id);
		if(!empty($rc)){
			$where_in = str_replace(['["','","','"]'],["'","','","'"],$id);
			$rk = $this->db->select('description')->where($this->primary_key.' IN ('.$where_in.')')->get($this->table_project_scope)->result();
			$dat = [];
			foreach($rk as $r){
				$dat[] = $r->description;
			}
			$data = implode(", ", $dat);
		}
		return $data;
	}

	// importing data
	// TODO: Need fixing import function
	public function import_data($list_data)
	{
		$i = 0;
		$error = '';
		
		foreach ($list_data as $k => $v) {
			$i += 1;

			$id_customer = (isset($post['id_customer']) && !empty($post['id_customer']))? trim($post['id_customer']):0;
			$id_spk = (isset($post['id_spk']) && !empty($post['id_spk']))? trim($post['id_spk']):0;
			$id_pic = (isset($post['id_pic']) && !empty($post['id_pic']))? trim($post['id_pic']):0;
			$id_pm = (isset($post['id_pm']) && !empty($post['id_pm']))? trim($post['id_pm']):0;
			$id_gm = (isset($post['id_gm']) && !empty($post['id_gm']))? trim($post['id_gm']):0;
			$id_adm = (isset($post['id_adm']) && !empty($post['id_adm']))? trim($post['id_adm']):0;
			$id_sla = (isset($post['id_sla']) && !empty($post['id_sla']))? trim($post['id_sla']):0;
			$id_dept = (isset($post['id_dept']) && !empty($post['id_dept']))? trim($post['id_dept']):NULL;
			$type = (isset($post['type']) && !empty($post['type']))? trim($post['type']):0;
			$id_status = (isset($post['id_status']) && !empty($post['id_status']))? trim($post['id_status']):NULL;
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
					a.project,
					a.title,
					a.description as pscope,
					b.code as ccustomer,
					c.spk,
					'' as po,
					IF(a.date_plan_start IS NULL,'',DATE_FORMAT(FROM_UNIXTIME(a.date_plan_start), '%d-%m-%Y')) as dpstart,
					IF(a.date_plan_finish IS NULL,'',DATE_FORMAT(FROM_UNIXTIME(a.date_plan_finish), '%d-%m-%Y')) as dpfinish,
					IF(a.date_actual_start IS NULL,'',DATE_FORMAT(FROM_UNIXTIME(a.date_actual_start), '%d-%m-%Y')) as dastart,
					IF(a.date_actual_finish IS NULL,'',DATE_FORMAT(FROM_UNIXTIME(a.date_actual_finish), '%d-%m-%Y')) as dafinish,
					'' as member,
					c.description as status
			FROM
	   	 		".$this->table_name." a 
			LEFT JOIN 
				".$this->table_project_scope." b ON b.id = a.id_project_scope
			LEFT JOIN 
				".$this->table_spk." c ON c.id = a.id_spk
			LEFT JOIN 
				".$this->table_customer." b ON b.id = a.id_customer
			LEFT JOIN 
				".$this->table_status." c ON c.id = a.id_status
	   		ORDER BY
	   			".$this->primary_key." ASC
		";

		$res = $this->db->query($sql);
		$rs = $res->result_array();
		return $rs;
	}

	//============================== For Additional Method ==============================//
	// Generate new member row
	public function getNewMemberRow($row,$id=0,$view=FALSE)
	{ 
		if($id > 0){
			$data = $this->getMemberRows($id,$view);
		} else {
			$data = '';
			$no = $row+1;
			$oEmployee 	= $this->db->select('id,name')->order_by('name', 'ASC')->where("id_status='1'")->get($this->table_karyawan)->result();
			$data 		.= '<td>'.$no.'</td>';
			$data 		.= '<td>'.$this->return_build_chosenme($oEmployee,'','','','id_member['.$row.']','','id_member','','id','name','','','',' meta:index="'.$row.'"').'</td>';
			$data 		.= '<td><span class="jabatan" data-id="'.$row.'"></span></td>';
			$data 		.= '<td><span class="telp" data-id="'.$row.'"></span></td>';
			$data 		.= '<td><span class="email" data-id="'.$row.'"></span></td>';
			$data 		.= '<td><input type="button" class="ibtnDel btn btn-md btn-danger "  value="Delete"></td>';
		}

		return $data;
	} 
	
	// Generate member rows for edit & view
	public function getMemberRows($id,$view,$print=FALSE){
		$dt = '';
		$rs = $this->db->select('id_member')->where([$this->primary_key => $id])->order_by('id', 'ASC')->get($this->table_name)->row();
		$rd = json_decode($rs->id_member);

		$row = 0;
		if(!empty($rd)){
			$rs_num = count($rd);
			$oEmployee 	= $this->db->select('id,name')->order_by('name', 'ASC')->where("id_status='1'")->get($this->table_karyawan)->result();
			foreach ($rd as $f){
				$no = $row+1;
				$nm = $jb = $tel = $em = '';
				if(!empty($f)){
					$rx 	= $this->getMemberInfo($f);
					$nm 	= $rx->name;
					$jb 	= $rx->jabatan;
					$tel 	= $rx->phone; 
					$em 	= $rx->email;
				}
				if(!$view){
					$dt .= '<tr>';
					$dt .= '<td>'.$no.'</td>';
					$dt .= '<td>'.$this->return_build_chosenme($oEmployee,'',$f,'','id_member['.$row.']','','id_member','','id','name','','','',' meta:index="'.$row.'"').'</td>';
					$dt .= '<td><span class="jabatan" data-id="'.$row.'">'.$jb.'</span></td>';
					$dt .= '<td><span class="telp" data-id="'.$row.'">'.$tel.'</span></td>';
					$dt .= '<td><span class="email" data-id="'.$row.'">'.$em.'</span></td>';
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
					$dt .= '<td>'.$nm.'</td>';
					$dt .= '<td>'.$jb.'</td>';
					$dt .= '<td>'.$tel.'</td>';
					$dt .= '<td>'.$em.'</td>';
					$dt .= '</tr>';
				}

				$row++;
			}
		}

		return [$dt,$row];
	}

	// Get member info
	public function getMemberInfo($id)
	{ 
		$rs = $this->db->select('a.name, a.phone, a.email, b.description as jabatan')->join($this->table_jabatan.' b', 'b.id = a.id_jabatan')->where(['a.'.$this->primary_key => $id])->get($this->table_karyawan.' a')->row();

		return $rs;
	} 

	// Generate new po row
	public function getNewPoRow($row,$id=0,$view=FALSE)
	{ 
		if($id > 0){
			$data = $this->getPoRows($id,$view);
		} else {
			$data = '';
		}

		return $data;
	} 
	
	// Generate po rows for edit & view
	public function getPoRows($id,$view,$print=FALSE){
		$dt = '';
		$rs = $this->db->select('po, description, format(worth,0,"id_ID") as wpo, DATE_FORMAT(FROM_UNIXTIME(date_po), "%d-%m-%Y") as dpo')->where([$this->project_key => $id])->order_by('id', 'ASC')->get($this->table_po)->result();

		$row = 0;
		if(!empty($rs)){
			$rs_num = count($rs);
			foreach ($rs as $f){
				$no = $row+1;
				if(!$view){
					$dt .= '<tr>';
					$dt .= '<td>'.$no.'</td>';
					$dt .= '<td>'.$f->po.'</td>';
					$dt .= '<td>'.$f->description.'</td>';
					$dt .= '<td>'.$f->dpo.'</td>';
					$dt .= '<td>'.$f->wpo.'</td>';
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
					$dt .= '<td>'.$f->po.'</td>';
					$dt .= '<td>'.$f->description.'</td>';
					$dt .= '<td>'.$f->dpo.'</td>';
					$dt .= '<td>'.$f->wpo.'</td>';
					$dt .= '</tr>';
				}

				$row++;
			}
		}

		return [$dt,$row];
	}

	// Get po info
	public function getPoInfo($id)
	{ 
		$rs = $this->db->select('po, description, worth, DATE_FORMAT(FROM_UNIXTIME(date_po), "%d-%m-%Y") as dpo')->where([$this->primary_key => $id])->get($this->table_po)->row();

		return $rs;
	} 
}
