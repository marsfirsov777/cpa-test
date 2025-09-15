<?php

$token = "ba67df6a-a17c-476f-8e95-bcdb75ed3958";

function add_lead(array $data) {

    global $token;

    $jsonData = json_encode($data);
    $url = 'https://crm.belmar.pro/api/v1/addlead';

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'token: ' . $token
    ]);

    $response = curl_exec($ch);

    if(curl_errno($ch)) {
        $response = json_encode(['status' => false, 'message' => 'Помилка: ' . curl_error($ch)]);
    }

    curl_close($ch);

    return $response;
}

function get_statuses(string $date_from, string $date_to, int $page = 0, int $limit = 100) {

    global $token;

    $url = 'https://crm.belmar.pro/api/v1/getstatuses';

    $postData = json_encode([
        'date_from' => $date_from,
        'date_to' => $date_to,
        'page' => $page,
        'limit' => $limit
    ]);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'token: ' . $token
    ]);

    $response = curl_exec($ch);

    if(curl_errno($ch)) {
        $response = json_encode(['status' => false, 'message' => 'Помилка: ' . curl_error($ch)]);
    }
    curl_close($ch);
    
    return $response;
}

function render_pagination(int $currentPage, int $totalPages, string $baseUrl = '', array $queryParams = [], int $visiblePages = 10): string {
    if ($totalPages <= 1) return ''; // Не треба

    $html = '<ul class="pagination justify-content-center">';

    // функция генерації URL с параметрами
    $buildUrl = function ($page) use ($baseUrl, $queryParams) {
        $queryParams['page'] = $page;
        return $baseUrl . '?' . http_build_query($queryParams);
    };

    // Кнопка "назад"
    $prevPage = max(0, $currentPage - 1);
    $disabled = $currentPage <= 0 ? ' disabled' : '';
    $html .= '<li class="page-item' . $disabled . '"><a class="page-link" href="' . ($disabled ? '#' : $buildUrl($prevPage)) . '">←</a></li>';

    // Діапазон сторінок
    $start = max(0, $currentPage - floor($visiblePages / 2));
    $end = $start + $visiblePages - 1;
    if ($end >= $totalPages) {
        $end = $totalPages - 1;
        $start = max(0, $end - $visiblePages + 1);
    }

    // Якщо треба показати першу ...
    if ($start > 0) {
        $html .= '<li class="page-item"><a class="page-link" href="' . $buildUrl(0) . '">1</a></li>';
        if ($start > 1) {
            $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }

    // Основний діапазон
    for ($i = $start; $i <= $end; $i++) {
        $active = $i === $currentPage ? ' active' : '';
        $html .= '<li class="page-item' . $active . '"><a class="page-link" href="' . $buildUrl($i) . '">' . ($i + 1) . '</a></li>';
    }

    // Якщо треба показати ... та останню
    if ($end < $totalPages - 1) {
        if ($end < $totalPages - 2) {
            $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
        $html .= '<li class="page-item"><a class="page-link" href="' . $buildUrl($totalPages - 1) . '">' . $totalPages . '</a></li>';
    }

    // Кнопка "вперед"
    $nextPage = min($totalPages - 1, $currentPage + 1);
    $disabled = $currentPage >= $totalPages - 1 ? ' disabled' : '';
    $html .= '<li class="page-item' . $disabled . '"><a class="page-link" href="' . ($disabled ? '#' : $buildUrl($nextPage)) . '">→</a></li>';

    $html .= '</ul>';
    return $html;
}