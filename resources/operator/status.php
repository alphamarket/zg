<?php
namespace zinux\zg\resources\operator;

class status extends baseOperator
{
    public function show($args)
    {
        if(!$this->CheckZG())
            return;
        $this->cout("Outputing project status file ...");
        $this->RecursivePrint($this->GetStatus());
    }
    private function RecursivePrint($status, $indent = 0, $depth = 0, $max_depth = 5)
    {
        if($depth>$max_depth)
        {
            $this->cout()->cout("{", $indent)->cout("MAX_DEPTH reached recursive return!", $indent+1, self::hiRed)->cout("}", $indent);
            return;
        }
        $this->cout()->cout("{", $indent);
        foreach($status as $key => $value)
        {
            $this->cout($key, $indent+1, self::yellow, 0)
                    ->cout(" : ", 0, self::defColor, 0);
            if(!$this->is_iterable($value))
                $this ->cout($value, 0, self::cyan);
            else
                $this->RecursivePrint($value, $indent+1, $depth+1);
        }
        $this->cout("}", $indent);
    }
}
