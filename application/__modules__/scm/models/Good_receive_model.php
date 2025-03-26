<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Good_receive_model extends MY_Model
{
	/* Module */
 	protected $folder_name			= "scm/good_receive";
 	protected $table_po     		= _PREFIX_TABLE."data_po";
 	protected $table_name 			= _PREFIX_TABLE."data_good_receive";
    protected $table_project 		= _PREFIX_TABLE."data_project";
	protected $table_karyawan 		= _PREFIX_TABLE."data_karyawan";
	protected $table_uom 			= _PREFIX_TABLE."option_uom";
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
			'a.do',
			'DATE_FORMAT(FROM_UNIXTIME(a.date_receive), "%d-%m-%Y") as dt',
			'b.po as po',
			'a.note'
		];

		$sIndexColumn = 'a.'.$this->primary_key;
		$sTable = ' '.$this->table_name.' a 
					LEFT JOIN '.$this->table_po.' b ON a.id_po = b.id
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
				$row->do,
				$row->dt,
				$row->po,
				$row->note
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
	public function add_data($post)
	{
		// var_dump($post);
		// exit;
		$id_po = (isset($post['id_po']) && !empty($post['id_po'])) ? trim($post['id_po']) : NULL;
		$id_project = (isset($post['id_project']) && !empty($post['id_project'])) ? trim($post['id_project']) : NULL;
		$data = [
			'do' 				=> trim($post['do']),
			'date_receive' 		=> $this->date_to_int(trim($post['date_receive'])),
			'id_po' 		    => $id_po,
			'id_project' 		=> $id_project,
			'shipper_name' 		=> trim($post['shp_name']),
			'shipper_address' 	=> trim($post['shp_address']),
			'shipper_telp' 		=> trim($post['shp_telp']),
			'note' 				=> trim($post['note']),
			'insert_by'			=> $_SESSION["username"]
		];

		//$this->db->trans_off(); // Disable transaction
		$this->db->trans_start(); // set "True" for query will be rolled back
		$this->db->insert($this->table_name, $data);
		$lastId = $this->db->insert_id();
		// Add list_item data
		$list_item = [];
		// cek list_item item
		if (isset($post['item_description'])) {
			$item_num = count($post['item_description']); // cek sum
			$item_len_min = min(array_keys($post['item_description'])); // cek min key index
			$item_len = max(array_keys($post['item_description'])); // cek max key index
		} else {
			$item_num = 0;
		}

		if ($item_num > 0) {
			for ($i = $item_len_min; $i <= $item_len; $i++) {
				if (isset($post['item_description'][$i])) {
					$itemData = [
						'item_code' 	=> trim($post['item_code'][$i]),
						'description' 	=> trim($post['item_description'][$i]),
						'satuan' 		=> trim($post['satuan'][$i]),
						'qty' 			=> str_replace(",", ".", str_replace(".", "", trim($post['qty'][$i]))),
						'coly' 			=> str_replace(",", ".", str_replace(".", "", trim($post['coly'][$i]))),
						'remark' 		=> str_replace(",", ".", str_replace(".", "", trim($post['remark'][$i])))
					];

					$list_item[] = $itemData;
				}
			}
		}
		$this->db->update($this->table_name, ['list_item' => json_encode($list_item)], [$this->primary_key => $lastId]);
		$this->db->trans_complete();

		$rs = $this->db->trans_status();
		return [$rs, $lastId];

		// return $rs = $this->db->insert($this->table_name, $data);
	}

	// update data
	public function edit_data($post)
	{
		if (!empty($post['id'])) {
			$uid = trim($post['id']);
			$id_po = (isset($post['id_po']) && !empty($post['id_po'])) ? trim($post['id_po']) : NULL;
			$id_project = (isset($post['id_project']) && !empty($post['id_project'])) ? trim($post['id_project']) : NULL;
			$data = [
				'do' 				=> trim($post['do']),
				'date_receive' 		=> $this->date_to_int(trim($post['date_receive'])),
				'id_po' 		    => $id_po,
				'id_project' 		=> $id_project,
				'shipper_name' 		=> trim($post['shp_name']),
				'shipper_address' 	=> trim($post['shp_address']),
				'shipper_telp' 		=> trim($post['shp_telp']),
				'note' 				=> trim($post['note_order']),
				'update_by'			=> $_SESSION["username"]
			];

			//$this->db->trans_off(); // Disable transaction
			$this->db->trans_start(); // set "True" for query will be rolled back
			$this->db->update($this->table_name, $data, [$this->primary_key => $uid]);
			// Add list_item data
			$items = [];
			// cek list_item item
			if (isset($post['item_description'])) {
				$item_num = count($post['item_description']); // cek sum
				$item_len_min = min(array_keys($post['item_description'])); // cek min key index
				$item_len = max(array_keys($post['item_description'])); // cek max key index
			} else {
				$item_num = 0;
			}

			if ($item_num > 0) {
				for ($i = $item_len_min; $i <= $item_len; $i++) {
					if (isset($post['item_description'][$i])) {
						$itemData = [
							'item_code' 	=> trim($post['item_code'][$i]),
							'description' 	=> trim($post['item_description'][$i]),
							'satuan' 		=> trim($post['satuan'][$i]),
							'qty' 			=> str_replace(",", ".", str_replace(".", "", trim($post['qty'][$i]))),
							'coly' 			=> str_replace(",", ".", str_replace(".", "", trim($post['coly'][$i]))),
							'remark' 		=> str_replace(",", ".", str_replace(".", "", trim($post['remark'][$i])))
						];

						$items[] = $itemData;
					}
				}
			}
			
			$this->db->update($this->table_name, ['list_item' => json_encode($items)], [$this->primary_key => $uid]);
			$this->db->trans_complete();

			$rs = $this->db->trans_status();
			return [$rs, $uid];

			// return  $rs = $this->db->update($this->table_name, $data, [$this->primary_key => trim($post['id'])]);
		} else return null;
	}  

	// getting row data for update / detail view
	public function getRowData($id) { 
		$rs = $this->db->select('*,DATE_FORMAT(FROM_UNIXTIME(date_receive), "%d-%m-%Y") as dt')->where([$this->primary_key => $id])->get($this->table_name)->row();
		if(!empty($rs->id_po)){
			$rd = $this->db->select('po as po')->where([$this->primary_key => $rs->id_po])->get($this->table_po)->row();
			$rs = (object) array_merge((array) $rs, (array) $rd);
		} else {
			$rs = (object) array_merge((array) $rs, ['po'=>'']);
		}
        if(!empty($rs->id_project)){
			$rd = $this->db->select('title as project')->where([$this->primary_key => $rs->id_project])->get($this->table_project)->row();
			$rs = (object) array_merge((array) $rs, (array) $rd);
		} else {
			$rs = (object) array_merge((array) $rs, ['project'=>'']);
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

			$active = trim($v["E"]);
			if(empty($active)) $active = '0';
			$data = [
				'code' 				=> $v["B"],
				'description' 		=> $v["C"],
				'id_province' 		=> $v["D"],
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
	public function eksport_data()
	{
		$sql = "SELECT
					a.id,
					a.code,
					a.description,
					b.description as province,
					IF(a.active='1','Yes','No') as is_active
			FROM
	   	 		".$this->table_name." a LEFT JOIN ".$this->table_currname." b ON a.id_province = b.id 
	   		ORDER BY
	   			a.".$this->primary_key." ASC
		";

		$res = $this->db->query($sql);
		$rs = $res->result_array();
		return $rs;
	}

	//============================== For Additional Method ==============================//
	// Generate new quote item row
	public function getNewItemRow($row,$id=0,$view=FALSE)
	{ 
		if($id > 0){
			$data = $this->getItemRows($id,$view);
		} else {
			$data = '';
			$no = $row+1;
			$oSatuan 	= $this->db->select('id,description')->order_by('idx_seq', 'ASC')->where("active='1'")->get($this->table_uom)->result();
			$data 	.= '<td>'.$no.'</td>';
			$data 	.= '<td>'.$this->return_build_txt('','item_code['.$row.']','','item_code','text-align: right;','data-id="'.$row.'"').'</td>';
			$data 	.= '<td>'.$this->return_build_txtarea('','item_description['.$row.']','','','item_description','','data-id="'.$row.'"').'</td>';
			$data 	.= '<td>'.$this->return_build_chosenme($oSatuan,'','','','satuan['.$row.']','','satuan','','id','description','','','',' data-id="'.$row.'"').'</td>';
			$data 	.= '<td>'.$this->return_build_txt(number_format(0,0,',','.'),'qty['.$row.']','','qty','text-align: right;','data-id="'.$row.'"').'</td>';
			$data 	.= '<td>'.$this->return_build_txt(number_format(0,2,',','.'),'coly['.$row.']','','coly','text-align: right;','data-id="'.$row.'"').'</td>';
			$data 	.= '<td>'.$this->return_build_txt('','remark['.$row.']','','remark','text-align: right;','data-id="'.$row.'"').'</td>';
			$data 	.= '<td><input type="button" class="ibtnDel btn btn-md btn-danger "  value="Delete"></td>';
		}

		return $data;
	} 
	
	// Generate quote item rows for edit & view
	public function getItemRows($id,$view,$print=FALSE){
		$dt = '';
		$rs = $this->db->select('list_item')->where([$this->primary_key => $id])->get($this->table_name)->row();
		$rd = json_decode($rs->list_item);
		$rs_num = count($rd);

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
					$dt .= '<td>'.$this->return_build_txt($f->item_code,'item_code['.$row.']','','','item_code','','data-id="'.$row.'"').'</td>';
					$dt .= '<td>'.$this->return_build_txtarea($f->description,'item_description['.$row.']','','','item_description','','data-id="'.$row.'"').'</td>';
					$dt .= '<td>'.$this->return_build_chosenme($oSatuan,'',$f->satuan,'','satuan['.$row.']','','satuan','','id','description','','','',' data-id="'.$row.'"').'</td>';
					$dt .= '<td>'.$this->return_build_txt(number_format($f->qty,0,',','.'),'qty['.$row.']','','qty','text-align: right;','data-id="'.$row.'"').'</td>';
					$dt .= '<td>'.$this->return_build_txt(number_format($f->coly,2,',','.'),'coly['.$row.']','','coly','text-align: right;','data-id="'.$row.'"').'</td>';
					$dt .= '<td>'.$this->return_build_txt($f->remark,'remark['.$row.']','','','remark','','data-id="'.$row.'"').'</td>';
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
					$dt .= '<td>'.$f->item_code.'</td>';
					$dt .= '<td>'.$f->description.'</td>';
					$dt .= '<td style="text-align: center;">'.$arrS[(isset($f->satuan) && !empty($f->satuan))?$f->satuan:1]['description'].'</td>';
					$dt .= '<td style="text-align: center;">'.number_format($f->qty,0,',','.').'</td>';
					$dt .= '<td style="text-align: center;">'.number_format($f->coly,2,',','.').'</td>';
					$dt .= '<td>'.$f->remark.'</td>';
					$dt .= '</tr>';
				}

				$row++;
			}
		}

		return [$dt,$row];
	}

}
