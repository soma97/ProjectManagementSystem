<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\base\Exception;
use yii\helpers\VarDumper;

class SignupForm extends Model
{
    public $username;
    public $password;
    public $password_repeat;

    public function rules()
    {
        return [
            [['username', 'password', 'password_repeat'], 'required'],
            [['username', 'password'], 'string', 'min' => 4, 'max' => 55],
            ['password_repeat', 'compare', 'compareAttribute' => 'password']
        ];
    }

    public function signup()
    {
        $user = new User();
        $user->username = $this->username;
        try {
            $user->password = Yii::$app->security->generatePasswordHash($this->password);
            $user->access_token = Yii::$app->security->generateRandomString();
            $user->auth_key = Yii::$app->security->generateRandomString();
        } catch (Exception $e) {
            Yii::error("User was not saved. ".$e->getMessage());
            return false;
        }

        if($user->save()) {
            return true;
        }

        Yii::error("User was not saved. ".VarDumper::dumpAsString($user->errors));
        return false;
    }
}