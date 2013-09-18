<?php
namespace modules\defaultModule\controllers;


/**
 * Demo indexController
 * @author Dariush Hasanpoor <b.g.dariush@gmail.com>
 */
class IndexController extends ModuleController
{
    public function IndexAction()
    {
        $this->view->message = "A message from controller ...";
    }
}

