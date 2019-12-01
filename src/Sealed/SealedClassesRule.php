<?php declare(strict_types = 1);

namespace Grifart\PHPStanRules\Sealed;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Broker\Broker;
use PHPStan\Broker\ClassNotFoundException;
use PHPStan\Rules\Rule;

/**
 * @implements \PHPStan\Rules\Rule<\PhpParser\Node\Stmt\Class_>
 */
class SealedClassesRule implements Rule {

	/**
	 * @var Broker
	 */
	private $broker;

	/**
	 * @var SealedUtils
	 */
	private $utils;


	public function __construct(Broker $broker, SealedUtils $utils)
	{
		$this->broker = $broker;
		$this->utils = $utils;
	}


	public function getNodeType(): string
	{
		return Node\Stmt\Class_::class;
	}

	public function processNode(Node $node, Scope $scope): array
	{
		\assert($node instanceof Node\Stmt\Class_);
		if ($node->extends === NULL) {
			return [];
		}

		try {
			$parentReflection = $this->broker->getClass($node->extends->toString());

		} catch (ClassNotFoundException $e) {
			// Could not find parent class, this is strange.
			// Anyway this should be checked by PHPStan internal rules
			// so just ignore it for now
			return [];
		}
		$sealedParent = $this->utils->findSealedParentReflection($parentReflection);

		if ($sealedParent === NULL) {
			return [];
		}

		// todo: Or allow the same namespace?
		// todo: Or to name all allowed parent?
		if ($scope->getFile() !== $sealedParent->getFileName()) {
			return [
				\sprintf(
					"Class %s cannot extend sealed class %s outside of declaring file %s.\nSee https://kotlinlang.org/docs/reference/sealed-classes.html for more information what sealed classes are.",
					!$node->isAnonymous() ? $node->namespacedName->toString() : '<anonymous>',
					$sealedParent->getName(),
					$sealedParent->getFileName()
				)
			];
		}

		return [];
	}


}
