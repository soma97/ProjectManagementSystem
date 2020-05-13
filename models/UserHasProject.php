<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_has_project".
 *
 * @property int $user_id
 * @property int $project_id
 * @property string $role
 * @property int $internal
 *
 * @property Project $project
 * @property User $user
 */
class UserHasProject extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_has_project';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'project_id', 'role', 'internal'], 'required'],
            [['user_id', 'project_id', 'internal'], 'integer'],
            [['role'], 'string', 'max' => 45],
            [['user_id', 'project_id'], 'unique', 'targetAttribute' => ['user_id', 'project_id']],
            [['project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Project::className(), 'targetAttribute' => ['project_id' => 'id']],
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
            'project_id' => 'Project ID',
            'role' => 'Role',
            'internal' => 'Internal',
        ];
    }

    /**
     * Gets query for [[Project]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        return $this->hasOne(Project::className(), ['id' => 'project_id']);
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
