<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of appRoutes
 *
 * @author dariush
 */
class appRoutes extends zinux\zg\baseZg
{
    public function __construct(Item $application, Item $appRoutes, $project_path = ".")
    {
        $appRoutes->name = preg_replace("#(\w+)Routes$#i","$1", $appRoutes->name)."Routes";
        $this ->cout("Creating new application routes'", 1,  self::defColor, 0)
                ->cout($appRoutes->name, 0, self::yellow, 0)
                ->cout("' at '",0,self::defColor, 0)
                ->cout(dirname($appRoutes->path), 0, self::yellow, 0)
                ->cout("'.");
        if(!\zinux\kernel\utilities\fileSystem::resolve_path(dirname($appRoutes->path)))
            mkdir(dirname($appRoutes->path), 0775);
        $ns = preg_replace(
            array("#^".DIRECTORY_SEPARATOR."#i","#(\w+)(".DIRECTORY_SEPARATOR.")#i"),
            array("", "$1\\"), 
            str_replace($application->path, "", dirname($appRoutes->path))
        );
        $this->cout("+", 1, self::green,0);
        $mbc ="<?php
namespace $ns;
/**
* The {$application->name}'s router
*/
class {$appRoutes->name} extends \\zinux\\kernel\\routing\\routerBootstrap
{
    public function Fetch()
    {
        /**
         * Route Example For This:
         *      /foo/1234/edit/what?so=ever => /foo/edit/1234/what?so=ever
         */
        #\$this->addRoute(\"/foo/$1/edit$2\",\"/foo/edit/$1$2\");
        /**
         * Route Example For This:
         *      /foo/1234/delete/what?so=ever => /foo/delete/1234/what?so=ever
         */
        #\$this->addRoute(\"/foo/$1/delete$2\",\"/foo/delete/$1$2\");
    }
";
        file_put_contents($appBootstrap->path, $mbc);
        $this->cout("+", 0, self::green,0);
        $s = $this->GetStatus($project_path);
        $appBootstrap->parent = $application;
        $s->project->bootstraps[$appBootstrap->name]  = $appBootstrap;
        $this->SaveStatus($s);
        $this->cout("+", 0, self::green);
    }
}

?>
