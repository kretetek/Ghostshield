<?php
/**
 * Akeeba Engine
 * The modular PHP5 site backup engine
 *
 * This is Akeeba Engine's RackSpace CloudFiles API implementation
 *
 * @copyright Copyright (c)2009-2014 Nicholas K. Dionysopoulos
 * @license   GNU GPL version 3 or, at your option, any later version
 * @package   akeebaengine
 */

// Protection against direct access
defined('AKEEBAENGINE') or die();

/**
 * Base exception class
 */
class AEUtilCloudfilesExceptionBase extends Exception
{
}

/**
 * Exception thrown when the username is missing
 */
class AEUtilCloudfilesExceptionMissingUsername extends Exception
{
}

/**
 * Exception thrown when the API key is missing
 */
class AEUtilCloudfilesExceptionMissingApikey extends Exception
{
}

/**
 * Generic exception thrown upon failure of a REST API call
 */
class AEUtilCloudfilesExceptionHttp extends AEUtilCloudfilesExceptionBase
{
}

/**
 * Self-contained implementation of the RackSpace CloudFiles in PHP
 */
class AEUtilCloudfiles
{
	/** @var string The CloudFiles username */
	private $username = '';

	/** @var string The CloudFiles API key */
	private $apiKey = '';

	/** @var string The token returned by CloudFiles */
	private $token = '';

	/** @var int The expiration timestamp of the token we got from CloudFiles */
	private $tokenExpiration = 0;

	/** @var string RackSpace Tenant ID */
	private $tenantId = '';

	/** @var string The user contract (MossoCloudFS_aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee) returned by CloudFiles */
	private $userContract = '';

	/** @var string The authentication endpoint. This is a universal endpoint for all accounts now. */
	private $authEndpoint = 'https://identity.api.rackspacecloud.com/v2.0';

	/** @var array List of storage endpoints per region */
	private $storageEndpoints = array(
		'ORD' => 'https://storage101.ord1.clouddrive.com',
		'DFW' => 'https://storage101.dfw1.clouddrive.com',
		'HKG' => 'https://storage101.hkg1.clouddrive.com',
		'LON' => 'https://storage101.lon3.clouddrive.com',
		'IAD' => 'https://storage101.iad3.clouddrive.com',
		'SYD' => 'https://storage101.syd2.clouddrive.com',
	);

	/** @var string The region of the account. It is kindly reported by the Swift API, no need to set it. */
	private $region = 'LON';

	/** @var string The storage endpoint to use. If unspecified we use the default endpoint of each region. */
	private $storageEndpoint = '';

	/** @var string The storage API version to use */
	private $apiVersion = 'v1';

	/**
	 * Public constructor
	 *
	 * @param string $username The CloudFiles username
	 * @param string $apiKey   The CloudFiles API key
	 * @param array  $options  Configuration options (authEndpoint, storageEndpoint, apiVersion, userContract, tenantId, tokenExpiration, token)
	 *
	 * @return AEUtilCloudfiles
	 *
	 * @throws AEUtilCloudfilesExceptionMissingUsername  You have not given me a username
	 * @throws AEUtilCloudfilesExceptionMissingApikey    You have not given me an API key
	 */
	public function __construct($username, $apiKey, $options = array())
	{
		// Data validation
		if (empty($username))
		{
			throw new AEUtilCloudfilesExceptionMissingUsername('You have not specified your CloudFiles username');
		}

		if (empty($username))
		{
			throw new AEUtilCloudfilesExceptionMissingApikey('You have not specified your CloudFiles API key');
		}

		// Very simplistic options parsing
		if (is_array($options) && count($options))
		{
			foreach ($options as $key => $value)
			{
				if (isset($this->$key))
				{
					$this->$key = $value;
				}
			}
		}

		// Initialisation
		$this->username = $username;
		$this->apiKey = $apiKey;
	}

	/**
	 * Return the current options, useful to instantiate a new object without having to re-authenticate to CloudFiles
	 *
	 * @return array
	 */
	public function getCurrentOptions()
	{
		return array(
			'token'           => $this->token,
			'tokenExpiration' => $this->tokenExpiration,
			'tenantId'        => $this->tenantId,
			'userContract'    => $this->userContract,
			'authEndpoint'    => $this->authEndpoint,
			'region'          => $this->region,
			'storageEndpoint' => $this->storageEndpoint,
			'apiVersion'      => $this->apiVersion,
		);
	}

