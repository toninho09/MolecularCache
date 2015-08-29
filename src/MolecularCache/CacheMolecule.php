<?php
	namespace MolecularCache;
	class CacheMolecule{

		protected $class="\MolecularCache\Cache\CacheFile";
		
		protected static $instance;
		
	    public static function __callStatic($name, $arguments)
	    {
			$retorno = call_user_func_array(array(self::getInstance(),$name),$arguments);
			if(!is_null($retorno)) return $retorno;
			return self::getInstance();
		}
		
		public function __call($name, $arguments)
	    {
			$retorno = call_user_func_array(array(self::getInstance(),$name),$arguments);
			if(!is_null($retorno)) return $retorno;
			return self::getInstance();
		}
		
		public function register(\MolecularCore\Core &$app = null){
			if (!isset(self::$instance)) {
				if(!is_null($app->getConfig('cache.type'))) 
					$this->class = $this->getCacheClass($app->getConfig('cache.type'));
	            self::$instance = new $this->class();
	        }
		}
		
		public function run(){
		}
		
		public static function getInstance(){
			return self::$instance;
		}

		public function getCacheClass($type){
			switch ($type) {
				case 'file':
					return "\MolecularCache\Cache\CacheFile";
					break;
				
				default:
					throw new \Exception("Cache Type not Found.", 1);
					break;
			}
		}
	}