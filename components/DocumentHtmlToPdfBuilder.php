<?php

namespace steroids\document\components;

use Yii;
use yii\base\Exception;
use yii\helpers\FileHelper;
use steroids\document\DocumentModule;
use mikehaertl\wkhtmlto\Pdf;

class DocumentHtmlToPdfBuilder
{
    public static function build($name, $html, $params)
    {
        $styles = [
            'body{margin-left: 1em;}',
            'figure {margin-left: 0; margin-top: 0;}',
            'table {border-spacing: 0; border: 1px;}',
            '.image-style-align-center {display: block; margin-left: auto; margin-right: auto;}',
        ];

        // Normalize html
        $html = html_entity_decode($html);
        $html = str_replace('<html>', '<html><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">', $html);
        //$html = str_replace('<body', '<body style="zoom: 1.3;"', $html);
        $html = str_replace('<html>', '<html><style>' . implode(' ', $styles) . '</style>', $html);
        $html = preg_replace('/{<\\/span><span style="[^"]+">([a-zA-Zа-яА-Я0-9]+)<\\/span><span style="[^"]+">}/u', '{$1}', $html);
        $html = preg_replace('/<figure( class="[^"]+")?( style="[^"]+")?><img src="([^"]+)"[^>]*><\/figure>/u', '<img src="$3"$1$2/>', $html);

        // Insert variable values
        $html = str_replace(
            array_map(
                fn(string $key) => '{' . $key . '}',
                array_keys($params)
            ),
            array_values($params),
            $html
        );

        if (YII_DEBUG && isset($_GET['html'])) {
            return $html;
        }

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
