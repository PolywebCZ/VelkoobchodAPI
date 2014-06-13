<?php

class Http {

	private $_host		 = null;
	private $_protocol	 = null;

	const HTTP	 = 'http';
	const HTTPS	 = 'https';

	/**
	 * Továrnička třídy
	 *
	 * @param string $host
	 * @param string $protocol
	 * @return Http
	 */
	static public function connect($host, $protocol = self::HTTP) {
		return new self($host, $protocol);
	}

	protected function __construct($host, $protocol) {
		$this->_host	 = $host;
		$this->_protocol = $protocol;
	}

	const POST = 'POST';
	const GET	 = 'GET';

	/**
	 * POST Request
	 *
	 * @param string $url
	 * @param array $params
	 * @return string
	 */
	public function doPost($url, $params = array()) {
		return $this->_exec(self::POST, $this->_url($url), $params);
	}

	/**
	 * GET Request
	 *
	 * @param string $url
	 * @param array $params
	 * @return string
	 */
	public function doGet($url, $params = array()) {
		return $this->_exec(self::GET, $this->_url($url), $params);
	}

	private $_headers = array();

	/**
	 * setHeaders
	 *
	 * @param array $headers
	 * @return Http
	 */
	public function setHeaders($headers) {
		$this->_headers = $headers;
		return $this;
	}

	/**
	 * Vytvoří absolutní URL
	 *
	 * @param unknown_type $url
	 * @return unknown
	 */
	private function _url($url = null) {
		return "{$this->_protocol}://{$this->_host}/{$url}";
	}

	/**
	 * Vykonání požadavku
	 *
	 * @param string $type
	 * @param string $url
	 * @param array $params
	 * @return string
	 */
	private function _exec($type, $url, $params = array()) {
		$headers = $this->_headers;
		$s		 = curl_init();

		switch ($type) {
			case self::POST:
				// API velkoobchodu přijímá JSON řetězec
				$params = json_encode($params);
				curl_setopt($s, CURLOPT_URL, $url);
				curl_setopt($s, CURLOPT_POST, true);
				curl_setopt($s, CURLOPT_POSTFIELDS, $params);
				break;
			case self::GET:
				curl_setopt($s, CURLOPT_URL, $url . '?' . http_build_query($params));
				break;
		}

		curl_setopt($s, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($s, CURLOPT_HTTPHEADER, $headers);
		$_out	 = curl_exec($s);
		$status	 = curl_getinfo($s, CURLINFO_HTTP_CODE);
		curl_close($s);

		switch ($status) {
			case 200:
				$out = json_decode($_out);
				if (isset($out->error)) {
					/*
					 * Při chybě pošle API HTTP kód 200, protože HTTP požadavek byl úspěšný.
					 * Jako odpověď však pošle pole [error => xxx, message => 'zpráva chyby']
					 */
					throw new Api_Exception("API error [{$out->error}]: {$out->message}", $out->error);
				}
				break;
			default:
				throw new Http_Exception("HTTP error: {$status} | response: " . htmlspecialchars($_out), $status);
		}
		return $out;
	}

}

class Http_Exception extends Exception {
	
}

class Api_Exception extends Exception {
	
}
