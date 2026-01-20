<?php

namespace App\Services\Seo;

class JsonLdOptimizer
{
    /**
     * Optimize and normalize JSON-LD schemas.
     */
    public function optimize(array $schemas): array
    {
        $optimized = [];

        foreach ($schemas as $schema) {
            $formatted = $this->recursiveFormat($schema);

            if (!empty($formatted)) {
                $optimized[] = $formatted;
            }
        }

        return $optimized;
    }

    protected function recursiveFormat(mixed $data): mixed
    {
        if (is_array($data)) {
            // Remove nulls and empty strings (but keep 0 or false)
            $data = array_filter($data, fn($value) => !is_null($value) && $value !== '');

            foreach ($data as $key => &$value) {
                // Recursive call
                $value = $this->recursiveFormat($value);

                // optimization: cast numeric strings to int for specific keys
                if (in_array($key, ['width', 'height', 'position', 'contentUrl', 'code'])) {
                    if (is_numeric($value)) {
                        $value = (int) $value;
                    }
                }
            }

            // Enforce @context sequence if it exists (it should be first visually, though JSON is unordered)
            // But mainly we just return the cleaned array
            return $data;
        }

        return $data;
    }
}
