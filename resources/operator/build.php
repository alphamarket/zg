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
                ->cout(self::green."+".self::defColor." The built config file has saved ".self::green."successfully".self::defColor.".", 1);
        $this->saveLogs();
    }
    
    public function log($args)
    {
        if(!$this->CheckZG()) return;
        $this->restrictArgCount($args,1,0);
        /*
         * $op = 0 : --all
         * $op = 1 : --events
         * $op = 2 : --proc
         */
        $op = 0;
        if(count($args))
        {
            $found = 0;
            foreach(array('--all', '--events', '--proc', "--clear") as $index => $value)
            {
                if(strtolower($args[0]) == strtolower($value))
                {
                    $found = 1;
                    $op = $index;
                }
            }
            if(!$found)
            {
                throw new \zinux\kernel\exceptions\invalideOperationException("Invalid argument '{$args[0]}'!");
            }
        }
        $fc = new \zinux\kernel\caching\fileCache("ZG_BUILD_COMMAND_LOG");
        $all = $fc->fetchAll();
        $this->cout();
        switch($op)
        {
            case 0:
            case 1:
                if(!isset($all['log']) || !count($all['log']))
                {
                    $this ->cout("[X] ", 0.5, self::red, 0);
                    $this->cout("No event record found ...!");
                    goto __END_EV;
                }
                $this ->cout("[OK] ", 0.5, self::green, 0);
                $this->cout("Build Events :");
                $this->print_log($all['log']);
__END_EV:
                if($op)
                    break;
                $this->cout();
            case 2:
                if(!isset($all['processed']) || !count($all['processed']))
                {
                    $this ->cout("[X] ", 0.5, self::red, 0);
                    $this->cout("No processed record found...!");
                    return;
                }
                $this ->cout("[OK] ", 0.5, self::green, 0);
                $this->cout("Processed items:");
                $this->print_log($all['processed']);
                break;
            case 3:
                $fc->deleteAll();
                $this ->cout("- ", 1, self::red, 0)
                        ->cout("Build log cleared!");
                break;
            default:
                throw new \zinux\kernel\exceptions\invalideOperationException("Invalid opteration #$op!");
        }
    }
    protected function print_log($log)
    {
        $c = 0;
        foreach($log as $value)
        {
            $c++;
            $this ->cout("[ {$c} ]", 2, self::yellow,0)
                    ->cout(" : ")
                    ->cout("{", 2);
            if(!isset($value->path) || !strlen($value->path))
            {
                $value->path = $value->name;
                unset($value->name);
            }
            if(isset($value->name))
                $this ->cout("Title", 3, self::yellow,0)
                        ->cout(" : ", 0, self::yellow, 0)
                        ->cout($value->name);
            $this ->cout("Detail", 3, self::yellow,0)
                    ->cout(" : ", 0, self::yellow, 0)
                    ->cout($value->path);

            $this->cout("}", 2);
        }
    }
}
