<?php

namespace Marcelklehr\LinkPreview\Parsers;

use Marcelklehr\LinkPreview\Contracts\LinkInterface;
use Marcelklehr\LinkPreview\Contracts\ParserInterface;
use Marcelklehr\LinkPreview\Contracts\PreviewInterface;

/**
 * Class YouTubeParser
 */
class YouTubeParser implements ParserInterface {
	/**
	 * Url validation pattern taken from http://stackoverflow.com/questions/2964678/jquery-youtube-url-validation-with-regex
	 */
	const PATTERN = '/^(?:https?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})(?:\S+)?$/';


	/**
	 * @inheritdoc
	 */
	public function __toString() {
		return 'youtube';
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
			  'embed' => '<iframe id="ytplayer" type="text/html" width="640" height="390" src="//www.youtube.com/embed/'.$matches[1].'" frameborder="0"></iframe>'
		  ]);
		}
	}
}
