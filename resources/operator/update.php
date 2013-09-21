<?php
namespace zinux\zg\resources\operator;

class update extends baseOperator
{    
    public function __construct($suppress_header_text = 0)
    {
        parent::__construct($suppress_header_text);
        $this->output_buffering = ini_get("output_buffering");
        ini_set('output_buffering','on');
        
    }
    public function __destruct()
    {
        ini_set('output_buffering',$this->output_buffering);
    }
    public function update($args)
    {
        # should support --cache to update cache files...!
        $this->restrictArgCount($args, 3, 0);
        while(count($args))
        {
            $arg = array_shift($args);
            switch($arg)
            {
                case "--cache":
                    $this->cache_update = 1;
                    break;
                case "--simulate":
                    $this->simulate = 1;
                    break;
                case "--verbose":
                    $this->verbose = 1;
                    break;
                default:
                    throw new \zinux\kernel\exceptions\invalideArgumentException("Invalid argument '$args[0]' supplied ...");
            }
        }
        $this->cout("Updating your project's zinux framework from its online repo.");
        $this->cout("Testing your network, please wait.... ", 0, self::defColor, 0);
        # check network 
        if(!isset($this->simulate) && !$this->is_connected())
        {
            $this->cout('[ FAILED ]',0, self::red);
            throw new \zinux\kernel\exceptions\invalideOperationException
                (self::defColor."You need to have ".self::yellow."network connection".self::defColor." to do this operation!<br />".self::red."[ Aborting ]");
        }
        $this->cout("[ OK ]",0, self::green);
        $zinux_dir = isset($this->cache_update)?Z_CACHE_ROOT:WORK_ROOT."/zinux";
        $this->cout("Checking Git application.... ",0 ,self::defColor, 0);
        # check if git exists 
        if(!exec('git 2>/dev/null | wc -l'))
            throw new \zinux\kernel\exceptions\notFoundException("'git' not found in system!<br />To install git http://git-scm.com/downloads");
        $this->cout("[ OK ]", 0, self::green);
        $this->update_repo("zinux".(isset($this->cache_update)?".cache":""), $zinux_dir);
        $this->cout("Now the ".self::yellow."zinux framework".self::defColor." is updated ...");
    }
    protected function update_repo($name, $repo_path, $indent = 0, $clone_uri = "https://github.com/dariushha/zinux")
    {
        if(false && !($path = \zinux\kernel\utilities\fileSystem::resolve_path($repo_path)))
            throw new \zinux\kernel\exceptions\notFoundException("'$repo_path' not found!");
        $indent+= 0.5;
        $debug_git = 0; 
        $repo_man = "$repo_path/manifest.json";
        $man_failed = 0;
        try
        {
            $repo = new \zinux\zg\vendor\PHPGit\Repository($repo_path, $debug_git, array('git_executable' => 'git'));
            if(!\zinux\kernel\utilities\fileSystem::resolve_path($repo_man) && ($man_failed = 1))
                throw new \zinux\kernel\exceptions\notFoundException();
        }
        catch(\Exception $e)
        {
            $this->cout("- The git repository/manifest not found in '$repo_path'.", $indent,self::red);
            $this->cout("+ Trying to download a new '".self::yellow.$name.self::green."' repository...", $indent, self::green);
            if(!isset($this->simulate) && !($man_failed = 0))
            {
                exec("rm -fr $repo_path");
                exec("cd ".dirname($repo_path)." && git clone '$clone_uri' '$name' 1>/dev/null 2>&1 ");
                $repo = new \zinux\zg\vendor\PHPGit\Repository($repo_path, $debug_git, array('git_executable' => 'git'));
                if(!\zinux\kernel\utilities\fileSystem::resolve_path($repo_man))
                {
                    $this ->cout(self::red."Notice: ".self::defColor.
                                        "The ".self::yellow.$name.self::defColor." git repository updated. but still manifest file '".
                                        self::yellow."manifest.json".self::defColor."'", $indent)
                            ->cout("not found at '".self::yellow.$repo_path.self::defColor."'!", $indent+0.5)
                            ->cout("No recursive updating will happen for repo ".self::yellow.$name.self::defColor." at '".self::yellow.$repo_path.self::defColor."'", $indent+0.5);
                    $man_failed = 1;
                }
                else
                    $man_failed = 0;
            }
        }
        if(!isset($this->simulate))
            $repo->git("checkout master");
        $this->cout("Updating '".self::yellow.$repo_path.self::defColor."' repo. ", $indent-0.5, self::defColor, 0);
        if(!isset($this->simulate))
            $repo->git("pull origin master");
        $this->cout("[ OK ]", 0, self::green);
        if($man_failed) return;
        
        $manifest = json_decode(file_get_contents(\zinux\kernel\utilities\fileSystem::resolve_path($repo_man)));
        
        if(!$this->has_arg($repo->getBranches(), "master"))
            throw new \zinux\kernel\exceptions\invalideOperationException("The 'master' branch does not exist!!<br />".self::red."[ Aborting ]");
        if(!isset($manifest->modules) || !count($manifest->modules))
        {
            $this ->cout("According to ".self::yellow.$name.self::defColor."'s manifest file no module exists!", $indent)
                    ->cout("Skipping module updating procedure.", $indent, self::red);
            return;
        }
        if(isset($this->verbose))
            $this->cout("Updating ".self::yellow.$name.self::defColor."'s module repositories.", $indent);
        foreach($manifest->modules as $value)
        {
            $this->update_repo($value->name, $repo_path.DIRECTORY_SEPARATOR.$value->path, $indent+0.5, $value->repo);
        }
        if(isset($this->verbose))
            $this->cout("Updating ".self::yellow.$name.self::defColor."'s module repositories is done.", $indent);
        $indent-=0.5;
        if(isset($this->verbose))
            $this->cout("repository '".self::yellow.$name.self::defColor."' and its all sup-repositories are updated ...", $indent, self::green);
    }
    protected function is_connected()
    {
        $connected = @fsockopen("www.google.com", 80); //website and port
        if ($connected)
        {
            fclose($connected);
            return true;
        }
        return false;
    }
    public function cout($content = "<br />", $tap_index = 0, $color = self::defColor, $auto_break = 1)
    {
        ob_start();
            parent::cout($content, $tap_index, $color, $auto_break);
        echo preg_replace(array("#<br\s*(/)?>#i", "#<(/)?pre>#i"),array(PHP_EOL, ""), ob_get_clean());
        ob_flush();
        return $this;
    }
}
