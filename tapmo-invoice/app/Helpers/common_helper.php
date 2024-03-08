<?php

use CodeIgniter\I18n\Time;

function data_time_formet($string)
{
    $time = Time::parse($string);
    return $time->toLocalizedString('MMM d, yyyy'); // March 9, 2016
}

function paymentMode($list)
{
    if (isset($list) && count($list) > 0) {
        if (count($list) > 1) {
            return  implode(",", $list);
        }
        return $list[0];
    }
    return '';
}

function getIndianCurrency(float $number)
{
    $decimal = round($number - ($no = floor($number)), 2) * 100;
    $hundred = null;
    $digits_length = strlen($no);
    $i = 0;
    $str = array();
    $words = array(
        0 => '', 1 => 'one', 2 => 'two',
        3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six',
        7 => 'seven', 8 => 'eight', 9 => 'nine',
        10 => 'ten', 11 => 'eleven', 12 => 'twelve',
        13 => 'thirteen', 14 => 'fourteen', 15 => 'fifteen',
        16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen',
        19 => 'nineteen', 20 => 'twenty', 30 => 'thirty',
        40 => 'forty', 50 => 'fifty', 60 => 'sixty',
        70 => 'seventy', 80 => 'eighty', 90 => 'ninety'
    );
    $digits = array('', 'hundred', 'thousand', 'lakh', 'crore');
    while ($i < $digits_length) {
        $divider = ($i == 2) ? 10 : 100;
        $number = floor($no % $divider);
        $no = floor($no / $divider);
        $i += $divider == 10 ? 1 : 2;
        if ($number) {
            $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
            $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
            $str[] = ($number < 21) ? $words[$number] . ' ' . $digits[$counter] . $plural . ' ' . $hundred : $words[floor($number / 10) * 10] . ' ' . $words[$number % 10] . ' ' . $digits[$counter] . $plural . ' ' . $hundred;
        } else {
            $str[] = null;
        }
    }
    $Rupees = implode('', array_reverse($str));
    // $paise = ($decimal > 0) ? "." . ($words[$decimal / 10] . " " . $words[$decimal % 10]) . ' Paise' : '';
    return ($Rupees ? $Rupees . 'Rupees ' : '') . "ONLY";
    //. $paise;
}


function getGst($tax_lines)
{

    $gst =  (object) ['CGST' => [], "SGST" => [], "IGST" => []];

    $total_gst = 0;
    $total_gst_rate = 0;
    foreach ($tax_lines as $taxDetails) {
        $value = new stdClass;
        $value->rate = $taxDetails->rate * 100;
        $value->price = $taxDetails->price;
        if (empty($gst->{$taxDetails->title})) {
            $total_gst_rate = $total_gst_rate + $value->rate;
        }
        $gst->{$taxDetails->title} = $value;
        $total_gst = $total_gst + (float)$taxDetails->price;
    }
    return ['gst' => $gst, "total_gst" => $total_gst, "total_gst_rate" => $total_gst_rate];
}


function get_total_line_discount($discount_allocations)
{

    $discount = 0;
    if (isset($discount_allocations)) {
        foreach ($discount_allocations as $discounts_value) {
            $discount = $discount + (float)$discounts_value->amount;
        }
    }

    return $discount;
}

function get_line_total_val($line_price, $line_quantity, $discount)
{

    return ((float)$line_price * (float)$line_quantity) - $discount;
}

function get_taxable_val($line_price, $total_gst)
{

    return  (float)$line_price - $total_gst;
}

function draft_order_discount_cost($applied_discount)
{
    $discount = 0;
    if (isset($applied_discount)) {
        foreach ($applied_discount as $line_discount) {
            $discount = $discount + (float)$applied_discount->amount;
        }
    }
    return $discount;
}

function draft_order_shipping_cost($shipping_line)
{
    $shipping_price = 0;
    if (isset($shipping_lines)) {
        foreach ($shipping_lines as $shipping_line) {
            $shipping_price = $shipping_price + (float)$shipping_line->price;
        }
    }
    return $shipping_price;
}


