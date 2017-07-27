<?php
namespace Skeleton\Tools\Annotation;


class Extractor
{
	use \Objection\TStaticClass;
	
	
	/**
	 * @param mixed $element
	 * @return string
	 */
	private static function getDocComment($element): string
	{
		if ($element instanceof \ReflectionClass ||
			$element instanceof \ReflectionMethod ||
			$element instanceof \ReflectionProperty)
		{
			return $element->getDocComment();
		}
		else
		{
			return (new \ReflectionClass($element))->getDocComment();
		}
	}
	
	
	/**
	 * @param mixed $element
	 * @param string|array $annotation
	 * @param bool $allowComment
	 * @return bool
	 */
	public static function has($element, $annotation, bool $allowComment = true): bool
	{
		if (is_array($annotation))
		{
			foreach ($annotation as $singleAnnotation)
			{
				if (self::has($element, $singleAnnotation, $allowComment)) 
					return true;
			}
			
			return false;
		}
		
		$pattern = $allowComment ?
			"/^[ \\t]*\\/?\\*+[ \\t]*@{$annotation}.*$/m" : 
			"/^[ \\t]*\\/?\\*+[ \\t]*@{$annotation}[ \\t]*$/m";
		
		return (preg_match($pattern, self::getDocComment($element)) == 1);
	}
	
	/**
	 * @param mixed $element
	 * @param string $annotation
	 * @return string|false
	 */
	public static function get($element, string $annotation)
	{
		$pattern = "/^[ \\t]*\\/?\\*+[ \\t]*@{$annotation} ([\\w\\\\]*).*$/m";
		$matches = [];
		$result = preg_match($pattern, self::getDocComment($element), $matches);
		
		return ($result === 1 ?
			$matches[1] : 
			false);
	}
	
	/**
	 * @param mixed $element
	 * @param string $parameterName
	 * @return string|false
	 */
	public static function getParameterType($element, string $parameterName)
	{		
		$pattern = "/^[ \\t]*\\*[ \\t]*@var ([\\w\\\\]+)[ \\t]+\\$?{$parameterName}.*$/m";
		$matches = [];
		$result = preg_match($pattern, self::getDocComment($element), $matches);
		
		return ($result === 1 ?
			$matches[1] :
			false);
	}
}