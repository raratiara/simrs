<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dokter_menu_model extends MY_Model
{
	/* Module */
 	protected $folder_name				= "setup/dokter_menu";
 	protected $table_name 				= _PREFIX_TABLE."dokter";
 	protected $primary_key 				= "id";

 	/* upload */
 	protected $attachment_folder	= "./uploads/dokter";
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
				$fieldname = 'foto';
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


	// fix
	public function get_list_data()
	{
		$aColumns = [
			NULL,
			NULL,
			'dt.id',
			'dt.poli',
			'dt.spesialis',
			'dt.nama',
			'dt.date_join'
		];
		

		$sIndexColumn = $this->primary_key;
		/*$sTable = ' '.$this->table_name;*/

		$sTable = '(SELECT a.id, a.poli_id, a.spesialis, a.nama, a.foto, a.date_join, b.nama as poli FROM dokter a left join poli b on b.id = a.poli_id)dt';
		

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
				$row->poli,
				$row->spesialis,
				$row->nama,
				$row->date_join

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

		$upload = $this->upload_file('1', 'foto', FALSE, '', TRUE, '');
		$foto = '';
		if($upload['status']){
			$foto = $upload['upload_file'];
		} else if(isset($upload['error_warning'])){
			echo $upload['error_warning']; exit;
		}

		$data = [
			'poli_id' 		=> trim($post['poli']),
			'spesialis' 	=> trim($post['spesialis']),
			'nama' 			=> trim($post['nama']),
			'foto' 			=> $foto,
			'date_join' 	=> trim($post['tgl_join']),
			'created_by'	=> $_SESSION["username"]
		];
		
		

		$rs = $this->db->insert($this->table_name, $data);
		$lastId = $this->db->insert_id();

		

		if(isset($post['hari'])){
			$item_num = count($post['hari']); // cek sum
			$item_len_min = min(array_keys($post['hari'])); // cek min key index
			$item_len = max(array_keys($post['hari'])); // cek max key index
		} else {
			$item_num = 0;
		}
		if($item_num>0){
			for($i=$item_len_min;$i<=$item_len;$i++) 
			{
				if(isset($post['hari'][$i])){
					$itemData = [
								'dokter_id' 	=> $lastId,
								'hari_id' 		=> trim($post['hari'][$i]),
								'jam' 			=> trim($post['jam'][$i])
								];
					$this->db->insert('jadwal_dokter', $itemData);
				}
			}
		}

		
		return $rs;
	}  

	public function edit_data($post) { 
		if(!empty($post['id'])){

			$upload = $this->upload_file('1', 'foto', FALSE, '', TRUE, '');
			$foto = '';
			if($upload['status']){
				$foto = $upload['upload_file'];
			} else if(isset($upload['error_warning'])){
				echo $upload['error_warning']; exit;
			}

		
			$data = [
				'poli_id' 		=> trim($post['poli']),
				'spesialis' 	=> trim($post['spesialis']),
				'nama' 			=> trim($post['nama']),
				'foto' 			=> $foto,
				'date_join' 	=> trim($post['tgl_join'])
			];


			if(isset($post['hari'])){
				$item_num = count($post['hari']); // cek sum
				$item_len_min = min(array_keys($post['hari'])); // cek min key index
				$item_len = max(array_keys($post['hari'])); // cek max key index
			} else {
				$item_num = 0;
			}
			if($item_num>0){
				for($i=$item_len_min;$i<=$item_len;$i++) 
				{
					if(isset($post['hari'][$i])){
						
						if($post['hdnid'][$i] != ''){
							$uid = $post['hdnid'][$i];
							$itemData = [
								'hari_id' 		=> trim($post['hari'][$i]),
								'jam' 			=> trim($post['jam'][$i])
								];

							$this->db->update('jadwal_dokter', $itemData, [$this->primary_key => $uid]);
						}else{ 
							$itemData = [
								'dokter_id' 	=> $post['id'],
								'hari_id' 		=> trim($post['hari'][$i]),
								'jam' 			=> trim($post['jam'][$i])
								];

							$this->db->insert('jadwal_dokter', $itemData);

						}
						
						
					}
				}
			}


			return  $rs = $this->db->update($this->table_name, $data, [$this->primary_key => trim($post['id'])]);
		} else return null;
	}  

	public function getRowData($id) { 
		$mTable = '(SELECT a.id, a.poli_id, a.spesialis, a.nama, a.foto, a.date_join, b.nama as poli FROM dokter a left join poli b on b.id = a.poli_id)dt';
		
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
				'poli_id' 	=> $v["B"],
				'spesialis'	=> $v["C"],
				'nama' 		=> $v["D"],
				'foto' 		=> $v["E"],
				'date_join' => $v["F"]
				
			];

			$rs = $this->db->insert($this->table_name, $data);
			if (!$rs) $error .=",baris ". $v["A"];
		}

		return $error;
	}

	public function eksport_data()
	{
		$sql = "select b.nama as poli, a.spesialis, a.nama, a.foto, a.date_join FROM dokter a
				left join poli b on b.id = a.poli_id
	   			ORDER BY id ASC
		";

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
			$msHari = $this->db->query("select * from mshari")->result(); 
			
			$data 	.= '<td>'.$no.'<input type="hidden" id="hdnid'.$row.'" name="hdnid['.$row.']" value=""/></td>';
			$data 	.= '<td>'.$this->return_build_chosenme($msHari,'','','','hari['.$row.']','','hari','','id','nama','','','',' data-id="'.$row.'" ').'</td>';
			$data 	.= '<td>'.$this->return_build_txt('','jam['.$row.']','','jam','text-align: right;','data-id="'.$row.'" ').'</td>';

			$hdnid='';
			$data 	.= '<td><input type="button" class="ibtnDel btn btn-md btn-danger " onclick="del(\''.$row.'\',\''.$hdnid.'\')" value="Delete"></td>';
		}

		return $data;
	} 
	
	// Generate expenses item rows for edit & view
	public function getExpensesRows($id,$view,$print=FALSE){ 
		$dt = ''; 
		
		$rs = $this->db->query("select a.*, b.nama as hari FROM jadwal_dokter a left join mshari b on b.id = a.hari_id where a.dokter_id = '".$id."' ")->result(); 
		$rd = $rs;

		$row = 0; 
		if(!empty($rd)){ 
			$rs_num = count($rd); 
			$tbl_obat = 'obat'; 
			$msHari = $this->db->query("select * from mshari")->result(); 
			
			foreach ($rd as $f){
				$no = $row+1;
				if(!$view){ 
					$dt .= '<tr>';
					$dt .= '<td>'.$no.'<input type="hidden" id="hdnid'.$row.'" name="hdnid['.$row.']" value="'.$f->id.'"/></td>';
					$dt .= '<td>'.$this->return_build_chosenme($msHari,'',isset($f->hari_id)?$f->hari_id:1,'','hari['.$row.']','','hari','','id','nama','','','',' data-id="'.$row.'" ').'</td>';
					
					$dt .= '<td>'.$this->return_build_txt($f->jam,'jam['.$row.']','','jam','text-align: right;','data-id="'.$row.'" ').'</td>';
					
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
					$dt .= '<td>'.$f->hari.'</td>';
					$dt .= '<td>'.$f->jam.'</td>';
					$dt .= '</tr>';
				}

				$row++;
			}
		}

		return [$dt,$row];
	}

}
