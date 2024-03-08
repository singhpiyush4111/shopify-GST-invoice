<?= $this->extend('common/layout') ?>
<?= $this->section('content') ?>
<?php

if (!isset($order->billing_address) || empty($order->billing_address)) {
    $order->billing_address = (object)DEFAULT_ADDRESS_OBJECT;
}

if (!isset($order->shipping_address) || empty($order->shipping_address)) {
    $order->shipping_address = (object)DEFAULT_ADDRESS_OBJECT;
}


?>

<form id="update-data">
    <input type="hidden" class="txt_csrfname" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />

    <section>
        <div class="card has-sections">
            <div class="card-section">
                <div class="row">
                    <div class="columns four">
                        <label><input type="checkbox" class="checkbox" name="original" checked="checked">Original</label>
                    </div>
                    <div class="columns four">
                        <label><input type="checkbox" class="checkbox" id="edit-form" name="editDetails">Edit</label>
                    </div>
                </div>
                <h5>Billing Address</h5>

                <div class="row">
                    <div class="columns six">
                        <label>First name</label>
                        <input type="text" name="billing_address[first_name]" value="<?= $order->billing_address->first_name ?>" />
                    </div>
                    <div class="columns six">
                        <label>Last name</label>
                        <input type="text" name="billing_address[last_name]" value="<?= $order->billing_address->last_name ?>" />
                    </div>
                </div>
                <div class="row">
                    <div class="columns six">
                        <label>Address</label>
                        <textarea type="text" name="billing_address[address1]"><?= $order->billing_address->address1 ?> </textarea>
                    </div>
                    <div class="columns six">
                        <label>Apartment, suite, etc.</label>
                        <textarea type="text" name="billing_address[address2]"> <?= $order->billing_address->address2 ?></textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="columns six">
                        <label>City</label>
                        <input type="text" name="billing_address[city]" value="<?= $order->billing_address->city ?>" />
                    </div>
                    <div class="columns six">
                        <label>State</label>
                        <input type="text" name="billing_address[province]" value="<?= $order->billing_address->province ?>" />
                    </div>
                </div>
                <div class="row">
                    <div class="columns six">
                        <label>PIN code</label>
                        <input type="text" name="billing_address[zip]" value="<?= $order->billing_address->zip ?>" />
                    </div>
                    <div class="columns six">
                        <label>Phone</label>
                        <input type="text" name="billing_address[phone]" value="<?= $order->billing_address->phone ?>" />
                    </div>
                </div>
            </div>

            <hr />
            <div class="card-section">
                <h5>Shipping Address</h5>
                <div class="row">
                    <div class="columns six">
                        <label>First name</label>
                        <input type="text" name="shipping_address[first_name]" value="<?= $order->shipping_address->first_name ?>" />
                    </div>
                    <div class="columns six">
                        <label>Last name</label>
                        <input type="text" name="shipping_address[last_name]" value="<?= $order->shipping_address->last_name ?>" />
                    </div>
                </div>
                <div class="row">
                    <div class="columns six">
                        <label>Address</label>
                        <textarea type="text" name="shipping_address[address1]"><?= $order->shipping_address->address1 ?></textarea>
                    </div>
                    <div class="columns six">
                        <label>Apartment, suite, etc.</label>
                        <textarea type="text" name="shipping_address[address2]"><?= $order->shipping_address->address2 ?></textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="columns six">
                        <label>City</label>
                        <input type="text" name="shipping_address[city]" value="<?= $order->shipping_address->city ?>" />
                    </div>
                    <div class="columns six">
                        <label>State</label>
                        <input type="text" name="shipping_address[province]" value="<?= $order->shipping_address->province ?>" />
                    </div>
                </div>
                <div class="row">
                    <div class="columns six">
                        <label>PIN code</label>
                        <input type="text" name="shipping_address[zip]" value="<?= $order->shipping_address->zip ?>" />
                    </div>
                    <div class="columns six">
                        <label>Phone</label>
                        <input type="text" name="shipping_address[phone]" value="<?= $order->shipping_address->phone ?>" />
                    </div>
                </div>
            </div>
            <hr />
            <div class="card-section">
                <h5>GST Details</h5>
                <?php
                $gst_company_name = '';
                $gst_number = '';
                if (isset($order->custom_gst_details)) {
                    $gst_company_name =  isset($order->custom_gst_details->gst_company_name)  ? $order->custom_gst_details->gst_company_name : "";
                    $gst_number =  isset($order->custom_gst_details->gst_number)  ? $order->custom_gst_details->gst_number : "";
                }
                ?>
                <div class="row">
                    <div class="columns six">
                        <label>Company Name</label>
                        <input type="text" name="gst_company_name" value="<?= $gst_company_name ?>" />
                    </div>
                    <div class="columns six">
                        <label>GST Number</label>
                        <input type="text" name="gst_number" value="<?= $gst_number ?>" />
                    </div>
                </div>


            </div>
            <div class="card-section">
                <div class="row">
                    <div class="columns twelve">
                        <button type="submit" id="download-pdf">Download Invoice</button>
                    </div>
                </div>
            </div>

        </div>
    </section>
</form>





<script>
    $(document).ready(function() {
        var checked = false;

        function disableInput(disableInput = true) {
            $("#update-data :input ").prop("disabled", disableInput);
            $('#update-data .checkbox').prop('disabled', false);
            $('#update-data #download-pdf').prop('disabled', false);
        }

        disableInput()

        $('#update-data').on('submit', function(event) {
            event.preventDefault();
            $('.page_loader').show();

            disableInput(false)
            var formData = $(this).serialize();
            disableInput(!checked)
            var rowId = <?= $order_id ?>;
            var param = window.location.search + "&id=" + rowId;
            var url = "<?= base_url() ?>/download_invoice_by_id<?= !$is_draft_order ? '' : '/is_draft' ?>" + param;
            $.ajax({
                type: 'POST',
                url: url,
                data: formData,
                success: function(pdfUrl) {
                    $('.page_loader').hide();
                    window.open(pdfUrl, '_blank');
                },
                error: function(error) {
                    $('.page_loader').hide();
                    console.error('Error downloading the PDF:', error);
                }
            });



        });

        $("#edit-form").click(function(e) {

            checked = $(this).prop('checked') ? true : false;

            disableInput(!checked)
        })
    });
</script>





<?= $this->endSection() ?>