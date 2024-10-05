<?php

namespace backend\controllers;
use Telegram\Bot\Api;
use yii\base\Controller;

class TelegramController extends Controller
{
    private const TELEGRAM_BOT_API_TOKEN = '7928375574:AAGawhrZDHTs2KJZQAfHlwTzgyTyyyHMWdU';

    public function actionIndex()
    {
        $telegram = new Api(self::TELEGRAM_BOT_API_TOKEN);
        echo "Fetching updates...\n";

        while (true) {
            $updates = $telegram->getUpdates();

            if (empty($updates)) {
                echo "Нет новых обновлений.\n";
            } else {
                $lastUpdateId = 0;
                foreach ($updates as $update) {
                    $lastUpdateId = max($lastUpdateId, $update->getUpdateId());

                    if ($update->getMessage()) {
                        $chatId = $update->getMessage()->getChat()->getId();
                        $text = $update->getMessage()->getText();

                        echo "Received message: $text\n";

                        if (preg_match('/^\/reg (.+) (.+) (.+)$/', $text, $matches)) {
                            $username = $matches[1];
                            $email = $matches[2];
                            $password = $matches[3];

                            $response = $this->sendCreateRequest($username, $email, $password);
                            $telegram->sendMessage(['chat_id' => $chatId, 'text' => $response]);
                        } elseif (preg_match('/^\/login (.+) (.+)$/', $text, $matches)) {
                            $username = $matches[1];
                            $password = $matches[2];

                            $response = $this->sendLoginRequest($username, $password);
                            $telegram->sendMessage(['chat_id' => $chatId, 'text' => $response['message']]);
                        } else {
                            $telegram->sendMessage(['chat_id' => $chatId, 'text' => 'Команда не найдена']);
                        }
                    }
                }

                $telegram->getUpdates(['offset' => $lastUpdateId + 1]);
            }

            sleep(5);
        }
    }

    public function sendCreateRequest($username, $email, $password)
    {
        $url = 'http://events.hyth.com/user/create';
        $data = json_encode([
            'username' => $username,
            'email' => $email,
            'password' => $password,
        ]);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data),
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    public function sendLoginRequest($username, $password)
    {
        $url = 'http://events.hyth.com/user/login';
        $data = json_encode([
            'username' => $username,
            'password' => $password,
        ]);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data),
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }
}