<?php
namespace zg\vendors\creators;
/**
 * view creator
 */
class createView extends \zg\baseZg
{
    /**
     * ctor a new view
     * @param \zg\vendors\item $controller target controller item
     * @param \zg\vendors\item $view target view item
     * @param string $project_path project directory
     */
    public function __construct(\zg\vendors\item $controller, \zg\vendors\item $view, $project_path = ".")
    {
        $view_path = \preg_replace("#//#i", "/", $view->path);
        $ns = $this->convert_to_relative_path($view->path, $project_path);;
        $this ->cout("Creating new view '", 0.5,  self::getColor(self::defColor), 0)
                ->cout($view->name, 0, self::getColor(self::yellow), 0)
                ->cout("' for '",0,self::getColor(self::defColor), 0)
                ->cout($ns, 0, self::getColor(self::yellow), 0)
                ->cout("' controller.");
        if(!\zinux\kernel\utilities\fileSystem::resolve_path(dirname($view->path)))
            mkdir(dirname($view->path), 0775,1);
        $view->action = preg_replace("#(.*)(view)$#i", "$1Action", $view->name);
        $view->extention = preg_replace("#(.*)\.(.*)$#i","$2",$view->path);
        $mbc = "<!--
 The \\$ns\\{$view->name}
 @by Zinux Generator <b.g.dariush@gmail.com>
 -->
<p>
    A view for '<b>\\{$this->convert_to_relative_path($controller->path, $project_path)}\\{$controller->name}::{$view->action}()</b>'<br />
    Location: '<b>{$view_path}</b>'
</p>";
        file_put_contents($view->path, $mbc);
        $s = $this->GetStatus($project_path);
        $view->parent = $controller;
        $s->modules->collection[strtolower($controller->parent->name)]->controller[strtolower($controller->name)]->view[strtolower($view->name)] = $view;
        $this->SaveStatus($s);
    }
}