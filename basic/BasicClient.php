<?php

/**
 * Created by PhpStorm.
 * User: ASUS
 * Date: 2020/4/18
 * Time: 13:11
 */
class BasicClient
{
    private $placeFile = FORAGE_AUTOLOADER_PATH.'/config/place.json';
    private $recordFile = FORAGE_AUTOLOADER_PATH.'/config/record.json';
    private $index = 0;

    public function syncData() {
        global $config;
        $lib = new SearchPlace();
        $page = 0;
        $pageSize = 20;
        $total = 1;
        $ak = isset($config['ak']) ? $config['ak'] : '';
        $lib->setPageSize($pageSize);
        $lib->setAK($ak);

        $data = [];
        while ($page*$pageSize < $total) {
            $lib->setPage($page);
            $list = $lib->search();
            if (empty($list)) break;
            if ($list['total'] <= 0) break;
            $total = $list['total'];
            foreach ($list['results'] as $result) {
                $tmp = [
                    'name'      => $result['name'],
                    'location'  => $result['location'],
                    'address'   => $result['address'],
                    'telephone' => isset($result['telephone']) ? $result['telephone'] : '暂无',
                ];
                if ($result['detail'] == 1 && !empty($result['detail_info'])) {
                    if (strpos($result['detail_info']['tag'], '咖啡') !== false || strpos($result['detail_info']['tag'], '蛋糕') !== false || strpos($result['detail_info']['tag'], '甜品') !== false) continue;
                    $tmp['distance']    = $result['detail_info']['distance'];
                    $tmp['detail_url']  = $result['detail_info']['detail_url'];
                    $tmp['price']       = isset($result['detail_info']['price']) ? $result['detail_info']['price'] : '暂无';
                    $tmp['overall_rating']  = isset($result['detail_info']['overall_rating']) ? $result['detail_info']['overall_rating'] : '暂无';
                    $tmp['comment_num'] = isset($result['detail_info']['comment_num']) ? $result['detail_info']['comment_num'] : '暂无';
                }
                $data[] = $tmp;
            }
            $page++;
        }
        if (!empty($data)) file_put_contents($this->placeFile, json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    private function randomFood() {
        $list = json_decode(file_get_contents($this->placeFile), true);
        $records = json_decode(file_get_contents($this->recordFile), true);
        $total = count($list);
        $like_list = $records['like_list'];
        $randoms = range(0, $total - 1);
        $randoms = array_diff($randoms, $like_list, $records['ban_list'], $records['filter']);
        $like_list = array_diff($like_list, $records['filter']);
        $new_list = array_merge($like_list, array_rand($randoms, 5));
        $key = array_rand($new_list);
        $index = $new_list[$key];
        $records['filter'][] = $index;
        $this->index = $index;
        file_put_contents($this->recordFile, json_encode($records, JSON_UNESCAPED_UNICODE));
        return $list[$index];
    }

    public function dealRecord($index, $mark = 'like') {
        $records = json_decode(file_get_contents($this->recordFile), true);
        switch ($mark) {
            case 'like':
                if (in_array($index, $records['like_list'])) return false;
                $records['like_list'][] = $index;
                break;
            case 'ban':
                if (in_array($index, $records['ban_list'])) return false;
                $records['ban_list'][] = $index;
                break;
            case 'change':
                if (in_array($index, $records['change'])) return false;
                $records['change'][] = $index;
                break;
        }
        file_put_contents($this->recordFile, json_encode($records, JSON_UNESCAPED_UNICODE));
        return true;
    }

    public function clearFilter() {
        $records = json_decode(file_get_contents($this->recordFile), true);
        $records['filter'] = [];
        $records['change'] = [];
        file_put_contents($this->recordFile, json_encode($records, JSON_UNESCAPED_UNICODE));
    }

    public function sendDingTalkMsg() {
        $lib = new DingTalkClient();
        $data = $this->randomFood();
        $tpl = "![screenshot](%slunch.jpg)  
        ### %s  
        地址：%s   距离:%s米  
        人均：%s   评分:%s   评价人数:%s";
        $text = sprintf($tpl, ROOTURL, $data['name'], $data['address'], $data['distance'], $data['price'], $data['overall_rating'], $data['comment_num']);
        $lib->sendActionCard($this->index, '', $text, $data['detail_url']);
    }
}