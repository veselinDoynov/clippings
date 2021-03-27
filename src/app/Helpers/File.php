<?php

namespace App\Helpers;

class File
{
    /**
     * @param array $data
     * @param array $headersMapping
     * @return array
     * @throws \Exception
     */
    public static function formatForUserImport(array $data, array $headersMapping): array
    {
        $headers = array_shift($data);
        $fields = [];


        if (!empty(array_diff(array_keys($headersMapping), $headers))) {
            throw new \Exception('Invalid headers.', 400);
        }

        foreach ($headers as $key => $header) {
            if (empty($headersMapping[$header])) {
                continue;
            }
            $fields[$key] = $headersMapping[$header];
        }

        foreach ($data as $i => $row) {
            $data[$i] = array_combine($fields, array_slice($row, 0, count($fields)));
        }

        return $data;
    }
}

