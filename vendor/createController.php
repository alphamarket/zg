<?php
namespace zinux\zg\vendor;
/**
 * Description of createmoduleBootstrap
 *
 * @author dariush
 */

class createController extends \zinux\zg\resources\operator\baseOperator
{
    public function __construct(Item $module, Item $controller, $project_path = ".")
    {
        $controller->name = preg_replace("#controller$#i","", $controller->name)."Controller";
        $this ->cout("Creating new controller '",1,  self::defColor, 0)
                ->cout($controller->name, 0, self::yellow, 0)
                ->cout("' at '",0,self::defColor, 0)
                ->cout("{$module->parent->name}\\{$module->name}\\controllers", 0, self::yellow, 0)
                ->cout("'.");
        $this->cout("+", 1, self::green,0);
        $mbc = "<?php
namespace {$module->parent->name}\\{$module->name}\\controllers;
    
/**
 * The \\{$module->parent->name}\\{$module->name}\\controllers\\{$controller->name}
 * @by Zinux Generator <b.g.dariush@gmail.com>
 */
class ".preg_replace("#controller$#i","", $controller->name)."Controller extends \\zinux\\kernel\\controller\\baseController
{
    /**
    * The \\{$module->parent->name}\\{$module->name}\\controllers\\{$controller->name}::IndexAction()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function IndexAction()
    {
    }
}
";
        $this->cout("+", 0, self::green,0);
        if(!\zinux\kernel\utilities\fileSystem::resolve_path("{$module->path}/views/view/"))
            mkdir("{$module->path}/views/view/", 0775);
        $this->cout("+", 0, self::green,0);
        if(!\zinux\kernel\utilities\fileSystem::resolve_path("{$module->path}/views/view/".preg_replace("#controller$#i","", $controller->name)))
            mkdir("{$module->path}/views/view/".preg_replace("#controller$#i","", $controller->name), 0775);
        $this->cout("+", 0, self::green,0);
        file_put_contents($controller->path, $mbc);
        $this->cout("+", 0, self::green,0);
        $s = $this->GetStatus($project_path);
        $controller->parent = $module;
        $s->modules->modules[$module->name]->controller[$controller->name] = $controller;
        $this->SaveStatus($s);
        $this->cout("+", 0, self::green);
    }
}

?>
