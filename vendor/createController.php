<?php
namespace zinux\zg\vendor;
/**
 * Description of createModuleBoostrap
 *
 * @author dariush
 */

class createController extends \zinux\zg\resources\operator\baseOperator
{
    public function __construct(Item $module, Item $controller, $project_path = ".")
    {
        $this->cout("+", 1, self::green,0);
        $mbc = "<?php
namespace {$module->parent->name}\\{$module->name}\\controllers;
    
/**
 * The \\{$module->parent->name}\\{$module->name}\\{$controller->name}
 * @by Zinux Generator <b.g.dariush@gmail.com>
 */
class ".preg_replace("#controller$#i","", $controller->name)."Controller extends \\zinux\\kernel\\controller\\baseController
{
    /**
    * The \\{$module->parent->name}\\{$module->name}\\{$controller->name}::IndexAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function IndexAction()
    {
    }
}
";
        $this->cout("+", 0, self::green,1);
        if(!\zinux\kernel\utilities\fileSystem::resolve_path("{$module->path}/views/view/"))
            mkdir("{$module->path}/views/view/", 0775);
            
        $this->Run(array(
                "mkdir {$module->path}/views/view/".preg_replace("#controller$#i","", $controller->name)
        ),0);
        file_put_contents($controller->path, $mbc);
        $this->cout("+", 1, self::green,0);
        $s = $this->GetStatus($project_path);
        $controller->parent = $module;
        $s->modules->modules[$module->name]->controller[$controller->name] = $controller;
        $this->SaveStatus($s);
        $this->cout("+", 0, self::green);
    }
}

?>
