<?php
/**
 * Yet another CSV Reader.
 * Created by [istranger] [2017-08-19 12:45]
 */

declare (strict_types=1);

namespace common\csv\helpers;

use common\helpers\ArrayHelper;
use common\helpers\CommonHelper;
use common\helpers\StringHelper;

/**
 * The class provides methods for working with Yii models.
 */
abstract class CsvReaderHelper
{
    /**
     * Trims strings items.
     *
     * NOTE: works only with the one dimensional scalar array.
     *
     * @param string[] $array    List of strings that will be trimmed.
     * @param string   $charlist Optionally, the stripped characters can also be specified using the charlist parameter.
     *                           Simply list all characters that you want to be stripped.
     *                           With .. you can specify a range of characters.
     *
     * @return array
     */
    public static function trimStrings(array $array, string $charlist = " \t\n\r\0\x0B"): array
    {
        return ArrayHelper::trim($array, $charlist);
    }


    /**
     * Returns first element of array.
     *
     * @param array      $array
     * @param mixed|null $defaultValue
     *
     * @return mixed First element of array or =null if array is empty.
     */
    public static function getFirst(array $array, $defaultValue = null)
    {
        return ArrayHelper::getFirst($array) ?? $defaultValue;
    }

    /**
     * Checks whether specified array contains only scalar values {@link is_scalar()}.
     *
     * NOTE: "scalar array" means that array is one-dimensional, however it may be indexed or associative
     * (see {@link self::isIndexed()} and {@link self::isAssociative()}).
     *
     * NOTE: empty array is always scalar.
     *
     * @param array|mixed $arrayToCheck
     *
     * @return bool
     */
    public static function isScalarArray($arrayToCheck): bool
    {
        return ArrayHelper::isScalar($arrayToCheck);
    }

    /**
     * Replace placeholders in the specified text.
     *
     * @param string $text                  Source text. For example: 'Text with some {placeholder} that will be replaced.'.
     * @param array  $placeholdersToReplace In format: ['placeholder' => 'replaceTo']
     * @param string $placeholderPrefix
     * @param string $placeholderSuffix
     *
     * @return string
     */
    public static function replacePlaceholders(string $text, array $placeholdersToReplace, string $placeholderPrefix = '{', string $placeholderSuffix = '}'): string
    {
        return StringHelper::replacePlaceholders($text, $placeholdersToReplace, $placeholderPrefix, $placeholderSuffix);
    }

    /**
     * Checks whether specified  $classToCheckInstance is instantiated from $parentClassName.
     *
     * @param string|\stdClass $classToCheckInstance Either a string containing the name of the class to reflect, or an object.
     * @param string           $parentClassName      The class name being checked against.
     *
     * @return bool
     */
    public static function isInstanceOf($classToCheckInstance, string $parentClassName)
    {
        return CommonHelper::checkInstanceOf($classToCheckInstance, $parentClassName);
    }
}