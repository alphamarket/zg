<?php
namespace zinux\zg\vendor\removers;
/**
 * Description of removeView
 *
 * @author dariush
 */
class removeView extends \zinux\zg\baseZg
{
    public function __construct(\zinux\zg\vendor\item $controller, \zinux\zg\vendor\item $view, $project_path = ".")
    {
        $view->name = preg_replace("#(\w+)view$#i","$1", $view->name)."View";
        $ns = $this->convert_to_relative_path($view->path, $project_path);;
        $this ->cout("Removing new view '", 1,  self::defColor, 0)
                ->cout($view->name, 0, self::yellow, 0)
                ->cout("' for '",0,self::defColor, 0)
                ->cout($ns, 0, self::yellow, 0)
                ->cout("' controller.");
        if(!\zinux\kernel\utilities\fileSystem::resolve_path(dirname($view->path)))
            mkdir(dirname($view->path), 0775);
        $this->cout("+", 1, self::green,0);
        $mbc = "<!--
 The $ns\\{$view->name}
 @by Zinux Generator <b.g.dariush@gmail.com>
 -->
<p>
    A view for <b>{$controller->name}</b>.<br />
    Location '<b>{$view->path}</b>'.
</p>";
        file_put_contents($view->path, $mbc);
        $this->cout("+", 0, self::green,0);
        $s = $this->GetStatus($project_path);
        $view->parent = $controller;
        $s->modules->collection[strtolower($controller->parent->name)]->controller[strtolower($controller->name)]->view[strtolower($view->name)] = $view;
        $this->SaveStatus($s);
        $this->cout("+", 0, self::green);
    }
}
