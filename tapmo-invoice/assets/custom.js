function queryStringToObject($queryString = undefined) {
  // Remove the leading '?' if present
  var search = $queryString
    ? $queryString.replace('?', '')
    : location.search.substring(1);
  return JSON.parse(
    '{"' + search.replace(/&/g, '","').replace(/=/g, '":"') + '"}',
    function (key, value) {
      return key === '' ? value : decodeURIComponent(value);
    }
  );
}

function objectToQueryString(obj) {
  return Object.keys(obj)
    .map((key) => `${key}=${obj[key]}`)
    .join('&');
}

$(document).ready(function () {
  $('#onSubmit').on('click', function () {
    submitFilter();
  });
  $('#clearAndSubmit').on('click', function () {
    submitFilter(true);
  });
  $('#bulkDownload').on('click', function () {
    var rowId = $(this).data('rowid');
    bulkDownload(rowId);
  });
  $('#data-table tbody tr .direct-download-pdf').on('click', function () {
    var rowId = $(this).data('rowid');
    directDownload(rowId);
  });
  $("input[name='original']").change(function () {
    console.log('qwerty', this.checked, originalFilter);
    originalFilter = this.checked ? 'on' : '';
    console.log('qwerty', this.checked, originalFilter);
  });
});

function submitFilter(clear = false) {
  formData = $('#add-filter');
  var queryString = $(formData).serialize();
  console.log('Sssss', queryString);
  var url_query_object = queryStringToObject(window.location.search);
  delete url_query_object['page_info'];
  if (!clear) {
    var form_query_object = queryStringToObject(queryString);
    $('#add-filter :checkbox:not(:checked)').each(function () {
      form_query_object[this.name] = '';
    });
    url_query_object = { ...url_query_object, ...form_query_object };

    var searchValue = $("input[name='order_number").val();
    url_query_object['order_number']=searchValue;
  } else {
    delete url_query_object['start_date'];
    delete url_query_object['end_date'];
    delete url_query_object['limit'];
  }
  
  var object_query_string = objectToQueryString(url_query_object);
  const urlPieces = [location.protocol, '//', location.host, location.pathname];
  let url = urlPieces.join('');
  var link = `${url}?${object_query_string}`;
  console.log(
    'queryString44',
    url_query_object,
    form_query_object,
    url_query_object,
    object_query_string,
    link
  );
  window.open(link, '_self');
  return false;
}

function updateLinkList(link) {
  var linkList = getLinkList() || [];
  linkList.push(link);
  sessionStorage.setItem('links', JSON.stringify(linkList));
}

function getLinkList() {
  return JSON.parse(sessionStorage.getItem('links'));
}

function bulkDownload() {
  $('.page_loader').show();

  const tableData = document.querySelectorAll(
    '#data-table .direct-download-pdf'
  );

  const orderList = Array.from(tableData).map((element) =>
    element.getAttribute('data-rowid')
  );
  var formData = { original: originalFilter, orderList };
  var param = window.location.search;
  var url = `${base_url}/bulk_download_invoice_by_id_list${
    !isDraftOrder ? '' : '/is_draft'
  }${param}`;
  $.ajax({
    type: 'POST',
    url: url,
    data: formData,
    success: function (pdfUrl) {
      $('.page_loader').hide();
      window.open(pdfUrl, '_blank');
    },
    error: function (error) {
      $('.page_loader').hide();
      console.error('Error downloading the PDF:', error);
    },
  });
}

function directDownload(rowId = '', backToPage = false) {
  $('.page_loader').show();
  console.log('qwerty-download', originalFilter);

  var param = window.location.search;
  if (rowId) {
    param += '&id=' + rowId;
  }
  var url = `${base_url}/download_invoice_by_id${
    !isDraftOrder ? '' : '/is_draft'
  }${param}`;
  console.log('tyuiop', originalFilter);
  var formData = { original: originalFilter };

  $.ajax({
    type: 'GET',
    url: url,
    data: formData,
    success: function (pdfUrl) {
      $('.page_loader').hide();
      window.open(pdfUrl, '_blank');
      if (backToPage) {
        window.history.back();
      }
    },
    error: function (error) {
      $('.page_loader').hide();
      console.error('Error downloading the PDF:', error);
    },
  });
}
