<?php
namespace zinux\zg\resources\operator;

class status extends baseOperator
{
    public function show($args)
    {
        $this->CheckProject();
        $this->cout("Outputing project status file ...")->cout();
        foreach($this->GetStatus() as $key => $value)
        {
            $this ->cout($key, 1, self::yellow, 0)
                    ->cout(" : ", 0, self::defColor, 0)
                    ->cout($value, 0, self::cyan);
        }
    }
}
