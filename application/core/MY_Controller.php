<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

// controller without save & print ability
class MY_Controller extends CI_Controller {
	
    public function __construct() {
        parent::__construct();
		$this->load->model("login/login_model","auth");
		$this->auth->hassession();

		$this->data	= [];		
		$this->data['breadcrumb'] = '<li>
								<i class="fa fa-home"></i>
								<a href="'._URL.'">Home</a>
								<i class="fa fa-circle"></i>
							</li>';
		if(!empty($this->label_parent_modul)) $this->data['breadcrumb'] .= '<li><span>'.ucwords(str_replace('_',' ',$this->label_parent_modul)).'</span><i class="fa fa-circle"></i></li>';
		if(isset($this->label_subparent_modul) && !empty($this->label_subparent_modul)) $this->data['breadcrumb'] .= '<li><span>'.$this->label_subparent_modul.'</span><i class="fa fa-circle"></i></li>';
		if(isset($this->label_subparentitem_modul) && !empty($this->label_subparentitem_modul)) $this->data['breadcrumb'] .= '<li><span>'.$this->label_subparentitem_modul.'</span><i class="fa fa-circle"></i></li>';
		$this->data['breadcrumb'] .= '<li><span>'.$this->label_modul.'</span></li>';

		$menu["parent_menu"] 		= $this->parent_menu;
		if(isset($this->subparent_menu)){
			$menu["subparent_menu"] 	= $this->subparent_menu;
		}
		if(isset($this->subparentitem_menu)){
			$menu["subparentitem_menu"] = $this->subparentitem_menu;
		}
		$menu["sub_menu"] 			= $this->sub_menu;
      	$this->data["folder_name"]	= $this->folder_name;
		$this->data['check_menu']	= $menu;

		$this->load->model($this->model_name,'self_model');
    }

	// Index
	public function index()
	{
		if(_USER_ACCESS_LEVEL_VIEW == "1") {
			$this->view();
		} else {
			$this->load->view('errors/html/error_hacks_401');
		}
	}

	// View Main Module Page
	public function view()
	{
		if(_USER_ACCESS_LEVEL_VIEW == "1") {		
			// general load
			$this->data['icon'] 		= $this->icon;
			$this->data['thData'] 		= $this->tabel_header;
			$this->data['title'] 		= $this->label_list_data;
			$this->data['js'] 			= $this->folder_name.'/js_view';
			$this->data['css'] 			= $this->folder_name.'/css_view';
			$this->data['sview'] 		= $this->folder_name.'/view';
			$this->data['sfolder'] 		= $this->folder_name;
			$this->data['smodul'] 		= $this->label_modul;
			// merge with form field asset
			$this->data = array_merge($this->data, $this->form_field_asset());

			$this->load->view(_TEMPLATE , $this->data);
		} else {
			$this->load->view('errors/html/error_hacks_401');
		}
	}

	// Get List Data
	public function get_data()
	{
		if(_USER_ACCESS_LEVEL_VIEW == "1") {
			$this->self_model->get_list_data();
		} else {
			$this->load->view('errors/html/error_hacks_401');
		}
	}

	// Get Row Data
	public function get_detail_data()
	{
		if(_USER_ACCESS_LEVEL_VIEW == "1") {
			$data = FALSE;
			if ($post = $this->input->post(null, true))
			{
				$data = $this->self_model->getRowData($post['id']);
			}

			echo json_encode($data);
		} else {
			$this->load->view('errors/html/error_hacks_401');
		}
	}

	// Delete Data
	public function delete()
	{
		if(_USER_ACCESS_LEVEL_VIEW == "1" && _USER_ACCESS_LEVEL_DELETE == "1") {
			if ($post = $this->input->post(null, true))
			{
				$rs = $this->self_model->delete($post['id']);

				if ($rs) {
					$data['msg'] = $this->label_sukses_dihapus;
					$data['status'] = TRUE;
				} else {
					$data['msg'] = $this->label_gagal_dihapus;
					$data['status'] = FALSE;
				}
			} else {
				$data['msg'] = $this->label_gagal_eksekusi;
				$data['status'] = FALSE;
			}

			echo json_encode($data);
		} else {
			$this->load->view('errors/html/error_hacks_401');
		}
	}

