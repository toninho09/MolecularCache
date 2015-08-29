<?php
namespace MolecularCache\Interfaces;
Interface CacheInterface{
	function add($key,$content,$time = 60);
	function set($key,$content,$time = 60);
	function get($key,$default = '');
	function has($key);
	function forever($key,$content);
	function forget($key);
}