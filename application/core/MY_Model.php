<?php

class MY_Model extends CI_Model {

	/**
	 * Nama table terkait
	 * 
	 * @var string
	 */
	protected $table_name;

	/**
	 * Primary key table terkait
	 * 
	 * @var string
	 */
	protected $primary_key = 'id';

	/**
	 * List nama field yang boleh diisi
	 * 
	 * @var array
	 */
	protected $fillable = [];

	/**
	 * Nama table item
	 * Dipakai kalau bentuknya master detail misal : sales quotation, sales order, sales delivery, sales invoice
	 * 
	 * @var string
	 */
	protected $item_table_name = '';

	/**
	 * Nama field foreign key di table item
	 * 
	 * @var string
	 */
	protected $foreign_key_item = '';

	protected $user_menu_tabel 	= _PREFIX_TABLE.'user_menu';
	protected $user_akses_tabel 	= _PREFIX_TABLE.'user_akses';
	protected $user_akses_role_tabel = _PREFIX_TABLE.'user_akses_role';
	
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Insert data. return inserted data on success
	 * 
	 * @param array $data
	 * @return mixed 
	 */
	public function insert($data)
	{
		$status = $this->db->insert(
			$this->table_name, 
			elements($this->fillable, $data)
		);

		return $status ? $this->find($this->db->insert_id()) : false;
	}

	/**
	 * Get all record from table
	 * 
	 * @return array Array object 
	 */
	public function all()
	{
		return $this->db->get($this->table_name)->result();
	}

	/**
	 * Find data based on primary key
	 * 
	 * @param integer $primary_key
	 * @return mixed
	 */
	public function find($primary_key)
	{
		$row = $this->db->get_where(
			$this->table_name, 
			[$this->primary_key => $primary_key], 
			1
		);

		return $row ? $row->row() : false;
	}

	/**
	 * Update data based on primary key
	 * 
	 * @param integer $primary_key
	 * @param array $data
	 * @return mixed
	 */
	public function update($primary_key, $data)
	{
		return $this->db->update(
			$this->table_name, 
			elements($this->fillable, $data), 
			[$this->primary_key => $primary_key]
		);
	}
	
	/**
	 * Delete row based on primary key
	 * 
	 * @param integer $primary_key
	 * @return boolean
	 */
	public function delete($primary_key)
	{
		return $this->db->delete(
			$this->table_name, 
			[$this->primary_key => $primary_key]
		);
	}

	/**
	 * Delete single item. Untuk method deleteItem di controller
	 * 
	 * @param integer $id primary key
	 * @return boolean status
	 */
	public function deleteItem($id)
	{
		return $this->db->delete(
			$this->item_table_name,
			['id' => $id]
		);
	}

	/**
	 * Delete all items based on parent id. Untuk method delete di controller
	 * 
	 * @param integer $id ID parent
	 * @return boolean
	 */
	public function deleteItems($id)
	{
		return $this->db->delete(
			$this->item_table_name,
			[$this->foreign_key_item => $id]
		);
	}

	/**
	 * Get items based on id
	 * 
	 * @param integer $id id parent
	 * @return array Object array of items
	 */
	public function getItems($id)
	{
		return $this->db->get_where(
			$this->item_table_name,
			[$this->foreign_key_item => $id]
		)->result();
	}

	/**
	 * Insert single item
	 * 
	 * @param integer $id ID parent
	 * @param array $data Data item
	 * @return boolean
	 */
	public function insertItem($id, $data)
	{
		if (!is_array($data)) {
			return false;
		}

		$data[$this->foreign_key_item] = $id;
		return $this->db->insert($this->item_table_name, $data);
	}

	/**
	 * Update single item
	 * 
	 * @param integer $id ID item
	 * @param array $data New data
	 * @return boolean
	 */
	public function updateItem($id, $data)
	{
		if (!is_array($data)) {
			return false;
		}

		return $this->db->update($this->item_table_name, $data, ['id' => $id]);
	}

	/**
	 * Insert items banyak sekaligus. Untuk method store di controller
	 * 
	 * @param integer $id Id parent
	 * @param array $data Array of item array
	 * @return boolean
	 */
	public function insertBatchItems($id, $data)
	{
		// pastikan ada datanya
		if (!is_array($data) || count($data) == 0) {
			return false;
		}

		return $this->db->insert_batch(
			$this->item_table_name,
			array_map(function($d)  use ($id) {
				$d[$this->foreign_key_item] = $id; // tambahin id parent di array
				$d['insert_by'] = $_SESSION['name'];
				return $d; // array baru dengan tambahan id parent
			}, $data)
		);
	}

	/**
	 * Update items if exists and create new if not. Untuk method update di controller
	 * 
	 * @param integer $id ID parent
	 * @param array $items Array of item array
	 * @return boolean 
	 */
	public function updateOrInsertBatchItems($id, $items)
	{
		$new = array_filter($items, function($item) {
			return !isset($item['id']);
		});

		// insert yang baru
		if (count($new) > 0) {
			$this->insertBatchItems($id, $new);
		}

		$exists = array_filter($items, function($item) {
			return isset($item['id']);
		});

		// update batch biar 1 query
		if (count($exists) > 0) 
		{
			$this->db->update_batch(
				// nama table
				$this->item_table_name,
				// tambahin field update_by
				array_map(function($d)  {
					$d['update_by'] = $_SESSION['name'];
					return $d;
				}, $exists),
				// key untuk update
				'id'
			);
		}

		return true;
	}

	/**
	 * Fungsi untuk cek data digunakan di table lain
	 * Dipakai untuk validasi sebelum data master dihapus
	 * 
	 * @param array $params array key table
	 * [
	 * 		['field' => '{field}', 'table' => '{table_name}', 'value' => '{value}'],
	 * 		...
	 * ]
	 * 
	 * @return boolean
	 */
	public function checkUsedInTable($params)
	{
		// loop ke semua table sampai
		foreach ($params as $param) 
		{
			$row = $this->db->select($param['field'], ' AS total')
					->where($param['field'], $param['value'])
					->get($param['table'])
					->row();
					
			if ($row->total > 0) {
				return true;
			}
		}

		return 0;
	}

