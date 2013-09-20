<?php
namespace zinux\zg\resources\operator;

class status extends baseOperator
{
    public function show($args)
    {
        $this->restrictArgCount($args, 1, 0);
        
        if(!$this->CheckZG())
            return;
        
        $this->cout("Outputing project status file ...");
        
        $s = $this->GetStatus();
        
        if(!$this->has_arg($args, "+h"))
            unset($s->history);
        if(isset($s->configs->show_parents))
            $this->show_parents = 1;
        
        $this->RecursivePrint($s);
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
                    $this->RecursivePrint($value, $indent+1, $depth+1);
            }
        $this->cout("}", $indent);
    }
    public function version()
    {
        $this->cout(ZG_VERSION);
    }
}
