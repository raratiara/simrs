<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Purchase Order # <?=$rs->po;?></title>
    
    <style>
    .invoice-box {
        max-width: 800px;
        margin: auto;
        padding: 30px;
        border: 1px solid #eee;
        box-shadow: 0 0 10px rgba(0, 0, 0, .15);
        font-size: 16px;
        line-height: 24px;
        font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
        color: #555;
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
        padding-bottom: 20px;
    }
    
    .invoice-box table tr.top table td.title {
        font-size: 45px;
        line-height: 45px;
        color: #333;
    }
    
    .invoice-box table tr.information table td {
        padding-bottom: 40px;
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
    
    .invoice-box table tr.total td:nth-child(2), .invoice-box table tr.total td:nth-child(3) {
        /*border-top: 2px solid #eee;*/
        text-align: right;
        font-weight: bold;
    }
    
    .invoice-box table tr.sign td:first-child, .invoice-box table tr.sign td:nth-child(3), .invoice-box table tr.sign td:last-child {
        text-align: center;
        width: 150px;
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
    </style>
</head>

<body>
    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="5">
                    <table>
                        <tr>
                            <td class="title">
                                <!--img src="https://www.sparksuite.com/images/logo.png" style="width:100%; max-width:300px;"-->
								Logo
                            </td>
                            <td>
                                Purchase Order #: <?=$rs->po;?><br>
                                Order Date: <?=date('d',$rs->date_order).' '._BLN[date('m',$rs->date_order)-1].' '.date('Y',$rs->date_order);?><br>
                                Delivery Date: <?=date('d',$rs->date_delivery).' '._BLN[date('m',$rs->date_delivery)-1].' '.date('Y',$rs->date_delivery);?><br>
                                Term of Payment: <?=$rs->payment_term;?><br>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="information">
                <td colspan="5">
                    <table>
                        <tr>
                            <td>
                                <?=_COMPANY_NAME;?><br>
                                Bekasi
                            </td>
                            <td>
                                <?=$rs->supplier_name;?><br>
                                <?=wordwrap($rs->supplier_address,40,"<br />");?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>            
            <tr class="heading">
                <td>
                    Item
                </td>
                <td>
                    Qty
                </td>
                <td>
                    Price
                </td>
                <td>
                    Disc
                </td>
                <td>
                    Sub Total
                </td>
            </tr>
            <?=$rs2[0];?>
            <tr class="total">
                <td colspan="2"><?=$rs->includetax;?></td>
                <td colspan="2">
                   Discount<?=$rs->txdisc;?>:
                </td>
                <td>
                   <?=number_format($rs->txvdisc,2,',','.');?>
                </td>
            </tr>
            <tr class="total">
                <td colspan="2"></td>
                <td colspan="2">
                   Ppn (<?=$rs->vtax;?> %):
                </td>
                <td>
                   <?=number_format($rs->tax,2,',','.');?>
                </td>
            </tr>
            <tr class="total">
                <td colspan="2"></td>
                <td colspan="2">
                   Expedition Cost:
                </td>
                <td>
                   <?=number_format($rs->trans_cost,2,',','.');?>
                </td>
            </tr>
            <tr class="total">
                <td colspan="2"></td>
                <td colspan="2">
                   Total:
                </td>
                <td>
                   <?=number_format($rs->nett_total,2,',','.');?>
                </td>
            </tr>
            <tr>
                <td colspan="5"></td>
            </tr>
            <tr>
                <td colspan="5">Terbilang : <?=$rs->terbilang;?></td>
            </tr>
            <tr>
                <td colspan="5"></td>
            </tr>
            <tr>
                <td colspan="5"></td>
            </tr>
            <tr>
                <td colspan="5"></td>
            </tr>
            <tr class="sign">
                <td>Created By</td>
                <td></td>
                <td>Approveded By</td>
                <td></td>
                <td>Supplier By</td>
            </tr>
            <tr>
                <td colspan="5"></td>
            </tr>
            <tr>
                <td colspan="5"></td>
            </tr>
            <tr class="sign">
                <td>( .................... )</td>
                <td></td>
                <td>( .................... )</td>
                <td></td>
                <td>( .................... )</td>
            </tr>
        </table>
    </div>
</body>
</html>