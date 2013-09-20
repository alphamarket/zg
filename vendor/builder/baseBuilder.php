<?php
namespace zinux\zg\vendor\builder;

abstract class baseBuilder extends \zinux\zg\resources\operator\baseOperator
{
    public function creatVirtualStatusFile($project_name)
    {
        $s = new \zinux\zg\vendor\status;
        $parent = new \zinux\zg\vendor\item(basename(realpath(".")), realpath("."));
        $s->project = new \zinux\zg\vendor\item("project", realpath("./$project_name/"),$parent);
        $s->hs = \zinux\kernel\security\hash::Generate(serialize($s),1,1);
        return $s;
    }
}
