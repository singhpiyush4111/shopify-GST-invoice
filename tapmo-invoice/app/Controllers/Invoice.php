<?php

namespace App\Controllers;

use App\Libraries\ShopifyLib;  //so we can easily load the custom library
use App\Libraries\DownloadInvoice;
use Spipu\Html2Pdf\Html2Pdf;
use CodeIgniter\HTTP\IncomingRequest;
use App\Models\SiteModel;
use App\Models\InvoiceAddress;
use CodeIgniter\HTTP\Files\UploadedFile;
use CodeIgniter\API\ResponseTrait;


//phpSpreadsheet library
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpParser\Node\Stmt\TryCatch;

class Invoice extends BaseController
{
    use ResponseTrait;
    public $data;
    public $shopify_lib;
    private $public_key = "";
    private $secret_key = "";
    private $access_Token = "";
    private $shop = "";
    protected $request;
    private $siteModel;
    private $invoiceAddress;
    private $session;
    private $downloadInvoice;

    public function __construct()
    {
        $db = db_connect();
        $this->siteModel = new SiteModel($db);
        $this->invoiceAddress = new InvoiceAddress($db);
        ini_set('memory_limit', '2048M');
        helper('common_helper');

        $this->public_key = $this->siteModel->get_app_key(SHOPIFY_APP_PUBLIC_KEY);
        $this->secret_key = $this->siteModel->get_app_key(SHOPIFY_APP_SECRET_KEY);
        $this->session     = \Config\Services::session();
        $this->request = service('request');
        $this->shopify_lib = new ShopifyLib();
        $this->downloadInvoice = new DownloadInvoice();
    }

    private function unAuthorized()
    {
        $get_params = $_GET;
        $get_params["path_info"] = $_SERVER['PATH_INFO'];
        $url = build_http_query($get_params);
        header("Location: " . base_url("?{$url}"));
        die;
    }

    public function getOrders()
    {
        return view('orderList');
    }

    public function getOrdersApi($is_draft = "is_draft_false")
    {
        $shop = $_GET['shop'];
        $is_draft_order = $is_draft == "is_draft" ? true : false;
        $shopData =  $this->siteModel->get("shop_url", $shop);
        $token = $shopData->access_Token;
        $since_id = 1;
        $pageInfoArray = ["prevLink" => "", "nextLink" => ""];
        $query = array(
            "Content-type" => "application/json", // Tell Shopify that we're expecting a response in JSON format
        );
        $limit = (!$is_draft_order) ? 10 : 50;

        if (isset($_GET['limit']) && empty($_GET['limit'])) {
            $limit = (int)$_GET['limit'];
        }
        $order_data = array("limit" => $limit);
        if (empty($_GET['page_info']) && !$is_draft_order) {
            $order_data["status"] = "any";
        } else if (isset($_GET['page_info'])) {
            $order_data["page_info"] = $_GET['page_info'];
        }
        if (!isset($_GET['page_info']) && isset($_GET['start_date'])) {
            $order_data["created_at_min"] = $_GET['start_date'];
        }
        if (!isset($_GET['page_info']) && isset($_GET['end_date'])) {
            $order_data["created_at_max"] = $_GET['end_date'];
        }

        // search by OrderID, Name, Amount, fullfilment Status
        if (!isset($_GET['page_info']) && isset($_GET['order_number'])) {
            $order_data["query"] = $_GET['order_number'];
        }

        $api_type = $is_draft_order ? "draft_orders" : "orders";



        $orders = $this->shopify_lib->shopify_call($token, $shop, "/admin/api/2023-10/" . $api_type . ".json", $order_data, 'GET');

        $response = $orders['response'];
        $header = $orders["headers"];

        if (isset($header['link'])) {
            $pageInfoArray = shopifyPagination($header['link'], $is_draft_order);
        }
        $data = array("orders" => json_decode($response)->{$api_type}, "pageInfoArray" => $pageInfoArray, "is_draft_order" => $is_draft_order);

        // header("Content-Security-Policy: data:; frame-ancestors {$shop} https://admin.shopify.com;");
        // return view('orderList', $data);  
        // echo "<pre>";
        // echo json_encode($data,JSON_PRETTY_PRINT);
        // $data = array("orders" => json_decode($response)->{$api_type}, "pageInfoArray" => $pageInfoArray, "is_draft_order" => $is_draft_order);
        header("Content-Security-Policy: data:; frame-ancestors {$shop} https://admin.shopify.com;");
        //Need to delete
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
        return $this->respond($data);
    }
    public function getOrderById($is_draft = "is_draft_false")
    {

        $is_draft_order = $is_draft == "is_draft" ? true : false;
        $shop = $_GET["shop"];
        $access_Token =  $this->siteModel->get("shop_url", $shop)->access_Token;
        $order_id = $this->request->getGet('id');
        $api_type = $is_draft_order ? "draft_orders" : "orders";
        $key_type = $is_draft_order ? "draft_order" : "order";
        $order = $this->shopify_lib->shopify_call($access_Token, $shop, "/admin/api/2022-10/" . $api_type . "/" . $order_id . ".json", array(), 'GET');

        $data = json_decode($order['response']);
        $get_by_order_id = $this->invoiceAddress->get_by_order_id($order_id);
        if (!isset($data->{$key_type}->billing_address) || empty($data->{$key_type}->billing_address)) {
            $data->{$key_type}->billing_address = $data->{$key_type}->shipping_address;
        }
        // if ($get_by_order_id) {
        //     if (!$is_draft_order) {
        //         $billing_address = array_merge((array)$data->{$key_type}->billing_address, (array)json_decode($get_by_order_id->billing_address));
        //         $shipping_address = array_merge((array)$data->{$key_type}->shipping_address, (array)json_decode($get_by_order_id->shipping_address));

        //         $data->order->billing_address = (object)$billing_address;
        //         $data->order->shipping_address = (object)$shipping_address;

        //         $data->order->custom_gst_details = json_decode($get_by_order_id->gst_details);
        //     }
        // }
        header("Content-Security-Policy: data:; frame-ancestors {$shop} https://admin.shopify.com;");

        return view('update_order_details', ["order" => $data->{$key_type}, "order_id" => $order_id, "is_draft_order" => $is_draft_order]);
    }

