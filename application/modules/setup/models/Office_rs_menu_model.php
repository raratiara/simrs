<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Office_rs_menu_model extends MY_Model
{
	/* Module */
 	protected $folder_name				= "setup/office_rs_menu";
 	protected $table_name 				= _PREFIX_TABLE."rumah_sakit";
 	protected $primary_key 				= "id";

	function __construct()
	{
		parent::__construct();
	}

	// fix
	public function get_list_data()
	{
		/*$aColumns = [
			NULL,
			NULL,
			'id',
			'provinsi_id',
			'name'
		];*/
		
		$aColumns = [
			NULL,
			NULL,
			'dt.id',
			'dt.nama',
			'dt.alamat_1',
			'dt.provinsi',
			'dt.kota',
			'dt.kecamatan',
			'dt.kode_pos',
			'dt.telp',
			'dt.fax',
			'dt.email',
			'dt.website'
			
		];

		$sIndexColumn = $this->primary_key;
		//$sTable = ' '.$this->table_name;
		$sTable = '(select a.id, a.nama, a.alamat_1, a.kode_pos, a.provinsi_id, a.kabkota_id, a.kec_id, a.telp, a.fax, a.website, a.email, a.logo
			, b.name as provinsi, c.name as kota, d.name as kecamatan
			from rumah_sakit a
			left join provinsi b on b.id = a.provinsi_id
			left join kabkota c on c.id = a.kabkota_id
			left join kec d on d.id = a.kec_id)dt';
		

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
				$row->nama,
				$row->alamat_1,
				$row->provinsi,
				$row->kota,
				$row->kecamatan,
				$row->kode_pos,
				$row->telp,
				$row->fax,
				$row->email,
				$row->website

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
			$this->db->where([$this->primary_key => $id])->delete($this->table_akses_name);
			$this->db->where([$this->primary_key => $id])->delete($this->table_akses_role_name);
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
				$this->db->where([$this->primary_key => $pid])->delete($this->table_akses_name);
				$this->db->where([$this->primary_key => $pid])->delete($this->table_akses_role_name);
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
			'nama' 			=> trim($post['nama_rs']),
			'alamat_1' 		=> trim($post['alamat']),
			'kode_pos' 		=> trim($post['kodepos']),
			'provinsi_id' 	=> trim($post['provinsi']),
			'kabkota_id' 	=> trim($post['kota']),
			'kec_id' 		=> trim($post['kecamatan']),
			'telp' 			=> trim($post['telp']),
			'fax' 			=> trim($post['fax']),
			'website' 		=> trim($post['web']),
			'email' 		=> trim($post['email']),
			'logo' 			=> trim($post['logo']),
			'created_by'	=> $_SESSION["username"]
			//'created_at'	=> date_format('Y-m-d H:i:s')
		];

		return $rs = $this->db->insert($this->table_name, $data);
	}  

	public function edit_data($post) { 
		if(!empty($post['id'])){
			$data = [
				'nama' 			=> trim($post['nama_rs']),
				'alamat_1' 		=> trim($post['alamat']),
				'kode_pos' 		=> trim($post['kodepos']),
				'provinsi_id' 	=> trim($post['provinsi']),
				'kabkota_id' 	=> trim($post['kota']),
				'kec_id' 		=> trim($post['kecamatan']),
				'telp' 			=> trim($post['telp']),
				'fax' 			=> trim($post['fax']),
				'website' 		=> trim($post['web']),
				'email' 		=> trim($post['email']),
				'logo' 			=> trim($post['logo']),
				'updated_by'	=> $_SESSION["username"]
			];

			return  $rs = $this->db->update($this->table_name, $data, [$this->primary_key => trim($post['id'])]);
		} else return null;
	}  

	public function getRowData($id) { 
		$mTable = '(select a.id, a.nama, a.alamat_1, a.kode_pos, a.provinsi_id, a.kabkota_id, a.kec_id, a.telp, a.fax, a.website, a.email, a.logo
				, b.name as provinsi, c.name as kota, d.name as kecamatan
				from rumah_sakit a
				left join provinsi b on b.id = a.provinsi_id
				left join kabkota c on c.id = a.kabkota_id
				left join kec d on d.id = a.kec_id)dt';
		//$rs = $this->db->where([$this->primary_key => $id])->get($this->table_name)->row();
		$rs = $this->db->where([$this->primary_key => $id])->get($mTable)->row();
		
		/*if(!empty($rs->provinsi_id)){
			$ri = $this->db->select('name as parent_title')->where([$this->primary_key => $rs->provinsi_id])->get($this->table_name)->row();
			$rs = (object) array_merge((array) $rs, (array) $ri);
		} else {
			$rs = (object) array_merge((array) $rs, ['parent_title'=>'-']);
		}*/
		
		return $rs;
	} 

	public function import_data($list_data)
	{
		$i = 0;

		foreach ($list_data as $k => $v) {
			$i += 1;

			$data = [
				'nama' 			=> $v["B"],
				'alamat_1' 		=> $v["C"],
				'kode_pos' 		=> $v["D"],
				'telp' 			=> $v["E"],
				'fax' 			=> $v["F"],
				'website' 		=> $v["G"],
				'email' 		=> $v["H"],
				'logo' 			=> $v["I"],
				'provinsi_id' 	=> $v["J"],
				'kabkota_id' 	=> $v["K"],
				'kec_id' 		=> $v["L"]
				//'insert_by'			=> $_SESSION["username"]
			];

			$rs = $this->db->insert($this->table_name, $data);
			if (!$rs) $error .=",baris ". $v["A"];
		}

		return $error;
	}

	public function eksport_data()
	{
		$sql = "select a.nama, a.alamat_1, a.kode_pos, a.telp, a.fax, a.website, a.email, a.logo
				, b.name as provinsi, c.name as kota, d.name as kecamatan
				from rumah_sakit a
				left join provinsi b on b.id = a.provinsi_id
				left join kabkota c on c.id = a.kabkota_id
				left join kec d on d.id = a.kec_id

	   		ORDER BY a.id ASC
		";

		$res = $this->db->query($sql);
		$rs = $res->result_array();
		return $rs;
	}

}
