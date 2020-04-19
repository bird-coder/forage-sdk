<?php

include 'ForageSdk.php';
$lib = new index();
$action = Util::param('action');
$lib->$action();

class index
{
    private $lockFile = './log/lock.txt';

    public function __construct()
    {
        Log::write('receive params: '.urldecode(json_encode($_REQUEST)));
    }

    public function choose() {
        if ($this->isLock()) return false;
        $this->lock();
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
        $this->unlock();
        echo "<script>alert('操作成功');history.go(-1);</script>";
    }

    private function isLock() {
        $time = intval(file_get_contents($this->lockFile));
        if (time() < $time + 1) return true;
        return false;
    }

    private function lock() {
        file_put_contents($this->lockFile, time());
    }

    private function unlock() {
        if (file_exists($this->lockFile)) unlink($this->lockFile);
    }
}