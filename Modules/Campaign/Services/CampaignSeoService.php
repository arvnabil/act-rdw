<?php

namespace Modules\Campaign\Services;

use Modules\Campaign\Models\Campaign;

/**
 * Standalone SEO service for Campaign module.
 * Does NOT touch Modules/SEO — completely independent.
 */
class CampaignSeoService
{
    /**
     * Build meta tag data array from campaign seo_data.
     */
    public function getMetaTags(Campaign $campaign): array
    {
        $seo = $campaign->seo_data ?? [];

        return [
            'title'          => $seo['meta_title']       ?? $campaign->name,
            'description'    => $seo['meta_description'] ?? '',
            'canonical'      => $seo['canonical_url']    ?? url('/campaign/' . $campaign->slug),
            'robots'         => $seo['robots']           ?? 'index, follow',
            'og_title'       => $seo['og_title']         ?? $campaign->name,
            'og_description' => $seo['og_description']   ?? ($seo['meta_description'] ?? ''),
            'og_image'       => $seo['og_image']         ?? '',
            'og_type'        => 'website',
            'og_url'         => url('/campaign/' . $campaign->slug),
        ];
    }

    /**
     * Build JSON-LD structured data from campaign seo_data and geo_data.
     *
     * @return array<int, array>
     */
    public function getJsonLd(Campaign $campaign): array
    {
        $schemas = [];
        $seo     = $campaign->seo_data ?? [];
        $geo     = $campaign->geo_data ?? [];
        $baseUrl = url('/campaign/' . $campaign->slug);

        // WebPage
        $schemas[] = [
            '@context'    => 'https://schema.org',
            '@type'       => 'WebPage',
            'name'        => $seo['meta_title']       ?? $campaign->name,
            'description' => $seo['meta_description'] ?? '',
            'url'         => $baseUrl,
        ];

        // BreadcrumbList
        $schemas[] = [
            '@context'        => 'https://schema.org',
            '@type'           => 'BreadcrumbList',
            'itemListElement' => [
                [
                    '@type'    => 'ListItem',
                    'position' => 1,
                    'name'     => 'Home',
                    'item'     => url('/'),
                ],
                [
                    '@type'    => 'ListItem',
                    'position' => 2,
                    'name'     => $campaign->name,
                    'item'     => $baseUrl,
                ],
            ],
        ];

        // FAQPage from geo_data.faq
        $faq = $geo['faq'] ?? [];
        if (! empty($faq) && is_array($faq)) {
            $mainEntity = [];
            foreach ($faq as $item) {
                if (! empty($item['question']) && ! empty($item['answer'])) {
                    $mainEntity[] = [
                        '@type'          => 'Question',
                        'name'           => $item['question'],
                        'acceptedAnswer' => [
                            '@type' => 'Answer',
                            'text'  => $item['answer'],
                        ],
                    ];
                }
            }
            if (! empty($mainEntity)) {
                $schemas[] = [
                    '@context'   => 'https://schema.org',
                    '@type'      => 'FAQPage',
                    'mainEntity' => $mainEntity,
                ];
            }
        }

        // Organization from geo_data.organization_context
        $orgContext = $geo['organization_context'] ?? '';
        if (! empty($orgContext)) {
            $schemas[] = [
                '@context'    => 'https://schema.org',
                '@type'       => 'Organization',
                'description' => $orgContext,
                'url'         => url('/'),
            ];
        }

        return $schemas;
    }
}