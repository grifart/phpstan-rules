<?php declare(strict_types = 1);


namespace Grifart\PHPStanRules\Sealed;


use PHPStan\Reflection\ClassReflection;
use PHPStan\Type\FileTypeMapper;

final class SealedUtils
{

	/** @var FileTypeMapper */
	private $docBlockAnalyzer;

	public function __construct(FileTypeMapper $docBlockAnalyzer)
	{
		$this->docBlockAnalyzer = $docBlockAnalyzer;
	}

	/**
	 * Resolves if given class is sealed.
	 */
	public function isSealed(ClassReflection $classReflection): bool {
		$docBlock = $classReflection->getNativeReflection()->getDocComment();
		if ($docBlock === FALSE) {
			return FALSE;
		}

		$docBlock = $this->docBlockAnalyzer->getResolvedPhpDoc(
			$classReflection->getFileName(),
			$classReflection->getName(),
			null,
			null,
			$docBlock
		);
		$tags = $docBlock->getPhpDocNode()->getTagsByName('@sealed');
		return count($tags) > 0;
	}

	/**
	 * Search for the sealed parent in class hierarchy
	 */
	public function findSealedParentReflection(ClassReflection $reflection): ?ClassReflection {
		do {
			if ($this->isSealed($reflection)) {
				return $reflection;
			}
		} while (($reflection = $reflection->getParentClass()) !== FALSE);
		return NULL;
	}

}
