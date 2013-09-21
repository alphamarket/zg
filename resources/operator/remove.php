<?php
namespace zinux\zg\resources\operator;

class _new extends baseOperator
{
    public function project($args)
    {
        if(!$this->CheckZG()) return;
        $this->restrictArgCount($args);
        
        $pName = implode("-", $args);
        if(!\zinux\kernel\utilities\fileSystem::resolve_path($pName))
            throw new \zinux\kernel\exceptions\invalideArgumentException("A folder named '$pName' already exists...");
    }
    
    public function module($args)
    {
        if(!$this->CheckZG())
            return;
        
        $this->restrictArgCount($args, 1);
        
        $this ->cout("Creating new module '", 0, self::defColor, 0)
                ->cout("{$args[0]}Module", 0, self::yellow, 0)
                ->cout("' ...");
        $args[0] = preg_replace("#(\w+)module$#i", "$1", $args[0])."Module";
        $c = new \zinux\zg\vendor\creator;
        $module = $c->createModule($args[0]);
        $controller = $c->createController("index", $module);
        $layout = $c->createLayout("default", $module);
        $view = $c->createView("index", $controller);
    }
    
    public function controller($args)
    {
        if(!$this->CheckZG()) return;
        $this->restrictArgCount($args, 2,1);
        if(count($args)==1)
            $args[] = "default";
        # fail safe
        $this->restrictArgCount($args, 2,2);
        $args[0] = preg_replace("#(\w+)controller$#i", "$1", $args[0])."Controller";
        $args[1] = preg_replace("#(\w+)module$#i", "$1", $args[1])."Module";
        $s = $this->GetStatus();
        if(!isset($s->modules->collection[strtolower($args[1])]))
            throw new \zinux\kernel\exceptions\notFoundException("Module '{$args[1]}' does not exists in zg manifest!<br />Try 'zg build' command!");
        if(isset($s->modules->collection[strtolower($args[1])]->controller[strtolower($args[0])]))
            throw new \zinux\kernel\exceptions\notFoundException("Controller '{$args[1]}/{$args[0]}' already exists in zg manifest!<br />Try 'zg build' command!");
        $c = new \zinux\zg\vendor\creator;
        $controller = $c->createController($args[0], $s->modules->collection[strtolower($args[1])]);
        $c->createView("index", $controller);
    }
    
    public function action($args)
    {
        if(!$this->CheckZG()) return;
        $this->restrictArgCount($args, 3,1);
        if(count($args)==1)
            $args[] = "index";
        if(count($args)==2)
            $args[] = "default";
        
        $args[0] = preg_replace("#(\w+)action#i", "$1", $args[0])."Action";
        $args[1] = preg_replace("#(\w+)controller$#i", "$1", $args[1])."Controller";
        $args[2] = preg_replace("#(\w+)module$#i", "$1", $args[2])."Module";
        $s = $this->GetStatus();
        if(!isset($s->modules->collection[strtolower($args[2])]))
            throw new \zinux\kernel\exceptions\notFoundException("Module '{$args[2]}' does not exists in zg manifest!<br />Try 'zg build' command!");
        if(!isset($s->modules->collection[strtolower($args[2])]->controller[strtolower($args[1])]))
            throw new \zinux\kernel\exceptions\notFoundException("Controller '{$args[2]}/{$args[1]}' does not exists in zg manifest!<br />Try 'zg build' command!");
        if(isset($s->modules->collection[strtolower($args[2])]->controller[strtolower($args[1])]->action[strtolower($args[0])]))
            throw new \zinux\kernel\exceptions\notFoundException("Action '{$args[2]}/{$args[1]}/{$args[0]}' already exists in zg manifest!<br />Try 'zg build' command!");
        $c = new \zinux\zg\vendor\creator;
        $c->createAction($args[0], $s->modules->collection[strtolower($args[2])]->controller[strtolower($args[1])]);
        $args[0] = preg_replace("#(\w+)action#i", "$1", $args[0]);
        $this->view($args);
    }
    public function view($args)
    {
        if(!$this->CheckZG()) return;
        $this->restrictArgCount($args, 3,1);
        if(count($args)==1)
            $args[] = "index";
        if(count($args)==2)
            $args[] = "default";
        
        $args[0] = preg_replace("#(\w+)view#i", "$1", $args[0])."View";
        $args[1] = preg_replace("#(\w+)controller$#i", "$1", $args[1])."Controller";
        $args[2] = preg_replace("#(\w+)module$#i", "$1", $args[2])."Module";
        $s = $this->GetStatus();
        if(!isset($s->modules->collection[strtolower($args[2])]))
            throw new \zinux\kernel\exceptions\notFoundException("Module '{$args[2]}' does not exists in zg manifest!<br />Try 'zg build' command!");
        if(!isset($s->modules->collection[strtolower($args[2])]->controller[strtolower($args[1])]))
            throw new \zinux\kernel\exceptions\notFoundException("Controller '{$args[2]}/{$args[1]}' does not exists in zg manifest!<br />Try 'zg build' command!");
        if(isset($s->modules->collection[strtolower($args[2])]->controller[strtolower($args[1])]->view[strtolower($args[0])]))
            throw new \zinux\kernel\exceptions\notFoundException("View '{$args[2]}/{$args[1]}/{$args[0]}' already exists in zg manifest!<br />Try 'zg build' command!");
            
        $c = new \zinux\zg\vendor\creator;
        $c->createView($args[0], $s->modules->collection[strtolower($args[2])]->controller[strtolower($args[1])]);
    }
    
