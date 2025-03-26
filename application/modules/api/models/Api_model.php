<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api_model extends MY_Model
{
	/* Module */
 	protected $folder_name	= "api/api";
    protected $table 		= "users";

	function __construct()
	{
		parent::__construct();
	}
 
    public function register($data)
    {
		$query = $this->db->insert($this->table, $data);

        return $query;
    }
 
    public function cek_login($email)
    {
        $query = $this->db->where('email', $email)
				->get($this->table)
                ->num_rows();
 
        if($query >  0){
            $hasil = $this->db->where('email', $email)
                    ->limit(1)
                    ->get($this->table)
                    ->row_array();
        } else {
            $hasil = array(); 
        }

        return $hasil;
    }}
