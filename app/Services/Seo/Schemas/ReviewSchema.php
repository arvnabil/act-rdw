<?php

namespace App\Services\Seo\Schemas;

class ReviewSchema extends BaseSchema
{
    public function __construct(
        protected string $itemName,
        protected string $itemUrl,
        protected array $reviews // [['author' => '...', 'rating' => 5, 'review' => '...']]
    ) {}

    public function toArray(): array
    {
        if (empty($this->reviews)) {
            return [];
        }

        $reviewList = [];
        $totalRating = 0;

        foreach ($this->reviews as $review) {
            $author = $review['author'] ?? $review['name'] ?? 'Anonymous';
            $rating = (int)($review['rating'] ?? $review['score'] ?? 5);
            $text = $review['review'] ?? $review['content'] ?? $review['testimonial'] ?? '';

            $reviewList[] = [
                '@type' => 'Review',
                'author' => ['@type' => 'Person', 'name' => $author],
                'reviewRating' => [
                    '@type' => 'Rating',
                    'ratingValue' => $rating,
                    'bestRating' => 5,
                ],
                'reviewBody' => strip_tags($text),
            ];
            $totalRating += $rating;
        }

        $avgRating = count($reviewList) > 0 ? round($totalRating / count($reviewList), 1) : 0;

        return $this->clean([
            '@type' => 'Product',
            'name' => $this->itemName,
            'url' => $this->itemUrl,
            'aggregateRating' => [
                '@type' => 'AggregateRating',
                'ratingValue' => $avgRating,
                'reviewCount' => count($reviewList),
                'bestRating' => 5,
            ],
            'review' => $reviewList,
        ]);
    }
}
