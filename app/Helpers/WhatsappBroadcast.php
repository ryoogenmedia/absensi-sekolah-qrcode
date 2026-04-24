<?php

namespace App\Helpers;

class WhatsappBroadcast
{
    //! ENDPOINT METHOD
    public function endpoint()
    {
        return [
            // SEND ENDPOINT
            "send_file"     => "/api/send/file",
            "send_text"     => "/api/send/text",
            "send_image"    => "/api/send/image",
            "send_contact"  => "/api/send/contact",
            "send_location" => "/api/send/location",

            // ENDPOINT GET QRCODE
            "get_qrcode"         => "/api/qrcode",
            "status_scan_qrcode" => "/api/scan-status",

            // DEVICE ENDPOINT
            "device_disconnect"    => "/api/device/disconnect",
            "device_delete"        => "/api/device/delete",
            "device_restart"       => "/api/device/restart",
            "device_connection"    => "/api/device/connection-state",
            "device_battery"       => "/api/device/battery",
            "device_status"        => "/api/device/status-connected",

            // PROFILE
            "update_profile" => "/api/profile",
        ];
    }

    //! CONFIGURATION
    public function config()
    {
        $baseDB = base_whatsapp(); // helper user-defined

        $urlBaseDB   = $baseDB['url'] ?? null;
        $portBaseDB  = $baseDB['port'] ?? null;
        $portBaseENV = config('whatsapp.whatsapp_port');
        $urlBaseENV  = config('whatsapp.whatsapp_url');

        return [
            'url'  => $urlBaseDB  ?? $urlBaseENV,
            'port' => $portBaseDB ?? $portBaseENV,
        ];
    }

    //! SEND REQUEST HELPERS
    protected function sendRequest($method, $endpoint, $payload = [], $isMultipart = false)
    {
        $config = $this->config();
        $url = $config['url'] . ($config['port'] ? ':' . $config['port'] : '') . $endpoint;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $isMultipart ? $payload : json_encode($payload));
            if (!$isMultipart) {
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            }
        }

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new \Exception("Curl error: $error");
        }

        return $response;
    }

    //! QR CODE
    public function getQrCode()
    {
        return $this->sendRequest('GET', $this->endpoint()['get_qrcode']);
    }
    public function getScanQrCodeStatus()
    {
        return $this->sendRequest('GET', $this->endpoint()['status_scan_qrcode']);
    }

    //! SEND MESSAGE
    public function sendText($from, $text)
    {
        return $this->sendRequest('POST', $this->endpoint()['send_text'], [
            'from' => $from,
            'text' => $text
        ]);
    }

    public function sendImage($from, $image, $imageName, $caption = '')
    {
        return $this->sendRequest('POST', $this->endpoint()['send_image'], [
            'from'       => $from,
            'image'      => $image,
            'image_name' => $imageName,
            'caption'    => $caption,
        ]);
    }

    public function sendFile($from,$filePath, $fileName, $caption = '')
    {
        if (!file_exists($filePath)) {
            throw new \Exception("File not found at path: $filePath");
        }

        return $this->sendRequest('POST', $this->endpoint()['send_file'], [
            'from'      => $from,
            'file_name' => $fileName,
            'file'      => new \CURLFile($filePath),
            'caption'   => $caption,
        ], true);
    }


    public function sendContact($to, $contactId)
    {
        return $this->sendRequest('POST', $this->endpoint()['send_contact'], [
            'to'        => $to,
            'contactId' => $contactId,
        ]);
    }

    public function sendLocation($to, $lat, $lng, $description)
    {
        return $this->sendRequest('POST', $this->endpoint()['send_location'], [
            'to'          => $to,
            'lat'         => $lat,
            'lng'         => $lng,
            'description' => $description,
        ]);
    }

    //! DEVICE
    public function disconnectDevice()
    {
        return $this->sendRequest('GET', $this->endpoint()['device_disconnect']);
    }

    public function deleteDevice()
    {
        return $this->sendRequest('GET', $this->endpoint()['device_delete']);
    }

    public function restartDevice()
    {
        return $this->sendRequest('GET', $this->endpoint()['device_restart']);
    }

    public function getDeviceConnection()
    {
        return $this->sendRequest('GET', $this->endpoint()['device_status']);
    }

    public function getBatteryLevel()
    {
        return $this->sendRequest('GET', $this->endpoint()['device_battery']);
    }

    public function isDeviceConnected()
    {
        return $this->sendRequest('GET', $this->endpoint()['device_connection']);
    }

    //! PROFILE
    public function updateProfile($name = null, $status = null, $picture = null)
    {
        $data = [];

        if (!empty($name)) {
            $data['name'] = $name;
        }

        if (!empty($status)) {
            $data['status'] = $status;
        }

        if (!empty($picture)) {
            $data['picture'] = $picture;
        }

        if (empty($data)) {
            throw new \InvalidArgumentException('At least one of name, status, or picture must be provided.');
        }

        return $this->sendRequest('POST', $this->endpoint()['update_profile'], $data);
    }

}
