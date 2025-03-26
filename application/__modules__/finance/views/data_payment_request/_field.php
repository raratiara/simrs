									<div class="row">
										<div class="col-md-6 col-sm-12">
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">No PR </label>
												<div class="col-md-8">
													<?=$txtpr;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">Tipe Referensi </label>
												<div class="col-md-8">
													<?=$seloforType;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">No Referensi </label>
												<div class="col-md-8">
													<?=$selonoRef;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">No Project </label>
												<div class="col-md-8">
													<?=$txtseloProject;?><?=$seloProject;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">Nama Project </label>
												<div class="col-md-8">
													<?=$txtproject;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">WBS </label>
												<div class="col-md-8">
													<?=$seloWbs;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">Payment Purposed </label>
												<div class="col-md-8">
													<?=$txtdescription;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">Note </label>
												<div class="col-md-8">
													<?=$txtnotes;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">Last Status </label>
												<div class="col-md-8">
													<?=$txtasdraft;?><?=$txtasoldstat;?><?=$txtasnewstat;?><?=$seloLastStatus;?>
												</div>
											</div>
										</div>
										<div class="col-md-6 col-sm-12">
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">Date </label>
												<div class="col-md-8">
													<?=$txtdateca;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">Payment To </label>
												<div class="col-md-8">
													<?=$seloRequestor;?>
												</div>
											</div>
											<div class="form-group supp_field">
												<label class="col-md-4 control-label no-padding-right">Supplier ID </label>
												<div class="col-md-8">
													<?=$txtsuppid;?>
												</div>
											</div>
											<div class="form-group balance_field">
												<label class="col-md-4 control-label no-padding-right">Remaining Balance </label>
												<div class="col-md-8">
													<?=$txtremainingpr;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">Total Request </label>
												<div class="col-md-8">
													<?=$txttotalpr;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">Transfer to </label>
												<div class="col-md-8">
													<?=$radioactive;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">No Rekening </label>
												<div class="col-md-8">
													<?=$txtnorek;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">Nama Rekening </label>
												<div class="col-md-8">
													<?=$txtnamarek;?>
												</div>
											</div>
										</div>
									</div>
									<div class="row ca">
                                        <div class="col-md-12">
											<div class="portlet box green">
												<div class="portlet-title">
													<div class="caption"><i class="fa fa-cubes"></i>Attachment </div>
													<div class="tools">
														<input type="button" class="btn btn-default blue btn-outline btn-circle btn-sm active" id="addprrow" value="Add Row" />
													</div>
												</div>
												<div class="portlet-body">
													<div class="table-scrollable tablesaw-cont">
													<table class="table table-striped table-bordered table-hover pr-list tablesaw tablesaw-stack" data-tablesaw-mode="stack">
														<thead>
															<tr>
																<th scope="col">No</th>
																<th scope="col">Deskripsi</th>
																<th scope="col">Attach</th>
																<th scope="col"></th>
															</tr>
														</thead>
														<tbody>
														</tbody>
														<tfoot>
														</tfoot>
													</table>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="row ca-log">
                                        <div class="col-md-12">
											<div class="portlet box green">
												<div class="portlet-title">
													<div class="caption"><i class="fa fa-cubes"></i>LOG Status </div>
													<div class="tools">
														<!--input type="button" class="btn btn-default blue btn-outline btn-circle btn-sm active" id="addcacloserow" value="Add Row" /-->
													</div>
												</div>
												<div class="portlet-body">
													<div class="table-scrollable tablesaw-cont">
													<table class="table table-striped table-bordered table-hover pr-log-list tablesaw tablesaw-stack" data-tablesaw-mode="stack">
														<thead>
															<tr>
																<th scope="col">No</th>
																<th scope="col">Deskripsi</th>
																<th scope="col">Date</th>
																<th scope="col">PIC</th>
																<th scope="col">Attachment</th>
																<!--th scope="col"></th-->
															</tr>
														</thead>
														<tbody>
														</tbody>
														<tfoot>
														</tfoot>
													</table>
													</div>
												</div>
											</div>
										</div>
									</div>
									<h4 class="form-section new-stat">Approval Supporting Document</h4>
									<div class="row new-stat">
										<div class="col-md-6 col-sm-12">
											<div class="form-group">
												<label class="col-md-4 control-label no-padding">File </label>
												<div class="col-md-8">
													<?=$filenewstatus;?>
												</div>
											</div>
										</div>
									</div>