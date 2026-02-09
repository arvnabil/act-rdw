<?php

namespace App\Services\Seo\Schemas;

class ArticleSchema extends BaseSchema
{
    public function __construct(
        protected string $headline,
        protected string $description,
        protected string $url,
        protected ?string $image,
        protected array $author, // ['@type' => 'Person', 'name' => 'Name']
        protected array $publisher, // ['@id' => '...']
        protected string $datePublished,
        protected string $dateModified,
        protected ?int $wordCount = null,
        protected array $keywords = [],
        protected ?string $articleSection = null
    ) {}

    public function toArray(): array
    {
        return $this->clean([
            '@type' => 'Article',
            '@id' => $this->url . '#article',
            'headline' => $this->headline,
            'description' => $this->description,
            'image' => $this->image,
            'datePublished' => $this->datePublished,
            'dateModified' => $this->dateModified,
            'author' => $this->author,
            'publisher' => $this->publisher,
            'wordCount' => $this->wordCount,
            'keywords' => $this->keywords,
            'articleSection' => $this->articleSection,
            'mainEntityOfPage' => ['@id' => $this->url . '#webpage'],
            'isPartOf' => ['@id' => $this->url . '#webpage'],
            'inLanguage' => 'id-ID',
        ]);
    }
}
