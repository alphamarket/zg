<?php
namespace zg\vendors;
/**
 * Zinux item creation handler
 */
class creator extends \zg\operators\baseOperator
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
     * @param string $project_path project directory to create
     * @return \zg\vendors\item the created module
     * @throws \zinux\kernel\exceptions\invalidOperationException in case of module's folder already exists
     */
    public function createModule($name ,$project_path = ".")
    {
        # this opt is valid under project directories
        $this->CheckZG($project_path,1);
        # get status object
        $s = $this->GetStatus($project_path);
        # check if module folder does not exist
        if(!file_exists($s->modules->meta->path))
            mkdir($s->modules->meta->path, 0775,1);
        # normalizing the args
        $this->NormalizeName($name, "module");
        $bs_name = preg_replace("#(\w+)module$#i","$1", $name)."Bootstrap";
        # in case of module folder already exist
        if(\zinux\kernel\utilities\fileSystem::resolve_path("{$s->modules->meta->path}/{$name}"))
            throw new \zinux\kernel\exceptions\invalidOperationException("Module '{$name}' already exists ...");
        # create a module item
        $module = new \zg\vendors\item("{$name}", "{$s->modules->meta->path}/{$name}", $s->modules->meta);
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
        new \zg\vendors\creators\createModuleBootstrap($module, new \zg\vendors\Item("{$bs_name}", $module->path."/{$bs_name}.php"), $project_path);
        # return created module
        return $module;
    } 
    /**
     * creates new controller
     * @param string $name controller's name 
     * @param \zg\vendors\Item $module parent module object
     * @param string $project_path project direcroty
     * @return \zg\vendors\Item created controller
     */
    public function createController($name, Item $module ,$project_path = ".")
    {
        # this opt is valid under project directories
        $this->CheckZG($project_path,1);
        # normalizing the arg
        $this->NormalizeName($name, "controller");
        # create new controller object
        $controller = new \zg\vendors\Item($name, $module->path."/controllers/{$name}.php");
        # create the controller
        new \zg\vendors\creators\createController($module, $controller, $project_path);
        # return created controller
        return $controller;
    }
    /**
     * creates new action
     * @param string $name action's name
     * @param \zg\vendors\item $controller parent controller object
     * @param string $project_path project directory
     * @return \zg\vendors\Item created action
     */
    public function createAction($name, item $controller,$project_path = ".")
    {
        # this opt is valid under project directories
        $this->CheckZG($project_path,1);
        # normalizing the arg
        $this->NormalizeName($name, "action");
        # create the action object
        $action =  new \zg\vendors\item($name, $name);
        # create the action
        new \zg\vendors\creators\createAction($controller, $action);
        # return created action
        return $action;
    }
    
    /**
     * creates new application bootstrap
     * @param string $name bootstrap's name
     * @param string $project_path project directory
     * @return \zg\vendors\Item created bootstrap
     */
    public function createAppBootstrap($name, $project_path = ".")
    {
        # this opt is valid under project directories
        $this->CheckZG($project_path,1);
        # get status object
        $s = $this->GetStatus($project_path);
        # normalizing the arg
        $this->NormalizeName($name, "bootstrap");
        # validate the app path
        if(!isset($s->project->meta->app_path))
        {
            # validate the project's meta
            if(!isset($s->project->meta))
                $s->project->meta = new \stdClass();
            # define  the project's app path
            $s->project->meta->app_path = \realpath($s->project->path)."/application/";
            # save changes
            $this->SaveStatus($s);
        }
        # create the bs object
        $appbs = new \zg\vendors\Item($name, $s->project->meta->app_path."/{$name}.php");
        # create the bs
        new \zg\vendors\creators\createAppBootstrap($s->project, $appbs, $project_path);
        # return the created bs
        return $appbs;
    }
    /**
     * creates new application routes
     * @param string $name routes' name
     * @param string $project_path project directory
     * @return \zg\vendors\Item
     */
    public function createAppRoutes($name, $project_path = ".")
    {
        # this opt is valid under project directories
        $this->CheckZG($project_path,1);
        # get status object
        $s = $this->GetStatus($project_path);
        # normalizing the arg
        $this->NormalizeName($name, "routes");
        # validate the app path
        if(!isset($s->project->meta->app_path))
        {
            # validate the project's meta
            if(!isset($s->project->meta))
                $s->project->meta = new \stdClass();
            # define  the project's app path
            $s->project->meta->app_path = \realpath($s->project->path)."/application/";
            # save changes
            $this->SaveStatus($s);
        }
        # create routes object
        $appr = new \zg\vendors\Item($name, $s->project->meta->app_path."/{$name}.php");
        # create the routes
        new \zg\vendors\creators\createAppRoutes($s->project, $appr, $project_path);
        # return the created routes
        return $appr;
    }
    /**
     * creates new view 
     * @param string $name view's name
     * @param \zg\vendors\item $controller parent controller
     * @param string $project_path project directory
     * @return \zg\vendors\Item created view
     */
    public function createView($name, item $controller, $project_path = ".")
    {
        # this opt is valid under project directories
        $this->CheckZG($project_path,1);
        # normalizing the arg
        $this->NormalizeName($name, "view");
        # create view object
        $view = new \zg\vendors\Item($name, 
            $controller->parent->path."/views/view/".preg_replace("#(\w+)controller$#i","$1", basename($controller->path, ".php"))."/{$name}.phtml");
        # create the view
        new \zg\vendors\creators\createView($controller, $view, $project_path);
        # return the created view
        return $view;
    }
    /**
     * creates new layout
     * @param string $name layout's name
     * @param \zg\vendors\Item $module parent module
     * @param string $project_path project directory
     * @return \zg\vendors\Item created layout
     */
    public function createLayout($name, Item $module ,$project_path = ".")
    {
        # this opt is valid under project directories
        $this->CheckZG($project_path,1);
        # normalizing the arg
        $this->NormalizeName($name, "layout");
        # create new layout object
        $layout = new \zg\vendors\Item($name, $module->path."/views/layout/{$name}.phtml");
        # create the layout
        new \zg\vendors\creators\createLayout($module, $layout, $project_path);
        # return created layout
        return $layout;
    }
    /**
     * creates new helper
     * @param string $name helper's name
     * @param \zg\vendors\Item $module parent module
     * @param string $project_path project directory
     * @return \zg\vendors\Item created helper
     */
    public function createHelper($name, Item $module ,$project_path = ".")
    {
        # this opt is valid under project directories
        $this->CheckZG($project_path,1);
        # normalizing the arg
        $this->NormalizeName($name, "helper");
        # create helper object
        $helper = new \zg\vendors\Item($name, $module->path."/views/helper/{$name}.php");
        # create the helper
        new \zg\vendors\creators\createHelper($module, $helper, $project_path);
        # return created helper
        return $helper;
    }
    /**
     * creates new model
     * @param string $name model's name
     * @param \zg\vendors\Item $module parent module
     * @param string $project_path project directory
     * @return \zg\vendors\Item created model
     */
    public function createModel($name, Item $module ,$project_path = ".")
    {
        # this opt is valid under project directories
        $this->CheckZG($project_path,1);
        # normalizing the arg
        # no naming convention for models
        # $name = preg_replace("#(\w+)helper$#i","$1", $name)."Helper";
        $this->NormalizeName($name);
        # create model object
        $model = new \zg\vendors\Item($name, $module->path."/models/{$name}.php");
        # create the model
        new \zg\vendors\creators\createModel($module, $model, $project_path);
        # return created model
        return $model;
    }
    
}

?>
