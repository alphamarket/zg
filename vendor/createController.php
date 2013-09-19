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
        $mbc = "<?php
namespace {$module->parent->name}\\{$module->name}\\controllers;


/**
 * IndexController
 * @author Zinux Generator <b.g.dariush@gmail.com>
 */
class ".preg_replace("#controller$#i","", $controller->name)."Controller extends \\zinux\\kernel\\controller\\baseController
{
}
";
        if(!\zinux\kernel\utilities\fileSystem::resolve_path("{$module->path}/views/view/"))
            mkdir("{$module->path}/views/view/", 0775);
            
        $this->Run(array(
                "mkdir {$module->path}/views/view/".preg_replace("#controller$#i","", $controller->name)
        ));
        file_put_contents($controller->path, $mbc);
        $this->cout("+", 0, self::green);
        $s = $this->GetStatus($project_path);
        $controller->parent = $module;
        $s->modules->modules[$module->name]->controller[$controller->name] = $controller;
        $this->SaveStatus($s);
        new createAction($controller, new item("index", "IndexAction"), $project_path);
    }
}

?>
