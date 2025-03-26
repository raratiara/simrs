<?php
header("Access-Control-Allow-Origin: * ");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

class Home extends Api_Controller
{
	/* Module */
 	private $folder_name			= "api/home";
 	private $model_name				= "api_model";

   	public function __construct()
	{
      	parent::__construct();
		$this->load->model($this->model_name);
   	}

	// sample protecting block using JWT
    public function index()
    {
        $token = null;
        $authHeader = $this->input->get_request_header('Authorization');
        $arr = explode(" ", $authHeader);
        $token = isset($arr[1])?$arr[1]:false;
 
        if($token){
            try {
                $decoded = $this->extractJWTdata($token);       
                // Access is granted. Add code of the operation here 
                if($decoded){
                    // response true
					// Takes raw data from the request
					$json = $this->input->raw_input_stream;;
					// Converts it into a PHP object=>assoc array
					$_POST = json_decode($json,true);

                    $response = [
                        'message' => 'Access granted'
                    ];

					$this->output
					->set_status_header(200)
					->set_header('Access-Control-Allow-Origin: * ')
					->set_content_type('application/json', 'utf-8')
					->set_output(json_encode($response));
                }
            } catch (Throwable $t){
 
                $response = [
                    'message' => 'Access denied',
                    "error" => $t->getMessage()
                ];

				$this->output
				->set_status_header(401)
				->set_content_type('application/json', 'utf-8')
				->set_output(json_encode($response));
            }
        } else {
				$this->output
				->set_status_header(400);
		}
	}
}