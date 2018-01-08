<?php

use Phalcon\Mvc\View;

class CalendarController extends \Phalcon\Mvc\Controller {

    private $request_url;
    private $http_code;
    private $result_code;
    private $http_msg;
    //接口属性
    private $platTypeKey;
    private $platAccount;
    private $lang;

    public function initialize() {
        $this->request_url = 'https://www.netkiller.cn/api';
        $this->oauthKey = 'ade06a7ff83a0ce5';
        $this->platTypeKey = 'hengxin';
        $this->platAccount = 'hx';
        $this->timeStamp = time();
        $this->lang = 'zh';
        $this->token = md5($this->platAccount . $this->oauthKey . $this->timeStamp);
    }

    public function indexAction($date = '') {
        if ($date == '') {
            $date = date('Y-m-d');
        }
        $this->view->disableLevel(array(
            View::LEVEL_MAIN_LAYOUT => false
        ));
        /** HXPM * */
        $this->view->currNowDate = $date;
        $this->view->partial('calendar/hxpm');
        exit();

        $datas = $this->generator($date);
        if ($datas) {
            $this->view->setVar('datas', $datas);
        } else {
            $this->response->setStatusCode(404, 'Template Not Found');
            echo 'Template Not Found';
            return;
        }
    }

    //pc端简体 财经日历模板
    public function hxpmAction($day = '') {
        /** ajax ,固定模板页面
          $url = "http://www.netkiller.cn/cn/fe_calendar.html";
         * */
        $this->view->disableLevel(array(
            View::LEVEL_MAIN_LAYOUT => false
        ));
        $this->view->currNowDate = $date;
    }

    public function hxpmjsonAction() {
        $params = $this->request->getPost();
        $select = array('nowDate' => isset($params['nowDate']) ? $params['nowDate'] : date('Y-m-d'));

        $key = sprintf(":calendar:pc:hxpmjson:%s", $select['nowDate']);
        $html = null;
        $html = $this->cache->get($key);
        if ($html == null) {
            $html = $this->curl('http://news1.netkiller.cn/gwapi/financeIndex', $select);
            $this->cache->save($key, $html, 300);
        }
        echo $html;
        exit();
    }

    /**
     * 财经日历pc端简体模板
     * @param type $date
     * @return type
     */
    public function netkillerAction($date = '') {
        $this->view->disableLevel(array(
            View::LEVEL_MAIN_LAYOUT => false
        ));
        if ($date != '') {
            $date = str_replace('.html', '', $date);
        }
        $maxDate = date("Y-m-d", strtotime('+7 day'));
        if ($date > $maxDate) {
            $date = $maxDate;
        }
        $datas = $this->generator($date);
        if ($datas) {
            $this->view->setVar('datas', $datas);
        } else {
            $this->response->setStatusCode(404, 'Template Not Found');
            echo 'Template Not Found';
            return;
        }
    }

    /**
     * 财经日历app端简体模板
     * @param type $date
     * @return type
     */
    public function appAction($date = '') {
        $this->view->disableLevel(array(
            View::LEVEL_MAIN_LAYOUT => false
        ));
        if ($date != '') {
            $date = str_replace('.html', '', $date);
        }
        $maxDate = date("Y-m-d", strtotime('+7 day'));
        if ($date > $maxDate) {
            $date = $maxDate;
        }
        $datas = $this->app_generator($date);
        if ($datas) {
            $this->view->setVar('datas', $datas);
        } else {
            $this->response->setStatusCode(404, 'Template Not Found');
            echo 'Template Not Found';
            return;
        }
    }

    /**
     * 财经日历webui端简体模板
     * @param type $date
     * @return type
     */
    public function webuiAction($date = '') {
        $this->view->disableLevel(array(
            View::LEVEL_MAIN_LAYOUT => false
        ));
        if ($date != '') {
            $date = str_replace('.html', '', $date);
        }
        $maxDate = date("Y-m-d", strtotime('+7 day'));
        if ($date > $maxDate) {
            $date = $maxDate;
        }
        $datas = $this->webui_generator($date);
        if ($datas) {
            $this->view->setVar('datas', $datas);
        } else {
            $this->response->setStatusCode(404, 'Template Not Found');
            echo 'Template Not Found';
            return;
        }
    }

