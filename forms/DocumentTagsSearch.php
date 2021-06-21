<?php

namespace steroids\document\forms;

use steroids\document\forms\meta\DocumentTagsSearchMeta;

class DocumentTagsSearch extends DocumentTagsSearchMeta
{
    public function fields()
    {
        return [
            'id',
            'name',
            'title',
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
