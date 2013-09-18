<?php
namespace modules\defaultModule\controllers;


/**
 * Demo ApplicationController this is an abstract class for module
 * @author Dariush Hasanpoor <b.g.dariush@gmail.com>
 */
abstract class  ModuleController extends \zinux\kernel\controller\baseController
{
    protected function SetMessage($message)
    {
        $_SESSION[__CLASS__]['message'] = $message;
        $this->view->__message = $message;
    }
    
    public function Initiate()
    {
        if(isset($_SESSION[__CLASS__]) && isset($_SESSION[__CLASS__]['message']))
        {
            $this->view->__message = $_SESSION[__CLASS__]['message'];
            unset($_SESSION[__CLASS__]['message']);
        }
    }
}