    /**
     * 财经日历pc端繁体模板
     * @param type $date
     * @return type
     */
    public function netkillertwAction($date = '') {
        $this->view->disableLevel(array(
            View::LEVEL_MAIN_LAYOUT => false
        ));
        if ($date != '') {
            $date = str_replace('.html', '', $date);
        }
        $maxDate = date("Y-m-d", strtotime('+7 day'));
        if ($date > $maxDate) {
            $date = $maxDate;
        }
        $datas = $this->tw_generator($date);
        if ($datas) {
            $this->view->setVar('datas', $datas);
        } else {
            $this->response->setStatusCode(404, 'Template Not Found');
            echo 'Template Not Found';
            return;
        }
    }

    /**
     * 财经日历app端简体模板
     * @param type $date
     * @return type
     */
    public function cfAction($date = '') {
        $this->view->disableLevel(array(
            View::LEVEL_MAIN_LAYOUT => false
        ));
        if ($date != '') {
            $date = str_replace('.html', '', $date);
        }
        $maxDate = date("Y-m-d", strtotime('+7 day'));
        if($date == "index?app=app"){
            $date = date("Y-m-d", time());
        }
        if ($date > $maxDate) {
            $date = $maxDate;
        }

		$key = sprintf(":calendar:html:cf:%s:", $date );
        $calendar = $this->cache->get($key);
        if ($calendar === null) {
                $datas = $this->cf_generator($date);
                $this->cache->save($key, $datas, 86400);
        }else{
                $datas = $calendar;
        }

        if ($datas) {
            $this->view->setVar('datas', $datas);
        } else {
            $this->response->setStatusCode(404, 'Template Not Found');
            echo 'Template Not Found';
            return;
        }
    }

    //pc端简体 财经日历模板
    private function generator($day = '') {
        if ($day == '') {
            $url = "http://www.netkiller.cn/zh/calendar/index.html";
        } else {
            $url = "http://www.netkiller.cn/zh/calendar/" . $day . ".html";
        }
        $key = sprintf(":calendar:pc:%s", $day);
        $html = null;
        $html = $this->cache->get($key);
        if ($html == null) {
            $html = $this->curl($url);
            $this->cache->save($key, $html, 300);
        }
        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->loadHTML($html);
        $xpath = new DOMXPath($dom);

        $xml = $xpath->query('/html/body/div[@class="wrapper"]');

        $xhtml = $dom->saveHTML($xml->item(0));
        $new_xhtml = str_replace(array('http://www.netkiller.cn/zh/calendar', '"/public/images'), array('/calendar/netkiller', '"http://www.netkiller.cn/public/images'), $xhtml);

        $new_xhtml = str_replace('<div class="banner">', '<div class="banner" style="margin-top:-419px;">', $new_xhtml);

        $new_xhtml = preg_replace('/<div class="tuig-box clearfix">.+?<\/div>/is', '', $new_xhtml);
        $new_xhtml = preg_replace('/<a href="\S+?" class="xiangq-btn">[^<]+<\/a>/is', '', $new_xhtml);
        $new_xhtml = preg_replace('/<div class="w1190">.+?<div class="ban-navbox">/is', '<div class="ban-navbox">', $new_xhtml);


        return($new_xhtml);
    }

    //pc端繁体 财经日历模板
    private function tw_generator($day = '') {
        if ($day == '') {
            $url = "http://www.netkiller.cn/tw/calendar/index.html";
        } else {
            $url = "http://www.netkiller.cn/tw/calendar/" . $day . ".html";
        }
        $key = sprintf(":calendar:pctw:%s", $day);
        $html = null;
        $html = $this->cache->get($key);
        if ($html == null) {
            $html = $this->curl($url);
            $this->cache->save($key, $html, 300);
        }
        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->loadHTML($html);
        $xpath = new DOMXPath($dom);
        $xml = $xpath->query('/html/body/div[@class="greyBg"]/div[@id="wrap"]/div[@class="w960"]/div[@class="cen-conbox clearfix"]/div[@class="innerContent fr"]');
        $xhtml = $dom->saveHTML($xml->item(0));
        $new_xhtml = str_replace('http://www.netkiller.cn/tw/calendar', '/calendar/netkillertw', $xhtml);
        return($new_xhtml);
    }

