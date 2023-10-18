<?php

// This file is auto-generated, don't edit it. Thanks.

namespace AlibabaCloud\SDK\Dingtalk\Vchengfeng_1_0\Models;

use AlibabaCloud\Tea\Model;

class PageListObjectiveProgressRequest extends Model
{
    /**
     * @example 20
     *
     * @var string
     */
    public $objectiveId;

    /**
     * @example 1
     *
     * @var int
     */
    public $pageNumber;

    /**
     * @example 10
     *
     * @var int
     */
    public $pageSize;
    protected $_name = [
        'objectiveId' => 'objectiveId',
        'pageNumber'  => 'pageNumber',
        'pageSize'    => 'pageSize',
    ];

    public function validate()
    {
    }

    public function toMap()
    {
        $res = [];
        if (null !== $this->objectiveId) {
            $res['objectiveId'] = $this->objectiveId;
        }
        if (null !== $this->pageNumber) {
            $res['pageNumber'] = $this->pageNumber;
        }
        if (null !== $this->pageSize) {
            $res['pageSize'] = $this->pageSize;
        }

        return $res;
    }

    /**
     * @param array $map
     *
     * @return PageListObjectiveProgressRequest
     */
    public static function fromMap($map = [])
    {
        $model = new self();
        if (isset($map['objectiveId'])) {
            $model->objectiveId = $map['objectiveId'];
        }
        if (isset($map['pageNumber'])) {
            $model->pageNumber = $map['pageNumber'];
        }
        if (isset($map['pageSize'])) {
            $model->pageSize = $map['pageSize'];
        }

        return $model;
    }
}
