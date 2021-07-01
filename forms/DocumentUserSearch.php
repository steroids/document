<?php

namespace steroids\document\forms;

use steroids\document\forms\meta\DocumentUserSearchMeta;

class DocumentUserSearch extends DocumentUserSearchMeta
{
    public function fields()
    {
        return [
            'id',
            'uid',
            'code',
            'document',
            'scanStatus',
            'scanStatusTime',
            'scanModeratorId',
            'scanModeratorComment',
            'originalStatus',
            'originalStatusTime',
            'originalModeratorId',
            'originalModeratorComment',
            'updateTime',
        ];
    }

    public function prepare($query)
    {
        parent::prepare($query);

        $query
            ->andFilterWhere([
                'categoryId' => $this->categoryId,
                'userId' => $this->userId,
                'codeNumber' => $this->codeNumber,
            ])
            ->andFilterWhere(['like', 'LOWER(title)', mb_strtolower(trim($this->title ?: ''))])
            ->orderBy([
                'scanStatus' => SORT_ASC,
                'originalStatus' => SORT_ASC,
                'id' => SORT_DESC
            ]);

    }
}
