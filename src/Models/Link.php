<?php

namespace Marcelklehr\LinkPreview\Models;

use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Client\ClientInterface;
use Marcelklehr\LinkPreview\Contracts\LinkInterface;
use Marcelklehr\LinkPreview\Exceptions\MalformedUrlException;
use Marcelklehr\LinkPreview\Exceptions\ConnectionErrorException;

/**
 * Class Link
 */
class Link implements LinkInterface {
	/**
	 * @var string $url
	 */
	private $url;

	/**
	 * @var array $parsers
	 */
	private $parsers;

	/**
	 * @var ClientInterface $client
	 */
	private $client;

	/**
	 * @var RequestFactoryInterface  $requestFactory
	 */
	private $requestFactory;

	/**
	 * @param string $url
	 * @throws MalformedUrlException
	 */
	public function __construct($url, array $parsers, ClientInterface $client, RequestFactoryInterface $requestFactory) {
		if (filter_var($url, FILTER_VALIDATE_URL) === false) {
			throw new MalformedUrlException();
		}

		$this->client = $client;
		$this->parser = $parsers;
		$this->requestFactory = $requestFactory;
		$this->setUrl($url);
	}

	protected function fetch() {
		$url = $this->getUrl();
		do {
			if (isset($res)) {
				if ($res->getStatus() === 301 || $res->getStatus() === 302) {
					$url = $res->getHeader('Location');
				} else {
					throw new ConnectionErrorException("Server returned error: ".$res->getStatus());
				}
			}
			$res = $this->client->sendRequest($this->requestFactory->createRequest('GET', $url));
		} while ($res->getStatus() !== 200);
		return $res;
	}

	/**
	 * @inheritdoc
	 */
	public function getPreview() {
		$res = $this->fetch();
		$preview = new Preview($this->getUrl());
		foreach ($this->parsers as $parserName => $parser) {
			if ($parser->canParseLink($this)) {
				continue;
			}
			$parser->parseLink($res, $preview);
		}
		return $preview;
	}

	/**
	 * @inheritdoc
	 */
	public function getUrl() {
		return $this->url;
	}

	/**
	 * @inheritdoc
	 */
	public function setUrl($url) {
		$this->url = $url;

		return $this;
	}
}
