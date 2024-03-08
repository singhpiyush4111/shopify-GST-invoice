<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo base_url('assets/uptown.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/picker/themes/default.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/picker/themes/default.date.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/picker/themes/default.time.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/font-awesome/css/font-awesome.min.css'); ?>">
    <style>
        .page_loader {
            position: fixed;
            left: 0px;
            top: 0px;
            width: 100%;
            height: 100%;
            z-index: 9999;
            background: url('<?php echo base_url("public/loader.webp"); ?>') 50% 50% no-repeat rgb(250, 250, 250);
            opacity: .8;
        }
    </style>
</head>

<body>
    <script>
        var base_url = "<?= base_url() ?>";
        var limit = Boolean(<?= isset($_GET['limit']) && !empty($_GET['limit']) ? true : false ?>);
        var originalFilter = "<?= isset($_GET['original']) && !empty($_GET['original']) ? $_GET['original'] : '' ?>";
        var isDraftOrder = Boolean(<?= isset($is_draft_order) && !empty($is_draft_order) && $is_draft_order ? true : false ?>);
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="<?php echo base_url('assets/picker/picker.js'); ?>"></script>
    <script src="<?php echo base_url('assets/picker/picker.date.js'); ?>"></script>
    <script src="<?php echo base_url('assets/custom.js'); ?>"></script>


    <?= $this->renderSection("content"); ?>
    <div class="page_loader"></div>



</body>

</html>