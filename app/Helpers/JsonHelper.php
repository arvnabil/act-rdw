<?php

namespace App\Helpers;

use Illuminate\Support\Arr;

class JsonHelper
{
    /**
     * Nests a flat associative array into a multi-dimensional array based on a separator.
     * 
     * @param array|string|null $flat The flat array or JSON string.
     * @param string $rootKey The root key to wrap everything in (e.g. 'Spesifikasi').
     * @param string $separator The separator to split keys (default ' - ').
     * @return array
     */
    public static function nest($flat, string $rootKey, string $separator = ' - '): array
    {
        if (is_string($flat)) {
            $flat = json_decode($flat, true) ?: [];
        }

        if (empty($flat) || !is_array($flat)) {
            return [$rootKey => []];
        }

        $nested = [];

        foreach ($flat as $key => $value) {
            // Case 1: Key has separator (e.g. "Dimensi - Tinggi")
            if (str_contains($key, $separator)) {
                $parts = explode($separator, $key);
                $group = trim($parts[0]);
                $item = trim(implode($separator, array_slice($parts, 1)));
                
                if (!isset($nested[$group])) {
                    $nested[$group] = [];
                }
                $nested[$group][$item] = $value;
            } 
            // Case 2: Value is already an array (e.g. "Spesifikasi & Perincian" => [...])
            // Treat key as the group name.
            else if (is_array($value)) {
                $nested[$key] = $value;
            }
            // Case 3: Flat key, scalar value -> put in General
            else {
                if (!isset($nested['General'])) {
                    $nested['General'] = [];
                }
                $nested['General'][$key] = $value;
            }
        }

        return [$rootKey => $nested];
    }

    /**
     * Normalizes data to ensure it is nested under the root key.
     * If the root key is missing, it assumes the data is flat and nests it.
     * 
     * @param array|null $data
     * @param string $rootKey
     * @return array
     */
    public static function normalize(array|null $data, string $rootKey): array
    {
        if (empty($data)) {
            return [$rootKey => []];
        }

        if (isset($data[$rootKey])) {
            return $data;
        }

        // If root key missing, nest the whole thing
        return self::nest($data, $rootKey);
    }

    /**
     * Flattens a nested array back into a format suitable for Filament's Repeater of KeyValues.
     * 
     * @param array|null $nested The nested array from database.
     * @param string $rootKey The root key to look for.
     * @return array In format [['group_name' => '...', 'items' => [...]]]
     */
    public static function toRepeater(array|null $nested, string $rootKey): array
    {
        $normalized = self::normalize($nested, $rootKey);
        $data = $normalized[$rootKey] ?? [];
        $repeater = [];

        foreach ($data as $groupName => $items) {
            $repeater[] = [
                'group_name' => $groupName,
                'items' => is_array($items) ? $items : [],
            ];
        }

        return $repeater;
    }

    /**
     * Converts Repeater data back into the nested DB structure.
     * 
     * @param array $repeater In format [['group_name' => '...', 'items' => [...]]]
     * @param string $rootKey The root key to wrap in.
     * @return array
     */
    public static function fromRepeater(array $repeater, string $rootKey): array
    {
        $nested = [];

        foreach ($repeater as $row) {
            $groupName = $row['group_name'] ?? 'General';
            $items = $row['items'] ?? [];
            
            if (!empty($items)) {
                $nested[$groupName] = $items;
            }
        }

        return [$rootKey => $nested];
    }
}
