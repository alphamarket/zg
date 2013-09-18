<?php
namespace zinux\zg\resources\operator;

abstract class baseOperator extends \zinux\zg\baseZg
{
    public function Run($opt)
    {
        $s = $this->GetStatus();
        $this->cout("", 1,self::defColor, 0);
        foreach($opt as $value)
        {
            system($value);
            if($s)
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
        
        $this ->cout()
                ->cout("[ DONE ]", 0, self::yellow);
    }
}
