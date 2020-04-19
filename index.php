<?php

include 'ForageSdk.php';
$lib = new index();
$action = Util::param('action');
$lib->$action();

class index
{
    public function __construct()
    {
        Log::write('receive params: '.urldecode(json_encode($_REQUEST)));
    }

    public function choose() {
        $mark = Util::param('mark');
        if (in_array($mark, ['like', 'change', 'ban'])) {
            $index = Util::param('id');
            if (!empty($index)) {
                $a = new BasicClient();
                if ($a->dealRecord($index, $mark)) {
                    if ($mark != 'like') $a->sendDingTalkMsg();
                }
            }
        }
        echo "<script>alert('操作成功');history.go(-1);</script>";
    }
}