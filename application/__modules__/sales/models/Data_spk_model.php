<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Data_spk_model extends MY_Model
{
	/* Module */
 	protected $folder_name			= "sales/data_spk";
 	protected $table_name 			= _PREFIX_TABLE."data_spk";
 	protected $table_customer 		= _PREFIX_TABLE."data_customer";
 	protected $table_status 		= _PREFIX_TABLE."option_general_status";
 	protected $primary_key 			= "id";
	/* upload */
 	protected $attachment_folder	= "./uploads/data/spk";
	protected $allow_type			= "gif|jpeg|jpg|png|pdf|xls|xlsx|doc|docx|txt";
	protected $allow_size			= "0"; // 0 for limit by default php conf (in Kb)

	function __construct()
	{
		parent::__construct();
	}
	
	// Remove file
	public function remove_file($id = "", $filename= "") {
		if(!empty($id) && !empty($filename)){
			$filepath = $this->attachment_folder.'/'.$id.'/'.$filename;
			if(is_file($filepath)) {
				unlink($filepath);
			}
		}
	}
	
	// Upload file
	public function upload_file($id = "", $fieldname= "", $replace=FALSE, $oldfilename= "", $array=FALSE, $i=0) {
		$data = array();
		$data['status'] = FALSE;
		if(!empty($id) && !empty($fieldname)){
			// handling multiple upload (as array field)
			if($array){
				// Define new $_FILES array - $_FILES['file']
				$_FILES['file']['name'] = $_FILES[$fieldname]['name'][$i];
				$_FILES['file']['type'] = $_FILES[$fieldname]['type'][$i];
				$_FILES['file']['tmp_name'] = $_FILES[$fieldname]['tmp_name'][$i];
				$_FILES['file']['error'] = $_FILES[$fieldname]['error'][$i];
				$_FILES['file']['size'] = $_FILES[$fieldname]['size'][$i];
				// override field
				$fieldname = 'file';
			}
			// handling regular upload (as one field)
			if(isset($_FILES[$fieldname]) && !empty($_FILES[$fieldname]['name']))
			{
				$dir = $this->attachment_folder.'/'.$id;
				if(!is_dir($dir)) {
					mkdir($dir);
				}
				if($replace){
					$this->remove_file($id, $oldfilename);
				}
				$config['upload_path']   = $dir.'/';
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
			'a.id',
			'a.spk',
			'DATE_FORMAT(FROM_UNIXTIME(a.date_spk), "%d-%m-%Y") as dspk',
			'DATE_FORMAT(FROM_UNIXTIME(a.date_due), "%d-%m-%Y") as ddue',
			'b.name as customer',
			'a.worth as wspk'
		];

		$sIndexColumn = 'a.'.$this->primary_key;
		$sTable = ' '.$this->table_name.' a
					LEFT JOIN '.$this->table_customer.' b ON b.id=a.id_customer
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
				$row->spk,
				$row->dspk,
				$row->ddue,
				$row->customer,
				number_format($row->wspk,0,",",".")
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
			// removing attachment file
			$files = $this->db->select('document')->where([$this->primary_key => $id])->get($this->table_name)->row();
			$fdoc = json_decode($files->document);
			if(!empty($fdoc)){
				foreach($fdoc as $k=>$v){
					if(!empty($v->file)){
						$this->remove_file($id,$v->file);
					}
				}
			}

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
				// removing attachment file
				$files = $this->db->select('document')->where([$this->primary_key => $pid])->get($this->table_name)->row();
				$fdoc = json_decode($files->document);
				if(!empty($fdoc)){
					foreach($fdoc as $k=>$v){
						if(!empty($v->file)){
							$this->remove_file($pid,$v->file);
						}
					}
				}

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
		$id_customer = (isset($post['id_customer']) && !empty($post['id_customer']))? trim($post['id_customer']):NULL;
		//$id_status = (isset($post['id_status']) && !empty($post['id_status']))? trim($post['id_status']):NULL;
		$data = [
			'date_spk' 			=> $this->date_to_int(trim($post['date_spk'])),
			'date_due' 			=> $this->date_to_int(trim($post['date_due'])),
			'spk' 				=> trim($post['spk']),
			'id_customer' 		=> $id_customer,
			'description' 		=> trim($post['description']),
			'worth' 			=> str_replace(",",".",str_replace(".","",trim($post['worth']))),
			//'id_status' 		=> $id_status,
			'insert_by'			=> $_SESSION["username"]
		];

		//$this->db->trans_off(); // Disable transaction
		$this->db->trans_start(); // set "True" for query will be rolled back
		$this->db->insert($this->table_name, $data);
		$lastId = $this->db->insert_id();

		// Add attach data
		$attc =[];
		// cek item attach
		if(isset($post['attcdescription'])){
			$item_num = count($post['attcdescription']); // cek sum
			$item_len_min = min(array_keys($post['attcdescription'])); // cek min key index
			$item_len = max(array_keys($post['attcdescription'])); // cek max key index
		} else {
			$item_num = 0;
		}
		
		if($item_num>0){
			for($i=$item_len_min;$i<=$item_len;$i++) 
			{
				if(isset($post['attcdescription'][$i])){
					$itemData = [
						'description' => trim($post['attcdescription'][$i]),
						];
					$upload = $this->upload_file($lastId, 'attcfile', FALSE, '', TRUE, $i);
					$itemData['file'] = '';
					if($upload['status']){
						$itemData['file'] = $upload['upload_file'];
					} else if(isset($upload['error_warning'])){
						echo $upload['error_warning']; exit;
					}
					$attc[] = $itemData;
				}
			}
		}
		$this->db->update($this->table_name, ['document' => json_encode($attc)], [$this->primary_key => $lastId]);
		$this->db->trans_complete();

		$rs = $this->db->trans_status();

		return $rs;
	}  

	// update data
	public function edit_data($post) { 
		if(!empty($post['id'])){
			$uid = trim($post['id']);
			$files = $this->db->select('document')->where([$this->primary_key => $uid])->get($this->table_name)->row();
			$fdoc = json_decode($files->document);
			$id_customer = (isset($post['id_customer']) && !empty($post['id_customer']))? trim($post['id_customer']):NULL;
			//$id_status = (isset($post['id_status']) && !empty($post['id_status']))? trim($post['id_status']):NULL;
			$data = [
				'date_spk' 			=> $this->date_to_int(trim($post['date_spk'])),
				'date_due' 			=> $this->date_to_int(trim($post['date_due'])),
				'spk' 				=> trim($post['spk']),
				'id_customer' 		=> $id_customer,
				'description' 		=> trim($post['description']),
				'worth' 			=> str_replace(",",".",str_replace(".","",trim($post['worth']))),
				//'id_status' 		=> $id_status,
				'update_by'			=> $_SESSION["username"]
			];

			//$this->db->trans_off(); // Disable transaction
			$this->db->trans_start(); // set "True" for query will be rolled back
			$this->db->update($this->table_name, $data, [$this->primary_key => $uid]);

			// update attach data
			$attc =[];
			// cek item attach
			if(isset($post['attcdescription'])){
				$item_num = count($post['attcdescription']); // cek sum
				$item_len_min = min(array_keys($post['attcdescription'])); // cek min key index
				$item_len = max(array_keys($post['attcdescription'])); // cek max key index
			} else {
				$item_num = 0;
			}
			
			if($item_num>0){
				for($i=$item_len_min;$i<=$item_len;$i++) 
				{
					if(isset($post['attcdescription'][$i])){
						$itemData = [
							'description' => trim($post['attcdescription'][$i]),
							];

						$itemData['file'] = '';
						if(isset($fdoc[$i]->file) && !empty($fdoc[$i]->file)){
							$itemData['file'] = $fdoc[$i]->file;
						}
						$upload = $this->upload_file($uid, 'attcfile', TRUE, $itemData['file'], TRUE, $i);
						if($upload['status']){
							$itemData['file'] = $upload['upload_file'];
						} else if(isset($upload['error_warning'])){
							echo $upload['error_warning']; exit;
						}
						$attc[] = $itemData;
					}
				}
			}
			$this->db->update($this->table_name, ['document' => json_encode($attc)], [$this->primary_key => $uid]);
			$this->db->trans_complete();

			$rs = $this->db->trans_status();
			return  $rs;
		} else return null;
	}  

	// getting row data for update / detail view
	public function getRowData($id) { 
		$rs = $this->db->select('*,DATE_FORMAT(FROM_UNIXTIME(date_spk), "%d-%m-%Y") as dspk,DATE_FORMAT(FROM_UNIXTIME(date_due), "%d-%m-%Y") as ddue')->where([$this->primary_key => $id])->get($this->table_name)->row();
		if(!empty($rs->id_customer)){
			$rd = $this->db->select('name as customer')->where([$this->primary_key => $rs->id_customer])->get($this->table_customer)->row();
			$rs = (object) array_merge((array) $rs, (array) $rd);
		} else {
			$rs = (object) array_merge((array) $rs, ['customer'=>'']);
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

			$id_customer = trim($v["E"]);
			if(empty($id_customer)) $id_customer = NULL;
			$id_status = trim($v["H"]);
			if(empty($id_status)) $id_status = NULL;
			$data = [
				'spk' 				=> trim($v["B"]),
				'id_customer' 		=> $id_customer,
				'description' 		=> trim($v["F"]),
				'worth' 			=> trim($v["G"]),
				'document' 			=> [],
				//'id_status' 		=> $id_status,
				'insert_by'			=> $_SESSION["username"]
			];

			$dspk = trim($v["C"]);
			if(empty($dspk)){
					$data['date_spk'] = NULL;
			} else {
					$data['date_spk'] = strtotime($dspk);
			}

			$ddue = trim($v["D"]);
			if(empty($ddue)){
					$data['date_due'] = NULL;
			} else {
					$data['date_due'] = strtotime($ddue);
			}

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
					a.spk,
					DATE_FORMAT(FROM_UNIXTIME(a.date_spk), '%d-%m-%Y') as dspk,
					DATE_FORMAT(FROM_UNIXTIME(a.date_due), '%d-%m-%Y') as ddue,
					b.name as customer,
					a.description,
					format(a.worth,0,'id_ID') as wspk
			FROM
	   	 		".$this->table_name." a 
			LEFT JOIN 
				".$this->table_customer." b ON a.id_customer = b.id 
	   		ORDER BY
	   			".$this->primary_key." ASC
		";

		$res = $this->db->query($sql);
		$rs = $res->result_array();
		return $rs;
	}
	
	//============================== For Additional Method ==============================//
	// Generate new attachment row
	public function getNewAttcRow($row,$id=0,$view=FALSE)
	{ 
		if($id > 0){
			$data = $this->getAttcRows($id,$view);
		} else {
			$data = '';
			$no = $row+1;
			$data 	.= '<td>'.$no.'</td>';
			$data 	.= '<td>'.$this->return_build_txtarea('','attcdescription['.$row.']','','','attcdescription','','data-id="'.$row.'"').'</td>';
			$data 	.= '<td>'.$this->return_build_fileinput('attcfile['.$row.']','attcfile_'.$row, '', '', '', 'data-id="'.$row.'"').'</td>';
			$data 	.= '<td><input type="button" class="pibtnDel btn btn-md btn-danger " data-id="'.$row.'" value="Delete"></td>';
		}

		return $data;
	} 
	
	// Generate attachment rows for edit & view
	public function getAttcRows($id,$view,$print=FALSE){
		$dt = '';
		$rs = $this->db->select('document')->where([$this->primary_key => $id])->get($this->table_name)->row();
		$rd = json_decode($rs->document);

		$row = 0;
		if(!empty($rd)){
			$rs_num = count($rd);
			foreach ($rd as $f){
				$no = $row+1;
				if(!$view){
					$dt .= '<tr>';
					$dt .= '<td>'.$no.'</td>';
					$dt .= '<td>'.$this->return_build_txtarea($f->description,'attcdescription['.$row.']','','','attcdescription','','data-id="'.$row.'"').'</td>';
					$dt .= '<td>'.$this->return_build_fileinput('attcfile['.$row.']','attcfile_'.$row, '', '', '', 'data-id="'.$row.'"').'<span>'.$this->setLink($id,$f->file,'attcfile_'.$row).'</span></td>';
					$dt .= '<td><input type="button" class="pibtnDel btn btn-md btn-danger " data-id="'.$row.'" value="Delete"></td>';
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
					$dt .= '<td>'.$this->setLink($id,$f->file).'</td>';
					$dt .= '</tr>';
				}

				$row++;
			}
		}

		return [$dt,$row];
	}

	// Generate file link
	private function setLink($i,$f,$holder=''){
		$link = '';
		if($f){
			$link = '<a href="/uploads/data/spk/'.$i.'/'.$f.'" class="'.$holder.'" target="_blank">'.$f.'</a>';
		}
		
		return $link;
	}

	// Removing file while using edit form
	public function rmAttc($id,$file){
		$this->remove_file($id,$file);
	}

}
