                    <h3 class="page-title"></h3>
                     <div class="row">
                        <div class="col-md-12">
                            <!-- BEGIN TABLE PORTLET-->
                            <div class="portlet box blue">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <?php if (isset($icon) && $icon<>"") echo '<i class="fa '.$icon.'"></i>';?><?php if (isset($title) && $title<>"") echo $title;?>
									</div>
                                    <div class="actions">
										<?php if  (_USER_ACCESS_LEVEL_EKSPORT == "1") { ?>
											<a class="btn btn-default btn-sm btn-circle" id="btnEksportData">
												<i class="fa fa-download"></i>
												Eksport
											</a>
										<?php } ?>
										<?php if  (_USER_ACCESS_LEVEL_IMPORT == "1") { ?>
											<a class="btn btn-default btn-sm btn-circle" id="btnImportData">
												<i class="fa fa-upload"></i>
												Import
											</a>
										<?php } ?>
										<?php if  (_USER_ACCESS_LEVEL_ADD == "1") { ?>
											
											<a class="btn btn-default btn-sm btn-circle" id="btnAddData">
												<i class="fa fa-floppy-o"></i>
												Add Data
											</a> 
										<?php } ?>
										<?php if  (_USER_ACCESS_LEVEL_DELETE == "1") { ?>
											
											<a class="btn btn-default btn-sm btn-circle" id="btnBulkData">
												<i class="fa fa-times"></i>
												Delete Bulk
											</a> 
										<?php } ?>
                                    </div>
                                </div>
                                <div class="portlet-body">
											<form name="frmListData" id="frmListData">
												<table id="dynamic-table" class="table table-striped table-bordered table-hover table-header-fixed" style="width:100%">
													<thead>
														<tr>
<?php if (_USER_ACCESS_LEVEL_DELETE == "1"){ ?>
															<th width="15px"><input type="checkbox" id="check-all"></th>
<?php } ?>
															<!--th width="120px">Action</th-->
															<?php 
															if (isset($thData) && $thData<>"") {
																foreach($thData as $th){
																	if(!is_array($th)){
																		echo '<th>'.$th.'</th>';
																	} else {
																		echo '<th '.$th[1].'>'.$th[0].'</th>';
																	}
																}
															}	
															?>
														</tr>
													</thead>

													<tbody>
													</tbody>
												</table>
											</form>
                                </div>
                            </div>
                            <!-- END TABLE PORTLET-->
                        </div>
                    </div>
