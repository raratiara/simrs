<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Data_pca_model extends MY_Model
{
	/* Module */
 	protected $folder_name			= "sales/data_pca";
 	protected $table_name 			= _PREFIX_TABLE."data_rab";
 	protected $table_item 			= _PREFIX_TABLE."data_rab_detail";
 	protected $table_project	 	= _PREFIX_TABLE."data_project";
 	protected $table_po		 		= _PREFIX_TABLE."data_po";
 	protected $table_spk		 	= _PREFIX_TABLE."data_spk";
 	protected $table_wbs 			= _PREFIX_TABLE."option_wbs";
 	protected $primary_key 			= "id";
 	protected $project_key 			= "id_project";
	/* upload */
 	protected $attachment_folder	= "./uploads/data/pca";
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
			'b.project',
			'b.title',
			'a.total_budget as wbudget'
		];

		$sIndexColumn = 'a.'.$this->primary_key;
		$sTable = ' '.$this->table_name.' a
					LEFT JOIN '.$this->table_project.' b ON b.id=a.id_project
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
				$row->project,
				$row->title,
				number_format($row->wbudget,0,",",".")
			));
		}

		echo json_encode($output);
	}

	// filltering null value from array
	public function is_not_null($val){
		return !is_null($val);
	}		

	// delete item action
	// TODO: Need fixing delete file function
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
		$id_project = (isset($post['id_project']) && !empty($post['id_project']))? trim($post['id_project']):0;
		$data = [
			'id_project' 		=> $id_project,
			'total_budget' 		=> str_replace(",",".",str_replace(".","",trim($post['total_budget']))),
			'insert_by'			=> $_SESSION["username"]
		];

		//$this->db->trans_off(); // Disable transaction
		$this->db->trans_start(); // set "True" for query will be rolled back
		$this->db->insert($this->table_name, $data);
		$lastId = $this->db->insert_id();
		// Add rab data
		$rab =[];
		// cek item rab
		if(isset($post['id_wbs'])){
			$item_num = count($post['id_wbs']); // cek sum
			$item_len_min = min(array_keys($post['id_wbs'])); // cek min key index
			$item_len = max(array_keys($post['id_wbs'])); // cek max key index
		} else {
			$item_num = 0;
		}
		
		if($item_num>0){
			for($i=$item_len_min;$i<=$item_len;$i++) 
			{
				if(isset($post['description'][$i])){
					$itemData = [
						'wbs' 	=> (isset($post['id_wbs'][$i]) && !empty($post['id_wbs'][$i]))? trim($post['id_wbs'][$i]):0,
						'description' => trim($post['description'][$i]),
						'budget' => str_replace(",",".",str_replace(".","",trim($post['budget'][$i])))
						];
					$rab[] = $itemData;
				}
			}
		}
		$this->db->update($this->table_name, ['rab' => json_encode($rab)], [$this->primary_key => $lastId]);

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

		return $rs = $this->db->trans_status();
	}  

	// update data
	public function edit_data($post) { 
		if(!empty($post['id'])){
			$uid = trim($post['id']);
			$files = $this->db->select('document')->where([$this->primary_key => $uid])->get($this->table_name)->row();
			$fdoc = json_decode($files->document);
			$id_project = (isset($post['id_project']) && !empty($post['id_project']))? trim($post['id_project']):0;
			$data = [
				'id_project' 		=> $id_project,
				'total_budget' 		=> str_replace(",",".",str_replace(".","",trim($post['total_budget']))),
				'update_by'			=> $_SESSION["username"]
			];

			//$this->db->trans_off(); // Disable transaction
			$this->db->trans_start(); // set "True" for query will be rolled back
			$this->db->update($this->table_name, $data, [$this->primary_key => $uid]);
			// update rab data
			$rab =[];
			// cek item rab
			if(isset($post['id_wbs'])){
				$item_num = count($post['id_wbs']); // cek sum
				$item_len_min = min(array_keys($post['id_wbs'])); // cek min key index
				$item_len = max(array_keys($post['id_wbs'])); // cek max key index
			} else {
				$item_num = 0;
			}
			
			if($item_num>0){
				for($i=$item_len_min;$i<=$item_len;$i++) 
				{
					if(isset($post['description'][$i])){
						$itemData = [
							'wbs' 	=> (isset($post['id_wbs'][$i]) && !empty($post['id_wbs'][$i]))? trim($post['id_wbs'][$i]):0,
							'description' => trim($post['description'][$i]),
							'budget' => str_replace(",",".",str_replace(".","",trim($post['budget'][$i])))
							];
						$rab[] = $itemData;
					}
				}
			}
			$this->db->update($this->table_name, ['rab' => json_encode($rab)], [$this->primary_key => $uid]);

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

			return $rs = $this->db->trans_status();			
		} else return null;
	}  

	// getting row data for update / detail view
	public function getRowData($id) { 
		$rs = $this->db->where([$this->primary_key => $id])->get($this->table_name)->row();
		if(!empty($rs->id_project)){
			$rd = $this->getProjectInfo($rs->id_project);
			$rs = (object) array_merge((array) $rs, (array) $rd);
			$rd = $this->getPoRowsInfo($rs->id_project);
			$rs = (object) array_merge((array) $rs, $rd);
		} else {
			$rs = (object) array_merge((array) $rs, ['project'=>'','title'=>'','nilai_proyek'=>'','total_worth'=>'0']);
		}
		unset($rs->date_insert);
		unset($rs->insert_by);
		unset($rs->date_update);
		unset($rs->update_by);
		
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

			$id_project = (isset($v["B"]) && !empty($v["B"]))? trim($v["B"]):0;
			$data = [
				'id_project' 		=> $id_project,
				'total_budget' 		=> trim($v["C"]),
				'insert_by'			=> $_SESSION["username"]
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
		$sql = "SELECT
					a.id,
					b.project,
					b.title,
					format(a.total_budget,0,'id_ID') as wbudget
			FROM
	   	 		".$this->table_name." a 
			LEFT JOIN 
				".$this->table_project." b ON a.id_project = b.id 
	   		ORDER BY
	   			".$this->primary_key." ASC
		";

		$res = $this->db->query($sql);
		$rs = $res->result_array();
		return $rs;
	}

	//============================== For Additional Method ==============================//
	// Get project info
	public function getProjectInfo($id)
	{ 
		$rs = $this->db->select('a.project,a.title,if(b.worth IS NULL,0,b.worth) as nilai_proyek')->join($this->table_spk.' b','b.id = a.id_spk','left')->where(['a.'.$this->primary_key => $id])->get($this->table_project.' a')->row();
		//$rd = $this->getPoRowsInfo($id);
		//$rs = (object) array_merge((array) $rs, $rd);

		return $rs;
	} 

	// Get po info by project
	public function getPoRowsInfo($id)
	{ 
		$data = [];
		$total_worth = 0;
		$rs = $this->db->select('po, description, worth as nilaipo, format(worth,0,"id_ID") as wpo, DATE_FORMAT(FROM_UNIXTIME(date_po), "%d-%m-%Y") as dpo')->where($this->project_key." = ".$id." AND id_status NOT IN ('4')")->order_by('id', 'ASC')->get($this->table_po)->result();
		foreach ($rs as $f){
			$total_worth += $f->nilaipo;
		}
		$data['total_worth'] = $total_worth;

		return $data;
	} 
	
	// Generate new rab row
	public function getNewRabRow($row,$id=0,$view=FALSE)
	{ 
		if($id > 0){
			$data = $this->getRabRows($id,$view);
		} else {
			$data = '';
			$no = $row+1;
			$oWbs 	= $this->db->select('id,code,description')->order_by('idx_seq', 'ASC')->where("active='1'")->get($this->table_wbs)->result();
			$data 	.= '<td>'.$no.'</td>';
			$data 	.= '<td>'.$this->return_build_chosenme($oWbs,'','','','id_wbs['.$row.']','','id_wbs','','id','code','-','description','',' meta:index="'.$row.'"').'</td>';
			$data 	.= '<td>'.$this->return_build_txtarea('','description['.$row.']','','','description','','data-id="'.$row.'"').'</td>';
			$data 	.= '<td>'.$this->return_build_txt(number_format(0,0,',','.'),'budget['.$row.']','','budget','text-align: right;','data-id="'.$row.'"').'</td>';
			$data 	.= '<td><input type="button" class="ibtnDel btn btn-md btn-danger "  value="Delete"></td>';
		}

		return $data;
	} 
	
	// Generate member rows for edit & view
	public function getRabRows($id,$view,$print=FALSE){
		$dt = '';
		$rs = $this->db->select('rab')->where([$this->primary_key => $id])->get($this->table_name)->row();
		$rd = json_decode($rs->rab);

		$row = 0;
		if(!empty($rd)){
			$rs_num = count($rd);
			$oWbs 	= $this->db->select('id,code,description')->order_by('idx_seq', 'ASC')->where("active='1'")->get($this->table_wbs)->result();
			if($view){
				$arrWbs = json_decode(json_encode($oWbs), true);
				$arrW = [];
				foreach($arrWbs as $ai){
					$arrW[$ai['id']] = $ai;
				}
			}
			foreach ($rd as $f){
				$no = $row+1;
				if(!$view){
					$dt .= '<tr>';
					$dt .= '<td>'.$no.'</td>';
					$dt .= '<td>'.$this->return_build_chosenme($oWbs,'',$f->wbs,'','id_wbs['.$row.']','','id_wbs','','id','code','-','description','',' meta:index="'.$row.'"').'</td>';
					$dt .= '<td>'.$this->return_build_txtarea($f->description,'description['.$row.']','','','description','','data-id="'.$row.'"').'</td>';
					$dt .= '<td>'.$this->return_build_txt(number_format($f->budget,0,',','.'),'budget['.$row.']','','budget','text-align: right;','data-id="'.$row.'"').'</td>';
					$dt .= '<td><input type="button" class="ibtnDel btn btn-md btn-danger "  value="Delete"></td>';
					$dt .= '</tr>';
				} else {
					$ai = $arrW[$f->wbs];
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
					$dt .= '<td>'.$ai['code'].'</td>';
					$dt .= '<td>'.$f->description.'</td>';
					$dt .= '<td style="text-align: right;">'.number_format($f->budget,0,',','.').'</td>';
					$dt .= '</tr>';
				}

				$row++;
			}
		}

		return [$dt,$row];
	}
	
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
			$link = '<a href="/uploads/data/pca/'.$i.'/'.$f.'" class="'.$holder.'" target="_blank">'.$f.'</a>';
		}
		
		return $link;
	}

	// Removing file while using edit form
	public function rmAttc($id,$file){
		$this->remove_file($id,$file);
	}
}