	/**
	 * Authenticate the user and obtain a new token. If there is a token and it's not expired yet we will reuse it.
	 *
	 * @param bool $force Force authentication?
	 */
	public function authenticate($force = false)
	{
		// Should I proceed?
		if (!$force)
		{
			if (!empty($this->token) && !empty($this->tokenExpiration))
			{
				if ($this->tokenExpiration > (time() + 3600))
				{
					// I have a token and its expiration time is more than one hour into the future. No need to re-auth.
					return;
				}
			}
		}

		$request = new AEUtilCloudfilesRequest('POST', $this->authEndpoint . '/tokens');

		$dataRaw = (object)array(
			'auth' => array(
				"RAX-KSKEY:apiKeyCredentials" => array(
					'username' => $this->username,
					'apiKey'   => $this->apiKey,
				)
			)
		);

		$dataForPost = json_encode($dataRaw);
		$request->data = $dataForPost;
		$request->setHeader('Accept', 'application/json');
		$request->setHeader('Content-Type', 'application/json');
		$request->setHeader('Content-Length', strlen($request->data));

		$response = $request->getResponse();

		$this->token = $response->body->access->token->id;
		$this->tenantId = $response->body->access->token->tenant->id;

		$date = new DateTime($response->body->access->token->expires);
		$this->tokenExpiration = $date->getTimestamp();

		$raxAuthRegionKey = 'RAX-AUTH:defaultRegion';
		$defaultRegion = $response->body->access->user->$raxAuthRegionKey;

		if (empty($this->region))
		{
			$this->region = strtoupper($defaultRegion);
		}

		if (empty($this->storageEndpoint))
		{
			$this->storageEndpoint = $this->storageEndpoints[$this->region];
		}

		foreach ($response->body->access->serviceCatalog as $service)
		{
			if ($service->name != 'cloudFiles')
			{
				continue;
			}

			foreach ($service->endpoints as $endpoint)
			{
				if ($endpoint->region != $defaultRegion)
				{
					continue;
				}

				$this->userContract = $endpoint->tenantId;
				break;
			}
		}
	}

	/**
	 * Lists the containers in the CloudFiles account
	 *
	 * @param bool   $assoc         Should I return an associative array, where the key is the container name? (default: no)
	 * @param string $lastContainer Start listing AFTER this last container (pagination)
	 * @param int    $limit         How many containers to list
	 *
	 * @return array Array or objects. Internal objects have keys count, bytes, name
	 */
	public function listContainers($assoc = false, $lastContainer = null, $limit = 10000)
	{
		// Re-authenticate if necessary
		$this->authenticate();

		// Get the URL to list containers
		$url = $this->storageEndpoint . '/' . $this->apiVersion . '/' . $this->userContract;

		// Get the request object
		$request = new AEUtilCloudfilesRequest('GET', $url);
		$request->setHeader('X-Auth-Token', $this->token);
		$request->setHeader('Accept', 'application/json');
		$request->setParameter('format', 'json');

		if (!empty($lastContainer))
		{
			$request->setParameter('marker', $lastContainer);
		}

		if (!is_numeric($limit))
		{
			$limit = 10000;
		}

		if ($limit <= 0)
		{
			$limit = 10000;
		}

		$request->setParameter('limit', $limit);

		$response = $request->getResponse();

		if (!$assoc)
		{
			return $response->body;
		}

		$ret = array();

		if (!empty($response->body))
		{
			foreach ($response->body as $container)
			{
				$ret[$container->name] = $container;
			}
		}

		return $ret;
	}

