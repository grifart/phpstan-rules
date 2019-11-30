<?php declare(strict_types = 1);


namespace Grifart\PHPStanRules\Sealed;


use PHPStan\Broker\Broker;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Type\FileTypeMapper;

final class SealedUtils
{

	/** @var FileTypeMapper */
	private $docBlockAnalyzer;

	/** @var Broker */
	private $broker;

	public function __construct(FileTypeMapper $docBlockAnalyzer, Broker $broker)
	{
		$this->docBlockAnalyzer = $docBlockAnalyzer;
		$this->broker = $broker;
	}

	/**
	 * Resolves if given class is sealed.
	 */
	public function isSealed(ClassReflection $classReflection): bool {
		$docBlock = $this->docBlockAnalyzer->getResolvedPhpDoc(
			$classReflection->getFileName(),
			$classReflection->getName(),
			null,
			null,
			$classReflection->getNativeReflection()->getDocComment()
		);
		$tags = $docBlock->getPhpDocNode()->getTagsByName('@sealed');
		return count($tags) > 0;
	}

	/**
	 * Search for the sealed parent in class hierarchy
	 */
	public function getSealedParent(string $className): ?ClassReflection {
		$classReflection = $this->broker->getClass($className);
		while (($parent = $classReflection->getParentClass()) !== FALSE) {
			if ($this->isSealed($parent)) {
				return $parent;
			}
		}
		return NULL;
	}

}
