<?php
require_once('./api.php');

if($_SERVER['REQUEST_METHOD'] == 'POST') {

    $firstName = filter_input(INPUT_POST, 'first-name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $lastName  = filter_input(INPUT_POST, 'last-name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $phone     = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_SPECIAL_CHARS);
    $email     = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $box_id     = filter_input(INPUT_POST, 'box-id', FILTER_SANITIZE_NUMBER_INT);
    $offer_id     = filter_input(INPUT_POST, 'offer-id', FILTER_SANITIZE_NUMBER_INT);
    $country_code     = filter_input(INPUT_POST, 'country-code', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $language     = filter_input(INPUT_POST, 'language', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $password     = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $ip     = filter_input(INPUT_POST, 'ip', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $landingUrl     = filter_input(INPUT_POST, 'landing-url', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // Валідація
    $isValid = true;

    // Мінімальна перевірка: всі обов’язкові поля не порожні
    $requiredFields = [
        $firstName, $lastName, $phone, $email, $box_id, $offer_id,
        $country_code, $language, $password, $ip, $landingUrl
    ];

    foreach ($requiredFields as $field) {
        if (empty($field)) {
            $isValid = false;
            break;
        }
    }

    // Додаткова перевірка email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $isValid = false;
    }

    // Перевірка телефону — простий формат
    if (!preg_match('/^\+?[0-9]{10,15}$/', $phone)) {
        $isValid = false;
    }

    // Повертаємо false, якщо щось не так
    if (!$isValid) {
        echo json_encode(['status' => false, 'message' => 'Невірні або порожні поля']);
        exit;
    }

        //echo json_encode(['success' => true, 'message' => 'Дані валідні']);

    $data = [
        "firstName"   => $firstName,
        "lastName"    => $lastName,
        "phone"       => $phone,
        "email"       => $email,
        "countryCode" => $country_code,
        "box_id"      => $box_id,       // пример значения
        "offer_id"    => $offer_id,       // пример значения
        "landingUrl"  => $landingUrl,
        "ip"          => $ip,
        "password"    => $password,
        "language"    => $language,
        "clickId"     => "click123",
        "quizAnswers" => "",
        "custom1"     => "",
        "custom2"     => "",
        "custom3"     => ""
    ];

    echo add_lead($data);
}