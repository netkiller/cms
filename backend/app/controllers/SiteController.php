
<?php

class SiteController extends ControllerBase
{
    public function indexAction(){
        $site = Division::find($this->Division_id);

        $this->view->setVar('site',$site);
    }
}
