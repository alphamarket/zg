<?php
namespace zinux\zg\resources\operator;

abstract class baseOperator extends \zinux\zg\baseZg
{    
    
    public function __construct($suppress_header_text = 0)
    {
        if(!$suppress_header_text)
            $this->PrintTItleString();
    }
    public function Run($opt, $record_history = 1)
    {
        $s = $this->GetStatus();
        foreach($opt as $value)
        {
            system($value);
            if($record_history && $s && !isset($s->configs->skip_history))
            {
                $h = new \stdClass();
                $h->opt = $value;
                $h->time = date("M-d-Y H:i:s");
                $s->history[] = $h;
            }
        }
        if($s)
            $this->SaveStatus($s);
    }
    
    public function PrintTItleString()
    {
        $this->cout("Zinux Generator by Dariush Hasanpoor [b.g.dariush@gmail.com] 2013", 0, self::yellow);
    }
}
