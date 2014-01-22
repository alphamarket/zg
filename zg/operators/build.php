<?php
namespace zg\operators;
/**
 * zg build * handler
 */
class build extends \zg\vendors\builder\baseBuilder
{    
    /**
     * check if build should be verbose!?
     * @var boolean
     */
    protected $verbose = 1;
    /**
     * ctor a new project builder
     * @param boolean $suppress_header_text check if should it suppress header txt
     * @param boolean $verbose check if it should be verbose
     */
    public function __construct($suppress_header_text = 0, $verbose = 1)
    {
        # ctor the parent
        parent::__construct($suppress_header_text);
        # init log instance
        $this->log = array();
        # init processed instance
        $this->processed = array();
        # flag verbosely
        $this->verbose = $verbose;
    }
    /**
     * zg build handler
     * @throws \zinux\kernel\exceptions\notFoundException in case of one of project/app/module directory not found
     */
    public function build($args)
    {
        # this can have upto 6 options can be passed (3 pair opt)
        $this->restrictArgCount($args,6,0);
        # flag that app route passed
        $this->app_pased = 1;
        # it project root not supplied
        if(!($_root = $this->get_pair_arg_value($args, "-p")))
            # set CWD
            $_root = WORK_ROOT;
        # if application not supplied
        if(!($_app = $this->get_pair_arg_value($args, "-a")))
        {
            # unflag that app route passed
            unset($this->app_pased);
            # set app dir to default 
            $this->app = $_root.DIRECTORY_SEPARATOR."application";
        }
        # if module path not supplied
        if(!($_module = $this->get_pair_arg_value($args, "-m")))
            # set module dir to default 
            $_module = "modules";
        # project root not exists
        if(!($this->root = \zinux\kernel\utilities\fileSystem::resolve_path($_root)))
            throw new \zinux\kernel\exceptions\notFoundException("'$_root' does not exist...");
        # if app route passed and not exists ( a project can have no app folder)
        if(isset($this->app_pased) && !($this->app = \zinux\kernel\utilities\fileSystem::resolve_path($_root.DIRECTORY_SEPARATOR.$_app)))
            throw new \zinux\kernel\exceptions\notFoundException("'".$_root.DIRECTORY_SEPARATOR.$_app."' does not exist...");
        # if no module dir exists
        if(!($this->modules = \zinux\kernel\utilities\fileSystem::resolve_path($_root.DIRECTORY_SEPARATOR.$_module)))
            throw new \zinux\kernel\exceptions\notFoundException("'".$_root.DIRECTORY_SEPARATOR.$_module."' does not exist...");
        # now we have secured our $root && $module path
        # create an virtual status object in ram
        $this->s = $this->creatVirtualStatusFile($this->root);
        # first define module pass to status object
        $this->s->modules->meta = new \zg\vendors\Item("modules", $this->modules, $this->s->project);
        # init module collection
        $this->s->modules->collection = array();
        # fetch app dir's items
        $this->fetchApplication();
        # fetch modules items
        $this->fetchModules();
        # fetch controllers in modules
        $this->fetchController();
        # fetch actions in controllers
        $this->fetchAction();
        # fetch models in modules
        $this->fetchModels();
        # fetch helpers in modules
        $this->fetchHelpers();
        # fetch layouts in modules
        $this->fetchLayouts();
        # fetch views in controllers
        $this->fetchViewes();
        # if no item found
        if(!count($this->processed))
        {
            # indictate it
            $this ->cout()
                    ->cout("The current directory structure didn't with any standard zinux project", 1, self::getColor(self::yellow))
                    ->cout("No zinux project has been built!", 1, self::getColor(self::red))
                    ->cout("[ Aborting ]", 1, self::getColor(self::red));
            # remove the project conf dir
            exec("rm -fr .".PRG_CONF_PATH);
            return;
        }
        # otherwise saved retrieved status object
        $this->SaveStatus($this->s);
        # indicate it
        $this ->cout()
                ->cout()
                ->cout(self::getColor(self::green)."+".self::getColor(self::defColor)." The built config file has saved ".self::getColor(self::green)."successfully".self::getColor(self::defColor).".", 1);
        # save build log
        $this->saveLogs();
    }
    # override the cout method 
    public function cout($content = "<br />", $tap_index = 0, $color = self::defColor, $auto_break = 1)
    {
        # if not verbose we do not print any thing
        if(!$this->verbose) return $this;
        # otherwise print normally
        parent::cout($content, $tap_index, $color, $auto_break);
        return $this;
    }
    /**
     * zg build log handler
     * @throws \zinux\kernel\exceptions\invalideOperationException if invalid arg supplied
     */
    public function log($args)
    {
        # this opt is valid under project directories
        if(!$this->CheckZG()) return;
        # it can have max 1 and min 0 arg
        $this->restrictArgCount($args,1,0);
        /*
         * $op = 0 : --all
         * $op = 1 : --events
         * $op = 2 : --proc
         * $op = 3 : --clear
         */
        $op = 0;
        # fetch arg type
        if(count($args))
        {
            $found = 0;
            foreach(array('-a', '-e', '-p', "--clear") as $index => $value)
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
        # invoke the cache file handler
        $fc = new \zinux\kernel\caching\fileCache("ZG_BUILD_COMMAND_LOG");
        # fetch all cached data regarding this class
        $all = $fc->fetchAll();
        # new line
        $this->cout();
        switch($op)
        {
            # in case of all 
            case 0:
            # in case of events
            case 1:
                # if no log cached
                if(!isset($all['log']) || !count($all['log']))
                {
                    # inddicate it
                    $this ->cout("[X] ", 0.5, self::getColor(self::red), 0);
                    $this->cout("No event record found ...!");
                    # goto extraction point
                    goto __END_EV;
                }
                # otherwise print the logged data
                $this ->cout("[OK] ", 0.5, self::getColor(self::green), 0);
                $this->cout("Build Events :");
                $this->print_log($all['log']);
# extraction point
__END_EV:
                # if its not case of all
                if($op)
                    # return
                    break;
                # otherwise
                $this->cout();
            # in case of processed
            case 2:
                # if no processed cached
                if(!isset($all['processed']) || !count($all['processed']))
                {
                    # indicate it
                    $this ->cout("[X] ", 0.5, self::getColor(self::red), 0);
                    $this->cout("No processed record found...!");
                    # return
                    return;
                }
                # otherwise print the processed data
                $this ->cout("[OK] ", 0.5, self::getColor(self::green), 0);
                $this->cout("Processed items:");
                $this->print_log($all['processed']);
                break;
            # in case of clear
            case 3:
                # delete all cache data
                $fc->deleteAll();
                # indicate it
                $this ->cout("- ", 0.5, self::getColor(self::red), 0)
                        ->cout("Build log cleared!");
                break;
            # otherwise this is an invalid arg
            default:
                throw new \zinux\kernel\exceptions\invalideOperationException("Invalid opteration #$op!");
        }
    }
    /**
     * Prints passed logged array
     * @param array $log
     */
    protected function print_log($log)
    {
        $c = 0;
        foreach($log as $value)
        {
            $c++;
            $this ->cout("[ {$c} ]", 1, self::getColor(self::yellow),0)
                    ->cout(" : ")
                    ->cout("{", 1);
            if(!isset($value->path) || !strlen($value->path))
            {
                $value->path = $value->name;
                unset($value->name);
            }
            if(isset($value->name))
                $this ->cout("Title", 2, self::getColor(self::yellow),0)
                        ->cout(" : ", 0, self::getColor(self::yellow), 0)
                        ->cout($value->name);
            $this ->cout("Detail", 2, self::getColor(self::yellow),0)
                    ->cout(" : ", 0, self::getColor(self::yellow), 0)
                    ->cout($value->path);

            $this->cout("}", 1);
        }
    }
}
