<?php

namespace App\Services;

use Exception;

class AmoCRM
{
    private $accessToken;
    private $urlContacts;
    private $urlLeads;
    private $headers;
    private $over30SecFieldId;

    public function __construct($accessToken, $urlContacts, $urlLeads, $over30SecFieldId)
    {
        $this->accessToken = $accessToken;
        $this->urlContacts = $urlContacts;
        $this->urlLeads = $urlLeads;
        $this->over30SecFieldId = $over30SecFieldId;
        $this->headers = [
            "Authorization: Bearer $this->accessToken",
            "Content-Type: application/json",
        ];
    }

    private function sendRequest($url, $data)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $decoded_response_lead = json_decode($response, true);

        /* debug
        echo "<pre>"; 
        echo "Ответ сервера на создание сделки:\n"; 
        print_r($decoded_response_lead); 
        echo "</pre>"; 
        */

        return $decoded_response_lead;

        if ($httpCode !== 200) {
            throw new Exception("Ошибка запроса: $httpCode");
        }
        return json_decode($response, true);
    }

    public function createContact($name, $email, $phone)
    {
        $contactData = [
            'name' => $name,
            'custom_fields_values' => [
                [
                    'field_code' => 'EMAIL',
                    'values' => [['value' => $email, 'enum_code' => 'WORK']]
                ],
                [
                    'field_code' => 'PHONE',
                    'values' => [['value' => $phone, 'enum_code' => 'WORK']]
                ]
            ]
        ];

        $response = $this->sendRequest($this->urlContacts, [$contactData]);
        if (isset($response['_embedded']['contacts'][0]['id'])) {
            return $response['_embedded']['contacts'][0]['id'];
        }
        throw new Exception("Ошибка при создании контакта.");
    }

    public function createLead($contactId, $price, $over30Sec)
    {
        $leadData = [
            'name' => 'Новая заявка с сайта',
            'price' => (int)$price,
            '_embedded' => [
                'contacts' => [
                    ['id' => $contactId]
                ]
            ],
            'custom_fields_values' => [
                [
                    'field_id' => $this->over30SecFieldId,
                    'values' => [['value' => (bool)$over30Sec]]
                ]
            ]
        ];

        return $this->sendRequest($this->urlLeads, [$leadData]);
    }
}
