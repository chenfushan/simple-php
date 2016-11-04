<?php 
	require_once dirname(__FILE__).'/MemcacheConf.php';
	/**
	 * set the memcache value
	 * @param string  $key     the memcache key
	 * @param string  $value   the key of value
	 * @param integer $timeout the value time out
	 */
	function setMemcacheValue($key, $value, $timeout = -1)
	{
		if ($timeout == -1) {
			$timeout = MEMCACHE_TIMEOUT;
		}
		$mc = new Memcache;
		$mc->connect(MEMCACHE_HOST,MEMCACHE_PORT);
		return $mc->set($key, $value, 0, $timeout);

	}

	/**
	 * get the memcache value by key
	 * @param  string $key memcache key
	 * @return string      the value for key
	 */
	function getMemcacheValue($key)
	{
		$mc = new Memcache;
		$mc->connect(MEMCACHE_HOST,MEMCACHE_PORT);
		return $mc->get($key);
	}

 ?>