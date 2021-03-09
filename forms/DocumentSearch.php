<?php

namespace steroids\document\forms;

use steroids\document\forms\meta\DocumentSearchMeta;
use steroids\document\models\DocumentCategory;

class DocumentSearch extends DocumentSearchMeta
{
    public function fields()
    {
        return [
            'id',
            'categoryId',
            'name',
            'type',
            'title',
            'signMode',
            'isSignRequired',
            'isScanRequired',
            'isOriginalRequired',
            'versionTime',
            'categoryName' => 'category.name',
        ];
    }

    public function prepare($query)
    {
        parent::prepare($query);

        $categoryId = $this->categoryName
            ? DocumentCategory::find()->select('id')->where(['name' => $this->categoryName])->scalar()
            : null;

        $query
            ->with('category')
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere([
                'categoryId' => $categoryId,
                'type' => $this->type,
            ])
            ->orderBy(['id' => SORT_ASC]);
    }
}
