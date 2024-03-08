    import axios from 'axios';
    import apiURL from '../Constant/apiConstant';

    const download_pdf = "https://tapmo-invoice.aniket/download_invoice_by_id?embedded=1&hmac=cc074e747148808218d94dcc355ebac25979975515e4c6c72909a97f4fcd05ad&host=YWRtaW4uc2hvcGlmeS5jb20vc3RvcmUvdGFwbW8taW4&id_token=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJodHRwczpcL1wvdGFwbW8taW4ubXlzaG9waWZ5LmNvbVwvYWRtaW4iLCJkZXN0IjoiaHR0cHM6XC9cL3RhcG1vLWluLm15c2hvcGlmeS5jb20iLCJhdWQiOiIzYmEyZWE5OTk3ZDMxNjFiNjBmNWY5ZTUxMjcwYTU5MiIsInN1YiI6Ijg5NzE1MzEwODQ1IiwiZXhwIjoxNzA4NTkzMzM1LCJuYmYiOjE3MDg1OTMyNzUsImlhdCI6MTcwODU5MzI3NSwianRpIjoiNDk5ODk2ZTktY2M2My00NWUxLTk0NzEtZDhhMWUwY2I5OWJhIiwic2lkIjoiNzEzOGFiNmMtNzRkNi00OTFkLTk0MDctY2U4MmNmODMxNWU5Iiwic2lnIjoiZTVkZTNhNDUzNzNmNmQ4ZDIxMDg5OTYxMDFjNThkZGI4NDE1ZmVhODU3MTkyZjJmOWEyOGQwNjkxYmRhMWMwYSJ9.gSlDWkaaKzGcqUO8UdhpsMN28dg0S-OYDGuoti99D0M&locale=en&session=a42a72129ff8c51a69ebdeab21e6bef732fb6b32542089079265f740b1e0ba40&shop=tapmo-in.myshopify.com&timestamp=1708593275&id=5517429735677&original="


    // const bulk_download = "https://tapmo-invoice.aniket/bulk_download_invoice_by_id_list?embedded=1&hmac=327a7fe3b9c0261a0cf291eef2ad280733d3d71fc8ba4430a3c94476ac73537b&host=YWRtaW4uc2hvcGlmeS5jb20vc3RvcmUvdGFwbW8taW4&id_token=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJodHRwczpcL1wvdGFwbW8taW4ubXlzaG9waWZ5LmNvbVwvYWRtaW4iLCJkZXN0IjoiaHR0cHM6XC9cL3RhcG1vLWluLm15c2hvcGlmeS5jb20iLCJhdWQiOiIzYmEyZWE5OTk3ZDMxNjFiNjBmNWY5ZTUxMjcwYTU5MiIsInN1YiI6Ijg5NzE1MzEwODQ1IiwiZXhwIjoxNzA4Njc0NDMyLCJuYmYiOjE3MDg2NzQzNzIsImlhdCI6MTcwODY3NDM3MiwianRpIjoiZGIxM2M1NTgtOTY1YS00ZTEwLWI2MGYtNjNhZmQ0Y2ZmYWI5Iiwic2lkIjoiNzEzOGFiNmMtNzRkNi00OTFkLTk0MDctY2U4MmNmODMxNWU5Iiwic2lnIjoiMzhjZTUzOTc3ZWI0ODY4NDgyYzliYWFhNWZiYjBlZTEwNmE0YzA3MGM3MmViZDM3M2Q4NzU3NzRjM2IxNTEwMCJ9.JB_WyAKf31f6tBQZ2ia7Q18rAuDo5ymudquGlCz31DM&locale=en&session=a42a72129ff8c51a69ebdeab21e6bef732fb6b32542089079265f740b1e0ba40&shop=tapmo-in.myshopify.com&timestamp=1708674372"






    export const fetchData = async (pageChangeData = null, paginationModel, pageData, startDate, endDate, orderNumber) => {
        try {
            const shop = new URLSearchParams(window.location.search).get('shop');
            console.log('shopURL',shop)
            let url = `${apiURL.getOrderList}?shop=${shop}`;

            if (startDate) {
                url += `&start_date=${encodeURIComponent(startDate)}`;
            }
            if (endDate) {
                url += `&end_date=${encodeURIComponent(endDate)}`;
            }
            if (orderNumber) {
                url += `&order_number=${encodeURIComponent(orderNumber)}`;
            }

            console.log('dateAPI', startDate, endDate, orderNumber,)


            if (pageChangeData !== null) {
                const pageType = pageChangeData.page - paginationModel.page;
                url += `&page_info=${encodeURIComponent(pageType === 1 ? pageData.nextLink : pageData.prevLink)}`;
            }

            const { data: { orders: jsonData, pageInfoArray } } = await axios.get(url);
            console.log('URL', url)
            return { jsonData, pageInfoArray };
        } catch (error) {
            console.error('Error fetching data:', error);
            throw error;
        }
    };

    export const downloadPdf = async (orderId) => {
        try {
            const response = await axios.get(`${download_pdf}`);
            console.log(response)
            const pdfURL = `${apiURL.pdfTempPath}/${response.data.fileName}`;
            console.log('wwwww',apiURL);
            window.open(pdfURL, '_blank');
        } catch (error) {
            console.error('Error downloading PDF:', error);
            
        }
    };


    export const download_bulkPdf = async (orderList) => {
        try {
            
            //console.log('Request payload:', { orderList }); 

            const shop = new URLSearchParams(window.location.search).get('shop');
            const response = await axios.post(`${apiURL.bulkDownload}?shop=${shop}`, {orderList});
            console.log(orderList)
            console.log(response.data);
    
            const zipFileName = response.data.zipFileName;
            const zipDownloadURL = `${apiURL.downloadZip}/${zipFileName}`;
            
            console.log('Bulk Download URL:', zipDownloadURL);
            window.open(zipDownloadURL, '_blank');

            
        } catch (error) {
            console.error('Error downloading Bulk PDF:', error);

            
          
        }
    };



    export const generateExcel = async (orderList) => {
        try {
            
            const shop = new URLSearchParams(window.location.search).get('shop');
            const response = await axios.post(`${apiURL.generateExcel}?shop=${shop}`, {orderList});
            console.log(orderList);
            console.log(response.data);
            
            
            const excelFileName = response.data.excelFileName;
            console.log('exclefilename',excelFileName)
            const excelDownloadURL = `${apiURL.downloadExcel}/${excelFileName}`;
            
            console.log('Excel Download URL:', excelDownloadURL);
    
            
            window.open(excelDownloadURL, '_blank');


            console.log('serverResponse', response)
        } catch (error) {
            console.error('Error generating Excel file:', error);
        }
    };
    
    

    