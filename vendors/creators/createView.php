<?php
namespace zinux\zg\vendors\creators;
/**
 * view creator
 */
class createView extends \zinux\zg\baseZg
{
    /**
     * ctor a new view
     * @param \zinux\zg\vendors\item $controller target controller item
     * @param \zinux\zg\vendors\item $view target view item
     * @param string $project_path project directory
     */
    public function __construct(\zinux\zg\vendors\item $controller, \zinux\zg\vendors\item $view, $project_path = ".")
    {
        $ns = $this->convert_to_relative_path($view->path, $project_path);;
        $this ->cout("Creating new view '", 0.5,  self::defColor, 0)
                ->cout($view->name, 0, self::yellow, 0)
                ->cout("' for '",0,self::defColor, 0)
                ->cout($ns, 0, self::yellow, 0)
                ->cout("' controller.");
        if(!\zinux\kernel\utilities\fileSystem::resolve_path(dirname($view->path)))
            mkdir(dirname($view->path), 0775);
        
        $mbc = "<!--
 The $ns\\{$view->name}
 @by Zinux Generator <b.g.dariush@gmail.com>
 -->
<p>
    A view for <b>{$controller->name}</b>.<br />
    Location '<b>{$view->path}</b>'.
</p>";
        file_put_contents($view->path, $mbc);
        
        $s = $this->GetStatus($project_path);
        $view->parent = $controller;
        $s->modules->collection[strtolower($controller->parent->name)]->controller[strtolower($controller->name)]->view[strtolower($view->name)] = $view;
        $this->SaveStatus($s);
    }
}