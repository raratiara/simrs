<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Waiting_list_perawat_menu_model extends MY_Model
{
	/* Module */
 	protected $folder_name			= "pendaftaran/waiting_list_perawat_menu_model";
 	protected $table_name 			= _PREFIX_TABLE."diagnosa";
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
			'dt.id',
			'dt.no_urut',
			'dt.no_rekam_medis',
			'dt.nama_pasien',
			'dt.poli',
			'dt.dokter',
			'dt.status_desc'
			
		];
		

		$sIndexColumn = $this->primary_key;
		/*$sTable = ' '.$this->table_name;*/

		$sTable = '(SELECT a.*, b.no_rekam_medis, b.nama as nama_pasien, c.nama as poli, d.nama as dokter, 			e.nama as status_desc 
					FROM pendaftaran a 
					left join pasien b on b.id = a.pasien_id
					left join poli c on c.id = poli_id
					left join dokter d on d.id = a.dokter_id 
					left join status e on e.id = a.status
					where a.status = 1)dt';
		

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

		$no=1;
		foreach($rResult as $row)
		{
			$edit = "";
			if (_USER_ACCESS_LEVEL_UPDATE == "1")  {
				$edit = '<a class="btn btn-xs btn-primary" href="javascript:void(0);" onclick="edit('."'".$row->id."'".')" role="button"><i class="fa fa-pencil"></i></a>';
			}
			

			array_push($output["aaData"],array(
				'<div class="action-buttons">
					'.$edit.'
				</div>',
				$no,
				$row->no_urut,
				$row->no_rekam_medis,
				$row->nama_pasien,
				$row->poli,
				$row->dokter,
				$row->status_desc
				
				
			));

			$no++;
		}

		echo json_encode($output);
	}

	// filltering null value from array
	public function is_not_null($val){
		return !is_null($val);
	}		


	// adding data
	/*public function add_data($post) { 
		$status='2'; //waiting dokter
		

			
		$data = [
			'id_pendaftaran' 	=> trim($post['id_pendaftaran']),
			'tinggi_badan'		=> trim($post['tinggi_badan']),
			'suhu_tubuh' 		=> trim($post['suhu_tubuh']),
			'tekanan_darah' 	=> trim($post['tekanan_darah']),
			'saturasi' 			=> trim($post['saturasi']),
			'berat_badan' 		=> trim($post['berat_badan']),
			'denyut_nadi' 		=> trim($post['denyut_nadi']),
			'frekuensi_napas'	=> trim($post['frekuensi_napas']),
			'tingkat_nyeri' 	=> trim($post['tingkat_nyeri']),
			'pemeriksaan_tanda_vital_by' => $_SESSION["username"],
			'pemeriksaan_tanda_vital_at' => date("Y-m-d H:i:s")
		];

		return $rs = $this->db->insert($this->table_name, $data);
	}  */

	// update data
	public function edit_data($post) { 
		if(!empty($post['id'])){
			
			$data = [
				'tinggi_badan'		=> trim($post['tinggi_badan']),
				'suhu_tubuh' 		=> trim($post['suhu_tubuh']),
				'tekanan_darah' 	=> trim($post['tekanan_darah']),
				'saturasi' 			=> trim($post['saturasi']),
				'berat_badan' 		=> trim($post['berat_badan']),
				'denyut_nadi' 		=> trim($post['denyut_nadi']),
				'frekuensi_napas'	=> trim($post['frekuensi_napas']),
				'tingkat_nyeri' 	=> trim($post['tingkat_nyeri']),
				'pemeriksaan_tanda_vital_by' => $_SESSION["username"],
				'pemeriksaan_tanda_vital_at' => date("Y-m-d H:i:s")
			];

			if(trim($post['status']) == 1){
				$data_pendaftaran = [
					'status'		=> 2 //waiting dokter
				];
				$this->db->update('pendaftaran', $data_pendaftaran, [$this->primary_key => trim($post['id_pendaftaran'])]);
			}
			
			
			return  $rs = $this->db->update($this->table_name, $data, [$this->primary_key => trim($post['id'])]);
		} else return null;
	}  

	// getting row data for update / detail view
	public function getRowData($id) { 
		$mTable = '(select a.id as id_pendaftaran, a.date_pendaftaran, a.no_urut, a.poli_id, a.dokter_id, a.jenis_pembayaran, a.status, a.created_by, a.created_at, b.nama as poli, c.nama as dokter, d.nama as pasien, 		  e.nama as status_name, d.alamat_tinggal, d.no_tinggal, d.rt_tinggal, d.rw_tinggal, 				d.provinsi_id_tinggal, d.kabkota_id_tinggal, d.kec_id_tinggal, d.kel_id_tinggal, 				d.no_rekam_medis, a.date_jadwal_pemeriksaan, a.jam_jadwal_pemeriksaan
					,f.name as prov, g.name as kota, h.name as kec, i.name as kel, d.tgl_lahir, j.id
					from pendaftaran a
					left join poli b on b.id = a.poli_id
					left join dokter c on c.id = a.dokter_id
					left join pasien d on d.id = a.pasien_id
					left join status e on e.id = a.status
					left join provinsi f on f.id = d.provinsi_id_tinggal
					left join kabkota g on g.id = d.kabkota_id_tinggal
					left join kec h on h.id = d.kec_id_tinggal
					left join kelurahan i on i.id =  d.kel_id_tinggal
					left join diagnosa j on j.id_pendaftaran = a.id
				)dt';
		$idwhere = 'dt.id_pendaftaran';
		//$rs = $this->db->where([$this->primary_key => $id])->get($this->table_name)->row();
		$rs = $this->db->where([$idwhere => $id])->get($mTable)->row();
		
		
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
