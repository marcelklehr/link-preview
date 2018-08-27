<?php

namespace Marcelklehr\LinkPreview\Contracts;

/**
 * Interface PreviewInterface
 * @codeCoverageIgnore
 */
interface PreviewInterface {
	/**
   * Returns the URl of the previewed page
	 * @return string
	 */
	public function getUrl();

	/**
	 * Return a list of all populated scopes
	 * @return array
	 */
	public function getScopes();

	/**
	 * Return an array of data for the specified scope
	 * @return array
	 */
	public function getScope($scope);

	/**
	 * Mass assignment of class properties per scope
   * @param string $scope
	 * @param array $params
	 * @return $this
	 */
	public function update($scope, array $params);

	/**
	 * Return all parsed data as an array
	 * @return array
	 */
	public function toArray();
}
