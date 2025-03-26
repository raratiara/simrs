<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pasien_baru_menu_model extends MY_Model
{
	/* Module */
 	protected $folder_name			= "pendaftaran/pasien_baru_menu";
 	protected $table_name 			= _PREFIX_TABLE."pasien";
 	protected $primary_key 			= "id";
 	/* upload */
 	protected $attachment_folder	= "./uploads/pasien";
	protected $allow_type			= "gif|jpeg|jpg|png|pdf|xls|xlsx|doc|docx|txt";
	protected $allow_size			= "0"; // 0 for limit by default php conf (in Kb)

	function __construct()
	{
		parent::__construct();
	}


	// Upload file
	public function upload_file($id = "", $fieldname= "", $replace=FALSE, $oldfilename= "", $array=FALSE, $i=0) { 
		$data = array();
		$data['status'] = FALSE; 
		if(!empty($id) && !empty($fieldname)){ 
			// handling multiple upload (as array field)

			if($array){ 
				// Define new $_FILES array - $_FILES['file']
				$_FILES['file']['name'] = $_FILES[$fieldname]['name'];
				$_FILES['file']['type'] = $_FILES[$fieldname]['type'];
				$_FILES['file']['tmp_name'] = $_FILES[$fieldname]['tmp_name'];
				$_FILES['file']['error'] = $_FILES[$fieldname]['error'];
				$_FILES['file']['size'] = $_FILES[$fieldname]['size']; 
				// override field
				//$fieldname = 'foto';
			}
			// handling regular upload (as one field)
			if(isset($_FILES[$fieldname]) && !empty($_FILES[$fieldname]['name']))
			{ 
				/*$dir = $this->attachment_folder.'/'.$id;
				if(!is_dir($dir)) {
					mkdir($dir);
				}
				if($replace){
					$this->remove_file($id, $oldfilename);
				}*/
				$config['upload_path']   = $this->attachment_folder;
				$config['allowed_types'] = $this->allow_type;
				$config['max_size'] 	 = $this->allow_size;
				
				$this->load->library('upload', $config); 
				
				if(!$this->upload->do_upload($fieldname)){  
					$err_msg = $this->upload->display_errors(); 
					$data['error_warning'] = strip_tags($err_msg);				
					$data['status'] = FALSE;
				} else {
					$fileData = $this->upload->data();
					$data['upload_file'] = $fileData['file_name'];
					$data['status'] = TRUE;
				}
			}
		}

		
		
		return $data;
	}


	// Generate item list
	public function get_list_data()
	{
		$aColumns = [
			NULL,
			NULL,
			'dt.id',
			'dt.no_rekam_medis',
			'dt.nama',
			'dt.tgl_lahir',
			'dt.alamat_tinggal',
			'no_hp',
			'no_bpjs'
		];
		

		$sIndexColumn = $this->primary_key;
		/*$sTable = ' '.$this->table_name;*/

		$sTable = '(
					select 
					a.id, a.no_rekam_medis, a.nama, a.jenis_kelamin, a.agama_id, a.pendidikan, a.pekerjaan, a.status_kawin, a.no_hp, a.email, a.alamat_tinggal, a.no_tinggal, a.rt_tinggal, a.rw_tinggal, a.provinsi_id_tinggal, a.kabkota_id_tinggal, a.kec_id_tinggal, a.kel_id_tinggal, a.no_bpjs, a.jenis_identitas_id, a.no_identitias, a.attachment_bpjs, a.attachment_identitas, a.alamat_identitas, a.no_identitas, a.rt_identitas, a.rw_identitas, a.prov_id_identitas, a.kabkota_id_identitas, a.kec_id_identitas, a.kel_id_identitas, a.nama_lengkap_ayah, a.nama_lengkap_ibu, a.nama_lengkap_pasangan, a.hp_pasangan, a.nama_penanggung_jawab, a.hp_penanggung_jawab, a.tgl_lahir, a.tempat_lahir
					, if(a.jenis_kelamin="p","Perempuan","Laki-laki") as jenis_kelamin_desc, b.nama as agama, c.nama as pendidikan_desc
					, d.nama as status_kawin_desc, e.name as provinsi_tinggal, f.name as kabkota_tinggal, g.name as kec_tinggal
					, h.name as kel_tinggal, i.nama as jenis_identitas
					, ee.name as provinsi_identitas, ff.name as kabkota_identitas, gg.name as kec_identitas, hh.name as kel_identitas
					from pasien a
					left join agama b on b.id = a.agama_id
					left join pendidikan c on c.id = a.pendidikan
					left join status_kawin d on d.id = a.status_kawin
					left join provinsi e on e.id = a.provinsi_id_tinggal
					left join kabkota f on f.id = a.kabkota_id_tinggal
					left join kec g on g.id = a.kec_id_tinggal
					left join kelurahan h on h.id = a.kel_id_tinggal
					left join tipe_identitas i on i.id = a.jenis_identitas_id
					left join provinsi ee on ee.id = a.prov_id_identitas
					left join kabkota ff on ff.id = a.kabkota_id_identitas
					left join kec gg on gg.id = a.kec_id_identitas
					left join kelurahan hh on hh.id = a.kel_id_identitas)dt';
		

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
				$row->no_rekam_medis,
				$row->nama,
				$row->tgl_lahir,
				$row->alamat_tinggal,
				$row->no_hp,
				$row->no_bpjs


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

	// Get next number 
	public function getNextNumber() { 
		
		$yearcode = date("y");
		$monthcode = date("m");
		$period = $yearcode.$monthcode; 
		

		$cek = $this->db->query("select * from pasien where SUBSTRING(no_rekam_medis, 3, 4) = '".$period."'");
		$rs_cek = $cek->result_array();

		if(empty($rs_cek)){
			$num = '0001';
		}else{
			$cek2 = $this->db->query("select max(no_rekam_medis) as maxnum from pasien where SUBSTRING(no_rekam_medis, 3, 4) = '".$period."'");
			$rs_cek2 = $cek2->result_array();
			$dt = $rs_cek2[0]['maxnum']; 
			$getnum = substr($dt,6); 
			$num = str_pad($getnum + 1, 4, 0, STR_PAD_LEFT);
			
		}

		return $num;
		
	} 

	// adding data
	public function add_data($post) { 
		// BOF auto numbering 
		$lettercode = ('RM'); // ca code
		$yearcode = date("y");
		$monthcode = date("m");
		$period = $yearcode.$monthcode; 
		
		
		$runningnumber = $this->getNextNumber(); // next count number
		
		$nextnum 	= $lettercode.$period.$runningnumber;


		$upload_bpjs = $this->upload_file('1', 'foto_bpjs', FALSE, '', TRUE, '');
		$foto_bpjs = '';
		if($upload_bpjs['status']){
			$foto_bpjs = $upload_bpjs['upload_file'];
		} else if(isset($upload_bpjs['error_warning'])){
			echo $upload_bpjs['error_warning']; exit;
		}

		$upload_identitas = $this->upload_file('1', 'foto_identitas', FALSE, '', TRUE, '');
		$foto_identitas = '';
		if($upload_identitas['status']){
			$foto_identitas = $upload_identitas['upload_file'];
		} else if(isset($upload_identitas['error_warning'])){
			echo $upload_identitas['error_warning']; exit;
		}
		
			
		$data = [
			'no_rekam_medis' 		=> $nextnum,
			'nama' 					=> trim($post['nama_pasien']),
			'jenis_kelamin' 		=> trim($post['jenis_kelamin']),
			'agama_id' 				=> trim($post['agama']),
			'pendidikan' 			=> trim($post['pendidikan']),
			'pekerjaan' 			=> trim($post['pekerjaan']),
			'status_kawin' 			=> trim($post['status_kawin']),
			'no_hp' 				=> trim($post['no_hp']),
			'email' 				=> trim($post['email']),
			'alamat_tinggal' 		=> trim($post['alamat_tinggal']),
			'no_tinggal' 			=> trim($post['alamat_tinggal_no']),
			'rt_tinggal' 			=> trim($post['alamat_tinggal_rt']),
			'rw_tinggal' 			=> trim($post['alamat_tinggal_rw']),
			'provinsi_id_tinggal' 	=> trim($post['prov_tempattinggal']),
			'kabkota_id_tinggal' 	=> trim($post['kota_tempattinggal']),
			'kec_id_tinggal' 		=> trim($post['kec_tempattinggal']),
			'kel_id_tinggal' 		=> trim($post['kel_tempattinggal']),
			'no_bpjs' 				=> trim($post['no_bpjs']),
			'jenis_identitas_id' 	=> trim($post['tipe_identitas']),
			'no_identitias' 		=> trim($post['no_identitas']),
			'attachment_bpjs' 		=> $foto_bpjs,
			'attachment_identitas' 	=> $foto_identitas,
			'alamat_identitas' 		=> trim($post['alamat_identitas']),
			'no_identitas' 			=> trim($post['alamat_identitas_no']),
			'rt_identitas' 			=> trim($post['alamat_identitas_rt']),
			'rw_identitas' 			=> trim($post['alamat_identitas_rw']),  
			'prov_id_identitas' 	=> trim($post['prov_tempatidentitas']),  
			'kabkota_id_identitas' 	=> trim($post['kota_tempatidentitas']),
			'kec_id_identitas' 		=> trim($post['kec_tempatidentitas']),
			'kel_id_identitas' 		=> trim($post['kel_tempatidentitas']),
			'nama_lengkap_ayah' 	=> trim($post['nama_ayah']),
			'nama_lengkap_ibu' 		=> trim($post['nama_ibu']),
			'nama_lengkap_pasangan'	=> trim($post['nama_pasangan']),
			'hp_pasangan' 			=> trim($post['nohp_pasangan']),
			'nama_penanggung_jawab' => trim($post['nama_penanggung']),
			'hp_penanggung_jawab' 	=> trim($post['nohp_penanggung']),
			'kec_id_identitas' 		=> trim($post['kec_tempatidentitas']),
			'tgl_lahir' 			=> trim($post['tgl_lahir']),
			'tempat_lahir' 			=> trim($post['tempat_lahir']),
			'created_by'			=> $_SESSION["username"],
			'created_at' 			=> date("Y-m-d H:i:s")
		];

		return $rs = $this->db->insert($this->table_name, $data);
	}  

	// update data
	public function edit_data($post) { 

		if(!empty($post['id'])){

			$upload_bpjs = $this->upload_file('1', 'foto_bpjs', FALSE, '', TRUE, '');
			$foto_bpjs = '';
			if($upload_bpjs['status']){
				$foto_bpjs = $upload_bpjs['upload_file'];
			} else if(isset($upload_bpjs['error_warning'])){
				echo $upload_bpjs['error_warning']; exit;
			}

			$upload_identitas = $this->upload_file('1', 'foto_identitas', FALSE, '', TRUE, '');
			$foto_identitas = '';
			if($upload_identitas['status']){
				$foto_identitas = $upload_identitas['upload_file'];
			} else if(isset($upload_identitas['error_warning'])){
				echo $upload_identitas['error_warning']; exit;
			}


			$data = [
				'nama' 					=> trim($post['nama_pasien']),
				'jenis_kelamin' 		=> trim($post['jenis_kelamin']),
				'agama_id' 				=> trim($post['agama']),
				'pendidikan' 			=> trim($post['pendidikan']),
				'pekerjaan' 			=> trim($post['pekerjaan']),
				'status_kawin' 			=> trim($post['status_kawin']),
				'no_hp' 				=> trim($post['no_hp']),
				'email' 				=> trim($post['email']),
				'alamat_tinggal' 		=> trim($post['alamat_tinggal']),
				'no_tinggal' 			=> trim($post['alamat_tinggal_no']),
				'rt_tinggal' 			=> trim($post['alamat_tinggal_rt']),
				'rw_tinggal' 			=> trim($post['alamat_tinggal_rw']),
				'provinsi_id_tinggal' 	=> trim($post['prov_tempattinggal']),
				'kabkota_id_tinggal' 	=> trim($post['kota_tempattinggal']),
				'kec_id_tinggal' 		=> trim($post['kec_tempattinggal']),
				'kel_id_tinggal' 		=> trim($post['kel_tempattinggal']),
				'no_bpjs' 				=> trim($post['no_bpjs']),
				'jenis_identitas_id' 	=> trim($post['tipe_identitas']),
				'no_identitias' 		=> trim($post['no_identitas']),
				'attachment_bpjs' 		=> $foto_bpjs,
				'attachment_identitas' 	=> $foto_identitas,
				'alamat_identitas' 		=> trim($post['alamat_identitas']),
				'no_identitas' 			=> trim($post['alamat_identitas_no']),
				'rt_identitas' 			=> trim($post['alamat_identitas_rt']),
				'rw_identitas' 			=> trim($post['alamat_identitas_rw']),
				'prov_id_identitas' 	=> $trim($post['prov_tempatidentitas']),
				'kabkota_id_identitas' 	=> trim($post['kota_tempatidentitas']),
				'kec_id_identitas' 		=> trim($post['kec_tempatidentitas']),
				'kel_id_identitas' 		=> trim($post['kel_tempatidentitas']),
				'nama_lengkap_ayah' 	=> trim($post['nama_ayah']),
				'nama_lengkap_ibu' 		=> trim($post['nama_ibu']),
				'nama_lengkap_pasangan'	=> trim($post['nama_pasangan']),
				'hp_pasangan' 			=> trim($post['nohp_pasangan']),
				'nama_penanggung_jawab' => trim($post['nama_penanggung']),
				'hp_penanggung_jawab' 	=> trim($post['nohp_penanggung']),
				'kec_id_identitas' 		=> trim($post['kec_tempatidentitas']),
				'modified_by'			=> $_SESSION["username"],
				'modified_at' 			=> date("Y-m-d H:i:s")
			];
			
			return  $rs = $this->db->update($this->table_name, $data, [$this->primary_key => trim($post['id'])]);
		} else return null;
	}  

	// getting row data for update / detail view
	public function getRowData($id) { 
		$mTable = '(
					select 
					a.id, a.no_rekam_medis, a.nama, a.jenis_kelamin, a.agama_id, a.pendidikan, a.pekerjaan, a.status_kawin, a.no_hp, a.email, a.alamat_tinggal, a.no_tinggal, a.rt_tinggal, a.rw_tinggal, a.provinsi_id_tinggal, a.kabkota_id_tinggal, a.kec_id_tinggal, a.kel_id_tinggal, a.no_bpjs, a.jenis_identitas_id, a.no_identitias, a.attachment_bpjs, a.attachment_identitas, a.alamat_identitas, a.no_identitas, a.rt_identitas, a.rw_identitas, a.prov_id_identitas, a.kabkota_id_identitas, a.kec_id_identitas, a.kel_id_identitas, a.nama_lengkap_ayah, a.nama_lengkap_ibu, a.nama_lengkap_pasangan, a.hp_pasangan, a.nama_penanggung_jawab, a.hp_penanggung_jawab, a.tgl_lahir, a.tempat_lahir
					, if(a.jenis_kelamin="p","Perempuan","Laki-laki") as jenis_kelamin_desc, b.nama as agama, c.nama as pendidikan_desc
					, d.nama as status_kawin_desc, e.name as provinsi_tinggal, f.name as kabkota_tinggal, g.name as kec_tinggal
					, h.name as kel_tinggal, i.nama as jenis_identitas
					, ee.name as provinsi_identitas, ff.name as kabkota_identitas, gg.name as kec_identitas, hh.name as kel_identitas
					from pasien a
					left join agama b on b.id = a.agama_id
					left join pendidikan c on c.id = a.pendidikan
					left join status_kawin d on d.id = a.status_kawin
					left join provinsi e on e.id = a.provinsi_id_tinggal
					left join kabkota f on f.id = a.kabkota_id_tinggal
					left join kec g on g.id = a.kec_id_tinggal
					left join kelurahan h on h.id = a.kel_id_tinggal
					left join tipe_identitas i on i.id = a.jenis_identitas_id
					left join provinsi ee on ee.id = a.prov_id_identitas
					left join kabkota ff on ff.id = a.kabkota_id_identitas
					left join kec gg on gg.id = a.kec_id_identitas
					left join kelurahan hh on hh.id = a.kel_id_identitas)dt';
		
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
				'no_rekam_medis' 		=> $v["B"],
				'nama' 					=> $v["C"],
				'jenis_kelamin' 		=> $v["D"],
				'agama_id' 				=> $v["E"],
				'pendidikan' 			=> $v["F"],
				'pekerjaan' 			=> $v["G"],
				'status_kawin' 			=> $v["H"],
				'no_hp' 				=> $v["I"],
				'email' 				=> $v["J"],
				'alamat_tinggal'		=> $v["K"],
				'no_tinggal' 			=> $v["L"],
				'rt_tinggal' 			=> $v["M"],
				'rw_tinggal' 			=> $v["N"],
				'provinsi_id_tinggal' 	=> $v["O"],
				'kabkota_id_tinggal' 	=> $v["P"],
				'kec_id_tinggal' 		=> $v["Q"],
				'kel_id_tinggal' 		=> $v["R"],
				'no_bpjs' 				=> $v["S"],
				'jenis_identitas_id' 	=> $v["T"],
				'no_identitias' 		=> $v["U"],
				'alamat_identitas' 		=> $v["V"],
				'no_identitas' 			=> $v["W"],
				'rt_identitas' 			=> $v["X"],
				'rw_identitas' 			=> $v["Y"],
				'prov_id_identitas' 	=> $v["Z"],
				'kabkota_id_identitas' 	=> $v["AA"],
				'kec_id_identitas' 		=> $v["AB"],
				'kel_id_identitas' 		=> $v["AC"],
				'nama_lengkap_ayah' 	=> $v["AD"],
				'nama_lengkap_ibu' 		=> $v["AE"],
				'nama_lengkap_pasangan' => $v["AF"],
				'hp_pasangan' 			=> $v["AG"],
				'nama_penanggung_jawab' => $v["AH"],
				'hp_penanggung_jawab' 	=> $v["AI"],
				'tgl_lahir' 			=> $v["AJ"],
				'tempat_lahir' 			=> $v["AK"]

			];

			$rs = $this->db->insert($this->table_name, $data);
			if (!$rs) $error .=",baris ". $v["A"];
		}

		return $error;
	}


	// export data
	public function eksport_data()
	{
		$sql = '
	   			select 
					a.id, a.no_rekam_medis, a.nama, a.pekerjaan, a.no_hp, a.email, a.alamat_tinggal, a.no_tinggal, a.rt_tinggal, a.rw_tinggal, a.no_bpjs, a.no_identitias,  a.alamat_identitas, a.no_identitas, a.rt_identitas, a.rw_identitas,  a.nama_lengkap_ayah, a.nama_lengkap_ibu, a.nama_lengkap_pasangan, a.hp_pasangan, a.nama_penanggung_jawab, a.hp_penanggung_jawab, a.tgl_lahir, a.tempat_lahir
					, if(a.jenis_kelamin="p","Perempuan","Laki-laki") as jenis_kelamin, b.nama as agama, c.nama as pendidikan
					, d.nama as status_kawin, e.name as provinsi_tinggal, f.name as kabkota_tinggal, g.name as kec_tinggal
					, h.name as kel_tinggal, i.nama as jenis_identitas
					, ee.name as provinsi_identitas, ff.name as kabkota_identitas, gg.name as kec_identitas, hh.name as kel_identitas
					from pasien a
					left join agama b on b.id = a.agama_id
					left join pendidikan c on c.id = a.pendidikan
					left join status_kawin d on d.id = a.status_kawin
					left join provinsi e on e.id = a.provinsi_id_tinggal
					left join kabkota f on f.id = a.kabkota_id_tinggal
					left join kec g on g.id = a.kec_id_tinggal
					left join kelurahan h on h.id = a.kel_id_tinggal
					left join tipe_identitas i on i.id = a.jenis_identitas_id
					left join provinsi ee on ee.id = a.prov_id_identitas
					left join kabkota ff on ff.id = a.kabkota_id_identitas
					left join kec gg on gg.id = a.kec_id_identitas
					left join kelurahan hh on hh.id = a.kel_id_identitas
					order by a.id asc
		';

		$res = $this->db->query($sql);
		$rs = $res->result_array();
		return $rs;
	}

	
}
