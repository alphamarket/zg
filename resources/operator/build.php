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
        $this->app_pased = 1;
        if(!($_root = $this->get_pair_arg_value($args, "-p")))
            $_root = WORK_ROOT;
        if(!($_app = $this->get_pair_arg_value($args, "-a")))
        {
            unset($this->app_pased);
            $this->app = "application";
        }
        if(!($_module = $this->get_pair_arg_value($args, "-m")))
            $_module = "modules";
        
        if(!($this->root = \zinux\kernel\utilities\fileSystem::resolve_path($_root)))
            throw new \zinux\kernel\exceptions\notFoundException("'$_root' does not exists...");
        if(isset($this->app_pased) && !($this->app = \zinux\kernel\utilities\fileSystem::resolve_path($_root.DIRECTORY_SEPARATOR.$_app)))
            throw new \zinux\kernel\exceptions\notFoundException("'".$_root.DIRECTORY_SEPARATOR.$_app."' does not exists...");
        if(!($this->modules = \zinux\kernel\utilities\fileSystem::resolve_path($_root.DIRECTORY_SEPARATOR.$_module)))
            throw new \zinux\kernel\exceptions\notFoundException("'".$_root.DIRECTORY_SEPARATOR.$_module."' does not exists...");
        # now we have secured our $root && $module path
        $this->s = $this->creatVirtualStatusFile($this->root);
        $this->s->modules->meta = new \zinux\zg\vendor\Item("modules", $this->modules, $this->s->project);
        $this->s->modules->modules = array();
        $this->fetchApplication();
        $this->fetchModules();
        $this->fetchController();
        $this->fetchAction();
        $this->fetchModels();
        $this->fetchHelpers();
        $this->fetchLayouts();
        $this->fetchViewes();
        $this->SaveStatus($this->s);
        $this ->cout()
                ->cout()
                ->cout(self::green."+".self::defColor." The built config file has saved ".self::green."successfully".self::defColor.".");
        $this->saveLogs();
    }
}
