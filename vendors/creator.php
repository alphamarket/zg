<?php
namespace zinux\zg\vendors;
/**
 * Zinux item creation handler
 */
class creator extends \zinux\zg\operators\baseOperator
{
    /**
     * ctor a new creator
     */
    public function __construct()
    {
        # suppress header text
        parent::__construct(1);
    }
    /**
     * Handlers module creatation
     * @param string $name the module's name
     * @param string $projectDir project directory to create
     * @return \zinux\zg\vendors\item the created module
     * @throws \zinux\kernel\exceptions\invalideOperationException in case of module's folder already exists
     */
    public function createModule($name ,$projectDir = ".")
    {
        # this opt is valid under project directories
        $this->CheckZG($projectDir,1);
        # get status object
        $s = $this->GetStatus($projectDir);
        # check if module folder does not exist
        if(!file_exists($s->modules->meta->path))
            mkdir($s->modules->meta->path, 0775);
        # normalizing the args
        $name = preg_replace("#(\w+)module$#i","$1", $name)."Module";
        $bs_name = preg_replace("#(\w+)module$#i","$1", $name)."Bootstrap";
        # in case of module folder already exist
        if(\zinux\kernel\utilities\fileSystem::resolve_path("{$s->modules->meta->path}/{$name}"))
            throw new \zinux\kernel\exceptions\invalideOperationException("Module '{$name}' already exists ...");
        # create a module item
        $module = new \zinux\zg\vendors\item("{$name}", "{$s->modules->meta->path}/{$name}", $s->modules->meta);
        # add the module to modules collections
        $s->modules->collection[strtolower($module->name)] = $module;
        # save status object
        $this->SaveStatus($s);
        # create directory structures
        $this->Run(array(
                "mkdir {$module->path}",
                "cd {$module->path} && mkdir controllers",
                "cd {$module->path} && mkdir models",
                "cd {$module->path} && mkdir views",
                "cd {$module->path}/views && mkdir view",
                "cd {$module->path}/views && mkdir helper",
                "cd {$module->path}/views && mkdir layout",
                "chmod 775 -R {$module->path}"    
        ));
        # add a new bootstrap to module
        new \zinux\zg\vendors\creators\createModuleBootstrap($module, new \zinux\zg\vendors\Item("{$bs_name}", $module->path."/{$bs_name}.php"), $projectDir);
        # return created module
        return $module;
    } 
    /**
     * creates new controller
     * @param string $name controller's name 
     * @param \zinux\zg\vendors\Item $module parent module object
     * @param string $projectDir project direcroty
     * @return \zinux\zg\vendors\Item created controller
     */
    public function createController($name, Item $module ,$projectDir = ".")
    {
        # this opt is valid under project directories
        $this->CheckZG($projectDir,1);
        # normalizing the arg
        $name = preg_replace("#(\w+)controller$#i","$1", $name)."Controller";
        # create new controller object
        $controller = new \zinux\zg\vendors\Item($name, $module->path."/controllers/{$name}.php");
        # create the controller
        new \zinux\zg\vendors\creators\createController($module, $controller, $projectDir);
        # return created controller
        return $controller;
    }
    /**
     * creates new action
     * @param string $name action's name
     * @param \zinux\zg\vendors\item $controller parent controller object
     * @param string $projectDir project directory
     * @return \zinux\zg\vendors\Item created action
     */
    public function createAction($name, item $controller,$projectDir = ".")
    {
        # this opt is valid under project directories
        $this->CheckZG($projectDir,1);
        # normalizing the arg
        $name = preg_replace("#(\w+)action$#i","$1", $name)."Action";
        # create the action object
        $action =  new \zinux\zg\vendors\item($name, $name);
        # create the action
        new \zinux\zg\vendors\creators\createAction($controller, $action);
        # return created action
        return $action;
    }
    
