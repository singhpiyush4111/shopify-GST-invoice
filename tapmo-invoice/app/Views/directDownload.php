<?= $this->extend('common/layout') ?>

<?= $this->section('content') ?>

<script>
  $(document).ready(function() {
    $('.page_loader').show();
    directDownload("", true);
  });
</script>

<?= $this->endSection() ?>