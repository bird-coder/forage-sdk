<?php

include 'ForageSdk.php';
$lib = new index();
$action = Util::param('action');
$lib->$action();

class index
{
    public function __construct()
    {
        Log::write('receive params: '.urldecode(file_get_contents("php://input")));
    }

    public function choose() {
        $mark = Util::param('mark');
        if (in_array($mark, ['like', 'change', 'ban'])) {
            $index = Util::param('id');
            $a = new BasicClient();
            if ($a->dealRecord($index, $mark)) {
                if ($mark != 'like') $a->sendDingTalkMsg();
            }
        }
        echo "<script>alert('操作成功');window.opener=null;window.open('','_self');window.close();</script>";
    }
}