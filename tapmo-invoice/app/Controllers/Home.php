<?php

namespace App\Controllers;

use App\Libraries\ShopifyLib;  //so we can easily load the custom library
use Spipu\Html2Pdf\Html2Pdf;
use CodeIgniter\HTTP\IncomingRequest;
use App\Models\SiteModel;

class Home extends BaseController
{
    public $data;
    public $shopify_lib;
    private $public_key = "";
    private $secret_key = "";
    protected $request;
    public function __construct()
    {
        $db = db_connect();
        $this->siteModel = new SiteModel($db);

        ini_set('memory_limit', '2048M');
        helper('common_helper');

        $this->request = service('request');
        $this->public_key = $this->siteModel->get_app_key(SHOPIFY_APP_PUBLIC_KEY);
        $this->secret_key = $this->siteModel->get_app_key(SHOPIFY_APP_SECRET_KEY);
        $this->shopify_lib = new ShopifyLib();
        $this->session     = \Config\Services::session();
    }

    private function is_installed($shop)
    {
        $shop_data =  $this->siteModel->get("shop_url", $shop);
        if ($shop_data) {
            return $shop_data;
        }
        return null;
    }
    private function redirect_to_invoice()
    {
        $url = build_http_query($_GET);



        if (isset($_GET['session'])) {
            $newdata = [
                'session'  => $_GET['session'],
                'shop'     => $_GET['shop'],
                'embedded' => $_GET['embedded'],
            ];
            $this->session->set($newdata);
        }
        $PATH_INFO = isset($_GET['path_info']) ? $_GET['path_info'] : "order_list";

        header("Location: " . base_url("{$PATH_INFO}?{$url}"));
        die;
    }

    public function install()
    {
        if (isset($_GET["shop"])) {


            if (isset($_GET["embedded"]) && verifyRequest($_GET, $this->secret_key)) {
                $this->redirect_to_invoice();
            }

            $shop = $_GET["shop"];
            $api_key = $this->public_key;
            $scopes = "read_orders,read_draft_orders,read_customers,read_all_orders";
            $redirect_uri =  base_url("generate_token");

            // Build install/approval URL to redirect to
            $install_url = "https://" . $shop . "/admin/oauth/authorize?client_id=" . $api_key . "&scope=" . $scopes . "&redirect_uri=" . urlencode($redirect_uri);

            // Redirect
            header("Location: " . $install_url);
            die();
        } else {
            //redirect to Shopify app
            $this->data['title'] = 'shopify Install';
            $this->data['content'] = 'shopify/shopify_install';
            // $this->load->view('_main_layout', $this->data);
            echo "hello";
        }
    }
    public function generate_token()
    {
        if (isset($_GET["shop"]) && isset($_GET["code"]) && isset($_GET["timestamp"])) {
            // Set variables for our request
            $shop = $_GET["shop"];
            $api_key = $this->public_key;
            $shared_secret = $this->secret_key;
            $code = $_GET["code"];
            $timestamp = $_GET["timestamp"];
            $signature = $_GET["hmac"];
            if (!verifyRequest($_GET, $shared_secret)) {
                die("invalid access");
            }

            // // Set variables for our request
            $query = array(
                "Content-type" => "application/json", // Tell Shopify that we're expecting a response in JSON format
                "client_id" => $api_key, // Your API key
                "client_secret" => $shared_secret, // Your app credentials (secret key)
                "code" => $code // Grab the access key from the URL
            );

            // Call our Shopify function
            $shopify_response = $this->shopify_lib->shopify_call(null, $shop, "/admin/oauth/access_token", $query, 'POST');
            $shopify_response = json_decode($shopify_response['response'], true);

            //  $is_installed = $this->is_installed($shop);
            //  if($is_installed && ($shopify_response["access_token"] == $is_installed->access_Token))
            //  {

            //     header("Location: " . base_url("order_list?{$url}"));
            // 	 die;
            //  }

            // Convert response into a nice and simple array
            $data = array(
                "site_name" => $shop,
                "access_Token" => $shopify_response["access_token"],
                "shop_url" => $shop,
                "status" => "published"
            );
            $this->siteModel->update_or_add("site_name", $data);
            // Redirect
            $this->redirect_to_invoice();
            die();
        } else {
            die("invalid access");
        }
    }
}