	/**
	 * Lists the contents of a directory inside the container
	 *
	 * @param string $container The name of the container to list
	 * @param string $path      The path to the directory you want to list, '' for the root.
	 * @param bool   $assoc     Should I return an associative array with filenames as keys?
	 * @param null   $lastEntry The entry AFTER which to start listing
	 * @param int    $limit     How many files to show (1000 by default)
	 * @param string $prefix    The common prefix of files to list
	 *
	 * @return array Array of objects. Object keys: hash, last_modified, bytes, name, content_type
	 */
	public function listContents($container, $path = '', $assoc = false, $lastEntry = null, $limit = 1000, $prefix = '')
	{
		// Re-authenticate if necessary
		$this->authenticate();

		// Get the URL to list containers
		$url = $this->storageEndpoint . '/' . $this->apiVersion . '/' . $this->userContract . '/' . $container;
		$url = rtrim($url, '\\/');
		$path = ltrim($path, '\\/');
		$url .= '/' . $path;

		// Get the request object
		$request = new AEUtilCloudfilesRequest('GET', $url);
		$request->setHeader('X-Auth-Token', $this->token);
		$request->setHeader('Accept', 'application/json');
		$request->setParameter('format', 'json');

		if (!empty($lastEntry))
		{
			$request->setParameter('marker', $lastEntry);
		}

		if (!empty($prefix))
		{
			$request->setParameter('prefix', $prefix);
		}

		if (!is_numeric($limit))
		{
			$limit = 1000;
		}

		if ($limit <= 0)
		{
			$limit = 1000;
		}

		$request->setParameter('limit', $limit);
		$request->setParameter('delimiter', '/');

		$response = $request->getResponse();

		if (!$assoc)
		{
			return $response->body;
		}

		$ret = array();

		if (!empty($response->body))
		{
			foreach ($response->body as $file)
			{
				$ret[$file->name] = $file;
			}
		}

		return $ret;
	}

	/**
	 * Uploads a file. The $input array can have one of the following formats:
	 *
	 * 1. A string with the contents of the file to be put to CloudFiles
	 *
	 * 2. An array('fp' => $fp) containing a file pointer, open in read binary mode, to the file to upload
	 *
	 * 3. An array('file' => $pathToFile) containing the path to the file to upload
	 *
	 * 4. An array('data' => $rawData) which is the same as passing a string (case 1)
	 *
	 * When using an array you can also pass the following optional parameters in the array:
	 * size        The size of the uploaded content in bytes
	 *
	 * @param string|array $input       See the method description
	 * @param string       $container   The name of the CloudFiles container to use
	 * @param string       $path        The path inside the container of the uploaded file
	 * @param string       $contentType The content type of the uploaded file
	 */
	public function putObject($input, $container, $path, $contentType = null)
	{
		// Re-authenticate if necessary
		$this->authenticate();

		// Get the URL to list containers
		$url = $this->storageEndpoint . '/' . $this->apiVersion . '/' . $this->userContract . '/' . $container;
		$url = rtrim($url, '\\/');
		$path = ltrim($path, '\\/');
		$url .= '/' . $path;

		// Get the request object
		$request = new AEUtilCloudfilesRequest('PUT', $url);
		$request->setHeader('X-Auth-Token', $this->token);
		//$request->setHeader('Accept', 'application/json');

		// Decide what to do based on the $input format
		if (is_string($input))
		{
			$input = array(
				'data' => $input,
				'size' => strlen($input),
			);
		}

		// Data
		if (isset($input['fp']))
		{
			$request->fp = $input['fp'];
		}
		elseif (isset($input['file']))
		{
			$request->fp = @fopen($input['file'], 'rb');
		}
		elseif (isset($input['data']))
		{
			$request->data = $input['data'];
		}

		// Content-Length (required)
		if (isset($input['size']) && $input['size'] >= 0)
		{
			$request->size = $input['size'];
		}
		else
		{
			if (isset($input['file']))
			{
				clearstatcache(false, $input['file']);
				$request->size = @filesize($input['file']);
			}
			elseif (isset($input['data']))
			{
				$request->size = strlen($input['data']);
			}
		}

		if (empty($contentType))
		{
			$contentType = 'application/octet-stream';
		}

		$request->setParameter('Content-Type', $contentType);
		$request->setParameter('Content-Length', $request->size);

		$request->getResponse();

		if (isset($input['file']))
		{
			@fclose($request->fp);
		}
	}

