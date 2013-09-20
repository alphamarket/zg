<?php
namespace zinux\zg\vendor\builder;

abstract class baseBuilder extends \zinux\zg\resources\operator\baseOperator
{
    public function creatVirtualStatusFile($project_path)
    {
        if(!($project_path = \zinux\kernel\utilities\fileSystem::resolve_path($project_path, 1)))
            throw new \zinux\kernel\exceptions\notFoundException("The project path didn't found...");
        $s = new \zinux\zg\vendor\status;
        $parent = new \zinux\zg\vendor\item(dirname($project_path), dirname($project_path));
        $s->project = new \zinux\zg\vendor\item("project", $project_path ,$parent);
        $s->hs = \zinux\kernel\security\hash::Generate(serialize($s),1,1);
        return $s;
    }
}
