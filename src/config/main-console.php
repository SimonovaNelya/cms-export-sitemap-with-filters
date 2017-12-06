<?php
return [

    'components' =>
    [
        'cmsExport' => [
            'handlers'     =>
            [
                'skeeks\cms\exportSitemapWithFilters\ExportSitemapWithFiltersHandler' =>
                [
                    'class' => 'skeeks\cms\exportSitemapWithFilters\ExportSitemapWithFiltersHandler'
                ]
            ]
        ],

        'i18n' => [
            'translations' =>
            [
                'skeeks/exportSitemapWithFilters' => [
                    'class'             => 'yii\i18n\PhpMessageSource',
                    'basePath'          => '@skeeks/cms/exportSitemapWithFilters/messages',
                    'fileMap' => [
                        'skeeks/exportSitemapWithFilters' => 'main.php',
                    ],
                ]
            ]
        ]
    ]
];