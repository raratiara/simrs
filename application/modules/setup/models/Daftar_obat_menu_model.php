<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Daftar_obat_menu_model extends MY_Model
{
	/* Module */
 	protected $folder_name				= "setup/daftar_obat_menu";
 	protected $table_name 				= _PREFIX_TABLE."obat";
 	protected $primary_key 				= "id";

	function __construct()
	{
		parent::__construct();
	}

	// fix
	public function get_list_data()
	{
		$aColumns = [
			NULL,
			NULL,
			'dt.id',
			'dt.kategori',
			'dt.kode',
			'dt.nama',
			'dt.satuan',
			'dt.harga',
			'dt.qty_awal',
			'dt.qty_onhand',
			'dt.min_qty'
		];
		

		$sIndexColumn = $this->primary_key;
		/*$sTable = ' '.$this->table_name;*/

		$sTable = '(SELECT a.id, a.kategori_id, a.kode, a.nama, a.satuan_id, a.harga, a.qty_awal, a.qty_onhand, a.min_qty, b.nama as kategori, c.description as satuan
			FROM obat a
			left join option_kategori_obat b on b.id = a.kategori_id
			left join option_uom c on c.id = a.satuan_id )dt';
		

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
				$row->kategori,
				$row->kode,
				$row->nama,
				$row->satuan,
				$row->harga,
				$row->qty_awal,
				$row->qty_onhand,
				$row->min_qty

			));
		}

		echo json_encode($output);
	}

	// filltering null value from array
	public function is_not_null($val){
		return !is_null($val);
	}		

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

	public function add_data($post) {
		$data = [
			'kategori_id' 	=> trim($post['kategori']),
			'kode' 			=> trim($post['kode']),
			'nama' 			=> trim($post['nama']),
			'satuan_id' 	=> trim($post['satuan']),
			'harga' 		=> trim($post['harga']),
			'qty_awal' 		=> trim($post['qty_awal']),
			'qty_onhand' 	=> trim($post['qty_onhand']),
			'min_qty' 		=> trim($post['min_qty'])
		];

		return $rs = $this->db->insert($this->table_name, $data);
	}  

	public function edit_data($post) { 
		if(!empty($post['id'])){
			$data = [
				'kategori_id' 	=> trim($post['kategori']),
				'kode' 			=> trim($post['kode']),
				'nama' 			=> trim($post['nama']),
				'satuan_id' 	=> trim($post['satuan']),
				'harga' 		=> trim($post['harga']),
				'qty_awal' 		=> trim($post['qty_awal']),
				'qty_onhand' 	=> trim($post['qty_onhand']),
				'min_qty' 		=> trim($post['min_qty'])
			];

			return  $rs = $this->db->update($this->table_name, $data, [$this->primary_key => trim($post['id'])]);
		} else return null;
	}  

	public function getRowData($id) { 
		$mTable = '(SELECT a.id, a.kategori_id, a.kode, a.nama, a.satuan_id, a.harga, a.qty_awal, a.qty_onhand, a.min_qty, b.nama as kategori, c.description as satuan
			FROM obat a
			left join option_kategori_obat b on b.id = a.kategori_id
			left join option_uom c on c.id = a.satuan_id )dt';
		
		//$rs = $this->db->where([$this->primary_key => $id])->get($this->table_name)->row();
		$rs = $this->db->where([$this->primary_key => $id])->get($mTable)->row();
		
		
		return $rs;
	} 

	public function import_data($list_data)
	{
		$i = 0;

		foreach ($list_data as $k => $v) {
			$i += 1;

			$data = [
				'kategori_id' 	=> $v["B"],
				'kode'			=> $v["C"],
				'nama' 			=> $v["D"],
				'satuan_id' 	=> $v["E"],
				'harga' 		=> $v["F"],
				'qty_awal' 		=> $v["G"],
				'qty_onhand' 	=> $v["H"],
				'min_qty' 		=> $v["I"]
				
			];

			$rs = $this->db->insert($this->table_name, $data);
			if (!$rs) $error .=",baris ". $v["A"];
		}

		return $error;
	}

	public function eksport_data()
	{
		$sql = "select a.id, b.nama as kategori, a.kode, a.nama, c.description as satuan, a.harga, a.qty_awal, a.qty_onhand, a.min_qty
			FROM obat a
			left join option_kategori_obat b on b.id = a.kategori_id
			left join option_uom c on c.id = a.satuan_id
	   			ORDER BY a.id ASC
		";

		$res = $this->db->query($sql);
		$rs = $res->result_array();
		return $rs;
	}

}