    public function layout($args)
    {
        if(!$this->CheckZG()) return;
        $this->restrictArgCount($args, 2,1);
        if(count($args)==1)
            $args[] = "default";
        # fail safe
        $this->restrictArgCount($args, 2,2);
        $args[0] = preg_replace("#(\w+)layout$#i", "$1", $args[0])."Layout";
        $args[1] = preg_replace("#(\w+)module$#i", "$1", $args[1])."Module";
        $s = $this->GetStatus();
        if(!isset($s->modules->collection[strtolower($args[1])]))
            throw new \zinux\kernel\exceptions\notFoundException("Module '{$args[1]}' does not exists in zg manifest!<br />Try 'zg build' command!");
        if(isset($s->modules->collection[strtolower($args[1])]->layout[strtolower($args[0])]))
            throw new \zinux\kernel\exceptions\notFoundException("Layout  '{$args[1]}/{$args[0]}' already exists in zg manifest!<br />Try 'zg build' command!");
            
        $c = new \zinux\zg\vendor\creator;
        $c->createLayout($args[0], $s->modules->collection[strtolower($args[1])]);
    }
    public function helper($args)
    {
        if(!$this->CheckZG()) return;
        $this->restrictArgCount($args, 2,1);
        if(count($args)==1)
            $args[] = "default";
        # fail safe
        $this->restrictArgCount($args, 2,2);
        $args[0] = preg_replace("#(\w+)helper$#i", "$1", $args[0])."Helper";
        $args[1] = preg_replace("#(\w+)module$#i", "$1", $args[1])."Module";
        $s = $this->GetStatus();
        if(!isset($s->modules->collection[strtolower($args[1])]))
            throw new \zinux\kernel\exceptions\notFoundException("Module '{$args[1]}' does not exists in zg manifest!<br />Try 'zg build' command!");
        if(isset($s->modules->collection[strtolower($args[1])]->helper[strtolower($args[0])]))
            throw new \zinux\kernel\exceptions\notFoundException("Helper '{$args[1]}/{$args[0]}' already exists in zg manifest!<br />Try 'zg build' command!");
            
        $c = new \zinux\zg\vendor\creator;
        $c->createHelper($args[0], $s->modules->collection[strtolower($args[1])]);
    }
    public function model($args)
    {
        if(!$this->CheckZG()) return;
        $this->restrictArgCount($args, 2,1);
        if(count($args)==1)
            $args[] = "default";
        # fail safe
        $this->restrictArgCount($args, 2,2);
        # we don't modify model's name in order to have free uses
        #$args[0] = preg_replace("#(\w+)model$#i", "$1", $args[0])."Model";
        $args[1] = preg_replace("#(\w+)module$#i", "$1", $args[1])."Module";
        $s = $this->GetStatus();
        if(!isset($s->modules->collection[strtolower($args[1])]))
            throw new \zinux\kernel\exceptions\notFoundException("Module '{$args[1]}' does not exists in zg manifest!<br />Try 'zg build' command!");
        if(isset($s->modules->collection[strtolower($args[1])]->model[strtolower($args[0])]))
            throw new \zinux\kernel\exceptions\notFoundException("Model '{$args[1]}/{$args[0]}' already exists in zg manifest!<br />Try 'zg build' command!");
            
        $c = new \zinux\zg\vendor\creator;
        $c->createModel($args[0], $s->modules->collection[strtolower($args[1])]);
    }
}
