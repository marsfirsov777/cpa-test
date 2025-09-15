<?php require_once($_SERVER['DOCUMENT_ROOT']  . '/api.php'); ?>
<?php

$page = isset($_GET['page']) ? (int)$_GET['page'] : 0;
$date_from = isset($_GET['date_from']) ? filter_input(INPUT_GET, 'date_from', FILTER_SANITIZE_FULL_SPECIAL_CHARS) . ' 00:00:00' : date('Y-m-d 00:00:00', strtotime('-30 days'));
$date_to   = isset($_GET['date_to']) ? filter_input(INPUT_GET, 'date_to', FILTER_SANITIZE_FULL_SPECIAL_CHARS) . ' 23:59:59' : date('Y-m-d 23:59:59');
$limit     = 100;

$data = get_statuses($date_from, $date_to, $page, $limit);

echo $data;