<?php
/**
 * Created by PhpStorm.
 * User: pengcheng
 * Date: 2016/10/28
 * Time: 11:46
 */

namespace app\models\doctor;


use app\models\base\BaseActiveRecord;

class ExpertTeam extends BaseActiveRecord
{
    public static function tableName()
    {
        return 'expert_team';
    }

    public function rules()
    {
        return parent::rules(); // TODO: Change the autogenerated stub
    }

    public function attributes()
    {
        return parent::attributes(); // TODO: Change the autogenerated stub
    }
}