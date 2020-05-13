<?php


namespace app\models;


use yii\base\Model;

class AddUserForm extends Model
{
    public $user;
    public $role;
    public $internal;

    public function rules()
    {
        return [
            [['user','role'], 'required'],
            [['internal'], 'boolean']
        ];
    }
}