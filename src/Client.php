<?php

namespace Marcelklehr\LinkPreview;

use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Client\ClientInterface;
use Marcelklehr\LinkPreview\Contracts\ParserInterface;
use Marcelklehr\LinkPreview\Contracts\PreviewInterface;
use Marcelklehr\LinkPreview\Parsers\HtmlParser;
use Marcelklehr\LinkPreview\Parsers\YouTubeParser;
use Marcelklehr\LinkPreview\Parsers\VimeoParser;
use Marcelklehr\LinkPreview\Models\Link;
use Marcelklehr\LinkPreview\Exceptions\UnknownParserException;

class Client {
	/**
	 * @var ParserInterface[]
	 */
	private $parsers = [];

	/**
	 * @var ClientInterface $client
	 */
	private $client;

	/**
	 * @var RequestFactoryInterface $requestFactory
	 */
	private $requestFactory;

	/**
	 * @param string $url Request address
	 */
	public function __construct(ClientInterface $client, RequestFactoryInterface $requestFactory) {
		$this->client = $client;
		$this->requestFactory = $requestFactory;
		$this->addDefaultParsers();
	}

	/**
	 * Add parser to the beginning of parsers list
	 *
	 * @param ParserInterface $parser
	 * @return $this
	 */
	public function addParser(ParserInterface $parser) {
		$this->parsers = [(string) $parser => $parser] + $this->parsers;

		return $this;
	}

	/**
	 * @param $id
	 * @return bool|ParserInterface
	 */
	public function getParser($id) {
		return isset($this->parsers[$id]) ? $this->parsers[$id] : false;
	}

	/**
	 * Get parsers
	 * @return ParserInterface[]
	 */
	public function getParsers() {
		return $this->parsers;
	}

	/**
	 * Set parsers
	 * @param ParserInterface[] $parsers
	 * @return $this
	 */
	public function setParsers($parsers) {
		$this->parsers = $parsers;

		return $this;
	}

	/**
	 * @return Link
	 */
	public function getLink($url) {
		return new Link($url, $this->parsers, $this->client, $this->requestFactory);
	}

	/**
	 * Remove parser from parsers list
	 *
	 * @param string $name Parser name
	 * @return $this
	 */
	public function removeParser($name) {
		if (in_array($name, $this->parsers, false)) {
			unset($this->parsers[$name]);
		}

		return $this;
	}

	/**
	 * Add default parsers
	 * @return void
	 */
	protected function addDefaultParsers() {
		$this->addParser(new HtmlParser());
		$this->addParser(new YouTubeParser());
		$this->addParser(new VimeoParser());
	}
}
