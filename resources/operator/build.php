<?php
namespace zinux\zg\resources\operator;

class build extends \zinux\zg\vendor\builder\baseBuilder
{
    public function __construct($suppress_header_text = 0)
    {
        parent::__construct($suppress_header_text);
        $this->log = array();
        $this->processed = array();
    }
    public function build($args)
    {
        $this->restrictArgCount($args,4,0);
        if(!($_root = $this->get_pair_arg_value($args, "-p")))
            $_root = WORK_ROOT;
        if(!($_module = $this->get_pair_arg_value($args, "-m")))
            $_module = "modules";
        if(!($_app = $this->get_pair_arg_value($args, "-a")))
            $_app = "application";
        
        if(!($this->root = \zinux\kernel\utilities\fileSystem::resolve_path($_root)))
            throw new \zinux\kernel\exceptions\notFoundException("'$_root' does not exists...");
        if(!($this->app = \zinux\kernel\utilities\fileSystem::resolve_path($_root.DIRECTORY_SEPARATOR.$_app)))
            throw new \zinux\kernel\exceptions\notFoundException("'".$_root.DIRECTORY_SEPARATOR.$_app."' does not exists...");
        if(!($this->modules = \zinux\kernel\utilities\fileSystem::resolve_path($_root.DIRECTORY_SEPARATOR.$_module)))
            throw new \zinux\kernel\exceptions\notFoundException("'".$_root.DIRECTORY_SEPARATOR.$_module."' does not exists...");
        # now we have secured our $root && $module path
        $this->s = $this->creatVirtualStatusFile($this->root);
        $this->s->modules->meta = new \zinux\zg\vendor\Item("modules", $this->modules, $this->s->project);
        $this->fetchModules();
        $this->fetchController();
        $this->fetchAction();
        $this->fetchModels();
        $this->fetchHelpers();
        $this->fetchLayouts();
        $this->fetchViewes();
        \zinux\kernel\utilities\debug::_var($this->log);
    }
    protected function fetchModules()
    {
        $this->s->modules->modules = array();
        foreach(glob("{$this->modules}/*", GLOB_ONLYDIR) as $module)
        {
            if(preg_match("#\w+module$#i", $module))
            {
                $name = basename($module);
                $module = new \zinux\zg\vendor\item("{$name}", "{$this->s->modules->meta->path}/{$name}", $this->s->modules->meta);
                $this->s->modules->modules[strtolower($module->name)] = $module;
                $bs = preg_replace("#module$#i","Bootstrap.php", $name);
                if(($bootstrap = \zinux\kernel\utilities\fileSystem::resolve_path($module->path.DIRECTORY_SEPARATOR.$bs)))
                {
                    $moduleBootstrap = new \zinux\zg\vendor\item(basename($bs, ".php"), $bootstrap, $module);
                    $s->modules->modules[strtolower($module->name)]->bootstrap = $moduleBootstrap;
                    $this->processed[] = $moduleBootstrap;
                }
                $this->processed[] = $module;
            }
            else
                $this->log[] = new \zinux\zg\vendor\item("Folder '$module' skipped", "Didn't match with standrad module folder pattern.");
        }
    }
    protected function fetchController()
    {
        foreach($this->s->modules->modules as $name => $module)
        {
            if(!($cp = \zinux\kernel\utilities\fileSystem::resolve_path("{$module->path}/controllers")))
            {
                $this->log[] = new \zinux\zg\vendor\item("Controllers folder not found at '{$module->path}'", "");
                continue;
            }
            foreach(glob("$cp/*") as $file)
            {
                $name = basename($file, ".php");
                if(is_file($file) && preg_match("#\w+controller.php$#i", $file))
                {
                    $this->check_php_syntax($file);
                    $class = $this->convert_to_relative_path($file, $this->root, $this->s)."\\$name";
                    if(class_exists($class))
                    {
                        $this->log[] = new \zinux\zg\vendor\item("Skipped requiring '$file'", "The class '$class' already defined!");
                    }
                    elseif(!is_readable($file) || !(require_once $file))
                    {
                        $this->log[] = new \zinux\zg\vendor\item("File '$file' skipped", "Couldn't open the file.");
                        continue;
                    }
                    if(!class_exists($class))
                    {
                        $this->log[] = new \zinux\zg\vendor\item("Controller '$file' found, but relative class '$class' not found","");  
                        continue;
                    }
                    $controller = new \zinux\zg\vendor\Item($name, $file, $module);
                    $this->s->modules->modules[strtolower($module->name)]->controller[strtolower($controller->name)] = $controller;
                    $this->processed[] = $controller;
                }
                elseif(is_file($file))
                    $this->log[] = new \zinux\zg\vendor\item("File '$file' skipped","Didn't match with standard controller file pattern.");
            }
        }
    }
    protected function fetchAction()
    {
        foreach($this->s->modules->modules as $name => $module)
        {
            if(!isset($module->controller))
            {
                $this->log[] = new \zinux\zg\vendor\item("No controller found in {$module->name}", "");
                continue;
            }
            foreach($module->controller  as $cname => $controller)
            {
                $class = $this->convert_to_relative_path($controller->path, $this->root, $this->s)."\\$cname";
                # this if should never reach TRUE, cause the 
                if(!class_exists($class))
                    require_once $controller->path;
                foreach(get_class_methods($class) as $key => $method)
                {
                    if(preg_match("#\w+action#i", $method))
                    {
                        if(!is_callable(array($class, $method)))
                            $this->log[] = new \zinux\zg\vendor\item("Method '$method' is not callable", "In class '$class' method '$method' is not callable!");
                        $action = new \zinux\zg\vendor\item($method, $method, $controller);
                        $this->s->modules->
                            modules[strtolower($name)]->
                            controller[strtolower($cname)]->
                            action[strtolower($action->name)] = $action;
                        $this->processed[] = $action;
                    }
                }
            }
        }
    }
    public function fetchModels()
    {
        foreach($this->s->modules->modules as $name => $module)
        {
            if(!($mp = \zinux\kernel\utilities\fileSystem::resolve_path($module->path."/models")))
            {
                $this->log[] = new \zinux\zg\vendor\item("Models folder not found at '{$module->path}'", "");
                continue;
            }
            foreach(glob("$mp/*") as $file)
            {
                $name = basename($file, ".php");
                if(is_file($file) && preg_match("#\w+\b(.php)\b$#i", $file))
                {
                    $this->check_php_syntax($file);
                    $class = $this->convert_to_relative_path($file, $this->root, $this->s)."\\$name";
                    if(class_exists($class))
                    {
                        $this->log[] = new \zinux\zg\vendor\item("Skipped requiring '$file'", "The class '$class' already defined!");
                    }
                    elseif(!is_readable($file) || !(require_once $file))
                    {
                        $this->log[] = new \zinux\zg\vendor\item("File '$file' skipped", "Couldn't open the file.");
                        continue;
                    }
                    if(!class_exists($class))
                    {
                        $this->log[] = new \zinux\zg\vendor\item("Model '$file' found, but relative class '$class' not found","");  
                        continue;
                    }
                    $model = new \zinux\zg\vendor\Item($name, $file, $module);
                    $this->s->modules->modules[strtolower($module->name)]->model[strtolower($model->name)] = $model;
                    $this->processed[] = $model;
                }
                elseif(is_file($file))
                    $this->log[] = new \zinux\zg\vendor\item("File '$file' skipped","Didn't match with standard controller file pattern.");
            }
        }
    }
    public function fetchHelpers()
    {
        foreach($this->s->modules->modules as $name => $module)
        {
            if(!($mp = \zinux\kernel\utilities\fileSystem::resolve_path($module->path."/views/helper")))
            {
                $this->log[] = new \zinux\zg\vendor\item("Helpers folder not found at '{$module->path}/views/helper'", "");
                continue;
            }
            foreach(glob("$mp/*") as $file)
            {
                $name = basename($file, ".php");
                if(is_file($file) && preg_match("#\w+\b(.php)\b$#i", $file))
                {
                    $this->check_php_syntax($file);
                    $class = $this->convert_to_relative_path($file, $this->root, $this->s)."\\$name";
                    if(class_exists($class))
                    {
                        $this->log[] = new \zinux\zg\vendor\item("Skipped requiring '$file'", "The class '$class' already defined!");
                    }
                    elseif(!is_readable($file) || !(require_once $file))
                    {
                        $this->log[] = new \zinux\zg\vendor\item("File '$file' skipped", "Couldn't open the file.");
                        continue;
                    }
                    $helper = new \zinux\zg\vendor\Item($name, $file, $module);
                    $this->s->modules->modules[strtolower($module->name)]->helper[strtolower($helper->name)] = $helper;
                    $this->processed[] = $helper;
                }
                elseif(is_file($file))
                    $this->log[] = new \zinux\zg\vendor\item("File '$file' skipped","Didn't match with standard controller file pattern.");
            }
        }
    }
    public function fetchLayouts()
    {
        foreach($this->s->modules->modules as $name => $module)
        {
            if(!($mp = \zinux\kernel\utilities\fileSystem::resolve_path($module->path."/views/layout")))
            {
                $this->log[] = new \zinux\zg\vendor\item("Layouts folder not found at '{$module->path}/views/layout'", "");
                continue;
            }
            foreach(glob("$mp/*") as $file)
            {
                $name = basename($file, ".phtml");
                if(is_file($file) && preg_match("#\w+layout.phtml$#i", $file))
                {
                    $this->check_php_syntax($file);
                    if(!is_readable($file))
                    {
                        $this->log[] = new \zinux\zg\vendor\item("File '$file' skipped", "Couldn't open the file.");
                        continue;
                    }
                    $layout = new \zinux\zg\vendor\Item($name, $file, $module);
                    $this->s->modules->modules[strtolower($module->name)]->layout[strtolower($layout->name)] = $layout;
                    $this->processed[] = $layout;
                }
                elseif(is_file($file))
                    $this->log[] = new \zinux\zg\vendor\item("File '$file' skipped","Didn't match with standard layout file pattern.");
            }
        }
    }
    protected function fetchViewes()
    {
        foreach($this->s->modules->modules as $name => $module)
        {
            if(!isset($module->controller))
            {
                $this->log[] = new \zinux\zg\vendor\item("No controller found in {$module->name}", "");
                continue;
            }
            foreach($module->controller  as $cname => $controller)
            {
                $name = preg_replace("#controller#i", "", $cname);
                if(!($vp = \zinux\kernel\utilities\fileSystem::resolve_path($module->path."/views/view/$name")))
                {
                    echo $this->log[] = new \zinux\zg\vendor\item("Views folder not found at '{$module->path}/views/view/$name'", "");
                    continue;
                }
                foreach(glob("$vp/*") as $file)
                {
                    $name = basename($file, ".phtml");
                    if(is_file($file) && preg_match("#\w+view.phtml$#i", $file))
                    {
                        $this->check_php_syntax($file);
                        if(!is_readable($file))
                        {
                            $this->log[] = new \zinux\zg\vendor\item("File '$file' skipped", "Couldn't open the file.");
                            continue;
                        }
                        $view = new \zinux\zg\vendor\Item($name, $file, $controller);
                        $T = $this->s->modules->modules[strtolower($module->name)]->controller[strtolower($controller->name)]->view[strtolower($view->name)] = $view;
                        $this->processed[] = $view;
                    }
                    elseif(is_file($file))
                        $this->log[] = new \zinux\zg\vendor\item("File '$file' skipped","Didn't match with standard view file pattern.");
                }
            }
        }
    }
}
