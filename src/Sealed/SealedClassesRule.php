<?php declare(strict_types = 1);

namespace Grifart\PHPStanRules\Sealed;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Broker\Broker;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Rules\Rule;
use PHPStan\Type\FileTypeMapper;

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

		$sealedParent = $this->utils->getSealedParent($node->namespacedName->toString());
		if ($sealedParent === NULL) {
			return [];
		}

		// todo: Or allow the same namespace?
		// todo: Or to name all allowed parent?
		if ($scope->getFile() !== $sealedParent->getFileName()) {
			return [
				\sprintf(
					'You cannot extend sealed class %s outside of declaring file %s.',
					$sealedParent->getName(),
					$sealedParent->getFileName()
				)
			];
		}

		return [];
	}


}
