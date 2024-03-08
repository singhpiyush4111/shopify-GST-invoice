import React, { useState, useEffect } from 'react';
import {
    TextField,
    Button,
    Grid,
    Box,
    InputAdornment,
    Typography
} from '@mui/material';
import { DataGrid } from '@mui/x-data-grid';
import DownloadIcon from '@mui/icons-material/Download';
import ClearIcon from '@mui/icons-material/Clear';
import { columns } from './OrderColums';
import { fetchData, download_bulkPdf,generateExcel } from '../../Services/apiServices';
import { formatOrderDate, capitalizeFirstLetter } from '../../Utility/commanFunction';
import { Loader } from './loader';



const Filterorder = () => {
    const [startDate, setStartDate] = useState('');
    const [endDate, setEndDate] = useState('');
    const [apidata, setApiData] = useState([]);
    const [pageData, setPageData] = useState({ nextLink: '', prevLink: '' });
    const [pageLoader, setPageLoader] = useState(true);
    const [paginationModel, setPaginationModel] = useState({
        page: 0,
        pageSize: 10,
    });
    const [searchValue, setSearchValue] = useState('');
    const [noDataFound, setNoDataFound] = useState(false);



    {/**fetch data from API function apiservices.js*/ }
    const getOrderList = async (pageChangeData = null) => {
        try {
            setPageLoader(true);

            const { jsonData, pageInfoArray } = await fetchData(pageChangeData, paginationModel, pageData, startDate, endDate);
            console.log('page', pageChangeData)
            console.log('dateFilter', startDate, endDate)

            setPageData(pageInfoArray)
            setPageLoader(false)
            setApiData(jsonData)
            1
            if (pageChangeData !== null) {
                setPaginationModel(prevPaginationModel => ({
                    ...prevPaginationModel,
                    pageSize: paginationModel.pageSize,
                    page: pageChangeData.page,
                }));
            }
            setPageLoader(false);
        } catch (error) {
            console.error('Error fetching data:', error);
            setPageLoader(false);
        }
    };
    useEffect(() => {
        getOrderList();
    }, []);


    {/**Search Cahnges Function*/ }
    const handleSearchChange = async (event) => {
        const newValue = event.target.value;
        setSearchValue(newValue);

        try {
            setPageLoader(true)
            const { jsonData, pageInfoArray } = await fetchData(null, paginationModel, pageData, startDate, endDate, newValue);

            setPageData(pageInfoArray);
            setApiData(jsonData);
            setPageLoader(false)
            if (jsonData.length > 0) {
                setPaginationModel(prev => ({ ...prev, page: 0 }));
                setNoDataFound(true);
            } else {
                setPageLoader(false);
            }
        } catch (error) {
            console.error('Error fetching data:', error);
            setPageLoader(false);
        }
    }

    {/**Clear Search Box Function*/ }
    const handleClearSearch = async () => {
        setSearchValue('');
        await getOrderList();
    };

    {/**Event object for Start End Date*/ }
    const handleStartDateChange = (event) => {
        setStartDate(event.target.value);
    };
    const handleEndDateChange = (event) => {
        setEndDate(event.target.value);
    };

    {/**Function for Submit button*/ }
    const handleSubmit = async () => {
        try {
            setPageLoader(true);
            const { jsonData, pageInfoArray } = await fetchData(null, paginationModel, pageData, startDate, endDate);
            setPageData(pageInfoArray);
            setApiData(jsonData);
            setPageLoader(false);
            if (jsonData.length > 0) setPaginationModel(prev => ({ ...prev, page: 0 }));
        } catch (error) {
            console.error('Error fetching data:', error);
            setPageLoader(false)
        }
    };


    const handleClearDates = async () => {
        setStartDate('');
        setEndDate('');
        setSearchValue('');
        await getOrderList();
    };

    const handleGoBack = async () => {
        setPageLoader(true)
        await getOrderList();
        setSearchValue('');
        setPageLoader(false)
    }

    const handleBulkDownload = async () => {
        try {
            setPageLoader(true)
            const orderList = apidata.map(order => order.id);

            await download_bulkPdf(orderList);
            setPageLoader(false)
        } catch (error) {
            console.error('Error in bulk download:', error);
            setPageLoader(false)
        }
    };

    const handleExcelFile = async () =>{
        try {
            const orderList = apidata.map(order => order.id);
            await generateExcel(orderList);
        } catch (error) {
            console.log('error handling excel file', error)
        }
    }



    {/**Mapping Data for API*/ }
    const rows = apidata ? apidata.map((order) => ({
        id: order.name,
        date: formatOrderDate(order.created_at),
        customer: order.customer ? `${capitalizeFirstLetter(order.customer.first_name)} ${capitalizeFirstLetter(order.customer.last_name)}` : ' ',
        total: `₹ ${order.line_items[0].price_set.shop_money.amount}`,
        paymentstatus: capitalizeFirstLetter(order.financial_status),
        fulfilmentstatus: capitalizeFirstLetter(order.line_items[0].fulfillment_status || 'Unfulfilled'),
        item: order.line_items[0].quantity,
        action: 'View'
    })) : [];

    return (
        <>
            {/*Search BAR*/}
            <TextField
                label="Search by Order ID & Customer Name"
                variant="outlined"
                size='small'
                fullWidth
                sx={{
                    marginBottom: 2,
                }}
                value={searchValue}
                onChange={handleSearchChange}
                InputProps={{
                    endAdornment: (
                        <InputAdornment position="end">
                            {searchValue && (
                                <ClearIcon
                                    style={{ cursor: 'pointer' }}
                                    onClick={handleClearSearch}
                                />
                            )}
                        </InputAdornment>
                    ),
                }}
            />

            <Grid container spacing={2} mb={2}>
                <Grid item xs={12} sm={6} md={2}>
                    <TextField
                        label="Start Date"
                        type="date"
                        value={startDate}
                        size='small'
                        onChange={handleStartDateChange}
                        InputLabelProps={{
                            shrink: true,
                        }}
                        fullWidth
                    />
                </Grid>
                <Grid item xs={12} sm={6} md={2}>
                    <TextField
                        label="End Date"
                        type="date"
                        value={endDate}
                        size='small'
                        onChange={handleEndDateChange}
                        InputLabelProps={{
                            shrink: true,
                        }}
                        fullWidth
                    />
                </Grid>
                <Grid item xs={12} sm={6} md={2}>
                    <Button variant="contained" color="primary" size="medium" onClick={handleSubmit} fullWidth>
                        Submit
                    </Button>
                </Grid>
                <Grid item xs={12} sm={6} md={2}>
                    <Button variant="contained" size="medium" color="primary" onClick={handleClearDates} fullWidth>
                        Clear & Submit
                    </Button>
                </Grid>
                <Grid item xs={12} md={3}>
                    <Button startIcon={<DownloadIcon />} variant="contained" size="medium" color="primary" fullWidth onClick={handleBulkDownload} >
                        Bulk Downloads
                    </Button>
                </Grid>
                <Grid item xs={12} md={1}>
                    <Button variant="outlined" onClick={handleExcelFile}>Export</Button>
                </Grid>
            </Grid>

            <Box sx={{ height: '100%', width: '100%' }}>
                {pageLoader && <Loader />}
                {!pageLoader && (
                    <>
                        {apidata.length > 0 ? (
                            <DataGrid
                                pagination
                                // checkboxSelection
                                disableRowSelectionOnClick
                                paginationMode={'server'}
                                rowCount={Number.MAX_VALUE}
                                rows={rows}
                                columns={columns}
                                loader={pageLoader}
                                paginationModel={paginationModel}
                                onPaginationModelChange={(data) => {
                                    getOrderList(data);
                                    // console.log('Pagination Model:', data);
                                }}
                                slotProps={{
                                    pagination: {
                                        prevIconButtonProps: {
                                            disabled: !pageData.prevLink,
                                        },
                                        nextIconButtonProps: {
                                            disabled: !pageData.nextLink,
                                        },
                                    },
                                }}
                            />
                        ) : (
                            <Box
                                display="flex"
                                flexDirection="column"
                                alignItems="center"
                                justifyContent="center"
                                height="50vh"
                            >
                                <Typography variant="h5" align="center">
                                    {noDataFound ? `No Data Found...` : 'Correct Order ID & Customer Name to search'}
                                </Typography>

                                {noDataFound && (
                                    <Button variant="outlined" color="primary" onClick={() => handleGoBack()}>
                                        GO Back
                                    </Button>
                                )}
                            </Box>

                        )}
                    </>
                )}

            </Box>




        </>
    );
};

export default Filterorder;
