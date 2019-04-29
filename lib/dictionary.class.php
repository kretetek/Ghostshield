<?php

class definition {

	// Oxford Dictonary API
	// https://od-api.oxforddictionaries.com/api/v1
	// App ID 1926bdbe
	// App Key

	private $key;
	private $id;
	private $base;
	private $url;
	private $attributes;
	private $word;
	public $response;

	function __construct($word) {
	
		global $page;
	
		$this->url		= "https://od-api.oxforddictionaries.com:443/api/v1";
		$this->id		= "1926bdbe";
		$this->key		= "12f35d18d4aeb3dd40b10c0467cf32df";
		$this->page 	= $page;
		
		$this->word		= strtolower($word);
		
		$this->response = $this->get($word);
		$this->parseResponse();
	
	}
	
	function headers() {
	
		$headers = [
			"Accept: application/json",
			"app_id: ".$this->id,
			"app_key: ".$this->key
		];
		return $headers;
	
	}

	function get($word) {
	
		$cache = db::_select("cache", ["where" => ["site" => ($this->page->dev?"dev":"pub"), "var" => "glossary-".$word], "order_by" => "_created", "order_direction" => "DESC"]);
		
		if (isset($cache[0]["data"])) return unserialize($cache[0]["data"]);
		
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $this->url."/entries/en/".urlencode(strtolower($word)));
		curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers());
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$response = curl_exec($ch);
		curl_close($ch);
		
		$result = json_decode($response);
		
		db::_insert("cache", ["site" => $this->page->env, "var" => "glossary-".$word, "type" => "glossary", "data" => serialize($result)]);
		
		return $result;

	}
	
	function parseResponse() {

		if (isset($this->response) && sizeof($this->response->results) > 0) {
			if (isset($this->response->results[0]->lexicalEntries[0]->entries[0]->senses) && sizeof($this->response->results[0]->lexicalEntries[0]->entries[0]->senses[0]) > 0) {
		/* && sizeof($this->response->results->lexicalEntries->entries->senses) > 0*/
			//$this->attributes["defintion"] = $this->response["results"]["lexicalEntries"]["entries"]["senses"]["definitions"];
				$this->attributes["word"] = $this->response->results[0]->id;
				$this->attributes["definition"] = $this->response->results[0]->lexicalEntries[0]->entries[0]->senses[0]->definitions[0];
				if (isset($this->response->results[0]->lexicalEntries[0]->entries[0]->senses[0]->domains)) $this->attributes["domain"] = $this->response->results[0]->lexicalEntries[0]->entries[0]->senses[0]->domains[0];
				if (isset($this->response->results[0]->lexicalEntries[0]->entries[0]->etymologies)) $this->attributes["etymology"] = $this->response->results[0]->lexicalEntries[0]->entries[0]->etymologies[0];
				$this->attributes["provider"] = $this->response->metadata->provider;
			
			} elseif (isset($this->response->results[0]->lexicalEntries[0]->derivativeOf) && sizeof($this->response->results[0]->lexicalEntries[0]->derivativeOf[0]) > 0) {
			
	//			error_log("recursively looking up definition for ".$this->response->results[0]->lexicalEntries[0]->derivativeOf[0]->id);
				$this->response = $this->get($this->response->results[0]->lexicalEntries[0]->derivativeOf[0]->id);
				$this->parseResponse(); // recursive (blow-back?)
			
			}
		
		}

	}
	
	public function definition() {
	
		return	'<h3>'.$this->attributes["word"].($this->attributes["word"] != $this->word ? ' ('.$this->word.')' : '')."</h3>".
				(isset($this->page->data("glossary")->attr[$this->word]) ? '<p class="self_defined">'.$this->page->data("glossary")->attr[$this->word]["definition"].'</p>'.
				'<p class="provider"><i>'.(isset($this->page->data("glossary")->attr[$this->word]["source"])?$this->page->data("glossary")->attr[$this->word]["source"]:"Ghostshield.com").'</i></p>' : '').
			(!isset($this->page->data("glossary")->attr[$this->word]) || $this->page->data("glossary")->attr[$this->word]["dictionary"] !== false ?
				'<p>'.$this->attributes["definition"].(isset($this->attributes["domain"]) ? ' ('.$this->attributes["domain"].')' : '').'</p>'.
				(isset($this->attributes["etymology"]) > "" ? '<p>Etymology: '.$this->attributes["etymology"].'</p>' : '').
				'<p class="provider"><i>'.$this->attributes["provider"].'</i></p>' : '');
	
	}

}
