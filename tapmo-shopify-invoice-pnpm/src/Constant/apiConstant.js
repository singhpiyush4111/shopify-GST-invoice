const BASE_URL = import.meta.env.VITE_REACT_APP_API_KEY;
const apiURL={
    getOrderList:  BASE_URL + '/order_list_api',
    pdfTempPath: BASE_URL + '/download_pdf',
    downloadZip: BASE_URL + '/download_zip',
    bulkDownload: BASE_URL + '/bulk_download_invoice_by_id_list',
    downloadExcel: BASE_URL+ '/create_excel_file',
    generateExcel: BASE_URL+ '/generate_excel'
}
export default apiURL;

//order_list_api?shop=tapmo-in.myshopify.com&timestamp=1706522361
