<?php

namespace Marcelklehr\LinkPreview\Parsers;

use Marcelklehr\LinkPreview\Contracts\PreviewInterface;
use Marcelklehr\LinkPreview\Contracts\ParserInterface;
use Marcelklehr\LinkPreview\Contracts\LinkInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class HtmlParser
 */
class HtmlParser implements ParserInterface {
	/**
	 * Supported HTML tags
	 *
	 * @var array
	 */
	private $tags = [
	'basic' => [
		'title' => [
			['selector' => 'meta[property="twitter:title"]', 'attribute' => 'content'],
			['selector' => 'meta[property="og:title"]', 'attribute' => 'content'],
			['selector' => 'meta[itemprop="name"]', 'attribute' => 'content'],
			['selector' => 'title']
		],

		'description' => [
			['selector' => 'meta[property="twitter:description"]', 'attribute' => 'content'],
			['selector' => 'meta[property="og:description"]', 'attribute' => 'content'],
			['selector' => 'meta[itemprop="description"]', 'attribute' => 'content'],
			['selector' => 'meta[name="description"]', 'attribute' => 'content'],
		]
	],
	'image' => [
		'small' => [
			['selector' => 'meta[property="twitter:image"]', 'attribute' => 'content'],
			['selector' => 'meta[property="og:image"]', 'attribute' => 'content'],
			['selector' => 'meta[itemprop="image"]', 'attribute' => 'content'],
		],

		'favicon' => [
		  ['selector' => 'link[rel="shortcut icon"]', 'attribute' => 'href'],
		  ['selector' => 'link[rel="icon"]', 'attribute' => 'href']
		]
	],
	'video' => [
		'url' => [
			['selector' => 'meta[property="twitter:player:stream"]', 'attribute' => 'content'],
			['selector' => 'meta[property="og:video"]', 'attribute' => 'content'],
		],

		'type' => [
			['selector' => 'meta[property="twitter:player:stream:content_type"]', 'attribute' => 'content'],
			['selector' => 'meta[property="og:video:type"]', 'attribute' => 'content'],
		]
	]
	];

	/**
	 * Smaller images will be ignored
	 * @var int
	 */
	private $smallImageMinimumSize = 200;
	private $largeImageMinimumSize = 650;

	/**
	 * @inheritdoc
	 */
	public function __toString() {
		return 'general';
	}


	/**
	 * @param int $width
	 * @param int $height
	 */
	public function setMinimumImageDimensions($small, $large) {
		$this->smallImageMinimumSize = $small;
		$this->largeImageMinimumSize = $large;
	}

	/**
	 * @inheritdoc
	 */
	public function canParseLink(LinkInterface $link) {
		return !filter_var($link->getUrl(), FILTER_VALIDATE_URL) === false;
	}

	/**
	 * @inheritdoc
	 */
	public function parseLink($res, PreviewInterface $preview) {
		$mime = $res->getHeader('Content-Type');
		if ($this->isHtml($mime)) {
			$this->parseHtml($res, $preview);
		} elseif ($this->isImage($mime)) {
			$this->parseImage($res, $preview);
		}

		return $this;
	}

	protected function isHtml($mime) {
		return !strncmp($mime, 'text/', strlen('text/'));
	}

	protected function isImage($mime) {
		return !strncmp($mime, 'image/', strlen('image/'));
	}

	/**
	 * @param PreviewInterface $link
	 * @return array
	 */
	protected function parseImage($res, PreviewInterface $preview) {
		$preview->update('image', [
			'large' => $preview->getUrl(),
			'small' =>
				$preview->getUrl()
		]);
	}

	/**
	 * Extract required data from html source
	 * @param PreviewInterface $link
	 * @return array
	 */
	protected function parseHtml($res, PreviewInterface $preview) {
		$images = [];

		try {
			$parser = new Crawler();
			$parser->addHtmlContent($res->getBody()->getContents());

			// Parse all known tags
			foreach ($this->tags as $scope => $tags) {
				$data = [];
				foreach ($tags as $tag => $selectors) {
					foreach ($selectors as $selector) {
						if ($parser->filter($selector['selector'])->count() > 0) {
							if (isset($selector['attribute'])) {
								$data[$tag] = $parser->filter($selector['selector'])->first()->attr($selector['attribute']);
							} else {
								$data[$tag] = $parser->filter($selector['selector'])->first()->text();
							}
							break;
						}
					}
				}
				$preview->update($scope, $data);
			}

			// Parse all images on this page
			foreach ($parser->filter('img') as $image) {
				if (!$image->hasAttribute('src')) {
					continue;
				}
				if (filter_var($image->getAttribute('src'), FILTER_VALIDATE_URL) === false) {
					continue;
				}

				// This is not bulletproof, actual image maybe bigger than tags
				if (
		  $image->hasAttribute('width') && $image->getAttribute('width') >= $this->smallImageMinimumSize ||
		  $image->hasAttribute('height') && $image->getAttribute('height') >= $this->smallImageMinimumSize
		) {
					$preview->update('image', ['small' => $image->getAttribute('src')]);
				}

				if (
		  $image->hasAttribute('width') && $image->getAttribute('width') >= $this->largeImageMinimumSize ||
		  $image->hasAttribute('height') && $image->getAttribute('height') >= $this->largeImageMinimumSize
		) {
					$preview->update('image', ['small' => $image->getAttribute('src')]);
				}
			}
		} catch (\InvalidArgumentException $e) {
			// Ignore exceptions
		}
	}
}
