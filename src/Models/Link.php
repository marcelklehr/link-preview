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
		$this->parsers = $parsers;
		$this->requestFactory = $requestFactory;
		$this->setUrl($url);
	}

    /**
     * @throws \Marcelklehr\LinkPreview\Exceptions\ConnectionErrorException
     */
    protected function fetch() {
		$url = $this->getUrl();
		do {
			if (isset($res)) {
				if (($res->getStatusCode() === 301 || $res->getStatusCode() === 302) && $res->hasHeader('Location')) {
					$url = $res->getHeader('Location')[0];
				} else {
					throw new ConnectionErrorException("Server returned error: ".$res->getStatusCode());
				}
			}
			$res = $this->client->sendRequest($this->requestFactory->createRequest('GET', $url));
		} while ($res->getStatusCode() !== 200);
		return $res;
	}

    /**
     * @inheritdoc
     * @throws \Marcelklehr\LinkPreview\Exceptions\ConnectionErrorException
     */
	public function getPreview() {
		$res = $this->fetch();
		$preview = new Preview($this->getUrl());
		foreach ($this->parsers as $parserName => $parser) {
			if (!$parser->canParseLink($this)) {
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