function verifyRequest($request, $secret)
{
    // Per the Shopify docs:
    // Everything except hmac and signature...
    $hmac = $request['hmac'];
    unset($request['hmac']);
    unset($request['signature']);

    // Sorted lexilogically...
    ksort($request);

    // Special characters replaced...
    foreach ($request as $k => $val) {
        $k = str_replace('%', '%25', $k);
        $k = str_replace('&', '%26', $k);
        $k = str_replace('=', '%3D', $k);
        $val = str_replace('%', '%25', $val);
        $val = str_replace('&', '%26', $val);
        $request[$k] = $val;
    }

    // Hashed per hmac standards, using sha256 and the shared secret
    $test = hash_hmac('sha256', http_build_query($request), $secret);

    // Verified when equal
    return $hmac === $test;
}



/**
 * Builds an http query string.
 * @param array $query  // of key value pairs to be used in the query
 * @return string       // http query string.
 **/
function build_http_query($query)
{

    $query_array = array();

    foreach ($query as $key => $key_value) {

        $query_array[] = urlencode($key) . '=' . urlencode($key_value);
    }

    return implode('&', $query_array);
}

function downloadInvoiceById($array, $orderId)
{
    $array['id'] = $orderId;
    $param = build_http_query($array);
    return base_url("download_invoice_by_id?{$param}");
}

function shopifyPagination($links, $is_draft_order = false)
{
    $link_array = array();

    //Create variables for the new page infos
    $prev_link = '';
    $next_link = '';

    //Check if there's more than one links / page infos. Otherwise, get the one and only link provided
    if (strpos($links, ',')  !== false) {
        $link_array = explode(',', $links);
    } else {
        $link = $links;
    }


    //Check if the $link_array variable's size is more than one
    if (sizeof($link_array) > 1) {
        $prev_link = $link_array[0];

        $param = parse_url($prev_link);
        parse_str($param['query'], $prev_link);
        $prev_link = $prev_link['page_info'];

        $next_link = $link_array[1];

        $param = parse_url($next_link);
        parse_str($param['query'], $next_link);

        $next_link = $next_link['page_info'];
    } else {
        if (strpos($link, "previous") !== false) {
            $prev_link = $link;

            $param = parse_url($prev_link);
            parse_str($param['query'], $prev_link);

            $prev_link = $prev_link['page_info'];

            $next_link = "";
        } else {
            $next_link = $link;

            $param = parse_url($next_link);
            parse_str($param['query'], $next_link);

            $next_link = $next_link['page_info'];

            $prev_link = "";
        }
    }
    if ($prev_link) {
        $prev_link = str_replace('>; rel="previous"', "", $prev_link);
        // $prev_link = base_url("order_list?page_info=".$prev_link);
    }
    if ($next_link) {
        $next_link = str_replace('>; rel="next"', "", $next_link);
        // $next_link = base_url("order_list?page_info=".$next_link);
    }

    return array("prevLink" => $prev_link, "nextLink" => $next_link);
}

function array_to_pagination_url($data, $page_info, $is_draft_order)
{
    $data["page_info"] = $page_info;
    $uri_path = "order_list" . ($is_draft_order ? "/is_draft" : "") . "?";
    return base_url($uri_path . http_build_query($data));
}


function assets_path($uri_path)
{
    $ASSETS_BASE_PATH = $_ENV['ASSETS_BASE_PATH'];
    return  $ASSETS_BASE_PATH . $uri_path;
}

function tabLink($is_draft_order = false)
{
    $data = $_GET;
    unset($data['page_info']);
    unset($data['start_date']);
    unset($data['end_date']);
    unset($data['limit']);
    unset($data['original']);

    $uri_path = "order_list" . ($is_draft_order ? "/is_draft" : "") . "?";
    return base_url($uri_path . http_build_query($data));
}

function checkTabActive($is_draft_order = false)
{
    $path = strtok($_SERVER["REQUEST_URI"], '?');
    if ($is_draft_order && $path  == "/order_list/is_draft" || !$is_draft_order && $path  == "/order_list") {
        return "active";
    } else "";
}
