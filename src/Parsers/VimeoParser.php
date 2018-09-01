<?php

namespace Marcelklehr\LinkPreview\Parsers;

use Marcelklehr\LinkPreview\Contracts\LinkInterface;
use Marcelklehr\LinkPreview\Contracts\ParserInterface;
use Marcelklehr\LinkPreview\Contracts\PreviewInterface;

/**
 * Class YouTubeParser
 */
class VimeoParser implements ParserInterface {
	/**
	 * Url validation pattern based on http://stackoverflow.com/questions/13286785/get-video-id-from-vimeo-url/22071143#comment48088417_22071143
	 */
	const PATTERN = '/^.*(?:vimeo.com)\\/(?:channels\\/|groups\\/[^\\/]*\\/videos\\/|album\\/\\d+\\/video\\/|video\\/|)(\\d+)(?:$|\\/|\\?)/';

	/**
	 * @inheritdoc
	 */
	public function __toString() {
		return 'vimeo';
	}

	/**
	 * @inheritdoc
	 */
	public function canParseLink(LinkInterface $link) {
		return (preg_match(static::PATTERN, $link->getUrl()));
	}

	/**
	 * @inheritdoc
	 */
	public function parseLink($res, PreviewInterface $preview) {
		preg_match(static::PATTERN, $preview->getUrl(), $matches);

		if (isset($matches[1])) {
			$preview->update('video', [
			  'id'=> $matches[1],
			  'embed' => '<iframe id="viplayer" width="640" height="390" src="//player.vimeo.com/video/'.$matches[1].'"" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>'
		  ]);
		}
	}
}
