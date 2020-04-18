<?php

/**
 * Created by PhpStorm.
 * User: ASUS
 * Date: 2020/4/18
 * Time: 13:36
 */
class SearchPlace
{
    private $url = 'http://api.map.baidu.com/place/v2/search?query=%s&tag=%s&location=%s&radius=%s&output=%s&scope=%s&filter=%s&coord_type%d&page_size=%d&page_num=%d&ak=%s';

    private $query = '美食';

    private $tag = '美食,餐厅';

    private $location = '31.179166,121.420976';

    private $radius = '500';

    private $output = 'json';

    private $scope = '2';

    private $filter = 'industry_type:cater|sort_name:default|sort_rule:0|price_section:15,100';

    private $coord_type = 3;

    private $page_size = 20;

    private $page_num = 0;

    private $ak;

    public function search() {
        if (empty($this->ak)) return false;
        $url = sprintf($this->url, $this->query, $this->tag, $this->location, $this->radius, $this->output, $this->scope, $this->filter, $this->coord_type, $this->page_size, $this->page_num, $this->ak);
        $output = Util::curl($url);
        $data = json_decode($output, true);
        $list = [];
        if (!empty($data) && $data['status'] == 0) {
            $list['total'] = $data['total'];
            $list['results'] = $data['results'];
        }
        return $list;
    }

    public function setAK($ak) {
        $this->ak = $ak;
    }

    public function setLocation($latitude, $longitude) {
        $this->location = $latitude.','.$longitude;
    }

    public function setFilter($filter) {
        $this->filter = $filter;
    }

    public function setQuery($query) {
        $this->query = $query;
    }

    public function setTag($tag) {
        $this->tag = $tag;
    }

    public function setRadius($radius) {
        $this->radius = $radius;
    }

    public function setPage($page) {
        $this->page_num = $page;
    }

    public function setPageSize($pageSize) {
        $this->page_size = $pageSize;
    }
}