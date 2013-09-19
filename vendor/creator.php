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
        $s = $this->GetStatus($projectDir);
        
        if(!file_exists($s->modules->meta->path))
            mkdir($s->modules->meta->path, 0775);
        
        if(\zinux\kernel\utilities\fileSystem::resolve_path("{$s->modules->meta->path}/{$name}Module"))
            throw new \zinux\kernel\exceptions\invalideOperationException("Module '{$name}' already exists ...");
            
        $module = new \zinux\zg\vendor\item("{$name}Module", "{$s->modules->meta->path}/{$name}Module", $s->modules->meta);
        $s->modules->modules[$module->name] = $module;
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
        new \zinux\zg\vendor\moduleBootstrap($module, new \zinux\zg\vendor\Item("{$name}Bootstrap", $module->path."/{$name}Bootstrap.php"), $projectDir);
        return $module;
    } 
    public function createController($name, Item $module ,$projectDir = ".")
    {
        $s = $this->GetStatus($projectDir);
        $name = preg_replace("#(\w+)controller$#i","$1", $name)."Controller";
        $controller = new \zinux\zg\vendor\Item($name, $module->path."/controllers/{$name}.php");
        new \zinux\zg\vendor\createController($module, $controller, $projectDir);
        return $controller;
    }
    
    public function createAppBootstrap($name, $projectDir = ".")
    {
        $s = $this->GetStatus($projectDir);
        $name = preg_replace("#(\w+)bootstrap$#i","$1", $name)."Bootstrap";
        $appbs = new \zinux\zg\vendor\Item($name, $s->project->path."/application/{$name}.php");
        new \zinux\zg\vendor\appBootstrap($s->project, $appbs, $projectDir);
        return $appbs;
    }
    public function createAppRoutes($name, $projectDir = ".")
    {
        $s = $this->GetStatus($projectDir);
        $name = preg_replace("#(\w+)routes$#i","$1", $name)."Routes";
        $appbs = new \zinux\zg\vendor\Item($name, $s->project->path."/application/{$name}.php");
        new \zinux\zg\vendor\appBootstrap($s->project, $appbs, $projectDir);
        return $appbs;
    }
}

?>
