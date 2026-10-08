<?php

return [
    'version' => [
        'tiny' => '8.0.2',
        'language' => [
            // https://cdn.jsdelivr.net/npm/tinymce-i18n@latest/
            'version' => '25.8.4',
            'package' => 'langs8',
        ],
        'licence_key' => env('TINY_LICENSE_KEY', 'no-api-key'),
    ],

    // Gunakan 'vendor' agar tidak perlu API key cloud TinyMCE
    'provider' => env('TINY_PROVIDER', 'vendor'),

    /**
     * change darkMode: 'auto'|'force'|'class'|'media'|false|'custom'
     */
    'darkMode' => 'auto',

    'skins' => [
        // oxide, oxide-dark, tinymce-5, tinymce-5-dark
        'ui' => 'oxide',

        // dark, default, document, tinymce-5, tinymce-5-dark, writer
        'content' => 'default',
    ],

    'profiles' => [
        'default' => [
            'plugins' => 'accordion autoresize codesample directionality advlist link image lists preview pagebreak searchreplace wordcount code fullscreen insertdatetime media table emoticons',
            'toolbar' => 'undo redo removeformat | fontfamily fontsize styles | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | numlist bullist outdent indent | forecolor backcolor | blockquote table hr | image link media codesample emoticons | code fullscreen',
            'upload_directory' => 'content-media',
            'custom_configs' => [
                'image_title' => true,
                'automatic_uploads' => true,
                'image_advtab' => true,
                'resize' => true,
                'object_resizing' => true,
                'image_dimensions' => true,
            ],
        ],

        'simple' => [
            'plugins' => 'autoresize directionality emoticons link wordcount',
            'toolbar' => 'removeformat | bold italic | rtl ltr | numlist bullist | link emoticons',
            'upload_directory' => null,
        ],

        'minimal' => [
            'plugins' => 'link wordcount',
            'toolbar' => 'bold italic link numlist bullist',
            'upload_directory' => null,
        ],

        'full' => [
            'plugins' => 'accordion autoresize codesample directionality advlist autolink link image lists charmap preview anchor pagebreak searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media table emoticons help',
            'toolbar' => 'undo redo removeformat | fontfamily fontsize styles | bold italic underline strikethrough | rtl ltr | alignjustify alignright aligncenter alignleft | numlist bullist outdent indent accordion | forecolor backcolor | blockquote table toc hr | image link anchor media codesample emoticons | visualblocks code preview wordcount fullscreen help',
            'upload_directory' => 'content-media',
            'custom_configs' => [
                'image_title' => true,
                'automatic_uploads' => true,
                'image_advtab' => true,
                'resize' => true,
                'object_resizing' => true,
                'image_dimensions' => true,
            ],
        ],
    ],

    'languages' => [],

    'extra' => [
        'toolbar' => [],
    ],
];
