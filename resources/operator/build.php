<?php
namespace zinux\zg\resources\operator;

class build extends \zinux\zg\vendor\builder\baseBuilder
{
    public function __construct($suppress_header_text = 0)
    {
        parent::__construct($suppress_header_text);
        $this->log = array();
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
        #\zinux\kernel\utilities\debug::_var($this->log);
    }
    protected function fetchModules()
    {
        foreach(glob("{$this->modules}/*", GLOB_ONLYDIR) as $module)
        {
            if(preg_match("#\w+module$#i", $module))
            {
                $name = basename($module);
                $module = new \zinux\zg\vendor\item("{$name}", "{$this->s->modules->meta->path}/{$name}", $this->s->modules->meta);
                $this->s->modules->modules[strtolower($module->name)] = $module;
            }
            else
                $this->log[] = new \zinux\zg\vendor\item("Folder '$module' skipped", "Didn't match with standrad module folder pattern.");
        }
    }
    protected function fetchController()
    {
        foreach($this->s->modules->modules as $name => $module)
        {
            foreach(glob("{$module->path}/controllers/*") as $file)
            {
                $name = basename($file, ".php");
                if(is_file($file) && preg_match("#\w+controller$#i", $name))
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
            foreach($module->controller  as $cname => $contoller)
            {
                $class = $this->convert_to_relative_path($contoller->path, $this->root, $this->s)."\\$cname";
                # this if should never reach TRUE, cause the 
                if(!class_exists($class))
                    require_once $contoller->path;
                foreach(get_class_methods($class) as $key => $method)
                {
                    if(preg_match("#\w+action#i", $method))
                        $this->cout("$key   =>   $method");
                }
            }
        }
    }
}
