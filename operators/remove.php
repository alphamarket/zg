<?php
namespace zinux\zg\operators;
/**
 * zg remove * handler
 */
class remove extends baseOperator
{    
    /**
     * zg remove module handler
     * @throws \zinux\kernel\exceptions\notFoundException in case of target module not found in zg manifest
     */
    public function module($args)
    {
        # this opt is valid under project directories
        if(!$this->CheckZG()) return;
        # this opt should absolutely has 1 arg
        $this->restrictArgCount($args, 1, 1);
        # indicating the phase
        $this ->cout("Removing module '", 0, self::defColor, 0)
                ->cout("{$args[0]}Module", 0, self::yellow, 0)
                ->cout("' ...");
        # normalizing args
        $args[0] = preg_replace("#(\w+)module$#i", "$1", $args[0])."Module";
        # get status object
        $s = $this->GetStatus();
        # if module does not exist
        if(!isset($s->modules->collection[strtolower($args[0])]))
            throw new \zinux\kernel\exceptions\notFoundException("Module '{$args[0]}' does not exist in zg manifest!<br />Try 'zg build' command!");
        # invoke a remover
        $c = new \zinux\zg\vendors\remover;
        # remove it from file system
        $c->removeFS($s->modules->collection[strtolower($args[0])]);
    }
    /**
     * zg remove controller handler
     * @throws \zinux\kernel\exceptions\notFoundException in case of target module or target controller does not exist
     */
    public function controller($args)
    {
        # this opt is valid under project directories
        if(!$this->CheckZG()) return;
        # this opt shoud at least has 1 arg and atmost has 2 arg
        $this->restrictArgCount($args, 2,1);
        # if no module supplied suppose it is the default module
        if(count($args)==1)
            $args[] = "default";
        # a fail safe for args
        $this->restrictArgCount($args, 2,2);
        # normalizing args
        $raw = preg_replace("#(\w+)controller$#i", "$1", $args[0]);
        $args[0] = $raw."Controller";
        $args[1] = preg_replace("#(\w+)module$#i", "$1", $args[1])."Module";
        # get status object
        $s = $this->GetStatus();
        # if no module exists
        if(!isset($s->modules->collection[strtolower($args[1])]))
            throw new \zinux\kernel\exceptions\notFoundException("Module '{$args[1]}' does not exist in zg manifest!<br />Try 'zg build' command!");
        # if no controller exists
        if(!isset($s->modules->collection[strtolower($args[1])]->controller[strtolower($args[0])]))
            throw new \zinux\kernel\exceptions\notFoundException("Controller '{$args[1]}/{$args[0]}' does not exist in zg manifest!<br />Try 'zg build' command!");
        # invoke a remover
        $c = new \zinux\zg\vendors\remover;
        # remove it from file system
        $c->removeFS($s->modules->collection[strtolower($args[1])]->controller[strtolower($args[0])], 0);
        # also remove it views from file system
        $c->removeFS(new \zinux\zg\vendors\item("views",\zinux\kernel\utilities\fileSystem::resolve_path($s->modules->collection[strtolower($args[1])]->path."/views/view/".$raw)));
    }
    /**
     * zg remove action handler
     * @throws \zinux\kernel\exceptions\invalideOperationException in case of client trying to remove index action
     * @throws \zinux\kernel\exceptions\notFoundException if one of either module, controller or action does not exist
     */
    public function action($args)
    {
        # this opt is valid under project directories
        if(!$this->CheckZG()) return;
        # this opt shoud at least has 1 arg and atmost has 3 arg
        $this->restrictArgCount($args, 3,1);
        # if no controller supplied suppose it is the index controller
        if(count($args)==1)
            $args[] = "index";
        # if no module supplied suppose it is the default module
        if(count($args)==2)
            $args[] = "default";
        # a fail safe for args
        $this->restrictArgCount($args, 3,1);
        # normalizing args
        $args[0] = preg_replace("#(\w+)action#i", "$1", $args[0])."Action";
        $args[1] = preg_replace("#(\w+)controller$#i", "$1", $args[1])."Controller";
        $args[2] = preg_replace("#(\w+)module$#i", "$1", $args[2])."Module";
        # if client tries to remove index action 
        if(preg_match("#indexaction#i", $args[0]))
            throw new \zinux\kernel\exceptions\invalideOperationException("By zinux architecture structure, you cannot remove 'IndexAction'!");
        # get status object
        $s = $this->GetStatus();
        # if module does not exist
        if(!isset($s->modules->collection[strtolower($args[2])]))
            throw new \zinux\kernel\exceptions\notFoundException("Module '{$args[2]}' does not exist in zg manifest!<br />Try 'zg build' command!");
        # if controller does not exist
        if(!isset($s->modules->collection[strtolower($args[2])]->controller[strtolower($args[1])]))
            throw new \zinux\kernel\exceptions\notFoundException("Controller '{$args[2]}/{$args[1]}' does not exist in zg manifest!<br />Try 'zg build' command!");
        # if action does not exist
        if(!isset($s->modules->collection[strtolower($args[2])]->controller[strtolower($args[1])]->action[strtolower($args[0])]))
            throw new \zinux\kernel\exceptions\notFoundException("Action '{$args[2]}/{$args[1]}/{$args[0]}' does not exist in zg manifest!<br />Try 'zg build' command!");
        # invoke a remover
        $c = new \zinux\zg\vendors\remover;
        # remove the action
        $c->removeAction($s->modules->collection[strtolower($args[2])]->controller[strtolower($args[1])]->action[strtolower($args[0])]);
        # fetch target action's raw name
        $args[0] = preg_replace("#(\w+)action#i", "$1", $args[0]);
        # also remove its related view
        $this->view($args);
    }
    /**
     * zg remove view handler
     * @throws \zinux\kernel\exceptions\notFoundException if one of either module, controller or view does not exist
     */
    public function view($args)
    {
        # this opt is valid under project directories
        if(!$this->CheckZG()) return;
        # this opt shoud at least has 1 arg and atmost has 3 arg
        $this->restrictArgCount($args, 3,1);
        # if no controller supplied suppose it is the index controller
        if(count($args)==1)
            $args[] = "index";
        # if no module supplied suppose it is the default module
        if(count($args)==2)
            $args[] = "default";
        # a fail safe for args
        $this->restrictArgCount($args, 3,1);
        # normalizing args
        $args[0] = preg_replace("#(\w+)view#i", "$1", $args[0])."View";
        $args[1] = preg_replace("#(\w+)controller$#i", "$1", $args[1])."Controller";
        $args[2] = preg_replace("#(\w+)module$#i", "$1", $args[2])."Module";
        # get status object
        $s = $this->GetStatus();
        # if no module exists
        if(!isset($s->modules->collection[strtolower($args[2])]))
            throw new \zinux\kernel\exceptions\notFoundException("Module '{$args[2]}' does not exist in zg manifest!<br />Try 'zg build' command!");
        # if no controller exists
        if(!isset($s->modules->collection[strtolower($args[2])]->controller[strtolower($args[1])]))
            throw new \zinux\kernel\exceptions\notFoundException("Controller '{$args[2]}/{$args[1]}' does not exist in zg manifest!<br />Try 'zg build' command!");
        # if no view exists
        if(!isset($s->modules->collection[strtolower($args[2])]->controller[strtolower($args[1])]->view[strtolower($args[0])]))
            throw new \zinux\kernel\exceptions\notFoundException("View '{$args[2]}/{$args[1]}/{$args[0]}' does not exist in zg manifest!<br />Try 'zg build' command!");
        # invoke a remover
        $c = new \zinux\zg\vendors\remover;
        # remove target view from file system
        $c->removeFS($s->modules->collection[strtolower($args[2])]->controller[strtolower($args[1])]->view[strtolower($args[0])]);
    }
    /**
     * zg remove layout handler
     * @throws \zinux\kernel\exceptions\notFoundException in case of module not found
     */
    public function layout($args)
    {
        # this opt is valid under project directories
        if(!$this->CheckZG()) return;
        # this opt shoud at least has 1 arg and atmost has 2 arg
        $this->restrictArgCount($args, 2,1);
        # if no module supplied suppose it's default module
        if(count($args)==1)
            $args[] = "default";
        # a fail safe for args
        $this->restrictArgCount($args, 2, 2);
        # normalizing args
        $args[0] = preg_replace("#(\w+)layout$#i", "$1", $args[0])."Layout";
        $args[1] = preg_replace("#(\w+)module$#i", "$1", $args[1])."Module";
        # get status object
        $s = $this->GetStatus();
        # if no module exists
        if(!isset($s->modules->collection[strtolower($args[1])]))
            throw new \zinux\kernel\exceptions\notFoundException("Module '{$args[1]}' does not exist in zg manifest!<br />Try 'zg build' command!");
        # if no layout exists
        if(!isset($s->modules->collection[strtolower($args[1])]->layout[strtolower($args[0])]))
            throw new \zinux\kernel\exceptions\notFoundException("Layout  '{$args[1]}/{$args[0]}' does not exist in zg manifest!<br />Try 'zg build' command!");
        # invoke a remover
        $c = new \zinux\zg\vendors\remover;
        # remove the target layout from file system
        $c->removeFS($s->modules->collection[strtolower($args[1])]->layout[strtolower($args[0])]);
    }
    /**
     * zg remove helper handler
     * @throws \zinux\kernel\exceptions\notFoundException in case of module not found
     */
    public function helper($args)
    {
        # this opt is valid under project directories
        if(!$this->CheckZG()) return;
        # this opt shoud at least has 1 arg and atmost has 2 arg
        $this->restrictArgCount($args, 2,1);
        # if no module supplied suppose it's default module
        if(count($args)==1)
            $args[] = "default";
        # a fail safe for args
        $this->restrictArgCount($args, 2, 2);
        # normalizing args
        $args[0] = preg_replace("#(\w+)helper$#i", "$1", $args[0])."Helper";
        $args[1] = preg_replace("#(\w+)module$#i", "$1", $args[1])."Module";
        # get status object
        $s = $this->GetStatus();
        # if no module found
        if(!isset($s->modules->collection[strtolower($args[1])]))
            throw new \zinux\kernel\exceptions\notFoundException("Module '{$args[1]}' does not exist in zg manifest!<br />Try 'zg build' command!");
        # if no helper found
        if(!isset($s->modules->collection[strtolower($args[1])]->helper[strtolower($args[0])]))
            throw new \zinux\kernel\exceptions\notFoundException("Helper '{$args[1]}/{$args[0]}' does not exist in zg manifest!<br />Try 'zg build' command!");
        # invoke a remover
        $c = new \zinux\zg\vendors\remover;
        # remove target helper from file system
        $c->removeFS($s->modules->collection[strtolower($args[1])]->helper[strtolower($args[0])]);
    }
    /**
     * zg remove model handler
     * @throws \zinux\kernel\exceptions\notFoundException in case of module not found
     */
    public function model($args)
    {
        # this opt is valid under project directories
        if(!$this->CheckZG()) return;
        # this opt shoud at least has 1 arg and atmost has 2 arg
        $this->restrictArgCount($args, 2,1);
        # if no module supplied suppose it's default module
        if(count($args)==1)
            $args[] = "default";
        # a fail safe for args
        $this->restrictArgCount($args, 2, 2);
        # normalizing args
        # we don't modify model's name in order to have free uses
        #$args[0] = preg_replace("#(\w+)model$#i", "$1", $args[0])."Model";
        $args[1] = preg_replace("#(\w+)module$#i", "$1", $args[1])."Module";
        # get status object
        $s = $this->GetStatus();
        # if no module found
        if(!isset($s->modules->collection[strtolower($args[1])]))
            throw new \zinux\kernel\exceptions\notFoundException("Module '{$args[1]}' does not exist in zg manifest!<br />Try 'zg build' command!");
        # if no model found
        if(!isset($s->modules->collection[strtolower($args[1])]->model[strtolower($args[0])]))
            throw new \zinux\kernel\exceptions\notFoundException("Model '{$args[1]}/{$args[0]}' does not exist in zg manifest!<br />Try 'zg build' command!");
        # invoke a remover
        $c = new \zinux\zg\vendors\remover;
        # remove target model from file system
        $c->removeFS($s->modules->collection[strtolower($args[1])]->model[strtolower($args[0])]);
    }
    /**
     * zg remove application boostrap handler
     * @throws \zinux\kernel\exceptions\notFoundException in case of bootstrap does not exist
     */
    public function app_bootstrap($args)
    {
        # this opt is valid under project directories
        if(!$this->CheckZG()) return;
        # this opt shoud has absolutely 1 arg
        $this->restrictArgCount($args,1,1);
        # normalizing args
        $args[0] = preg_replace("#(\w+)bootstrap$#i","$1", $args[0])."Bootstrap";
        # get status object
        $s = $this->GetStatus();
        # if no bootstrap found
        if(!isset($s->project->bootstrap[strtolower($args[0])]))
            throw new \zinux\kernel\exceptions\notFoundException("Application bootstrap '{$args[0]}' does not exist in zg manifest!<br />Try 'zg build' command!");
        # invoke a remover
        $c = new \zinux\zg\vendors\remover;
        # remove the target bootstrap from file system
        $c->removeFS($s->project->bootstrap[strtolower($args[0])]);
    }
    /**
     * zg remove application boostrap handler
     * @throws \zinux\kernel\exceptions\notFoundException in case of bootstrap does not exist
     */
    public function app_routes($args)
    {
        # this opt is valid under project directories
        if(!$this->CheckZG()) return;
        # this opt shoud has absolutely 1 arg
        $this->restrictArgCount($args,1,1);
        # normalizing args
        $args[0] = preg_replace("#(\w+)routes$#i","$1", $args[0])."Routes";
        # get status object
        $s = $this->GetStatus();
        # if no routes found
        if(!isset($s->project->routes[strtolower($args[0])]))
            throw new \zinux\kernel\exceptions\notFoundException("Application routes '{$args[0]}' does not exist in zg manifest!<br />Try 'zg build' command!");
        # invoke a remover
        $c = new \zinux\zg\vendors\remover;
        # remove the target routes from file system
        $c->removeFS($s->project->routes[strtolower($args[0])]);
    }
}
