<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pasien_lama_menu_model extends MY_Model
{
	/* Module */
 	protected $folder_name			= "pendaftaran/pasien_lama_menu";
 	protected $table_name 			= _PREFIX_TABLE."pendaftaran";
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
			'dt.id',
			'dt.date_pendaftaran',
			'dt.date_jadwal_pemeriksaan',
			'dt.jam_jadwal_pemeriksaan',
			'dt.no_urut',
			'dt.poli',
			'dt.dokter',
			'dt.jenis_pembayaran',
			'dt.status_name',
			'dt.pasien'
		];
		

		$sIndexColumn = $this->primary_key;
		/*$sTable = ' '.$this->table_name;*/

		$sTable = '(select a.id, a.date_pendaftaran, a.no_urut, a.poli_id, a.dokter_id, a.jenis_pembayaran, 		a.status, a.created_by, a.created_at, b.nama as poli, c.nama as dokter, d.nama as pasien, 		  e.nama as status_name, d.alamat_tinggal, d.no_tinggal, d.rt_tinggal, d.rw_tinggal, 				d.provinsi_id_tinggal, d.kabkota_id_tinggal, d.kec_id_tinggal, d.kel_id_tinggal, 				d.no_rekam_medis, a.date_jadwal_pemeriksaan, a.jam_jadwal_pemeriksaan
					,f.name as prov, g.name as kota, h.name as kec, i.name as kel
					from pendaftaran a
					left join poli b on b.id = a.poli_id
					left join dokter c on c.id = a.dokter_id
					left join pasien d on d.id = a.pasien_id
					left join status e on e.id = a.status
					left join provinsi f on f.id = d.provinsi_id_tinggal
					left join kabkota g on g.id = d.kabkota_id_tinggal
					left join kec h on h.id = d.kec_id_tinggal
					left join kelurahan i on i.id =  d.kel_id_tinggal)dt';
		

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
				$row->date_pendaftaran,
				$row->date_jadwal_pemeriksaan,
				$row->jam_jadwal_pemeriksaan,
				$row->no_urut,
				$row->poli,
				$row->dokter,
				$row->jenis_pembayaran,
				$row->status_name,
				$row->pasien

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
		$status='1'; //pendaftaran
		$no_urut='';

		$data_pendaftaran = $this->db->query("select * from pendaftaran where date_pendaftaran = '".date("Y-m-d")."'"); 
		$row_data_pendaftaran = $data_pendaftaran->result_array();

		if(empty($row_data_pendaftaran)){
			$no_urut = '1';
		}else{
			$data_terdaftar = $this->db->query("select max(no_urut) as max_urut from pendaftaran where date_pendaftaran = '".date("Y-m-d")."'");
			$row_data_terdaftar = $data_terdaftar->result_array();
			$getno = $row_data_terdaftar[0]['max_urut']; 
			$no_urut= $getno+1;
		}

			
		$data = [
			'date_pendaftaran' 			=> date("Y-m-d"),
			'date_jadwal_pemeriksaan'	=> date("Y-m-d"),
			'jam_jadwal_pemeriksaan' 	=> date("H:i:s"),
			'no_urut' 					=> $no_urut,
			'poli_id' 					=> trim($post['poli']),
			'dokter_id' 				=> trim($post['dokter']),
			'jenis_pembayaran' 			=> trim($post['jenis_pembayaran']),
			'status' 					=> $status,
			'pasien_id' 				=> trim($post['pasien']),
			'created_by'				=> $_SESSION["username"],
			'created_at' 				=> date("Y-m-d H:i:s")
		];


		return $rs = $this->db->insert($this->table_name, $data);

		/*$data_diagnosa = [
			'id_pendaftaran' 			=> $rs
		];

		$this->db->insert('diagnosa', $data_diagnosa);*/

	}  

	// update data
	public function edit_data($post) { 
		if(!empty($post['id'])){
			$data = [
				/*'date_jadwal_pemeriksaan'	=> trim($post['jenis_kelamin']),
				'jam_jadwal_pemeriksaan' 	=> trim($post['agama']),*/
				'poli_id' 				=> trim($post['poli']),
				'dokter_id' 			=> trim($post['dokter']),
				'jenis_pembayaran' 		=> trim($post['jenis_pembayaran']),
				//'pasien_id' 			=> trim($post['pasien']),
				'modified_by'			=> $_SESSION["username"],
				'modified_at' 			=> date("Y-m-d H:i:s")
			];
			
			return  $rs = $this->db->update($this->table_name, $data, [$this->primary_key => trim($post['id'])]);
		} else return null;
	}  

	// getting row data for update / detail view
	public function getRowData($id) { 
		$mTable = '(select a.id, a.date_pendaftaran, a.no_urut, a.poli_id, a.dokter_id, a.jenis_pembayaran, 		a.status, a.created_by, a.created_at, b.nama as poli, c.nama as dokter, d.nama as pasien, 		  e.nama as status_name, d.alamat_tinggal, d.no_tinggal, d.rt_tinggal, d.rw_tinggal, 				d.provinsi_id_tinggal, d.kabkota_id_tinggal, d.kec_id_tinggal, d.kel_id_tinggal, 				d.no_rekam_medis, a.date_jadwal_pemeriksaan, a.jam_jadwal_pemeriksaan
					,f.name as prov, g.name as kota, h.name as kec, i.name as kel, d.tgl_lahir
					from pendaftaran a
					left join poli b on b.id = a.poli_id
					left join dokter c on c.id = a.dokter_id
					left join pasien d on d.id = a.pasien_id
					left join status e on e.id = a.status
					left join provinsi f on f.id = d.provinsi_id_tinggal
					left join kabkota g on g.id = d.kabkota_id_tinggal
					left join kec h on h.id = d.kec_id_tinggal
					left join kelurahan i on i.id =  d.kel_id_tinggal)dt';
		
		//$rs = $this->db->where([$this->primary_key => $id])->get($this->table_name)->row();
		$rs = $this->db->where([$this->primary_key => $id])->get($mTable)->row();
		
		
		return $rs;
	} 

	public function getRowData_bynorm($val) { 
	
		$sql = '
	   			select a.id, a.date_pendaftaran, a.no_urut, a.poli_id, a.dokter_id, a.jenis_pembayaran, 		a.status, a.created_by, a.created_at, b.nama as poli, c.nama as dokter, d.nama as pasien, 		  e.nama as status_name, d.alamat_tinggal, d.no_tinggal, d.rt_tinggal, d.rw_tinggal, 				d.provinsi_id_tinggal, d.kabkota_id_tinggal, d.kec_id_tinggal, d.kel_id_tinggal, 				d.no_rekam_medis, a.date_jadwal_pemeriksaan, a.jam_jadwal_pemeriksaan, d.id as pasien_id
					,f.name as prov, g.name as kota, h.name as kec, i.name as kel, d.tgl_lahir
					from pendaftaran a
					left join poli b on b.id = a.poli_id
					left join dokter c on c.id = a.dokter_id
					left join pasien d on d.id = a.pasien_id
					left join status e on e.id = a.status
					left join provinsi f on f.id = d.provinsi_id_tinggal
					left join kabkota g on g.id = d.kabkota_id_tinggal
					left join kec h on h.id = d.kec_id_tinggal
					left join kelurahan i on i.id =  d.kel_id_tinggal
				 where d.no_rekam_medis = "'.$val.'"
		';

		$res = $this->db->query($sql);
		$rs = $res->result_array(); 
		return $rs;
	} 

	// importing data
	public function import_data($list_data)
	{
		$i = 0;

		foreach ($list_data as $k => $v) {
			$i += 1;

			$data = [
				'date_pendaftaran' 			=> $v["B"],
				'date_jadwal_pemeriksaan'	=> $v["C"],
				'jam_jadwal_pemeriksaan' 	=> $v["D"],
				'no_urut' 					=> $v["E"],
				'poli_id' 					=> $v["F"],
				'dokter_id' 				=> $v["G"],
				'jenis_pembayaran' 			=> $v["H"],
				'status' 					=> $v["I"],
				'pasien_id' 				=> $v["J"]
				

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
		$sql = '
	   			select a.id, a.date_pendaftaran, a.date_jadwal_pemeriksaan, a.jam_jadwal_pemeriksaan, a.no_urut, b.nama as poli, c.nama as dokter, a.jenis_pembayaran, e.nama as status_name, d.nama as pasien
					from pendaftaran a
					left join poli b on b.id = a.poli_id
					left join dokter c on c.id = a.dokter_id
					left join pasien d on d.id = a.pasien_id
					left join status e on e.id = a.status
					left join provinsi f on f.id = d.provinsi_id_tinggal
					left join kabkota g on g.id = d.kabkota_id_tinggal
					left join kec h on h.id = d.kec_id_tinggal
					left join kelurahan i on i.id =  d.kel_id_tinggal
					order by a.id asc
		';

		$res = $this->db->query($sql);
		$rs = $res->result_array();
		return $rs;
	}

}
