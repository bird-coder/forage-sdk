<?php


class DingTalkClient
{
    public function curl($url, $postFields = null)
    {
        $header = array("Content-Type: application/json;charset=utf-8");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        //https 请求
        if(strlen($url) > 5 && strtolower(substr($url,0,5)) == "https" ) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }

        $reponse = curl_exec($ch);

        if (curl_errno($ch))
        {
            throw new Exception(curl_error($ch),0);
        }
        else
        {
            $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if (200 !== $httpStatusCode)
            {
                throw new Exception($reponse,$httpStatusCode);
            }
        }
        curl_close($ch);
        return $reponse;
    }

    private function getMillisecond() {
        list($s1, $s2) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000);
    }

    private function getCanonicalStringForIsv($timestamp, $suiteTicket) {
        $result = $timestamp;
        if($suiteTicket != null) {
            $result .= "\n".$suiteTicket;
        }
        return $result;
    }

    private function computeSignature($accessSecret, $canonicalString) {
        $s = hash_hmac('sha256', $canonicalString, $accessSecret, true);
        return urlencode(base64_encode($s));
    }

    private function getUrl() {
        global $config;
        $timestamp = $this->getMillisecond();
        $appSecret = $config['appSecret'];
        $access_token = $config['access_token'];
        $webhook = $config['webhook'].$access_token;
        $sign = $this->computeSignature($appSecret, $this->getCanonicalStringForIsv($timestamp, $appSecret));
        return $webhook.'&timestamp='.$timestamp.'&sign='.$sign;
    }

    public function sendText($msg = "我就是我, 是不一样的烟火") {
        $webhook = $this->getUrl();
        $data = array ('msgtype' => 'text','text' => array ('content' => $msg));
        $data_string = json_encode($data);
        try{
            $result = $this->curl($webhook, $data_string);
            Log::write($result, 'debug');
        } catch (Exception $e) {
            Log::write($e->getMessage());
        }
    }

    public function sendActionCard($index, $title = '', $text = '', $url) {
        $webhook = $this->getUrl();
        $data = [
            'msgtype'   => 'actionCard',
            'actionCard'=> [
                'title' => $title ?: '今天的推荐',
                'text'  => $text ?: '123',
                'btnOrientation'=> 0,
                'btns'  => [
                    [
                        'title' => '收藏',
                        'actionURL' => ROOTURL.'index.php?action=choose&mark=like&id='.$index
                    ],
                    [
                        'title' => '就它了',
                        'actionURL' => $url
                    ],
                    [
                        'title' => '换一个',
                        'actionURL' => ROOTURL.'index.php?action=choose&mark=change&id='.$index
                    ],
                    [
                        'title' => '拉黑',
                        'actionURL' => ROOTURL.'index.php?action=choose&mark=ban&id='.$index
                    ]
                ]
            ]
        ];
        try{
            $result = $this->curl($webhook, json_encode($data));
            Log::write($result, 'debug');
        } catch (Exception $e) {
            Log::write($e->getMessage());
        }
    }
}