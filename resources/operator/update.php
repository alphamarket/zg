<?php
namespace zinux\zg\resources\operator;

class update extends baseOperator
{    
    public function update($args)
    {
        $this->restrictArgCount($args, 0);
        $this->cout("Testing PHPGit...");
        $repo = new \zinux\zg\vendor\PHPGit\Repository("zinux");
        \zinux\kernel\utilities\debug::_var($repo);
        \zinux\kernel\utilities\debug::_var($repo->git("checkout master"));
//        \zinux\kernel\utilities\debug::_var($repo);
//        \zinux\kernel\utilities\debug::_var($repo);
        
    }
}
