<?php
namespace zinux\zg\operators;
/**
 * zg new * handler
 */
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
        $pName = implode(" ", $args);
        $this->NormalizeName($pName);
        # validate project name with currently existed directories
        if(file_exists($pName))
            throw new \zinux\kernel\exceptions\invalideArgumentException("A folder named '$pName' already exists...");
        # create an status file
        # this also creates project direcroty as well
        $this->CreateStatusFile($pName);
        # get an initial status object
        $s = $this->GetStatus($pName);
        # create a meta object about project
        $s->modules->meta = new \zinux\zg\vendors\Item("modules", $s->project->path."/modules", $s->project);
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
                "cd $pName/zinux && rm -fr test zg",
                "echo '# add this to apache vhost.conf files
<VirtualHost *:80>
	ServerAdmin webmaster@localhost
	ServerName $pName.local
	DocumentRoot \"/var/www/$pName/public_html\"
</VirtualHost>

# add this to /etc/hosts
# 127.0.0.1 $pName.local
' > ./$pName/public_html/$pName.local",
                "chmod -R 775 $pName", 
                "chmod 777 $pName"
        );
        # run the above command
        $this->Run($opt);
        # modifying project's zinux manifest
        $zmanifest = json_decode(file_get_contents("$pName/zinux/manifest.json"));
        # remove the zg module from target project
        unset($zmanifest->modules->zg);
        # re-write the manifest 
        file_put_contents("$pName/zinux/manifest.json", json_encode($zmanifest));
        # if the client wants an empty project
        if($empty)
        {
            # no further opertaion needed
            $this->cout("An empty project created successfully...", 0.5);
            return;
        }
        # invoke an item creator
        $c = new \zinux\zg\vendors\creator;
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
    }
    /**
     * zg new module handler
     * @param array $args
     * @throws \zinux\kernel\exceptions\invalideOperationException in case of target module already exists
     */
    public function module($args)
    {
        # this opt is valid under project directories
        if(!$this->CheckZG()) return;
        # this opt should only have 1 passed arg 
        $this->restrictArgCount($args, 1, 1);
        # indicate the phase
        $this ->cout("Creating new module '", 0.5, self::defColor, 0)
                ->cout("{$args[0]}Module", 0, self::yellow, 0)
                ->cout("' ...");
        # normalize the arg
        $this->NormalizeName($args[0], "module");
        # if module does not exist
        if(isset($s->modules->collection[strtolower($args[0])]))
            throw new \zinux\kernel\exceptions\invalideOperationException("Module '{$args[0]}' does not exist in zg manifest!<br />Try 'zg build' command!");
        # invoke a creator 
        $c = new \zinux\zg\vendors\creator;
        # create a module based on passed arg
        $module = $c->createModule($args[0]);
        # create an index controller in created module
        $controller = $c->createController("index", $module);
        # create a default layout for created module
        $c->createLayout("default", $module);
        # create an index controller for created module
        # it also creates an index view for created controller
        $c->createView("index", $controller);
    }
    /**
     * zg new controller handler
     * @throws \zinux\kernel\exceptions\notFoundException in case of target module does not exist
     * @throws \zinux\kernel\exceptions\invalideOperationException in case of target controller already exists
     */
    public function controller($args)
    {
        # this opt is valid under project directories
        if(!$this->CheckZG()) return;
        # this module can have maximum 2 args and minimum 1 arg
        $this->restrictArgCount($args, 2,1);
        # if no module supplied suppose it is the default module
        if(count($args)==1)
            $args[] = "default";
        # a fail safe for args
        $this->restrictArgCount($args, 2,2);
        # normalize module and controller names
        $this->NormalizeName($args[0], "controller");
        $this->NormalizeName($args[1], "module");
        # get status object
        $s = $this->GetStatus();
        # if module does not exist
        if(!isset($s->modules->collection[strtolower($args[1])]))
            throw new \zinux\kernel\exceptions\notFoundException("Module '{$args[1]}' does not exist in zg manifest!<br />Try 'zg build' command!");
        # if controller already exists
        if(isset($s->modules->collection[strtolower($args[1])]->controller[strtolower($args[0])]))
            throw new \zinux\kernel\exceptions\invalideOperationException("Controller '{$args[1]}/{$args[0]}' already exists in zg manifest!<br />Try 'zg build' command!");
        # invoke a creator
        $c = new \zinux\zg\vendors\creator;
        # create a new controller 
        # it also manually creates an index action
        $controller = $c->createController($args[0], $s->modules->collection[strtolower($args[1])]);
        # create an index view for created controller
        $c->createView("index", $controller);
    }
    /**
     * zg new action handler
     * @throws \zinux\kernel\exceptions\notFoundException in case of target module or controller does not exist
     * @throws \zinux\kernel\exceptions\invalideOperationException in case of target action already exists
     */
    public function action($args)
    {
        # this opt is valid under project directories
        if(!$this->CheckZG()) return;
        # this module can have maximum 3 args and minimum 1 arg
        $this->restrictArgCount($args, 3,1);
        # if no controller supplied suppose it is the index controller
        if(count($args)==1)
            $args[] = "index";
        # if no module supplied suppose it is the  default module
        if(count($args)==2)
            $args[] = "default";
        # a fail safe for args
        $this->restrictArgCount($args, 3,1);
        # normalize the args
        $this->NormalizeName($args[0], "action");
        $this->NormalizeName($args[1], "controller");
        $this->NormalizeName($args[2], "module");
        # get status object
        $s = $this->GetStatus();
        # if module does not exist
        if(!isset($s->modules->collection[strtolower($args[2])]))
            throw new \zinux\kernel\exceptions\notFoundException("Module '{$args[2]}' does not exist in zg manifest!<br />Try 'zg build' command!");
        # if controller does not exist
        if(!isset($s->modules->collection[strtolower($args[2])]->controller[strtolower($args[1])]))
            throw new \zinux\kernel\exceptions\notFoundException("Controller '{$args[2]}/{$args[1]}' does not exist in zg manifest!<br />Try 'zg build' command!");
        # if action already exists
        if(isset($s->modules->collection[strtolower($args[2])]->controller[strtolower($args[1])]->action[strtolower($args[0])]))
            throw new \zinux\kernel\exceptions\invalideOperationException("Action '{$args[2]}/{$args[1]}/{$args[0]}' already exists in zg manifest!<br />Try 'zg build' command!");
        # invoke a creator
        $c = new \zinux\zg\vendors\creator;
        # create an action for passed module and controller
        $c->createAction($args[0], $s->modules->collection[strtolower($args[2])]->controller[strtolower($args[1])]);
        # normalize the action name for creating a view
        $args[0] = preg_replace("#(\w+)action#i", "$1", $args[0]);
        # invoke 'zg new view' for current args
        $this->view($args);
    }
    /**
     * zg new view handler
     * @throws \zinux\kernel\exceptions\notFoundException in case of target module or controller does not exist
     * @throws \zinux\kernel\exceptions\invalideOperationException in case of target view already exists
     */
    public function view($args)
    {
        # this opt is valid under project directories
        if(!$this->CheckZG()) return;
        # this module can have maximum 3 args and minimum 1 arg
        $this->restrictArgCount($args, 3,1);
        # if no controller supplied suppose it is the index controller
        if(count($args)==1)
            $args[] = "index";
        # if no module supplied suppose it is the  default module
        if(count($args)==2)
            $args[] = "default";
        # a fail safe for args
        $this->restrictArgCount($args, 3,1);
        # normalize the args
        $this->NormalizeName($args[0], "view");
        $this->NormalizeName($args[1], "controller");
        $this->NormalizeName($args[2], "module");
        # get status object
        $s = $this->GetStatus();
        # if module does not exist
        if(!isset($s->modules->collection[strtolower($args[2])]))
            throw new \zinux\kernel\exceptions\notFoundException("Module '{$args[2]}' does not exist in zg manifest!<br />Try 'zg build' command!");
        # if controller does not exist
        if(!isset($s->modules->collection[strtolower($args[2])]->controller[strtolower($args[1])]))
            throw new \zinux\kernel\exceptions\notFoundException("Controller '{$args[2]}/{$args[1]}' does not exist in zg manifest!<br />Try 'zg build' command!");
        # if view already exists
        if(isset($s->modules->collection[strtolower($args[2])]->controller[strtolower($args[1])]->view[strtolower($args[0])]))
            throw new \zinux\kernel\exceptions\invalideOperationException("View '{$args[2]}/{$args[1]}/{$args[0]}' already exists in zg manifest!<br />Try 'zg build' command!");
        # invoke a creator
        $c = new \zinux\zg\vendors\creator;
        # create a new view for passed args
        $c->createView($args[0], $s->modules->collection[strtolower($args[2])]->controller[strtolower($args[1])]);
    }
    /**
     * zg new layout handler
     * @throws \zinux\kernel\exceptions\notFoundException in case of target module does not exist
     * @throws \zinux\kernel\exceptions\invalideOperationException in case of target layout already exists
     */
    public function layout($args)
    {
        # this opt is valid under project directories
        if(!$this->CheckZG()) return;
        # this module can have maximum 2 args and minimum 1 arg
        $this->restrictArgCount($args, 2,1);
        # if no module supplied suppose it is the  default module
        if(count($args)==1)
            $args[] = "default";
        # a fail safe for args
        $this->restrictArgCount($args, 2,2);
        # normalize the args
        $this->NormalizeName($args[0], "layout");
        $this->NormalizeName($args[1], "module");
        # get status object
        $s = $this->GetStatus();
        # if module does not exist
        if(!isset($s->modules->collection[strtolower($args[1])]))
            throw new \zinux\kernel\exceptions\notFoundException("Module '{$args[1]}' does not exist in zg manifest!<br />Try 'zg build' command!");
        # if layout already existss
        if(isset($s->modules->collection[strtolower($args[1])]->layout[strtolower($args[0])]))
            throw new \zinux\kernel\exceptions\invalideOperationException("Layout  '{$args[1]}/{$args[0]}' already exists in zg manifest!<br />Try 'zg build' command!");
        # invoke a creator
        $c = new \zinux\zg\vendors\creator;
        # create a layout with passed args
        $c->createLayout($args[0], $s->modules->collection[strtolower($args[1])]);
    }
    /**
     * zg new helper handler
     * @throws \zinux\kernel\exceptions\notFoundException in case of target module does not exist
     * @throws \zinux\kernel\exceptions\invalideOperationException in case of target helper already exists
     */
    public function helper($args)
    {
        # this opt is valid under project directories
        if(!$this->CheckZG()) return;
        # this module can have maximum 2 args and minimum 1 arg
        $this->restrictArgCount($args, 2,1);
        # if no module supplied suppose it is the  default module
        if(count($args)==1)
            $args[] = "default";
        # a fail safe for args
        $this->restrictArgCount($args, 2,2);
        # normalize the args        
        $this->NormalizeName($args[0], "helper");
        $this->NormalizeName($args[1], "module");
        # get status object
        $s = $this->GetStatus();
        # if module does not exist
        if(!isset($s->modules->collection[strtolower($args[1])]))
            throw new \zinux\kernel\exceptions\notFoundException("Module '{$args[1]}' does not exist in zg manifest!<br />Try 'zg build' command!");
        # if layout already existss
        if(isset($s->modules->collection[strtolower($args[1])]->helper[strtolower($args[0])]))
            throw new \zinux\kernel\exceptions\invalideOperationException("Helper '{$args[1]}/{$args[0]}' already exists in zg manifest!<br />Try 'zg build' command!");
        # invoke a creator
        $c = new \zinux\zg\vendors\creator;
        # create a helper with passed args
        $c->createHelper($args[0], $s->modules->collection[strtolower($args[1])]);
    }
    /**
     * zg new model handler
     * @throws \zinux\kernel\exceptions\notFoundException in case of target module does not exist 
     * @throws \zinux\kernel\exceptions\invalideOperationException in case of target model already exists
     */
    public function model($args)
    {
        # this opt is valid under project directories
        if(!$this->CheckZG()) return;
        # this module can have maximum 2 args and minimum 1 arg
        $this->restrictArgCount($args, 2,1);
        # if no module supplied suppose it is the  default module
        if(count($args)==1)
            $args[] = "default";
        # a fail safe for args
        $this->restrictArgCount($args, 2,2);
        # normalize the args        
        # we don't modify model's name in order to have free uses
        #$args[0] = preg_replace("#(\w+)model$#i", "$1", $args[0])."Model";
        $this->NormalizeName($args[0]);
        $this->NormalizeName($args[1], "module");
        # get status object
        $s = $this->GetStatus();
        # if module does not exist
        if(!isset($s->modules->collection[strtolower($args[1])]))
            throw new \zinux\kernel\exceptions\notFoundException("Module '{$args[1]}' does not exist in zg manifest!<br />Try 'zg build' command!");
        # if layout already existss
        if(isset($s->modules->collection[strtolower($args[1])]->model[strtolower($args[0])]))
            throw new \zinux\kernel\exceptions\invalideOperationException("Model '{$args[1]}/{$args[0]}' already exists in zg manifest!<br />Try 'zg build' command!");
        # invoke a creator
        $c = new \zinux\zg\vendors\creator;
        # create a model with passed args
        $c->createModel($args[0], $s->modules->collection[strtolower($args[1])]);
    }
    /**
     * zg new application bootstrap handler
     * @throws \zinux\kernel\exceptions\invalideOperationException in case of target bootstrap already exists
     */
    public function app_bootstrap($args)
    {
        # this opt is valid under project directories
        if(!$this->CheckZG()) return;
        # this module can only have 1 arg
        $this->restrictArgCount($args,1,1);
        # get status object
        $s = $this->GetStatus();
        # normalize the args
        $this->NormalizeName($args[0], "bootstrap");
        # if bootstrap already exists
        if(isset($s->project->bootstrap[strtolower($args[0])]))
            throw new \zinux\kernel\exceptions\invalideOperationException("Application bootstrap '{$args[0]}' already exists in zg manifest!<br />Try 'zg build' command!");
        # invoke a creator
        $c = new \zinux\zg\vendors\creator();
        # create an application bootstrap
        $c->createAppBootstrap($args[0]);
    }
    /**
     * zg new application routes  handler
     * @throws \zinux\kernel\exceptions\invalideOperationException in case of target route already exists
     */
    public function app_routes($args)
    {
        # this opt is valid under project directories
        if(!$this->CheckZG()) return;
        # this module can only have 1 arg
        $this->restrictArgCount($args,1,1);
        # get status object
        $s = $this->GetStatus();
        # normalize the args
        $this->NormalizeName($args[0], "routes");
        # if routes already exists
        if(isset($s->project->routes[strtolower($args[0])]))
            throw new \zinux\kernel\exceptions\invalideOperationException("Application routes '{$args[0]}' already exists in zg manifest!<br />Try 'zg build' command!");
        # invoke a creator
        $c = new \zinux\zg\vendors\creator();
        # create an application routes
        $c->createAppRoutes($args[0]);
    }
}