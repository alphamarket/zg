<?php
namespace zinux\zg\operators;

/**
 * Description of security
 *
 * @author dariush
 */
class security extends baseOperator
{
    
    protected $support_ext = array("*.*");
    
    public function __construct($suppress_header_text = 0)
    {
        parent::__construct($suppress_header_text);
        $crypt_cache_path = ".".PRG_CONF_PATH.".last_crypts/";
        if(!file_exists($crypt_cache_path))
            mkdir($crypt_cache_path, 0755);
        $s = $this->GetStatus();
        $s->project->cryption->meta = new \zinux\zg\vendor\item("cryption", realpath($crypt_cache_path)."/", $s->project);
        $this->SaveStatus($s);
    }
    
    public function rglob($pattern='*', $flags = 0, $path='')
    {
        $paths=  glob($path.'*', GLOB_MARK|GLOB_ONLYDIR|GLOB_NOSORT);
        $files=  glob($path.$pattern, $flags);
        foreach ($paths as $path) { $files=array_merge($files,$this->rglob($pattern, $flags, $path)); }
        return $files;
    }
    protected function enc_head_op($args)
    {
        $this->CheckZG(".", 1);
        $this->restrictArgCount($args, 3,1);
        if(count($args)>1 && !$this->has_arg($args, "-i"))
            throw new \zinux\kernel\exceptions\invalideArgumentException;
        $iter = 1;
        if($this->has_arg($args, "-i"))
            $iter = $this->get_pair_arg_value($args, "-i", 1);
        if($iter<1 || !is_numeric($iter))
            throw new \zinux\kernel\exceptions\invalideArgumentException("The iteration amount should be greater that 1.");
        
        $key = md5($args[0].sha1($args[0]));
        
        return array($iter, \zinux\kernel\security\hash::Generate($key, 1), \zinux\kernel\security\hash::Generate($key, 1, 1));
    }
    public function encrypt($args)
    {
        list($iter, $key, $hash_sum) = $this->enc_head_op($args);
        
        $s = $this->GetStatus();
        $s->project->key_check_sum = $hash_sum;
        $s->project->cryption->collection = array();
        foreach ($this->getFiles() as $file)
        {
            if(!is_writable($file)) 
            {
                $this->cout("> ", 0.5, self::red, 0);
                $this->cout("'$file' is not writable!", 0, self::defColor, 0);
                $this->cout(" [ FAILED ]", 0, self::red);
                continue;
            }
            $s->project->cryption->collection[] =$file;
            $this->cout("> ", 0.5, self::green, 0);
            $this->cout($file, 0, self::defColor, 0);
            $e = new \zinux\kernel\security\encryption(MCRYPT_BlOWFISH, MCRYPT_MODE_CBC);
            $file_cont = file_get_contents($file);
            for($i = 0;$i<$iter;$i++)
            {
                $file_cont = $e->encrypt($file_cont, $key);
            }
            file_put_contents($file, $file_cont);
            $this->cout(" [ OK ]", 0, self::green);
        }
        $s->project->cryption->is_encrypted = 1;
        $this->SaveStatus($s);
    }
    public function decrypt($args)
    {
        list($iter, $key, $hash_sum) = $this->enc_head_op($args);
        $s = $this->GetStatus();
        if(!isset($s->project->cryption) || !isset($s->project->cryption->is_encrypted) || !isset($s->project->cryption->collection))
        {
            $this->cout("[ DANGER CLOSE ]", 0, self::red)
                    ->cout("The project didn't flaged as encrypted!", 0.5, self::yellow)
                    ->cout("It is possible to lose your entire project by decrptying the project ", 0.5, self::yellow)
                    ->cout("that is not previously encrypted by '".self::cyan."zg security encrypt".self::yellow."' command!!!", 0.5, self::yellow)
                    ->cout("If you believe the project did realy encrypted by '".self::cyan."zg security encrypt".self::yellow."'", 0.5, self::yellow)
                    ->cout("command, You may proceed at you own risk!", 0.5, self::yellow)
                    ->cout();
            while(true)
            {
                $txt = strtolower(readline(self::green."  Do you want to preceed?".self::defColor." [y/N] "));
                if($txt == "n")
                    return;
                if($txt != "y") continue;
                break;
            }
            $risky = 1;
        }
        if(!isset($risky))
            if(!isset($s->project->key_check_sum) || $s->project->key_check_sum != $hash_sum)
                throw new \zinux\kernel\exceptions\invalideOperationException("The '".self::cyan."encryption key".self::yellow."' doesn't match with encrypted key!");
        
        $crypt_cache_path = $s->project->cryption->meta->path;
        if(!isset($s->project->cryption->collection))
            $files = $this->getFiles();
        else        
            $files = $s->project->cryption->collection;
        $s->project->cryption->cache = array();
        foreach ($files as $file)
        {
            if(!is_writable($file)) 
            {
                $this->cout("> ", 0.5, self::red, 0);
                $this->cout("'$file' is not writable!", 0, self::defColor, 0);
                $this->cout(" [ FAILED ]", 0, self::red);
                continue;
            }
            $p = $crypt_cache_path.sha1($file);
            $i = 1;
            while(file_exists($p))
                $p.=$i++;
            $p.=".crypt";
            $e = new \zinux\kernel\security\encryption(MCRYPT_BlOWFISH, MCRYPT_MODE_CBC);
            $file_cont = file_get_contents($file);
            file_put_contents($p, $file_cont);
            $this->cout("> ", 0.5, self::green, 0);
            $this->cout($file, 0, self::defColor, 0);
            $s->project->cryption->cache[] = new \zinux\zg\vendor\item($file, realpath($p));
            for($i = 0;$i<$iter;$i++)
            {
                $file_cont = $e->decrypt($file_cont, $key);
            }
            file_put_contents($file, $file_cont);
            $this->cout(" [ OK ]", 0, self::green);
        }
        exec("chmod 775 {$s->project->path} -R >/dev/null 2>&1");
        exec("chmod 777 {$s->project->path}");
        unset($s->project->cryption->is_encrypted);
        $this->SaveStatus($s);
    }
    public function cache($args)
    {
        if(!$this->CheckZG()) return;
        $this->restrictArgCount($args, 1, 1);
        $s = $this->GetStatus();
        if(!isset($s->project->cryption) || !isset($s->project->cryption->meta))
            throw new \zinux\kernel\exceptions\invalideOperationException("No cryption data found!");
        switch(true)
        {
            case $this->has_arg($args, "--clear"):
                if(!isset($s->project->cryption->meta->path))
                    throw new \zinux\kernel\exceptions\invalideOperationException("No metadata found on cryption data!");
                exec("rm -rf '{$s->project->cryption->meta->path}'");
                $this ->cout("Cryption cached data cleared ", 0 ,self::defColor, 0)
                        ->cout("successfully.", 0, self::green);
                unset($s->project->cryption);
                break;
            case $this->has_arg($args, "--reset"):
                if(!isset($s->project->cryption->cache))
                    throw new \zinux\kernel\exceptions\invalideOperationException("No cache data found!");
                
                foreach($s->project->cryption->cache as $item)
                {
                    $file = $item->name;
                    if(!is_writable($file)) 
                    {
                        $this->cout("> ", 0.5, self::red, 0);
                        $this->cout("'$file' is not writable!", 0, self::defColor, 0);
                        $this->cout(" [ FAILED ]", 0, self::red);
                        continue;
                    }
                    $this->cout("> ", 0.5, self::green, 0);
                    $this->cout($file, 0, self::defColor, 0);
                    file_put_contents($file, file_get_contents($item->path));
                    $this->cout(" [ OK ]", 0, self::green);
                }
                
                $this ->cout()
                        ->cout("All encrypted files reseted ", 0, self::defColor, 0)
                        ->cout("successfully", 0, self::green, 0)
                        ->cout("...");
                $s->project->cryption->is_encrypted = 1;
                break;
            default:
                throw new \zinux\kernel\exceptions\invalideArgumentException;
        }
        $this->SaveStatus($s);
    }
    protected function getFiles()
    {
        $m = preg_grep("/^zinux\/.*/i",($this->rglob("*")),PREG_GREP_INVERT);
        foreach($m as $key=> $value)
        {
            if(is_dir($value))
                unset($m[$key]);
        }
        return $m;
    }
}
