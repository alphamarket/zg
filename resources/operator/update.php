<?php
namespace zinux\zg\resources\operator;

class update extends baseOperator
{    
    public function update($args)
    {
        $this->restrictArgCount($args, 0);
    }
}
