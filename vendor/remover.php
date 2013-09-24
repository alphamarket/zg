<?php
namespace zinux\zg\vendor;
/**
 * Zinux item remotion handler
 */
class remover extends \zinux\zg\operators\baseOperator
{
    /**
     * ctor a new remover
     */
    public function __construct()
    {
        parent::__construct(1);
    }
    /**
     * removes items from file system
     * @param \zinux\zg\vendor\item $item target item to remove
     * @param boolean $rebuild should re-build the project after remotion
     * @param string $projectDir project directory
     */
    public function removeFS(item $item, $rebuild = 1,$projectDir = ".")
    {
        # this opt is valid under project directories
        $this->CheckZG($projectDir,1);
        # get status object
        $s = $this->GetStatus($projectDir);
        # remove the item from file system 
        exec("rm -fr '{$item->path}'");
        # indicate the success
        $this->cout("- {$item->path}", 0.5, self::red);
        # if no rebuid? return
        if(!$rebuild) return;
        # invoke a rebuilder
        $b = new \zinux\zg\operators\build(1, 1);
        # rebuild the config file
        $b->build(array('-p', $s->project->path, "-m", $s->items->meta->name));
    } 
   /**
    * removes an action from its parent
    * @param \zinux\zg\vendor\item $action target action to remove
    * @param string $projectDir project directory
    */
    public function removeAction(item $action,$projectDir = ".")
    {
        # this opt is valid under project directories
        $this->CheckZG($projectDir,1);
        # invoke an action remover
        new \zinux\zg\vendor\removers\removeAction($action, $projectDir);
    }
}