<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login_model extends CI_MODEL { 

	protected $user_tabel = _PREFIX_TABLE.'user';

	function __construct(){
		parent::__construct();
	}

	// https://stackoverflow.com/questions/3108591/calculate-number-of-hours-between-2-dates-in-php
	public function timetocurr($datetime,$frm = 'h') {
		
		if(empty($datetime)) return false;
		
		$date1 = new DateTime($datetime);
		$date2 = new DateTime();

		$diff = $date2->diff($date1);

		if($frm == 'd'){
			$time = $diff->days;
		} else {
			$hours = $diff->h;
			$time = $hours + ($diff->days*24);
		}

		return $time;
	}
	
	// get data by cookie
    public function get_by_cookie($cookie)
    {
        $this->db->where('cookie', $cookie);
        return $this->db->get($this->user_tabel);
    }

	public function hassession(){  // for check session, put on contruct
	    // ambil cookie
        $cookie = get_cookie(_COOKIES_NAME);
		// cek session
		$session = $this->session->userdata('id');
        if (!empty($session)){
			// do something if need
		} else if($cookie <> '') {
            // cek cookie
            $user = $this->get_by_cookie($cookie)->row();
            if ($user) {
				$this->session->set_userdata([
					'id' 		=> $user->user_id,
					'isaktif' 	=> TRUE,
					'username' 	=> $user->username,
					'name' 		=> $user->name,
					'role' 		=> $user->id_groups,
					'branch' 	=> $user->id_branch,
					'worker' 	=> $user->id_karyawan,
					'base_menu' => $user->base_menu,
					'ppFile' 	=> $user->ppFile
				]);
				redirect($this->uri->uri_string());
            } else {
				redirect('/login');
            }
		} else {
			redirect('/login');
		}
	}
}