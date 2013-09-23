<?php
namespace zinux\zg\operators;

class _new extends baseOperator
{
    /**
     * zg new project handler 
     * @param array $args passed argument
     * @throws \zinux\kernel\exceptions\invalideArgumentException if a folder with passed name found in current directory!
     */
    public function project($args)
    {
        # this opt shoud has args -gt 0 
        $this->restrictArgCount($args);
        # check if client wants an empty project
        $empty = $this->remove_arg($args, "--empty");
        # create a proper project file name
        $pName = implode("-", $args);
        # validate project name with currently existed directories
        if(file_exists($pName))
            throw new \zinux\kernel\exceptions\invalideArgumentException("A folder named '$pName' already exists...");
        # create an status file
        # this also creates project direcroty as well
        $this->CreateStatusFile($pName);
        # get an initial status object
        $s = $this->GetStatus($pName);
        # create a meta object about project
        $s->modules->meta = new \zinux\zg\vendor\Item("modules", $s->project->path."/modules", $s->project);
        # save the status file
        $this->SaveStatus($s);
        /**
         * Officially at this stage the project has created
         * from this point we only create project items
         */
        # indicate that project has been created
        $this ->cout("Creating new project '", 0.5, self::defColor, 0)
                ->cout("$pName", 0, self::yellow, 0)
                ->cout("' ...");
        /**
         * Operations in this array:
         *      # creates public_html
         *      # copies zinux project
         *      # creates a virtual-host sample suite for project
         *      # creates proper permission for project directory and its sub-directories
         */
        $opt = array(
                "cp ".ZG_TEMPLE_ROOT."/* $pName/ -R",
                "cp -rf ".Z_CACHE_ROOT." $pName",
                "echo '# add this to apache vhost.conf files
<VirtualHost *:80>
	ServerAdmin webmaster@localhost
	ServerName $pName.local
	DocumentRoot \"/var/www/$pName/public_html\"
</VirtualHost>

# add this to /etc/hosts
# 127.0.0.1 $vpname.local
' > ./$pName/public_html/$vpname.local",
                "chmod -R 775 $pName", 
                "chmod 777 $pName"
        );
        # run the above command
        $this->Run($opt);
        # if the client wants an empty project
        if($empty)
        {
            # no further opertaion needed
            $this->cout("An empty project created successfully...", 0.5);
            return;
        }
        # invoke an item creator
        $c = new \zinux\zg\vendor\creator;
        # create a module name default
        $module = $c->createModule("default", $pName);
        # create a controller name index
        # this also manually create an index action
        # it won't create index view too!
        $controller = $c->createController("index", $module, $pName);
        # create an application bootstrap named app
        $c->createAppBootstrap("app", $pName);
        # create an application routes named app
        $c->createAppRoutes("app", $pName);
        # craete a default layout for index controller
        $c->createLayout("default", $module, $pName);
        # manually create an index view for index controller
        $c->createView("index", $controller, $pName);
        # invoke a config builder
        $b = new build(1, 0);
        # build new configuration file based on created items
        $b->build(array('-p', $s->project->path, "-m", $s->modules->meta->name));
        # remove the un-wanted cache direcroty in CWD
        $this->Run(array("rm -fr ./".PRG_CONF_DIRNAME));
    }
    
    public function module($args)
    {
        if(!$this->CheckZG())
            return;
        
        $this->restrictArgCount($args, 1);
        
        $this ->cout("Creating new module '", 0.5, self::defColor, 0)
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
    public function app_bootstrap($args)
    {
        if(!$this->CheckZG()) return;
        $this->restrictArgCount($args,1,1);
        $s = $this->GetStatus();
        $args[0] = preg_replace("#(\w+)bootstrap$#i","$1", $args[0])."Bootstrap";
        if(isset($s->project->bootstrap[strtolower($args[0])]))
            throw new \zinux\kernel\exceptions\notFoundException("Application bootstrap '{$args[0]}' already exists in zg manifest!<br />Try 'zg build' command!");
        $c = new \zinux\zg\vendor\creator();
        $c->createAppBootstrap($args[0]);
    }
    public function app_routes($args)
    {
        if(!$this->CheckZG()) return;
        $this->restrictArgCount($args,1,1);
        $s = $this->GetStatus();
        $args[0] = preg_replace("#(\w+)routes$#i","$1", $args[0])."Routes";
        if(isset($s->project->routes[strtolower($args[0])]))
            throw new \zinux\kernel\exceptions\notFoundException("Application routes '{$args[0]}' already exists in zg manifest!<br />Try 'zg build' command!");
        $c = new \zinux\zg\vendor\creator();
        $c->createAppRoutes($args[0]);
    }
}