    //app端简体 财经日历模板
    private function app_generator($day = '') {
        if ($day == '') {
            $url = "http://m.netkiller.cn/zh/calender/index.html";
        } else {
            $url = "http://m.netkiller.cn/zh/calender/" . $day . ".html";
        }
        $key = sprintf(":calendar:app:%s", $day);
        $html = null;
        $html = $this->cache->get($key);
        if ($html == null) {
            $html = $this->curl($url);
            $this->cache->save($key, $html, 300);
        }
        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->loadHTML($html);
        $xpath = new DOMXPath($dom);
        /* $xml_1 = $xpath->query('/html/body/div[@class="date_1"]');
          $xml_2 = $xpath->query('/html/body/section[@class="Calendar"]');
          $xml_3 = $xpath->query('/html/body/section[@class="Thing"]');
          $xhtml_1 = $dom->saveHTML($xml_1->item(0));
          $xhtml_2 = $dom->saveHTML($xml_2->item(0));
          $xhtml_3 = $dom->saveHTML($xml_3->item(0));
          $xhtml_4 = $dom->saveHTML($xml_3->item(1));
          $xhtml = $xhtml_1.$xhtml_2.$xhtml_3.$xhtml_4; */
        $xml_1 = $xpath->query('/html/body/section[@id="wrapper"]/section[@class="cen-conbox page-topfont"]/section[@class="calenda-box"]');
        $xhtml_1 = $dom->saveHTML($xml_1->item(0));
        $xhtml_2 = $dom->saveHTML($xml_1->item(1));
        $xhtml_3 = $dom->saveHTML($xml_1->item(2));
        $xhtml = $xhtml_1 . $xhtml_2 . $xhtml_3;
        $new_xhtml = str_replace('http://m.netkiller.cn/zh/calender', '/calendar/app', $xhtml);
        $new_xhtml_a = preg_replace("(<a[^>]*class=\"text\">(.+?)<\/a>)", "$1", $new_xhtml);

        return($new_xhtml_a);
    }

    //创富app端简体 财经日历模板
    private function cf_generator($day = '') {
        if ($day == '') {
            $url = "http://m.netkiller.cn/finance/index.html";
        } else {
            $url = "http://m.netkiller.cn/finance/" . $day . ".html";
        }
        $html = $this->curl($url);
        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->loadHTML($html);
        $xpath = new DOMXPath($dom);
        $xml_1 = $xpath->query('/html/body/section[@id="wrapper"]/section[@class="cen-conbox page-topfont"]/section[@class="hangq-box box-sty"]'
                . '/ul[@class="fan-newsbox"]/li');

        $xhtml_1 = $dom->saveHTML($xml_1->item(0));
        $xhtml_2 = $dom->saveHTML($xml_1->item(2));
        $xhtml_3 = $dom->saveHTML($xml_1->item(3));
        $xhtml_4 = $dom->saveHTML($xml_1->item(4));
        $xhtml_5 = $dom->saveHTML($xml_1->item(5));
        $xhtml_6 = $dom->saveHTML($xml_1->item(6));
        $xhtml_7 = $dom->saveHTML($xml_1->item(7));
        $xhtml_8 = $dom->saveHTML($xml_1->item(8));
        $xhtml_9 = $dom->saveHTML($xml_1->item(9));
        $xhtml = $xhtml_1 . $xhtml_2 . $xhtml_3 . $xhtml_4 . $xhtml_5 . $xhtml_6 . $xhtml_7 . $xhtml_8 . $xhtml_9;
        $new_xhtml = str_replace('http://m.netkiller.cn/finance', '/calendar/cf', $xhtml);
        $new_xhtml_a = preg_replace("(<a[^>]*class=\"text\">(.+?)<\/a>)", "$1", $new_xhtml);
//var_dump($xhtml) ;die;
        return($new_xhtml_a);
    }

