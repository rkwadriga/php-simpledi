<?php declare(strict_types=1);

namespace rkwadiga\simpledi;

use rkwadiga\simpledi\exception\PhpDocReaderException;

class PhpDocReader
{
    private array $skippedWords = ['package'];

    public function read(string $doc) : array
    {
        $res = [];
        $docArr = array_slice(explode("\n", $doc), 1, -1);
        foreach ($docArr as $str) {
            $str = trim($str);
            if (empty($str)) {
                continue;
            }
            $continue = false;
            foreach ($this->skippedWords as $word) {
                if (strpos($str, "@{$word}") !== false) {
                    $continue = true;
                    continue;
                }
            }
            if ($continue) {
                continue;
            }
            if (!preg_match('/@(\w+)(.*)/', $str, $matches)) {
                continue;
            }
            if (!in_array($matches[1], [Container::SINGLETON, Container::TRANSIENT, Container::SCOPED])) {
                throw new PhpDocReaderException(sprintf('Invalid behavior: "%s"', $matches[1]), PhpDocReaderException::INVALID_BEHAVIOR);
            }
            $res = array_merge(['behavior' => $matches[1]], $this->convertParamsStringToArray($matches[2]));
            break;
        }
        return $res;
    }

    private function convertParamsStringToArray(string $str) : array
    {
        $res = [];
        if (empty($str)) {
            return $res;
        }
        if (substr($str, 0, 1) !== '(' || substr($str, -1) !== ')') {
            throw new PhpDocReaderException("Invalid PHPDoc format in string \"{$str}\"", PhpDocReaderException::INVALID_FORMAT);
        }
        // Clear string
        $str = substr($str, 1, -1);
        // Read all string values
        $stringValPattern = '/([\w\d_]+)=[\'|"]([\w\d\/\\\_ {}\(\)\[\]&$#!?,.+=%*:;><\|~\^-]+)[\'|"]/';
        while (!empty($str) && preg_match($stringValPattern, $str, $matches)) {
            $this->clearStringFromFoundParam($str, $matches[0]);
            $res[$matches[1]] = $matches[2];
        }

        // Read all int values
        $intValPattern = '/([\w\d_]+)=(\d+.\d+|\d+)/';
        while (!empty($str) && preg_match($intValPattern, $str, $matches)) {
            $this->clearStringFromFoundParam($str, $matches[0]);
            $res[$matches[1]] = strpos($matches[2], '.') === false ? (int)$matches[2] : (float)$matches[2];
        }

        // Read all array values
        $arrayValPattern = '/([\w\d_]+)=({|\[)(.+)(}|\])/';
        while (!empty($str) && preg_match($arrayValPattern, $str, $matches)) {
            $this->clearStringFromFoundParam($str, $matches[0]);
            $jsonString = $matches[2] . $matches[3] . $matches[4];
            $arrayValue = json_decode($jsonString, true);
            if ($arrayValue === null) {
                throw new PhpDocReaderException(sprintf('Invalid json format: "%s" (%s, %s)',
                    $jsonString,
                    json_last_error(),
                    json_last_error_msg()
                ), PhpDocReaderException::INVALID_JSON);
            }
            $res[$matches[1]] = $arrayValue;
        }

        return $res;
    }

    private function clearStringFromFoundParam(&$str, $found) : void
    {
        $str = str_replace($found, '', $str);
        if (in_array(substr($str, 0, 1), [',', ';'])) {
            $str = trim(substr($str, 1));
        }
    }
}