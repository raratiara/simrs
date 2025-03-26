<!doctype html>
<?php
$mk_currenttime  = mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y"));
if($rs->date > _CUT_OFF_LOGO){
	$new_logo = TRUE;
	$nbid_logo = _ASSET_LOGO_2022_SURAT;
} else {
	$new_logo = FALSE;
	$nbid_logo = _ASSET_LOGO;
}
?>
<html>
<head>
    <meta charset="utf-8">
    <title>Purchase Order # <?= $rs->po; ?></title>

    <style>
        body {
            -webkit-print-color-adjust: exact !important;
        }

        @page {
            size: 21cm 29.7cm;
            margin: 10mm;
            /* change the margins as you want them to be. */
        }

        .invoice-box {
            font-size: 11px;
            line-height: 20px;
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
        }

        @media only screen {
            .invoice-box {
                max-width: 800px;
                margin: auto;
                padding: 30px;
                border: 1px solid #eee;
                box-shadow: 0 0 10px rgba(0, 0, 0, .15);
            }
        }

        @media print {
            .pagebreak {
                page-break-before: always;
            }

            /* page-break-after works, as well */
        }

        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
        }

        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }

        .invoice-box table tr td:nth-child(5) {
            text-align: right;
        }

        .invoice-box table tr.top td:nth-child(2),
        .invoice-box table tr.information td:nth-child(2) {
            text-align: right;
        }

        .invoice-box table tr.top table td {
            padding-bottom: 0px;
        }

        .invoice-box table tr.top table td.title {
            font-size: 32px;
            line-height: 45px;
            color: #333;
        }

		.invoice-box table tr.top table td.title div.logo_address {
			display: absolute;
			margin-bottom: -100px;
		}

		.invoice-box div.logo_address img {
			width: 375px;
		}

        .invoice-box table tr.information table td {
            padding-bottom: 10px;
        }

        .invoice-box table tr.heading td {
            background: #eee;
            text-align: right;
            border-bottom: 1px solid #ddd;
            border-right: 1px solid #ddd;
            font-weight: bold;
        }

        .invoice-box table tr.heading td:first-child {
            text-align: left;
        }

        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
            border-right: 1px solid #eee;
        }

        .invoice-box table tr.item td.last {
            border-right: none;
        }

        .invoice-box table tr.item.last td {
            /*border-bottom: none;*/
        }

        .invoice-box table tr.total td:nth-child(1) {
            border-right: 2px solid #eee;
        }

        .invoice-box table tr.total td:nth-child(2) {
            border-right: 2px solid #eee;
            border-bottom: 2px solid #eee;
            text-align: right;
            font-weight: bold;
        }

        .invoice-box table tr.total td:nth-child(3) {
            border-right: 2px solid #eee;
            border-bottom: 2px solid #eee;
            text-align: right;
            font-weight: bold;
        }

        .invoice-box table tr.sign td {
            text-align: center;
            width-min: 150px;
        }

        @media only screen and (max-width: 600px) {
            .invoice-box table tr.top table td {
                width: 100%;
                display: block;
                text-align: center;
            }

            .invoice-box table tr.information table td {
                width: 100%;
                display: block;
                text-align: center;
            }
        }

        /** RTL **/
        .rtl {
            direction: rtl;
            font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
        }

        .rtl table {
            text-align: right;
        }

        .rtl table tr td:nth-child(5) {
            text-align: left;
        }

        .top-cont {
            margin-bottom: -20px;
        }

        .comp-box {
            display: inline-block;
            width: 230px;
            text-align: left;
        }

        .company {
            font-weight: bold;
            color: #1472c0;
        }

        .company-address {
            font-weight: bold;
        }

        .label-box {
            display: inline-block;
            font-weight: bold;
            vertical-align: top;
            width: 85px;
            text-align: left;
        }

        .label-box-bottom {
            display: inline-block;
            vertical-align: top;
            width: 85px;
            text-align: left;
        }

        .info-box {
            display: inline-block;
            width: 120px;
            text-align: left;
        }

        .label-quote {
            display: inline-block;
            vertical-align: top;
            width: 180px;
        }

        .inter {
            display: inline-block;
            vertical-align: top;
            width: 20px;
            text-align: center;
        }

        .info-quote {
            display: inline-block;
            font-weight: bold;
        }

        .label-to {
            display: inline-block;
            background: #eee;
            font-weight: bold;
            border: 2px solid #eee;
            padding: 5px;
            width: 300px;
        }

        .label-to-contact {
            display: inline-block;
            font-weight: bold;
            border-left: 2px solid #eee;
            border-right: 2px solid #eee;
            padding: 5px;
            width: 300px;
        }

        .label-to-customer {
            font-weight: bold;
        }

        .label-to-address {
            display: inline-block;
            border: 2px solid #eee;
            padding: 5px;
            width: 300px;
        }

        .bottom-notes {
            display: inline-block;
            vertical-align: top;
            width: 330px;
        }

        .bottom-inter {
            display: inline-block;
            width: 70px;
        }

        .bottom-sign {
            display: inline-block;
            vertical-align: top;
            width: 220px;
            /* padding-top: 50px; */
            padding-top: 5px;
            font-weight: bold;
            border-bottom: 2px solid #000;
            text-align: center;
        }

        .bottom-position {
            display: inline-block;
            vertical-align: top;
            font-weight: bold;
            text-align: center;
        }

        .bsign {
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="6">
                    <div class="top-cont">
                        <table>
                            <tr>
                                <td class="title">
							<?php
							if(!$new_logo){
							?>
                                <img width="109" src="<?php echo $nbid_logo; ?>" alt="<?php echo _COMPANY_NAME; ?>" />
							<?php
							} else {
							?>
                                <div class="logo_address"><img src="<?php echo $nbid_logo; ?>" alt="<?php echo _COMPANY_NAME; ?>" /></div>
							<?php
							}
							?>
                                </td>
                                <td class="title">
                                    <span class="comp-box">Purchase Order</span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
            <tr class="information">
                <td colspan="6">
                    <table>
                        <tr>
                            <td>
							<?php
							if(!$new_logo){
							?>
                                <span class="company"><?=_COMPANY_NAME;?></span><br>
                                <span class="company-address">HEAD OFFICE<br/>
								Prima Orchard Trade Mall Bekasi Blok C5<br/>
								Jl. Raya Perjuangan No.1, Kota Bekasi 17121 – Jawa Barat<br/>
								Phone : (021) 8888 9442<br/>
								NPWP  : 73.253.177.7-407.000</span>
							<?php
							}
							?>
                            </td>
                            <td>
                                <span class="label-box">PO No</span><span class="inter">:</span><span class="info-box"><?= $rs->po; ?></span><br />
                                <span class="label-box">Date</span><span class="inter">:</span><span class="info-box"><?= date('F j, Y', $rs->date); ?></span><br />
                                <span class="label-box">Currency</span><span class="inter">:</span><span class="info-box"><?= $rs->currency; ?></span><br />
                                <span class="label-box">Delivery Date</span><span class="inter">:</span><span class="info-box"><?= date('F j, Y', $rs->delivery_date); ?></span><br />
                                <span class="label-box">Page</span><span class="inter">:</span><span class="info-box">1 of 1</span><br />
                                <span class="label-box">SPK</span><span class="inter">:</span><span class="info-box"><?= $rs->spk; ?></span><br />
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="information">
                <td colspan="6">
                    <table>
                        <tr>
                            <td>
                                <span class="label-to">Purchase To :</span><br />
                                <span class="label-to-contact">SID : <?= isset($rs->supplier_code) ? $rs->supplier_code : ''; ?></span><br />
                                <span class="label-to-contact">PIC : <?= isset($rs->supplier_pic) ? $rs->supplier_pic : ''; ?></span><br />
                                <span class="label-to-address"><?= isset($rs->supplier_address) ? nl2br($rs->supplier_address) : ''; ?></span>
                            </td>
                            <td style="text-align: left;">
                                <span class="label-to">Delivery To :</span><br />
                                <span class="label-to-contact">NBID Warehouse : <?= isset($rs->warehouse_description) ? $rs->warehouse_description : ''; ?></span><br />
                                <span class="label-to-address">
                                    <?= isset($rs->warehouse_address) ? nl2br($rs->warehouse_address) : ''; ?><br>
                                    <?= isset($rs->warehouse_address) ? nl2br($rs->warehouse_province) : ''; ?><br>
                                    <?= isset($rs->warehouse_address) ? nl2br($rs->warehouse_city) : ''; ?><br>
                                    <?= isset($rs->warehouse_kodepos) ? nl2br($rs->warehouse_kodepos) : ''; ?>
                                </span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="12"><span class="label-quote">Please supply the following items:</span><span class="inter">:</span><span class="info-quote"><?= $rs->description; ?></span></td>
            </tr>
            <tr>
                <td colspan="6"></td>
            </tr>
            <tr class="heading" style="width: 100%;">
                <td>No</td>
                <!-- <td style="text-align:left;">Item Code</td> -->
                <td style="text-align:left;">Description</td>
                <td style="text-align:center;">Unit</td>
                <td style="text-align:center;">Qty</td>
                <td>Unit Price</td>
                <td>Amount</td>
            </tr>
            <?= $rs2[0]; ?>
            <?php
            if ($rs->worth > 0) {
            ?>
                <tr class="total">
                    <td colspan="4"></td>
                    <td>
                        Grand Total
                    </td>
                    <td>
                        <?= number_format($rs->worth, 0, ',', '.'); ?>
                    </td>
                </tr>
            <?php
            }
            ?>
            <tr>
                <td colspan="6"></td>
            </tr>
            <tr>
                <td colspan="6"></td>
            </tr>
            <tr>
                <td colspan="4">
                    <span class="bottom-notes">Remarks :<br><?= nl2br($rs->remark); ?></span>
                    <span class="bottom-inter"></span>
                </td>
            </tr>
            <tr>
                <td colspan="4">
                    <span class="bottom-notes">Supplier Confirmation :<br>
                        We acknowledge receipt of this purchase order and confirm our compliance with the detail and other terms and condition behind this page
                    </span> <br>
                    <span class="label-box-bottom">Name</span><span class="inter">:</span><span class="info-box">____________________</span><br />
                    <span class="label-box-bottom">Date</span><span class="inter">:</span><span class="info-box">____________________</span><br />
                    <span class="label-box-bottom">Sign</span><span class="inter">:</span><span class="info-box" style="margin-top: 40px;">____________________</span><br />

                </td>
                <td colspan="2" class="bsign">
                    <span class="bottom-sign"><?= _COMPANY_NAME; ?><br /><br /><br /><br /><br /><br />Anung Wicaksono</span><br />
                    <span class="bottom-position">Direktur Utama</span>
                </td>
                
            </tr>
            <tr>
                <td colspan="12">
                    <span style="font-size: smaller;">
                        -The PO Number must appear on Delivery Order and Invoice. <br>
                        -For the sake of optimal communication, please sign our purchase order and return to us by email.
                    </span>
                </td>
            </tr>
        </table>
    </div>

    <div class="pagebreak invoice-box">
        <td class="title">
            <span class="comp-box">Term and Condition</span>
        </td> <br>
        <?= nl2br($rs->term_condition); ?>
    </div>

</body>

</html>