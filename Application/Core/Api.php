<?php

namespace Core;

class Api
{
    private array $request;

    private string $message;

    public function getTimes() {
        try {
            if (false === ($res = $this->query('api/v1/dictionary/get/times'))) {
                throw new \Exception('error', 1);
            }

            if (null === ($res = $this->parse($res))) {
                throw new \Exception('error', 1);
            }
            return $res['times'] ?? [];
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getDays() {
        try {
            if (false === ($res = $this->query('api/v1/dictionary/get/days'))) {
                throw new \Exception('error', 1);
            }

            if (null === ($res = $this->parse($res))) {
                throw new \Exception('error', 1);
            }
            return $res['days'] ?? [];
        } catch (\Exception $e) {
            return null;
        }
    }


    private function query(string $path, array $params = [], string $method = 'GET')
    {
        $ch = curl_init($_ENV['domain'] . $path . ($method === 'GET' ? '?' . http_build_query($params) : ''));

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Accept: application/json",
            "Content-Type: application/json"]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);


        switch ($method) {
            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                break;
            case 'PUT':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
                break;
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
                break;
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        $res = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        $this->request['status'] = $info['http_code'];

        return $res;
    }

    private function parse(string $res)
    {
        $res = json_decode($res, true);
        $res = json_last_error() === JSON_ERROR_NONE ? $res : null;
        if (isset($res['ok']) && (int)$res['ok'] === 1) {
            $res = $res['content'] ?? [];
        } else {
            $this->message = $res['content']['message'] ?? 'Неизвестная ошибка';
            $res = null;
        }
        return $res;
    }

    public function getRequest(): ?array
    {
        return $this->request;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

}