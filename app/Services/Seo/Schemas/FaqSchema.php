<?php

namespace App\Services\Seo\Schemas;

class FaqSchema extends BaseSchema
{
    public function __construct(
        protected array $items // [['question' => '...', 'answer' => '...']]
    ) {}

    public function toArray(): array
    {
        if (empty($this->items)) {
            return [];
        }

        $mainEntity = [];
        foreach ($this->items as $item) {
            $question = $item['question'] ?? $item['title'] ?? null;
            $answer = $item['answer'] ?? $item['content'] ?? $item['description'] ?? null;

            if ($question && $answer) {
                $mainEntity[] = [
                    '@type' => 'Question',
                    'name' => strip_tags($question),
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => strip_tags($answer),
                    ],
                ];
            }
        }

        if (empty($mainEntity)) {
            return [];
        }

        return [
            '@type' => 'FAQPage',
            'mainEntity' => $mainEntity,
        ];
    }
}
