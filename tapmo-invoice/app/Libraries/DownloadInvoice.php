<?php

namespace App\Libraries;


use App\Libraries\ShopifyLib;  //so we can easily load the custom library
use App\Models\SiteModel;
use App\Models\InvoiceAddress;

class DownloadInvoice
{

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
	}
	function createPdf($is_draft = "is_draft_false", $orderId = false, $is_bulk = false)
	{
	}
}
