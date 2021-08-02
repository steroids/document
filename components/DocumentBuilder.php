<?php

namespace steroids\document\components;

use steroids\document\enums\DocumentSignStatus;
use steroids\document\enums\DocumentType;
use steroids\document\models\DocumentUser;
use yii\base\Component;
use yii\base\Exception;

class DocumentBuilder extends Component
{
    const LANGUAGE_RU = 'ru';
    const LANGUAGE_EN = 'en';

    public array $langSuffixes = [
        self::LANGUAGE_RU => 'Рус',
        self::LANGUAGE_EN => 'Англ',
    ];

    /**
     * Переменные:
     *  Подписано1
     *  Подписано2
     *  ПодписаноРус1
     *  ПодписаноРус2
     *  ПодписаноАнгл1
     *  ПодписаноАнгл2
     * @var string
     */
    public string $attributeSignedPrefix = 'Подписано';

    public function download(DocumentUser $documentUser)
    {
        switch ($documentUser->document->type) {
            case DocumentType::PDF:
                return $documentUser->document->download();

            case DocumentType::TEMPLATE_HTML:
                return DocumentHtmlToPdfBuilder::build(
                    $documentUser->document->name . '_' . $documentUser->uid,
                    $documentUser->document->templateHtml,
                    array_merge(
                        $this->getStatusParams($documentUser),
                        $documentUser->userParams,
                        $documentUser->refParams,
                        $documentUser->params,
                    )
                );

            case DocumentType::BLANK:
                throw new Exception('Nothing to download. Document type - "blank"');
        }

        throw new Exception('Unsupported document type: ' . $documentUser->document->type);
    }

    public function getStatusParams(DocumentUser $documentUser)
    {
        $params = [];

        // Sign letter
        foreach ([$documentUser->firstSignStatus, $documentUser->secondSignStatus] as $index => $signStatus) {
            $number = $index + 1;
            $isSigned = $signStatus === DocumentSignStatus::SIGNED;
            $texts = [
                self::LANGUAGE_RU => \Yii::t('steroids', 'Подписано путем введения одноразового пароля, направленного через СМС'),
                self::LANGUAGE_EN => \Yii::t('steroids', 'Signed by entering a One-Time password sent via SMS'),
            ];

            $params = array_merge($params, [
                $this->attributeSignedPrefix . $number => $isSigned ? $texts[self::LANGUAGE_RU] : '',
                $this->attributeSignedPrefix . $this->langSuffixes[static::LANGUAGE_RU] . $number => $isSigned ? $texts[self::LANGUAGE_RU] : '',
                $this->attributeSignedPrefix . $this->langSuffixes[static::LANGUAGE_EN] . $number => $isSigned ? $texts[self::LANGUAGE_EN] : '',
            ]);
        }

        return $params;
    }
}
