<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends API_Controller
{
	/* Module */
 	//private $model_name				= "api_model";

   	public function __construct()
	{
      	parent::__construct();

		//$this->load->model($this->model_name);
   	}

    public function index()
    {
        $response = [
            'message' => 'Access denied',
            "error" => 'Not allowed root access.'
            ];

		$this->render_json($response, 400);
		exit;
	}
	
	// register basic example
    public function register()
    {
		$valid_elem = ['first_name', 'last_name', 'email', 'password'];
		$params = elements($valid_elem, $this->mParams);
		if(!values_satisfied($params)){
			$firstname  = $params['first_name'];
			$lastname   = $params['last_name'];
			$email      = $params['email'];
			$password   = $params['password'];
		 
			$password_hash = password_hash($password, PASSWORD_BCRYPT);
		 
			$dataRegister = [
				'first_name' => $firstname,
				'last_name' => $lastname,
				'email' => $email,
				'password' => $password_hash
			];
		 
			$register = $this->api->register($dataRegister);
		 
			if($register == true){
				$response = [
					'status' => 201, // Created
					'message' => 'Registration Successful'
				];
			} else {
				$response = [
					'status' => 503, // Service Unavailable
					'message' => 'Registration Fail'
				];
			}
		} else {
			$response = [
				'status' => 400, // Bad Request
				'message' => 'Failed',
				'error' => 'Require not satisfied'
			];
		}
		
		$this->output->set_header('Access-Control-Allow-Origin: *');
		$this->output->set_header('Access-Control-Allow-Methods: POST');
		$this->output->set_header('Access-Control-Max-Age: 3600');
		$this->output->set_header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');
		$this->render_json($response, $response['status']);
    }
 
	// login basic example
    public function login()
    {
		$valid_elem = ['email', 'password'];
		$params = elements($valid_elem, $this->mParams);
		if(!values_satisfied($params)){
			$email      = $params['email'];
			$password   = $params['password'];

			$cek_login = $this->api->cek_login($email);	 
			if(password_verify($password, isset($cek_login['password'])?$cek_login['password']:''))
			{
				$data = array(
						"id" => $cek_login['id'],
						"firstname" => $cek_login['first_name'],
						"lastname" => $cek_login['last_name'],
						"email" => $cek_login['email']
						);
	 
				$token = $this->genJWTdata($data);	 
				$response = [
					'status' => 200,
					'message' => 'Success',
					"token" => $token[0],
					"expire" => $token[1],
					"email" => $email
				];
			} else {
				$response = [
					'status' => 401,
					'message' => 'Failed',
					'error' => 'Access credentials not match.'
				];
			}
		} else {
			$response = [
				'status' => 400, // Bad Request
				'message' => 'Failed',
				'error' => 'Require not satisfied'
			];
		}
		
		$this->output->set_header('Access-Control-Allow-Origin: *');
		$this->output->set_header('Access-Control-Allow-Methods: POST');
		$this->output->set_header('Access-Control-Max-Age: 3600');
		$this->output->set_header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');
		$this->render_json($response, $response['status']);
    }
}
