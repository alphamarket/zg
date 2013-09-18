<?php
namespace zinux\zg\resources\operator;

class _new extends baseOperator
{
    public function project($args)
    {
        if(!count($args))
            throw new \zinux\kernel\exceptions\invalideArgumentException("Empty argument passed ...");
        if(count($args)>1)
            throw new \zinux\kernel\exceptions\invalideArgumentException("Undefined argument '{$args[1]}' passed ...");
        
        $pName = $args[0];
        
        $this ->cout("Creating new project '", 0, self::defColor, 0)
                ->cout("$pName", 0, self::yellow, 0)
                ->cout("' ...");
        
        $opt = array(
                "mkdir $pName",
                "cd $pName",
                "cp -rf ".Z_CACHE_ROOT." ./$pName",
                "mv ./$pName/".basename(Z_CACHE_ROOT)." ./$pName/zinux"
        );
        $this->Run($opt);
        $this->CreateStatusFile($pName);
    }
    public function test($args)
    {
        $this->cout(__METHOD__, 0, self::yellow);
    }
    public function hihi($args)
    {
        $this->cout(__METHOD__, 0, self::yellow);
    }
}
