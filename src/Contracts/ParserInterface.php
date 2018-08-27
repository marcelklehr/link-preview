<?php

namespace Marcelklehr\LinkPreview\Contracts;

/**
 * Interface ParserInterface
 * @codeCoverageIgnore
 */
interface ParserInterface {

	/**
	 * Parsers name
	 * @return string
	 */
	public function __toString();

	/**
	 * Can this parser parse the link supplied?
	 * @param LinkInterface $link
	 * @return boolean
	 */
	public function canParseLink(LinkInterface $link);

	/**
	 * Parse link
	 * @param PreviewInterface $link
	 * @return $this
	 */
	public function parseLink(PreviewInterface $link);
}
