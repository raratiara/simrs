<div class="row">
	<!-- Left -->
	<div class="col-md-6 col-sm-12">
		<div class="form-group">
			<label class="col-md-4 control-label no-padding-right">PO <span class="required">*</span></label>
			<div class="col-md-8">
				<?= $seloPO; ?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-md-4 control-label no-padding-right">Project</label>
			<div class="col-md-8">
				<?= $seloProject; ?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-md-4 control-label no-padding-right">Purchase to: </label>
		</div>
		<div class="form-group">
			<label class="col-md-4 control-label no-padding-right">Supplier: </label>
			<div class="col-md-8">
				<?= $seloSupplier; ?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-md-4 control-label no-padding-right">Nama Supplier: </label>
			<div class="col-md-8">
				<?= $txtnamasupplier; ?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-md-4 control-label no-padding-right">Alamat Supplier: </label>
			<div class="col-md-8">
				<?= $txtalamatsupplier; ?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-md-4 control-label no-padding-right">Person in Charge: </label>
			<div class="col-md-8">
				<?= $txtpic; ?>
			</div>
		</div>
	</div>
	<!-- Right -->
	<div class="col-md-6 col-sm-12">
		<div class="form-group">
			<label class="col-md-4 control-label no-padding-right">Date</label>
			<div class="col-md-8">
				<?= $txtdate; ?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-md-4 control-label no-padding-right">Delivery Date <span class="required">*</span></label>
			<div class="col-md-8">
				<?= $txtdatedelivery; ?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-md-4 control-label no-padding-right">Currency </label>
			<div class="col-md-8">
				<?= $seloCurrency; ?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-md-4 control-label no-padding-right">Total Harga </label>
			<div class="col-md-8">
				<?= $worth; ?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-md-4 control-label no-padding-right">SPK</span></label>
			<div class="col-md-8">
				<?= $seloSpk; ?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-md-4 control-label no-padding-right">Approval:</span></label>
			<div class="col-md-8">
				<?= $radioapproval; ?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-md-4 control-label no-padding-right">Warehouse</span></label>
			<div class="col-md-8">
				<?= $seloWarehouse; ?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-md-4 control-label no-padding-right">Alamat Warehouse</span></label>
			<div class="col-md-8">
				<?= $txtalamatwarehouse; ?>
			</div>
		</div>
	</div>

	<!-- Bottom row -->
	<div class="col-md-12 col-sm-12" style="margin-top: 20px;">
		<div class="form-group">
			<label class="col-md-2 control-label no-padding-right">TOP</label>
			<div class="col-md-10">
				<div class="col-md-2">
					<div class="form-group">
						<label class="col-md-12">DP</label>
						<div class="col-md-12"><?= $txttopdp; ?></div>
					</div>
				</div>
				<div class="col-md-2">
					<div class="form-group">
						<label class="col-md-12">Term 1</label>
						<div class="col-md-12"><?= $txttopt1; ?></div>
					</div>
				</div>
				<div class="col-md-2">
					<div class="form-group">
						<label class="col-md-12">Term 2</label>
						<div class="col-md-12"><?= $txttopt2; ?></div>
					</div>
				</div>
				<div class="col-md-2">
					<div class="form-group">
						<label class="col-md-12">Term 3</label>
						<div class="col-md-12"><?= $txttopt3; ?></div>
					</div>
				</div>
				<div class="col-md-2">
					<div class="form-group">
						<label class="col-md-12">Final Term</label>
						<div class="col-md-12"><?= $txttoptf; ?></div>
					</div>
				</div>
				<div class="col-md-2">
					<div class="form-group">
						<label class="col-md-12">Retensi</label>
						<div class="col-md-12"><?= $txttoprt; ?></div>
					</div>
				</div>
			</div>
		</div>
		<div class="form-group">
			<label class="col-md-2 control-label no-padding-right">Remark</label>
			<div class="col-md-10">
				<?= $txtremark; ?>
			</div>
		</div>
	</div>
	<div class="col-md-12 col-sm-12">
		<div class="form-group">
			<label class="col-md-2 control-label no-padding-right">Term and Condition</label>
			<div class="col-md-10">
				<?= $txtterm; ?>
				<!-- <div id="term_condition"> </div> -->
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<div class="portlet box green">
			<div class="portlet-title">
				<div class="caption"><i class="fa fa-cubes"></i>List Item </div>
				<div class="tools">
					<input type="button" class="btn btn-default blue btn-outline btn-circle btn-sm active" id="addrow" value="Add Row" />
				</div>
			</div>
			<div class="portlet-body">
				<div class="table-scrollable tablesaw-cont">
					<table class="table table-striped table-bordered table-hover order-list tablesaw tablesaw-stack" data-tablesaw-mode="stack">
						<thead>
							<tr>
								<th scope="col">No</th>
								<th scope="col">Item Code</th>
								<th scope="col">Deskripsi</th>
								<th scope="col" style="text-align:center;">Satuan</th>
								<th scope="col" style="text-align:center;">Qty</th>
								<th scope="col" style="text-align:right;">Harga Satuan</th>
								<th scope="col" style="text-align:right;">Total Harga</th>
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