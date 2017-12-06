<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 29.08.2016
 */
namespace skeeks\cms\exportSitemapWithFilters;

use skeeks\cms\cmsWidgets\treeMenu\TreeMenuCmsWidget;
use skeeks\cms\export\ExportHandler;
use skeeks\cms\export\ExportHandlerFilePath;
use skeeks\cms\importCsv\handlers\CsvHandler;
use skeeks\cms\importCsv\helpers\CsvImportRowResult;
use skeeks\cms\importCsv\ImportCsvHandler;
use skeeks\cms\importCsvContent\widgets\MatchingInput;
use skeeks\cms\models\CmsContent;
use skeeks\cms\models\CmsContentElement;
use skeeks\cms\models\CmsContentPropertyEnum;
use skeeks\cms\models\CmsSite;
use skeeks\cms\models\CmsTree;
use skeeks\cms\models\Tree;
use skeeks\cms\modules\admin\widgets\BlockTitleWidget;
use skeeks\cms\modules\admin\widgets\form\ActiveFormUseTab;
use skeeks\cms\relatedProperties\PropertyType;
use skeeks\cms\relatedProperties\propertyTypes\PropertyTypeElement;
use skeeks\cms\relatedProperties\propertyTypes\PropertyTypeList;
use skeeks\cms\shop\models\ShopCmsContentElement;
use skeeks\cms\shop\models\ShopProduct;
use skeeks\cms\widgets\formInputs\selectTree\SelectTree;
use skeeks\modules\cms\money\models\Currency;
use skeeks\cms\exportSitemap\ExportSitemapHandler;
use yii\base\Exception;
use yii\bootstrap\Alert;
use yii\console\Application;
use yii\data\Pagination;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;
use yii\helpers\FileHelper;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\UrlNormalizer;
use yii\widgets\ActiveForm;
use yii\web\View;

/**
 * @property string $rootSitemapsDir
 *
 * Class ExportSitemapWithFiltersHandler
 * @package skeeks\cms\exportSitemapHandler
 */
class ExportSitemapWithFiltersHandler extends ExportSitemapHandler
{

    public function export()
    {
        //TODO: if console app
        \Yii::$app->urlManager->baseUrl     = $this->base_url;
        \Yii::$app->urlManager->scriptUrl   = $this->base_url;

        ini_set("memory_limit","8192M");
        set_time_limit(0);

        //Создание дирректории
        if ($dirName = dirname($this->rootFilePath))
        {
            $this->result->stdout("Корневая директория: {$dirName}\n");

            if (!is_dir($dirName) && !FileHelper::createDirectory($dirName))
            {
                throw new Exception("Не удалось создать директорию для файла");
            }
        }

        $result = [];
        $sitemap = [];

        $this->result->stdout("\tСоздание файла siemap для разделов\n");
        $result = $this->_addTrees($result);

        if ($result)
        {
            $publicUrl = $this->generateSitemapFile('tree.xml', $result);
            $this->result->stdout("\tФайл успешно сгенерирован: {$publicUrl}\n");

            $sitemap[] = $publicUrl;
        }

        if ($this->content_ids)
        {

            $this->result->stdout("\tЭкспорт контента\n");

            foreach ($this->content_ids as $contentId)
            {
                $content = CmsContent::findOne($contentId);
                $files = $this->_exportContent($content);

                $sitemap = ArrayHelper::merge($sitemap, $files);
            }
        }

        $resultFilters = $this->_addAFilters($result);
        if ($resultFilters)
        {
            $publiFiltercUrl[] = $this->generateSitemapFile('.xml', $resultFilters);
            $this->result->stdout("\tФайл успешно сгенерирован: {$publiFiltercUrl}\n");

            $sitemap = ArrayHelper::merge($sitemap, $publiFiltercUrl);
        }


        if ($sitemap)
        {
            $this->result->stdout("\tГенерация sitemap\n");

            $data = [];
            foreach ($sitemap as $file)
            {
                $data[] =
                    [
                        "loc"           => $file,
                        "lastmod"       => $this->_lastMod(new Tree(['updated_at' => time()])),
                    ];
            }

            $sitemapContent = \Yii::$app->view->render('@skeeks/cms/exportSitemapWithFilters/views/sitemapindex', [
                'data' => $data
            ]);

            $fp = fopen($this->rootFilePath, 'w');
            // записываем в файл текст
            fwrite($fp, $sitemapContent);
            // закрываем
            fclose($fp);

            if (!file_exists($this->rootFilePath))
            {
                throw new Exception("\t\tНе удалось создать файл");
            }
        }

        return $this->result;
    }

    /**
     * @param array $data
     * @return $this
     */
    protected function _addAFilters(&$data = [])
    {
        $filters = SavedFilters::find()->where(['is_active' => 1])->all();
        if ($filters)
        {
            /**
             * @var SavedFilters $filter
             */
            foreach ($filters as $filter)
            {
                $data[] =
                    [
                        "loc"           => $filter->getUrl(true),
                        "lastmod"       => $this->_lastMod($filter),
                    ];
            }
        }

        return $this;
    }


    /**
     * @param Tree $model
     * @return string
     */
    private function _lastMod($model)
    {
        //$string = "2013-08-03T21:14:41+01:00";
        //$string = date("Y-m-d", $model->updated_at) . "T" . date("H:i:s+04:00", $model->updated_at);
        $string = date("c", $model->updated_at);

        return $string;
    }

}