    //app端简体 财经日历模板
    private function webui_generator($day = '') {
        if ($day == '') {
            $url = "http://m.netkiller.cn/zh/calender/index.html";
        } else {
            $url = "http://m.netkiller.cn/zh/calender/" . $day . ".html";
        }
        $key = sprintf(":calendar:webui:%s", $day);
        $html = null;
        $html = $this->cache->get($key);
        if ($html == null) {
            $html = $this->curl($url);
            $this->cache->save($key, $html, 300);
        }
        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->loadHTML($html);
        $xpath = new DOMXPath($dom);
        /* $xml_1 = $xpath->query('/html/body/div[@class="date_1"]');
          $xml_2 = $xpath->query('/html/body/section[@class="Calendar"]');
          $xml_3 = $xpath->query('/html/body/section[@class="Thing"]');
          $xhtml_1 = $dom->saveHTML($xml_1->item(0));
          $xhtml_2 = $dom->saveHTML($xml_2->item(0));
          $xhtml_3 = $dom->saveHTML($xml_3->item(0));
          $xhtml_4 = $dom->saveHTML($xml_3->item(1));
          $xhtml = $xhtml_1.$xhtml_2.$xhtml_3.$xhtml_4; */
        $xml_1 = $xpath->query('/html/body/section[@id="wrapper"]/section[@class="cen-conbox page-topfont"]/section[@class="calenda-box"]');
        $xhtml_1 = $dom->saveHTML($xml_1->item(0));
        $xhtml_2 = $dom->saveHTML($xml_1->item(1));
        $xhtml_3 = $dom->saveHTML($xml_1->item(2));
        $xhtml = $xhtml_1 . $xhtml_2 . $xhtml_3;
        $new_xhtml = str_replace('http://m.netkiller.cn/zh/calender', '/calendar/webui', $xhtml);
        $new_xhtml_a = preg_replace("(<a[^>]*class=\"text\">(.+?)<\/a>)", "$1", $new_xhtml);
        return($new_xhtml_a);
    }

