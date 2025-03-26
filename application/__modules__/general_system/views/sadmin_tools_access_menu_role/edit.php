                    <h3 class="page-title"></h3>
                     <div class="row">
                        <div class="col-md-12">
                            <!-- BEGIN TABLE PORTLET-->
                            <div class="portlet box blue">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class="fa fa-cogs"></i><?php if (isset($title) && $title<>"") echo $title;?>
									</div>
                                    <div class="actions">
											<?php if  (_USER_ACCESS_LEVEL_VIEW == "1") { ?>
											<a class="btn btn-default btn-sm btn-circle" href="<?php echo base_url($sfolder.'/');?>">
												<i class="fa fa-list-alt"></i>
												List Data
											</a>
											<?php } ?>
                                    </div>
                                </div>
                                <div class="portlet-body form">
											<form method="post" name="frmData" id="frmData"  class="form-horizontal" role="form">
										 		<div class="form-body">
												<div class="alert alert-danger display-hide"><button class="close" data-close="alert"></button> You have some form errors. Please check below. </div>
												<div class="alert alert-success display-hide"><button class="close" data-close="alert"></button> Your form validation is successful! </div>
												<div class="form-group">
													<label class="col-md-5 control-label no-padding-right">User Role <span class="required">*</span></label>
													<div class=" col-md-7">
														<div class=" input-group ">
															<?=$selrole;?>
														</div>
													</div>
												</div>
											 		<div class="form-group">
														<label class="col-sm-3 control-label no-padding-right" for="form-field-1"></label> 
				 										<div class="col-sm-1"> <input type="checkbox" name="view" id="view" value="1"> View </div>
				 										<div class="col-sm-1"> <input type="checkbox" name="add" id="add" value="1"> Add </div>
				 										<div class="col-sm-1"> <input type="checkbox" name="update" id="update" value="1"> Update </div>
				 										<div class="col-sm-1"> <input type="checkbox" name="delete" id="delete" value="1"> Delete </div>
				 										<div class="col-sm-1"> <input type="checkbox" name="detail" id="detail" value="1"> Detail </div>
				 										<div class="col-sm-1"> <input type="checkbox" name="import" id="import" value="1"> Import </div>
				 										<div class="col-sm-1"> <input type="checkbox" name="eksport" id="eksport" value="1"> Eksport </div>
													</div> 
													<?php
													$sql = "SELECT * FROM "._PREFIX_TABLE."user_menu WHERE parent_id = 0 and show_menu = '1' ORDER BY um_order ASC";
													$res = $this->db->query($sql);
													$rs = $res->result_array();
													if (count($rs) > 0) {
														FOREACH ($rs AS $r) {
															$title = "";
															$readonly = "";

															$sql_1 = "SELECT * FROM "._PREFIX_TABLE."user_menu WHERE parent_id = ".$r["user_menu_id"]." AND show_menu = '1'  ORDER BY um_order ASC";
															$res_1 = $this->db->query($sql_1);
															$rs_1 = $res_1->result_array();
															
															if (count($rs_1) > 0 || $r["is_parent"] == true) { 
																$title = "<b>".$r["title"]."</b>";
																$readonly = "disabled";
															} else {
																$title = $r["title"];
															}

															$check_view = ""; $check_add = ""; $check_update = ""; $check_del = ""; $check_detail = ""; $check_import = ""; $check_eksport = "";
															if (isset($rs_akses[$r["user_menu_id"]]["view"]) && $rs_akses[$r["user_menu_id"]]["view"] == 1) $check_view = " checked=\"true\"";
															if (isset($rs_akses[$r["user_menu_id"]]["add"]) && $rs_akses[$r["user_menu_id"]]["add"] == 1) $check_add = " checked=\"true\"";
															if (isset($rs_akses[$r["user_menu_id"]]["edit"]) && $rs_akses[$r["user_menu_id"]]["edit"] == 1) $check_update = " checked=\"true\"";
															if (isset($rs_akses[$r["user_menu_id"]]["del"]) && $rs_akses[$r["user_menu_id"]]["del"] == 1) $check_del = " checked=\"true\"";
															if (isset($rs_akses[$r["user_menu_id"]]["detail"]) && $rs_akses[$r["user_menu_id"]]["detail"] == 1) $check_detail = " checked=\"true\"";
															if (isset($rs_akses[$r["user_menu_id"]]["import"]) && $rs_akses[$r["user_menu_id"]]["import"] == 1) $check_import = " checked=\"true\"";
															if (isset($rs_akses[$r["user_menu_id"]]["eksport"]) && $rs_akses[$r["user_menu_id"]]["eksport"] == 1) $check_eksport = " checked=\"true\"";
															?>
													<div class="form-group">
														<label class="col-sm-3 control-label no-padding-right"><?php echo $title;?></label> 
				 										<div class="col-sm-1"><input <?php echo  $check_view;?> type="checkbox" class="checkbox_view" name="view_<?php echo $r["user_menu_id"];?>" id="view_<?php echo $r["user_menu_id"];?>" value="1"></div>
				 										<div class="col-sm-1"><input <?php echo  $check_add;?>  type="checkbox" class="checkbox_add" name="add_<?php echo $r["user_menu_id"];?>" id="add_<?php echo $r["user_menu_id"];?>" value="1" <?php echo $readonly;?>></div>
				 										<div class="col-sm-1"><input <?php echo  $check_update;?>  type="checkbox" class="checkbox_update"  name="update_<?php echo $r["user_menu_id"];?>" id="update_<?php echo $r["user_menu_id"];?>" value="1" <?php echo $readonly;?>></div>
				 										<div class="col-sm-1"><input <?php echo  $check_del;?>  type="checkbox" class="checkbox_delete"  name="delete_<?php echo $r["user_menu_id"];?>" id="delete_<?php echo $r["user_menu_id"];?>" value="1" <?php echo $readonly;?>></div>
				 										<div class="col-sm-1"><input <?php echo  $check_detail;?>  type="checkbox" class="checkbox_detail"  name="detail_<?php echo $r["user_menu_id"];?>" id="detail_<?php echo $r["user_menu_id"];?>" value="1" <?php echo $readonly;?>></div>
				 										<div class="col-sm-1"><input <?php echo  $check_import;?>  type="checkbox" class="checkbox_import"  name="import_<?php echo $r["user_menu_id"];?>" id="import_<?php echo $r["user_menu_id"];?>" value="1" <?php echo $readonly;?>></div>
				 										<div class="col-sm-1"><input <?php echo  $check_eksport;?>  type="checkbox" class="checkbox_eksport"  name="eksport_<?php echo $r["user_menu_id"];?>" id="eksport_<?php echo $r["user_menu_id"];?>" value="1" <?php echo $readonly;?>></div>
													</div> 
															<?php
															if (count($rs_1) > 0) {
																FOREACH ($rs_1 AS $r_1) {
																	$title = "";
																	$readonly = "";

																	$sql_2 = "SELECT * FROM "._PREFIX_TABLE."user_menu WHERE parent_id = ".$r_1["user_menu_id"]." AND show_menu = '1'  ORDER BY um_order ASC";
																	$res_2 = $this->db->query($sql_2);
																	$rs_2 = $res_2->result_array();
																	
																	if ($r_1["is_parent"] == true) { 
																		$title = "[ <b>".$r_1["title"]."</b> ]";
																		$readonly = "disabled";
																	} else {
																		$title = "<i>".$r_1["title"]."</i>";
																	}

																	$check_view = ""; $check_add = ""; $check_update = ""; $check_del = ""; $check_detail = ""; $check_import = ""; $check_eksport = "";
																	if (isset($rs_akses[$r_1["user_menu_id"]]["view"]) && $rs_akses[$r_1["user_menu_id"]]["view"] == 1) $check_view = " checked=\"true\"";
																	if (isset($rs_akses[$r_1["user_menu_id"]]["add"]) && $rs_akses[$r_1["user_menu_id"]]["add"] == 1) $check_add = " checked=\"true\"";
																	if (isset($rs_akses[$r_1["user_menu_id"]]["edit"]) && $rs_akses[$r_1["user_menu_id"]]["edit"] == 1) $check_update = " checked=\"true\"";
																	if (isset($rs_akses[$r_1["user_menu_id"]]["del"]) && $rs_akses[$r_1["user_menu_id"]]["del"] == 1) $check_del = " checked=\"true\"";
																	if (isset($rs_akses[$r_1["user_menu_id"]]["detail"]) && $rs_akses[$r_1["user_menu_id"]]["detail"] == 1) $check_detail = " checked=\"true\"";
																	if (isset($rs_akses[$r_1["user_menu_id"]]["import"]) && $rs_akses[$r_1["user_menu_id"]]["import"] == 1) $check_import = " checked=\"true\"";
																	if (isset($rs_akses[$r_1["user_menu_id"]]["eksport"]) && $rs_akses[$r_1["user_menu_id"]]["eksport"] == 1) $check_eksport = " checked=\"true\"";
																	?>
																	<div class="form-group">
																		<label class="col-sm-3 control-label no-padding-right"><?php echo $title;?></label> 
								 										<div class="col-sm-1"><input <?php echo  $check_view;?> type="checkbox" class="checkbox_view" name="view_<?php echo $r_1["user_menu_id"];?>" id="view_<?php echo $r_1["user_menu_id"];?>" value="1"></div>
								 										<div class="col-sm-1"><input <?php echo  $check_add;?>  type="checkbox" class="checkbox_add" name="add_<?php echo $r_1["user_menu_id"];?>" id="add_<?php echo $r_1["user_menu_id"];?>" value="1" <?php echo $readonly;?>></div>
								 										<div class="col-sm-1"><input <?php echo  $check_update;?> type="checkbox" class="checkbox_update" name="update_<?php echo $r_1["user_menu_id"];?>" id="update_<?php echo $r_1["user_menu_id"];?>" value="1" <?php echo $readonly;?>></div>
								 										<div class="col-sm-1"><input <?php echo  $check_del;?> type="checkbox" class="checkbox_delete" name="delete_<?php echo $r_1["user_menu_id"];?>" id="delete_<?php echo $r_1["user_menu_id"];?>" value="1" <?php echo $readonly;?>></div>
																		<div class="col-sm-1"><input <?php echo  $check_detail;?>  type="checkbox" class="checkbox_detail"  name="detail_<?php echo $r_1["user_menu_id"];?>" id="detail_<?php echo $r_1["user_menu_id"];?>" value="1" <?php echo $readonly;?>></div>
								 										<div class="col-sm-1"><input <?php echo  $check_import;?> type="checkbox" class="checkbox_import" name="import_<?php echo $r_1["user_menu_id"];?>" id="import_<?php echo $r_1["user_menu_id"];?>" value="1" <?php echo $readonly;?>></div>
								 										<div class="col-sm-1"><input <?php echo  $check_eksport;?> type="checkbox" class="checkbox_eksport" name="eksport_<?php echo $r_1["user_menu_id"];?>" id="eksport_<?php echo $r_1["user_menu_id"];?>" value="1" <?php echo $readonly;?>></div>
																	</div> 
																<?php
																	if (count($rs_2) > 0) {
																		FOREACH ($rs_2 AS $r_2) {
																			$title = "";
																			$readonly = "";

																			$sql_3 = "SELECT * FROM "._PREFIX_TABLE."user_menu WHERE parent_id = ".$r_2["user_menu_id"]." AND show_menu = '1'  ORDER BY um_order ASC";
																			$res_3 = $this->db->query($sql_3);
																			$rs_3 = $res_3->result_array();
																			
																			if ($r_2["is_parent"] == true) { 
																				$title = "< <b>".$r_2["title"]."</b> >";
																				$readonly = "disabled";
																			} else {
																				$title = "<i>".$r_2["title"]."</i>";
																			}

																			$check_view = ""; $check_add = ""; $check_update = ""; $check_del = ""; $check_detail = ""; $check_import = ""; $check_eksport = "";
																			if (isset($rs_akses[$r_2["user_menu_id"]]["view"]) && $rs_akses[$r_2["user_menu_id"]]["view"] == 1) $check_view = " checked=\"true\"";
																			if (isset($rs_akses[$r_2["user_menu_id"]]["add"]) && $rs_akses[$r_2["user_menu_id"]]["add"] == 1) $check_add = " checked=\"true\"";
																			if (isset($rs_akses[$r_2["user_menu_id"]]["edit"]) && $rs_akses[$r_2["user_menu_id"]]["edit"] == 1) $check_update = " checked=\"true\"";
																			if (isset($rs_akses[$r_2["user_menu_id"]]["del"]) && $rs_akses[$r_2["user_menu_id"]]["del"] == 1) $check_del = " checked=\"true\"";
																			if (isset($rs_akses[$r_2["user_menu_id"]]["detail"]) && $rs_akses[$r_2["user_menu_id"]]["detail"] == 1) $check_detail = " checked=\"true\"";
																			if (isset($rs_akses[$r_2["user_menu_id"]]["import"]) && $rs_akses[$r_2["user_menu_id"]]["import"] == 1) $check_import = " checked=\"true\"";
																			if (isset($rs_akses[$r_2["user_menu_id"]]["eksport"]) && $rs_akses[$r_2["user_menu_id"]]["eksport"] == 1) $check_eksport = " checked=\"true\"";
																			?>
																			<div class="form-group">
																				<label class="col-sm-3 control-label no-padding-right"><?php echo $title;?></label> 
																				<div class="col-sm-1"><input <?php echo  $check_view;?> type="checkbox" class="checkbox_view" name="view_<?php echo $r_2["user_menu_id"];?>" id="view_<?php echo $r_2["user_menu_id"];?>" value="1"></div>
																				<div class="col-sm-1"><input <?php echo  $check_add;?>  type="checkbox" class="checkbox_add" name="add_<?php echo $r_2["user_menu_id"];?>" id="add_<?php echo $r_2["user_menu_id"];?>" value="1" <?php echo $readonly;?>></div>
																				<div class="col-sm-1"><input <?php echo  $check_update;?> type="checkbox" class="checkbox_update" name="update_<?php echo $r_2["user_menu_id"];?>" id="update_<?php echo $r_2["user_menu_id"];?>" value="1" <?php echo $readonly;?>></div>
																				<div class="col-sm-1"><input <?php echo  $check_del;?> type="checkbox" class="checkbox_delete" name="delete_<?php echo $r_2["user_menu_id"];?>" id="delete_<?php echo $r_2["user_menu_id"];?>" value="1" <?php echo $readonly;?>></div>
																				<div class="col-sm-1"><input <?php echo  $check_detail;?>  type="checkbox" class="checkbox_detail"  name="detail_<?php echo $r_2["user_menu_id"];?>" id="detail_<?php echo $r_2["user_menu_id"];?>" value="1" <?php echo $readonly;?>></div>
																				<div class="col-sm-1"><input <?php echo  $check_import;?> type="checkbox" class="checkbox_import" name="import_<?php echo $r_2["user_menu_id"];?>" id="import_<?php echo $r_2["user_menu_id"];?>" value="1" <?php echo $readonly;?>></div>
																				<div class="col-sm-1"><input <?php echo  $check_eksport;?> type="checkbox" class="checkbox_eksport" name="eksport_<?php echo $r_2["user_menu_id"];?>" id="eksport_<?php echo $r_2["user_menu_id"];?>" value="1" <?php echo $readonly;?>></div>
																			</div> 
																			<?php
																			if (count($rs_3) > 0) {
																				FOREACH ($rs_3 AS $r_3) {

																					$check_view = ""; $check_add = ""; $check_update = ""; $check_del = ""; $check_detail = ""; $check_import = ""; $check_eksport = "";
																					if (isset($rs_akses[$r_3["user_menu_id"]]["view"]) && $rs_akses[$r_3["user_menu_id"]]["view"] == 1) $check_view = " checked=\"true\"";
																					if (isset($rs_akses[$r_3["user_menu_id"]]["add"]) && $rs_akses[$r_3["user_menu_id"]]["add"] == 1) $check_add = " checked=\"true\"";
																					if (isset($rs_akses[$r_3["user_menu_id"]]["edit"]) && $rs_akses[$r_3["user_menu_id"]]["edit"] == 1) $check_update = " checked=\"true\"";
																					if (isset($rs_akses[$r_3["user_menu_id"]]["del"]) && $rs_akses[$r_3["user_menu_id"]]["del"] == 1) $check_del = " checked=\"true\"";
																					if (isset($rs_akses[$r_3["user_menu_id"]]["detail"]) && $rs_akses[$r_3["user_menu_id"]]["detail"] == 1) $check_detail = " checked=\"true\"";
																					if (isset($rs_akses[$r_3["user_menu_id"]]["import"]) && $rs_akses[$r_3["user_menu_id"]]["import"] == 1) $check_import = " checked=\"true\"";
																					if (isset($rs_akses[$r_3["user_menu_id"]]["eksport"]) && $rs_akses[$r_3["user_menu_id"]]["eksport"] == 1) $check_eksport = " checked=\"true\"";
																					?>
																					<div class="form-group">
																						<label class="col-sm-3 control-label no-padding-right"><i><?php echo $r_3["title"];?></i></label> 
																						<div class="col-sm-1"><input <?php echo  $check_view;?> type="checkbox" class="checkbox_view" name="view_<?php echo $r_3["user_menu_id"];?>" id="view_<?php echo $r_3["user_menu_id"];?>" value="1"></div>
																						<div class="col-sm-1"><input <?php echo  $check_add;?>  type="checkbox" class="checkbox_add" name="add_<?php echo $r_3["user_menu_id"];?>" id="add_<?php echo $r_3["user_menu_id"];?>" value="1"></div>
																						<div class="col-sm-1"><input <?php echo  $check_update;?> type="checkbox" class="checkbox_update" name="update_<?php echo $r_3["user_menu_id"];?>" id="update_<?php echo $r_3["user_menu_id"];?>" value="1"></div>
																						<div class="col-sm-1"><input <?php echo  $check_del;?> type="checkbox" class="checkbox_delete" name="delete_<?php echo $r_3["user_menu_id"];?>" id="delete_<?php echo $r_3["user_menu_id"];?>" value="1"></div>
																						<div class="col-sm-1"><input <?php echo  $check_detail;?>  type="checkbox" class="checkbox_detail"  name="detail_<?php echo $r_3["user_menu_id"];?>" id="detail_<?php echo $r_3["user_menu_id"];?>" value="1"></div>
																						<div class="col-sm-1"><input <?php echo  $check_import;?> type="checkbox" class="checkbox_import" name="import_<?php echo $r_3["user_menu_id"];?>" id="import_<?php echo $r_3["user_menu_id"];?>" value="1"></div>
																						<div class="col-sm-1"><input <?php echo  $check_eksport;?> type="checkbox" class="checkbox_eksport" name="eksport_<?php echo $r_3["user_menu_id"];?>" id="eksport_<?php echo $r_3["user_menu_id"];?>" value="1"></div>
																					</div> 
																				<?php
																				}
																			}
																		}
																	}
																}
															}

														}
													}
													?>
												</div>

												<div class="clearfix form-actions">
													<div class="col-md-offset-5 col-md-7">
														<?php if  (_USER_ACCESS_LEVEL_UPDATE == "1") { ?>
														<button class="btn btn-info" type="submit" id="SubmitData">
															<i class="fa fa-check bigger-110"></i>
															Submit
														</button>
														<?php } ?>
														<button class="btn" type="reset">
															<i class="fa fa-undo bigger-110"></i>
															Reset
														</button>
													</div>
												</div>
											</form>
                                </div>
                            </div>
                            <!-- END TABLE PORTLET-->
                        </div>
                    </div>