	public function import($data, $uniqueField)
	{
		$this->db->trans_start();
		
		foreach ($data['rows'] as $row) 
		{
			$sql = "SELECT COUNT($uniqueField) AS total FROM $this->table_name WHERE $uniqueField = ? LIMIT 1";
			$exists = $this->db->query($sql, [$row[$uniqueField]])->row();

			if ($exists->total > 0) {
				$this->db->update($this->table_name, $row, [$uniqueField => $row[$uniqueField]]);
			} else {
				$this->db->insert($this->table_name, $row);
			}
		}

		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	public function return_show_selectbox($table_name, $key, $val, $var_name, $option, $label)
	{ 
		$data = "";
		$sql = "SELECT * FROM ".$table_name ." WHERE ".$key." = ". $val; 
		$res = $this->db->query($sql);
		$rs = $res->result_array();
		if (count($rs) > 0)  {
			$data .= "<select name=\"".$var_name."\" id=\"".$var_name."\" class=\"form-control select2_sample1\">";
			FOREACH ($rs AS $r) {
				$data .= "<option value=\"".$r[$option]."\">".$r[$label]."</option>";
			}
			$data .= "</select>";
		}
		return $data;
	}
		
	public function export_to_excel($colnames, $colfields, $data, $header="",$file="export", $footer ="")
	{ 
		$string_to_export = "";
		if ($header <> "") $string_to_export .= $header."\n\n";
		
		foreach ($colnames AS $k=>$v) {
			$string_to_export .= $v. "\t";
		} 
		$string_to_export .= "\n";

		foreach ($data AS $key => $value)
		{  
			foreach ($colfields AS $k=>$v) {
				$string_to_export .= $this->_trim_export_string($value[$v])."\t"; 
			}
			$string_to_export .= "\n";
		} 
		if ($footer <> "") $string_to_export .= "\n\n".$footer."\n\n";

		// Convert to UTF-16LE and Prepend BOM
		$string_to_export = "\xFF\xFE" .mb_convert_encoding($string_to_export, 'UTF-16LE', 'UTF-8');

		$filename = $file."_".date("Y-m-d_H:i:s").".xls";

		header('Content-type: application/vnd.ms-excel;charset=UTF-16LE');
		header('Content-Disposition: attachment; filename='.$filename);
		header("Cache-Control: no-cache");
		echo $string_to_export;
		die();
	}
	
	public function export_to_csv($colnames, $colfields, $data, $header="",$file="export", $footer ="")
	{ 
		$array = [];
		$array[] = $colnames;
		foreach ($data AS $key => $value)
		{  
			$o = [];
			foreach ($colfields AS $k=>$v) {
				$o[] = $this->_trim_export_string($value[$v]); 
			}
			$array[] = $o;
		} 

		$filename = $file."_".date("Y-m-d_H:i:s").".csv";
		header('Content-Disposition: attachment; filename='.$filename);
		header("Pragma: no-cache");
		header("Expires: 0");
		$out = fopen("php://output", 'w');
		foreach ($array as $data)
		{
			fputcsv($out, $data,"\t");
		}
		fclose($out);
		die();
	}
	
	public function _trim_export_string($value)
	{
		$value = str_replace(array("&nbsp;","&amp;","&gt;","&lt;"),array(" ","&",">","<"),$value);
		return  strip_tags(str_replace(array("\t","\n","\r"),"",$value));
	}

	public function _trim_print_string($value)
	{
		$value = str_replace(array("&nbsp;","&amp;","&gt;","&lt;"),array(" ","&",">","<"),$value);

		//If the value has only spaces and nothing more then add the whitespace html character
		if(str_replace(" ","",$value) == "")
			$value = "&nbsp;";

		return strip_tags($value);
	}

	function escape_str($value)
	{
		return $this->db->escape_str($value);
	} 

	public function user_akses($var="") 
	{ 
		if ($var <> "" && $_SESSION["id"] <> "" && $_SESSION["role"] <> "" && $_SESSION["base_menu"] <> "") { 
			if($_SESSION["base_menu"] == 'custom'){
				$sql = "SELECT a.* 
						FROM ".$this->user_akses_tabel." a 
						INNER JOIN ".$this->user_menu_tabel." b 
						ON a.user_menu_id = b.user_menu_id 
						WHERE a.user_id = ".$_SESSION["id"]." AND module_name = '".$var."'";
			} else {
				$sql = "SELECT a.* 
						FROM ".$this->user_akses_role_tabel." a 
						INNER JOIN ".$this->user_menu_tabel." b 
						ON a.user_menu_id = b.user_menu_id 
						WHERE a.role_id = ".$_SESSION["role"]." AND module_name = '".$var."'";
			}
			$res = $this->db->query($sql);
			$rs   = $res->row_array(); 
			return $rs;
		}
	}
	
	public function getRomanNumerals($decimalInteger) 
	{
		$n = intval($decimalInteger);
		$res = '';

		$roman_numerals = array(
			'M'  => 1000,
			'CM' => 900,
			'D'  => 500,
			'CD' => 400,
			'C'  => 100,
			'XC' => 90,
			'L'  => 50,
			'XL' => 40,
			'X'  => 10,
			'IX' => 9,
			'V'  => 5,
			'IV' => 4,
			'I'  => 1);

		foreach ($roman_numerals as $roman => $numeral) {
			$matches = intval($n / $numeral);
			$res .= str_repeat($roman, $matches);
			$n = $n % $numeral;
		}

		return $res;
	}

	public function terbilang($obj) {
		$eobj = explode('.',$obj);
		$satuan = array();
		$satuan[0] = '';
		$satuan[1] = 'ribu ';
		$satuan[2] = 'juta ';
		$satuan[3] = 'milyar ';

		$temp1 = $eobj[0];
		$temp2 = $eobj[1]; // desimal
		$temp3 = array();
		$str = '';
		if ($temp1 != '' && is_numeric($temp1)) {
			$temp1 = strrev($temp1);
			$arr_size = ceil(strlen($temp1)/3);
			$msg = '';
			for ($i=0; $i<$arr_size; $i++) {
				$temp3[$arr_size-($i+1)] = strrev(substr($temp1,$i*3,3));
			}
			for ($i = $arr_size-1; $i>=0; $i--) {
				if (($arr_size - ($i+1)) == 1) {
					if ($temp3[$i] == 1) {
						$str = 'seribu ' . $str;
					}
					else {
						if ($temp3[$i] == '000') {
							$str = $this->toString($temp3[$i]) . $str;
						} else {
							$str = $this->toString($temp3[$i]) . $satuan[$arr_size - ($i+1)] . $str;
						}

					}
				} else {
					if ($temp3[$i] == '000') {
						$str = $this->toString($temp3[$i]) . $str;
					} else {
						$str = $this->toString($temp3[$i]) . $satuan[$arr_size - ($i+1)] . $str;
					}
				}
			}

			if ($temp2 != '' && is_numeric($temp2)) {
			}
			return $str;
		}
		else {
		}
	}

	public function toString($number) 
	{
		$terbilang = [
			'', 'satu ', 'dua ', 'tiga ', 
			'empat ', 'lima ', 'enam ', 
			'tujuh ', 'delapan ', 'sembilan '
		];
		
		$str = '';
		$temp2 = '';

		for ($i=0; $i<strlen($number); $i++) {
			$temp2 = $number[$i];
			$temp3 = '';
			$pass = true;
			switch (strlen($number) - $i) {
				case 1 : 
					$temp3 = ''; 
					break;
				case 2 : 
					if ($temp2 == '1') {
						$temp3 = 'belas ';
						$i++;
						$temp2 = $number[$i];
						if ($temp2 == '0') { 
							$temp3 = 'sepuluh ';
							$pass = false;
						} else if ($temp2 == '1') {
							$temp3 = 'sebelas ';
							$pass = false;
						}
					} else if ($temp2 =='0') { 
						$temp3 = '';
					} else {
						$temp3 = 'puluh '; 
					}
					break;
				case 3 :
					if ($temp2 == '1') {
						$temp3 = 'seratus ';
						$pass = false;

					} else if ($temp2 == '0') {
						$temp3 = '';
					} else {
						$temp3 = 'ratus ';
					}
					break;
				default : $temp3 = '';
			}
			if ($pass) {
				if ($temp3 == '') $str .= $terbilang[$temp2];
				else $str .= $terbilang[$temp2] . $temp3;
			}
			else {
				$str .= $temp3;
			}
		}
		return $str;
	}

	public function getValObjArray($objList,$key,$val) 
	{
		$data = "";
		if (count($objList) > 0)  {
			foreach($objList AS $r) {
				if($r->$key==$val) return $r;
			}
		}
		
		return $data;
	}

	public function return_select_option($rs, $option, $flabel, $mlabel='', $llabel='', $set=[])
	{ 
		$data = "";
		$set_rule = FALSE;
		if(!empty($set)){
			$set_rule = TRUE;
		}
		$data .= "<option value=\"\">-- Select --</option>";
		if (count($rs) > 0)  {
			foreach($rs AS $r) {
				if($set_rule){
					if(is_array($set)){
						if(in_array($r->$option, $set)) {
							$data .= "<option value=\"".$r->$option."\">".$r->$flabel;
							if(!empty($mlabel)){
								$data .= " ".$mlabel." ";
							}
							if(!empty($llabel)){
								$data .= $r->$llabel;
							}
							$data .= "</option>";
						}
					} else {
						if($r->$option==$set){
							$data .= "<option value=\"".$r->$option."\">".$r->$flabel;
							if(!empty($mlabel)){
								$data .= " ".$mlabel." ";
							}
							if(!empty($llabel)){
								$data .= $r->$llabel;
							}
							$data .= "</option>";
						}
					}
				} else {
					$select = "";
					$data .= "<option value=\"".$r->$option."\">".$r->$flabel;
					if(!empty($mlabel)){
						$data .= " ".$mlabel." ";
					}
					if(!empty($llabel)){
						$data .= $r->$llabel;
					}
					$data .= "</option>";
				}
			}
		}

		return $data;
	}
	
	public function date_to_int($dt){
		$rs = NULL;
		if(!empty($dt)){
			$rs = strtotime($dt);
		}
		
		return $rs;
	}

	public function get_ppn_value($dt){
		$rs = 0.1;
		if(!empty($dt)){
			$date_check = strtotime($dt);
			$rd = $this->db->select('ppn_tarif')->where('date_effective <=', $date_check)->order_by('date_effective','desc')->get("erp_data_ppn")->row();
			//$rs = $this->db->last_query();

			if($rd){
				$rs = $rd->ppn_tarif/100;
			}
		}
		return $rs;
	}

	//BOF for metronic style form
	public function return_build_txt($val='', $var_name, $id_name='', $addclass='', $addstyle='', $attrib='')
	{ 
		$data = "";
		$idname = "";
		if(!empty($id_name)){
			$idname = " id=\"".$id_name."\"";
		}
		$style = "";
		if(!empty($addstyle)){
			$style = " style=\"".$addstyle."\"";
		}
		$data .= "<input class=\"form-control ".$addclass."\"".$style." name=\"".$var_name."\"".$idname." type=\"text\" value=\"".$val."\" ".$attrib."/>";
		return $data;
	}

	public function return_build_txtstatic($val='')
	{ 
		$data = "<p class=\"form-control-static\"> ".$val." </p>";
		return $data;
	}

	public function return_build_txtpasswd($val='', $var_name, $id_name='', $addclass='', $addstyle='', $attrib='')
	{ 
		$data = "";
		$idname = "";
		if(!empty($id_name)){
			$idname = " id=\"".$id_name."\"";
		}
		$style = "";
		if(!empty($addstyle)){
			$style = " style=\"".$addstyle."\"";
		}
			$data .= "<input class=\"form-control ".$addclass."\"".$style." name=\"".$var_name."\"".$idname." type=\"password\" value=\"".$val."\" ".$attrib."/>";
		return $data;
	}

	public function return_build_txthidden($val='', $var_name, $id_name='', $attrib='')
	{ 
		$data = "";
		$idname = "";
		if(!empty($id_name)){
			$idname = " id=\"".$id_name."\"";
		}
			$data .= "<input class=\"form-control\" name=\"".$var_name."\"".$idname." type=\"hidden\" value=\"".$val."\" ".$attrib."/>";
		return $data;
	}

	public function return_build_txttime($val='', $var_name, $id_name='', $addclass='', $addstyle='', $attrib='')
	{ 
		$data = "";
		$idname = "";
		if(!empty($id_name)){
			$idname = " id=\"".$id_name."\"";
		}
		$style = "";
		if(!empty($addstyle)){
			$style = " style=\"".$addstyle."\"";
		}
		$data .= "<input class=\"form-control ".$addclass."\"".$style." name=\"".$var_name."\"".$idname." type=\"time\" value=\"".$val."\" ".$attrib."/>";
		return $data;
	}

	public function return_build_txtdate($val='', $var_name, $id_name='', $addclass='', $addstyle='')
	{ 
		$data = "";
		$idname = "";
		if(!empty($id_name)){
			$idname = " id=\"".$id_name."\"";
		}
		$style = "";
		if(!empty($addstyle)){
			$style = " style=\"".$addstyle."\"";
		}
			$data .= "<div class=\"input-group date date-picker\" data-date-format=\"dd-mm-yyyy\">
					<input type=\"text\" class=\"form-control ".$addclass."\"".$style." name=\"".$var_name."\"".$idname." type=\"text\" value=\"".$val."\" >
					<span class=\"input-group-btn picker\">
						<button class=\"btn default\" type=\"button\">
							<i class=\"fa fa-calendar\"></i>
						</button>
					</span>
				</div>";
		return $data;
	}
	
	public function return_build_txtemail($val='', $var_name, $id_name='', $addclass='', $addstyle='', $attrib='')
	{ 
		$data = "";
		$idname = "";
		if(!empty($id_name)){
			$idname = " id=\"".$id_name."\"";
		}
		$style = "";
		if(!empty($addstyle)){
			$style = " style=\"".$addstyle."\"";
		}
			$data .= "<div class=\"input-group\">
										<span class=\"input-group-addon\">
												<i class=\"fa fa-envelope\"></i>
										</span>
										<input type=\"email\" class=\"form-control ".$addclass."\"".$style." name=\"".$var_name."\"".$idname." value=\"".$val."\" placeholder=\"Email Address\" ".$attrib.">
				</div>";
		return $data;
	}

	public function return_build_fileinput($var_name, $id_name='', $holder='', $addclass='', $addstyle='', $attrib='multiple=""')
	{ 
		$data = "";
		$idname = "";
		if(!empty($id_name)){
			$idname = " id=\"".$id_name."\"";
		}
		$style = "";
		if(!empty($addstyle)){
			$style = " style=\"".$addstyle."\"";
		}
		$data .= "<input class=\"form-control ".$addclass."\"".$style." name=\"".$var_name."\"".$idname." type=\"file\" ".$attrib."/>";
		if(!empty($holder)){
			$data .= "<span class=\"".$holder."\"></span>";
		}

		return $data;
	}

	public function return_build_txtarea($val='', $var_name, $id_name='', $rows='4', $addclass='', $addstyle='', $attrib='')
	{ 
		$data = "";
		$idname = "";
		if(!empty($id_name)){
			$idname = " id=\"".$id_name."\"";
		}
		$style = "";
		if(!empty($addstyle)){
			$style = " style=\"".$addstyle."\"";
		}
		$data .= "<textarea class=\"form-control ".$addclass."\"".$style." name=\"".$var_name."\"".$idname." rows=\"".$rows."\" ".$attrib.">".$val."</textarea>";
		return $data;
	}

	public function return_build_select($rs, $multi='', $val=[], $set=[], $var_name, $id_name='', $addclass='', $addstyle='', $option, $flabel, $mlabel='', $llabel='', $disabled='', $attrib='',$padln='0',$padstr='',$padtype=STR_PAD_RIGHT)
	{ 
		$data = "";
		$idname = "";
		if(!empty($id_name)){
			$idname = " id=\"".$id_name."\"";
		}
		$style = "";
		if(!empty($addstyle)){
			$style = " style=\"".$addstyle."\"";
		}
		$multiple = "";
		if(!empty($multi) && $multi=='multiple'){
			$multiple = " multiple";
		}
		$set_rule = FALSE;
		if(!empty($set)){
			$set_rule = TRUE;
		}
		$multi_llabel = FALSE;
		$multi_llabel_num = 0;
		if(!empty($llabel)){
			$arr_llabel = explode(',',$llabel);
			$multi_llabel_num = count($arr_llabel);
			if($multi_llabel_num>1){
				$multi_llabel = TRUE;
			}
		}

		$data .= "<select".$multiple." name=\"".$var_name."\"".$idname." class=\"form-control ".$addclass."\"".$style." ".$disabled." ".$attrib.">";
		$data .= "<option value=\"\">-- Select --</option>";
		if (count($rs) > 0)  {
			foreach($rs AS $r) {
				if($set_rule){
					if(is_array($set)){
						if(in_array($r->$option, $set)) {
							$select = "";
							if(!empty($val)){
								if(is_array($val)){
									if(in_array($r->$option, $val)){
										$select = " selected";
									}
								} else {
									if($r->$option == $val){
										$select = " selected";
									}
								}
							}
							$data .= "<option value=\"".$r->$option."\"".$select.">".str_pad($r->$flabel, $padln, $padstr, $padtype);
							if(!empty($mlabel)){
								$data .= " ".$mlabel." ";
							}
							if(!empty($llabel)){
								if($multi_llabel){
									$alc = 0;
									foreach($arr_llabel as $al){
										if($alc>0){
											$data .= " ".$mlabel." ";
										}
										$data .= $r->$al;
										$alc++;
									}
								} else {
									$data .= $r->$llabel;
								}
							}
							$data .= "</option>";
						}
					} else {
						if($r->$option==$set){
							$select = "";
							if(!empty($val)){
								if(is_array($val)){
									if(in_array($r->$option, $val)){
										$select = " selected";
									}
								} else {
									if($r->$option == $val){
										$select = " selected";
									}
								}
							}
							$data .= "<option value=\"".$r->$option."\"".$select.">".str_pad($r->$flabel, $padln, $padstr, $padtype);
							if(!empty($mlabel)){
								$data .= " ".$mlabel." ";
							}
							if(!empty($llabel)){
								if($multi_llabel){
									$alc = 0;
									foreach($arr_llabel as $al){
										if($alc>0){
											$data .= " ".$mlabel." ";
										}
										$data .= $r->$al;
										$alc++;
									}
								} else {
									$data .= $r->$llabel;
								}
							}
							$data .= "</option>";
						}
					}
				} else {
					$select = "";
					if(!empty($val)){
						if(is_array($val)){
							if(in_array($r->$option, $val)){
								$select = " selected";
							}
						} else {
							if($r->$option == $val){
								$select = " selected";
							}
						}
					}
					$data .= "<option value=\"".$r->$option."\"".$select.">".str_pad($r->$flabel, $padln, $padstr, $padtype);
					if(!empty($mlabel)){
						$data .= " ".$mlabel." ";
					}
					if(!empty($llabel)){
						if($multi_llabel){
							$alc = 0;
							foreach($arr_llabel as $al){
								if($alc>0){
									$data .= " ".$mlabel." ";
								}
								$data .= $r->$al;
								$alc++;
							}
						} else {
							$data .= $r->$llabel;
						}
					}
					$data .= "</option>";
				}
			}
		}
		$data .= "</select>";

		return $data;
	}

	public function return_build_select2me($rs, $multi='', $val=[], $set=[], $var_name, $id_name='', $addclass='', $addstyle='', $option, $flabel, $mlabel='', $llabel='', $disabled='', $attrib='',$padln='0',$padstr='',$padtype=STR_PAD_RIGHT)
	{ 
		$data = "";
		$idname = "";
		if(!empty($id_name)){
			$idname = " id=\"".$id_name."\"";
		}
		$style = "";
		if(!empty($addstyle)){
			$style = " style=\"".$addstyle."\"";
		}
		$multiple = "";
		if(!empty($multi) && $multi=='multiple'){
			$multiple = " multiple";
		}
		$set_rule = FALSE;
		if(!empty($set)){
			$set_rule = TRUE;
		}
		$multi_llabel = FALSE;
		$multi_llabel_num = 0;
		if(!empty($llabel)){
			$arr_llabel = explode(',',$llabel);
			$multi_llabel_num = count($arr_llabel);
			if($multi_llabel_num>1){
				$multi_llabel = TRUE;
			}
		}

		$data .= "<select".$multiple." name=\"".$var_name."\"".$idname." class=\"form-control select2me ".$addclass."\"".$style." ".$disabled." ".$attrib.">";
		if(empty($multi) || $multi!=='multiple'){
			$data .= "<option value=\"\">-- Select --</option>";
		}
		if (count($rs) > 0)  {
			foreach($rs AS $r) {
				if($set_rule){
					if(is_array($set)){
						if(in_array($r->$option, $set)) {
							$select = "";
							if(!empty($val)){
								if(is_array($val)){
									if(in_array($r->$option, $val)){
										$select = " selected";
									}
								} else {
									if($r->$option == $val){
										$select = " selected";
									}
								}
							}
							$data .= "<option value=\"".$r->$option."\"".$select.">".str_pad($r->$flabel, $padln, $padstr, $padtype);
							if(!empty($mlabel)){
								$data .= " ".$mlabel." ";
							}
							if(!empty($llabel)){
								if($multi_llabel){
									$alc = 0;
									foreach($arr_llabel as $al){
										if($alc>0){
											$data .= " ".$mlabel." ";
										}
										$data .= $r->$al;
										$alc++;
									}
								} else {
									$data .= $r->$llabel;
								}
							}
							$data .= "</option>";
						}
					} else {
						if($r->$option==$set){
							$select = "";
							if(!empty($val)){
								if(is_array($val)){
									if(in_array($r->$option, $val)){
										$select = " selected";
									}
								} else {
									if($r->$option == $val){
										$select = " selected";
									}
								}
							}
							$data .= "<option value=\"".$r->$option."\"".$select.">".str_pad($r->$flabel, $padln, $padstr, $padtype);
							if(!empty($mlabel)){
								$data .= " ".$mlabel." ";
							}
							if(!empty($llabel)){
								if($multi_llabel){
									$alc = 0;
									foreach($arr_llabel as $al){
										if($alc>0){
											$data .= " ".$mlabel." ";
										}
										$data .= $r->$al;
										$alc++;
									}
								} else {
									$data .= $r->$llabel;
								}
							}
							$data .= "</option>";
						}
					}
				} else {
					$select = "";
					if(!empty($val)){
						if(is_array($val)){
							if(in_array($r->$option, $val)){
								$select = " selected";
							}
						} else {
							if($r->$option == $val){
								$select = " selected";
							}
						}
					}
					$data .= "<option value=\"".$r->$option."\"".$select.">".str_pad($r->$flabel, $padln, $padstr, $padtype);
					if(!empty($mlabel)){
						$data .= " ".$mlabel." ";
					}
					if(!empty($llabel)){
						if($multi_llabel){
							$alc = 0;
							foreach($arr_llabel as $al){
								if($alc>0){
									$data .= " ".$mlabel." ";
								}
								$data .= $r->$al;
								$alc++;
							}
						} else {
							$data .= $r->$llabel;
						}
					}
					$data .= "</option>";
				}
			}
		}
		$data .= "</select>";

		return $data;
	}

	public function return_build_chosenme($rs, $multi='', $val=[], $set=[], $var_name, $id_name='', $addclass='', $addstyle='', $option, $flabel, $mlabel='', $llabel='', $disabled='', $attrib='',$padln='0',$padstr='',$padtype=STR_PAD_RIGHT)
	{ 
		$data = "";
		$idname = "";
		if(!empty($id_name)){
			$idname = " id=\"".$id_name."\"";
		}
		$style = "";
		if(!empty($addstyle)){
			$style = " style=\"".$addstyle."\"";
		}
		$multiple = "";
		if(!empty($multi) && $multi=='multiple'){
			$multiple = " multiple";
		}
		$set_rule = FALSE;
		if(!empty($set)){
			$set_rule = TRUE;
		}
		$multi_llabel = FALSE;
		$multi_llabel_num = 0;
		if(!empty($llabel)){
			$arr_llabel = explode(',',$llabel);
			$multi_llabel_num = count($arr_llabel);
			if($multi_llabel_num>1){
				$multi_llabel = TRUE;
			}
		}

		$data .= "<select".$multiple." name=\"".$var_name."\"".$idname." class=\"form-control chosenme ".$addclass."\"".$style." ".$disabled." ".$attrib.">";
		$data .= "<option value=\"\"></option>";
		if (count($rs) > 0)  {
			foreach($rs AS $r) {
				if($set_rule){
					if(is_array($set)){
						if(in_array($r->$option, $set)) {
							$select = "";
							if(!empty($val)){
								if(is_array($val)){
									if(in_array($r->$option, $val)){
										$select = " selected";
									}
								} else {
									if($r->$option == $val){
										$select = " selected";
									}
								}
							}
							$data .= "<option value=\"".$r->$option."\"".$select.">".str_pad($r->$flabel, $padln, $padstr, $padtype);
							if(!empty($mlabel)){
								$data .= " ".$mlabel." ";
							}
							if(!empty($llabel)){
								if($multi_llabel){
									$alc = 0;
									foreach($arr_llabel as $al){
										if($alc>0){
											$data .= " ".$mlabel." ";
										}
										$data .= $r->$al;
										$alc++;
									}
								} else {
									$data .= $r->$llabel;
								}
							}
							$data .= "</option>";
						}
					} else {
						if($r->$option==$set){
							$select = "";
							if(!empty($val)){
								if(is_array($val)){
									if(in_array($r->$option, $val)){
										$select = " selected";
									}
								} else {
									if($r->$option == $val){
										$select = " selected";
									}
								}
							}
							$data .= "<option value=\"".$r->$option."\"".$select.">".str_pad($r->$flabel, $padln, $padstr, $padtype);
							if(!empty($mlabel)){
								$data .= " ".$mlabel." ";
							}
							if(!empty($llabel)){
								if($multi_llabel){
									$alc = 0;
									foreach($arr_llabel as $al){
										if($alc>0){
											$data .= " ".$mlabel." ";
										}
										$data .= $r->$al;
										$alc++;
									}
								} else {
									$data .= $r->$llabel;
								}
							}
							$data .= "</option>";
						}
					}
				} else {
					$select = "";
					if(!empty($val)){
						if(is_array($val)){
							if(in_array($r->$option, $val)){
								$select = " selected";
							}
						} else {
							if($r->$option == $val){
								$select = " selected";
							}
						}
					}
					$data .= "<option value=\"".$r->$option."\"".$select.">".str_pad($r->$flabel, $padln, $padstr, $padtype);
					if(!empty($mlabel)){
						$data .= " ".$mlabel." ";
					}
					if(!empty($llabel)){
						if($multi_llabel){
							$alc = 0;
							foreach($arr_llabel as $al){
								if($alc>0){
									$data .= " ".$mlabel." ";
								}
								$data .= $r->$al;
								$alc++;
							}
						} else {
							$data .= $r->$llabel;
						}
					}
					$data .= "</option>";
				}
			}
		}
		$data .= "</select>";

		return $data;
	}

	public function return_build_simple_select($rs, $multi='', $val=[], $var_name, $id_name='', $addclass='', $addstyle='', $disabled='', $attrib='',$padln='0',$padstr='',$padtype=STR_PAD_RIGHT)
	{ 
		$data = "";
		$idname = "";
		if(!empty($id_name)){
			$idname = " id=\"".$id_name."\"";
		}
		$style = "";
		if(!empty($addstyle)){
			$style = " style=\"".$addstyle."\"";
		}
		$multiple = "";
		if(!empty($multi) && $multi=='multiple'){
			$multiple = " multiple";
		}

		$data .= "<select".$multiple." name=\"".$var_name."\"".$idname." class=\"form-control ".$addclass."\"".$style." ".$disabled." ".$attrib.">";
		$data .= "<option value=\"\">-- Select --</option>";
		if (!empty($rs))  {
			foreach($rs AS $k=>$v) {
				$select = "";
				if(!empty($val)){
					if(is_array($val)){
						if(in_array($v, $val)){
							$select = " selected";
						}
					} else {
						if($v == $val){
							$select = " selected";
						}
					}
				}
				$data .= "<option value=\"".$v."\"".$select.">".str_pad($v, $padln, $padstr, $padtype)."</option>";
			}
		}
		$data .= "</select>";

		return $data;
	}

	public function return_build_simple_select2me($rs, $multi='', $val=[], $var_name, $id_name='', $addclass='', $addstyle='', $disabled='', $attrib='',$padln='0',$padstr='',$padtype=STR_PAD_RIGHT)
	{ 
		$data = "";
		$idname = "";
		if(!empty($id_name)){
			$idname = " id=\"".$id_name."\"";
		}
		$style = "";
		if(!empty($addstyle)){
			$style = " style=\"".$addstyle."\"";
		}
		$multiple = "";
		if(!empty($multi) && $multi=='multiple'){
			$multiple = " multiple";
		}

		$data .= "<select".$multiple." name=\"".$var_name."\"".$idname." class=\"form-control select2me ".$addclass."\"".$style." ".$disabled." ".$attrib.">";
		$data .= "<option value=\"\">-- Select --</option>";
		if (!empty($rs))  {
			foreach($rs AS $k=>$v) {
				$select = "";
				if(!empty($val)){
					if(is_array($val)){
						if(in_array($v, $val)){
							$select = " selected";
						}
					} else {
						if($v == $val){
							$select = " selected";
						}
					}
				}
				$data .= "<option value=\"".$v."\"".$select.">".str_pad($v, $padln, $padstr, $padtype)."</option>";
			}
		}
		$data .= "</select>";

		return $data;
	}

	//$set in array with array value [key,value,optional-attrib]
	//sample set value = [[1,'Yes','disabled'],[0,'No']]
	public function return_build_radio($val='', $set='', $var_name, $id_name='', $orientation = '', $addclass='', $addstyle='')
	{ 
		$data = "";
		if(!empty($set)){
			$idname = "";
			if(!empty($id_name)){
				$idname = " id=\"".$id_name."\"";
			}
			$layout = "";
			if(!empty($orientation) && $orientation=='inline'){
				$layout = " class=\"radio-inline\"";
			}
			$style = "";
			if(!empty($addstyle)){
				$style = " style=\"".$addstyle."\"";
			}
			$data .= "<div class=\"radio-list\">";
			if(is_array($set)){
				foreach($set as $k=>$v){
					$checked = "";
					if(!empty($val) || $val=='0'){
						if($v[0] == $val){
							$checked = " checked";
						}
					}
					$attrib = "";
					if(!empty($v[2])){
						$attrib = " ".$v[2];
					}
					$data .= "<label".$layout."><input type=\"radio\" name=\"".$var_name."\"".$idname." value=\"".$v[0]."\"".$checked.$attrib." class=\"".$addclass."\"".$style."> ".$v[1]." </label>";
				}
			}
			$data .= "</div>";
		}
		return $data;
	}
	
	public function return_build_checkbox($val=[], $set='', $var_name, $id_name='', $orientation = '', $addclass='', $addstyle='')
	{ 
		$data = "";
		if(!empty($set)){
			$idname = "";
			if(!empty($id_name)){
				$idname = " id=\"".$id_name."\"";
			}
			$layout = "";
			if(!empty($orientation) && $orientation=='inline'){
				$layout = " class=\"checkbox-inline\"";
			}
			$style = "";
			if(!empty($addstyle)){
				$style = " style=\"".$addstyle."\"";
			}
			$data .= "<div class=\"checkbox-list\">";
			if(is_array($set)){
				foreach($set as $k=>$v){
					$checked = "";
					if(!empty($val) || $val=='0'){
						if(is_array($val)){
							if(in_array($v[0], $val)){
								$checked = " checked";
							}
						} else {
							if($v[0] == $val){
								$checked = " checked";
							}
						}
					}
					$attrib = "";
					if(!empty($v[2])){
						$attrib = " ".$v[2];
					}
					$data .= "<label".$layout."><input type=\"checkbox\" name=\"".$var_name."\"".$idname." value=\"".$v[0]."\"".$checked.$attrib." class=\"".$addclass."\"".$style."> ".$v[1]." </label>";
				}
			}
			$data .= "</div>";
		}
		return $data;
	}

	public function return_build_radioselect($rs, $val=[], $set=[], $var_name, $id_name='', $orientation = '', $option, $flabel, $mlabel='', $llabel='', $disabled=FALSE, $addclass='', $addstyle='')
	{ 
		$data = "";
		$dataset = [];

		$set_rule = FALSE;
		if(!empty($set)){
			$set_rule = TRUE;
		}

		$disstat = "";
		if($disabled){
			$disstat = "disabled";
		}

		$multi_llabel = FALSE;
		$multi_llabel_num = 0;
		if(!empty($llabel)){
			$arr_llabel = explode(',',$llabel);
			$multi_llabel_num = count($arr_llabel);
			if($multi_llabel_num>1){
				$multi_llabel = TRUE;
			}
		}

		if (count($rs) > 0)  {
			foreach($rs AS $r) {
				if($set_rule){
					if(is_array($set)){
						if(in_array($r->$option, $set)) {
							$label = "";
							$label .= $r->$flabel;
							if(!empty($mlabel)){
								$label .= " ".$mlabel." ";
							}
							if(!empty($llabel)){
								if($multi_llabel){
									$alc = 0;
									foreach($arr_llabel as $al){
										if($alc>0){
											$label .= " ".$mlabel." ";
										}
										$label .= $r->$al;
										$alc++;
									}
								} else {
									$label .= $r->$llabel;
								}
							}
							$dataset[] = [$r->$option,$label,$disstat];
						}
					} else {
						if($r->$option==$set){
							$label = "";
							$label .= $r->$flabel;
							if(!empty($mlabel)){
								$label .= " ".$mlabel." ";
							}
							if(!empty($llabel)){
								if($multi_llabel){
									$alc = 0;
									foreach($arr_llabel as $al){
										if($alc>0){
											$label .= " ".$mlabel." ";
										}
										$label .= $r->$al;
										$alc++;
									}
								} else {
									$label .= $r->$llabel;
								}
							}
							$dataset[] = [$r->$option,$label,$disstat];
						}
					}
				} else {
					$label = "";
					$label .= $r->$flabel;
					if(!empty($mlabel)){
						$label .= " ".$mlabel." ";
					}
					if(!empty($llabel)){
						if($multi_llabel){
							$alc = 0;
							foreach($arr_llabel as $al){
								if($alc>0){
									$label .= " ".$mlabel." ";
								}
								$label .= $r->$al;
								$alc++;
							}
						} else {
							$label .= $r->$llabel;
						}
					}
					$dataset[] = [$r->$option,$label,$disstat];
				}
			}
		}

		return $this->return_build_radio($val, $dataset, $var_name, $id_name, $orientation, $addclass, $addstyle);
	}

	public function return_build_checkboxselect($rs, $val=[], $set=[], $var_name, $id_name='', $orientation = '', $option, $flabel, $mlabel='', $llabel='', $disabled=FALSE, $addclass='', $addstyle='')
	{ 
		$data = "";
		$dataset = [];

		$set_rule = FALSE;
		if(!empty($set)){
			$set_rule = TRUE;
		}

		$disstat = "";
		if($disabled){
			$disstat = "disabled";
		}

		$multi_llabel = FALSE;
		$multi_llabel_num = 0;
		if(!empty($llabel)){
			$arr_llabel = explode(',',$llabel);
			$multi_llabel_num = count($arr_llabel);
			if($multi_llabel_num>1){
				$multi_llabel = TRUE;
			}
		}

		if (count($rs) > 0)  {
			foreach($rs AS $r) {
				if($set_rule){
					if(is_array($set)){
						if(in_array($r->$option, $set)) {
							$label = "";
							$label .= $r->$flabel;
							if(!empty($mlabel)){
								$label .= " ".$mlabel." ";
							}
							if(!empty($llabel)){
								if($multi_llabel){
									$alc = 0;
									foreach($arr_llabel as $al){
										if($alc>0){
											$label .= " ".$mlabel." ";
										}
										$label .= $r->$al;
										$alc++;
									}
								} else {
									$label .= $r->$llabel;
								}
							}
							$dataset[] = [$r->$option,$label,$disstat];
						}
					} else {
						if($r->$option==$set){
							$label = "";
							$label .= $r->$flabel;
							if(!empty($mlabel)){
								$label .= " ".$mlabel." ";
							}
							if(!empty($llabel)){
								if($multi_llabel){
									$alc = 0;
									foreach($arr_llabel as $al){
										if($alc>0){
											$label .= " ".$mlabel." ";
										}
										$label .= $r->$al;
										$alc++;
									}
								} else {
									$label .= $r->$llabel;
								}
							}
							$dataset[] = [$r->$option,$label,$disstat];
						}
					}
				} else {
					$label = "";
					$label .= $r->$flabel;
					if(!empty($mlabel)){
						$label .= " ".$mlabel." ";
					}
					if(!empty($llabel)){
						if($multi_llabel){
							$alc = 0;
							foreach($arr_llabel as $al){
								if($alc>0){
									$label .= " ".$mlabel." ";
								}
								$label .= $r->$al;
								$alc++;
							}
						} else {
							$label .= $r->$llabel;
						}
					}
					$dataset[] = [$r->$option,$label,$disstat];
				}
			}
		}

		return $this->return_build_checkbox($val, $dataset, $var_name, $id_name, $orientation, $addclass, $addstyle);
	}

	public function return_build_filesetselect($rs, $require=[], $set=[], $var_name, $id_name='', $option, $flabel, $mlabel='', $llabel='')
	{ 
		$data = '<div style="display:block;padding-left:15px;padding-right:15px;">';

		$set_rule = FALSE;
		if(!empty($set)){
			$set_rule = TRUE;
		}

		$multi_llabel = FALSE;
		$multi_llabel_num = 0;
		if(!empty($llabel)){
			$arr_llabel = explode(',',$llabel);
			$multi_llabel_num = count($arr_llabel);
			if($multi_llabel_num>1){
				$multi_llabel = TRUE;
			}
		}

		if (count($rs) > 0)  {
			foreach($rs AS $r) {
				$varname = $var_name."_".str_replace(" ","_",$r->$option);
				$idname = "";
				if(!empty($id_name)){
					$idname = $id_name."_".str_replace(" ","_",$r->$option);
				}

				if($set_rule){
					if(is_array($set)){
						if(in_array($r->$option, $set)) {
							$select = "";
							if(!empty($require)){
								if(is_array($require)){
									if(in_array($r->$option, $require)){
										$select = ' <span class="required">*</span>';
									}
								} else {
									if($r->$option == $require){
										$select = ' <span class="required">*</span>';
									}
								}
							}

							$label = "";
							$label .= $r->$flabel;
							if(!empty($mlabel)){
								$label .= " ".$mlabel." ";
							}
							if(!empty($llabel)){
								if($multi_llabel){
									$alc = 0;
									foreach($arr_llabel as $al){
										if($alc>0){
											$label .= " ".$mlabel." ";
										}
										$label .= $r->$al;
										$alc++;
									}
								} else {
									$label .= $r->$llabel;
								}
							}
					
							$data .= '<div class="form-group">
										<label>'.$label.$select.'</label>';
							$data .= $this->return_build_fileinput($varname, $idname, $varname, '', '', '');
							$data .= '</div>';
						}
					} else {
						if($r->$option==$set){
							$select = "";
							if(!empty($require)){
								if(is_array($require)){
									if(in_array($r->$option, $require)){
										$select = ' <span class="required">*</span>';
									}
								} else {
									if($r->$option == $require){
										$select = ' <span class="required">*</span>';
									}
								}
							}

							$label = "";
							$label .= $r->$flabel;
							if(!empty($mlabel)){
								$label .= " ".$mlabel." ";
							}
							if(!empty($llabel)){
								if($multi_llabel){
									$alc = 0;
									foreach($arr_llabel as $al){
										if($alc>0){
											$label .= " ".$mlabel." ";
										}
										$label .= $r->$al;
										$alc++;
									}
								} else {
									$label .= $r->$llabel;
								}
							}
					
							$data .= '<div class="form-group">
										<label>'.$label.$select.'</label>';
							$data .= $this->return_build_fileinput($varname, $idname, $varname, '', '', '');
							$data .= '</div>';
						}
					}
				} else {
					$select = "";
					if(!empty($require)){
						if(is_array($require)){
							if(in_array($r->$option, $require)){
								$select = ' <span class="required">*</span>';
							}
						} else {
							if($r->$option == $require){
								$select = ' <span class="required">*</span>';
							}
						}
					}

					$label = "";
					$label .= $r->$flabel;
					if(!empty($mlabel)){
						$label .= " ".$mlabel." ";
					}
					if(!empty($llabel)){
						if($multi_llabel){
							$alc = 0;
							foreach($arr_llabel as $al){
								if($alc>0){
									$label .= " ".$mlabel." ";
								}
								$label .= $r->$al;
								$alc++;
							}
						} else {
							$label .= $r->$llabel;
						}
					}
					
					$data .= '<div class="form-group">
								<label>'.$label.$select.'</label>';
					$data .= $this->return_build_fileinput($varname, $idname, $varname, '', '', '');
					$data .= '</div>';
				}
			}
		}
		$data .= '</div>';

		return $data;
	}
	//EOF for metronic style form
	
	public function return_build_tree($table, $key, $val, $parentcol, $showstat='', $orderbycol = '', $except = [])
	{ 
		$data = array();
		$this->db->select($key.",".$val.",".$parentcol);
		$this->db->where([$parentcol => '0']);
		if(!empty($showstat)){
			$this->db->where([$showstat => '1']);
		}
		$this->db->order_by($orderbycol.' ASC');
		$tree = $this->db->get($table)->result();
		//echo $this->db->last_query(); exit;
		//var_dump($tree); exit;
		$set_except = FALSE;

		if(!empty($except)){
			$set_except = TRUE;
		}

		foreach ($tree as $row) 
		{
			if($set_except){
				if(is_array($except)){
					if(!in_array($row->$key, $except)) {
						$data[] = [$key => $row->$key, $val => $row->$val];
					}
				} else {
					if($row->$key!==$except){
						$data[] = [$key => $row->$key, $val => $row->$val];
					}
				}
			} else {
				$data[] = [$key => $row->$key, $val => $row->$val];
			}

			$rsarr = $this->return_build_subtree($table, $key, $val, $parentcol, $showstat, $orderbycol, $row->$key,0, $except);
			if($rsarr){
				$data = array_merge($data,$rsarr);
			}
		}
		
		$arrayobject = json_decode(json_encode($data));
		
		return $arrayobject;
	}
	
	public function return_build_subtree($table, $key, $val, $parentcol, $showstat='', $orderbycol = '', $idparentcol, $lvl=0, $except=[])
	{ 
		$has_sub = FALSE;
		$data = array();
		$this->db->select($key.",".$val);
		$this->db->where([$parentcol => $idparentcol]);

		if(!empty($showstat)){
			$this->db->where([$showstat => '1']);
		}

		$this->db->order_by($orderbycol.' ASC');
		$tree = $this->db->get($table)->result();
		$deep = $lvl+1;

		$set_except = FALSE;

		if(!empty($except)) {
			$set_except = TRUE;
		}

		foreach ($tree as $row) {
			$has_sub = TRUE;

			if($set_except) 
			{
				if(is_array($except))
				{
					if(!in_array($row->$key, $except)) {
						$data[] = [$key => $row->$key, $val => str_repeat("----", $deep).$row->$val];
					}
				} else {
					if($row->$key!=$except) {
						$data[] = [$key => $row->$key, $val => str_repeat("----", $deep).$row->$val];
					}
				}
			} else {
				$data[] = [$key => $row->$key, $val => str_repeat("----", $deep).$row->$val];
			}

			$rsarr = $this->return_build_subtree($table, $key, $val, $parentcol, $showstat, $orderbycol, $row->$key, $deep, $except);
			
			if($rsarr) {
				$data = array_merge($data,$rsarr);
			}
		}
				
		return ($has_sub) ? $data : FALSE;;
	}
}