    private function curl($url, $fields = array(), $auth = false) {
        $url_arr = parse_url($url);
        $curl = curl_init($url);
        $headers = array(
            'Accept: text/plain, */*; q=0.01',
            'Accept-Encoding: gzip, deflate',
            'Accept-Language: zh-CN,zh;q=0.8,en;q=0.6,vi;q=0.4,zh-TW;q=0.2',
            'Connection: keep-alive',
            'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
        );
        $headers[] = 'Host: ' . $url_arr['host'];
        $headers[] = 'Origin: https://' . $url_arr['host'];
        $headers[] = 'X-Requested-With: XMLHttpRequest';
        curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_VERBOSE, 0);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_REFERER, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        if ($fields) {
            $fields_string = http_build_query($fields);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_BINARYTRANSFER, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $fields_string);
        }
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    public function jsonAction() {
        $params = $this->request->getPost();
        $date = isset($params['nowDate']) ? $params['nowDate'] : date('Y-m-d');
        $listArr['token'] = $this->token;
        $listArr['timeStamp'] = $this->timeStamp;
        $listArr['platTypeKey'] = $this->platTypeKey;
        $listArr['platAccount'] = $this->platAccount;
        $listArr['lang'] = $this->lang;
        $listArr['siteflg'] = 1; // 站点
        $listArr['datestr'] = $date; // 站点

        $result = $this->cal_curl($this->request_url . '/restweb/finance/index.json', $listArr, 'POST');
        $this->http_code = $result['status'];
        //从mongodb中取出数据
        $cal_obj = json_decode($result['content'], true);
        $cal_arr = $cal_obj['finance']['financeDataList'];
        $db = $this->mongodb->db;
        $collection = $db->calendar;
        $mongo_data = $collection->find();
        $new_arr = array();
        foreach($mongo_data as $obj){
            $id_arr[] = $obj['id'];
            $new_arr[$obj['id']]['expect'] = $obj['expect'];
            $new_arr[$obj['id']]['actual'] = $obj['actual'];
            $new_arr[$obj['id']]['expect_text'] = $obj['expect_text'];
            $new_arr[$obj['id']]['actual_text'] = $obj['actual_text'];
        }
        foreach($cal_arr as $key=>$vs){

           // print_r($mongo_data);die;
            if($vs['fdTitle'] == ""){
                unset($cal_arr[$key]);
            }else{
                if(in_array($vs['externalId'], $id_arr)){
                    $cal_arr[$key]['expect_mongo'] = $new_arr[$vs['externalId']]['expect'];
                    $cal_arr[$key]['actual_mongo'] = $new_arr[$vs['externalId']]['actual'];
                    $cal_arr[$key]['expect_text_mongo'] = $new_arr[$vs['externalId']]['expect_text'];
                    $cal_arr[$key]['actual_text_mongo'] = $new_arr[$vs['externalId']]['actual_text'];
                }else{
                    $cal_arr[$key]['expect_mongo'] = "";
                    $cal_arr[$key]['actual_mongo'] = "";
                    $cal_arr[$key]['expect_text_mongo'] = "";
                    $cal_arr[$key]['actual_text_mongo'] = "";
                }
            }

        }
        $cal_arr = array_values($cal_arr);
        $cal_obj['finance']['financeDataList'] = $cal_arr;

        echo json_encode($cal_obj);
        exit();
    }

    public function jsonCalendarAction() {
        $params = $this->request->getPost();
        $date = isset($params['nowDate']) ? $params['nowDate'] : date('Y-m-d');
        //$compare = time() - strtotime($date);
        $importance = isset($params['importance']) && $params['importance'] != '' ? $params['importance'] : '低,中,高';
        if(strpos($importance,',')!=false){
                $importance_arr = explode(',',$importance);

        }else{
                $importance_arr = array($importance);
        }
        $listArr['token'] = $this->token;
        $listArr['timeStamp'] = $this->timeStamp;
        $listArr['platTypeKey'] = $this->platTypeKey;
        $listArr['platAccount'] = $this->platAccount;
        $listArr['lang'] = $this->lang;
        $listArr['siteflg'] = 1; // 站点
        $listArr['datestr'] = $date; // 站点

        $result = $this->cal_curl($this->request_url . '/restweb/finance/index.json', $listArr, 'POST');
        $this->http_code = $result['status'];
        //从mongodb中取出数据
        $cal_obj = json_decode($result['content'], true);
        $cal_arr = $cal_obj['finance']['financeDataList'];
        $db = $this->mongodb->db;
        $collection = $db->calendar;
        $mongo_data = $collection->find();
        $new_arr = array();
        foreach($mongo_data as $obj){
            $id_arr[] = $obj['id'];
            $new_arr[$obj['id']]['expect'] = $obj['expect'];
            $new_arr[$obj['id']]['actual'] = $obj['actual'];
            $new_arr[$obj['id']]['expect_text'] = $obj['expect_text'];
            $new_arr[$obj['id']]['actual_text'] = $obj['actual_text'];
        }
        foreach($cal_arr as $key=>$vs){
            $compare = time() - strtotime($date." ".$vs['fdTime']);

           // print_r($mongo_data);die;

            if($vs['fdTitle'] == "" || (count($importance_arr) != 0 && !in_array($vs['importance'],$importance_arr)) || ($compare>0 && $vs['actual'] == "--")){
                unset($cal_arr[$key]);
                continue;
            }else{
                if(in_array($vs['externalId'], $id_arr)){
                    $cal_arr[$key]['expect_mongo'] = $new_arr[$vs['externalId']]['expect'];
                    $cal_arr[$key]['actual_mongo'] = $new_arr[$vs['externalId']]['actual'];
                    $cal_arr[$key]['expect_text_mongo'] = $new_arr[$vs['externalId']]['expect_text'];
                    $cal_arr[$key]['actual_text_mongo'] = $new_arr[$vs['externalId']]['actual_text'];
                }else{
                    $cal_arr[$key]['expect_mongo'] = "";
                    $cal_arr[$key]['actual_mongo'] = "";
                    $cal_arr[$key]['expect_text_mongo'] = "";
                    $cal_arr[$key]['actual_text_mongo'] = "";
                }
            }

            //$cal_arr_new[$vs['fdTime'].$vs['fdCountry']][] = $vs;
        }
//        $cal_arr = array_values($cal_arr);
//
        foreach($cal_arr as $k=>$v){
            $cal_arr_new[$v['fdTime'].$v['fdCountry']][] = $v;
        }


        $cal_obj['finance']['financeDataList'] = $cal_arr_new;

        echo json_encode($cal_obj);
        exit();
    }

    private function cal_curl($url, $data = NULL, $method = 'GET') {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, TRUE);
            if (!empty($data)) {
                if (is_array($data)) {
                    $data = http_build_query($data);
                }
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            }
        } else {
            curl_setopt($ch, CURLOPT_URL, $url . $data);
        }
        $info['content'] = curl_exec($ch);
        $info['status'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($info['content'] === false) {
            $info['content'] = 'curl_error: ' . curl_error($ch);
        }
        curl_close($ch);
        return $info;
    }

}
