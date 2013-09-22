<?php
namespace zinux\zg\vendor;
/**
 * Description of removemoduleBootstrap
 *
 * @author dariush
 */
class remover extends \zinux\zg\operators\baseOperator
{
    public function __construct()
    {
        parent::__construct(1);
    }
    public function removeFS(item $module , $rebuild = 1,$projectDir = ".")
    {
        $this->CheckZG($projectDir,1);
        $s = $this->GetStatus($projectDir);
        exec("rm -fr '{$module->path}'");
        $this->cout("- {$module->path}", 0.5, self::red);
        if(!$rebuild) return;
        $b = new \zinux\zg\operators\build(1);
        $b->build(array('-p', $s->project->path, "-m", $s->modules->meta->name));
    } 
   
    public function removeAction(item $action,$projectDir = ".")
    {
        $this->CheckZG($projectDir,1);
        return new \zinux\zg\vendor\removers\removeAction($action, $projectDir);
    }
}

?>
