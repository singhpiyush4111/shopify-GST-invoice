import React from 'react'
import CircularProgress from '@mui/material/CircularProgress';

import {
  Typography,
  Box
} from '@mui/material';
export const Loader = () => {
  return (
    <>
      <Box sx={{
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        position: 'fixed',
        top: 0,
        left: 0,
        width: '100%',
        height: '100%',
        // backdropFilter: 'blur(1px)',
        zIndex: 9999, // Adjust the z-index as needed
      }}>
        <CircularProgress />
        <Box
          sx={{
            position: 'relative',
            top:'50px',
            right:'100px',
            textAlign: 'center',
          }}
        >
          <Typography variant="body1" color="textSecondary">
            Loading... Please wait 
          </Typography>
        </Box>
      </Box>
    </>
  )
}
