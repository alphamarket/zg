<?php
namespace zinux\zg\resources\operator;

class _new extends baseOperator
{
    public function project($args)
    {
        $this->restrictArgCount($args);
        
        $pName = implode("-", $args);
        if(file_exists($pName))
            throw new \zinux\kernel\exceptions\invalideArgumentException("A folder named '$pName' already exists...");
        $this->CreateStatusFile($pName);
        $s = $this->GetStatus($pName);
        $s->modules->meta = new \zinux\zg\vendor\Item("modules", $s->project->path."/modules", $s->project);
        $this->SaveStatus($s);
        
        $this ->cout("Creating new project '", 1, self::defColor, 0)
                ->cout("$pName", 0, self::yellow, 0)
                ->cout("' ...");
        $vpname = str_replace(" ", "-", $pName);
        $opt = array(
                "cp ".ZG_TEMPLE_ROOT."/* $pName/ -R",
                "cp -rf ".Z_CACHE_ROOT." $pName",
                "mv ./$pName/".basename(Z_CACHE_ROOT)." ./$pName/zinux",
                "echo '# add this to apache vhost.conf files
<VirtualHost *:80>
	ServerAdmin webmaster@localhost
	ServerName $vpname.local
	DocumentRoot \"/var/www/$pName/public_html\"
</VirtualHost>

# add this to /etc/hosts
# 127.0.0.1 $vpname.local
' > ./$pName/public_html/$vpname.local",
                "chmod -R 775 $pName", 
                "chmod 777 $pName"
        );
        /**
         * instead of copying templates directly we can do following processes
         * + Create appliaction/boostrap
         * + Creat application/routes
         * + Crate public_html
         * + Create defaultModule
         *      + COPYING defaultBootstrap.php correspondingly
         * + COPYING ModuleController.php directly
         * + Creating IndexController
         * + Creating IndexAction in IndexController 
         *      + Creating IndexView.phtml correspondingly
         * + COPYING defaultLayout.phtml directly
         */
        $this->Run($opt, 0);
        $c = new \zinux\zg\vendor\creator;
        $module = $c->createModule("default", $pName);
        $controller = $c->createController("index", $module, $pName);
        $appBootstrap =  $c->createAppBootstrap("app", $pName);
        $appRoutes =  $c->createAppRoutes("app", $pName);
        $layout = $c->createLayout("default", $module, $pName);
        $view = $c->createView("index", $controller, $pName);
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
        if(!isset($s->modules->modules[$args[1]]))
            throw new \zinux\kernel\exceptions\notFoundException("Module '{$args[1]}' does not exists in zg manifest!<br />    Try 'zg reload' command!");
        if(isset($s->modules->modules[$args[1]]->controller[$args[0]]))
            throw new \zinux\kernel\exceptions\notFoundException("Controller '{$args[1]}/{$args[0]}' already exists in zg manifest!<br />    Try 'zg reload' command!");
        $c = new \zinux\zg\vendor\creator;
        $controller = $c->createController($args[0], $s->modules->modules[$args[1]]);
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
        if(!isset($s->modules->modules[$args[2]]))
            throw new \zinux\kernel\exceptions\notFoundException("Module '{$args[2]}' does not exists in zg manifest!<br />    Try 'zg reload' command!");
        if(!isset($s->modules->modules[$args[2]]->controller[$args[1]]))
            throw new \zinux\kernel\exceptions\notFoundException("Controller '{$args[2]}/{$args[1]}' does not exists in zg manifest!<br />    Try 'zg reload' command!");
        if(isset($s->modules->modules[$args[2]]->controller[$args[1]]->action[$args[0]]))
            throw new \zinux\kernel\exceptions\notFoundException("Action '{$args[2]}/{$args[1]}/{$args[0]}' already exists in zg manifest!<br />    Try 'zg reload' command!");
        $c = new \zinux\zg\vendor\creator;
        $c->createAction($args[0], $s->modules->modules[$args[2]]->controller[$args[1]]);
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
        if(!isset($s->modules->modules[$args[2]]))
            throw new \zinux\kernel\exceptions\notFoundException("Module '{$args[2]}' does not exists in zg manifest!<br />    Try 'zg reload' command!");
        if(!isset($s->modules->modules[$args[2]]->controller[$args[1]]))
            throw new \zinux\kernel\exceptions\notFoundException("Controller '{$args[2]}/{$args[1]}' does not exists in zg manifest!<br />    Try 'zg reload' command!");
        if(isset($s->modules->modules[$args[2]]->controller[$args[1]]->view[$args[0]]))
            throw new \zinux\kernel\exceptions\notFoundException("View '{$args[2]}/{$args[1]}/{$args[0]}' already exists in zg manifest!<br />    Try 'zg reload' command!");
            
        $c = new \zinux\zg\vendor\creator;
        $c->createView($args[0], $s->modules->modules[$args[2]]->controller[$args[1]]);
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
        if(!isset($s->modules->modules[$args[1]]))
            throw new \zinux\kernel\exceptions\notFoundException("Module '{$args[1]}' does not exists in zg manifest!<br />    Try 'zg reload' command!");
        if(isset($s->modules->modules[$args[1]]->layout[$args[0]]))
            throw new \zinux\kernel\exceptions\notFoundException("Layout  '{$args[1]}/{$args[0]}' already exists in zg manifest!<br />    Try 'zg reload' command!");
            
        $c = new \zinux\zg\vendor\creator;
        $c->createLayout($args[0], $s->modules->modules[$args[1]]);
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
        if(!isset($s->modules->modules[$args[1]]))
            throw new \zinux\kernel\exceptions\notFoundException("Module '{$args[1]}' does not exists in zg manifest!<br />    Try 'zg reload' command!");
        if(isset($s->modules->modules[$args[1]]->helper[$args[0]]))
            throw new \zinux\kernel\exceptions\notFoundException("Helper '{$args[1]}/{$args[0]}' already exists in zg manifest!<br />    Try 'zg reload' command!");
            
        $c = new \zinux\zg\vendor\creator;
        $c->createHelper($args[0], $s->modules->modules[$args[1]]);
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
        if(!isset($s->modules->modules[$args[1]]))
            throw new \zinux\kernel\exceptions\notFoundException("Module '{$args[1]}' does not exists in zg manifest!<br />    Try 'zg reload' command!");
        if(isset($s->modules->modules[$args[1]]->model[$args[0]]))
            throw new \zinux\kernel\exceptions\notFoundException("Model '{$args[1]}/{$args[0]}' already exists in zg manifest!<br />    Try 'zg reload' command!");
            
        $c = new \zinux\zg\vendor\creator;
        $c->createModel($args[0], $s->modules->modules[$args[1]]);
    }
}
