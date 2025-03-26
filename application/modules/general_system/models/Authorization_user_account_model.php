<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Authorization_user_account_model extends MY_Model
{
	/* Module */
 	protected $folder_name			= "general_system/authorization_user_account";
 	protected $table_name 			= _PREFIX_TABLE."user";
 	protected $table_role 			= _PREFIX_TABLE."user_group";
 	protected $table_karyawan 		= _PREFIX_TABLE."data_karyawan";
 	protected $table_jabatan 		= _PREFIX_TABLE."option_jabatan";
 	protected $primary_key 			= "user_id";
 	protected $general_key 			= "id";

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
			'a.user_id',
			'a.name',
			'a.username',
			'a.email',
			'b.name as role',
			'(CASE WHEN a.base_menu="custom" THEN "User"
					WHEN a.base_menu="role" THEN "Role"
					ELSE "Default"
				END) as bmenu',
			'(CASE WHEN a.isaktif="1" THEN "New - Non Aktif"
					WHEN a.isaktif="2" THEN "Aktif"
					WHEN a.isaktif="3" THEN "Non Aktif"
					ELSE "Suspended"
				END) as is_aktif'
		];

		$sIndexColumn = 'a.'.$this->primary_key;
		$sTable = ' '.$this->table_name.' a 
					LEFT JOIN '._PREFIX_TABLE.'user_group b ON b.id=a.id_groups
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
		$sRole = $this->session->userdata('role');
		$sTrict = "";
		if($sRole > '1') $sTrict = " AND id_groups > 1"; // filtering role not include superadmin
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
				$detail = '<a class="btn btn-xs btn-success detail-btn" href="javascript:void(0);" onclick="detail('."'".$row->user_id."'".')" role="button"><i class="fa fa-search-plus"></i></a>';
			}
			$edit = "";
			if (_USER_ACCESS_LEVEL_UPDATE == "1")  {
				$edit = '<a class="btn btn-xs btn-primary" href="javascript:void(0);" onclick="edit('."'".$row->user_id."'".')" role="button"><i class="fa fa-pencil"></i></a>';
			}
			$delete_bulk = "";
			$delete = "";
			if (_USER_ACCESS_LEVEL_DELETE == "1")  {
				$delete_bulk = '<input name="ids[]" type="checkbox" class="data-check" value="'.$row->user_id.'">';
				$delete = '<a class="btn btn-xs btn-danger" href="javascript:void(0);" onclick="deleting('."'".$row->user_id."'".')" role="button"><i class="fa fa-trash"></i></a>';
			}

			array_push($output["aaData"],array(
				$delete_bulk,
				'<div class="action-buttons">
					'.$detail.'
					'.$edit.'
					'.$delete.'
				</div>',
				$row->user_id,
				$row->name,
				$row->username,
				$row->email,
				$row->role,
				$row->bmenu,
				$row->is_aktif
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
		$id_karyawan = (isset($post['id_karyawan']) && !empty($post['id_karyawan']))? trim($post['id_karyawan']):NULL;
		$bmenu = (isset($post['base_menu']) && !empty($post['base_menu']))? trim($post['base_menu']):'role';
		$isaktif = (isset($post['isaktif']) && !empty($post['isaktif']))? trim($post['isaktif']):'2';
		$data = [
			'name' 				=> trim($post['name']),
			'username' 			=> strtolower(trim($post['username'])),
			'id_karyawan' 		=> $id_karyawan,
			'email' 			=> trim($post['email']),
			'id_groups' 		=> trim($post['id_groups']),
			'base_menu' 		=> $bmenu,
			'isaktif' 			=> $isaktif,
			'insert_by'			=> $_SESSION["username"]
		];
		
		if(!empty(trim($post['passwd'])) && (trim($post['passwd']) == trim($post['repasswd']))){
			$data['passwd'] = md5($this->db->escape_str(strtolower(trim($post['passwd']))));
		}

		return $rs = $this->db->insert($this->table_name, $data);
	}  

	// update data
	public function edit_data($post) { 
		if(!empty($post['id'])){
			$id_karyawan = (isset($post['id_karyawan']) && !empty($post['id_karyawan']))? trim($post['id_karyawan']):NULL;
			$bmenu = (isset($post['base_menu']) && !empty($post['base_menu']))? trim($post['base_menu']):'role';
			$isaktif = (isset($post['isaktif']) && !empty($post['isaktif']))? trim($post['isaktif']):'2';
			$data = [
				'name' 				=> trim($post['name']),
				//'username' 			=> strtolower(trim($post['username'])),
				'id_karyawan' 		=> $id_karyawan,
				'email' 			=> trim($post['email']),
				'id_groups' 		=> trim($post['id_groups']),
				'base_menu' 		=> $bmenu,
				'isaktif' 			=> $isaktif,
				'update_by'			=> $_SESSION["username"]
			];
		
			if(!empty(trim($post['passwd'])) && (trim($post['passwd']) == trim($post['repasswd']))){
				$data['passwd'] = md5($this->db->escape_str(strtolower(trim($post['passwd']))));
			}

			return $rs = $this->db->update($this->table_name, $data, [$this->primary_key => trim($post['id'])]);
		} else return null;
	}  

	// getting row data for update / detail view
	public function getRowData($id) { 
		$rs = $this->db->select('user_id,name,username,id_karyawan,email,id_groups,base_menu,(CASE WHEN base_menu="custom" THEN "User" WHEN base_menu="role" THEN "Role" ELSE "Default" END) as bmenu,isaktif')->where([$this->primary_key => $id])->get($this->table_name)->row();
		if(!empty($rs->id_karyawan)){
			$rd = $this->db->select('CONCAT(a.name," - ",b.description) as link')->join($this->table_jabatan.' b','b.id=a.id_jabatan','left')->where(['a.'.$this->general_key => $rs->id_karyawan])->get($this->table_karyawan.' a')->row();
			$rs = (object) array_merge((array) $rs, (array) $rd);
		}
		if(!empty($rs->id_groups)){
			$rd = $this->db->select('description as role')->where([$this->general_key => $rs->id_groups])->get($this->table_role)->row();
			$rs = (object) array_merge((array) $rs, (array) $rd);
		}
		
		return $rs;
	} 

	// importing data
	public function import_data($list_data)
	{
		$i = 0;
		$error = '';
		
		foreach ($list_data as $k => $v) {
			$i += 1;

			$id_karyawan = (isset($v["D"]) && !empty($v["D"]))? trim($v["D"]):NULL;
			$bmenu = (isset($v["G"]) && !empty($v["G"]))? trim($v["G"]):'role';
			$isaktif = (isset($v["I"]) && !empty($v["I"]))? trim($v["I"]):'2';
			$data = [
				'name' 				=> $v["B"],
				'username' 			=> $v["C"],
				'id_karyawan' 		=> $id_karyawan,
				'email' 			=> $v["E"],
				'id_groups' 		=> $v["F"],
				'base_menu' 		=> $bmenu,
				'passwd' 			=> md5($this->db->escape_str(strtolower(trim($v["H"])))),
				'isaktif' 			=> $isaktif,
				'insert_by'			=> $_SESSION["username"]
			];

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
					a.user_id,
					a.name,
					a.username,
					a.email,
					b.name as role,
					(CASE WHEN a.base_menu='custom' THEN 'User'
							WHEN a.base_menu='role' THEN 'Role'
							ELSE 'Default'
						END) as bmenu,
					(CASE WHEN a.isaktif='1' THEN 'New - Non Aktif'
							WHEN a.isaktif='2' THEN 'Aktif'
							WHEN a.isaktif='3' THEN 'Non Aktif'
							ELSE 'Suspended'
						END) as is_aktif
			FROM
	   	 		".$this->table_name." a
			LEFT JOIN 
				"._PREFIX_TABLE."user_group b 
			ON 
				b.id=a.id_groups
	   		ORDER BY
	   			a.".$this->primary_key." ASC
		";

		$res = $this->db->query($sql);
		$rs = $res->result_array();
		return $rs;
	}
}
