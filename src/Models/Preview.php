<?php

namespace Marcelklehr\LinkPreview\Models;

use Marcelklehr\LinkPreview\Contracts\PreviewInterface;
use Marcelklehr\LinkPreview\Traits\HasExportableFields;
use Marcelklehr\LinkPreview\Traits\HasImportableFields;

class Preview implements PreviewInterface {
	/**
   * scopes:
   *  - basic
   *      title:
   *      description:
   *  - image:
   *      favicon:
   *      small:
   *      large:
   *  - video:
   *      url:
   *      type:
   *      id:
   *      embed:
   *
	 * @var array $scopedData Link meta data
	 */
	private $scopedData = [];

	private $url;

	public function __construct($url) {
		$this->url = $url;
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
	public function update($scope, array $data) {
		if (!isset($this->scopedData[$scope])) {
			$this->scopedData[$scope] = [];
		}
		foreach ($data as $field => $value) {
			$this->scopedData[$scope][$field] = $value;
		}
	}

	/**
	 * @inheritdoc
	 */
	public function getScope($scope) {
		return $this->scopedData[$scope];
	}

	/**
	 * @inheritdoc
	 */
	public function getScopes() {
		$output = [];

		if (!isset($this->scopedData)) {
			return $output;
		}

		foreach ($this->scopedData as $scopeName => $scopeData) {
			$output[] = $scopeName;
		}

		return $output;
	}

	/**
	 * @inheritdoc
	 */
	public function toArray() {
		return $this->scopedData;
	}
}
