<?php
namespace zinux\zg\operators;
/**
 * zg security handler
 */
class security extends baseOperator
{
    /**
     * ctor a new security instance
     */
    public function __construct($suppress_header_text = 0)
    {
        parent::__construct($suppress_header_text);
        $crypt_cache_path = ".".PRG_CONF_PATH."/.last_crypts/";
        if(!file_exists($crypt_cache_path))
            mkdir($crypt_cache_path, 0755);
        $s = $this->GetStatus();
        if(!isset($s->project->cryption->meta ))
            $s->project->cryption->meta = new \zinux\zg\vendors\item("cryption", realpath($crypt_cache_path)."/", $s->project);
        $this->SaveStatus($s);
    }
    /**
     * A generat operations for cryption security method
     * @param type $args
     * @return array() An array of ($iteration#, $iteration_hash_sum, $crypt_key, $crypt_key_hash_sum)
     * @throws \zinux\kernel\exceptions\invalideArgumentException in case of invalid arg supplied
     */
    protected function enc_head_op($args)
    {
        # this opt is valid under project directories
        $this->CheckZG(".", 1);
        # this opt shoud at least has 1 arg and atmost has 3 arg
        $this->restrictArgCount($args, 3,1);
        # if there is more that 1 arg it should be -i and its pair value
        if(count($args)>1 && !$this->has_arg($args, "-i"))
            throw new \zinux\kernel\exceptions\invalideArgumentException;
        # set default iter# to 1
        $iter = 1;
        # if iter# manually specified 
        if($this->has_arg($args, "-i"))
            # update the iter#
            $iter = $this->get_pair_arg_value($args, "-i", 1);
        # the iter# should be a numeric
        if($iter<1 || !is_numeric($iter))
            throw new \zinux\kernel\exceptions\invalideArgumentException("The iteration amount should be greater that 1.");
        # generate a $key based on client key
        $key = md5($args[0].sha1($args[0]));
        # return the cryption collection
        return array($iter, \zinux\kernel\security\hash::Generate($iter), \zinux\kernel\security\hash::Generate($key), \zinux\kernel\security\hash::Generate($key, 1, 1));
    }
    /**
     * zg security encrypt handler
     */
    public function encrypt($args)
    {
        # indicate the phase
        $this->cout("Initiating encryption operations ...<br />");
        # fetch data from args
        list($iter, $iter_sum, $key, $hash_sum) = $this->enc_head_op($args);
        # get status object
        $s = $this->GetStatus();
        # if the project is not encrypted or no files stored in encryption collection
        if(isset($s->project->cryption) && isset($s->project->cryption->meta->is_encrypted))
        {
            # indicate the warning
            $this->cout("[ DANGER CLOSE ]", 0, self::red)
                    ->cout("The project already flaged as encrypted!", 0.5, self::yellow)
                    ->cout();
            while(true)
            {
                # check if user wants to continue or not
                $txt = strtolower(readline(self::green."  Do you really want to encrypt it again?".self::defColor." [y/N] "));
                if($txt == "n")
                    return;
                if($txt != "y") continue;
                break;
            }
        }
        # init a new cryption key (project->cryption has already init in ctor)
        $s->project->cryption->meta->key_check_sum = $hash_sum;
        $s->project->cryption->meta->iter_check_sum = $iter_sum;
        $s->project->cryption->collection = array();
        # for each files in project
        foreach ($this->getFiles() as $file)
        {
            # if it is not readable
            if(!is_writable($file)) 
            {
                # indicate it
                $this->cout("> ", 0.5, self::red, 0);
                $this->cout("'".self::cyan.$file.self::defColor."' is not writable!", 0, self::defColor, 0);
                $this->cout(" [ FAILED ]", 0, self::red);
                # continue with others
                continue;
            }
            # add current file to encrypted collection
            $s->project->cryption->collection[] =$file;
            $this->cout("> ", 0.5, self::green, 0);
            $this->cout($file, 0, self::defColor, 0);
            # invoke an encryption
            $e = new \zinux\kernel\security\cryption;
            # get file's content
            $file_cont = file_get_contents($file);
            # foreach encryption iter#
            for($i = 0;$i<$iter;$i++)
            {
                # encrypt the file's content
                $file_cont = $e->encrypt($file_cont, $key);
            }
            # save the file
            file_put_contents($file, $file_cont);
            # indicate the success
            $this->cout(" [ OK ]", 0, self::green);
        }
        # flag project as encrypted
        $s->project->cryption->meta->is_encrypted = 1;
        # save status object
        $this->SaveStatus($s);
        # indicate success
        $this ->cout()
                ->cout(count($s->project->cryption->collection), 1, self::yellow, 0)
                ->cout(" files encrypted ", 0, self::defColor, 0)
                ->cout("successfully", 0, self::green,0)
                ->cout("...");
    }
    /**
     * zg security decrypt handler
     * @throws \zinux\kernel\exceptions\invalideOperationException in case client key does not match with encryption's key's hash sum
     */
    public function decrypt($args)
    {
        # indicate the phase
        $this->cout("Initiating decryption operations ...<br />");
        # fetch data from args
        list($iter, $iter_sum, $key, $hash_sum) = $this->enc_head_op($args);
        # get status object
        $s = $this->GetStatus();
        # if the project is not encrypted or no files stored in encryption collection
        if(!isset($s->project->cryption) || !isset($s->project->cryption->meta->is_encrypted) || !isset($s->project->cryption->collection))
        {
            # indicate the warning
            $this->cout("[ DANGER CLOSE ]", 0, self::red)
                    ->cout("The project didn't flaged as encrypted!", 0.5, self::yellow)
                    ->cout("It is possible to lose your entire project by decrptying the project ", 0.5, self::yellow)
                    ->cout("that is not previously encrypted by '".self::cyan."zg security encrypt".self::yellow."' command!!!", 0.5, self::yellow)
                    ->cout("If you believe the project did realy encrypted by '".self::cyan."zg security encrypt".self::yellow."'", 0.5, self::yellow)
                    ->cout("command, You may proceed at you own risk!", 0.5, self::yellow)
                    ->cout();
            while(true)
            {
                # check if user wants to continue or not
                $txt = strtolower(readline(self::green."  Do you want to preceed?".self::defColor." [y/N] "));
                if($txt == "n")
                    return;
                if($txt != "y") continue;
                break;
            }
            # flag the operation as risky
            $risky = 1;
        }
        # if opt is not risky
        if(!isset($risky))
            # check client provided key with previously stored encryption key hash sum
            if(!isset($s->project->cryption->meta->key_check_sum) || $s->project->cryption->meta->key_check_sum != $hash_sum)
                # if no match with key hash sum, throw exception
                throw new \zinux\kernel\exceptions\invalideOperationException("The '".self::cyan."encryption key".self::yellow."' doesn't match with encrypted key!");
        # get cryption cache folder's path 
        $crypt_cache_path = $s->project->cryption->meta->path;
        # if no collection file did generated 
        if(!isset($s->project->cryption->collection))
            # go for all files under current dir
            $files = $this->getFiles();
        else        
            # otherwise if collection provided 
            # proceed with collection files
            $files = $s->project->cryption->collection;
        # clear decryption cache data
        $s->project->cryption->cache = array();
        # for each fetched files
        foreach ($files as $file)
        {
            # if not writable 
            if(!is_writable($file)) 
            {
                # indicate it
                $this->cout("> ", 0.5, self::red, 0);
                $this->cout("'".self::cyan.$file.self::defColor."' is not writable!", 0, self::defColor, 0);
                $this->cout(" [ FAILED ]", 0, self::red);
                # continue with other files
                continue;
            }
            # create a decryption cache file with a 
            # hashed file name under decryption cache dir
            $p = $crypt_cache_path.sha1($file);
            # for possible hash collision fail safe
            $i = 1;
            while(file_exists($p))
                $p.=$i++;
            # append crypt extention to decryption cache file 
            $p.=".crypt";
            # invoke a new encryptor
            $e = new \zinux\kernel\security\cryption;
            # get files content
            $file_cont = file_get_contents($file);
            # store the encrypted file into cache file
            file_put_contents($p, $file_cont);
            $this->cout("> ", 0.5, self::green, 0);
            $this->cout($file, 0, self::defColor, 0);
            # add metadata about current decryption cache file 
            # to status object
            $s->project->cryption->cache[] = new \zinux\zg\vendors\item($file, realpath($p));
            # foreach encryption iter#
            for($i = 0;$i<$iter;$i++)
            {
                # decrypt the files content
                $file_cont = $e->decrypt($file_cont, $key);
            }
            # restore back the file
            file_put_contents($file, $file_cont);
            # indicate the sucess
            $this->cout(" [ OK ]", 0, self::green);
        }
        # reset currect permissions for project
        exec("chmod 775 {$s->project->path} -R >/dev/null 2>&1");
        exec("chmod 777 {$s->project->path}");
        # un-flag that the project has encrypted
        unset($s->project->cryption->meta->is_encrypted);
        # save the status object
        $this->SaveStatus($s);
        # indicate the success
        $this ->cout()
                ->cout(count($s->project->cryption->cache), 1, self::yellow, 0)
                ->cout(" files decrypted ", 0, self::defColor, 0)
                ->cout("successfully", 0, self::green,0)
                ->cout("...");
        # note the client that it decryption was a restorable operation
        $this ->cout()
                ->cout("You can always undo ".self::yellow."last".self::defColor." '".self::cyan."zg security decrypt".self::defColor."' command's effects, ")
                ->cout("By '".self::cyan."zg security cache --reset".self::defColor."' command", 2.5);
    }
    /**
     * zg security cache handler
     * @throws \zinux\kernel\exceptions\invalideOperationException in case of user trying to clear the cache 
     * data while the project is flaged as encrypted or cryption data are miss-configured
     * @throws \zinux\kernel\exceptions\invalideArgumentException in case of invalid arg supplied
     */
    public function cache($args)
    {
        # this opt is valid under project directories
        if(!$this->CheckZG()) return;
        # this opt only accepts 1 arg
        $this->restrictArgCount($args, 1, 1);
        # fetch object status
        $s = $this->GetStatus();
        #some fail safe
        if(!isset($s->project->cryption) || !isset($s->project->cryption->meta))
            throw new \zinux\kernel\exceptions\invalideOperationException("No cryption data found!");
        # matching args
        switch(true)
        {
            # in case of client wants to clear cached data
            case $this->has_arg($args, "--clear"):
                # while the project is flaged as encrypted 
                if(isset($s->project->cryption->meta->is_encrypted))
                    # it is not possible
                    throw new 
                        \zinux\kernel\exceptions\invalideOperationException
                            ("The project is already encrypted!<br />You cannot clear cryption cache while the project is encrypted!<br />Decrypt the project first!");
                # some fail safe
                if(!isset($s->project->cryption->meta->path))
                    throw new \zinux\kernel\exceptions\invalideOperationException("No metadata found on cryption data!");
                # remove the cryption cache files
                exec("rm -rf '{$s->project->cryption->meta->path}'");
                $this ->cout("Cryption cached data cleared ", 0 ,self::defColor, 0)
                        ->cout("successfully.", 0, self::green);
                # unset the cryption metadata
                unset($s->project->cryption);
                break;
            # in case of client wants to undo the previous decryption opt
            case $this->has_arg($args, "--reset"):
                # if no cache data exists
                if(!isset($s->project->cryption->cache))
                    # no proceed needed
                    throw new \zinux\kernel\exceptions\invalideOperationException("No cache data found!");
                # foreach cached file
                foreach($s->project->cryption->cache as $item)
                {
                    # fetch the real target file's address
                    $file = $item->name;
                    # if target file is not writeable
                    if(!is_writable($file)) 
                    {
                        # indicate it
                        $this->cout("> ", 0.5, self::red, 0);
                        $this->cout("'".self::cyan.$file.self::defColor."' is not writable!", 0, self::defColor, 0);
                        $this->cout(" [ FAILED ]", 0, self::red);
                        # continue with others
                        continue;
                    }
                    $this->cout("> ", 0.5, self::green, 0);
                    $this->cout($file, 0, self::defColor, 0);
                    # replace the target file's content with cache file's content
                    file_put_contents($file, file_get_contents($item->path));
                    # indicate the success
                    $this->cout(" [ OK ]", 0, self::green);
                }
                # indicate the sucess
                $this ->cout()
                        ->cout(count($s->project->cryption->cache), 1, self::yellow, 0)
                        ->cout(" registered files reseted ", 0, self::defColor, 0)
                        ->cout("successfully", 0, self::green, 0)
                        ->cout("...");
                # flag the project as encrypted
                $s->project->cryption->meta->is_encrypted = 1;
                break;
            # if arg does not match with any above cases
            default:
                throw new \zinux\kernel\exceptions\invalideArgumentException;
        }
        # save the status object
        $this->SaveStatus($s);
    }
    /**
     * Recursively retrieves project files except files under zinux project
     * @return array all files under current project
     */
    protected function getFiles()
    {
        # fetch all files under current project
        # & exclude files under zinux directory
        $m = preg_grep("/^zinux\/.*/i",(\zinux\kernel\utilities\fileSystem::rglob("*")),PREG_GREP_INVERT);
        # foreach found files
        foreach($m as $key=> $value)
        {
            # filter only files
            if(is_dir($value))
                unset($m[$key]);
        }
        # return found files
        return $m;
    }
}
