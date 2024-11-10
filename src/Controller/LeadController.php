<?php

namespace App\Controller;

use App\Services\AmoCRM;
use Exception;

class LeadController
{
    private $config;
    private $amoCRM;

    public function __construct($config)
    {
        $this->config = $config;
        $this->amoCRM = new AmoCRM(
            $this->config['amocrm']['access_token'],
            $this->config['amocrm']['url_contacts'],
            $this->config['amocrm']['url_leads'],
            $this->config['amocrm']['custom_fields']['over_30_sec_field_id']
        );
    }

    public function handleRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return json_encode(['status' => 'error', 'message' => 'Invalid request method']);
        }

        if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['phone']) || empty($_POST['price'])) {
            http_response_code(404);
            return json_encode(['status' => 'error', 'message' => 'The required information has not been sent.']);
        }

        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $price = trim($_POST['price']);
        $over30Sec = $_POST['over_30_sec'];

        $validationError = $this->validateForm($name, $email, $phone, $price);
        if ($validationError) {
            http_response_code(400);
            return json_encode(['status' => 'error', 'message' => $validationError]);
        }

        try {
            // Создание контакта и лида в AmoCRM
            $contactId = $this->amoCRM->createContact($name, $email, $phone);
            $responseLead = $this->amoCRM->createLead($contactId, $price, $over30Sec);

            if (isset($responseLead['_embedded']['leads'])) {
                http_response_code(200);
                return json_encode(['status' => 'success', 'message' => 'The request has been successfully submitted!']);
            } else {
                http_response_code(500);
                return json_encode(['status' => 'error', 'message' => 'Error sending the request.']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            return json_encode(['status' => 'error', 'message' => 'An error has occurred: ' . $e->getMessage()]);
        }
    }

    private function validateForm($name, $email, $phone, $price)
    {
        $errors = [];

        if (empty($name) || empty($email) || empty($phone) || empty($price)) {
            $errors[] = "Пожалуйста, заполните все поля.";
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Неверный формат email.";
        }

        if (!preg_match('/^\+?[0-9]{10,15}$/', $phone)) {
            $errors[] = "Неверный формат телефона. Используйте только цифры и, если необходимо, знак + в начале.";
        }

        if (!is_numeric($price) || $price <= 0) {
            $errors[] = "Цена должна быть положительным числом.";
        }

        return !empty($errors) ? implode(" ", $errors) : null;
    }
}