	/**
	 * Downloads a file from CloudFiles back to your server
	 *
	 * @param string   $container The name of the CloudFiles container
	 * @param string   $path      The path to the file to download
	 * @param resource $fp        A file pointer, opened in write binary mode, to write out the downloaded file
	 * @param array    $headers   An array of headers to send during the download, e.g. ['Range' => '1-100']
	 *
	 * @return void
	 */
	public function downloadObject($container, $path, &$fp, $headers = array())
	{
		// Re-authenticate if necessary
		$this->authenticate();

		// Get the URL to list containers
		$url = $this->storageEndpoint . '/' . $this->apiVersion . '/' . $this->userContract . '/' . $container;
		$url = rtrim($url, '\\/');
		$path = ltrim($path, '\\/');
		$url .= '/' . $path;

		// Get the request object
		$request = new AEUtilCloudfilesRequest('GET', $url);
		$request->setHeader('X-Auth-Token', $this->token);

		if (!empty($headers))
		{
			foreach($headers as $k => $v)
			{
				$request->setHeader($k, $v);
			}
		}

		$request->fp = $fp;

		$request->getResponse();
	}

	/**
	 * Delete a file from CloudFiles
	 *
	 * @param string $container The name of the CloudFiles container
	 * @param string $path      The path to the file to download
	 *
	 * @return void
	 */
	public function deleteObject($container, $path)
	{
		// Re-authenticate if necessary
		$this->authenticate();

		// Get the URL to list containers
		$url = $this->storageEndpoint . '/' . $this->apiVersion . '/' . $this->userContract . '/' . $container;
		$url = rtrim($url, '\\/');
		$path = ltrim($path, '\\/');
		$url .= '/' . $path;

		// Get the request object
		$request = new AEUtilCloudfilesRequest('DELETE', $url);
		$request->setHeader('X-Auth-Token', $this->token);
		//$request->setHeader('Accept', 'application/json');

		$request->getResponse();
	}
}

/**
 * RESTful API request abstraction
 */
final class AEUtilCloudfilesRequest
{
	/** @var string The HTTP verb to use, e.g. GET, POST, PUT, HEAD, DELETE */
	private $verb;

	/** @var string The API URL to call */
	private $url;

	/** @var array Query string parameters */
	private $parameters = array();

	/** @var array Headers to send with the request */
	private $headers = array();

	/** @var bool|resource File pointer for GET and POST data */
	public $fp = false;

	/** @var int Size of the POST data */
	public $size = 0;

	/** @var bool|string POST data */
	public $data = false;

	/** @var null|stdClass The response object */
	public $response = null;

	/**
	 * Constructor
	 *
	 * @param string $verb Verb
	 * @param string $url  Object URI
	 *
	 * @return AEUtilCloudfilesRequest
	 */
	function __construct($verb, $url = '')
	{
		$this->verb = $verb;

		$this->url = $url;

		$this->response = new stdClass();
		$this->response->error = false;
	}

	/**
	 * Set request parameter
	 *
	 * @param string $key   Key
	 * @param string $value Value
	 *
	 * @return void
	 */
	public function setParameter($key, $value)
	{
		$this->parameters[$key] = $value;
	}


	/**
	 * Set request header
	 *
	 * @param string $key   Key
	 * @param string $value Value
	 *
	 * @return void
	 */
	public function setHeader($key, $value)
	{
		$this->headers[$key] = $value;
	}


