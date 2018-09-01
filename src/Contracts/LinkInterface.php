<?php

namespace Marcelklehr\LinkPreview\Contracts;

/**
 * Interface LinkInterface
 * @codeCoverageIgnore
 */
interface LinkInterface {
	/**
	 * LinkInterface constructor.
	 * @param string $url
	 */
	public function __construct($url, array $parsers, ClientInterface $client, RequestFactoryInterface $requestFactory);

	/**
	 * Get website url
	 * @return string
	 */
	public function getUrl();

	/**
	 * Set website url
	 * @param string $url
	 * @return $this
	 */
	public function setUrl($url);

	/**
	 * Get the preview of the link
	 * @return PreviewInterface
	 */
	public function getPreview();
}
