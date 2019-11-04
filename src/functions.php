<?php

namespace LilleBitte\Messenger;

use LilleBitte\Messenger\Uri;

if (!function_exists('getUriFromServer')) {
	function getUriFromServer($server = [])
	{
		// get scheme
		$scheme = isset($server['HTTPS']) && !empty($server['HTTPS']) && $server['HTTPS'] !== 'off'
			? 'https'
			: 'http';

		// get host and port
		if (isset($server['SERVER_NAME']) && isset($server['SERVER_PORT'])) {
			$host = $server['SERVER_NAME'];
			$port = intval($server['SERVER_PORT']);
		}

		if (isset($server['HTTP_HOST'])) {
			$split = explode(':', $server['HTTP_HOST']);
			$host = $split[0];
			$port = isset($split[1]) ? intval($split[1]) : 80;
		}

		// get path
		$path = isset($server['PATH_INFO'])
			? $server['PATH_INFO']
			: '/';

		// get query string
		$query = isset($server['QUERY_STRING'])
			? $server['QUERY_STRING']
			: '';

		return (new Uri)
			->withScheme($scheme)
			->withHost($host)
			->withPort($port)
			->withPath($path)
			->withQuery($query);
	}
}

if (!function_exists('getMethodFromServer')) {
	function getMethodFromServer($server = [])
	{
		return isset($server['REQUEST_METHOD'])
			? $server['REQUEST_METHOD']
			: 'GET';
	}
}

if (!function_exists('getProtocolVersionFromServer')) {
	function getProtocolVersionFromServer($server = [])
	{
		$callback = function($proto)
		{
			$split = explode('/', $proto);
			return $split[1];
		};

		return isset($server['SERVER_PROTOCOL'])
			? $callback($server['SERVER_PROTOCOL'])
			: '1.1';
	}
}
