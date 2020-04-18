<?php

include 'ForageSdk.php';
$lib = new index();
$action = Util::param('action');
$lib->$aciton();

class index
{
    public function __construct()
    {
    }

    public function choose() {
        $mark = Util::param('mark');
        if (in_array($mark, ['like', 'change', 'ban'])) {
            $index = Util::param('id');
            $a = new BasicClient();
            $a->dealRecord($index, $mark);
            if ($mark != 'like') $a->sendDingTalkMsg();
        }
        header("location:".getenv("HTTP_REFERER"));
    }
}