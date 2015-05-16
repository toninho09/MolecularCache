<?php
namespace MolecularCache\Cache;
class CacheFile implements \MolecularCache\Interfaces\CacheInterface{

	private $folder;
	public function __construct($folder = null) {
	    $this->setCacheFolder(!is_null($folder) ? $folder : sys_get_temp_dir());
	}

	public function setCacheFolder($path){
		if(file_exists($path) && is_dir($path) && is_writable($path)){
			$this->folder = $path;
		}else{
			throw new \Exception("The Folder $path is invalid to save Cache", 1);
		}
	}

	public function getCacheFolder(){
		return $this->folder;
	}

	protected function generateFileLocation($key) {
    	return $this->folder . DIRECTORY_SEPARATOR . sha1($key);
  	}

	/**
	 * Add one value in the cache,
	 * @param string  $key     the key using to rescue the cache value
	 * @param mixed  $content  the value of the cache
	 * @param integer $time    the time to rescue the cache, in minutes
	 */	
	public function add($key,$content,$time = 60){
		$this->set($key,$content,$time);
	}

	public function set($key,$content,$time = 60){
		if(!is_numeric($time)) throw new \Exception("Time is not a numeric value.", 1);
		$time = strtotime('+'.$time.' minutes');
		$content = serialize(array('expired_at' => $time,'content' => $content));
		return $this->createFile($key, $content);
	}

	public function get($key,$default = null){
		$filename = $this->generateFileLocation($key);
	    if (file_exists($filename) && is_readable($filename)) {
	      $cache = unserialize(file_get_contents($filename));
	      if ($cache['expired_at'] > time()) {
	        return $cache['content'];
	      } else {
	        unlink($filename);
	        return $default;
	      }
	    }
	    return $default;
	}

	public function has($key){
		$filename = $this->generateFileLocation($key);
	    if (file_exists($filename) && is_readable($filename)) {
	      $cache = unserialize(file_get_contents($filename));
	      if ($cache['expired_at'] > time()) {
	        return true;
	      } else {
	        unlink($filename);
	        return false;
	      }
	    }
	    return false;
	}

	public function forever($key,$content){
		if(!is_numeric($time)) throw new \Exception("Time is not a numeric value.", 1);
		$time ="ever";
		$content = serialize(array('expired_at' => $time,'content' => $content));
		return $this->createFile($key, $content);
	}

	public function forget($key){
		$filename = $this->generateFileLocation($key);
	    if (file_exists($filename) && is_readable($filename)) {
	      $cache = unserialize(file_get_contents($filename));
	      if ($cache['expired_at'] != 'ever' && $cache['expired_at'] > time()) {
	        return true;
	      } else {
	        unlink($filename);
	        return false;
	      }
	    }
	}

	public function group($key){
		return false;
	}

	private function createFile($key,$content){
		$filename = $this->generateFileLocation($key);
    	return file_put_contents($filename, $content) OR trigger_error('Not Possible create the cache file.', E_USER_ERROR);
	}
}