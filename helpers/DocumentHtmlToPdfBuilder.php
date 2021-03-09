<?php

namespace app\helpers\components;

use Yii;
use yii\base\Exception;
use yii\helpers\FileHelper;
use steroids\document\DocumentModule;
use mikehaertl\wkhtmlto\Pdf;

class DocumentHtmlToPdfBuilder
{
    public static function build($name, $html, $params)
    {
        // Normalize html
        $html = html_entity_decode($html);
        $html = str_replace('<body', '<body style="margin: -30pt; zoom: 1.3;"', $html);
        $html = str_replace('<table', '<table border="none"', $html);

        // Insert variable values
        $html = str_replace(
            array_map(
                fn(string $key) => '{' . $key . '}',
                array_keys($params)
            ),
            array_values($params),
            $html
        );

        return static::generatePdf($name, $html);
    }

    /**
     * @param string $name
     * @param string $html
     * @return string
     * @throws Exception
     */
    protected static function generatePdf(string $name, string $html)
    {
        // Create pdf
        $pdf = new Pdf([
            'ignoreWarnings' => true,
            'orientation' => 'portrait',
        ]);
        $pdf->addPage($html);

        // Get file path
        $dir = Yii::getAlias(DocumentModule::getInstance()->documentsDir);
        FileHelper::createDirectory($dir);

        // Clean
        $path = "$dir/$name.pdf";
        if (file_exists($path)) {
            unlink($path);
        }

        // Save
        if (!$pdf->saveAs($path)) {
            throw new Exception('Cannot save pdf: ' . $pdf->getError());
        }

        return $path;
    }

}
