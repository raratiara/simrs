<div class="form-group">
	<label class="col-md-3 control-label no-padding-right">No DO <span class="required">*</span></label>
	<div class="col-md-9">
		<?= $txtdo; ?>
	</div>
</div>
<div class="form-group">
	<label class="col-md-3 control-label no-padding-right">Date <span class="required">*</span></label>
	<div class="col-md-9">
		<?= $txtdate; ?>
	</div>
</div>
<div class="form-group">
	<label class="col-md-3 control-label no-padding-right">PO </label>
	<div class="col-md-9">
		<?= $seloPo; ?>
	</div>
</div>
<div class="form-group">
	<label class="col-md-3 control-label no-padding-right">Project </label>
	<div class="col-md-9">
		<?= $seloProject; ?>
	</div>
</div>

<div class="form-group">
	<label class="col-md-3 control-label no-padding-right">Ship to:</label>
	<div class="col-md-9">
		<?= $datapic; ?>
		<div style="display:block;padding-left:15px;padding-right:15px;">
			<div class="form-group">
				<label>Nama <span class="required">*</span></label>
				<?= $txtrecname; ?>
			</div>
			<div class="form-group">
				<label>Alamat</label>
				<?= $txtrecaddress; ?>
			</div>
			<div class="form-group">
				<label>Telp <span class="required">*</span></label>
				<?= $txtrectelp; ?>
			</div>
		</div>
	</div>
</div>

<div class="form-group">
	<label class="col-md-3 control-label no-padding-right">Note </label>
	<div class="col-md-9">
		<?= $txtnote; ?>
	</div>
</div>

<div class="form-group">
	<label class="col-md-2 control-label no-padding-right"></label>
	<div class="col-md-12">
		<div class="col-md-3">
			<div class="form-group">
				<label class="col-md-12">Received by</label>
				<div class="col-md-12"><?= $seloReceived; ?></div>
			</div>
		</div>
		<div class="col-md-3">
			<div class="form-group">
				<label class="col-md-12">Shipped by</label>
				<div class="col-md-12"><?= $seloShipped; ?></div>
			</div>
		</div>
		<div class="col-md-3">
			<div class="form-group">
				<label class="col-md-12">Prepared by</label>
				<div class="col-md-12"><?= $seloPrepared; ?></div>
			</div>
		</div>
		<div class="col-md-3">
			<div class="form-group">
				<label class="col-md-12">Authorized by</label>
				<div class="col-md-12"><?= $seloAuthorized; ?></div>
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
								<th scope="col" style="text-align:center;">Coly</th>
								<th scope="col">Remark</th>
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