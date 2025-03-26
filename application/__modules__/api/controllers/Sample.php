<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Sample controller
 *
 * Sample REST API controller.
 *
 */

class Sample extends API_Controller {

   	public function __construct()
	{
      	parent::__construct();

		//apply & uncomment for securing
		//all avalilable method
        $this->verify_token();
   	}

	// [GET] /sample
	protected function get_items()
	{
		//apply & uncomment for securing
		//specific method
        //$this->verify_token();
		var_dump($this->jwt_data);

		$response = array(
			array('id' => 1, 'name' => 'sample 1'),
			array('id' => 2, 'name' => 'sample 2'),
			array('id' => 3, 'name' => 'sample 3'),
		);

		$this->output->set_header('Access-Control-Allow-Origin: *');
		$this->output->set_header('Access-Control-Allow-Headers: Authorization');
		$this->output->set_header('Access-Control-Allow-Methods: GET');
		$this->render_json($response, 200);
	}

	// [GET] /sample/{id}
	protected function get_item($id)
	{
		$response = array('id' => $id, 'name' => 'sample '.$id);

		$this->output->set_header('Access-Control-Allow-Origin: *');
		$this->output->set_header('Access-Control-Allow-Headers: Authorization');
		$this->output->set_header('Access-Control-Allow-Methods: GET');
		$this->render_json($response, 200);
	}
	
	// [GET] /sample/{parent_id}/{subitem}
	protected function get_subitems($parent_id, $subitem)
	{
		$response = array(
			array('id' => 1, 'name' => 'Parent '.$parent_id.' - '.$subitem.' 1'),
			array('id' => 2, 'name' => 'Parent '.$parent_id.' - '.$subitem.' 2'),
			array('id' => 3, 'name' => 'Parent '.$parent_id.' - '.$subitem.' 3'),
		);

		$this->output->set_header('Access-Control-Allow-Origin: *');
		$this->output->set_header('Access-Control-Allow-Headers: Authorization');
		$this->output->set_header('Access-Control-Allow-Methods: GET');
		$this->render_json($response, 200);
	}

	// [POST] /sample
	protected function create_item()
	{
		$this->load->helper('array');
		$params = elements(array('filter', 'valid', 'fields', 'here'), $this->mParams);

        $response = array('message' => 'Created','Param' => $params);

		$this->output->set_header('Access-Control-Allow-Origin: *');
		$this->output->set_header('Access-Control-Allow-Methods: POST');
		$this->output->set_header('Access-Control-Max-Age: 3600');
		$this->output->set_header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');
		$this->render_json($response, 201);
	}

	// [PUT] /sample/{id}
	protected function update_item($id)
	{
		$this->load->helper('array');
		$params = elements(array('filter', 'valid', 'email', 'here'), $this->mParams);

		$response = array('message' => 'Accepted','ID' => $id,'Param' => $params);

		$this->output->set_header('Access-Control-Allow-Origin: *');
		$this->output->set_header('Access-Control-Allow-Methods: PUT');
		$this->output->set_header('Access-Control-Max-Age: 3600');
		$this->output->set_header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');
		$this->render_json($response, 202);
	}

	// [DELETE] /sample/{id}
	protected function remove_item($id)
	{

		$response = array('message' => 'Accepted','ID' => $id);

		$this->output->set_header('Access-Control-Allow-Origin: *');
		$this->output->set_header('Access-Control-Allow-Methods: DELETE');
		$this->output->set_header('Access-Control-Max-Age: 3600');
		$this->output->set_header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');
		$this->render_json($response, 202);
	}
}
