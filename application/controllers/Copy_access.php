<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Copy_access extends CI_Controller {

 	public $table_name 			= _PREFIX_TABLE."user_akses_role";
 	public $primary_key 		= "id";
 	public $base_role 			= "1"; // set reference role
 	public $target_role 		= "3"; // set target role
 	public $include_menu 		= [62,47,38,41]; // set id menu for copy
 	public $exclude_menu 		= []; // set id menu for excluding copy

	public function index()
	{
		$rs = $this->db->where(['role_id' => $this->base_role])->get($this->table_name)->result();
		$num = 0;
		foreach($rs as $row)
		{
			if (in_array($row->user_menu_id, $this->include_menu)){ // for only selected menu
			//if (!in_array($row->user_menu_id, $this->exclude_menu)){ // for other than exclude menu
				$rcheck = $this->db->where("role_id='".$this->target_role."' AND user_menu_id='".$row->user_menu_id."'")->get($this->table_name);
				$rnum = $rcheck->num_rows();
				if($rnum==0){
					$num++;
					$data = [
						'role_id' 		=> $this->target_role,
						'user_menu_id' 	=> $row->user_menu_id,
						'view' 			=> $row->view,
						'add' 			=> $row->add,
						'edit' 			=> $row->edit,
						'del' 			=> $row->del,
						'detail' 		=> $row->detail,
						'eksport' 		=> $row->eksport,
						'import' 		=> $row->import,
						'insert_by'		=> 'sys'
					];
					$this->db->insert($this->table_name, $data);
				}
				//if($num==2) exit;
			}
		}
		echo $num.' role '.$this->base_role.' record copied to role '.$this->target_role;
	}
}