	/**
	 * Get the response
	 *
	 * @return object | false
	 *
	 * @throws AEUtilCloudfilesExceptionHttp When something goes awry
	 */
	public function getResponse()
	{
		$query = '';

		if (sizeof($this->parameters) > 0)
		{
			$query = substr($this->url, -1) !== '?' ? '?' : '&';

			foreach ($this->parameters as $var => $value)
			{
				$addToQuery = $var . '&';

				if (!($value == null || $value == ''))
				{
					$addToQuery = $var . '=' . rawurlencode($value) . '&';
				}

				$query .= $addToQuery;
			}

			$query = substr($query, 0, -1);

			$this->url .= $query;
		}

		// Basic setup
		$curl = curl_init();

		if (defined('AKEEBA_CACERT_PEM'))
		{
			@curl_setopt($curl, CURLOPT_CAINFO, AKEEBA_CACERT_PEM);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, (stristr(PHP_OS, 'WIN') ? false : true));
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, (stristr(PHP_OS, 'WIN') ? false : true));
		}
		else
		{
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		}

		curl_setopt($curl, CURLOPT_USERAGENT, 'AkeebaBackup/4.0');
		curl_setopt($curl, CURLOPT_URL, $this->url);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 100);

		// Headers
		$headers = array();

		foreach ($this->headers as $header => $value)
		{
			if (strlen($value) > 0)
			{
				$headers[] = $header . ': ' . $value;
			}
		}

		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, false);
		curl_setopt($curl, CURLOPT_WRITEFUNCTION, array(&$this, '__responseWriteCallback'));
		curl_setopt($curl, CURLOPT_HEADERFUNCTION, array(&$this, '__responseHeaderCallback'));
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

		// Request types
		switch ($this->verb)
		{
			case 'GET':
				break;
			case 'PUT':
			case 'POST': // POST only used for CloudFront
				if ($this->fp !== false)
				{
					curl_setopt($curl, CURLOPT_PUT, true);
					curl_setopt($curl, CURLOPT_INFILE, $this->fp);
					if ($this->size >= 0)
					{
						curl_setopt($curl, CURLOPT_INFILESIZE, $this->size);
					}
				}
				elseif ($this->data !== false)
				{
					curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $this->verb);
					curl_setopt($curl, CURLOPT_POSTFIELDS, $this->data);
					if ($this->size >= 0)
					{
						curl_setopt($curl, CURLOPT_BUFFERSIZE, $this->size);
					}
				}
				else
				{
					curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $this->verb);
				}
				break;
			case 'HEAD':
				curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'HEAD');
				curl_setopt($curl, CURLOPT_NOBODY, true);
				break;
			case 'DELETE':
				curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
				break;
			default:
				break;
		}

		// Execute, grab errors
		if (curl_exec($curl))
		{
			$this->response->code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		}
		else
		{
			$this->response->error = array(
				'code'    => curl_errno($curl),
				'message' => curl_error($curl),
				'url'     => $this->url
			);
		}

		@curl_close($curl);

		// Parse body into XML
		if (
			($this->response->error === false)
			&& isset($this->response->headers['Content-Type'])
			&& (strstr($this->response->headers['Content-Type'], 'application/json') !== false)
			&& isset($this->response->body)
		)
		{
			$this->response->body = json_decode($this->response->body);
		}

		if ($this->response->error || ($this->response->code >= 400))
		{
			if (!empty($this->response->body))
			{
				$body = json_encode($this->response->body);
				$body = json_decode($body, true);

				$this->response->code = '-1';
				$this->response->error = $this->response->body;

				if (is_array($body))
				{
					$allKeys = array_keys($body);
					$firstKey = array_shift($allKeys);
					$errorInfo = $body[$firstKey];

					if (isset($errorInfo['code']))
					{
						$this->response->code = $errorInfo['code'];
					}

					if (isset($errorInfo['message']))
					{
						$this->response->error = $errorInfo['message'];
					}
					else
					{
						$this->response->error = $firstKey;
					}
				}
			}

			if (empty($this->response->error) || empty($this->response->code))
			{
				$this->response->error = 'Timeout';
				$this->response->code = 0;
			}
			throw new AEUtilCloudfilesExceptionHttp($this->response->error, $this->response->code);
		}

		// Clean up file resources
		if (($this->fp !== false) && is_resource($this->fp))
		{
			fclose($this->fp);
		}

		return $this->response;
	}


	/**
	 * CURL write callback
	 *
	 * @param resource &$curl CURL resource
	 * @param string   &$data Data
	 *
	 * @return integer
	 */
	private function __responseWriteCallback(&$curl, &$data)
	{
		if (in_array($this->response->code, array(200, 206)) && $this->fp !== false)
		{
			return fwrite($this->fp, $data);
		}
		else
		{
			$this->response->body .= $data;
		}

		return strlen($data);
	}


	/**
	 * CURL header callback
	 *
	 * @param resource &$curl CURL resource
	 * @param string   &$data Data
	 *
	 * @return integer
	 */
	private function __responseHeaderCallback(&$curl, &$data)
	{
		$strlen = strlen($data);

		if ($strlen <= 2)
		{
			return $strlen;
		}

		if (substr($data, 0, 4) == 'HTTP')
		{
			$this->response->code = (int)substr($data, 9, 3);
		}
		else
		{
			list($header, $value) = explode(': ', trim($data), 2);
			$this->response->headers[$header] = is_numeric($value) ? (int)$value : $value;
		}

		return $strlen;
	}
}