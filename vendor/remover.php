<?php
namespace zinux\zg\vendor;
/**
 * Description of removemoduleBootstrap
 *
 * @author dariush
 */
class remover extends \zinux\zg\resources\operator\baseOperator
{
    public function __construct()
    {
        parent::__construct(1);
    }
    public function removeFS(item $module , $rebuild = 1,$projectDir = ".")
    {
        $this->CheckZG($projectDir,1);
        $s = $this->GetStatus($projectDir);
        exec("rm -fr '{$module->path}'");
        $this->cout("- {$module->path}", 1, self::red);
        if(!$rebuild) return;
        $b = new \zinux\zg\resources\operator\build(1);
        $b->build(array('-p', $s->project->path, "-m", $s->modules->meta->name));
    } 
    public function removeController(Item $controller ,$projectDir = ".")
    {
        $this->CheckZG($projectDir,1);
        $s = $this->GetStatus($projectDir);
        $name = preg_replace("#(\w+)controller$#i","$1", $name)."Controller";
        $controller = new \zinux\zg\vendor\Item($name, $module->path."/controllers/{$name}.php");
        new \zinux\zg\vendor\removers\removeController($module, $controller, $projectDir);
        return $controller;
    }
    public function removeAction($name, item $controller,$projectDir = ".")
    {
        $this->CheckZG($projectDir,1);
        $s = $this->GetStatus($projectDir);
        $name = preg_replace("#(\w+)action$#i","$1", $name)."Action";
        return new \zinux\zg\vendor\removers\removeAction(
                $controller, 
                new \zinux\zg\vendor\item($name, $name));
    }
    
    public function removeAppBootstrap($name, $projectDir = ".")
    {
        $this->CheckZG($projectDir,1);
        $s = $this->GetStatus($projectDir);
        $name = preg_replace("#(\w+)bootstrap$#i","$1", $name)."Bootstrap";
        $appbs = new \zinux\zg\vendor\Item($name, $s->project->path."/application/{$name}.php");
        new \zinux\zg\vendor\removers\appBootstrap($s->project, $appbs, $projectDir);
        return $appbs;
    }
    public function removeAppRoutes($name, $projectDir = ".")
    {
        $this->CheckZG($projectDir,1);
        $s = $this->GetStatus($projectDir);
        $name = preg_replace("#(\w+)routes$#i","$1", $name)."Routes";
        $appr = new \zinux\zg\vendor\Item($name, $s->project->path."/application/{$name}.php");
        new \zinux\zg\vendor\removers\appRoutes($s->project, $appr, $projectDir);
        return $appr;
    }
    public function removeView($name, item $controller, $projectDir = ".")
    {
        $this->CheckZG($projectDir,1);
        $s = $this->GetStatus($projectDir);
        $name = preg_replace("#(\w+)view$#i","$1", $name)."View";
        $view = new \zinux\zg\vendor\Item($name, 
            $controller->parent->path."/views/view/".preg_replace("#(\w+)controller$#i","$1", basename($controller->path, ".php"))."/{$name}.phtml");
        new \zinux\zg\vendor\removers\removeView($controller, $view, $projectDir);
        return $view;
    }
    public function removeLayout($name, Item $module ,$projectDir = ".")
    {
        $this->CheckZG($projectDir,1);
        $s = $this->GetStatus($projectDir);
        $name = preg_replace("#(\w+)layout$#i","$1", $name)."Layout";
        $controller = new \zinux\zg\vendor\Item($name, $module->path."/views/layout/{$name}.phtml");
        new \zinux\zg\vendor\removers\removeLayout($module, $controller, $projectDir);
        return $controller;
    }
    public function removeHelper($name, Item $module ,$projectDir = ".")
    {
        $this->CheckZG($projectDir,1);
        $s = $this->GetStatus($projectDir);
        $name = preg_replace("#(\w+)helper$#i","$1", $name)."Helper";
        $helper = new \zinux\zg\vendor\Item($name, $module->path."/views/helper/{$name}.php");
        new \zinux\zg\vendor\removers\removeHelper($module, $helper, $projectDir);
        return $helper;
    }
    public function removeModel($name, Item $module ,$projectDir = ".")
    {
        $this->CheckZG($projectDir,1);
        $s = $this->GetStatus($projectDir);
        # no naming convention for models
        # $name = preg_replace("#(\w+)helper$#i","$1", $name)."Helper";
        $model = new \zinux\zg\vendor\Item($name, $module->path."/models/{$name}.php");
        new \zinux\zg\vendor\removers\removeModel($module, $model, $projectDir);
        return $model;
    }
    
}

?>
