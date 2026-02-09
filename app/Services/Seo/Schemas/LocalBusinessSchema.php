<?php

namespace App\Services\Seo\Schemas;

class LocalBusinessSchema extends BaseSchema
{
    public function __construct(
        protected string $name,
        protected string $url,
        protected ?string $logo = null,
        protected ?string $address = null,
        protected ?string $city = null,
        protected ?string $province = null,
        protected ?string $postalCode = null,
        protected ?string $country = 'ID',
        protected ?string $phone = null,
        protected ?string $email = null,        
        protected array $openingHours = []
    ) {}

    public function toArray(): array
    {
        $data = [
            '@type' => 'LocalBusiness',
            '@id' => $this->qualifyId('#localbusiness', $this->url),
            'name' => $this->name,
            'url' => $this->url,
            'logo' => $this->logo,
            'telephone' => $this->phone,
            'email' => $this->email,
        ];

        // Add address if available
        if ($this->address || $this->city) {
            $data['address'] = $this->clean([
                '@type' => 'PostalAddress',
                'streetAddress' => $this->address,
                'addressLocality' => $this->city,
                'addressRegion' => $this->province,
                'postalCode' => $this->postalCode,
                'addressCountry' => $this->country,
            ]);
        }

        // Add opening hours if available
        if (!empty($this->openingHours)) {
            $data['openingHoursSpecification'] = array_map(fn($hour) => [
                '@type' => 'OpeningHoursSpecification',
                'dayOfWeek' => $hour['days'] ?? [],
                'opens' => $hour['opens'] ?? '09:00',
                'closes' => $hour['closes'] ?? '17:00',
            ], $this->openingHours);
        }

        return $this->clean($data);
    }
}
