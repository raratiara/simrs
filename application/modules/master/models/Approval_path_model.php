<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Approval_path_model extends MY_Model
{
	/* Module */
 	protected $folder_name			= "master/approval_path";
 	protected $table_name 			= _PREFIX_TABLE."option_approval_path";
 	protected $table_menu 			= _PREFIX_TABLE."user_menu";
 	protected $table_approval 		= _PREFIX_TABLE."option_approval";
 	protected $table_karyawan 		= _PREFIX_TABLE."data_karyawan";
 	protected $table_jabatan 		= _PREFIX_TABLE."option_jabatan";
 	protected $primary_key 			= "id";
 	protected $primary_menu_key 	= "user_menu_id";

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
			'b.title',
			'c.description',
			'(SELECT GROUP_CONCAT(name) FROM '.$this->table_karyawan.' WHERE replace(replace(a.id_member,"\"",""),",","][") LIKE CONCAT("%",CONCAT("[",id,"]"),"%")) as member',
			'IF(a.active="1","Yes","No") as is_active'
		];

		$sIndexColumn = 'a.'.$this->primary_key;
		$sTable = ' '.$this->table_name.' a 
					LEFT JOIN '.$this->table_menu.' b ON a.id_menu = b.user_menu_id
					LEFT JOIN '.$this->table_approval.' c ON a.id_approval = c.id
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
				$row->title,
				$row->description,
				$row->member,
				$row->is_active
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
		$id_menu = (isset($post['id_menu']) && !empty($post['id_menu']))? trim($post['id_menu']):NULL;
		$id_approval = (isset($post['id_approval']) && !empty($post['id_approval']))? trim($post['id_approval']):NULL;
		$active = (isset($post['active']) && !empty($post['active']))? trim($post['active']):'0';
		$data = [
			'id_menu' 				=> $id_menu,
			'id_approval' 			=> $id_approval,
			'id_member' 			=> json_encode((isset($post['id_member']) && !empty($post['id_member']))? $post['id_member']:[]),
			'active' 				=> $active,
			'insert_by'				=> $_SESSION["username"]
		];

		return $rs = $this->db->insert($this->table_name, $data);
	}  

	// update data
	public function edit_data($post) { 
		if(!empty($post['id'])){
			$id_menu = (isset($post['id_menu']) && !empty($post['id_menu']))? trim($post['id_menu']):NULL;
			$id_approval = (isset($post['id_approval']) && !empty($post['id_approval']))? trim($post['id_approval']):NULL;
			$active = (isset($post['active']) && !empty($post['active']))? trim($post['active']):'0';
			$data = [
				'id_menu' 				=> $id_menu,
				'id_approval' 			=> $id_approval,
				'id_member' 			=> json_encode((isset($post['id_member']) && !empty($post['id_member']))? $post['id_member']:[]),
				'active' 				=> $active,
				'update_by'				=> $_SESSION["username"]
			];

			return  $rs = $this->db->update($this->table_name, $data, [$this->primary_key => trim($post['id'])]);
		} else return null;
	}  

	// getting row data for update / detail view
	public function getRowData($id) { 
		$rs = $this->db->where([$this->primary_key => $id])->get($this->table_name)->row();
		if(!empty($rs->id_menu)){
			$rd = $this->db->select('title as menu')->where([$this->primary_menu_key => $rs->id_menu])->get($this->table_menu)->row();
			$rs = (object) array_merge((array) $rs, (array) $rd);
		} else {
			$rs = (object) array_merge((array) $rs, ['menu'=>'']);
		}
		if(!empty($rs->id_approval)){
			$rd = $this->db->select('description as approval')->where([$this->primary_key => $rs->id_approval])->get($this->table_approval)->row();
			$rs = (object) array_merge((array) $rs, (array) $rd);
		} else {
			$rs = (object) array_merge((array) $rs, ['approval'=>'']);
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

			$active = (isset($post['active']) && !empty($post['active']))? trim($post['active']):0;
			$data = [
				'description' 		=> trim($v["B"]),
				'active' 			=> $active,
				'insert_by'			=> $_SESSION["username"]
			];


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
					id,
					description,
					IF(active='1','Yes','No') as is_active
			FROM
	   	 		".$this->table_name."
	   		ORDER BY
	   			".$this->primary_key." ASC
		";

		$res = $this->db->query($sql);
		$rs = $res->result_array();
		return $rs;
	}

	//============================== For Additional Method ==============================//

	// Get Approval selector item
	public function getApprsel($mn,$id)
	{ 
		$data = '';
		if(!empty($id) && !empty($mn)){
			// edit/not empty id
			$rs = $this->db->query("SELECT id,description FROM ".$this->table_approval." WHERE 1=1 AND id NOT IN ('1','2','8') AND id NOT IN (SELECT * FROM ( SELECT id_approval FROM ".$this->table_name." WHERE id_approval!='".$id."' AND id_menu='".$mn."' GROUP BY id_approval HAVING COUNT(*) >= 1 ) AS subquery) ORDER BY idx_seq")->result();
		} else if(empty($id) && !empty($mn)){
			// add/not empty id
			$rs = $this->db->query("SELECT id,description FROM ".$this->table_approval." WHERE 1=1 AND active = '1' AND id NOT IN ('1','2','8') AND id NOT IN (SELECT * FROM ( SELECT id_approval FROM ".$this->table_name." WHERE id_menu='".$mn."' GROUP BY id_approval HAVING COUNT(*) >= 1 ) AS subquery) ORDER BY idx_seq")->result();
		} else {
			$rs = [];
		}
		$data = $this->return_select_option($rs, 'id','description');

		return $data;
	} 

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

		return [$dt,$row];
	}

	// Get member info
	public function getMemberInfo($id)
	{ 
		$rs = $this->db->select('a.name, a.phone, a.email, b.description as jabatan')->join($this->table_jabatan.' b', 'b.id = a.id_jabatan')->where(['a.'.$this->primary_key => $id])->get($this->table_karyawan.' a')->row();

		return $rs;
	} 
}
