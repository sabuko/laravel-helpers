<?php

use Illuminate\Support\Collection;


/**
 * It calls on all callbacks to the first, which did not return null.
 * The resulting value is the result of the function and returns.
 *
 * @param callable $callbacks
 *
 * @return mixed
*/
function elseChain(...$callbacks) {
    foreach ($callbacks as $callback) {
        $value = $callback();

        if (!empty($value)) {
            return $value;
        }
    }

    return null;
}

/**
 * Add missing zeros in beginning on zip-string. As example 123 will be 00123
 *
 * @param int $zip
 * @return string
 */
function toZip($zip) {
    $convertedZip = (string)$zip;

    while(strlen($convertedZip) < 5) {
        $convertedZip = "0{$convertedZip}";
    }

    return $convertedZip;
}

/**
 * Round all values in list of floats.
 *
 * @param array $array
 * @return array
 */
function array_round($array) {
    $keys = array_keys($array);

    $values = array_map(function ($value) {
        return round($value);
    }, $array);

    return array_combine($keys, $values);
}

/**
 * Get value of key from every item in list and return list of them
 *
 * @param array|string $array
 * @param string $key
 *
 * @return array
 */
function array_lists($array, $key) {
    return array_map(function ($item) use ($key) {
        return array_get($item, $key);
    }, $array);
}

/**
 * Get list of element which placed in $path in $array
 *
 * @param array|string $array
 * @param string $path
 * @param array $oldKeys
 *
 * @return mixed
 */
function array_get_list($array, $path, $oldKeys = []) {
    if (is_string($path)) {
        $path = explode('.', $path);
    }

    $key = array_shift($path);
    $oldKeys[] = $key;

    if (empty($path)) {
        return array_get($array, $key);
    }

    if ($key == '*') {
        $values = array_map(function ($item) use ($path, $oldKeys) {
            $value = array_get_list($item, $path, $oldKeys);

            if (!is_array($value)) {
                return [$value];
            }

            return $value;
        }, $array);

        return array_collapse($values);
    } else {
        $value = array_get($array, $key);

        return array_get_list($value, $path, $oldKeys);
    }
}

/**
 * Verifies whether an associative array or a list
 *
 * @param array $array
 *
 * @return boolean
 */
function isAssociative($array) {
    return $array !== array_values($array);
}

/**
 * Create directory recursively. The native mkdir() function recursively create directory incorrectly.
 * This is solution.
 *
 * @param string $path
 */
function mkdir_recursively($path) {
    $explodedPath = explode('/', $path);

    $currentPath = $explodedPath[0];

    array_walk($explodedPath, function ($dir) use (&$currentPath) {
        if ($currentPath != '/') {
            $currentPath .= '/'.$dir;
        } else {
            $currentPath .= $dir;
        }

        if (!file_exists($currentPath)) {
            mkdir($currentPath);
        }
    });
}

/**
 * Check equivalency of two arrays
 *
 * @param array $array1
 * @param array $array2
 *
 * @return boolean
 */
function array_equals($array1, $array2) {
    $collection1 = (new Collection($array1))->sort();
    $collection2 = (new Collection($array2))->sort();

    return $collection1->values() == $collection2->values();
}

/**
 * Return subsctaction of two arrays
 *
 * @param array $array1
 * @param array $array2
 *
 * @return array
 */
function array_subtraction($array1, $array2) {
    $intersection = array_intersect($array1, $array2);

    return array_diff($array1, $intersection);
}

/**
 * Generate GUID
 *
 * @return string
 */
function getGUID() {
    mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
    $charid = strtoupper(md5(uniqid(rand(), true)));
    $hyphen = chr(45);// "-"
    $uuid = chr(123)// "{"
        .substr($charid, 0, 8).$hyphen
        .substr($charid, 8, 4).$hyphen
        .substr($charid,12, 4).$hyphen
        .substr($charid,16, 4).$hyphen
        .substr($charid,20,12)
        .chr(125);// "}"
    return $uuid;
}
