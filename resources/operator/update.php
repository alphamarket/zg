<?php
namespace zinux\zg\resources\operator;

class update extends baseOperator
{    
    public function update($args)
    {
        $this->restrictArgCount($args, 0);
        $this->cout("Testing PHPGit...");
        # check if git exists 
        if(!exec('git 2>/dev/null | wc -l'))
            throw new \zinux\kernel\exceptions\notFoundException("'git' not found in system!<br />To install git http://git-scm.com/downloads");
        $repo = NULL;
        try
        {
            $repo = new \zinux\zg\vendor\PHPGit\Repository(WORK_ROOT."/zinuxa", 1, array('git_executable' => 'git'));
        }
        catch(\Exception $e)
        {
            $this->cout("- The git repository not found in '".WORK_ROOT."/zinux'.",0,self::red);
            $this->cout("+ Trying to download a new repository...", 0, self::green);
            exec("rm -fr ".WORK_ROOT."/zinxua");
            \zinux\zg\vendor\PHPGit\Repository::cloneUrl("https://github.com/dariushha/zinux", "zinux", 1, array('git_executable' => 'git'));
        }
        return;
        \zinux\kernel\utilities\debug::_var($repo, 0);
        \zinux\kernel\utilities\debug::_var($repo->git("checkout master"));
//        \zinux\kernel\utilities\debug::_var($repo);
//        \zinux\kernel\utilities\debug::_var($repo);
        
    }
}
