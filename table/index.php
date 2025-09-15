<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/header.php'); ?>
<?php require_once($_SERVER['DOCUMENT_ROOT']  . '/api.php'); ?>
<?php

$page = isset($_GET['page']) ? (int)$_GET['page'] : 0;
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] . ' 00:00:00' : date('Y-m-d 00:00:00', strtotime('-30 days'));
$date_to   = isset($_GET['date_to']) ? $_GET['date_to'] . ' 23:59:59' : date('Y-m-d 23:59:59');
$limit     = 100;

$data = json_decode(get_statuses($date_from, $date_to, $page, $limit));

/* echo '<pre>';
print_r($data); */
?>

<div class="container py-4 status-page">

  <h2>Статуси</h2>

  <!-- Фильтр -->
  <form id="filter-form" class="row g-3 mb-4">
    <div class="col-md-3">
      <label for="date_from" class="form-label">Дата з:</label>
      <input type="date" class="form-control" id="date_from" name="date_from" value="<?php echo $_GET['date_from'] ?? '' ?>">
    </div>
    <div class="col-md-3">
      <label for="date_to" class="form-label">Дата по:</label>
      <input type="date" class="form-control" id="date_to" name="date_to" value="<?php echo $_GET['date_to'] ?? '' ?>">
    </div>
    <div class="col-md-2 align-self-end">
      <div class="d-flex align-items-center">
        <button type="submit" class="btn btn-primary">Фільтрувати</button>
        <i class="bi bi-arrow-repeat ms-3 fs-4 animated-circle d-none" id="loader"></i>
      </div>
    </div>
  </form>

  <!-- Таблиця -->
  <table class="table table-striped" id="statuses-table">
    <thead>
      <tr>
        <th>ID</th>
        <th>Email</th>
        <th>Status</th>
        <th>FTD</th>
      </tr>
    </thead>
    <tbody>
        <?php if($data && $data->data): ?>

            <?php foreach($data->data as $status): ?>
                
                <tr>
                    <td><?php echo $status->id ?></td>
                    <td><?php echo $status->email ?></td>
                    <td><?php echo $status->status != '' ? $status->status : 'Невідомо' ?></td>
                    <td><?php echo $status->ftd ?></td>
                </tr>

            <?php endforeach; ?>

        <?php else: ?>

            <tr><td colspan="5" class="text-center">Немає данних</td></tr>
            
        <? endif; ?>
    </tbody>
  </table>

  <!-- Пагинація -->
  <!-- <nav aria-label="Пагінация">
    <ul class="pagination justify-content-center" id="pagination"></ul>
  </nav> -->

</div>

<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/footer.php'); ?>