<?php

class Sadmin_tools_access_menu_role_model extends MY_Model
{
	/* Module */
 	protected $folder_name			= "general_system/sadmin_tools_access_menu_role";
 	protected $table_name 			= _PREFIX_TABLE."user_group";
 	protected $primary_key 			= "id";
 	protected $role_key 			= "role_id";
 	protected $table_name1 			= _PREFIX_TABLE."user_menu";
 	protected $primary_key1 		= "user_menu_id";
 	protected $table_name2 			= _PREFIX_TABLE."user_akses_role";
 	protected $primary_key2 		= "user_akses_id";

	function __construct()
	{
		parent::__construct();
	}

	// fix
	public function get_list_data()
	{
		$aColumns = [
			NULL,
			'name',
			'description',
			'id'
		];

		$sIndexColumn = $this->primary_key;
		$sTable = ' '.$this->table_name;

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

		if($sWhere == ""){
			$sWhere = "WHERE ";
		} else {
			$sWhere .= " AND ";
		}
		$sWhere .= $this->primary_key." IN (
			SELECT * FROM
			(
				SELECT ".$this->role_key."
				FROM ".$this->table_name2."
				GROUP BY ".$this->role_key."
				HAVING COUNT(*) >= 1
			) AS subquery
		) ";

		/* Get data to display */
		$del_col = NULL;
		$filtered_cols = $aColumns;
		if (($key = array_search($del_col, $filtered_cols)) !== false) {
			unset($filtered_cols[$key]);
		}
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
				$detail = '<a class="btn btn-xs btn-success detail-btn" href="'.base_url($this->folder_name.'/detail/' . $row->id).'"><i class="fa fa-search-plus"></i></a>';
			}
			$edit = "";
			if (_USER_ACCESS_LEVEL_UPDATE == "1")  {
				$edit = '<a class="btn btn-xs btn-primary" href="'.base_url($this->folder_name.'/edit/' . $row->id).'"><i class="fa fa-pencil"></i></a>';
			}
			$delete = "";
			if (_USER_ACCESS_LEVEL_DELETE == "1")  {
				$delete = '<a class="btn btn-xs btn-danger" href="'.base_url($this->folder_name.'/delete/' . $row->id).'" role="button"  data-toggle="modal" data-confirm="Apakah anda yakin ingin menghapus data ID : '.$row->id.'?"><i class="fa fa-trash"></i></a>';
			}

			array_push($output["aaData"],array(
				'<div class="action-buttons">
					'.$detail.'
					'.$edit.'
					'.$delete.'
				</div>',
				$row->name,
				$row->description
			));
		}

		echo json_encode($output);
	}

	public function delete($id= "") {
		if (isset($id) && $id <> "") {
			$rs = $this->db->where([$this->role_key => $id])->delete($this->table_name2);
			return $rs;
		} else return null;
	}  

	public function add_data($post) {
		$user_id = $post['user_id'];
		$ops = FALSE;

		if(!empty($user_id)){
			$sql1 = "SELECT user_menu_id, module_name FROM ".$this->table_name1." WHERE parent_id = 0 ORDER BY um_order ASC";
			$res1 = $this->db->query($sql1);
			$rs1 = $res1->result_array();
			if (count($rs1) > 0) {
				$itemData = array();
				FOREACH ($rs1 AS $r1) {
					$data1 = array( 
						'role_id' 			=> $user_id,  
						'user_menu_id' 		=> $r1["user_menu_id"],  
						'view' 				=> $this->input->post('view_'.$r1["user_menu_id"]),  
						'add' 				=> $this->input->post('add_'.$r1["user_menu_id"]),  
						'edit' 				=> $this->input->post('update_'.$r1["user_menu_id"]),  
						'del' 				=> $this->input->post('delete_'.$r1["user_menu_id"]),  
						'detail' 			=> $this->input->post('detail_'.$r1["user_menu_id"]),  
						'import' 			=> $this->input->post('import_'.$r1["user_menu_id"]),  
						'eksport' 			=> $this->input->post('eksport_'.$r1["user_menu_id"]),
						'insert_by'			=> $_SESSION["username"]
					);

					$itemData[] = $data1;
	 
					$sql_1 = "SELECT user_menu_id, module_name FROM ".$this->table_name1." WHERE parent_id = ".$r1["user_menu_id"]." ORDER BY um_order ASC";
					$res_1 = $this->db->query($sql_1);
					$rs_1 = $res_1->result_array();
					if (count($rs_1) > 0) {
						FOREACH ($rs_1 AS $r_1) {
							 $data2 = array( 
								'role_id' 			=> $user_id,  
								'user_menu_id' 		=> $r_1["user_menu_id"],  
								'view' 				=> $this->input->post('view_'.$r_1["user_menu_id"]),  
								'add' 				=> $this->input->post('add_'.$r_1["user_menu_id"]),  
								'edit' 				=> $this->input->post('update_'.$r_1["user_menu_id"]),  
								'del' 				=> $this->input->post('delete_'.$r_1["user_menu_id"]),  
								'detail' 			=> $this->input->post('detail_'.$r_1["user_menu_id"]),  
								'import' 			=> $this->input->post('import_'.$r_1["user_menu_id"]),  
								'eksport' 			=> $this->input->post('eksport_'.$r_1["user_menu_id"]),
								'insert_by'			=> $_SESSION["username"]
							);

							$itemData[] = $data2;
	 
							$sql_2 = "SELECT user_menu_id, module_name FROM ".$this->table_name1." WHERE parent_id = ".$r_1["user_menu_id"]." ORDER BY um_order ASC";
							$res_2 = $this->db->query($sql_2);
							$rs_2 = $res_2->result_array();
							if (count($rs_2) > 0) {
								FOREACH ($rs_2 AS $r_2) {
									 $data3 = array( 
										'role_id' 			=> $user_id,  
										'user_menu_id' 		=> $r_2["user_menu_id"],  
										'view' 				=> $this->input->post('view_'.$r_2["user_menu_id"]),  
										'add' 				=> $this->input->post('add_'.$r_2["user_menu_id"]),  
										'edit' 				=> $this->input->post('update_'.$r_2["user_menu_id"]),  
										'del' 				=> $this->input->post('delete_'.$r_2["user_menu_id"]),  
										'detail' 			=> $this->input->post('detail_'.$r_2["user_menu_id"]),  
										'import' 			=> $this->input->post('import_'.$r_2["user_menu_id"]),  
										'eksport' 			=> $this->input->post('eksport_'.$r_2["user_menu_id"]),
										'insert_by'			=> $_SESSION["username"]
									);

									$itemData[] = $data3;
	 
									$sql_3 = "SELECT user_menu_id, module_name FROM ".$this->table_name1." WHERE parent_id = ".$r_2["user_menu_id"]." ORDER BY um_order ASC";
									$res_3 = $this->db->query($sql_3);
									$rs_3 = $res_3->result_array();
									if (count($rs_3) > 0) {
										FOREACH ($rs_3 AS $r_3) {
											 $data4 = array( 
												'role_id' 			=> $user_id,  
												'user_menu_id' 		=> $r_3["user_menu_id"],  
												'view' 				=> $this->input->post('view_'.$r_3["user_menu_id"]),  
												'add' 				=> $this->input->post('add_'.$r_3["user_menu_id"]),  
												'edit' 				=> $this->input->post('update_'.$r_3["user_menu_id"]),  
												'del' 				=> $this->input->post('delete_'.$r_3["user_menu_id"]),  
												'detail' 			=> $this->input->post('detail_'.$r_3["user_menu_id"]),  
												'import' 			=> $this->input->post('import_'.$r_3["user_menu_id"]),  
												'eksport' 			=> $this->input->post('eksport_'.$r_3["user_menu_id"]),
												'insert_by'			=> $_SESSION["username"]
											);

											$itemData[] = $data4;
										}
									}
								}
							}
						}
					}
				}
				if(count($itemData)>0){
					//$this->db->trans_off(); // Disable transaction
					$this->db->trans_start(); // set "True" for query will be rolled back				
					$this->db->insert_batch($this->table_name2,$itemData);
					$this->db->trans_complete();
					if($this->db->trans_status()){
						$ops = TRUE;
					}
				}
			}
		}

		return $ops;
	}  

	public function edit_data($post,$id) { 
		$user_id = $id;
		$ops = FALSE;

		if (isset($id) && $id <> "") {			
			$sql1 = "SELECT user_menu_id, module_name FROM ".$this->table_name1." WHERE parent_id = 0 ORDER BY um_order ASC";
			$res1 = $this->db->query($sql1);
			$rs1 = $res1->result_array();
			if (count($rs1) > 0) {
				$itemData = array();
				FOREACH ($rs1 AS $r1) {
					$data1 = array( 
						'role_id' 			=> $user_id,  
						'user_menu_id' 		=> $r1["user_menu_id"],  
						'view' 				=> $this->input->post('view_'.$r1["user_menu_id"]),  
						'add' 				=> $this->input->post('add_'.$r1["user_menu_id"]),  
						'edit' 				=> $this->input->post('update_'.$r1["user_menu_id"]),  
						'del' 				=> $this->input->post('delete_'.$r1["user_menu_id"]),  
						'detail' 			=> $this->input->post('detail_'.$r1["user_menu_id"]),  
						'import' 			=> $this->input->post('import_'.$r1["user_menu_id"]),  
						'eksport' 			=> $this->input->post('eksport_'.$r1["user_menu_id"]),
						'insert_by'			=> $_SESSION["username"]
					);

					$itemData[] = $data1;
	 
					$sql_1 = "SELECT user_menu_id, module_name FROM ".$this->table_name1." WHERE parent_id = ".$r1["user_menu_id"]." ORDER BY um_order ASC";
					$res_1 = $this->db->query($sql_1);
					$rs_1 = $res_1->result_array();
					if (count($rs_1) > 0) {
						FOREACH ($rs_1 AS $r_1) {
							 $data2 = array( 
								'role_id' 			=> $user_id,  
								'user_menu_id' 		=> $r_1["user_menu_id"],  
								'view' 				=> $this->input->post('view_'.$r_1["user_menu_id"]),  
								'add' 				=> $this->input->post('add_'.$r_1["user_menu_id"]),  
								'edit' 				=> $this->input->post('update_'.$r_1["user_menu_id"]),  
								'del' 				=> $this->input->post('delete_'.$r_1["user_menu_id"]),  
								'detail' 			=> $this->input->post('detail_'.$r_1["user_menu_id"]),  
								'import' 			=> $this->input->post('import_'.$r_1["user_menu_id"]),  
								'eksport' 			=> $this->input->post('eksport_'.$r_1["user_menu_id"]),
								'insert_by'			=> $_SESSION["username"]
							);

							$itemData[] = $data2;
	 
							$sql_2 = "SELECT user_menu_id, module_name FROM ".$this->table_name1." WHERE parent_id = ".$r_1["user_menu_id"]." ORDER BY um_order ASC";
							$res_2 = $this->db->query($sql_2);
							$rs_2 = $res_2->result_array();
							if (count($rs_2) > 0) {
								FOREACH ($rs_2 AS $r_2) {
									 $data3 = array( 
										'role_id' 			=> $user_id,  
										'user_menu_id' 		=> $r_2["user_menu_id"],  
										'view' 				=> $this->input->post('view_'.$r_2["user_menu_id"]),  
										'add' 				=> $this->input->post('add_'.$r_2["user_menu_id"]),  
										'edit' 				=> $this->input->post('update_'.$r_2["user_menu_id"]),  
										'del' 				=> $this->input->post('delete_'.$r_2["user_menu_id"]),  
										'detail' 			=> $this->input->post('detail_'.$r_2["user_menu_id"]),  
										'import' 			=> $this->input->post('import_'.$r_2["user_menu_id"]),  
										'eksport' 			=> $this->input->post('eksport_'.$r_2["user_menu_id"]),
										'insert_by'			=> $_SESSION["username"]
									);

									$itemData[] = $data3;
	 
									$sql_3 = "SELECT user_menu_id, module_name FROM ".$this->table_name1." WHERE parent_id = ".$r_2["user_menu_id"]." ORDER BY um_order ASC";
									$res_3 = $this->db->query($sql_3);
									$rs_3 = $res_3->result_array();
									if (count($rs_3) > 0) {
										FOREACH ($rs_3 AS $r_3) {
											 $data4 = array( 
												'role_id' 			=> $user_id,  
												'user_menu_id' 		=> $r_3["user_menu_id"],  
												'view' 				=> $this->input->post('view_'.$r_3["user_menu_id"]),  
												'add' 				=> $this->input->post('add_'.$r_3["user_menu_id"]),  
												'edit' 				=> $this->input->post('update_'.$r_3["user_menu_id"]),  
												'del' 				=> $this->input->post('delete_'.$r_3["user_menu_id"]),  
												'detail' 			=> $this->input->post('detail_'.$r_3["user_menu_id"]),  
												'import' 			=> $this->input->post('import_'.$r_3["user_menu_id"]),  
												'eksport' 			=> $this->input->post('eksport_'.$r_3["user_menu_id"]),
												'insert_by'			=> $_SESSION["username"]
											);

											$itemData[] = $data4;
										}
									}
								}
							}
						}
					}
				}
				//$this->db->trans_off(); // Disable transaction
				$this->db->trans_start(); // set "True" for query will be rolled back				
				$this->db->where([$this->role_key => $id])->delete($this->table_name2);
				if(count($itemData)>0){
					$this->db->insert_batch($this->table_name2,$itemData);
				}
				$this->db->trans_complete();
				if($this->db->trans_status()){
					$ops = TRUE;
				}
			}
		}
		
		return $ops;
	}  

	public function getRowData($id) { 
		return $rs = $this->db->where([$this->primary_key => $id])->get($this->table_name)->row();
	} 

	public function getRowDataAccess($id) { 
		return $rs = $this->db->where([$this->role_key => $id])->get($this->table_name2)->result_array();
	} 

	public function import_data($list_data)
	{
		$i = 0;

		foreach ($list_data as $k => $v) {
			$i += 1;

			$data = [
				'role_id' 			=> $v["B"],
				'user_menu_id' 		=> $v["C"],
				'view' 				=> $v["D"],
				'add' 				=> $v["E"],
				'edit' 				=> $v["F"],
				'del' 				=> $v["G"],
				'detail' 			=> $v["H"],
				'eksport' 			=> $v["I"],
				'import' 			=> $v["J"],
				'insert_by'			=> $_SESSION["username"]
			];

			$rs = $this->db->insert($this->table_name2, $data);
			if (!$rs) $error .=",baris ". $v["A"];
		}

		return $error;
	}

	public function eksport_data()
	{
		$sql = "SELECT
					a.user_akses_id,
					a.role_id,
					a.user_menu_id,
					a.view,
					a.add,
					a.edit,
					a.del,
					a.detail,
					a.eksport,
					a.import
			FROM
	   	 		".$this->table_name2." a
	   		ORDER BY
	   			a.".$this->primary_key2." ASC
		";

		$res = $this->db->query($sql);
		$rs = $res->result_array();
		return $rs;
	}

}
