<?php
namespace zinux\zg\vendor;
/**
 * Description of createmoduleBootstrap
 *
 * @author dariush
 */
class creator extends \zinux\zg\resources\operator\baseOperator
{
    public function __construct()
    {
        parent::__construct(1);
    }
    public function createModule($name ,$projectDir = ".")
    {
        $this->CheckZG($projectDir,1);
        $s = $this->GetStatus($projectDir);
        
        if(!file_exists($s->modules->meta->path))
            mkdir($s->modules->meta->path, 0775);
        $name = preg_replace("#(\w+)module$#i","$1", $name)."Module";
        $bs_name = preg_replace("#(\w+)module$#i","$1", $name)."Bootstrap";
        
        if(\zinux\kernel\utilities\fileSystem::resolve_path("{$s->modules->meta->path}/{$name}"))
            throw new \zinux\kernel\exceptions\invalideOperationException("Module '{$name}' already exists ...");
            
        $module = new \zinux\zg\vendor\item("{$name}", "{$s->modules->meta->path}/{$name}", $s->modules->meta);
        $s->modules->modules[strtolower($module->name)] = $module;
        $this->SaveStatus($s);
        
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
        new \zinux\zg\vendor\creators\moduleBootstrap($module, new \zinux\zg\vendor\Item("{$bs_name}", $module->path."/{$bs_name}.php"), $projectDir);
        return $module;
    } 
    public function createController($name, Item $module ,$projectDir = ".")
    {
        $this->CheckZG($projectDir,1);
        $s = $this->GetStatus($projectDir);
        $name = preg_replace("#(\w+)controller$#i","$1", $name)."Controller";
        $controller = new \zinux\zg\vendor\Item($name, $module->path."/controllers/{$name}.php");
        new \zinux\zg\vendor\creators\createController($module, $controller, $projectDir);
        return $controller;
    }
    public function createAction($name, item $controller,$projectDir = ".")
    {
        $this->CheckZG($projectDir,1);
        $s = $this->GetStatus($projectDir);
        $name = preg_replace("#(\w+)action$#i","$1", $name)."Action";
        return new \zinux\zg\vendor\creators\createAction(
                $controller, 
                new \zinux\zg\vendor\item($name, $name));
    }
    
    public function createAppBootstrap($name, $projectDir = ".")
    {
        $this->CheckZG($projectDir,1);
        $s = $this->GetStatus($projectDir);
        $name = preg_replace("#(\w+)bootstrap$#i","$1", $name)."Bootstrap";
        $appbs = new \zinux\zg\vendor\Item($name, $s->project->path."/application/{$name}.php");
        new \zinux\zg\vendor\creators\appBootstrap($s->project, $appbs, $projectDir);
        return $appbs;
    }
    public function createAppRoutes($name, $projectDir = ".")
    {
        $this->CheckZG($projectDir,1);
        $s = $this->GetStatus($projectDir);
        $name = preg_replace("#(\w+)routes$#i","$1", $name)."Routes";
        $appr = new \zinux\zg\vendor\Item($name, $s->project->path."/application/{$name}.php");
        new \zinux\zg\vendor\creators\appRoutes($s->project, $appr, $projectDir);
        return $appr;
    }
    public function createView($name, item $controller, $projectDir = ".")
    {
        $this->CheckZG($projectDir,1);
        $s = $this->GetStatus($projectDir);
        $name = preg_replace("#(\w+)view$#i","$1", $name)."View";
        $view = new \zinux\zg\vendor\Item($name, 
            $controller->parent->path."/views/view/".preg_replace("#(\w+)controller$#i","$1", basename($controller->path, ".php"))."/{$name}.phtml");
        new \zinux\zg\vendor\creators\createView($controller, $view, $projectDir);
        return $view;
    }
    public function createLayout($name, Item $module ,$projectDir = ".")
    {
        $this->CheckZG($projectDir,1);
        $s = $this->GetStatus($projectDir);
        $name = preg_replace("#(\w+)layout$#i","$1", $name)."Layout";
        $controller = new \zinux\zg\vendor\Item($name, $module->path."/views/layout/{$name}.phtml");
        new \zinux\zg\vendor\creators\createLayout($module, $controller, $projectDir);
        return $controller;
    }
    public function createHelper($name, Item $module ,$projectDir = ".")
    {
        $this->CheckZG($projectDir,1);
        $s = $this->GetStatus($projectDir);
        $name = preg_replace("#(\w+)helper$#i","$1", $name)."Helper";
        $helper = new \zinux\zg\vendor\Item($name, $module->path."/views/helper/{$name}.php");
        new \zinux\zg\vendor\creators\createHelper($module, $helper, $projectDir);
        return $helper;
    }
    public function createModel($name, Item $module ,$projectDir = ".")
    {
        $this->CheckZG($projectDir,1);
        $s = $this->GetStatus($projectDir);
        # no naming convention for models
        # $name = preg_replace("#(\w+)helper$#i","$1", $name)."Helper";
        $model = new \zinux\zg\vendor\Item($name, $module->path."/models/{$name}.php");
        new \zinux\zg\vendor\creators\createModel($module, $model, $projectDir);
        return $model;
    }
    
}

?>
