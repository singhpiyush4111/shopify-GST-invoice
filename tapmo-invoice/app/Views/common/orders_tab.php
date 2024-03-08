<div class="has-sections card">
  <ul class="tabs">
    <li class="<?= checkTabActive() ?>"><a href="<?= tabLink() ?>">Orders</a></li>
    <li class="<?= checkTabActive(true) ?>"><a href="<?= tabLink(true) ?>">Draft Orders</a></li>
    <li>


    </li>
  </ul>



</div>
<article>
  <div class="card has-sections filter">

    <div class="card-section">
      <form id="add-filter">
        <div class="column three">
          <div class="date-pair">
            <input class="datePair" name="start_date" type="text" placeholder="Start Date" value="<?= isset($_GET['start_date']) ? $_GET['start_date'] : '' ?>" />
            <input class="datePair" name="end_date" type="text" placeholder="End Date" value="<?= isset($_GET['end_date']) ? $_GET['end_date'] : '' ?>" />
          </div>
        </div>
        <div class="column two">
          <select name="limit">
            <option>Limit</option>
            <?php
            foreach ([10, 20, 50, 100, 150, 200, 250] as $value) {
              $selected = isset($_GET['limit']) && !empty($_GET['limit'] && $_GET['limit'] == $value) ? "selected" : "";
            ?>
              <option value="<?= $value ?>" <?= $selected ?>><?= $value ?></option>
            <?php
            }
            ?>
          </select>
        </div>

        <div class="column one">
          <button type="button" id="onSubmit">Submit</button>
        </div>
        <div class="column one">
          <button type="button" name="clear" id="clearAndSubmit">Clear and submit</button>
        </div>
        <div class="column two">
          <button type="button" name="clear" id="bulkDownload">Bulk Download</button>
        </div>
        <div class="column one">
          <label><input type="checkbox" name="original" <?= isset($_GET['original']) && !empty($_GET['original']) ? "checked" : "" ?>>Original</label>
        </div>
      </form>

    </div>
  </div>

</article>


<style>
  .date-pair {
    display: flex;
    align-content: center;
    justify-content: center;
    align-items: center;
    flex-direction: row;

  }

  .filter {
    margin-top: 20px;
    margin-left: 20px;
  }
</style>
<script>
  clearFilter = false;
  $('.datePair').pickadate({
    format: 'yyyy-mm-dd',
    formatSubmit: 'yyyy-mm-dd',
    hiddenName: true,
    clear: ''

  })
</script>