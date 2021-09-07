<?php

namespace Metafrastis\BaiduTranslate;

class BaiduTranslate {

	public $baiduid;
	public $cookie;
	public $sign;
	public $token;
	public $queue = [];
	public $response;
	public $responses = [];

	public function translate($args = [], $opts = []) {
		if (is_object($args)) {
			$args = json_decode(json_encode($args), true);
		}
		if (is_string($args)) {
			if (($arr = json_decode($args, true))) {
				$args = $arr;
			} else {
				parse_str($args, $arr);
				if ($arr) {
					$args = $arr;
				}
			}
		}
		$args = is_array($args) ? $args : [];
		$args['from'] = isset($args['from']) ? $args['from'] : null;
		$args['to'] = isset($args['to']) ? $args['to'] : null;
		$args['text'] = isset($args['text']) ? $args['text'] : null;
		$args['baiduid'] = !empty($args['baiduid']) ? $args['baiduid'] : $this->baiduid;
		if ($args['baiduid']) {
			$this->baiduid = $args['baiduid'];
		}
		$args['cookie'] = !empty($args['cookie']) ? $args['cookie'] : $this->cookie;
		if (!$args['cookie']) {
			$args['cookie'] = stream_get_meta_data(tmpfile())['uri'];
		}
		if (!is_file($args['cookie'])) {
			if (is_dir(dirname($args['cookie'])) || mkdir(dirname($args['cookie']), 0755, true)) {
				touch($args['cookie']);
			}
		}
		if (is_file($args['cookie']) && !filesize($args['cookie'])) {
			file_put_contents($args['cookie'], '# Netscape HTTP Cookie File
# https://curl.haxx.se/docs/http-cookies.html
# This file was generated by libcurl! Edit at your own risk.

.baidu.com	TRUE	/	FALSE	'.strval(time()+60).'	BAIDUID	'.$args['baiduid']."\x0a");
		}
		if ($args['cookie']) {
			$this->cookie = $args['cookie'];
		}
		$args['sign'] = !empty($args['sign']) ? $args['sign'] : $this->sign;
		if (!$args['sign']) {
			$args['sign'] = '127170.332787';
		}
		if ($args['sign']) {
			$this->sign = $args['sign'];
		}
		$args['token'] = !empty($args['token']) ? $args['token'] : $this->token;
		if (!$args['baiduid'] || !$args['token']) {
			$this->home($args, $opts);
			$args['baiduid'] = $this->baiduid ? $this->baiduid : $args['baiduid'];
			$args['token'] = $this->token ? $this->token : $args['token'];
		}
		if ($args['baiduid']) {
			$this->baiduid = $args['baiduid'];
		}
		if ($args['token']) {
			$this->token = $args['token'];
		}
		if (!$args['from']) {
			return false;
		}
		if (!$args['to']) {
			return false;
		}
		if (!$args['text']) {
			return false;
		}
		if (!$args['cookie']) {
			return false;
		}
		if (!$args['sign']) {
			return false;
		}
		if (!$args['token']) {
			return false;
		}
		$url = 'https://fanyi.baidu.com/v2transapi?from='.$args['from'].'&to='.$args['to'];
		$headers = [
			'Accept: '.'*'.'/'.'*',
			'Accept-Language: en-US,en;q=0.5',
			'Connection: keep-alive',
			'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
			'Origin: https://fanyi.baidu.com',
			'Referer: https://fanyi.baidu.com/',
			'Sec-Fetch-Dest: empty',
			'Sec-Fetch-Mode: cors',
			'Sec-Fetch-Site: same-origin',
			'User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:91.0) Gecko/20100101 Firefox/91.0',
			'X-Requested-With: XMLHttpRequest',
		];
		$params = [
			'from' => $args['from'],
			'to' => $args['to'],
			'query' => $args['text'],
			'transtype' => 'realtime',
			'simple_means_flag' => '3',
			'sign' => $args['sign'],
			'token' => $args['token'],
			'domain' => 'common',
		];
		$options = [
			CURLOPT_CERTINFO => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_SSL_VERIFYHOST => 2,
			CURLOPT_SSL_VERIFYPEER => 2,
			CURLOPT_COOKIEFILE => $args['cookie'],
			CURLOPT_COOKIEJAR => $args['cookie'],
		];
		$options = array_replace($options, $opts);
		$queue = isset($args['queue']) ? 'translate' : false;
		$response = $this->post($url, $headers, $params, $options, $queue);
		if (!$queue) {
			$this->response = $response;
		}
		if ($queue) {
			return;
		}
		$json = json_decode($response['body'], true);
		if (!$json || !isset($json['trans_result']['status']) || $json['trans_result']['status'] || !isset($json['trans_result']['data'][0]['dst'])) {
			return false;
		}
		return is_array($json['trans_result']['data'][0]['dst']) && isset($json['trans_result']['data'][0]['dst'][0]) ? $json['trans_result']['data'][0]['dst'][0] : $json['trans_result']['data'][0]['dst'];
	}

	public function detect($args = [], $opts = []) {
		if (is_object($args)) {
			$args = json_decode(json_encode($args), true);
		}
		if (is_string($args)) {
			if (($arr = json_decode($args, true))) {
				$args = $arr;
			} else {
				parse_str($args, $arr);
				if ($arr) {
					$args = $arr;
				}
			}
		}
		$args = is_array($args) ? $args : [];
		$args['text'] = isset($args['text']) ? $args['text'] : null;
		if (!$args['text']) {
			return false;
		}
		$url = 'https://fanyi.baidu.com/langdetect';
		$headers = [
			'Accept: '.'*'.'/'.'*',
			'Accept-Language: en-US,en;q=0.5',
			'Connection: keep-alive',
			'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
			'Origin: https://fanyi.baidu.com',
			'Referer: https://fanyi.baidu.com/',
			'Sec-Fetch-Dest: empty',
			'Sec-Fetch-Mode: cors',
			'Sec-Fetch-Site: same-origin',
			'User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:91.0) Gecko/20100101 Firefox/91.0',
			'X-Requested-With: XMLHttpRequest',
		];
		$params = [
			'query' => $args['text'],
		];
		$options = [
			CURLOPT_CERTINFO => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_SSL_VERIFYHOST => 2,
			CURLOPT_SSL_VERIFYPEER => 2,
			CURLOPT_COOKIEFILE => $args['cookie'],
			CURLOPT_COOKIEJAR => $args['cookie'],
		];
		$options = array_replace($options, $opts);
		$queue = isset($args['queue']) ? 'translate' : false;
		$response = $this->post($url, $headers, $params, $options, $queue);
		if (!$queue) {
			$this->response = $response;
		}
		if ($queue) {
			return;
		}
		$json = json_decode($response['body'], true);
		if (!$json || !isset($json['error']) || $json['error'] || !isset($json['lan'])) {
			return false;
		}
		return is_array($json['lan']) && isset($json['lan'][0]) ? $json['lan'][0] : $json['lan'];
	}

	public function home($args = [], $opts = []) {
		$args['cookie'] = isset($args['cookie']) ? $args['cookie'] : $this->cookie;
		if (!$args['cookie']) {
			$this->cookie = $args['cookie'] = stream_get_meta_data(tmpfile())['uri'];
		}
		if ($args['cookie']) {
			$this->cookie = $args['cookie'];
		}
		$args['force'] = isset($args['force']) ? $args['force'] : false;
		if ($this->baiduid && $this->token && !$args['force']) {
			return ['baiduid' => $this->baiduid, 'token' => $this->token];
		}
		$url = 'https://fanyi.baidu.com/';
		$headers = [
			'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,'.'*'.'/'.'*'.';q=0.8',
			'Accept-Language: en-US,en;q=0.5',
			'Cache-Control: max-age=0',
			'Connection: keep-alive',
			'Sec-Fetch-Dest: document',
			'Sec-Fetch-Mode: navigate',
			'Sec-Fetch-Site: none',
			'Sec-Fetch-User: ?1',
			'Upgrade-Insecure-Requests: 1',
			'User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:91.0) Gecko/20100101 Firefox/91.0',
		];
		$params = null;
		$options = [
			CURLOPT_CERTINFO => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_SSL_VERIFYHOST => 2,
			CURLOPT_SSL_VERIFYPEER => 2,
			CURLOPT_COOKIEFILE => $args['cookie'],
			CURLOPT_COOKIEJAR => $args['cookie'],
		];
		$options = array_replace($options, $opts);
		$queue = false;
		for ($j = 0; $j < 2; $j++) {
			$response = $this->get($url, $headers, $params, $options);
			$this->response = $response;
			if (preg_match('`Set\-Cookie[\x00-\x20\x7f]*\:[\x00-\x20\x7f]*BAIDUID\=([^\;\x0d\x0a]+)`i', $response['head'], $match)) {
				$this->baiduid = $match[1];
			}
			if (preg_match('`token[\x00-\x20\x7f]*\:[\x00-\x20\x7f]*[\x27]([^\x27]+)[\x27]`', $response['body'], $match)) {
				$this->token = $match[1];
				break;
			} elseif (preg_match('`token[\x00-\x20\x7f]*\:[\x00-\x20\x7f]*[\x22]([^\x22]+)[\x22]`', $response['body'], $match)) {
				$this->token = $match[1];
				break;
			}
		}
		return ['baiduid' => $this->baiduid, 'token' => $this->token];
	}

	public function request($method, $url, $headers = [], $params = null, $options = [], $queue = false) {
		if (is_string($headers)) {
			$headers = array_values(array_filter(array_map('trim', explode("\x0a", $headers))));
		}
		if (is_array($headers) && isset($headers['headers']) && is_array($headers['headers'])) {
			$headers = $headers['headers'];
		}
		if (is_array($headers)) {
			foreach ($headers as $key => $value) {
				if (is_string($key) && !is_numeric($key)) {
					$headers[$key] = sprintf('%s: %s', $key, $value);
				}
			}
		}
		$opts = [];
		$opts[CURLINFO_HEADER_OUT] = true;
		$opts[CURLOPT_CONNECTTIMEOUT] = 5;
		$opts[CURLOPT_CUSTOMREQUEST] = strtoupper($method);
		$opts[CURLOPT_ENCODING] = '';
		$opts[CURLOPT_FOLLOWLOCATION] = false;
		$opts[CURLOPT_HEADER] = true;
		$opts[CURLOPT_HTTPHEADER] = $headers;
		if ($params !== null) {
			$opts[CURLOPT_POSTFIELDS] = is_array($params) || is_object($params) ? http_build_query($params) : $params;
		}
		$opts[CURLOPT_RETURNTRANSFER] = true;
		$opts[CURLOPT_SSL_VERIFYHOST] = false;
		$opts[CURLOPT_SSL_VERIFYPEER] = false;
		$opts[CURLOPT_TIMEOUT] = 10;
		$opts[CURLOPT_URL] = $url;
		foreach ($opts as $key => $value) {
			if (!array_key_exists($key, $options)) {
				$options[$key] = $value;
			}
		}
		if ($queue) {
			$this->queue[] = ['options' => $options, 'queue' => $queue];
			return;
		}
		$follow = false;
		if ($options[CURLOPT_FOLLOWLOCATION]) {
			$follow = true;
			$options[CURLOPT_FOLLOWLOCATION] = false;
		}
		$errors = 2;
		$redirects = isset($options[CURLOPT_MAXREDIRS]) ? $options[CURLOPT_MAXREDIRS] : 5;
		while (true) {
			$ch = curl_init();
			curl_setopt_array($ch, $options);
			$body = curl_exec($ch);
			$info = curl_getinfo($ch);
			$head = substr($body, 0, $info['header_size']);
			$body = substr($body, $info['header_size']);
			$error = curl_error($ch);
			$errno = curl_errno($ch);
			curl_close($ch);
			$response = [
				'info' => $info,
				'head' => $head,
				'body' => $body,
				'error' => $error,
				'errno' => $errno,
			];
			if ($error || $errno) {
				if ($errors > 0) {
					$errors--;
					continue;
				}
			} elseif ($info['redirect_url'] && $follow) {
				if ($redirects > 0) {
					$redirects--;
					$options[CURLOPT_URL] = $info['redirect_url'];
					continue;
				}
			}
			break;
		}
		return $response;
	}

	public function get($url, $headers = [], $params = null, $options = [], $queue = false) {
		return $this->request('GET', $url, $headers, $params, $options, $queue);
	}

	public function post($url, $headers = [], $params = [], $options = [], $queue = false) {
		return $this->request('POST', $url, $headers, $params, $options, $queue);
	}

	public function multi($args = []) {
		if (!$this->queue) {
			return [];
		}
		$mh = curl_multi_init();
		$chs = [];
		foreach ($this->queue as $key => $request) {
			$ch = curl_init();
			$chs[$key] = $ch;
			curl_setopt_array($ch, $request['options']);
			curl_multi_add_handle($mh, $ch);
		}
		$running = 1;
		do {
			curl_multi_exec($mh, $running);
		} while ($running);
		$responses = [];
		foreach ($chs as $key => $ch) {
			curl_multi_remove_handle($mh, $ch);
			$body = curl_multi_getcontent($ch);
			$info = curl_getinfo($ch);
			$head = substr($body, 0, $info['header_size']);
			$body = substr($body, $info['header_size']);
			$error = curl_error($ch);
			$errno = curl_errno($ch);
			curl_close($ch);
			$response = [
				'info' => $info,
				'head' => $head,
				'body' => $body,
				'error' => $error,
				'errno' => $errno,
			];
			$this->responses[$key] = $response;
			$options = $this->queue[$key]['options'];
			if (strpos($options[CURLOPT_URL], '/v2transapi') !== false) {
				$json = json_decode($body, true);
				if (!$json || !isset($json['trans_result']['status']) || $json['trans_result']['status'] || !isset($json['trans_result']['data'][0]['dst'])) {
					$responses[$key] = false;
					continue;
				}
				$responses[$key] = is_array($json['trans_result']['data'][0]['dst']) && isset($json['trans_result']['data'][0]['dst'][0]) ? $json['trans_result']['data'][0]['dst'][0] : $json['trans_result']['data'][0]['dst'];
			} elseif (strpos($options[CURLOPT_URL], '/langdetect') !== false) {
				$json = json_decode($body, true);
				if (!$json || !isset($json['error']) || $json['error'] || !isset($json['lan'])) {
					$responses[$key] = false;
					continue;
				}
				$responses[$key] = is_array($json['lan']) && isset($json['lan'][0]) ? $json['lan'][0] : $json['lan'];
			} else {
				$responses[$key] = $body;
			}
		}
		curl_multi_close($mh);
		$this->queue = [];
		return $responses;
	}

}
