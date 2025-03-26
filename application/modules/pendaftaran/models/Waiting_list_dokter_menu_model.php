<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Waiting_list_dokter_menu_model extends MY_Model
{
	/* Module */
 	protected $folder_name			= "pendaftaran/waiting_list_dokter_menu_model";
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
					where a.status = 2)dt';
		

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


	// Get next number 
	public function getNextInvNumber() { 
		
		$year = date("Y");
		

		$cek = $this->db->query("select * FROM pembayaran where SUBSTRING(invoice_no, 8, 4) = '".$year."'");
		$rs_cek = $cek->result_array();

		if(empty($rs_cek)){
			$num = '00001';
		}else{
			$cek2 = $this->db->query("select max(invoice_no) as maxnum from pembayaran where SUBSTRING(invoice_no, 8, 4) = '".$year."'");
			$rs_cek2 = $cek2->result_array();
			$dt = $rs_cek2[0]['maxnum']; 
			$getnum = substr($dt,13); 
			$num = str_pad($getnum + 1, 5, 0, STR_PAD_LEFT);
			
		}

		return $num;
		
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
		$year = date("Y");

		if(!empty($post['id'])){
			
			$data = [
				'wawancara_medis'		=> trim($post['wawancara']),
				'diagnosa' 				=> trim($post['diagnosa']),
				'pemeriksaan_penunjang' => trim($post['pemeriksaan_penunjang']),
				'pemeriksaan_dokter_by' => $_SESSION["username"],
				'pemeriksaan_dokter_at' => date("Y-m-d H:i:s")
			];

			if(trim($post['status']) == 2){
				$runningnumber = $this->getNextInvNumber(); // next count number
				$nextnum 	='RS-INV/'.$year.'/'.$runningnumber;

				$data_pendaftaran = [
					'status'		=> 3 //waiting kasir
				];
				$this->db->update('pendaftaran', $data_pendaftaran, [$this->primary_key => trim($post['id_pendaftaran'])]);


				$data_pembayaran = [
					'pendaftaran_id' 	=> trim($post['id_pendaftaran']),
					'invoice_no' 		=> $nextnum,
					'invoice_date' 		=> date("Y-m-d")
				];

				$this->db->insert('pembayaran', $data_pembayaran);
			}


			if(isset($post['kode'])){
				$item_num = count($post['kode']); // cek sum
				$item_len_min = min(array_keys($post['kode'])); // cek min key index
				$item_len = max(array_keys($post['kode'])); // cek max key index
			} else {
				$item_num = 0;
			}
			if($item_num>0){
				for($i=$item_len_min;$i<=$item_len;$i++) 
				{
					if(isset($post['kode'][$i])){
						$nominal = trim($post['qty'][$i])*trim($post['hdnharga'][$i]);
						if($post['hdnid'][$i] != ''){
							$uid = $post['hdnid'][$i];
							$itemData = [
								'obat_id' 		=> trim($post['kode'][$i]),
								'qty' 			=> trim($post['qty'][$i]),
								'nominal' 		=> $nominal
								];

							$this->db->update('resep', $itemData, [$this->primary_key => $uid]);
						}else{ 
							$itemData = [
								'diagnosa_id' 	=> $post['id'],
								'obat_id' 		=> trim($post['kode'][$i]),
								'qty' 			=> trim($post['qty'][$i]),
								'nominal' 		=> $nominal
								];

							$this->db->insert('resep', $itemData);

						}
						
						
					}
				}
			}
			

			return  $rs = $this->db->update($this->table_name, $data, [$this->primary_key => trim($post['id'])]);
		} else return null;
	}  

	// getting row data for update / detail view
	public function getRowData($id) { 
		$mTable = '(select a.id as id_pendaftaran, a.date_pendaftaran, a.no_urut, a.poli_id, a.dokter_id, a.jenis_pembayaran, a.status, a.created_by, a.created_at, b.nama as poli, c.nama as dokter, d.nama as pasien, 		  e.nama as status_name, d.alamat_tinggal, d.no_tinggal, d.rt_tinggal, d.rw_tinggal, 				d.provinsi_id_tinggal, d.kabkota_id_tinggal, d.kec_id_tinggal, d.kel_id_tinggal, 				d.no_rekam_medis, a.date_jadwal_pemeriksaan, a.jam_jadwal_pemeriksaan
					,f.name as prov, g.name as kota, h.name as kec, i.name as kel, d.tgl_lahir, j.id,
					j.tinggi_badan, j.suhu_tubuh, j.tekanan_darah, j.saturasi, j.berat_badan, j.denyut_nadi, j.frekuensi_napas, j.tingkat_nyeri, j.wawancara_medis, j.diagnosa, j.pemeriksaan_penunjang
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


	// export data
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


	// Generate new expenses item row
	public function getNewExpensesRow($row,$id=0,$view=FALSE)
	{ 
		if($id > 0){ 
			$data = $this->getExpensesRows($id,$view);
		} else { 
			$data = '';
			$no = $row+1;
			$msObat = $this->db->query("select a.*, b.description as satuan from obat a left join option_uom b on b.id = a.satuan_id")->result(); 
			
			$data 	.= '<td>'.$no.'<input type="hidden" id="hdnid'.$row.'" name="hdnid['.$row.']" value=""/><input type="hidden" id="hdnharga'.$row.'" name="hdnharga['.$row.']" value=""/></td>';
			$data 	.= '<td>'.$this->return_build_chosenme($msObat,'','','','kode['.$row.']','','kode','','id','kode','','','',' data-id="'.$row.'" onchange="chgKode('.$row.')" ').'</td>';
			$data 	.= '<td>'.$this->return_build_txt('','nama['.$row.']','','nama','text-align: right;','data-id="'.$row.'" readonly').'</td>';
			$data 	.= '<td>'.$this->return_build_txt('','qty['.$row.']','','qty','text-align: right;','data-id="'.$row.'"').'</td>';
			$data 	.= '<td>'.$this->return_build_txt('','satuan['.$row.']','','satuan','text-align: right;','data-id="'.$row.'" readonly').'</td>';

			$hdnid='';
			$data 	.= '<td><input type="button" class="ibtnDel btn btn-md btn-danger " onclick="del(\''.$row.'\',\''.$hdnid.'\')" value="Delete"></td>';
		}

		return $data;
	} 
	
	// Generate expenses item rows for edit & view
	public function getExpensesRows($id,$view,$print=FALSE){ 
		$dt = ''; 
		
		$rs = $this->db->query("select a.*, b.nama as nama_obat, b.satuan_id, b.harga, c.description as satuan from resep a left join obat b on b.id = a.obat_id left join option_uom c on c.id = b.satuan_id where a.diagnosa_id = '".$id."'")->result(); 
		$rd = $rs;

		$row = 0; 
		if(!empty($rd)){ 
			$rs_num = count($rd); 
			$tbl_obat = 'obat'; 
			$msObat = $this->db->query("select * from obat")->result(); 
			
			/*if($view){
				$arrSat = json_decode(json_encode($msObat), true);
				$arrS = [];
				foreach($arrSat as $ai){
					$arrS[$ai['id']] = $ai;
				}
			}*/
			foreach ($rd as $f){
				$no = $row+1;
				if(!$view){
					$dt .= '<tr>';
					$dt .= '<td>'.$no.'<input type="hidden" id="hdnid'.$row.'" name="hdnid['.$row.']" value="'.$f->id.'"/><input type="hidden" id="hdnharga'.$row.'" name="hdnharga['.$row.']" value="'.$f->harga.'"/></td>';
					$dt .= '<td>'.$this->return_build_chosenme($msObat,'',isset($f->obat_id)?$f->obat_id:1,'','kode['.$row.']','','kode','','id','kode','','','',' data-id="'.$row.'" onchange="chgKode('.$row.')" ').'</td>';
					
					$dt .= '<td>'.$this->return_build_txt($f->nama_obat,'nama['.$row.']','','nama','text-align: right;','data-id="'.$row.'" readonly').'</td>';
					$dt .= '<td>'.$this->return_build_txt($f->qty,'qty['.$row.']','','qty','text-align: right;','data-id="'.$row.'"').'</td>';
					$dt .= '<td>'.$this->return_build_txt($f->satuan,'satuan['.$row.']','','satuan','text-align: right;','data-id="'.$row.'" readonly').'</td>';
					
					$dt .= '<td><input type="button" class="ibtnDel btn btn-md btn-danger "  value="Delete" onclick="del(\''.$row.'\',\''.$f->id.'\')"></td>';
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
					$dt .= '<td>'.$f->obat_id.'</td>';
					$dt .= '<td>'.$f->obat_id.'</td>';
					$dt .= '<td>'.$f->qty.'</td>';
					$dt .= '<td>'.$f->obat_id.'</td>';
					$dt .= '</tr>';
				}

				$row++;
			}
		}

		return [$dt,$row];
	}



}
