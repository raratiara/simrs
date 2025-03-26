<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Authorization_user_role extends CI_Controller
{
	/* Module */
 	private $folder_name			= "general_system/authorization_user_role";
 	private $module_name			= "general_system_authorization_user_role";
 	private $table_name 			= _PREFIX_TABLE."user_group";
 	private $primary_key 			= "id";
 	private $model_name				= "authorization_user_role_model";
	/* Navigation */
 	private $parent_menu			= "general_system";
 	private $subparent_menu			= "general_system_authorization";
 	private $sub_menu 				= "general_system_authorization_user_role";
	/* Label */
 	private $label_parent_modul		= "General System";
 	private $label_subparent_modul	= "Authorization";
 	private $label_modul			= "User Role";
 	private $label_list_data		= "Daftar Data User Role";
 	private $label_add_data			= "Tambah Data User Role";
 	private $label_update_data		= "Edit Data User Role";
 	private $label_sukses_disimpan 	= "Data berhasil disimpan";
 	private $label_gagal_disimpan 	= "Data gagal disimpan";
 	private $label_delete_data		= "Hapus Data User Role";
 	private $label_sukses_dihapus 	= "Data berhasil dihapus";
 	private $label_gagal_dihapus 	= "Data gagal dihapus";
 	private $label_detail_data		= "Detail Data User Role";
 	private $label_import_data		= "Import Data User Role";
 	private $label_sukses_diimport 	= "Data berhasil diimport";
 	private $label_gagal_diimport 	= "Import data di baris : ";
 	private $label_export_data		= "Export";
 	private $label_gagal_eksekusi 	= "Eksekusi gagal karena ketiadaan data";

   	public function __construct()
	{
      	parent::__construct();
		$this->load->model("login/login_model","auth");
		$this->auth->hassession();

		$this->data['breadcrumb'] = '<li>
								<i class="fa fa-home"></i>
								<a href="'._URL.'">Home</a>
								<i class="fa fa-circle"></i>
							</li>';
		if(!empty($this->label_parent_modul)) $this->data['breadcrumb'] .= '<li><span>'.$this->label_parent_modul.'</span><i class="fa fa-circle"></i></li>';
		if(!empty($this->label_subparent_modul)) $this->data['breadcrumb'] .= '<li><span>'.$this->label_subparent_modul.'</span><i class="fa fa-circle"></i></li>';
		$this->data['breadcrumb'] .= '<li><span>'.$this->label_modul.'</span></li>';

		$menu["parent_menu"] 		= $this->parent_menu;
		$menu["subparent_menu"] 	= $this->subparent_menu;
		$menu["sub_menu"] 			= $this->sub_menu;
      	$this->data["folder_name"]	= $this->folder_name;
		$this->data['check_menu']	= $menu;

		$this->load->model($this->model_name);

		# akses level
		$akses = $this->authorization_user_role_model->user_akses($this->module_name);
		define('_USER_ACCESS_LEVEL_VIEW',$akses["view"]);
		define('_USER_ACCESS_LEVEL_ADD',$akses["add"]);
		define('_USER_ACCESS_LEVEL_UPDATE',$akses["edit"]);
		define('_USER_ACCESS_LEVEL_DELETE',$akses["del"]);
		define('_USER_ACCESS_LEVEL_DETAIL',$akses["detail"]);
		define('_USER_ACCESS_LEVEL_IMPORT',$akses["import"]);
		define('_USER_ACCESS_LEVEL_EKSPORT',$akses["eksport"]);
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
			$this->data['icon'] 		= 'fa-database';
			$this->data['thData'] 		= ["ID","Role","Description"];
			$this->data['title'] 		= $this->label_list_data;
			$this->data['js'] 			= $this->folder_name.'/js_view';
			$this->data['sview'] 		= $this->folder_name.'/view';
			$this->data['sfolder'] 		= $this->folder_name;
			$this->data['smodul'] 		= $this->label_modul;
			// form
			$this->data['txtname'] 		= $this->authorization_user_role_model->return_build_txt('','name','name');
			$this->data['txtdesc'] 		= $this->authorization_user_role_model->return_build_txt('','description','description');
			
			$this->load->view(_TEMPLATE , $this->data);
		} else {
			$this->load->view('errors/html/error_hacks_401');
		}
	}

	// Get List Data
	public function get_data()
	{
		if(_USER_ACCESS_LEVEL_VIEW == "1") {
			$this->authorization_user_role_model->get_list_data();
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
				$data = $this->authorization_user_role_model->getRowData($post['id']);
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
				$rs = $this->authorization_user_role_model->delete($post['id']);

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
				$rs = $this->authorization_user_role_model->bulk($post['ids']);

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
				$rs = $this->authorization_user_role_model->add_data($post);

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
				$rs = $this->authorization_user_role_model->edit_data($post);

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
				$rs = $this->authorization_user_role_model->import_data($tmp_data);

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

	public function eksport()
	{
		if(_USER_ACCESS_LEVEL_VIEW == "1" && _USER_ACCESS_LEVEL_EKSPORT == "1") {
			$header	= "Report ".$this->label_modul."\n";
			$data 	= $this->authorization_user_role_model->eksport_data();

			$colnames = [
				"ID",
				"Role",
				"Description",
			];

			$colfields = [
				"id",
				"name",
				"description",
			];

			$this->authorization_user_role_model->export_to_excel($colnames,$colfields, $data, $header ,"rpt_authorization_user_role");
		} else {
			$this->load->view('errors/html/error_hacks_401');
		}
	}
}
