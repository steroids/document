<?php

namespace steroids\document\forms;

use steroids\document\forms\meta\DocumentCategorySearchMeta;

class DocumentCategorySearch extends DocumentCategorySearchMeta
{
    public function fields()
    {
        return [
            'id',
            'name',
            'title',
            'parentId',
            'updateTime',
        ];
    }

    public function prepare($query)
    {
        parent::prepare($query);

        $query
            ->andFilterWhere(['like', 'title', $this->title])
            ->orderBy(['id' => SORT_ASC]);
    }
}
