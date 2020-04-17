<?php


class BasicClient
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

    private function computeSignature($accessSecret, $canonicalString){
        $s = hash_hmac('sha256', $canonicalString, $accessSecret, true);
        return urlencode(base64_encode($s));
    }

    public function exec(){
        $timestamp = $this->getMillisecond();
        $appSecret = 'SECb5ebb3601f1aa76bf75452d6c1dccce13b1024bf400c88914105f4e4e34ac2d1';
        $sign = $this->computeSignature($appSecret, $this->getCanonicalStringForIsv($timestamp, $appSecret));
        $webhook = "https://oapi.dingtalk.com/robot/send?access_token=dbe2e460a51d922f357e9b3018420ee30e34d19a823cefe47fd38973ed96c303";
        $webhook .= '&timestamp='.$timestamp.'&sign='.$sign;
        $message="我就是我, 是不一样的烟火";
        $data = array ('msgtype' => 'text','text' => array ('content' => $message));
        $data_string = json_encode($data);

        try{
            $result = $this->curl($webhook, $data_string);
            echo $result;
        } catch (Exception $e) {
            var_dump($e->getMessage());
        }
    }
}