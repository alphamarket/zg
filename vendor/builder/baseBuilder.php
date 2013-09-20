<?php
namespace zinux\zg\vendor\builder;

abstract class baseBuilder extends \zinux\zg\resources\operator\baseOperator
{
    /**
     * Virtual zg status container
     * @var item
     */
    protected $s;
    /**
     * holds log items during building process
     * @var array()
     */
    protected $log; 
    /**
     * holds items that has been processed successfully during build process
     * @var array()
     */
    protected $processed; 
    
    public function creatVirtualStatusFile($project_path)
    {
        $this->step_show();
        if(!($project_path = \zinux\kernel\utilities\fileSystem::resolve_path($project_path, 1)))
            throw new \zinux\kernel\exceptions\notFoundException("The project path didn't found...");
        $this->step_show();
        $s = new \zinux\zg\vendor\status;
        $parent = new \zinux\zg\vendor\item(dirname($project_path), dirname($project_path));
        $s->project = new \zinux\zg\vendor\item("project", $project_path ,$parent);
        $this->step_show();
        $s->hs = \zinux\kernel\security\hash::Generate(serialize($s),1,1);
        $this->step_show();
        return $s;
    }
    protected function fetchApplication()
    {
        $this->step_show();
        if(!($this->app = \zinux\kernel\utilities\fileSystem::resolve_path($this->app)))
        {
            $this->step_show(1, "x", self::hiRed);
            $this->log[] = new \zinux\zg\vendor\item("Application directory not found","Skipping processing application directory.");
            return;
        }
        $this->step_show();
        foreach(glob("{$this->app}/*") as $file)
        {
            if(!is_file($file)) continue;
            $name = basename($file, ".php");
            $sp = preg_split("/((?<=[a-z])(?=[A-Z])|(?=[A-Z][a-z]))/",$name);
            $type = strtolower(array_pop($sp)."s");
            $app = new \zinux\zg\vendor\item($name, $file, $this->s->project);
            $this->s->project->{$type}[] = $app;
            $this->processed[] = $app;
            $this->step_show();
        }
        $this->step_show();
    }
    protected function fetchModules()
    {
        $this->s->modules->modules = array();
        $this->step_show();
        foreach(glob("{$this->modules}/*", GLOB_ONLYDIR) as $module)
        {
            if(preg_match("#\w+module$#i", $module))
            {
                $this->step_show();
                $name = basename($module);
                $module = new \zinux\zg\vendor\item("{$name}", "{$this->s->modules->meta->path}/{$name}", $this->s->modules->meta);
                $this->s->modules->modules[strtolower($module->name)] = $module;
                $bs = preg_replace("#module$#i","Bootstrap.php", $name);
                if(($bootstrap = \zinux\kernel\utilities\fileSystem::resolve_path($module->path.DIRECTORY_SEPARATOR.$bs)))
                {
                    $moduleBootstrap = new \zinux\zg\vendor\item(basename($bs, ".php"), $bootstrap, $module);
                    $this->s->modules->modules[strtolower($module->name)]->bootstrap = $moduleBootstrap;
                    $this->processed[] = $moduleBootstrap;
                    $this->step_show();
                }
                $this->processed[] = $module;
                $this->step_show();
            }
            else
            {
                $this->step_show(1, "x", self::hiRed);
                $this->log[] = new \zinux\zg\vendor\item("Folder '$module' skipped", "Didn't match with standrad module folder pattern.");
            }
        }
        $this->step_show();
    }
    protected function fetchController()
    {
        $this->step_show();
        foreach($this->s->modules->modules as $name => $module)
        {
            if(!($cp = \zinux\kernel\utilities\fileSystem::resolve_path("{$module->path}/controllers")))
            {
                $this->step_show(1, "x", self::hiRed);
                $this->log[] = new \zinux\zg\vendor\item("Controllers folder not found at '{$module->path}'", "");
                continue;
            }
            $this->step_show();
            foreach(glob("$cp/*") as $file)
            {
                $name = basename($file, ".php");
                if(is_file($file) && preg_match("#\w+controller.php$#i", $file))
                {
                    $this->step_show();
                    $this->check_php_syntax($file);
                    $class = $this->convert_to_relative_path($file, $this->root, $this->s)."\\$name";
                    if(class_exists($class))
                    {
                        $this->step_show(1, "x", self::hiRed);
                        $this->log[] = new \zinux\zg\vendor\item("Skipped requiring '$file'", "The class '$class' already defined!");
                    }
                    elseif(!is_readable($file) || !(require_once $file))
                    {  
                        $this->step_show(1, "x", self::hiRed);
                        $this->log[] = new \zinux\zg\vendor\item("File '$file' skipped", "Couldn't open the file.");
                        continue;
                    }
                    $this->step_show();
                    if(!class_exists($class))
                    {
                        $this->step_show(1, "x", self::hiRed);
                        $this->log[] = new \zinux\zg\vendor\item("Controller '$file' found, but relative class '$class' not found","");  
                        continue;
                    }
                    $this->step_show(); 
                    $controller = new \zinux\zg\vendor\Item($name, $file, $module);
                    $this->s->modules->modules[strtolower($module->name)]->controller[strtolower($controller->name)] = $controller;
                    $this->processed[] = $controller;
                    $this->step_show();
                }
                elseif(is_file($file))
                {
                    $this->step_show(1, "x", self::hiRed);
                    $this->log[] = new \zinux\zg\vendor\item("File '$file' skipped","Didn't match with standard controller file pattern.");
                }
            }
            $this->step_show();
        }
        $this->step_show();
    }
    protected function fetchAction()
    {
        $this->step_show();
        foreach($this->s->modules->modules as $name => $module)
        {
            if(!isset($module->controller))
            {
                $this->step_show(1, "x", self::hiRed);
                $this->log[] = new \zinux\zg\vendor\item("No controller found in {$module->name}", "");
                continue;
            }
            $this->step_show();
            foreach($module->controller  as $cname => $controller)
            {
                $this->step_show();
                $class = $this->convert_to_relative_path($controller->path, $this->root, $this->s)."\\$cname";
                # this if should never reach TRUE, cause the 
                if(!class_exists($class))
                    require_once $controller->path;
                /* @var $method string */
                foreach(get_class_methods($class) as $method)
                {
                    $this->step_show();
                    if(preg_match("#\w+action#i", $method))
                    {
                        $this->step_show();
                        if(!is_callable(array($class, $method)))
                        {
                            $this->step_show(1, "x", self::hiRed);
                            $this->log[] = new \zinux\zg\vendor\item("Method '$method' is not callable", "In class '$class' method '$method' is not callable!");
                        }
                        $action = new \zinux\zg\vendor\item($method, $method, $controller);
                        $this->s->modules->
                            modules[strtolower($name)]->
                            controller[strtolower($cname)]->
                            action[strtolower($action->name)] = $action;
                        $this->processed[] = $action;
                        $this->step_show();
                    }
                    $this->step_show();
                }
                $this->step_show();
            }
            $this->step_show();
        }
        $this->step_show();
    }
    public function fetchModels()
    {
        $this->step_show();
        foreach($this->s->modules->modules as $name => $module)
        {
            if(!($mp = \zinux\kernel\utilities\fileSystem::resolve_path($module->path."/models")))
            {
                $this->step_show(1, "x", self::hiRed);
                $this->log[] = new \zinux\zg\vendor\item("Models folder not found at '{$module->path}'", "");
                continue;
            }
            $this->step_show();
            foreach(glob("$mp/*") as $file)
            {
                $this->step_show();
                $name = basename($file, ".php");
                if(is_file($file) && preg_match("#\w+\b(.php)\b$#i", $file))
                {
                    $this->step_show();
                    $this->check_php_syntax($file);
                    $class = $this->convert_to_relative_path($file, $this->root, $this->s)."\\$name";
                    if(class_exists($class))
                    {
                        $this->step_show(1, "x", self::hiRed);
                        $this->log[] = new \zinux\zg\vendor\item("Skipped requiring '$file'", "The class '$class' already defined!");
                    }
                    elseif(!is_readable($file) || !(require_once $file))
                    {
                        $this->step_show(1, "x", self::hiRed);
                        $this->log[] = new \zinux\zg\vendor\item("File '$file' skipped", "Couldn't open the file.");
                        continue;
                    }
                    $this->step_show();
                    if(!class_exists($class))
                    {
                        $this->step_show(1, "x", self::hiRed);
                        $this->log[] = new \zinux\zg\vendor\item("Model '$file' found, but relative class '$class' not found","");  
                        continue;
                    }
                    $this->step_show();
                    $model = new \zinux\zg\vendor\Item($name, $file, $module);
                    $this->s->modules->modules[strtolower($module->name)]->model[strtolower($model->name)] = $model;
                    $this->processed[] = $model;
                    $this->step_show();
                }
                elseif(is_file($file))
                {
                    $this->step_show(1, "x", self::hiRed);
                    $this->log[] = new \zinux\zg\vendor\item("File '$file' skipped","Didn't match with standard controller file pattern.");
                }
                $this->step_show();
            }
            $this->step_show();
        }
        $this->step_show();
    }
    public function fetchHelpers()
    {
        $this->step_show();
        foreach($this->s->modules->modules as $name => $module)
        {
            $this->step_show();
            if(!($mp = \zinux\kernel\utilities\fileSystem::resolve_path($module->path."/views/helper")))
            {
                $this->step_show(1, "x", self::hiRed);
                $this->log[] = new \zinux\zg\vendor\item("Helpers folder not found at '{$module->path}/views/helper'", "");
                continue;
            }
            $this->step_show();
            foreach(glob("$mp/*") as $file)
            {
                $this->step_show();
                $name = basename($file, ".php");
                if(is_file($file) && preg_match("#\w+\b(.php)\b$#i", $file))
                {
                    $this->step_show();
                    $this->check_php_syntax($file);
                    $class = $this->convert_to_relative_path($file, $this->root, $this->s)."\\$name";
                    if(class_exists($class))
                    {
                        $this->step_show(1, "x", self::hiRed);
                        $this->log[] = new \zinux\zg\vendor\item("Skipped requiring '$file'", "The class '$class' already defined!");
                    }
                    elseif(!is_readable($file) || !(require_once $file))
                    {
                        $this->step_show(1, "x", self::hiRed);
                        $this->log[] = new \zinux\zg\vendor\item("File '$file' skipped", "Couldn't open the file.");
                        continue;
                    }
                    $this->step_show();
                    $helper = new \zinux\zg\vendor\Item($name, $file, $module);
                    $this->s->modules->modules[strtolower($module->name)]->helper[strtolower($helper->name)] = $helper;
                    $this->processed[] = $helper;
                    $this->step_show();
                }
                elseif(is_file($file))
                {
                    $this->step_show(1, "x", self::hiRed);
                    $this->log[] = new \zinux\zg\vendor\item("File '$file' skipped","Didn't match with standard controller file pattern.");
                }
                $this->step_show();
            }
            $this->step_show();
        }
        $this->step_show();
    }
    public function fetchLayouts()
    {
        $this->step_show();
        foreach($this->s->modules->modules as $name => $module)
        {
            $this->step_show();
            if(!($mp = \zinux\kernel\utilities\fileSystem::resolve_path($module->path."/views/layout")))
            {
                $this->step_show(1, "x", self::hiRed);
                $this->log[] = new \zinux\zg\vendor\item("Layouts folder not found at '{$module->path}/views/layout'", "");
                continue;
            }
            $this->step_show();
            foreach(glob("$mp/*") as $file)
            {
                $this->step_show();
                $name = basename($file, ".phtml");
                if(is_file($file) && preg_match("#\w+layout.phtml$#i", $file))
                {
                    $this->step_show();
                    $this->check_php_syntax($file);
                    if(!is_readable($file))
                    {
                        $this->step_show(1, "x", self::hiRed);
                        $this->log[] = new \zinux\zg\vendor\item("File '$file' skipped", "Couldn't open the file.");
                        continue;
                    }   
                    $this->step_show();
                    $layout = new \zinux\zg\vendor\Item($name, $file, $module);
                    $this->s->modules->modules[strtolower($module->name)]->layout[strtolower($layout->name)] = $layout;
                    $this->processed[] = $layout;
                    $this->step_show();
                }
                elseif(is_file($file))
                {
                    $this->step_show(1, "x", self::hiRed);
                    $this->log[] = new \zinux\zg\vendor\item("File '$file' skipped","Didn't match with standard layout file pattern.");
                }
                $this->step_show();
            }
            $this->step_show();
        }
    }
    protected function fetchViewes()
    {
        $this->step_show();
        foreach($this->s->modules->modules as $name => $module)
        {
            if(!isset($module->controller))
            {
                $this->step_show(1, "x", self::hiRed);
                $this->log[] = new \zinux\zg\vendor\item("No controller found in {$module->name}", "");
                continue;
            }
            foreach($module->controller  as $cname => $controller)
            {
                $this->step_show();
                $name = preg_replace("#controller#i", "", $cname);
                if(!($vp = \zinux\kernel\utilities\fileSystem::resolve_path($module->path."/views/view/$name")))
                {
                    $this->step_show(1, "x", self::hiRed);
                    $this->log[] = new \zinux\zg\vendor\item("Views folder not found at '{$module->path}/views/view/$name'", "");
                    continue;
                }
                $this->step_show();
                foreach(glob("$vp/*") as $file)
                {
                    $this->step_show();
                    $name = basename($file, ".phtml");
                    if(is_file($file) && preg_match("#\w+view.phtml$#i", $file))
                    {
                        $this->step_show();
                        $this->check_php_syntax($file);
                        if(!is_readable($file))
                        {
                            $this->step_show(1, "x", self::hiRed);
                            $this->log[] = new \zinux\zg\vendor\item("File '$file' skipped", "Couldn't open the file.");
                            continue;
                        }
                        $this->step_show();
                        $view = new \zinux\zg\vendor\Item($name, $file, $controller);
                        $this->s->modules->modules[strtolower($module->name)]->controller[strtolower($controller->name)]->view[strtolower($view->name)] = $view;
                        $this->processed[] = $view;
                        $this->step_show();
                    }
                    elseif(is_file($file))
                    {
                        $this->step_show(1, "x", self::hiRed);
                        $this->log[] = new \zinux\zg\vendor\item("File '$file' skipped","Didn't match with standard view file pattern.");
                    }
                    $this->step_show();
                }
                $this->step_show();
            }
            $this->step_show();
        }
        $this->step_show();
    }
    
    public function saveLogs()
    {
        for($index = 0; $index<count($this->log); $index++)
        {
            for($index1 = $index+1; $index1<count($this->log); $index1++)
            {
                if(strtolower($this->log[$index]->name) == strtolower($this->log[$index1]->name))
                    unset($this->log[$index1]); 
            }
        }
        $fc = new \zinux\kernel\caching\fileCache("ZG_BUILD_COMMAND_LOG");
        $fc->save("log", $this->log);
        $fc->save("processed", $this->processed);
        $this ->cout()
                ->cout("Statistical reports:", 1)
                ->cout("Logged events# ".self::yellow.count($this->log).self::defColor.".", 2)
                ->cout("Processed items# ".self::yellow.count($this->processed).self::defColor.".", 2)
                ->cout()
                ->cout("Use '".self::yellow."zg build logs".self::defColor."' to print logged info.", 1);
    }
    protected function step_show($step_cout = 1, $char = ".", $color = self::hiGreen)
    {
        static $count = 0;
        while($step_cout--)
        {
            if(!($count++%55))
                $this->cout()->cout("",1,self::defColor,0);
            
            $this->cout($char,0,$color,0);
        }
    }
}
