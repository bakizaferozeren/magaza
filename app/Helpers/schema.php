<?php

// ============================================
// JSON-LD SCHEMA MARKUP
// ============================================

function schemaOrganization(): string
{
    $data = [
        '@context' => 'https://schema.org',
        '@type'    => 'Organization',
        'name'     => setting('site_name'),
        'url'      => url(),
        'logo'     => setting('site_logo') ? uploadUrl(setting('site_logo')) : '',
        'contactPoint' => [
            '@type'       => 'ContactPoint',
            'telephone'   => setting('site_phone'),
            'contactType' => 'customer service',
            'areaServed'  => 'TR',
            'availableLanguage' => ['Turkish', 'English'],
        ],
        'sameAs' => array_filter([
            setting('facebook_url'),
            setting('instagram_url'),
            setting('twitter_url'),
            setting('youtube_url'),
        ]),
    ];

    return schemaTag($data);
}

function schemaWebsite(): string
{
    $data = [
        '@context' => 'https://schema.org',
        '@type'    => 'WebSite',
        'name'     => setting('site_name'),
        'url'      => url(),
        'potentialAction' => [
            '@type'       => 'SearchAction',
            'target'      => [
                '@type'       => 'EntryPoint',
                'urlTemplate' => url('arama?q={search_term_string}'),
            ],
            'query-input' => 'required name=search_term_string',
        ],
    ];

    return schemaTag($data);
}

function schemaProduct(array $product, array $reviews = []): string
{
    $price     = $product['sale_price'] ?? $product['price'];
    $inStock   = $product['stock_status'] === 'in_stock';
    $imageUrl  = !empty($product['image']) ? uploadUrl('products/' . $product['image']) : '';

    $data = [
        '@context'    => 'https://schema.org',
        '@type'       => 'Product',
        'name'        => $product['name'] ?? '',
        'description' => strip_tags($product['short_desc'] ?? ''),
        'sku'         => $product['sku'] ?? '',
        'brand'       => [
            '@type' => 'Brand',
            'name'  => $product['brand_name'] ?? '',
        ],
        'image'  => $imageUrl,
        'offers' => [
            '@type'           => 'Offer',
            'url'             => url('urun/' . ($product['slug'] ?? '')),
            'priceCurrency'   => 'TRY',
            'price'           => number_format((float)$price, 2, '.', ''),
            'priceValidUntil' => date('Y-12-31'),
            'itemCondition'   => 'https://schema.org/NewCondition',
            'availability'    => $inStock
                ? 'https://schema.org/InStock'
                : 'https://schema.org/OutOfStock',
            'seller' => [
                '@type' => 'Organization',
                'name'  => setting('site_name'),
            ],
        ],
    ];

    // Degerlendirimeler
    if (!empty($reviews)) {
        $totalRating = array_sum(array_column($reviews, 'rating'));
        $avgRating   = round($totalRating / count($reviews), 1);

        $data['aggregateRating'] = [
            '@type'       => 'AggregateRating',
            'ratingValue' => $avgRating,
            'reviewCount' => count($reviews),
            'bestRating'  => 5,
            'worstRating' => 1,
        ];

        $data['review'] = array_map(function($review) {
            return [
                '@type'         => 'Review',
                'author'        => ['@type' => 'Person', 'name' => $review['author_name']],
                'reviewRating'  => [
                    '@type'       => 'Rating',
                    'ratingValue' => $review['rating'],
                    'bestRating'  => 5,
                ],
                'reviewBody'    => $review['comment'],
                'datePublished' => date('Y-m-d', strtotime($review['created_at'])),
            ];
        }, array_slice($reviews, 0, 5));
    }

    return schemaTag($data);
}

function schemaBreadcrumb(array $items): string
{
    $listItems = [];
    foreach ($items as $i => $item) {
        $listItems[] = [
            '@type'    => 'ListItem',
            'position' => $i + 1,
            'name'     => $item['label'],
            'item'     => $item['url'],
        ];
    }

    $data = [
        '@context'        => 'https://schema.org',
        '@type'           => 'BreadcrumbList',
        'itemListElement' => $listItems,
    ];

    return schemaTag($data);
}

function schemaFaq(array $faqs): string
{
    $items = array_map(function($faq) {
        return [
            '@type'          => 'Question',
            'name'           => $faq['question'],
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text'  => strip_tags($faq['answer']),
            ],
        ];
    }, $faqs);

    $data = [
        '@context'   => 'https://schema.org',
        '@type'      => 'FAQPage',
        'mainEntity' => $items,
    ];

    return schemaTag($data);
}

function schemaArticle(array $blog): string
{
    $data = [
        '@context'      => 'https://schema.org',
        '@type'         => 'Article',
        'headline'      => $blog['title'] ?? '',
        'description'   => excerpt($blog['content'] ?? '', 160),
        'image'         => !empty($blog['image']) ? uploadUrl('blog/' . $blog['image']) : '',
        'datePublished' => $blog['published_at'] ?? $blog['created_at'] ?? '',
        'dateModified'  => $blog['updated_at'] ?? '',
        'author'        => [
            '@type' => 'Person',
            'name'  => $blog['author_name'] ?? setting('site_name'),
        ],
        'publisher' => [
            '@type' => 'Organization',
            'name'  => setting('site_name'),
            'logo'  => [
                '@type' => 'ImageObject',
                'url'   => setting('site_logo') ? uploadUrl(setting('site_logo')) : '',
            ],
        ],
    ];

    return schemaTag($data);
}

function schemaTag(array $data): string
{
    return '<script type="application/ld+json">'
        . json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
        . '</script>' . PHP_EOL;
}
