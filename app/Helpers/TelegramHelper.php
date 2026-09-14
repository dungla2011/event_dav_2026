<?php

namespace App\Helpers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class TelegramHelper
{
    /**
     * Gửi tin nhắn qua Telegram Bot
     * 
     * @param string $botToken Bot token từ BotFather
     * @param string $chatId Chat ID hoặc Channel ID
     * @param string $message Nội dung tin nhắn
     * @param array $options Các tùy chọn bổ sung (parse_mode, disable_web_page_preview, etc.)
     * @return array Kết quả gửi tin
     */
    public static function sendMessage($botToken, $chatId, $message, $options = [])
    {
        try {
            // Validate input
            if (empty($botToken) || empty($chatId) || empty($message)) {
                return [
                    'success' => false,
                    'error' => 'Missing required parameters: botToken, chatId, or message'
                ];
            }

            $client = new Client([
                'timeout' => 30,
                'connect_timeout' => 10
            ]);

            // Chuẩn bị data gửi
            $data = array_merge([
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'HTML', // Hỗ trợ HTML formatting
                'disable_web_page_preview' => true
            ], $options);

            // URL Telegram Bot API
            $url = "https://api.telegram.org/bot{$botToken}/sendMessage";

            // Gửi request
            $response = $client->post($url, [
                'json' => $data,
                'headers' => [
                    'Content-Type' => 'application/json'
                ]
            ]);

            $statusCode = $response->getStatusCode();
            $responseBody = json_decode($response->getBody()->getContents(), true);

            if ($statusCode === 200 && $responseBody['ok'] === true) {
                Log::info('Telegram message sent successfully', [
                    'chat_id' => $chatId,
                    'message_id' => $responseBody['result']['message_id'] ?? null
                ]);

                return [
                    'success' => true,
                    'message_id' => $responseBody['result']['message_id'] ?? null,
                    'response' => $responseBody
                ];
            } else {
                Log::error('Telegram API returned error', [
                    'status_code' => $statusCode,
                    'response' => $responseBody
                ]);

                return [
                    'success' => false,
                    'error' => $responseBody['description'] ?? 'Unknown error from Telegram API',
                    'error_code' => $responseBody['error_code'] ?? null
                ];
            }

        } catch (RequestException $e) {
            $errorMessage = $e->getMessage();
            
            // Lấy response body nếu có
            if ($e->hasResponse()) {
                $responseBody = $e->getResponse()->getBody()->getContents();
                $responseData = json_decode($responseBody, true);
                
                if ($responseData && isset($responseData['description'])) {
                    $errorMessage = $responseData['description'];
                }
            }

            Log::error('Telegram request failed', [
                'error' => $errorMessage,
                'chat_id' => $chatId
            ]);

            return [
                'success' => false,
                'error' => $errorMessage
            ];
            
        } catch (\Exception $e) {
            Log::error('Telegram helper unexpected error', [
                'error' => $e->getMessage(),
                'chat_id' => $chatId
            ]);

            return [
                'success' => false,
                'error' => 'Unexpected error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Gửi tin nhắn tới nhiều chat ID
     * 
     * @param string $botToken Bot token
     * @param array $chatIds Mảng các chat ID
     * @param string $message Nội dung tin nhắn
     * @param array $options Tùy chọn bổ sung
     * @return array Kết quả gửi tin
     */
    public static function sendMessageToMultiple($botToken, $chatIds, $message, $options = [])
    {
        $results = [];
        $successCount = 0;
        $failCount = 0;

        foreach ($chatIds as $chatId) {
            $result = self::sendMessage($botToken, trim($chatId), $message, $options);
            
            $results[$chatId] = $result;
            
            if ($result['success']) {
                $successCount++;
            } else {
                $failCount++;
            }

            // Thêm delay nhỏ để tránh rate limit
            usleep(100000); // 0.1 giây
        }

        return [
            'success' => $successCount > 0,
            'total' => count($chatIds),
            'success_count' => $successCount,
            'fail_count' => $failCount,
            'results' => $results
        ];
    }

    /**
     * Kiểm tra bot token và chat ID có hợp lệ không
     * 
     * @param string $botToken
     * @param string $chatId
     * @return array
     */
    public static function validateConnection($botToken, $chatId)
    {
        return self::sendMessage($botToken, $chatId, '🤖 Test connection - Bot hoạt động bình thường!');
    }

    /**
     * Lấy thông tin về bot
     * 
     * @param string $botToken
     * @return array
     */
    public static function getBotInfo($botToken)
    {
        try {
            $client = new Client(['timeout' => 10]);
            $url = "https://api.telegram.org/bot{$botToken}/getMe";
            
            $response = $client->get($url);
            $responseBody = json_decode($response->getBody()->getContents(), true);
            
            if ($responseBody['ok'] === true) {
                return [
                    'success' => true,
                    'bot_info' => $responseBody['result']
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $responseBody['description'] ?? 'Invalid bot token'
                ];
            }
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
