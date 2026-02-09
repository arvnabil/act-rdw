<?php

namespace App\Services\Seo\Schemas;

abstract class BaseSchema
{
    /**
     * Convert the schema object to an array for JSON-LD.
     */
    abstract public function toArray(): array;

    /**
     * Clean null or empty values recursively.
     */
    protected function clean(array $data): array
    {
        return array_filter($data, function ($v) {
            if (is_array($v)) {
                $v = $this->clean($v);
                return !empty($v);
            }
            return !is_null($v) && $v !== '';
        });
    }

    /**
     * Prefix an ID with the base URL if it's a relative anchor.
     */
    protected function qualifyId(string $id, string $baseUrl): string
    {
        if (str_starts_with($id, '#')) {
            return rtrim($baseUrl, '/') . $id;
        }
        return $id;
    }
}
