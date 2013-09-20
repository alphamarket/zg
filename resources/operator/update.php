<?php
namespace zinux\zg\resources\operator;

class update extends baseOperator
{    
    public function update($args)
    {
        $this->restrictArgCount($args, 0);
        $this->cout("Updating your project's zinux framework from its online repo.");
        $this->cout("Testing your network, please wait....");
        # check network 
        if(!$this->is_connected())
        {
            throw new \zinux\kernel\exceptions\invalideOperationException
                (self::defColor."You need to have ".self::yellow."network connection".self::defColor." to do this operation!<br />".self::red."[ Aborting ]");
        }
        $zinux_dir = WORK_ROOT."/zinux";
        # check if git exists 
        if(!exec('git 2>/dev/null | wc -l'))
            throw new \zinux\kernel\exceptions\notFoundException("'git' not found in system!<br />To install git http://git-scm.com/downloads");
        
        $this->update_repo("zinux", $zinux_dir);
        $this->cout("Now your project's zinux framework is updated ...");
        
    }
    protected function update_repo($name, $repo_path, $indent = 0)
    {
        if(!($path = \zinux\kernel\utilities\fileSystem::resolve_path($repo_path)))
            throw new \zinux\kernel\exceptions\notFoundException("'$repo_path' not found!");
        $this->cout("Updating '".self::yellow.$repo_path.self::defColor."' repo.", $indent);
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
            $this->cout("- The git repository/manifest not found in '$repo_path", $indent,self::red);
            $this->cout("+ Trying to download a new repository...", $indent, self::green);
            #exec("rm -fr $zinux_dir");
            #\zinux\zg\vendor\PHPGit\Repository::cloneUrl("https://github.com/dariushha/zinux", "zinux", 1, array('git_executable' => 'git'));
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
        $repo->git("checkout master");
        #echo $repo->git("pull origin master");
        if($man_failed) return;
        
        $manifest = json_decode(file_get_contents(\zinux\kernel\utilities\fileSystem::resolve_path($repo_man)));
        
        if(!$this->has_arg($repo->getBranches(), "master"))
            throw new \zinux\kernel\exceptions\invalideOperationException("The 'master' branch does not exist!!<br />".self::red."[ Aborting ]");
        
        if(!isset($manifest->dependencies))
        {
            $this ->cout("According to ".self::yellow.$name.self::defColor."'s manifest file no dependency exists!", $indent)
                    ->cout("Skipping dependency updating procedure.", $indent, self::yellow);
            return;
        }
        if(isset($this->verbose))    
        $this->cout("Updating $name depenency repositories.", $indent, self::yellow);
        foreach($manifest->dependencies as $value)
        {
            $this->update_repo($value->name, $repo_path.DIRECTORY_SEPARATOR.$value->path, $indent+0.5);
        }
        if(isset($this->verbose))
            $this->cout("Updating ".self::yellow.$name.self::defColor."'s depenency repositories is done.", $indent);
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
    
}
