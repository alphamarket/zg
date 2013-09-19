<?php
namespace zinux\zg\resources\operator;

abstract class baseOperator extends \zinux\zg\baseZg
{    
    
    public function __construct($suppress_header_text = 0)
    {
        if(!$suppress_header_text)
            $this->PrintTItleString();
    }
    public function Run($opt, $record_history = 1, $new_line = 1)
    {
        $s = $this->GetStatus();
        $this->cout("", 1,self::defColor, 0);
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
            $this->cout("+", 0, self::green, 0);
        }
        if($s)
            $this->SaveStatus($s);
        if($new_line)
            $this->cout();
    }
    
    public function PrintTItleString()
    {
        $this->cout("Zinux Generator by Dariush Hasanpoor [b.g.dariush@gmail.com] 2013", 0, self::yellow);
    }
}
