<!DOCTYPE html>
<html>
<link rel="stylesheet" type="text/css" href="<?php echo assets_path("assets/invoice.css"); ?>">
<?php $inr_icon = '<img src="' . assets_path('assets/indian-rupee.svg') . '" width="5" heignt="5"/>';
$discount_key = (!$is_draft_order) ? "discount_allocations" : "applied_discount";
$meta_fields = $updated_data->meta_fields;
$financial_status = $updated_data->financial_status;
$is_payment_pending = $financial_status == "pending" ? true : false;
$order_id =  $updated_data->id;
?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <title>TapMO Invoice</title>
</head>

<body>
    <div class="template-invoice-bold">
        <div class="wrapper">
            <table class="top-banner">
                <tr>
                    <td>
                        <div>
                            <span class="font-bold">GSTIN:</span> 09APDPT5973R1ZP
                        </div>
                    </td>
                    <td>
                        <div class="font-bold">
                            <span> <?= !$is_draft_order && !$is_payment_pending ? "Tax Invoice" : "Proforma Invoice"  ?> </span>
                        </div>
                    </td>
                    <td>
                        <div class="font-bold">
                            <?= $is_original ? "Original For Recipient" : "Duplicate For Supplier" ?>
                        </div>
                    </td>

                </tr>
            </table>

            <!-- End Top banner  -->
            <div class="store-brand">
                <img style="width: 200px;" src="https://card.tapmo.in/assets/uploads/logos/logo_tapmo.png" alt="TapMo India">
                <table class="store-contact">
                    <th>
                    <td>
                        <p>support@tapmo.in</p>
                    </td>
                    <td>
                        <p>https://tapmo.in/</p>
                    </td>
                    <td>
                        <p>08527811831</p>
                    </td>
                    </th>
                </table>
            </div>
            <!-- Start Invoice Meta details  -->
            <div class="invoice-meta text-left">
                <table>
                    <tr>
                        <td width="33.33%">
                            <?php if (!$is_draft_order && !$is_payment_pending) { ?>
                                <p>
                                    <span>

                                        Invoice No:</span> #TapMo<?= $invoice_number  ?>
                                </p>

                            <?php } ?>

                            <?php if ($is_payment_pending) { ?>
                                <p>
                                    <span> Proforma Invoice No: </span> <?= $order_id ?>
                                </p>

                            <?php } ?>

                            <p>
                                <span><?= !$is_draft_order ? "Order Id" : "Proforma Invoice No"  ?>:</span> <?= $order->name ?>
                            </p>
                            <p>
                                <span>Invoice Date:</span> <?= data_time_formet($order->created_at) ?>
                            </p>
                            <?php if (!$is_draft_order && !$is_payment_pending) { ?>
                                <p>
                                    <span>Payment:</span> <?= paymentMode($order->payment_gateway_names) ?>
                                </p>
                            <?php } ?>
                        </td>
                        <td width="33.33%">
                            <p>
                                <span>Place of supply</span>

                            </p>
                            <?php if (isset($updated_data->shipping_address)) { ?>
                                <?= $updated_data->shipping_address->city ?> , <?= $updated_data->shipping_address->province ?>
                            <?php } ?>
                            <p>

                            </p>
                        </td>
                        <td width="33.33%">
                            <p>
                                <span>Account Number:</span> 0200102000036652
                            </p>
                            <p>
                                <span>Account Name:</span> TapMo India
                            </p>
                            <p>
                                <span>IFSC Code:</span> IBKL0000200
                            </p>
                            <p>
                                <span>Bank Name:</span> IDBI Bank Limited
                            </p>
                            <p>
                                <span>Bank Branch:</span> Noida Sector 63.
                            </p>
                        </td>

                    </tr>

                </table>
            </div>
            <!-- End Invoice Meta details  -->
            <!-- Start Invoice details  -->
            <table class="invoice-details-head">
                <thead>
                    <tr class="invoice-detail-tr">
                        <th class="bg-gray" style="border-left: none!important;">
                            <span>Bill to</span>
                        </th>
                        <th class="bg-gray" style="border-left: none!important;">
                            <span>Ship to</span>
                        </th>
                        <th class="bg-gray" style="border-left: none!important;">
                            <span>Supplier</span>
                        </th>
                    </tr>
                </thead>
            </table>
            <div class="invoice-details">
                <table>
                    <tr>
                        <td>
                            <!-- Billing address  -->
                            <div>
                                <table class="sublime invoice-address">
                                    <tbody>
                                        <?php
                                        if (isset($meta_fields->billing_address) && $meta_fields->billing_address !== "") {  ?>
                                            <tr>

                                                <td> <?= $meta_fields->billing_address ?> </td>
                                            </tr>

                                        <?php } else if (isset($updated_data->billing_address)) { ?>

                                            <tr>
                                                <td> <?= $updated_data->billing_address->first_name  ?> <?= $updated_data->billing_address->last_name  ?> </td>
                                            </tr>
                                            <tr>
                                                <td> <?= $updated_data->billing_address->address1  ?> </td>
                                            </tr>
                                            <tr>
                                                <td> <?= $updated_data->billing_address->address2 ?> </td>
                                            </tr>
                                            <tr>
                                                <td> <?= $updated_data->billing_address->city  ?>,<?= $updated_data->billing_address->province  ?>, <?= $updated_data->billing_address->zip  ?>, <?= $order->billing_address->country  ?> </td>
                                            </tr>
                                            <tr>
                                                <td> <?= $updated_data->billing_address->phone  ?> </td>
                                            </tr>
                                        <?php } ?>

                                        <?php
                                        if (isset($order->customer)) {
                                        ?>
                                            <tr>
                                                <td> <?= $order->customer->email ?> </td>
                                            </tr>
                                        <?php



                                        }

                                        ?>


                                        <?php
                                        if (isset($meta_fields->gst_company_name) && $meta_fields->gst_company_name !== "") {
                                        ?>
                                            <tr>

                                                <td>
                                                    <span class="font-black">Company Name:</span> <?= $meta_fields->gst_company_name ?>
                                                </td>
                                            </tr>
                                        <?php } ?>

                                        <?php
                                        if (isset($meta_fields->gst_number) && $meta_fields->gst_number !== "") {
                                        ?>
                                            <tr>

                                                <td>
                                                    <span class="font-black">GSTIN:</span> <?= $meta_fields->gst_number ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </td>
                        <td>
                            <!-- Shipping address  -->
                            <?php if (isset($updated_data->shipping_address)) { ?>
                                <div>
                                    <table class="sublime invoice-address">
                                        <tbody>


                                            <tr>
                                                <td> <?= $updated_data->shipping_address->first_name  ?> <?= $updated_data->shipping_address->last_name  ?> </td>
                                            </tr>
                                            <tr>
                                                <td> <?= $updated_data->shipping_address->address1  ?> </td>
                                            </tr>
                                            <tr>
                                                <td> <?= $updated_data->shipping_address->address2 ?> </td>
                                            </tr>
                                            <tr>
                                                <td> <?= $updated_data->shipping_address->city  ?>, <?= $updated_data->shipping_address->province  ?>, <?= $updated_data->shipping_address->zip  ?>, <?= $order->shipping_address->country  ?> </td>
                                            </tr>
                                            <tr>
                                                <td><?= $updated_data->shipping_address->phone ?> </td>
                                            </tr>
                                            <?php
                                            if (isset($order->customer)) {
                                            ?>
                                                <tr>
                                                    <td> <?= $order->customer->email ?> </td>
                                                </tr>
                                            <?php



                                            }

                                            ?>

                                        </tbody>
                                    </table>
                                </div>
                            <?php } ?>
                        </td>
                        <td>
                            <!-- Supplier address  -->
                            <div>
                                <table class="sublime invoice-address">
                                    <tbody>
                                        <tr>
                                            <td> TapMo India </td>
                                        </tr>
                                        <tr>
                                            <td> H1a/20 Basement Front of H-53 Sector 63 Noida </td>
                                        </tr>
                                        <tr>
                                            <td> Tel. 08527811831 </td>
                                        </tr>
                                        <tr>
                                            <td> support@tapmo.in </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="font-black">GSTIN:</span> 09APDPT5973R1ZP
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
            <!-- End Invoice details  -->
            <!-- Start Addresses  -->
            <!-- End Addresses  -->
            <!-- Start Product Table  -->
            <div class="product-table">
                <table class="styled-table">
                    <thead class="border-x-none">
                        <tr>
                            <th width="18%">Item</th>
                            <th>Qty</th>
                            <th>Rate</th>
                            <th>Discount</th>
                            <th>Taxable Val</th>
                            <th>HSN</th>
                            <th>CGST</th>
                            <th>SGST</th>
                            <th>IGST</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>


                        <?php

                        $totalSumCGST = 0;
                        $totalSumSGST = 0;
                        $totalSumIGST = 0;
                        $total_taxable_val = 0;
                        $total_items_price_with_gst = 0;
                        $total_item_price_with_discount_and_gst = 0;
                        $total_item_discount = 0;

                        foreach ($order->line_items as $line_item) { ?>

                            <?php
                            $gst_data = getGst($line_item->tax_lines);
                            $gstObj = $gst_data['gst'];
                            $total_gst = $gst_data['total_gst'];
                            $CGST = $gstObj->CGST;
                            $SGST = $gstObj->SGST;
                            $IGST = $gstObj->IGST;
                            $total_line_discount = get_total_line_discount($line_item->{$discount_key});
                            $line_total_val      = get_line_total_val($line_item->price, $line_item->quantity, $total_line_discount);
                            $taxable_val         = get_taxable_val($line_total_val, $total_gst);
                            $total_taxable_val   = $total_taxable_val  + $taxable_val;
                            $totalSumCGST = (!empty($CGST) ? $CGST->price : 0) + $totalSumCGST;
                            $totalSumSGST = (!empty($SGST) ? $SGST->price : 0) + $totalSumSGST;
                            $totalSumIGST = (!empty($IGST) ? $IGST->price : 0) + $totalSumIGST;
                            $total_items_price_with_gst = $total_items_price_with_gst + $line_total_val;
                            $total_item_price_with_discount_and_gst = $total_item_price_with_discount_and_gst + $line_item->price;
                            ?>
                            <tr>
                                <td> <?= $line_item->name ?></td>
                                <td><?= $line_item->quantity ?></td>
                                <td><?= $inr_icon ?> <?= $line_item->price ?> </td>
                                <td> <?= $inr_icon ?> <?= get_total_line_discount($line_item->{$discount_key}) ?></td>
                                <td><?= $inr_icon ?> <?= $taxable_val ?></td>
                                <td>94032090</td>
                                <td><?= !empty($CGST) ? "(" . $CGST->rate . "%) <div>" . $inr_icon . $CGST->price . "</div>" : ""; ?> </td>
                                <td><?= !empty($SGST) ? "(" . $SGST->rate . "%) <div>" . $inr_icon . $SGST->price . "</div>" : ""; ?> </td>
                                <td><?= !empty($IGST) ? "(" . $IGST->rate . "%) <div>" . $inr_icon . $IGST->price . "</div>" : ""; ?> </td>
                                <td><?= $inr_icon ?> <?= $line_total_val  ?></td>
                            </tr>
                        <?php } ?>
                        <!-- Render each item in order  -->

                        <!-- END Render each item in order  -->
                        <!-- Total row -->
                    </tbody>
                    <tfoot class="gray-border-top font-black">
                        <?php $totalGstData = getGst($order->tax_lines);
                        $totalGstObj = $totalGstData['gst'];
                        $totalGstValue = $totalGstData['total_gst'];
                        $totalGstRate  = $totalGstData['total_gst_rate'];
                        $totalCGST = $totalGstObj->CGST;
                        $totalSGST = $totalGstObj->SGST;
                        $totalIGST = $totalGstObj->IGST;
                        $total_gst_amount =  $totalSumCGST + $totalSumSGST + $totalSumIGST;
                        //    $taxable_val  = get_taxable_val($order->total_price,$totalGstValue); 

                        ?>
                        <tr>
                            <th colspan="1" class="bg-gray no-border gray-border-x" style="border-left: none!important;"> Total </th>
                            <td>
                                <!-- QTY (Should be empty) -->
                            </td>
                            <td>
                                <!-- Total Rate  --> <?= $inr_icon ?> <?= $total_item_price_with_discount_and_gst ?>
                            </td>
                            <td>
                                <!-- Total Discount    //$order->total_discounts --> <?= $inr_icon ?> <?= !$is_draft_order ? $order->total_discounts : draft_order_discount_cost($order->applied_discount)  ?>
                            </td>
                            <td>
                                <!-- Total Taxable Val  --> <?= $inr_icon ?> <?= $total_taxable_val ?>
                            </td>
                            <td>
                                <!-- HSN(Should be empty) -->
                            </td>

                            <td><?= !empty($totalCGST) ? $inr_icon . $totalSumCGST : ""; ?> </td>
                            <td><?= !empty($totalSGST) ? $inr_icon . $totalSumSGST : ""; ?> </td>
                            <td><?= !empty($totalIGST) ? $inr_icon . $totalSumIGST : ""; ?> </td>
                            <td>
                                <!-- Total TOTAL! --> <?= $inr_icon ?> <?= $total_items_price_with_gst ?>
                            </td>
                        </tr>
                    </tfoot>
                    <!-- End Total row -->
                </table>
                <div class="gray-border-bottom"></div>
            </div>
            <!-- End Product Table  -->
            <!-- Start total section -->
            <table class="total">
                <tr>
                    <td style="width:45% ;">
                        <div>
                            <?php if (!$is_draft_order && !$is_payment_pending && isset($order->processing_method)) { ?>
                                <div><span>Payment Mode:</span></div>
                                <div>
                                    <spen class="uppercase"> <?= $order->processing_method ?> </spen>
                                </div>
                            <?php } ?>

                            <div style="margin-top: 10;margin-bottom: 5;"><span>Terms and Conditions apply</span></div>
                            <div class="gray-border-bottom"></div>
                            <div style="margin-top: 5;"><span>Amount in words</span></div>
                            <div class="uppercase gray-border-bottom"> <?= getIndianCurrency($order->total_price) ?> </div>
                            <div class="font-bold" style="margin-top: 5;"> E. &amp; O.E </div>
                        </div>
                    </td>
                    <td style="width:15% ;">

                    </td>
                    <td style="width:40% ;">
                        <div>
                            <table class="total-table">
                                <tbody>
                                    <tr>
                                        <td> Product Amount: </td>
                                        <td class="text-right"> <?= $inr_icon ?> <?= $total_taxable_val ?> </td>
                                    </tr>
                                    <tr>
                                        <td> GST (<?= $totalGstRate ?>%): </td>
                                        <td class="text-right"> <?= $inr_icon ?> <?= $total_gst_amount ?> </td>
                                    </tr>
                                    <tr>
                                        <td> Other: </td>
                                        <td class="text-right"> <?= $inr_icon ?> <?= !$is_draft_order ? $order->total_shipping_price_set->shop_money->amount : draft_order_shipping_cost($order->shipping_line)  ?></td>
                                    </tr>
                                    <tr>
                                        <td style="padding-top:10px;"></td>
                                    </tr>
                                    <tr class="font-bold">
                                        <td class="bg-gray" style="margin-right: 10px"> Total </td>
                                        <td class="text-right"> <?= $inr_icon ?> <?= !$is_draft_order ?  $order->current_total_price : $order->total_price ?> </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>
                <!-- Left col -->

                <!-- Right col  -->

            </table>
            <!-- End total section -->
            <footer style="margin:0;">
                <div class="gray-border-bottom"></div>

                <div style="margin:0px 10px;">
                    <table class="total-table" style="margin-top: 10px;">
                        <tbody>
                            <tr class="font-bold">
                                <td> Signature </td>
                                <td class="text-right">
                                    <img style="margin-left: auto; max-height: 60px; width:auto; " alt="TapMo India" src="https://gst-invoice-app.s3.amazonaws.com/logos/edd140e0-1426-11ed-8155-2b29a8bdb829.jpeg">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </footer>
        </div>
    </div>
</body>

</html>