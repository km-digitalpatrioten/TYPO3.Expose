<?php
namespace TYPO3\Admin\TypoScript\Processors;

/*                                                                        *
 * This script belongs to the TYPO3.Admin package.              		  *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * Manipulate the context variable "objects", which we expect to be a QueryResultInterface;
 * taking the "page" context variable into account (on which page we are currently).
 *
 */
class SearchProcessor implements \TYPO3\TypoScript\RuntimeAwareProcessorInterface {

	public function beforeInvocation(\TYPO3\TypoScript\Core\Runtime $runtime, \TYPO3\TypoScript\TypoScriptObjects\AbstractTsObject $typoScriptObject, $typoScriptPath) {
		$this->tsRuntime = $runtime;
		$context = $runtime->getCurrentContext();
		$search = $this->getSearch();
		if (isset($context['objects']) && $search !== NULL) {
			$query = $context['objects']->getQuery();

			$query->matching($query->like('*', $search));

			$runtime->pushContext('objects', $query->execute());
		}
	}

	public function getSearch() {
		$request = $this->tsRuntime->getControllerContext()->getRequest();
		if ($request->hasArgument("search")) {
			return $request->getArgument("search");
		}
		return NULL;
	}

	public function process($subject) {
		return $subject;
	}

	public function afterInvocation(\TYPO3\TypoScript\Core\Runtime $runtime, \TYPO3\TypoScript\TypoScriptObjects\AbstractTsObject $typoScriptObject, $typoScriptPath) {
		if (isset($context['objects'])) {
			$runtime->popContext();
		}
	}
}
?>