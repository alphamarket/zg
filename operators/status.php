<?php
namespace zinux\zg\operators;

class status extends baseOperator
{
    public function show($args)
    {        
        if(!$this->CheckZG())
            return;
        
        $this->cout("Outputing project status file ...");
        
        $s = $this->GetStatus();
        
        if(!$this->remove_arg($args, "+h"))
            unset($s->history);
        
        if(isset($s->configs->show_parents) || $this->remove_arg($args, "+p"))
            $this->show_parents = 1;
        
        $max_depth = $this->get_pair_arg_value($args, "-d", 1);
        if(!$max_depth)
            $max_depth = 5;
        if(!is_numeric($max_depth))
            throw new \zinux\kernel\exceptions\invalideArgumentException("Invalid depth # '$max_depth'.");
        
        $p = new \zinux\zg\parser\parser($args, new \zinux\zg\command\commandGenerator());
        try
        {
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
    public function RecursivePrint($status, $indent = 0, $depth = 0, $max_depth = 5)
    {
        if($depth>$max_depth)
        {
            $this->cout()->cout("{", $indent)->cout("MAX_DEPTH reached recursive return!", $indent+1, self::hiRed)->cout("}", $indent);
            return;
        }
        $this->cout()->cout("{", $indent);
        if($this->is_iterable($status))
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
        $this->cout("}", $indent);
    }
    
    public function RecursiveWalk($status, $callBack)
    {
        if(!is_callable($callBack)) 
            throw new \zinux\kernel\exceptions\invalideArgumentException("The argument is not callable!");
        if($this->is_iterable($status))
            foreach($status as $key => $value)
            {
                if(strtolower($key)=="parent") continue;
                if(!$this->is_iterable($value)) continue;
                if(!$callBack($value)) return;
                $this->RecursiveWalk($value, $callBack);
            }
    }
    public function version($args)
    {
        $this->restrictArgCount($args,0,0);
        $this->cout(ZG_VERSION);
    }
}
