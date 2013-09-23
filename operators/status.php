<?php
namespace zinux\zg\operators;
/**
 * zg status handler
 */
class status extends baseOperator
{
    /**
     * zg status show handler
     * @throws \zinux\kernel\exceptions\invalideArgumentException in case of provided depth is invalid
     * @throws \zinux\zg\operators\Exception
     */
    public function show($args)
    {        
        # this opt is valid under project directories
        if(!$this->CheckZG()) return;
        # indicate the phase
        $this->cout("Outputing project status file ...");
        # get status object
        $s = $this->GetStatus();
        # check if parents needs to be showen
        if(isset($s->configs->show_parents) || $this->remove_arg($args, "+p"))
            # flag it
            $this->show_parents = 1;
        # fetch recursive depth from args
        $max_depth = $this->get_pair_arg_value($args, "-d", 1);
        # if no depth provided
        if(!$max_depth)
            # set to default
            $max_depth = 5;
        # the depth should be numeric
        if(!is_numeric($max_depth))
            throw new \zinux\kernel\exceptions\invalideArgumentException("Invalid depth # '$max_depth'.");
        # invoke a parser
        $p = new \zinux\zg\parser\parser($args, new \zinux\zg\command\commandGenerator());
        try
        {
            # hit the recursive print using args provided by parses considering args
            $this->RecursivePrint($p->getOperator($s, 1), 0, 0, $max_depth);
        }
        catch(\Exception $e)
        {
            $matches = array();
            if(preg_match("#Invalid command \'(.*)\' in \'(.*)\'#i", $e->getMessage(), $matches))
                $this ->cout("No data found in '".self::yellow.$matches[2].self::defColor."'")
                        ->cout("Parser broked at '".self::yellow.$matches[1].self::defColor."'");
            else
                throw $e;
        }
    }
    /**
     * prints an status object and its sub-items recursively
     * @param \stdClass $status current status object to process
     * @param int $indent UI indent factor
     * @param int $depth current depth#
     * @param int $max_depth max allowed depth
     */
    public function RecursivePrint($status, $indent = 0, $depth = 0, $max_depth = 5)
    {
        # if max depth reached
        if($depth>$max_depth)
        {
            # indicate it
            $this->cout()->cout("{", $indent)->cout("MAX_DEPTH reached recursive return!", $indent+1, self::hiRed)->cout("}", $indent);
            return;
        }
        # print an json formated output
        $this->cout()->cout("{", $indent);
        # if is iterable 
        if($this->is_iterable($status))
            # print the value
            foreach($status as $key => $value)
            {
                if(strtolower($key)=="parent"  && !isset($this->show_parents)) 
                {
                    $this ->cout($key, $indent+1, self::yellow, 0)
                            ->cout(" : ", 0, self::defColor, 0);
                    $this->cout("{ ", 0,self::defColor, 0)->cout("Due to configurations, parent property skiped!", 0, self::hiRed, 0)->cout(" }");
                    continue;
                }
                $this->cout($key, $indent+1, self::yellow, 0)
                        ->cout(" : ", 0, self::defColor, 0);
                if(!$this->is_iterable($value))
                    $this ->cout($value, 0, self::cyan);
                else
                    $this->RecursivePrint($value, $indent+1, $depth+1, $max_depth);
            }
        # close-up the json layout
        $this->cout("}", $indent);
    }
    /**
     * Recursively walks through an status instance
     * @param \stdClass $status current status object to process
     * @param function $callBack a callback function, which get will invoked in recursion 
     * @throws \zinux\kernel\exceptions\invalideArgumentException if $callback is not callable
     */
    public function RecursiveWalk($status, $callBack, $depth = 0, $max_depth = 5)
    {
        # if max depth reached
        if($depth>$max_depth) return;
        # if callback function is not callable
        if(!is_callable($callBack)) 
            # flag it
            throw new \zinux\kernel\exceptions\invalideArgumentException("The argument is not callable!");
        # if current status is iterable
        if($this->is_iterable($status))
            # foreach sub-items in status object
            foreach($status as $key => $value)
            {
                # we don't want to go backward
                if(strtolower($key)=="parent") continue;
                # it not iterable skip it from calling back on it
                if(!$this->is_iterable($value)) continue;
                # if call back stoped the recursion 
                # return from recursing 
                if(!$callBack($value)) return;
                # do a DFS on current status
                $this->RecursiveWalk($value, $callBack, $depth+1, $max_depth);
            }
    }
    /**
     * zg --version handler
     */
    public function version($args)
    {
        # no args excepted
        $this->restrictArgCount($args,0,0);
        # print the zg && zinux version
        $this->cout();
        $this->cout("Zinux Version: ".ZINUX_BUILD_VERSION);
        $this->cout();
        $this->cout("Zinux Generator version: ".ZG_VERSION);
    }
}