	// Bulk Delete Data
	public function bulk()
	{
		if(_USER_ACCESS_LEVEL_VIEW == "1" && _USER_ACCESS_LEVEL_DELETE == "1") {
			if ($post = $this->input->post(null, true))
			{
				$rs = $this->self_model->bulk($post['ids']);

				if ($rs['status']) {
					$data['msg'] = $this->label_sukses_dihapus;
					$data['status'] = TRUE;
				} else {
					$data['msg'] = $this->label_gagal_dihapus.$rs['err'];
					$data['status'] = FALSE;
				}
			} else {
				$data['msg'] = $this->label_gagal_eksekusi;
				$data['status'] = FALSE;
			}

			echo json_encode($data);
		} else {
			$this->load->view('errors/html/error_hacks_401');
		}
	}

	// Add Data
	public function add()
	{
		if(_USER_ACCESS_LEVEL_VIEW == "1" && _USER_ACCESS_LEVEL_ADD == "1") {
			if ($post = $this->input->post(null, true))
			{
				$rs = $this->self_model->add_data($post);

				if ($rs) {
					$data['msg'] = $this->label_sukses_disimpan;
					$data['status'] = TRUE;
				} else {
					$data['msg'] = $this->label_gagal_disimpan;
					$data['status'] = FALSE;
				}
			} else {
				$data['msg'] = $this->label_gagal_eksekusi;
				$data['status'] = FALSE;
			}

			echo json_encode($data);
		} else {
			$this->load->view('errors/html/error_hacks_401');
		}
	}

	// Update Data
	public function edit()
	{
		if(_USER_ACCESS_LEVEL_VIEW == "1" && _USER_ACCESS_LEVEL_UPDATE == "1") {
			if ($post = $this->input->post(null, true))
			{
				$rs = $this->self_model->edit_data($post);

				if ($rs) {
					$data['msg'] = $this->label_sukses_disimpan;
					$data['status'] = TRUE;
				} else {
					$data['msg'] = $this->label_gagal_disimpan;
					$data['status'] = FALSE;
				}
			} else {
				$data['msg'] = $this->label_gagal_eksekusi;
				$data['status'] = FALSE;
			}

			echo json_encode($data);
		} else {
			$this->load->view('errors/html/error_hacks_401');
		}
	}

	// Import Data
	public function import()
	{
		if(_USER_ACCESS_LEVEL_VIEW == "1" && _USER_ACCESS_LEVEL_IMPORT == "1") {
			$config['upload_path'] = './tmp';
			$config['allowed_types'] = 'xls';

			$data = array();
			$this->load->library('upload', $config);

			if (!$this->upload->do_upload("userfile")) {
				$data['msg'] = $this->upload->display_errors();
				$data['status'] = FALSE;
			} else {
				$upload_data = array('upload_data' => $this->upload->data());
				$this->load->library('PHPExcel/IOFactory');
				$this->load->library('PHPExcel');

				$objPHPExcel = new PHPExcel_Reader_Excel5();

				$objFile = $objPHPExcel->load($upload_data["upload_data"]["full_path"]);

				$objWorksheet = $objFile->setActiveSheetIndex(0);
				$tmp_iterate = 0;
				foreach ($objWorksheet->getRowIterator() as $row) {
					$row_index = $row->getRowIndex();
					$cellIterator = $row->getCellIterator();
					$cellIterator->setIterateOnlyExistingCells(false);
					if ($row_index > 1) {
						$tmp_iterate += 1;

						foreach($cellIterator as $cell) {
							$column_index = $cell->getColumn();

							$val= trim($cell->getValue());
							if(PHPExcel_Shared_Date::isDateTime($cell)) {
								$format = "";
							}

							$tmp_data[$tmp_iterate][$column_index] =  $val;
						}
					}
				}
				unlink($upload_data["upload_data"]["full_path"]);

				# upload to database
				$rs = $this->self_model->import_data($tmp_data);

				if ($rs == "") {
					$data['msg'] = $this->label_sukses_diimport;
					$data['status'] = TRUE;
				} else {
					$data['msg'] = $this->label_gagal_diimport. $rs;
					$data['status'] = FALSE;
				}
			}

			echo json_encode($data);
		} else {
			$this->load->view('errors/html/error_hacks_401');
		}
	}

	// Export Data
	public function eksport()
	{
		if(_USER_ACCESS_LEVEL_VIEW == "1" && _USER_ACCESS_LEVEL_EKSPORT == "1") {
			$header	= "Report ".$this->label_modul."\n";
			$data 	= $this->self_model->eksport_data();
			$this->self_model->export_to_csv($this->colnames,$this->colfields, $data, $header ,"report_".$this->module_name);
		} else {
			$this->load->view('errors/html/error_hacks_401');
		}
	}
}