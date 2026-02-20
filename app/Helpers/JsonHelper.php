<?php

namespace App\Helpers;

use Illuminate\Support\Arr;

class JsonHelper
{
    /**
     * Converts DB format (Nested Object) to Filament Repeater format (Array of Groups).
     */
    public static function toRepeater(mixed $state): array
    {
        if (empty($state)) return [];

        if (is_string($state)) {
            $state = json_decode($state, true) ?: [];
        }

        $repeater = [];

        foreach ($state as $groupName => $items) {
            $itemList = [];

            if (is_array($items)) {
                foreach ($items as $k => $v) {
                    if (is_array($v)) {
                        // If it's a nested array, flatten it and add as multiple entries
                        $flattened = self::flatten($v, (string)$k);
                        foreach ($flattened as $fk => $fv) {
                            $itemList[] = [
                                'key' => (string)$fk,
                                'value' => (string)$fv,
                            ];
                        }
                    } else {
                        $itemList[] = [
                            'key' => (string)$k,
                            'value' => (string)$v,
                        ];
                    }
                }
            } else {
                // If it's a string, treat as a single item with key "Value"
                $itemList[] = [
                    'key' => 'Value',
                    'value' => (string)$items,
                ];
            }

            $repeater[] = [
                'group_name' => (string)$groupName,
                'items' => $itemList,
            ];
        }

        return $repeater;
    }

    /**
     * Flattens nesting into "Key - SubKey" strings.
     */
    public static function flatten(array $array, string $prefix = ''): array
    {
        $result = [];
        foreach ($array as $key => $value) {
            $newKey = $prefix === '' ? (string)$key : $prefix . ' - ' . $key;
            if (is_array($value)) {
                $flattened = self::flatten($value, $newKey);
                foreach ($flattened as $fk => $fv) {
                    $result[$fk] = $fv;
                }
            } else {
                $result[$newKey] = (is_scalar($value) || is_null($value)) ? (string)$value : json_encode($value);
            }
        }
        return $result;
    }

    /**
     * Converts Repeater data back into the nested DB structure (Object).
     */
    public static function fromRepeater(array $repeater): array
    {
        $result = [];

        foreach ($repeater as $group) {
            $groupName = $group['group_name'] ?? null;
            $items = $group['items'] ?? [];

            if (!$groupName) continue;

            $groupData = [];
            foreach ($items as $item) {
                $k = $item['key'] ?? '';
                $v = $item['value'] ?? '';
                if ($k !== '') {
                    $groupData[$k] = $v;
                }
            }

            // Special Case: If only 1 item and key is "Value", save as string
            if (count($groupData) === 1 && isset($groupData['Value'])) {
                $result[$groupName] = $groupData['Value'];
            } else {
                $result[$groupName] = $groupData;
            }
        }

        return $result;
    }
    /**
     * Helper for importer: converts flat keys and JSON strings into nested structure.
     */
    public static function nest(mixed $state, ?string $rootKey = null): array
    {
        if (empty($state)) return [];

        if (is_string($state)) {
            $state = json_decode($state, true) ?: [];
        }

        $result = [];
        foreach ($state as $k => $v) {
            // Support "Group - Key" flattening
            if (str_contains((string)$k, ' - ')) {
                Arr::set($result, str_replace(' - ', '.', (string)$k), $v);
            } else {
                $result[$k] = $v;
            }
        }

        return $rootKey ? [$rootKey => $result] : $result;
    }
}
