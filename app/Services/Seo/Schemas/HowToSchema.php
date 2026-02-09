<?php

namespace App\Services\Seo\Schemas;

class HowToSchema extends BaseSchema
{
    public function __construct(
        protected string $name,
        protected string $description,
        protected string $url,
        protected array $steps, // [['name' => '...', 'text' => '...', 'image' => '...']]
        protected ?string $totalTime = null // ISO 8601 duration (e.g., PT30M)
    ) {}

    public function toArray(): array
    {
        if (empty($this->steps)) {
            return [];
        }

        $stepList = [];
        foreach ($this->steps as $index => $step) {
            $stepData = [
                '@type' => 'HowToStep',
                'position' => $index + 1,
                'name' => $step['name'] ?? $step['title'] ?? "Step " . ($index + 1),
                'text' => strip_tags($step['text'] ?? $step['description'] ?? $step['content'] ?? ''),
            ];

            if (!empty($step['image'])) {
                $stepData['image'] = $step['image'];
            }

            $stepList[] = $stepData;
        }

        return $this->clean([
            '@type' => 'HowTo',
            'name' => $this->name,
            'description' => $this->description,
            'url' => $this->url,
            'totalTime' => $this->totalTime,
            'step' => $stepList,
        ]);
    }
}
