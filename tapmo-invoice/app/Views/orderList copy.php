<?= $this->extend('common/layout') ?>
<?php
$first_name = isset($order->billing_address) ? $order->billing_address->first_name : "";
$last_name = isset($order->billing_address) ? $order->billing_address->last_name : "";


?>

<?= $this->section('content') ?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url("assets/order.css"); ?>">

<?= $this->include('common/orders_tab.php'); ?>

<table class="results" id="data-table">
  <thead>
    <tr>
      <th>Order</th>
      <th>Date</th>
      <th>Customer</th>
      <th>Total</th>
      <th>Payment status</th>
      <th>Fulfillment status</th>
      <th>Items</th>
      <th class="align-right">Action</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($orders as $order) { ?>
      <tr>
        <td><?= $order->name  ?></td>
        <td><?= data_time_formet($order->created_at) ?></td>
        <td><?= $first_name  ?> <?= $last_name  ?></td>
        <td>RS <?= $is_draft_order ? $order->total_price : $order->current_total_price ?></td>
        <td><span class="tag grey"><?= !$is_draft_order ? ucwords($order->financial_status) : "Draft Order" ?></span></td>
        <td><?php
            if (!$is_draft_order) {
              if ($order->fulfillment_status === "fulfilled") {
                echo "<span class='tag grey'>Fulfilled</span>";
              } else {
                echo "<span class='tag orange'>Unfulfilled</span>";
              }
            } else {
              if ($order->status === "competed") {
                echo "<span class='tag grey'>Completed</span>";
              } else {
                echo "<span class='tag orange'>" . $order->status . "</span>";
              }
            }

            ?></td>
        <td><?= count($order->line_items) ?></td>
        <td class="align-right">
          <Button class="button secondary direct-download-pdf" data-rowid="<?= $order->id ?>">Direct Download Pdf</Button>
          <!-- <Button class="button secondary download-pdf" data-rowid="<?= $order->id ?>">Download Pdf</Button> -->
        </td>
      </tr>

    <?php } ?>

  </tbody>
</table>
<?php
if ($pageInfoArray) { ?>
  <div class="pagination">
    <span class="button-group">
      <a href=<?= $pageInfoArray['prevLink'] ? array_to_pagination_url($_GET, $pageInfoArray['prevLink'], $is_draft_order) : "#" ?> class="button secondary icon-prev <?= !$pageInfoArray['prevLink'] ? "disabled" : "" ?>"></a>
      <a href=<?= $pageInfoArray['nextLink'] ? array_to_pagination_url($_GET, $pageInfoArray['nextLink'], $is_draft_order) : "#" ?> class="button secondary icon-next <?= !$pageInfoArray['nextLink'] ? "disabled" : "" ?>"></a>
    </span>   
  </div>
<?php  } ?>

<div id="myModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <!-- Modal content, including form elements -->
    <div id="content"></div>
  </div>
</div>


<script>
  $(document).ready(function() {
    $('.page_loader').hide();

    var modal = document.getElementById('myModal');
    var span = document.getElementsByClassName('close')[0];
    $('#data-table tbody tr .download-pdf').on('click', function() {
      $('.page_loader').show();
      var rowId = $(this).data('rowid');
      var param = window.location.search + "&id=" + rowId;
      var url = "<?= base_url() ?>/get_order_invoice_by_id<?= !$is_draft_order ? '' : '/is_draft' ?>" + param;
      $.ajax({
        url: url,
        type: "GET", // or "GET" depending on your server setup
        dataType: "html", // Set the data type to "html" to expect an HTML response
        success: function(response) {
          // Inject the HTML response into the 'response' div
          $('.page_loader').hide();
          $("#myModal #content").html(response);
        },
        error: function(xhr, status, error) {
          // Handle any errors that occur during the AJAX call
          $('.page_loader').hide();
          console.error("AJAX Error: " + status, error);
        }
      });
      modal.style.display = 'block';
    });
    span.onclick = function() {
      modal.style.display = 'none';
    };
    window.onclick = function(event) {
      if (event.target == modal) {
        modal.style.display = 'none';
      }
    };

  });
</script>

<?= $this->endSection() ?>