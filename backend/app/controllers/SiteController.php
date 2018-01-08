
<?php

class SiteController extends \Phalcon\Mvc\Controller
{
    // public function initialize() {
    	//$division = new Division();
        //$this->divisionId = Division::getID();
        // parent::initialize();
    // }
    public function indexAction(){
        $site = Site::find();

        $this->view->setVar('site',$site);
    }
}
