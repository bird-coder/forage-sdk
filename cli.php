<?php
include 'ForageSdk.php';

$lib = new BasicClient();
if (date('w') == 1) $lib->clearFilter();
$lib->sendDingTalkMsg();