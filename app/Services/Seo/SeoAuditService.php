<?php

namespace App\Services\Seo;

use App\Models\Page;
use App\Services\JsonLdGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SeoAuditService
{
    protected JsonLdGenerator $jsonLdGenerator;

    public function __construct(JsonLdGenerator $jsonLdGenerator)
    {
        $this->jsonLdGenerator = $jsonLdGenerator;
    }

    public function audit(Model $model): array
    {
        $issues = [];
        $score = 100;

        $this->auditMeta($model, $issues);
        $this->auditJsonLd($model, $issues);

        // Calculate Score
        foreach ($issues as $issue) {
            $deduction = match ($issue['severity']) {
                'error' => 20,
                'warning' => 10,
                'info' => 5,
                default => 0,
            };
            $score -= $deduction;
        }

        return [
            'score' => max(0, $score),
            'issues' => $issues,
        ];
    }

    protected function auditMeta(Model $model, array &$issues): void
    {
        $seo = $model->seo;
        $title = $seo?->title ?? $model->title ?? $model->name;
        $description = $seo?->description;

        // Title Checks
        if (!$title) {
            $issues[] = ['severity' => 'error', 'message' => 'Missing Meta Title', 'recommendation' => 'Add a Meta Title.'];
        } elseif (Str::length($title) < 30) {
            $issues[] = ['severity' => 'warning', 'message' => 'Meta Title too short', 'recommendation' => 'Increase title to at least 30 chars.'];
        } elseif (Str::length($title) > 60) {
            $issues[] = ['severity' => 'warning', 'message' => 'Meta Title too long', 'recommendation' => 'Keep title under 60 chars.'];
        }

        // Description Checks
        if (!$description) {
             // Check if fallback exists
             $issues[] = ['severity' => 'warning', 'message' => 'Missing explicit Meta Description', 'recommendation' => 'Add a custom Meta Description for better CTR.'];
        } elseif (Str::length($description) < 70) {
            $issues[] = ['severity' => 'info', 'message' => 'Meta Description short', 'recommendation' => 'Expand description to 70-160 chars.'];
        } elseif (Str::length($description) > 160) {
            $issues[] = ['severity' => 'warning', 'message' => 'Meta Description too long', 'recommendation' => 'Truncate description to 160 chars.'];
        }

        // NoIndex Check
        if ($seo?->noindex) {
            $issues[] = ['severity' => 'error', 'message' => 'Page is No-Indexed', 'recommendation' => 'Remove No Index if this is a public page.'];
        }
    }

    protected function auditJsonLd(Model $model, array &$issues): void
    {
        // Don't audit JSON-LD if noindex (it won't exist)
        if ($model->seo?->noindex) {
            return;
        }

        $result = $this->jsonLdGenerator->generate($model, true);
        $schemas = $result['@graph'] ?? [];

        $hasOrganization = false;
        $hasWebPage = false;
        $organizationId = url('/') . '#organization';

        foreach ($schemas as $schema) {
            $type = $schema['@type'] ?? '';

            if ($type === 'Organization') {
                $hasOrganization = true;
                if (($schema['@id'] ?? '') !== $organizationId) {
                     $issues[] = ['severity' => 'warning', 'message' => 'Inconsistent Organization @id', 'recommendation' => 'Organization @id must match global ID.'];
                }
            }

            if ($type === 'WebPage') {
                $hasWebPage = true;
                if (!isset($schema['isPartOf'])) {
                    $issues[] = ['severity' => 'error', 'message' => 'WebPage missing isPartOf', 'recommendation' => 'Link WebPage to WebSite (isPartOf).'];
                }
            }
        }

        if ($model instanceof Page && $model->is_homepage && !$hasOrganization) {
            $issues[] = ['severity' => 'error', 'message' => 'Homepage missing Organization Schema', 'recommendation' => 'Homepage must output Organization schema.'];
        }

        if (!$hasWebPage) {
            // It's okay if it's Article or Product, but usually we want WebPage too or specific type
            // For now, let's assume valid pages need a main entity
            $issues[] = ['severity' => 'error', 'message' => 'Missing WebPage Schema', 'recommendation' => 'Ensure a WebPage or equivalent entity is generated.'];
        }
    }
}
