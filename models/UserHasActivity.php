<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_has_activity".
 *
 * @property int $user_id
 * @property int $activity_id
 * @property string $role
 *
 * @property Activity $activity
 * @property User $user
 */
class UserHasActivity extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_has_activity';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'activity_id', 'role'], 'required'],
            [['user_id', 'activity_id'], 'integer'],
            [['role'], 'string', 'max' => 45],
            [['user_id', 'activity_id'], 'unique', 'targetAttribute' => ['user_id', 'activity_id']],
            [['activity_id'], 'exist', 'skipOnError' => true, 'targetClass' => Activity::className(), 'targetAttribute' => ['activity_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'activity_id' => 'Activity ID',
            'role' => 'Role',
        ];
    }

    /**
     * Gets query for [[Activity]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getActivity()
    {
        return $this->hasOne(Activity::className(), ['id' => 'activity_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
