<?php
namespace zinux\zg\operators;
/**
 * zg update handler
 */
class update extends baseOperator
{    
    /**
     * zg update update handler
     * @throws \zinux\kernel\exceptions\invalideArgumentException in case of invalid arg supplied
     * @throws \zinux\kernel\exceptions\notFoundException in case of 'Git' has not installed in system
     * @throws \zinux\kernel\exceptions\invalideOperationException in case that internet connection has not established
     */
    public function update($args)
    {
        # this opt is valid under project directories
        if(!$this->has_arg($args, "--cache") && !$this->CheckZG()) return;
        # should support --cache to update cache files...!
        # this opt shoud atmost has 4 arg
        $this->restrictArgCount($args, 4, 0);
        # foreach provided args
        while(count($args))
        {
            # fetch current arg
            $arg = array_shift($args);
            # match the fetched arg
            switch($arg)
            {
                # in case of cache arg supplied
                case "--cache":
                    $this->cache_update = 1;
                    break;
                # in case of simulation arg supplied
                case "--simulate":
                    $this->simulate = 1;
                    break;
                # in case of verbose arg supplied
                case "--verbose":
                    $this->verbose = 1;
                    break;
                # in case of all branch arg supplied
                case "--all":
                    if(isset($this->branch_name))
                        throw new \zinux\kernel\exceptions\invalideArgumentException("In-compatible argument, '\$branch_name'  is in-compatible with '--all'.");
                    $this->all_branches = 1;
                    break;
                # fetch a desire branch
                default:
                    if(isset($this->all_branches))
                        throw new \zinux\kernel\exceptions\invalideArgumentException("In-compatible argument, '\$branch_name'  is in-compatible with '--all'.");
                    $this->branch_name = array($arg);
            }
        }
        # the default branch is master
        if(!isset($this->branch_name))
            $this->branch_name = array("master");
        $this->cout("Updating zinux framework from its online repo.");
        $this->cout("Checking Git application.... ",0 ,self::getColor(self::defColor), 0);
        # check if git exists 
        if(!exec('git 2>/dev/null | wc -l'))
            throw new \zinux\kernel\exceptions\notFoundException("'git' not found in system!<br />To install git http://git-scm.com/downloads");
        $this->cout("[ OK ]", 0, self::getColor(self::green));
        $this->cout("Testing your internet connection, please wait.... ", 0, self::getColor(self::defColor), 0);
        # check network 
        if(!isset($this->simulate) && !$this->is_connected())
        {
            $this->cout('[ FAILED ]',0, self::getColor(self::red));
            throw new \zinux\kernel\exceptions\invalideOperationException
                (self::getColor(self::defColor)."You need to have ".self::getColor(self::yellow)."internet connection".self::getColor(self::defColor)." to do this operation!<br />".self::getColor(self::red)."[ Aborting ]");
        }
        $this->cout("[ OK ]",0, self::getColor(self::green));
        $this->cout("Warning: ".self::getColor(self::defColor)."All local changes will be ".self::getColor(self::yellow)."stash".self::getColor(self::defColor)." ...", 0, self::getColor(self::red));
        $zinux_dir = isset($this->cache_update)?Z_CACHE_ROOT:WORK_ROOT."/zinux";
        # update repo recursively
        $this->update_repo("zinux".(isset($this->cache_update)?".cache":""), $zinux_dir);
        $this->cout("Now the ".self::getColor(self::yellow)."zinux framework".self::getColor(self::defColor)." is updated ...");
    }
    /**
     * recursively update given repo
     * @param string $name repo's name
     * @param string $repo_path repo's path
     * @param int $indent UI print indention
     * @param string $clone_uri target repo clone URI
     * @throws \zinux\kernel\exceptions\notFoundException in case of $repo_path not found
     * @throws \zinux\kernel\exceptions\invalideOperationException in case of master branch does not exist
     * @throws \zinux\zg\operators\Exception general failure on updating repo
     */
    protected function update_repo($name, $repo_path, $indent = 0, $clone_uri = "https://github.com/dariushha/zinux")
    {
        # init vars
        $indent+= 0.5;
        $debug_git = 0; 
        $repo_man = "$repo_path/manifest.json";
        $man_failed = 0;
        try
        {
            # opening repo
            $repo = new \zinux\zg\vendors\PHPGit\Repository($repo_path, $debug_git, array('git_executable' => 'git'));
            # if repo manifest does not exist
            if(!\zinux\kernel\utilities\fileSystem::resolve_path($repo_man) && ($man_failed = 1))
                # make a mess!
                throw new \zinux\kernel\exceptions\notFoundException();
        }
        catch(\Exception $e)
        {
            $this->cout("- The git repository/manifest not found in '$repo_path'.", $indent-0.5,self::getColor(self::red));
            $this->cout("+ Trying to download a new '".self::getColor(self::yellow).$name.self::getColor(self::green)."' repository...", $indent-0.5, self::getColor(self::green));
            # if not simulation and repo's manifest exists
            if(!isset($this->simulate) && !($man_failed = 0))
            {
                # try to re-cloning the repo
                exec("rm -fr $repo_path");
                exec("cd ".dirname($repo_path)." && git clone '$clone_uri' '$name' 1>/dev/null 2>&1 ");
            }
            # open up the cloned repo
            $repo = new \zinux\zg\vendors\PHPGit\Repository($repo_path, $debug_git, array('git_executable' => 'git'));
            # if yet repo's manifest does not exist
            if(!\zinux\kernel\utilities\fileSystem::resolve_path($repo_man))
            {
                # indicate it
                $this ->cout(self::getColor(self::red)."Notice: ".self::getColor(self::defColor).
                                    "The ".self::getColor(self::yellow).$name.self::getColor(self::defColor)." git repository updated. but still manifest file '".
                                    self::getColor(self::yellow)."manifest.json".self::getColor(self::defColor)."'", $indent)
                        ->cout("not found at '".self::getColor(self::yellow).$repo_path.self::getColor(self::defColor)."'!", $indent+0.5)
                        ->cout("No recursive updating will happen for repo ".self::getColor(self::yellow).$name.self::getColor(self::defColor)." at '".self::getColor(self::yellow).$repo_path.self::getColor(self::defColor)."'", $indent+0.5);
                # flag repo's manifest as failed
                $man_failed = 1;
            }
            else
                # flag repo's manifest as success
                $man_failed = 0;
        }
        # if we're on a solo-branch updating mode
        # and target branch does not exist
        if(!isset($this->all_branches) && !$this->has_arg($repo->getBranches(), $this->branch_name[0]))
        {
            # indicate it
            $this->cout("No branch named  '".self::getColor(self::yellow).$this->branch_name[0].self::getColor(self::red)."' in '".self::getColor(self::yellow).$name.self::getColor(self::red)."' repository.", $indent, self::getColor(self::red));
            # skip the pull part
            goto __SKIP_PULL;
        }
        # the zinux project and its submodules 
        # should always has master branch
        # if no master branch exists
        if(!$this->has_arg($repo->getBranches(), "master"))
            # well that is not an standard of zinux project
            throw new \zinux\kernel\exceptions\invalideOperationException("The 'master' branch does not exist!!<br />".self::getColor(self::red)."[ Aborting ]");
        # if we are going with all branches
        if(isset($this->all_branches))
            # fetch all branches
            $this->branch_name = preg_replace(array("#origin\/(\w+)#i", "#(\w+->\w+)#i"), array("$1", ""), $repo->getBranches("-r"));
        # normalize the branches 
        \zinux\kernel\utilities\_array::array_normalize($this->branch_name);
        # foreach branch
        foreach($this->branch_name as $branch)
        {
            # if not simulating 
            if(!isset($this->simulate))
            {
                # stash the changes
                $repo->git("stash");
                if(isset($this->verbose))
                    $this->cout("Note: ".self::getColor(self::defColor)."In case of fail-safe any possible changes", $indent-0.5, self::getColor(self::yellow), 1)
                        ->cout("in '".self::getColor(self::yellow).$repo_path." : ".$branch.self::getColor(self::defColor)."' has been stashed!", $indent+0.5);
                # checkout to the branch
                $repo->git("checkout $branch");
            }
            # indicating the phase
            $this->cout("Updating '".self::getColor(self::yellow).$repo_path." : ".$branch.self::getColor(self::defColor)."' repo. ", $indent-0.5, self::getColor(self::defColor), 0);
            # if not simulating
            if(!isset($this->simulate))
                try
                {
                    # do pull on branch
                    $repo->git("pull origin $branch");
                }
                catch(\Exception $e)
                {
                    $this->cout("[ FAILED ]", 0, self::getColor(self::red));
                    throw $e;
                }
            # indicate the success
            $this->cout("[ OK ]", 0, self::getColor(self::green));
        }
        # skip pull phase
__SKIP_PULL:
        $repo->git("checkout master");
        $this->cout("Fetching '".self::getColor(self::yellow).$repo_path.self::getColor(self::defColor)."' repo's tags. ", $indent-0.5, self::getColor(self::defColor), 0);
        # if not simulating
        if(!isset($this->simulate))
            try
            {
                # fetch tags
                $repo->git("git fetch --tags");
            }
            catch(\Exception $e)
            {
                $this->cout("[ FAILED ]", 0, self::getColor(self::red));
                throw $e;
            }
        # indicate the success
        $this->cout("[ OK ]", 0, self::getColor(self::green));
        # if manifest failed no need to proceed
        if($man_failed) return;
        # fetch manifest's data stored in json format
        $manifest = json_decode(file_get_contents(\zinux\kernel\utilities\fileSystem::resolve_path($repo_man)));
        # if manifest failed
        if(!isset($manifest) || !isset($manifest->modules) || !count($manifest->modules))
        {
            # indicate it
            $this ->cout("According to ".self::getColor(self::yellow).$name.self::getColor(self::defColor)."'s manifest file no module exists!", $indent)
                    ->cout("Skipping module updating procedure.", $indent, self::getColor(self::red));
            return;
        }
        if(isset($this->verbose))
            $this->cout("Updating '".self::getColor(self::yellow).$name.self::getColor(self::defColor)."'s module repositories.", $indent);
        # foreach defined sub-modules in manifest
        foreach($manifest->modules as $value)
        {
            # recursively updating the modules
            $this->update_repo($value->name, $repo_path.DIRECTORY_SEPARATOR.$value->path, $indent+0.5, $value->repo);
        }
        if(isset($this->verbose))
            $this->cout("Updating '".self::getColor(self::yellow).$name.self::getColor(self::defColor)."'s module repositories is done.", $indent);
        $indent-=0.5;
        if(isset($this->verbose))
            $this->cout("repository '".self::getColor(self::yellow).$name.self::getColor(self::green)."' and its all sup-repositories are updated ...", $indent, self::getColor(self::green));
    }
    /**
     * checks if internet connected by socketing to www.google.com:80
     * @return boolean TRUE if internet connection is OK, otherwise returns false
     */
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
}
