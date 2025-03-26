<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Data_purchasing_model extends MY_Model
{
	/* Module */
 	protected $folder_name			= "scm/data_purchasing";
 	protected $table_project 		= _PREFIX_TABLE."data_project";
    protected $table_po		        = _PREFIX_TABLE."data_po";
	protected $table_supplier 		= _PREFIX_TABLE."data_supplier";
 	protected $table_name 			= _PREFIX_TABLE."data_purchasing";
	protected $table_spk			= _PREFIX_TABLE."data_spk";
	protected $table_warehouse 		= _PREFIX_TABLE."data_warehouse_address";
	protected $table_province		= _PREFIX_TABLE."option_province";
	protected $table_city 			= _PREFIX_TABLE."option_city";
	protected $table_currency 		= _PREFIX_TABLE."option_currency";
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
			'b.po as po',
            'c.title as project',
			'd.spk as spk',
			'a.worth as wpu',
			'IF(a.approval="1","Approved","Prepared") as approval',
		];

		$sIndexColumn = 'a.'.$this->primary_key;
		$sTable = ' '.$this->table_name.' a 
					LEFT JOIN '.$this->table_po.' b ON a.id_po = b.id
                    LEFT JOIN '.$this->table_project.' c ON a.id_project = c.id
					LEFT JOIN '.$this->table_spk.' d ON a.id_spk = d.id
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
				$row->project,
				$row->spk,
				number_format($row->wpu,0,",","."),
				$row->approval
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
		$id_project = (isset($post['id_project']) && !empty($post['id_project']))? trim($post['id_project']):NULL;
		$id_po = (isset($post['id_po']) && !empty($post['id_po']))? trim($post['id_po']):NULL;
		$id_currency = (isset($post['id_currency']) && !empty($post['id_currency']))? trim($post['id_currency']):NULL;
		$id_spk = (isset($post['id_spk']) && !empty($post['id_spk']))? trim($post['id_spk']):NULL;
		$id_supplier = (isset($post['id_supplier']) && !empty($post['id_supplier']))? trim($post['id_supplier']):NULL;
		$id_warehouse = (isset($post['id_warehouse']) && !empty($post['id_warehouse']))? trim($post['id_warehouse']):NULL;
		$approval = (isset($post['approval']) && !empty($post['approval']))? trim($post['approval']):'0';
		$data = [
			'id_po' 			=> $id_po,
			'id_project' 		=> $id_project,
			'id_currency' 		=> $id_currency,
			'date' 				=> $this->date_to_int(trim($post['date'])),
			'delivery_date' 	=> $this->date_to_int(trim($post['delivery_date'])),
			'id_spk'			=> $id_spk,
			'id_supplier'		=> $id_supplier,
			'id_warehouse' 		=> $id_warehouse,
			'approval' 			=> $approval,
			'worth' 			=> str_replace(",",".",str_replace(".","",trim($post['worth']))),
			'remark' 			=> trim($post['remark']),
			'term_condition' 	=> trim($post['term_condition']),
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
						'price' 		=> str_replace(",",".",str_replace(".","",trim($post['price'][$i]))),
						'subtotal' 		=> str_replace(",",".",str_replace(".","",trim($post['subtotal'][$i])))
					];

					$list_item[] = $itemData;
				}
			}
		}
		$this->db->update($this->table_name, ['list_item' => json_encode($list_item)], [$this->primary_key => $lastId]);

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

		$this->db->trans_complete();

		$rs = $this->db->trans_status();
		return [$rs,$lastId];
	}

	// update data
	public function edit_data($post)
	{
		if (!empty($post['id'])) {
			$uid = trim($post['id']);

			$id_project = (isset($post['id_project']) && !empty($post['id_project']))? trim($post['id_project']):NULL;
			$id_po = (isset($post['id_po']) && !empty($post['id_po']))? trim($post['id_po']):NULL;
			$id_currency = (isset($post['id_currency']) && !empty($post['id_currency']))? trim($post['id_currency']):NULL;
			$id_spk = (isset($post['id_spk']) && !empty($post['id_spk']))? trim($post['id_spk']):NULL;
			$id_supplier = (isset($post['id_supplier']) && !empty($post['id_supplier']))? trim($post['id_supplier']):NULL;
			$id_warehouse = (isset($post['id_warehouse']) && !empty($post['id_warehouse']))? trim($post['id_warehouse']):NULL;
			$approval = (isset($post['approval']) && !empty($post['approval']))? trim($post['approval']):'0';
			$data = [
				'id_po' 			=> $id_po,
				'id_project' 		=> $id_project,
				'id_currency' 		=> $id_currency,
				'date' 				=> $this->date_to_int(trim($post['date'])),
				'delivery_date' 	=> $this->date_to_int(trim($post['delivery_date'])),
				'id_spk'			=> $id_spk,
				'id_supplier'		=> $id_supplier,
				'id_warehouse' 		=> $id_warehouse,
				'approval' 			=> $approval,
				'worth' 			=> str_replace(",",".",str_replace(".","",trim($post['worth']))),
				'remark' 			=> trim($post['remark']),
				'term_condition' 	=> trim($post['term_condition']),
				'insert_by'			=> $_SESSION["username"]
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
							'price' 		=> str_replace(",",".",str_replace(".","",trim($post['price'][$i]))),
							'subtotal' 		=> str_replace(",",".",str_replace(".","",trim($post['subtotal'][$i])))
						];

						$items[] = $itemData;
					}
				}
			}
			
			$this->db->update($this->table_name, ['list_item' => json_encode($items)], [$this->primary_key => $uid]);

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

			$this->db->trans_complete();

			$rs = $this->db->trans_status();
			return [$rs,$uid];
		} else return null;
	}  

	// getting row data for update / detail view
	public function getRowData($id) { 
		$rs = $this->db->select('*,DATE_FORMAT(FROM_UNIXTIME(date), "%d-%m-%Y") as dt,DATE_FORMAT(FROM_UNIXTIME(delivery_date), "%d-%m-%Y") as ddt')->where([$this->primary_key => $id])->get($this->table_name)->row();
		if(!empty($rs->id_currency)){
			$rd = $this->db->select('code as currency')->where([$this->primary_key => $rs->id_currency])->get($this->table_currency)->row();
			$rs = (object) array_merge((array) $rs, (array) $rd);
		} else {
			$rs = (object) array_merge((array) $rs, ['currency'=>'']);
		}
		if(!empty($rs->id_po)){
			$rd = $this->db->select('po as po')->where([$this->primary_key => $rs->id_po])->get($this->table_po)->row();
			$rs = (object) array_merge((array) $rs, (array) $rd);
		} else {
			$rs = (object) array_merge((array) $rs, ['po'=>'']);
		}
		if(!empty($rs->id_supplier)){
			$rd = $this->getSupplierInfo($rs->id_supplier);
			$rs = (object) array_merge((array) $rs, (array) $rd);
		} else {
			$rs = (object) array_merge((array) $rs, ['id_supplier'=>'','code'=>'','supplier_name'=>'','supplier_address'=>'']);
		}
		if(!empty($rs->id_spk)){
			$rd = $this->getSpkInfo($rs->id_spk);
			$rs = (object) array_merge((array) $rs, (array) $rd);
		} else {
			$rs = (object) array_merge((array) $rs, ['id_spk'=>'','spk'=>'','description'=>'']);
		}
		if(!empty($rs->id_warehouse)){
			$rd = $this->getWarehouseInfo($rs->id_warehouse);
			$rs = (object) array_merge((array) $rs, (array) $rd);
		} else {
			$rs = (object) array_merge((array) $rs, ['id_warehouse'=>'','warehouse_description'=>'','warehouse_kodepos'=>'','warehouse_address'=>'']);
		}
		if(!empty($rs->id_project)){
			$rd = $this->getProjectInfo($rs->id_project);
			$rs = (object) array_merge((array) $rs, (array) $rd);
		} else {
			$rs = (object) array_merge((array) $rs, ['id_project'=>'','project_title'=>'']);
		}
		if(!empty($rs->id_province_warehouse)){
			$rd = $this->db->select('description as warehouse_province')->where([$this->primary_key => $rs->id_province_warehouse])->get($this->table_province)->row();
			$rs = (object) array_merge((array) $rs, (array) $rd);
		} else {
			$rs = (object) array_merge((array) $rs, ['warehouse_province'=>'']);
		}
		if(!empty($rs->id_city_warehouse)){
			$rd = $this->db->select('description as warehouse_city')->where([$this->primary_key => $rs->id_city_warehouse])->get($this->table_city)->row();
			$rs = (object) array_merge((array) $rs, (array) $rd);
		} else {
			$rs = (object) array_merge((array) $rs, ['warehouse_city'=>'']);
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
	// Get Supplier info
	public function getSupplierInfo($id)
	{ 
		$rs = $this->db->select('id as id_supplier,code as supplier_code,name as supplier_name,address as supplier_address, pic_name as supplier_pic')->where([$this->primary_key => $id])->get($this->table_supplier)->row();
		
		return $rs;
	} 

	// Get PO info
	public function getPoInfo($id)
	{ 
		$rs = $this->db->select('id as id_po,po as po,id_project')->where([$this->primary_key => $id])->get($this->table_po)->row();
		if(!empty($rs->id_project)){
			$rd = $this->getProjectInfo($rs->id_project);
			$rs = (object) array_merge((array) $rs, (array) $rd);
		} else {
			$rs = (object) array_merge((array) $rs, ['id_project'=>'','project_title'=>'']);
		}
		return $rs;
	} 
	
	// Get project info
	public function getProjectInfo($id)
	{ 
		$rs = $this->db->select('id as id_project, project as project, title as project_title')->where([$this->primary_key => $id])->get($this->table_project)->row();
		return $rs;
	} 

	// Get Spk info
	public function getSpkInfo($id)
	{ 
		$rs = $this->db->select('id as id_spk, spk as spk, description as spk_title')->where([$this->primary_key => $id])->get($this->table_spk)->row();
		return $rs;
	} 

	// Get Warehouse info
	public function getWarehouseInfo($id)
	{ 
		$rs = $this->db->select('id as id_warehouse, description as warehouse_description, kodepos as warehouse_kodepos, address as warehouse_address, id_province as id_province_warehouse, id_city as id_city_warehouse')->where([$this->primary_key => $id])->get($this->table_warehouse)->row();
		return $rs;
	} 

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
			$data 	.= '<td>'.$this->return_build_txt(number_format(0,0,',','.'),'price['.$row.']','','price','text-align: right;','data-id="'.$row.'"').'</td>';
			$data 	.= '<td>'.$this->return_build_txt(number_format(0,2,',','.'),'subtotal['.$row.']','','subtotal','text-align: right;','data-id="'.$row.'" readonly').'</td>';
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
					// $dt .= '<td>'.$f->item_code.'</td>';
					$dt .= '<td>'.$f->description.'</td>';
					$dt .= '<td style="text-align: center;">'.$arrS[(isset($f->satuan) && !empty($f->satuan))?$f->satuan:1]['description'].'</td>';
					$dt .= '<td style="text-align: center;">'.number_format($f->qty,0,',','.').'</td>';
					$dt .= '<td style="text-align: right;">'.number_format($f->price,0,',','.').'</td>';
					$dt .= '<td style="text-align: right;">'.number_format($f->subtotal,0,',','.').'</td>';
					$dt .= '</tr>';
				}

				$row++;
			}
		}

		return [$dt,$row];
	}
}
