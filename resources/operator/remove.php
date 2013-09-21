<?php
namespace zinux\zg\resources\operator;

class remove extends baseOperator
{    
    public function module($args)
    {
        if(!$this->CheckZG())
            return;
        
        $this->restrictArgCount($args, 1);
        
        $this ->cout("Removing module '", 0, self::defColor, 0)
                ->cout("{$args[0]}Module", 0, self::yellow, 0)
                ->cout("' ...");
        $args[0] = preg_replace("#(\w+)module$#i", "$1", $args[0])."Module";
        $s = $this->GetStatus();
        if(!isset($s->modules->collection[strtolower($args[0])]))
            throw new \zinux\kernel\exceptions\notFoundException("Module '{$args[0]}' does not exist in zg manifest!<br />Try 'zg build' command!");
        $c = new \zinux\zg\vendor\remover;
        $c->removeFS($s->modules->collection[strtolower($args[0])]);
    }
    
    public function controller($args)
    {
        if(!$this->CheckZG()) return;
        $this->restrictArgCount($args, 2,1);
        if(count($args)==1)
            $args[] = "default";
        # fail safe
        $this->restrictArgCount($args, 2,2);
        $raw = preg_replace("#(\w+)controller$#i", "$1", $args[0]);
        $args[0] = $raw."Controller";
        $args[1] = preg_replace("#(\w+)module$#i", "$1", $args[1])."Module";
        $s = $this->GetStatus();
        if(!isset($s->modules->collection[strtolower($args[1])]))
            throw new \zinux\kernel\exceptions\notFoundException("Module '{$args[1]}' does not exist in zg manifest!<br />Try 'zg build' command!");
        if(!isset($s->modules->collection[strtolower($args[1])]->controller[strtolower($args[0])]))
            throw new \zinux\kernel\exceptions\notFoundException("Controller '{$args[1]}/{$args[0]}' does not exist in zg manifest!<br />Try 'zg build' command!");
        $controller = $s->modules->collection[strtolower($args[1])]->controller[strtolower($args[0])];
        $c = new \zinux\zg\vendor\remover;
        $c->removeFS($controller, 0);
        $c->removeFS(new \zinux\zg\vendor\item("views",\zinux\kernel\utilities\fileSystem::resolve_path($s->modules->collection[strtolower($args[1])]->path."/views/view/".$raw)));
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
        if(preg_match("#indexaction#i", $args[0]))
            throw new \zinux\kernel\exceptions\invalideOperationException("By zinux architecture structure, you cannot remove 'IndexAction'!");
        $s = $this->GetStatus();
        if(!isset($s->modules->collection[strtolower($args[2])]))
            throw new \zinux\kernel\exceptions\notFoundException("Module '{$args[2]}' does not exist in zg manifest!<br />Try 'zg build' command!");
        if(!isset($s->modules->collection[strtolower($args[2])]->controller[strtolower($args[1])]))
            throw new \zinux\kernel\exceptions\notFoundException("Controller '{$args[2]}/{$args[1]}' does not exist in zg manifest!<br />Try 'zg build' command!");
        if(!isset($s->modules->collection[strtolower($args[2])]->controller[strtolower($args[1])]->action[strtolower($args[0])]))
            throw new \zinux\kernel\exceptions\notFoundException("Action '{$args[2]}/{$args[1]}/{$args[0]}' does not exist in zg manifest!<br />Try 'zg build' command!");
        $c = new \zinux\zg\vendor\remover;
        $c->removeAction($s->modules->collection[strtolower($args[2])]->controller[strtolower($args[1])]->action[strtolower($args[0])]);
        return;
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
            throw new \zinux\kernel\exceptions\notFoundException("Module '{$args[2]}' does not exist in zg manifest!<br />Try 'zg build' command!");
        if(!isset($s->modules->collection[strtolower($args[2])]->controller[strtolower($args[1])]))
            throw new \zinux\kernel\exceptions\notFoundException("Controller '{$args[2]}/{$args[1]}' does not exist in zg manifest!<br />Try 'zg build' command!");
        if(!isset($s->modules->collection[strtolower($args[2])]->controller[strtolower($args[1])]->view[strtolower($args[0])]))
            throw new \zinux\kernel\exceptions\notFoundException("View '{$args[2]}/{$args[1]}/{$args[0]}' does not exist in zg manifest!<br />Try 'zg build' command!");
            
        $c = new \zinux\zg\vendor\remover;
        $c->removeFS($s->modules->collection[strtolower($args[2])]->controller[strtolower($args[1])]->view[strtolower($args[0])]);
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
            throw new \zinux\kernel\exceptions\notFoundException("Module '{$args[1]}' does not exist in zg manifest!<br />Try 'zg build' command!");
        if(!isset($s->modules->collection[strtolower($args[1])]->layout[strtolower($args[0])]))
            throw new \zinux\kernel\exceptions\notFoundException("Layout  '{$args[1]}/{$args[0]}' does not exist in zg manifest!<br />Try 'zg build' command!");
            
        $c = new \zinux\zg\vendor\remover;
        $c->removeFS($s->modules->collection[strtolower($args[1])]->layout[strtolower($args[0])]);
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
            throw new \zinux\kernel\exceptions\notFoundException("Module '{$args[1]}' does not exist in zg manifest!<br />Try 'zg build' command!");
        if(!isset($s->modules->collection[strtolower($args[1])]->helper[strtolower($args[0])]))
            throw new \zinux\kernel\exceptions\notFoundException("Helper '{$args[1]}/{$args[0]}' does not exist in zg manifest!<br />Try 'zg build' command!");
        $c = new \zinux\zg\vendor\remover;
        $c->removeFS($s->modules->collection[strtolower($args[1])]->helper[strtolower($args[0])]);
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
            throw new \zinux\kernel\exceptions\notFoundException("Module '{$args[1]}' does not exist in zg manifest!<br />Try 'zg build' command!");
        if(!isset($s->modules->collection[strtolower($args[1])]->model[strtolower($args[0])]))
            throw new \zinux\kernel\exceptions\notFoundException("Model '{$args[1]}/{$args[0]}' does not exist in zg manifest!<br />Try 'zg build' command!");
            
        $c = new \zinux\zg\vendor\remover;
        $c->removeFS($s->modules->collection[strtolower($args[1])]->model[strtolower($args[0])]);
    }
}
