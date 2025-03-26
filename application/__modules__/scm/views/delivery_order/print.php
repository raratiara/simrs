<!doctype html>
<?php
$mk_currenttime  = mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y"));
if($rs->date > _CUT_OFF_LOGO){
	//$nbid_logo = _ASSET_LOGO_2022;
	$nbid_logo = _ASSET_LOGO_2022_INSIDE;
} else {
	$nbid_logo = _ASSET_LOGO;
}
?>
<html>
<head>
    <meta charset="utf-8">
    <title>Delivery Order # <?=$rs->id;?></title>
    
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
    
    .invoice-box table tr.top td:nth-child(2), .invoice-box table tr.information td:nth-child(2) {
        text-align: right;
    }
    
    .invoice-box table tr.top table td {
        padding-bottom: 0px;
    }
    
    .invoice-box table tr.top table td.title {
        /* font-size: 32px; */
        font-size: 14px;
        line-height: 45px;
        color: #333;
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
    
    .invoice-box table tr.item td{
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
		display:inline-block;
		width: 180px;
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
		display:inline-block;
		font-weight: bold;
		vertical-align:top;
		width: 85px;
        text-align: left;
	}
	
	.info-box {
		display:inline-block;
		width: 120px;
        text-align: left;
	}

	.label-quote {
		display:inline-block;
		vertical-align:top;
		width: 100px;
	}

	.inter {
		display:inline-block;
		vertical-align:top;
		width: 20px;
        text-align: center;
	}
	
	.info-quote {
		display:inline-block;
		font-weight: bold;
		width: 83%;
	}
	
	.label-to {
		display:inline-block;
		background: #eee;
		font-weight: bold;
		border: 2px solid #eee;
		padding: 5px;
		width: 300px;
	}

	.label-to-contact {
		display:inline-block;
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
		display:inline-block;
		border: 2px solid #eee;
		padding: 5px;
		width: 300px;
	}
	
	.bottom-notes {
		display:inline-block;
		vertical-align:top;
		width: 340px;
	}
	
	.bottom-inter {
		display:inline-block;		
		width: 60px;
	}
	
	.bottom-sign {
		display:inline-block;		
		vertical-align:top;
		width: 220px;
		padding-top: 15px;
		font-weight: bold;
		border-bottom: 2px solid #000;
		text-align: center;
	}

	.bottom-position {
		display:inline-block;		
		vertical-align:top;
		font-weight: bold;
		text-align: center;
	}

	.bottom-qrcode {
		display:inline-block;		
		vertical-align:top;
		width: 220px;
		font-size: 8px;
		padding-top: 10px;
		text-align: left;
	}

	.txt-qrcode {
		font-size: 8px;
		margin-top: -10px;
		padding-left: 20px;
	}
	
	.terbilang {
		display:inline-block;
		margin-top: 5px;
		padding-top: 3px;
		padding-bottom: 3px;
		border-top: 2px solid #eee;
		border-bottom: 2px solid #eee;
		width:100%;
		font-weight: bold;
	}

	.payment-box {
		display:inline-block;
		font-size: 11px;
		margin-top: -5px;
		padding: 5px;
		border: 2px solid #eee;
		width: 100%;
	}

	.label-bank {
		display:inline-block;
		vertical-align:top;
		width: 50px;
	}
	
	.bsign{
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
                                <img width="109" src="<?php echo $nbid_logo; ?>" alt="<?php echo _COMPANY_NAME; ?>" />
                            </td>
                            <td class="title">
                                <span class="comp-box">DELIVERY ORDER</span>
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
                                <span class="company"><?=_COMPANY_NAME;?></span><br>
                                <span class="company-address">HEAD OFFICE<br/>
								Prima Orchard Trade Mall Bekasi Blok C5<br/>
								Jl. Raya Perjuangan No.1, Kota Bekasi 17121 – Jawa Barat<br/>
								Phone : (021) 8888 9442<br/>
								NPWP  : 73.253.177.7-407.000</span>
                            </td>
                            <td>
                                <span class="label-box">Date</span><span class="inter">:</span><span class="info-box"><?=date('F j, Y',$rs->date);?></span><br/>
                                <span class="label-box">DO#</span><span class="inter">:</span><span class="info-box"><?=$rs->do;?></span><br/>
                                <span class="label-box">Page</span><span class="inter">:</span><span class="info-box">1 of 1</span><br/>
                                <span class="label-box">PO#</span><span class="inter">:</span><span class="info-box"><?=$rs->po;?></span><br/>
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
								<span class="label-to">Ship to : <?=isset($rs->recipient_name)?$rs->recipient_name:'';?></span><br/>
                                <span class="label-to">Telp: <?=isset($rs->recipient_telp)?$rs->recipient_telp:'';?></span><br/>
                                <span class="label-to-address"><?=isset($rs->recipient_address)?nl2br($rs->recipient_address):'';?></span>
                            </td>
                            <td>
                                <!-- empty -->
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>            
            <tr>
                <td colspan="6"></td>
            </tr>
            <tr class="heading">
                <td>No</td>
                <td style="text-align:center;">Item Code</td>
                <td style="width:200px;text-align:center;">Description</td>
                <td style="text-align:center;">Satuan</td>
                <td style="text-align:center;">Qty</td>
                <td style="text-align:center;">Coly</td>
                <td style="text-align:center;">Remark</td>
            </tr>
            <?=$rs2[0];?>
			
            <tr>
                <td colspan="6"></td>
            </tr>

            <tr>
                <td colspan="12">
                <table style="border: 1px solid black; border-collapse: collapse; width: 100%;">
                    <tr>
                        <th style="border: 1px solid black; width: 25%">Received by</th>
                        <th style="border: 1px solid black; width: 25%">Shipped by</th>
                        <th style="border: 1px solid black; width: 25%">Prepared by</th>
                        <th style="border: 1px solid black; width: 25%">Authorized by</th>
                    </tr>
                    <tr>
                        <td style="border: 1px solid black; height: 100px; text-align: center; vertical-align: bottom;">(<?=isset($rs->received)?$rs->received:'';?>)</td>
                        <td style="border: 1px solid black; height: 100px; text-align: center; vertical-align: bottom;">(<?=isset($rs->shipped)?$rs->shipped:'';?>)</td>
                        <td style="border: 1px solid black; height: 100px; text-align: center; vertical-align: bottom;">(<?=isset($rs->prepared)?$rs->prepared:'';?>)</td>
                        <td style="border: 1px solid black; height: 100px; text-align: center; vertical-align: bottom;">(<?=isset($rs->authorized)?$rs->authorized:'';?>)</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid black;">Date:</td>
                        <td style="border: 1px solid black;">Date:</td>
                        <td style="border: 1px solid black;">Date:</td>
                        <td style="border: 1px solid black;">Date:</td>
                    </tr>
                    </table> 
                </td>
            </tr>  

            <tr>
                <td colspan="6"></td>
            </tr>

            <tr>
                <td colspan="12">
                <table style="border: 1px solid black; border-collapse: collapse; width: 100%;">
                    <tr>
                        <th style="border: 1px solid black;">Note:</th>
                    </tr>
                    <tr>
                        <td style="border: 1px solid black; height: 80px;"><?=isset($rs->note)?$rs->note:'';?></td>
                    </tr>
                    </table> 
                </td>
            </tr> 

            
        </table>
    </div>
</body>
</html>