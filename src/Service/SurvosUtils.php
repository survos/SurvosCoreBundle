<?php

namespace Survos\CoreBundle\Service;

use Doctrine\Common\Util\ClassUtils;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\String\Slugger\SluggerInterface;

use function Symfony\Component\String\u;

class SurvosUtils
{
    public function __construct(
        private ParameterBagInterface $bag,
        private ?PropertyAccessorInterface $accessor = null,
        private ?SluggerInterface $asciiSlugger = null
    ) {
    }


    /**
     *
     * Remove projectDir from path, for easier reading.
     *
     * @param $filename
     * @return array|bool|float|int|mixed|string|string[]|\UnitEnum|null
     */
    public function cleanPath($filename)
    {
        $projectDir = $this->bag->get('kernel.project_dir');
        if (str_contains($filename, $projectDir)) {
            $filename = str_replace($projectDir, '', $filename);
        };
        $projectParent = pathinfo($projectDir, PATHINFO_DIRNAME);
        if (str_contains($filename, $projectParent)) {
            $filename = '..' . str_replace($projectParent, '', $filename);
        }
        return $filename;
    }

    public function flatten(array &$messages, array|null $subnode = null, string|null $path = null)
    {
        if (null === $subnode) {
            $subnode = &$messages;
        }
        foreach ($subnode as $key => $value) {
            if (is_array($value)) {
                $pathKey = $key;
                if (is_numeric($key)) {
                    self::assertKeyExists('code', $value);
                    $pathKey = $value['code'];
//                    unset($messages[$key]);
//                    dd($key, $value);
                }
                if (array_is_list($value)) {
//                    dd($value, $key, $subnode);
//                    $pathKey = $value[$key]['code'];
                }
                $nodePath = $path ? $path . '.' . $pathKey : $key;
                $this->flatten($messages, $value, $nodePath);
                if (is_numeric($key)) {
                    // remove code, not to be translated
//                    dd($messages, $path, $key, $pathKey, $subnode);
//                    unset($messages[$key]);
                }

                if (null === $path) {
                    unset($messages[$key]);
                }
            } elseif (null !== $path) {
                if (!in_array($key, ['code', 'id', 'icon'])) {
                    $messages[$path . '.' . $key] = $value;
                }
            }
        }
    }

    public function populateObjectFromData(mixed $object, array $data, bool $throwErrorIfMissingProperty = true)
    {
        foreach ($data as $var => $value) {
                $this->accessor->setValue($object, $var, $value);
//            try {
//            } catch (\Exception $exception) {
//                if ($throwErrorIfMissingProperty) {
//                    assert(false, "Invalid property: $var");
//                }
//            }
        }
        return $object;
    }

    public static function slugify(string $code, int $maxLength = 64, bool $forceLower = true, string $separator = '_'): string
    {
//        $code = str_replace(':', '', $code);
//        $slug = $this->asciiSlugger->slug($code, separator: $separator)->slice(0, $maxLength);
//        if ($forceLower) {
//            $slug->lower();
//        }
//        // because meili can't have periods
////        $slug = u($slug)->replace('.','-');
//        return strtolower($slug->toString());

        $code = str_replace(':','',$code);
        $slug = (new AsciiSlugger())->slug($code, separator: $separator)->slice(0, $maxLength);
        if ($forceLower) {
            $slug->lower();
        }
        // because meili can't have periods
//        $slug = u($slug)->replace('.','-');
        return strtolower($slug->toString());

    }


    public static function humanFilesize($size, $precision = 2): string
    {
        $units = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $step = 1024;
        $i = 0;
        while (($size / $step) > 0.9) {
            $size = $size / $step;
            $i++;
        }
        return round($size, $precision) . $units[$i];
    }

    public static function parseQueryString($data): array
    {
        $data = preg_replace_callback('/(?:^|(?<=&))[^=[]+/', function ($match) {
            return bin2hex(urldecode($match[0]));
        }, $data);

        parse_str((string)$data, $values);

        return array_combine(array_map('hex2bin', array_keys($values)), $values);
    }

    public static function ArrayMapKv(callable $callback, array $keyedData)
    {
        return array_map($callback, array_keys($keyedData), $keyedData);
    }

    public static function createDir($dir): string
    {
        if (!file_exists($dir)) {
            mkdir($dir, recursive: true);
        }
        return realpath($dir) . '/';
    }


    public static function actualClass(string|object $classOrEntity)
    {
        return ClassUtils::getRealClass(is_string($classOrEntity) ? $classOrEntity : $classOrEntity::class);
    }


    public static function trimmer(string $label): string
    {
        return trim($label, " \n\r\t\v\x00.;,/");
    }

    public static function missingKey($key, $array): string
    {
        $keys = array_keys($array);
        return self::missingElement($key, $keys);
    }

    public static function missingElement($key, $keys): string
    {
        sort($keys, SORT_STRING);
        return sprintf("Missing [%s]:\n%s", $key, join("\n", $keys));
    }


    public static function assertKeyExists($key, array|object $array, string $message = '')
    {
        if (is_object($array)) {
            $array = (array)$array;
        }
        assert(array_key_exists($key, $array), self::missingKey($key, $array) . "\n$message");
    }

    public static function assertInArray($key, array $array, string $message = '')
    {
        assert(in_array($key, $array), self::missingElement($key, $array) . "\n$message");
    }


    public function validate(null|iterable|object $obj, $msg = '')
    {
        return; // hack, problem with DictionaryValidator!
//        if (!$obj) {
//            return;
//        }
//        if (is_iterable($obj)) {
//            foreach ($obj as $item) {
//                $this->validate($item);
//            }
//        } else {
//            $errors = $this->validator->validate($obj);
//            if ($errors->count()) {
//                foreach ($errors as $error) {
////                    dd( $msg . "\n" . (string) $error);
//                    assert(false, (string)$msg . "\n" . (string)$error);
//                }
//                assert(!$errors->count(), (string)$msg . "\n" . (string)$errors);
//            }
//        }
    }

    public static function shortClass(string|object $class): string
    {
        return (new \ReflectionClass($class))->getShortName();
    }

    public static function dd($values)
    {
        dd($values);
    }

    public static function createAcronym(string $string, $onlyCapitals = false): ?string
    {
        $output = null;
        $token = strtok($string, ' ');
        while ($token !== false) {
            $character = mb_substr($token, 0, 1);
            if ($onlyCapitals and mb_strtoupper($character) !== $character) {
                $token = strtok(' ');
                continue;
            }
            $output .= $character;
            $token = strtok(' ');
        }
        return $output;
    }

    // https://stackoverflow.com/questions/4352203/any-php-function-that-will-strip-properties-of-an-object-that-are-null
    public static function cleanNullsOfObject(&$object) {
        foreach ($object as $property => &$value) {
            if (is_object($value)) {
                self::cleanNullsOfObject($value);
                if (empty(get_object_vars($value))) {
                    unset($object->$property);
                }
            }
            // check for array of objects
            if (is_array($value) && array_is_list($value)) {
                foreach ($value as $val) {
                    if (is_object($val)) {
                        self::cleanNullsOfObject($val);
                    }
                }

            }
//            if (is_array($value) && is_object($value[0])) {
//                foreach ($value as $val) {
//                    self::cleanNullsOfObject($val);
//                }
//            }
            if (is_null($value) || ( (is_string($value) && $value === '') || is_array($value) && empty($value))) {
                unset($object->$property);
            }
        }
    }

}
