import {React,useState} from 'react';
import Chip from '@mui/material/Chip';
import Button from '@mui/material/Button';
import DownloadIcon from '@mui/icons-material/Download';
import CheckIcon from '@mui/icons-material/Check';
import AutorenewIcon from '@mui/icons-material/Autorenew';
import ErrorOutlineOutlinedIcon from '@mui/icons-material/ErrorOutlineOutlined';

import PublishedWithChangesIcon from '@mui/icons-material/PublishedWithChanges';
import { downloadPdf } from '../../Services/apiServices';



export const columns = [
  { field: 'id', headerName: 'OrderID', width: 120 },
  {
    field: 'date',
    headerName: 'Date',
    width: 130,
    editable: true,
  },
  {
    field: 'customer',
    headerName: 'Customer',
    width: 150,
    editable: true,
  },
  {
    field: 'total',
    headerName: 'Total',
    width: 150,
    editable: true,
  },
  {
    field: 'paymentstatus',
    headerName: 'Payment Status',
    editable: true,
    width: 150,
    renderCell: (params) => {
      let chipProps = {
        label: params.value,
        variant: 'outlined',
        icon: <CheckIcon fontSize='small' />,
        color: 'success',
      };
      if (params.value.toLowerCase() === 'refunded') {
        chipProps.icon = <PublishedWithChangesIcon fontSize='small' />;
        chipProps.color = 'error';
      } else if (params.value.toLowerCase() === 'pending') {
        chipProps.icon = <AutorenewIcon fontSize='small' />;
        chipProps.color = 'warning';
      }

      return <Chip {...chipProps} />;
    },
  },
  {
    field: 'fulfilmentstatus',
    headerName: 'Fulfillment Status',
    width: 150,
    editable: true,
    renderCell: (params) => {
      let chipProps = {
        label: params.value,
        variant: 'outlined',
        icon: null,
        color: 'default',
      };

      if (params.value.toLowerCase() === 'unfulfilled') {
        chipProps.icon = <ErrorOutlineOutlinedIcon fontSize='small' />;
        chipProps.color = 'warning';
      } else if (params.value.toLowerCase() === 'fulfilled') {
        chipProps.icon = <CheckIcon fontSize='small' />;
        chipProps.color = 'success';
      }

      return <Chip {...chipProps} />;
    },
  },
  {
    field: 'item',
    headerName: 'Item',
    width: 110,
    editable: true,
  },
  {
    field: 'action',
    headerName: 'Downlaod PDF',
    width: 200,
    editable: true,
    renderCell: (params) => (
      <DownloadIcon color='primary' style={{ cursor: 'pointer' }} onClick={() => handleButtonClick(params.row)} />
    ),
  }
];


const handleButtonClick = (row) => {
  const orderId = row.id;
  console.log(row)

  if (orderId) {
    downloadPdf(orderId);
  }
};