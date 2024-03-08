<?php

namespace App\Controllers;

use App\Libraries\ShopifyLib;  //so we can easily load the custom library
use Spipu\Html2Pdf\Html2Pdf;
use CodeIgniter\HTTP\IncomingRequest;
use App\Models\SiteModel;
use CodeIgniter\HTTP\Files\UploadedFile;


class Download extends BaseController
{
  
    
    public function downloadInvoicePdf($filename)
    {
        $writablePath = WRITEPATH . 'temp/';
        $filePath = $writablePath . $filename;

        if (file_exists($filePath) && is_readable($filePath)) {
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="document.pdf"');
            header('Content-Length: ' . filesize($filePath));

            readfile($filePath);
            exit;
        } else {
            header('HTTP/1.0 404 Not Found');
            echo '404 Not Found';
            exit;
        }
    }

        public function downloadZippedInvoicePdf($filename)
    {
         $writablePath = WRITEPATH . 'temp/';
        $filePath = $writablePath . $filename;

        if (file_exists($filePath) && is_readable($filePath)) {
            header('Content-Type: application/zip');
            header('Content-Disposition', 'attachment; filename="'. $filename  .'.zip`"');
            header('Content-Length: ' . filesize($filePath));

            readfile($filePath);
            exit;
        } else {
            header('HTTP/1.0 404 Not Found');
            echo '404 Not Found';
            exit;
        }
    }
}
