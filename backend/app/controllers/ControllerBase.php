<?php

class ControllerBase extends \Phalcon\Mvc\Controller
{
    public $Division_id;
    public $url;
    public $basedir = '/www/netkiller.cn/inf.netkiller.cn';
    public function initialize(){
        if (!$this->session->has("auth")) {
            return $this->response->redirect('/login/index');
        }else{
            $name = $this->session->get("auth");
            $id = $name['id'];
            $this->Division_id = $id;
            $this->url = $name['url'];
        }
    }
    
}