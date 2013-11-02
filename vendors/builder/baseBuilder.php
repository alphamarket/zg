<?php
namespace zinux\zg\vendors\builder;

/**
 * base class for \zinux\zg\operators\build class
 */
abstract class baseBuilder extends \zinux\zg\operators\baseOperator
{
    /**
     * Virtual zg status container
     * @var item
     */
    protected $s;
    /**
     * holds log items during building process
     * @var array()
     */
    protected $log; 
    /**
     * holds items that has been processed successfully during build process
     * @var array()
     */
    protected $processed; 
    /**
     * Creates a virtual status object
     * @param string $project_path project path
     * @return \zinux\zg\vendors\status created status object 
     * @throws \zinux\kernel\exceptions\notFoundException in case of project didn't found
     */
    public function creatVirtualStatusFile($project_path)
    {
        # indicate a step
        $this->step_show();
        # in case of no project found
        if(!($project_path = \zinux\kernel\utilities\fileSystem::resolve_path($project_path, 1)))
            throw new \zinux\kernel\exceptions\notFoundException("The project path didn't found...");
        # indicate a step
        $this->step_show();
        # create a status object
        $s = new \zinux\zg\vendors\status;
        # set project parent item
        $parent = new \zinux\zg\vendors\item(dirname($project_path), dirname($project_path));
        # init status project item
        $s->project = new \zinux\zg\vendors\item("project", $project_path ,$parent);
        # indicate a step
        $this->step_show();
        # create the hash sum
        $s->hs = \zinux\kernel\security\hash::Generate(serialize($s),1,1);
        # indicate a step
        $this->step_show();
        # return created status object
        return $s;
    }
    /**
     * fetched appliction files and add them into status object
     */
    protected function fetchApplication()
    {
        # indicate a step
        $this->step_show();
        # if no application directory supplied
        if(!($this->app = \zinux\kernel\utilities\fileSystem::resolve_path($this->app)))
        {
            # log it
            $this->step_show(1, "x", self::getColor(self::hiRed));
            $this->log[] = new \zinux\zg\vendors\item("Application directory not found","Skipping processing application directory.");
            # return
            return;
        }
        # indicate a step
        $this->step_show();
        # foreach file in application path
        foreach(glob("{$this->app}/*") as $file)
        {
            # if no file continue with others
            if(!is_file($file)) continue;
            # fetch the file's name
            $name = basename($file, ".php");
            # fetch name's profix in standard 'camelFormat'
            $sp = preg_split("/((?<=[a-z])(?=[A-Z])|(?=[A-Z][a-z]))/",$name);
            # consider last part of name in 'camelFormat' as its type
            $type = strtolower(array_pop($sp));
            # create new app item
            $app = new \zinux\zg\vendors\item($name, $file, $this->s->project);
            # save file as fetched type and name under project item
            $this->s->project->{$type}[strtolower($name)] = $app;
            # flag it as proccessed
            $this->processed[] = $app;
            # indicate a step
            $this->step_show();
        }
        # indicate a step
        $this->step_show();
    }
    /**
     * fetched modules folders and add them into status object
     */
    protected function fetchModules()
    {
        # create new empty module collection
        $this->s->modules->collection = array();
        # indicate a step
        $this->step_show();
        # foreach directory under module dir
        foreach(glob("{$this->modules}/*", GLOB_ONLYDIR) as $module)
        {
            # if name matches with standard module name
            if(preg_match("#\w+module$#i", $module))
            {
                # indicate a step
                $this->step_show();
                # fetch the module name
                $name = basename($module);
                # create new module item
                $module = new \zinux\zg\vendors\item("{$name}", "{$this->s->modules->meta->path}/{$name}", $this->s->modules->meta);
                # add it to module collection
                $this->s->modules->collection[strtolower($module->name)] = $module;
                # check for module's bootstrap
                $bs = preg_replace("#module$#i","Bootstrap.php", $name);
                # if module's bootstrap exists
                if(($bootstrap = \zinux\kernel\utilities\fileSystem::resolve_path($module->path.DIRECTORY_SEPARATOR.$bs)))
                {
                    # add the module's bootstrap to status object
                    $moduleBootstrap = new \zinux\zg\vendors\item(basename($bs, ".php"), $bootstrap, $module);
                    $this->s->modules->collection[strtolower($module->name)]->bootstrap = $moduleBootstrap;
                    # flag it as processed
                    $this->processed[] = $moduleBootstrap;
                    # indicate a step
                    $this->step_show();
                }
                # flag it as processed
                $this->processed[] = $module;
                # indicate a step
                $this->step_show();
            }
            else
            {
                # if module's name does not match with its standard name
                $this->step_show(1, "x", self::getColor(self::hiRed));
                # flag it as error
                $this->log[] = new \zinux\zg\vendors\item("Folder '$module' skipped", "Didn't match with standrad module folder pattern.");
            }
        }
        # indicate a step
        $this->step_show();
    }
    /**
     * fetched controller files and add them into status object
     */
    protected function fetchController()
    {
        # indicate a step
        $this->step_show();
        # foreach founded modules 
        foreach($this->s->modules->collection as $name => $module)
        {
            # if it does not contains controller directory
            if(!($cp = \zinux\kernel\utilities\fileSystem::resolve_path("{$module->path}/controllers")))
            {
                $this->step_show(1, "x", self::getColor(self::hiRed));
                # flag it as error
                $this->log[] = new \zinux\zg\vendors\item("Controllers folder not found at '{$module->path}'", "");
                # continue with others
                continue;
            }
            # indicate a step
            $this->step_show();
            # foreach files in module's controller directory
            foreach(glob("$cp/*") as $file)
            {
                # fetch the name
                $name = basename($file, ".php");
                # if it's a file and macthes with standard controller name
                if(is_file($file) && preg_match("#\w+controller.php$#i", $file))
                {
                    # indicate a step
                    $this->step_show();
                    # check file's syntax
                    $this->check_php_syntax($file);
                    # fetch the expected class name
                    $class = $this->convert_to_relative_path($file, $this->root, $this->s)."\\$name";
                    # if this is no readable or failed to require it
                    if(!is_readable($file) || !($this->require_file($file)))
                    {  
                        $this->step_show(1, "x", self::getColor(self::hiRed));
                        # flag it as error
                        $this->log[] = new \zinux\zg\vendors\item("File '$file' skipped", "Couldn't open the file.");
                        # continue with others
                        continue;
                    }
                    # indicate a step
                    $this->step_show();
                    # check if controller exists with expecte name
                    if(!class_exists($class))
                    {
                        $this->step_show(1, "x", self::getColor(self::hiRed));
                        # if not flag it as error
                        $this->log[] = new \zinux\zg\vendors\item("Controller '$file' found, but relative class '$class' not found","");  
                        # continue with others
                        continue;
                    }
                    # indicate a step
                    $this->step_show(); 
                    # create new controller item
                    $controller = new \zinux\zg\vendors\Item($name, $file, $module);
                    # add it to module's controller collection
                    $this->s->modules->collection[strtolower($module->name)]->controller[strtolower($controller->name)] = $controller;
                    # flag it as success
                    $this->processed[] = $controller;
                    # indicate a step
                    $this->step_show();
                }
                # if its a file but didn't match with controller std name
                elseif(is_file($file))
                {
                    $this->step_show(1, "x", self::getColor(self::hiRed));
                    # flag it as error
                    $this->log[] = new \zinux\zg\vendors\item("File '$file' skipped","Didn't match with standard controller file pattern.");
                }
            }
            # indicate a step
            $this->step_show();
        }
        # indicate a step
        $this->step_show();
    }
    /**
     * fetched action methods and add them into status object
     */
    protected function fetchAction()
    {
        # indicate a step
        $this->step_show();
        # foreach founded modules 
        foreach($this->s->modules->collection as $module)
        {
            # if no controller setted for current module
            if(!isset($module->controller))
            {
                $this->step_show(1, "x", self::getColor(self::hiRed));
                # flag it as error
                $this->log[] = new \zinux\zg\vendors\item("No controller found in {$module->name}", "");
                # continue with others
                continue;
            }
            # indicate a step
            $this->step_show();
            # foreach founded controller in current module
            foreach($module->controller  as $cname => $controller)
            {
                # indicate a step
                $this->step_show();
                # fetch expected controller name
                $class = $this->convert_to_relative_path($controller->path, $this->root, $this->s)."\\$cname";
                # this if should never reach TRUE, cause the 
                if(!class_exists($class))
                    $this->require_file($controller->path);
                # foreach class' method
                /* @var $method string */
                foreach(get_class_methods($class) as $method)
                {
                    # indicate a step
                    $this->step_show();
                    # if it matches with std action name
                    if(preg_match("#\w+action#i", $method))
                    {
                        # indicate a step
                        $this->step_show();
                        # if it is not callable
                        if(!is_callable(array($class, $method)))
                        {
                            $this->step_show(1, "x", self::getColor(self::hiRed));
                            # flag it as error
                            $this->log[] = new \zinux\zg\vendors\item("Method '$method' is not callable", "In class '$class' method '$method' is not callable!");
                            # continue with others
                            continue;
                        }
                        # create new action item
                        $action = new \zinux\zg\vendors\item($method, $method, $controller);
                        # add it into module's controller's actions collection
                        $this->s->modules->
                            collection[strtolower($module->name)]->
                            controller[strtolower($controller->name)]->
                            action[strtolower($action->name)] = $action;
                        # flag it as processed
                        $this->processed[] = $action;
                        # indicate a step
                        $this->step_show();
                    }
                    # indicate a step
                    $this->step_show();
                }
                # indicate a step
                $this->step_show();
            }
            # indicate a step
            $this->step_show();
        }
        # indicate a step
        $this->step_show();
    }
    /**
     * fetched model files and add them into status object
     */
    public function fetchModels()
    {
        # indicate a step
        $this->step_show();
        # foreach founded modules 
        foreach($this->s->modules->collection as $name => $module)
        {
            # if no model directory exists
            if(!($mp = \zinux\kernel\utilities\fileSystem::resolve_path($module->path."/models")))
            {
                $this->step_show(1, "x", self::getColor(self::hiRed));
                # flag it as error
                $this->log[] = new \zinux\zg\vendors\item("Models folder not found at '{$module->path}'", "");
                # continue with others
                continue;
            }
            # indicate a step
            $this->step_show();
            # foreach file in model directory
            foreach(glob("$mp/*") as $file)
            {
                # indicate a step
                $this->step_show();
                # fetch model's name
                $name = basename($file, ".php");
                # if it is a file and matches with std model name
                if(is_file($file) && preg_match("#\w+\b(.php)\b$#i", $file))
                {
                    # indicate a step
                    $this->step_show();
                    # check file's syntax
                    $this->check_php_syntax($file);
                    # fetch expected class name
                    $class = $this->convert_to_relative_path($file, $this->root, $this->s)."\\$name";
                    # if this is no readable or failed to require it
                    if(!is_readable($file) || !($this->require_file($file)))
                    {
                        $this->step_show(1, "x", self::getColor(self::hiRed));
                        # flag it as error
                        $this->log[] = new \zinux\zg\vendors\item("File '$file' skipped", "Couldn't open the file.");
                        # continue with others
                        continue;
                    }
                    # indicate a step
                    $this->step_show();
                    # if class with expected name does not exist
                    if(!class_exists($class))
                    {
                        $this->step_show(1, "x", self::getColor(self::hiRed));
                        # flag it as error
                        $this->log[] = new \zinux\zg\vendors\item("Model '$file' found, but relative class '$class' not found","");
                        # contiue with others
                        continue;
                    }
                    # indicate a step
                    $this->step_show();
                    # create a model item
                    $model = new \zinux\zg\vendors\Item($name, $file, $module);
                    # add it to module's collection
                    $this->s->modules->collection[strtolower($module->name)]->model[strtolower($model->name)] = $model;
                    # flag it as success
                    $this->processed[] = $model;
                    # indicate a step
                    $this->step_show();
                }
                # if is a file and did not match with std model folder name
                elseif(is_file($file))
                {
                    $this->step_show(1, "x", self::getColor(self::hiRed));
                    # flag it as error
                    $this->log[] = new \zinux\zg\vendors\item("File '$file' skipped","Didn't match with standard controller file pattern.");
                }
                # indicate a step
                $this->step_show();
            }
            # indicate a step
            $this->step_show();
        }
        # indicate a step
        $this->step_show();
    }
    /**
     * fetched helper files and add them into status object
     */
    public function fetchHelpers()
    {
        # indicate a step
        $this->step_show();
        # foreach founded modules 
        foreach($this->s->modules->collection as $name => $module)
        {
            # indicate a step
            $this->step_show();
            # if helpers folder does not exist
            if(!($hp = \zinux\kernel\utilities\fileSystem::resolve_path($module->path."/views/helper")))
            {
                $this->step_show(1, "x", self::getColor(self::hiRed));
                # flag it as error
                $this->log[] = new \zinux\zg\vendors\item("Helpers folder not found at '{$module->path}/views/helper'", "");
                # continue with others
                continue;
            }
            # indicate a step
            $this->step_show();
            # foreach file in helper path
            foreach(glob("$hp/*") as $file)
            {
                # indicate a step
                $this->step_show();
                # fetch helper name
                $name = basename($file, ".php");
                # if it is a file and matches with std helper name
                if(is_file($file) && preg_match("#\w+\b(.php)\b$#i", $file))
                {
                    # indicate a step
                    $this->step_show();
                    # check file's syntax
                    $this->check_php_syntax($file);
                    # if not readable and cannot require it 
                    if(!is_readable($file) || !($this->require_file($file)))
                    {
                        $this->step_show(1, "x", self::getColor(self::hiRed));
                        # flag it as error
                        $this->log[] = new \zinux\zg\vendors\item("File '$file' skipped", "Couldn't open the file.");
                        # continue with others
                        continue;
                    }
                    # indicate a step
                    $this->step_show();
                    # create helper item
                    $helper = new \zinux\zg\vendors\Item($name, $file, $module);
                    # add it to module's helper collection
                    $this->s->modules->collection[strtolower($module->name)]->helper[strtolower($helper->name)] = $helper;
                    # flag it as success
                    $this->processed[] = $helper;
                    # indicate a step
                    $this->step_show();
                }
                # if it is a file and didn't match with std helper name
                elseif(is_file($file))
                {
                    $this->step_show(1, "x", self::getColor(self::hiRed));
                    # flag it as error
                    $this->log[] = new \zinux\zg\vendors\item("File '$file' skipped","Didn't match with standard controller file pattern.");
                }
                # indicate a step
                $this->step_show();
            }
            # indicate a step
            $this->step_show();
        }
        # indicate a step
        $this->step_show();
    }
    /**
     * fetched layout files and add them into status object
     */
    public function fetchLayouts()
    {
        # indicate a step
        $this->step_show();
        # foreach founded modules 
        foreach($this->s->modules->collection as $name => $module)
        {
            # indicate a step
            $this->step_show();
            # if not layout path exists
            if(!($lp = \zinux\kernel\utilities\fileSystem::resolve_path($module->path."/views/layout")))
            {
                $this->step_show(1, "x", self::getColor(self::hiRed));
                # flag it as error
                $this->log[] = new \zinux\zg\vendors\item("Layouts folder not found at '{$module->path}/views/layout'", "");
                # continue with others
                continue;
            }
            # indicate a step
            $this->step_show();
            # foreach file in layout path
            foreach(glob("$lp/*") as $file)
            {
                # indicate a step
                $this->step_show();
                # fetch file's name
                $name = basename($file, ".phtml");
                # if it is a file and matches with std layout file's name
                if(is_file($file) && preg_match("#\w+layout.phtml$#i", $file))
                {
                    # indicate a step
                    $this->step_show();
                    # check for syntax error
                    $this->check_php_syntax($file);
                    # if its is no readable
                    if(!is_readable($file))
                    {
                        $this->step_show(1, "x", self::getColor(self::hiRed));
                        # flag it as error
                        $this->log[] = new \zinux\zg\vendors\item("File '$file' skipped", "Couldn't open the file.");
                        # continue with others
                        continue;
                    }   
                    # indicate a step
                    $this->step_show();
                    # create a layout item
                    $layout = new \zinux\zg\vendors\Item($name, $file, $module);
                    # add it into module's laytout collection
                    $this->s->modules->collection[strtolower($module->name)]->layout[strtolower($layout->name)] = $layout;
                    # flag it as processed
                    $this->processed[] = $layout;
                    # indicate a step
                    $this->step_show();
                }
                # if it is a file and did not matched with std layout pattern
                elseif(is_file($file))
                {
                    $this->step_show(1, "x", self::getColor(self::hiRed));
                    # flag it as error
                    $this->log[] = new \zinux\zg\vendors\item("File '$file' skipped","Didn't match with standard layout file pattern.");
                }
                # indicate a step
                $this->step_show();
            }
            # indicate a step
            $this->step_show();
        }
    }
    /**
     * fetched views files and add them into status object
     */
    protected function fetchViewes()
    {
        # indicate a step
        $this->step_show();
        # foreach founded modules 
        foreach($this->s->modules->collection as $name => $module)
        {
            # if not controller exists in current module
            if(!isset($module->controller))
            {
                $this->step_show(1, "x", self::getColor(self::hiRed));
                # flag it as error
                $this->log[] = new \zinux\zg\vendors\item("No controller found in {$module->name}", "");
                # continue with others
                continue;
            }
            # foreach founded controller in this module
            foreach($module->controller  as $cname => $controller)
            {
                # indicate a step
                $this->step_show();
                # fetch controller raw-name
                $name = preg_replace("#controller#i", "", $cname);
                # if views' folder does not exist in current controller
                if(!($vp = \zinux\kernel\utilities\fileSystem::resolve_path($module->path."/views/view/$name")))
                {
                    $this->step_show(1, "x", self::getColor(self::hiRed));
                    # flag it as error
                    $this->log[] = new \zinux\zg\vendors\item("Views folder not found at '{$module->path}/views/view/$name'", "");
                    # continue with others
                    continue;
                }
                # indicate a step
                $this->step_show();
                # foreach files in views' folder
                foreach(glob("$vp/*") as $file)
                {
                    # indicate a step
                    $this->step_show();
                    # fetch file's name
                    $name = basename($file, ".phtml");
                    # if it is a file and matches with std view name
                    if(is_file($file) && preg_match("#\w+view.phtml$#i", $file))
                    {
                        # indicate a step
                        $this->step_show();
                        # check file's syntax
                        $this->check_php_syntax($file);
                        # if not readble
                        if(!is_readable($file))
                        {
                            $this->step_show(1, "x", self::getColor(self::hiRed));
                            # flag it as error
                            $this->log[] = new \zinux\zg\vendors\item("File '$file' skipped", "Couldn't open the file.");
                            # continue with others
                            continue;
                        }
                        # indicate a step
                        $this->step_show();
                        # create new view item
                        $view = new \zinux\zg\vendors\Item($name, $file, $controller);
                        # add it into controller's view collection
                        $this->s->modules->collection[strtolower($module->name)]->controller[strtolower($controller->name)]->view[strtolower($view->name)] = $view;
                        # flag it as processed
                        $this->processed[] = $view;
                        # indicate a step
                        $this->step_show();
                    }
                    # if it is a file and does not match with std view name
                    elseif(is_file($file))
                    {
                        $this->step_show(1, "x", self::getColor(self::hiRed));
                        # flag it as error
                        $this->log[] = new \zinux\zg\vendors\item("File '$file' skipped","Didn't match with standard view file pattern.");
                    }
                    # indicate a step
                    $this->step_show();
                }
                # indicate a step
                $this->step_show();
            }
            # indicate a step
            $this->step_show();
        }
        # indicate a step
        $this->step_show();
    }
    /**
     * Saves logged and proccessed items into a cache file
     */
    public function saveLogs()
    {
        for($index = 0; $index<count($this->log); $index++)
        {
            for($index1 = $index+1; $index1<count($this->log); $index1++)
            {
                if(strtolower($this->log[$index]->name) == strtolower($this->log[$index1]->name))
                    unset($this->log[$index1]); 
            }
        }
        $fc = new \zinux\kernel\caching\fileCache("ZG_BUILD_COMMAND_LOG");
        $fc->setCachePath($this->s->project->path."/.zg/cache");
        $fc->save("log", $this->log);
        $fc->save("processed", $this->processed);
        $this ->cout()
                ->cout("Statistical reports:", 1)
                ->cout("Logged events# ".self::getColor(self::yellow).count($this->log).self::getColor(self::defColor).".", 2)
                ->cout("Processed items# ".self::getColor(self::yellow).count($this->processed).self::getColor(self::defColor).".", 2)
                ->cout()
                ->cout("Use '".self::getColor(self::yellow)."zg build log".self::getColor(self::defColor)."' to print logged info.", 1);
    }
    /**
     * UI step show handler
     */
    protected function step_show($step_cout = 1, $char = ".", $color = self::hiGreen)
    {
        static $count = 0;
        while($step_cout--)
        {
            if(!($count++%55))
                $this->cout()->cout("",1,self::getColor(self::defColor),0);
            
            $this->cout($char,0,$color,0);
        }
    }
}
