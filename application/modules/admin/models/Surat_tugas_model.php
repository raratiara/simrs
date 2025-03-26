<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Surat_tugas_model extends MY_Model
{
	/* Module */
 	protected $folder_name			= "admin/surat_tugas";
 	protected $table_name 			= _PREFIX_TABLE."data_surat_tugas";
 	protected $table_karyawan 		= _PREFIX_TABLE."data_karyawan";
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
			'a.surat',
			'DATE_FORMAT(FROM_UNIXTIME(a.date_surat), "%d-%m-%Y") as dsurat',
			'a.name',
			'a.description',
			'b.name as approve'
		];

		$sIndexColumn = 'a.'.$this->primary_key;
		$sTable = ' '.$this->table_name.' a
					LEFT JOIN '.$this->table_karyawan.' b ON b.id=a.id_ttd
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
				$row->surat,
				$row->dsurat,
				$row->name,
				$row->description,
				$row->approve
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
		$id_rab = (isset($post['id_rab']) && !empty($post['id_rab']))? trim($post['id_rab']):NULL;
		$id_customer = (isset($post['id_customer']) && !empty($post['id_customer']))? trim($post['id_customer']):0;
		$id_term = (isset($post['id_term']) && !empty($post['id_term']))? trim($post['id_term']):NULL;
		$id_currency = (isset($post['id_currency']) && !empty($post['id_currency']))? trim($post['id_currency']):NULL;
		$id_valid = (isset($post['id_valid']) && !empty($post['id_valid']))? trim($post['id_valid']):NULL;
		$data = [
			'date_quotation' 	=> $this->date_to_int(trim($post['date_quotation'])),
			'quotation' 		=> trim($post['quotation']),
			'total_quote' 		=> str_replace(",",".",str_replace(".","",trim($post['total_quote']))),
			'rab' 				=> str_replace(",",".",str_replace(".","",trim($post['rab']))),
			'margin_plan' 		=> str_replace(",",".",str_replace(".","",trim($post['margin_plan']))),
			'id_rab' 			=> $id_rab,
			'id_customer' 		=> $id_customer,
			'description' 		=> trim($post['description']),
			'notes' 			=> trim($post['notes']),
			'id_term' 			=> $id_term,
			'id_currency' 		=> $id_currency,
			'id_valid' 			=> $id_valid,
			'insert_by'			=> $_SESSION["username"]
		];

		//$this->db->trans_off(); // Disable transaction
		$this->db->trans_start(); // set "True" for query will be rolled back
		$this->db->insert($this->table_name, $data);
		$lastId = $this->db->insert_id();
		// Add quote data
		$quote =[];
		// cek quote item
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
		$this->db->update($this->table_name, ['data_quotation' => json_encode($quote)], [$this->primary_key => $lastId]);

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
		$this->db->trans_complete();

		$rs = $this->db->trans_status();
		return [$rs,$lastId];
	}  

	// update data
	public function edit_data($post) { 
		if(!empty($post['id'])){
			$uid = trim($post['id']);
			$id_rab = (isset($post['id_rab']) && !empty($post['id_rab']))? trim($post['id_rab']):NULL;
			$id_customer = (isset($post['id_customer']) && !empty($post['id_customer']))? trim($post['id_customer']):0;
			$id_term = (isset($post['id_term']) && !empty($post['id_term']))? trim($post['id_term']):NULL;
			$id_currency = (isset($post['id_currency']) && !empty($post['id_currency']))? trim($post['id_currency']):NULL;
			$id_valid = (isset($post['id_valid']) && !empty($post['id_valid']))? trim($post['id_valid']):NULL;
			$data = [
				'date_quotation' 	=> $this->date_to_int(trim($post['date_quotation'])),
				'quotation' 		=> trim($post['quotation']),
				'total_quote' 		=> str_replace(",",".",str_replace(".","",trim($post['total_quote']))),
				'rab' 				=> str_replace(",",".",str_replace(".","",trim($post['rab']))),
				'margin_plan' 		=> str_replace(",",".",str_replace(".","",trim($post['margin_plan']))),
				'id_rab' 			=> $id_rab,
				'id_customer' 		=> $id_customer,
				'description' 		=> trim($post['description']),
				'notes' 			=> trim($post['notes']),
				'id_term' 			=> $id_term,
				'id_currency' 		=> $id_currency,
				'id_valid' 			=> $id_valid,
				'update_by'			=> $_SESSION["username"]
			];

			//$this->db->trans_off(); // Disable transaction
			$this->db->trans_start(); // set "True" for query will be rolled back
			$this->db->update($this->table_name, $data, [$this->primary_key => $uid]);
			// Update quote data
			$quote =[];
			// cek quote item
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
			$this->db->update($this->table_name, ['data_quotation' => json_encode($quote)], [$this->primary_key => $uid]);

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
			$this->db->trans_complete();

			$rs = $this->db->trans_status();
			return [$rs,$uid];
		} else return null;
	}  

	// getting row data for update / detail view
	public function getRowData($id) { 
		$rs = $this->db->select('*,DATE_FORMAT(FROM_UNIXTIME(date_quotation), "%d-%m-%Y") as dquotation')->where([$this->primary_key => $id])->get($this->table_name)->row();
		if(!empty($rs->id_rab)){
			$rd = $this->db->select('id_project,total_budget')->where([$this->primary_key => $rs->id_rab])->get($this->table_rab)->row();
			$rs = (object) array_merge((array) $rs, (array) $rd);
			if(!empty($rs->id_project)){
				$rd = $this->db->select('project,title as project_title')->where([$this->primary_key => $rs->id_project])->get($this->table_project)->row();
				$rs = (object) array_merge((array) $rs, (array) $rd);
			}
		}
		if(!empty($rs->id_customer)){
			$rd = $this->getCustomerInfo($rs->id_customer);
			$rs = (object) array_merge((array) $rs, (array) $rd);
		}
		if(!empty($rs->id_term)){
			$rd = $this->db->select('code as term')->where([$this->primary_key => $rs->id_term])->get($this->table_term)->row();
			$rs = (object) array_merge((array) $rs, (array) $rd);
		}
		if(!empty($rs->id_currency)){
			$rd = $this->db->select('code as currency')->where([$this->primary_key => $rs->id_currency])->get($this->table_currency)->row();
			$rs = (object) array_merge((array) $rs, (array) $rd);
		}
		if(!empty($rs->id_valid)){
			$rd = $this->db->select('description as valid')->where([$this->primary_key => $rs->id_valid])->get($this->table_valid)->row();
			$rs = (object) array_merge((array) $rs, (array) $rd);
		}

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

			$id_rab = !empty($v["E"])? trim($v["E"]):NULL;
			$id_customer = !empty($v["D"])? trim($v["D"]):0;
			$id_term = !empty($v["H"])? trim($v["H"]):NULL;
			$id_currency = !empty($v["I"])? trim($v["I"]):NULL;
			$id_valid = !empty($v["J"])? trim($v["J"]):NULL;
			$data = [
				'quotation' 		=> trim($v["B"]),
				'id_customer' 		=> $id_customer,
				'description' 		=> trim($v["F"]),
				'id_rab' 			=> $id_rab,
				'ketentuan' 		=> trim($v["G"]),
				'id_term' 			=> $id_term,
				'id_currency' 		=> $id_currency,
				'id_valid' 			=> $id_valid,
				'insert_by'			=> $_SESSION["username"]
			];

			$dquotation = trim($v["C"]);
			if(empty($dquotation)){
					$data['date_quotation'] = NULL;
			} else {
					$data['date_quotation'] = strtotime($dquotation);
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
					a.quotation,
					DATE_FORMAT(FROM_UNIXTIME(a.date_quotation), '%d-%m-%Y') as dquotation,
					b.name as customer,
					a.description,
					d.title as project_title,
					a.ketentuan,
			FROM
	   	 		".$this->table_name." a 
			LEFT JOIN 
				".$this->table_customer." b ON a.id_customer = b.id 
			LEFT JOIN 
				".$this->table_rab." c ON c.id=a.id_rab 
			LEFT JOIN 
				".$this->table_project." d ON d.id=c.id_project 
	   		ORDER BY
	   			".$this->primary_key." ASC
		";

		$res = $this->db->query($sql);
		$rs = $res->result_array();
		return $rs;
	}

	//============================== For Additional Method ==============================//
	// Get RAB info
	public function getRabInfo($id)
	{ 
		$rs = (object) ['total_budget'=>'0','id_customer'=>'','customer_name'=>'','customer_address'=>'',];
		if(!empty($id)){
			$rs = $this->db->select('id_project,total_budget')->where([$this->primary_key => $id])->get($this->table_rab)->row();
			if(!empty($rs->id_project)){
				$rd = $this->db->select('id_customer')->where([$this->primary_key => $rs->id_project])->get($this->table_project)->row();
				if(!empty($rd->id_customer)){
					$rx = $this->getCustomerInfo($rd->id_customer);
					$rs = (object) array_merge((array) $rs, (array) $rx);
				}
			} else {
				$rx = ['id_customer'=>'','customer_name'=>'','customer_address'=>'',];
				$rs = (object) array_merge((array) $rs, $rx);
			}
		}

		return $rs;
	} 

	// Get customer info
	public function getCustomerInfo($id)
	{ 
		$rs = $this->db->select('id as id_customer,name as customer_name,address as customer_address,contact_name as customer_contact')->where([$this->primary_key => $id])->get($this->table_customer)->row();

		return $rs;
	} 

	// Generate new quote item row
	public function getNewQuoteItemRow($row,$id=0,$view=FALSE)
	{ 
		if($id > 0){
			$data = $this->getQuoteItemRows($id,$view);
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
	
	// Generate quote item rows for edit & view
	public function getQuoteItemRows($id,$view,$print=FALSE){
		$dt = '';
		$rs = $this->db->select('data_quotation')->where([$this->primary_key => $id])->get($this->table_name)->row();
		$rd = json_decode($rs->data_quotation);
		$rs_num = count($rd);

		$row = 0;
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
				$dt .= '<td style="text-align: right;">'.$ai['description'].'</td>';
				$dt .= '<td style="text-align: right;">'.number_format($f->price,0,',','.').'</td>';
				$dt .= '<td style="text-align: right;">'.number_format($f->subtotal,0,',','.').'</td>';
				$dt .= '</tr>';
			}

			$row++;
		}

		return [$dt,$row];
	}

}