    public function downloadOrderById($is_draft = "is_draft_false", $orderId = false, $is_bulk = false)
    {
        $is_draft_order = $is_draft == "is_draft" ? true : false;
        if (!$is_bulk) {
            ob_end_clean();
        }
        ini_set('zlib.output_compression', 'Off');
        $api_type = $is_draft_order ? "draft_orders" : "orders";
        $key_type = $is_draft_order ? "draft_order" : "order";
        // $is_original   = isset($_GET['original']) && $_GET['original'] === "on" ? true : false;
        $is_original = true;
        $shop = $_GET["shop"];
        $updated_data = (object)[];
        $access_Token =  $this->siteModel->get("shop_url", $shop)->access_Token;
        $order_id = $orderId ? $orderId : $this->request->getGet('id');
        $order = $this->shopify_lib->shopify_call($access_Token, $shop, "/admin/api/2022-10/" . $api_type . "/" . $order_id . ".json", array(), 'GET');
        $metaDataRes =   $this->shopify_lib->shopify_call($access_Token, $shop, "/admin/api/2022-10/" . $api_type . "/" . $order_id . "/metafields.json", array(), 'GET');
        $metaData = json_decode($metaDataRes['response']);
        $data = json_decode($order['response']);

        if (!$is_draft_order) {
            $invoice_number = sprintf('%04d', ($data->{$key_type}->order_number - 1003));
        } else {

            $invoice_number =  str_replace("#", "", $data->{$key_type}->name);
        }
        if ((!isset($data->{$key_type}->billing_address) || empty($data->{$key_type}->billing_address)) && isset($data->{$key_type}->shipping_address) && !empty($data->{$key_type}->shipping_address)) {
            $data->{$key_type}->billing_address = $data->{$key_type}->shipping_address;
        }
        $updated_data = $data->{$key_type};
        $updated_data->meta_fields = (object) array();
        if (isset($metaData->metafields) && !empty($metaData->metafields)) {
            foreach ($metaData->metafields as $key => $metafield) {
                $updated_data->meta_fields->{$metafield->key} = $metafield->value;
            }
        }

        $dompdf = new \Dompdf\Dompdf(array("isRemoteEnabled" => true));
        $dompdf->loadHtml(view('invoice', ["order" => $data->{$key_type}, "updated_data" => $updated_data, "invoice_number" => $invoice_number, "is_draft_order" => $is_draft_order, "is_original" => $is_original]));
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $pdfContent = $dompdf->output();
        $filename_Duplicate = $is_original ? '' : '_Duplicate';
        $filename = "tapMo{$invoice_number}{$filename_Duplicate}.pdf";
        $writablePath = WRITEPATH . 'temp/';

        if (!is_dir($writablePath)) {
            mkdir($writablePath, 0777, true);
        }

        $filePath = $writablePath . $filename;
        file_put_contents($filePath, $pdfContent);
        if ($is_bulk) {
            return $filename;
        }

        // return base_url();
        $response_data = array('fileName' => $filename);
        header("Content-Security-Policy: data:; frame-ancestors {$shop} https://admin.shopify.com;");
        //Need to delete
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
        return $this->respond($response_data);
    }