    /**
     * creates new application bootstrap
     * @param string $name bootstrap's name
     * @param string $projectDir project directory
     * @return \zinux\zg\vendors\Item created bootstrap
     */
    public function createAppBootstrap($name, $projectDir = ".")
    {
        # this opt is valid under project directories
        $this->CheckZG($projectDir,1);
        # get status object
        $s = $this->GetStatus($projectDir);
        # normalizing the arg
        $name = preg_replace("#(\w+)bootstrap$#i","$1", $name)."Bootstrap";
        # create the bs object
        $appbs = new \zinux\zg\vendors\Item($name, $s->project->path."/application/{$name}.php");
        # create the bs
        new \zinux\zg\vendors\creators\createAppBootstrap($s->project, $appbs, $projectDir);
        # return the created bs
        return $appbs;
    }
    /**
     * creates new application routes
     * @param string $name routes' name
     * @param string $projectDir project directory
     * @return \zinux\zg\vendors\Item
     */
    public function createAppRoutes($name, $projectDir = ".")
    {
        # this opt is valid under project directories
        $this->CheckZG($projectDir,1);
        # get status object
        $s = $this->GetStatus($projectDir);
        # normalizing the arg
        $name = preg_replace("#(\w+)routes$#i","$1", $name)."Routes";
        # create routes object
        $appr = new \zinux\zg\vendors\Item($name, $s->project->path."/application/{$name}.php");
        # create the routes
        new \zinux\zg\vendors\creators\createAppRoutes($s->project, $appr, $projectDir);
        # return the created routes
        return $appr;
    }
    /**
     * creates new view 
     * @param string $name view's name
     * @param \zinux\zg\vendors\item $controller parent controller
     * @param string $projectDir project directory
     * @return \zinux\zg\vendors\Item created view
     */
    public function createView($name, item $controller, $projectDir = ".")
    {
        # this opt is valid under project directories
        $this->CheckZG($projectDir,1);
        # normalizing the arg
        $name = preg_replace("#(\w+)view$#i","$1", $name)."View";
        # create view object
        $view = new \zinux\zg\vendors\Item($name, 
            $controller->parent->path."/views/view/".preg_replace("#(\w+)controller$#i","$1", basename($controller->path, ".php"))."/{$name}.phtml");
        # create the view
        new \zinux\zg\vendors\creators\createView($controller, $view, $projectDir);
        # return the created view
        return $view;
    }
    /**
     * creates new layout
     * @param string $name layout's name
     * @param \zinux\zg\vendors\Item $module parent module
     * @param string $projectDir project directory
     * @return \zinux\zg\vendors\Item created layout
     */
    public function createLayout($name, Item $module ,$projectDir = ".")
    {
        # this opt is valid under project directories
        $this->CheckZG($projectDir,1);
        # normalizing the arg
        $name = preg_replace("#(\w+)layout$#i","$1", $name)."Layout";
        # create new layout object
        $layout = new \zinux\zg\vendors\Item($name, $module->path."/views/layout/{$name}.phtml");
        # create the layout
        new \zinux\zg\vendors\creators\createLayout($module, $layout, $projectDir);
        # return created layout
        return $layout;
    }
    /**
     * creates new helper
     * @param string $name helper's name
     * @param \zinux\zg\vendors\Item $module parent module
     * @param string $projectDir project directory
     * @return \zinux\zg\vendors\Item created helper
     */
    public function createHelper($name, Item $module ,$projectDir = ".")
    {
        # this opt is valid under project directories
        $this->CheckZG($projectDir,1);
        # normalizing the arg
        $name = preg_replace("#(\w+)helper$#i","$1", $name)."Helper";
        # create helper object
        $helper = new \zinux\zg\vendors\Item($name, $module->path."/views/helper/{$name}.php");
        # create the helper
        new \zinux\zg\vendors\creators\createHelper($module, $helper, $projectDir);
        # return created helper
        return $helper;
    }
    /**
     * creates new model
     * @param string $name model's name
     * @param \zinux\zg\vendors\Item $module parent module
     * @param string $projectDir project directory
     * @return \zinux\zg\vendors\Item created model
     */
    public function createModel($name, Item $module ,$projectDir = ".")
    {
        # this opt is valid under project directories
        $this->CheckZG($projectDir,1);
        # normalizing the arg
        # no naming convention for models
        # $name = preg_replace("#(\w+)helper$#i","$1", $name)."Helper";
        # create model object
        $model = new \zinux\zg\vendors\Item($name, $module->path."/models/{$name}.php");
        # create the model
        new \zinux\zg\vendors\creators\createModel($module, $model, $projectDir);
        # return created model
        return $model;
    }
    
}

?>
