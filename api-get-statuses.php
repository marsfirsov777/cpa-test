<?php require_once($_SERVER['DOCUMENT_ROOT']  . '/api.php'); ?>
<?php

$page = isset($_GET['page']) ? (int)$_GET['page'] : 0;
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] . ' 00:00:00' : date('Y-m-d 00:00:00', strtotime('-30 days'));
$date_to   = isset($_GET['date_to']) ? $_GET['date_to'] . ' 23:59:59' : date('Y-m-d 23:59:59');
$limit     = 100;

$data = get_statuses($date_from, $date_to, $page, $limit);

echo $data;