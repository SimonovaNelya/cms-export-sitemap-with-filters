SkeekS CMS cms-export-sitemap-with-filters
===================================

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist skeeks/cms-export-sitemap-with-filters "*"
```

or add

```
"skeeks/cms-export-sitemap-with-filters": "*"
```

Configuration app
----------

```php

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

```

##Links
* [Web site (rus)](https://cms.skeeks.com)
* [Author](https://skeeks.com)
* [ChangeLog](https://github.com/skeeks-cms/cms-vk-database/blob/master/CHANGELOG.md)


___

> [![skeeks!](https://gravatar.com/userimage/74431132/13d04d83218593564422770b616e5622.jpg)](https://skeeks.com)  
<i>SkeekS CMS (Yii2) — quickly, easily and effectively!</i>  
[skeeks.com](https://skeeks.com) | [cms.skeeks.com](https://cms.skeeks.com)


