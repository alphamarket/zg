<?php
namespace zinux\zg\resources\operator;

class build extends \zinux\zg\vendor\builder\baseBuilder
{
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
    }
    protected function fetchModules()
    {
        $modules = glob("{$this->modules}/*", GLOB_ONLYDIR);
        foreach($modules as $module)
        {
            if(preg_match("#\w+module$#i", $module))
            {
                $name = basename($module);
                $module = new \zinux\zg\vendor\item("{$name}", "{$this->s->modules->meta->path}/{$name}", $this->s->modules->meta);
                $this->s->modules->modules[strtolower($module->name)] = $module;
            }
        }
    }
    protected function fetchController()
    {
        
    }
}
