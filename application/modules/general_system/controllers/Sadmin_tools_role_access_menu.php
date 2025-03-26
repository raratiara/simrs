<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sadmin_tools_user_access_menu extends CI_Controller
{
	/* Module */
 	private $folder_name			= "general_system/sadmin_tools_user_access_menu";
 	private $module_name			= "general_system_sadmin_tools_user_access_menu";
 	private $table_name 			= _PREFIX_TABLE."user";
 	private $primary_key 			= "user_id";
 	private $model_name				= "sadmin_tools_user_access_menu_model";
	/* Navigation */
 	private $parent_menu			= "general_system";
 	private $subparent_menu			= "general_system_sadmin";
 	private $sub_menu 				= "general_system_sadmin_tools_user_access_menu";
	/* Label */
 	private $label_parent_modul		= "General System";
 	private $label_subparent_modul	= "Super Admin Tools";
 	private $label_modul			= "Access Menu";
 	private $label_list_data		= "Daftar Data Access Menu";
 	private $label_add_data			= "Tambah Data Access Menu";
 	private $label_update_data		= "Edit Data Access Menu";
 	private $label_sukses_disimpan 	= "Data berhasil disimpan";
 	private $label_gagal_disimpan 	= "Data gagal disimpan";
 	private $label_delete_data		= "Hapus Data Access Menu";
 	private $label_sukses_dihapus 	= "Data berhasil dihapus";
 	private $label_gagal_dihapus 	= "Data gagal dihapus";
 	private $label_detail_data		= "Detail Data Access Menu";
 	private $label_import_data		= "Import Data Access Menu";
 	private $label_sukses_diimport 	= "Data berhasil diimport";
 	private $label_gagal_diimport 	= "Import data di baris : ";
 	private $label_export_data		= "Export";

   	public function __construct()
	{
      	parent::__construct();

      	if (empty($_SESSION["id"]))  header("location:".base_url('login'));
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
		$akses = $this->sadmin_tools_user_access_menu_model->user_akses($this->module_name);
		define('_USER_ACCESS_LEVEL_VIEW',$akses["view"]);
		define('_USER_ACCESS_LEVEL_ADD',$akses["add"]);
		define('_USER_ACCESS_LEVEL_UPDATE',$akses["edit"]);
		define('_USER_ACCESS_LEVEL_DELETE',$akses["del"]);
		define('_USER_ACCESS_LEVEL_DETAIL',$akses["detail"]);
		define('_USER_ACCESS_LEVEL_IMPORT',$akses["import"]);
		define('_USER_ACCESS_LEVEL_EKSPORT',$akses["eksport"]);
   	}

	public function index()
	{
		if(_USER_ACCESS_LEVEL_VIEW == "1") {
			$this->view();
		} else {
			$this->load->view('errors/html/error_hacks_401');
		}
	}

	public function view()
	{
		if(_USER_ACCESS_LEVEL_VIEW == "1") {
			$this->data['title'] 		= $this->label_list_data;
			$this->data['js'] 			= $this->folder_name.'/js_view';
			$this->data['sview'] 		= $this->folder_name.'/view';
			$this->data['sfolder'] 		= $this->folder_name;
			$this->data['smodul'] 		= $this->label_modul;
			$this->load->view(_TEMPLATE , $this->data);
		} else {
			$this->load->view('errors/html/error_hacks_401');
		}
	}

	public function get_data()
	{
		if(_USER_ACCESS_LEVEL_VIEW == "1") {
			$this->sadmin_tools_user_access_menu_model->get_list_data();
		} else {
			$this->load->view('errors/html/error_hacks_401');
		}
	}

	public function delete($id)
	{
		if(_USER_ACCESS_LEVEL_VIEW == "1" && _USER_ACCESS_LEVEL_DELETE == "1") {
			$rs = $this->sadmin_tools_user_access_menu_model->delete($id);

			if ($rs) {
				$msg 	= $this->label_sukses_dihapus;
				$stats 	= '1';
			} else {
				$msg 	= $this->label_gagal_dihapus;
				$stats 	= '0';
			}

			$this->session->set_flashdata('msg',$msg);
			$this->session->set_flashdata('stats',$stats);
			redirect($this->folder_name.'/');
		} else {
			$this->load->view('errors/html/error_hacks_401');
		}
	}

	public function add()
	{
		if(_USER_ACCESS_LEVEL_VIEW == "1" && _USER_ACCESS_LEVEL_ADD == "1") {
			if ($post = $this->input->post())
			{
				$rs = $this->sadmin_tools_user_access_menu_model->add_data($post);

				if ($rs) {
					$msg 	= $this->label_sukses_disimpan;
					$stats 	= '1';
				} else {
					$msg 	= $this->label_gagal_disimpan;
					$stats 	= '0';
				}

				$this->session->set_flashdata('msg',$msg);
				$this->session->set_flashdata('stats',$stats);
				redirect($this->folder_name.'/add');
			} else {
				$sQuery 					= "SELECT name, username, user_id FROM "._PREFIX_TABLE."user WHERE 1 = 1 AND user_id NOT IN ( SELECT * FROM ( SELECT user_id FROM "._PREFIX_TABLE."user_akses GROUP BY user_id HAVING COUNT(*) >= 1 ) AS subquery )";
				$oUserAccount 				= $this->db->query($sQuery)->result();
				$this->data['seluser'] 		= $this->sadmin_tools_user_access_menu_model->return_build_select2me($oUserAccount,'','','','user_id','user_id','','','user_id','name','-','username');
				$this->data['title'] 		= $this->label_add_data;
				$this->data['js'] 			= $this->folder_name.'/js_form';
				$this->data['sview'] 		= $this->folder_name.'/add';
				$this->data['sfolder'] 		= $this->folder_name;
				$this->data['smodul'] 		= $this->label_modul;
				$this->load->view(_TEMPLATE , $this->data);
			}
		} else {
			$this->load->view('errors/html/error_hacks_401');
		}
	}

	public function edit($id)
	{
		if(_USER_ACCESS_LEVEL_VIEW == "1" && _USER_ACCESS_LEVEL_UPDATE == "1") {
			if ($post = $this->input->post())
			{
				$rs = $this->sadmin_tools_user_access_menu_model->edit_data($post,$id);

				if ($rs) {
					$msg 	= $this->label_sukses_disimpan;
					$stats 	= '1';
				} else {
					$msg 	= $this->label_gagal_disimpan;
					$stats 	= '0';
				}

				$this->session->set_flashdata('msg',$msg);
				$this->session->set_flashdata('stats',$stats);
				redirect($this->folder_name.'/');
			} else {
				$rs 						= $this->sadmin_tools_user_access_menu_model->getRowData($id);
				$sQuery 					= "SELECT name, username, user_id FROM "._PREFIX_TABLE."user WHERE 1 = 1 AND user_id IN ( SELECT * FROM ( SELECT user_id FROM "._PREFIX_TABLE."user_akses GROUP BY user_id HAVING COUNT(*) >= 1 ) AS subquery )";
				$oUserAccount 				= $this->db->query($sQuery)->result();
				$this->data['seluser'] 		= $this->sadmin_tools_user_access_menu_model->return_build_select2me($oUserAccount,'',$rs->user_id,'','user_id','user_id','','','user_id','name','-','username','disabled');
				$rs 						= $this->sadmin_tools_user_access_menu_model->getRowDataAccess($id);
				if (count($rs) > 0) {
					FOREACH ($rs AS $r) {
						$dt[$r["user_menu_id"]]["view"] = $r["view"];
						$dt[$r["user_menu_id"]]["add"] = $r["add"];
						$dt[$r["user_menu_id"]]["edit"] = $r["edit"];
						$dt[$r["user_menu_id"]]["del"] = $r["del"]; ;
						$dt[$r["user_menu_id"]]["detail"] = $r["detail"]; ;
						$dt[$r["user_menu_id"]]["import"] = $r["import"]; ;
						$dt[$r["user_menu_id"]]["eksport"] = $r["eksport"]; ;
					}
				}
				$this->data['rs_akses'] 	= $dt;
				$this->data['title'] 		= $this->label_update_data;
				$this->data['js'] 			= $this->folder_name.'/js_form';
				$this->data['sview'] 		= $this->folder_name.'/edit';
				$this->data['sfolder'] 		= $this->folder_name;
				$this->data['smodul'] 		= $this->label_modul;
				$this->load->view(_TEMPLATE , $this->data);
			}
		} else {
			$this->load->view('errors/html/error_hacks_401');
		}
	}

	public function detail($id)
	{
		if(_USER_ACCESS_LEVEL_VIEW == "1" && _USER_ACCESS_LEVEL_DETAIL == "1") {
			$rs 						= $this->sadmin_tools_user_access_menu_model->getRowData($id);
			$sQuery 					= "SELECT name, username, user_id FROM "._PREFIX_TABLE."user WHERE 1 = 1 AND user_id IN ( SELECT * FROM ( SELECT user_id FROM "._PREFIX_TABLE."user_akses GROUP BY user_id HAVING COUNT(*) >= 1 ) AS subquery )";
			$oUserAccount 				= $this->db->query($sQuery)->result();
			$this->data['seluser'] 		= $this->sadmin_tools_user_access_menu_model->return_build_select2me($oUserAccount,'',$rs->user_id,'','user_id','user_id','','','user_id','name','-','username','disabled');
			$rs 						= $this->sadmin_tools_user_access_menu_model->getRowDataAccess($id);
			if (count($rs) > 0) {
				FOREACH ($rs AS $r) {
					$dt[$r["user_menu_id"]]["view"] = $r["view"];
					$dt[$r["user_menu_id"]]["add"] = $r["add"];
					$dt[$r["user_menu_id"]]["edit"] = $r["edit"];
					$dt[$r["user_menu_id"]]["del"] = $r["del"]; ;
					$dt[$r["user_menu_id"]]["detail"] = $r["detail"]; ;
					$dt[$r["user_menu_id"]]["import"] = $r["import"]; ;
					$dt[$r["user_menu_id"]]["eksport"] = $r["eksport"]; ;
				}
			}
			$this->data['rs_akses'] 	= $dt;
			$this->data['title'] 		= $this->label_update_data;
			//$this->data['js'] 			= $this->folder_name.'/js_form';
			$this->data['sview'] 		= $this->folder_name.'/detail';
			$this->data['sfolder'] 		= $this->folder_name;
			$this->data['smodul'] 		= $this->label_modul;
			$this->load->view(_TEMPLATE , $this->data);
		} else {
			$this->load->view('errors/html/error_hacks_401');
		}
	}

	public function import_action()
	{
		if(_USER_ACCESS_LEVEL_VIEW == "1" && _USER_ACCESS_LEVEL_IMPORT == "1") {
			$config['upload_path'] = './public/tmp/';
			$config['allowed_types'] = 'xls|xlsx';

			$this->load->library('upload', $config);


			if (! $this->upload->do_upload("userfile")) {
				$err_msg = array('error' => $this->upload->display_errors());
				//print_r($err_msg);
				//exit;
				$view_success = "fail";
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
								/*
								if ($column_index == 'B') {
									$format = "Y-m-d";
									$val = date($format, PHPExcel_Shared_Date::ExcelToPHP($val));
								}
								*/
							}

							$tmp_data[$tmp_iterate][$column_index] =  $val;
						}
					}
				}
				unlink($upload_data["upload_data"]["full_path"]);

				# upload to database
				$rs = $this->sadmin_tools_user_access_menu_model->import_data($tmp_data);
			}

			if ($rs == "") {
				$msg 	= $this->label_sukses_diimport;
				$stats 	= '1';
			} else {
				$msg 	= $this->label_gagal_diimport. $rs;
				$stats 	= '0';
			}

			$this->session->set_flashdata('msg',$msg);
			$this->session->set_flashdata('stats',$stats);
			redirect($this->folder_name.'/');
		} else {
			$this->load->view('errors/html/error_hacks_401');
		}
	}

	public function eksport_action()
	{
		if(_USER_ACCESS_LEVEL_VIEW == "1" && _USER_ACCESS_LEVEL_EKSPORT == "1") {
			$header	= "Report ".$this->label_modul."\n";
			$data 	= $this->sadmin_tools_user_access_menu_model->eksport_data();

			$colnames = [
				"ID",
				"User ID",
				"Menu ID",
				"View",
				"Add",
				"Edit",
				"Delete",
				"Detail",
				"Eksport",
				"Import",
			];

			$colfields = [
				"user_akses_id",
				"user_id",
				"user_menu_id",
				"view",
				"add",
				"edit",
				"del",
				"detail",
				"eksport",
				"import",
			];

			$this->sadmin_tools_user_access_menu_model->export_to_excel($colnames,$colfields, $data, $header ,"rpt_sadmin_tools_access_menu");
		} else {
			$this->load->view('errors/html/error_hacks_401');
		}
	}
}