    public function bulkDownload($is_draft = "is_draft_false")
    {
        try {
            ob_end_clean();
            $order_id_list =  $this->request->getJSON()->orderList;
            $shop = $_GET["shop"];

            if (!is_null($order_id_list)) {
                $link_list = array();
                $zip = new \ZipArchive;
                $zipFileName = 'tapMo_bulk_' . mt_rand(100000, 999999) . '.zip';
                $writablePath = WRITEPATH . 'temp/';
                $zipFilePath = $writablePath . $zipFileName;

                if ($zip->open($zipFilePath, \ZipArchive::CREATE) === TRUE) {
                    foreach ($order_id_list as $orderId) {
                        $file_name = $this->downloadOrderById($is_draft, $orderId, true);

                        $filePath = $writablePath . $file_name;
                        $zip->addFile($filePath, $file_name);
                    }
                    $zip->close();

                    // return base_url('download_zip/' . $zipFileName);
                    $response_bulkdata = array('zipFileName' => $zipFileName);

                    header("Content-Security-Policy: data:; frame-ancestors {$shop} https://admin.shopify.com;");
                    //Need to delete
                    header("Access-Control-Allow-Origin: *");
                    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
                    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

                    return $this->respond($response_bulkdata);
                } else {
                    echo "Error creating zip file";
                }
            } else {
                // Handle the case when $order_id_list is null
                echo "Error: order_id_list is null";
            }
        } catch (\Exception $e) {
            error_log('erro', $e->getMessage());
        }
    }

    public function directDownloadOrderById()
    {
        return view('directDownload');
    }

    //phpSpreadsheet function

    // public function createExcelFile($order_id_list)
    // {
    //     try {
    //         // print_r($order_id_list);
    //         // die;
    //         // $order_id_list =  $this->request->getJSON()->orderList;

    //         $spreadsheet = new Spreadsheet();
    //         $sheet = $spreadsheet->getActiveSheet();
    //         //  $activeWorksheet->setCellValue('A1', 'Hello World !');
    //         //   $writer = new Xlsx($spreadsheet);
    //         //   $writer->save('testsheet.xlsx');
    //         $headers = ['Order ID', 'Date', 'Customer', 'Total', 'Payment Status', 'Fulfilment Status', 'Item'];

    //         $column = 0;
    //         foreach ($headers as $header) {
    //             $latter = chr(65 + $column++);
    //             $sheet->setCellValue($latter . '1', $header);
    //             // $column++;
    //         }

    //         $row = 2;
    //         foreach ($order_id_list as $order) {


    //             $orderDetails = $order;


    //             $sheet->setCellValue('A' . $row, $orderDetails->name);
    //             $sheet->setCellValue('B' . $row, $orderDetails->created_at);
    //             $customerFullName = ($orderDetails->customer->first_name ?? '') . ' ' . ($orderDetails->customer->last_name ?? '');
    //             $sheet->setCellValue('C' . $row, $customerFullName);
    //             $sheet->setCellValue('D' . $row, $orderDetails->current_total_price);
    //             $sheet->setCellValue('E' . $row, $orderDetails->financial_status);
    //             // $fulfillmentStatus = ($orderDetails->line_items[0]->fulfillment_status) || 'Unfulfilled';

    //             // Set the fulfillment status in the Excel sheet
    //             // $sheet->setCellValue('F' . $row, $fulfillmentStatus); 
    //             $sheet->setCellValue ('F' . $row,ucfirst($orderDetails->line_items[0]->fulfillment_status) || 'Unfulfilled');
    //             $sheet->setCellValue('G' . $row, $orderDetails->line_items[0]->quantity);
    //             $row++;
    //         }



    //         $directoryPath = WRITEPATH . 'excel_files/';


    //         if (!is_dir($directoryPath)) {
    //             mkdir($directoryPath, 0755, true);
    //         }

    //         $writer = new Xlsx($spreadsheet);
    //         $filename = 'current_data_' . date('YmdHis') . '.xlsx';
    //         $filePath = $directoryPath . $filename;

    //         $writer->save($filePath);

    //          return $this->respond($filename);
            
    //         // $spreadsheet = new Spreadsheet();
    //         // $sheet = $spreadsheet->getActiveSheet();
    //         // // $sheet->setTitle('xyx');


    //         // $headers = ['Order ID', 'Date', 'Customer', 'Total', 'Payment Status', 'Fulfilment Status', 'Item'];

    //         // $column = 'A';
    //         // foreach ($headers as $header) {
    //         //     $sheet->setCellValue($column . '1', $header);
    //         //     $column++;
    //         // }

    //         // $row = 2;
    //         // foreach ($order_id_list as $order) {

    //         //     $orderDetails = $order;


    //         //     $sheet->setCellValue('A' . $row, $orderDetails['id']);
    //         //     $sheet->setCellValue('B' . $row, $orderDetails['created_at']);
    //         //     $sheet->setCellValue('C' . $row, $orderDetails['customer']['email']);
    //         //     $sheet->setCellValue('D' . $row, $orderDetails['current_total_price']);
    //         //     $sheet->setCellValue('E' . $row, $orderDetails['financial_status']);
    //         //     $sheet->setCellValue('F' . $row, $orderDetails['fulfillment_status']);
    //         //     $sheet->setCellValue('G' . $row, $orderDetails['line_items'][0]['name']);
    //         //     $row++;
    //         // }

    //         // $directoryPath = WRITEPATH . 'excel_files/';


    //         // if (!is_dir($directoryPath)) {
    //         //     mkdir($directoryPath, 0755, true);
    //         // }

    //         // $writer = new Xlsx($spreadsheet);
    //         // $filename = 'current_data_' . date('YmdHis') . '.xlsx';
    //         // $filePath = $directoryPath . $filename;

    //         // $writer->save($filePath);


    //         // error_log("File saved at: " . $filePath);

    //         // return $this->respond(['excelFileName' => $filename]);

    //         // return $this->respond(['filename' => $filename]);

    //         // return $this->respond($filename);

    //     } catch (\Exception $e) {
    //         return $this->failServerError("Fail to generate excel file: " . $e->getMessage());
    //     }
    // }


    public function createExcelFile($order_data)
{
    try {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['Order ID', 'Date', 'Customer', 'Total', 'Payment Status', 'Fulfilment Status', 'Item'];

        $column = 0;
        foreach ($headers as $header) {
            $latter = chr(65 + $column++);
            $sheet->setCellValue($latter . '1', $header);
            $sheet->getColumnDimension($latter)->setAutoSize(true);
        }

        $row = 2;
            foreach ($order_data as $order) {
                $orderDetails = $order;
                // print_r($orderDetails);
                // die;
                $sheet->setCellValue('A' . $row, $orderDetails->name);
                $createdAt = new \DateTime($orderDetails->created_at);
                $sheet->setCellValue('B' . $row, $createdAt->format('Y-m-d')); // Date format is YYYY/MM/DD

                $customerFullName = ($orderDetails->customer->first_name ?? '') . ' ' . ($orderDetails->customer->last_name ?? ''); //fullname
                $sheet->setCellValue('C' . $row, $customerFullName);

                $sheet->setCellValue('D' . $row, $orderDetails->current_total_price);
                $sheet->setCellValue('E' . $row, $orderDetails->financial_status);
                $fulfillmentStatus = ucfirst($orderDetails->line_items[0]->fulfillment_status) ?? 'Unfulfilled'; //fullfilment_Status
                $sheet->setCellValue('F' . $row, $fulfillmentStatus);
                $sheet->setCellValue('G' . $row, $orderDetails->line_items[0]->quantity);
                $row++;
            }

        $directoryPath = WRITEPATH . 'excel_files/';

        if (!is_dir($directoryPath)) {
            mkdir($directoryPath, 0755, true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'current_data_' . date('YmdHis') . '.xlsx';
        $filePath = $directoryPath . $filename;

        $writer->save($filePath);

        return $this->respond($filename);

    } catch (\Exception $e) {
        return $this->failServerError("Fail to generate excel file: " . $e->getMessage());
    }
}





public function generateExcelFileForCurrentData($is_draft = 'is_draft_false')
{
    $shop = $_GET['shop'];
    $is_draft_order = $is_draft == "is_draft" ? true : false;
    $shopData = $this->siteModel->get("shop_url", $shop);
    $token = $shopData->access_Token;
    $apiType = $is_draft_order ? "draft_orders" : "orders";

    $orderData = $this->shopify_lib->shopify_call($token, $shop, "/admin/api/2022-10/{$apiType}.json", array(), 'GET')['response'];

    // Create the Excel file
    $excelFileName = $this->createExcelFile(json_decode($orderData)->orders);

    // Specify Content-Type and Content-Disposition headers
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="your_excel_file.xlsx"');



    return $this->respond(['excelFileName' => $excelFileName]);
}


    
}
