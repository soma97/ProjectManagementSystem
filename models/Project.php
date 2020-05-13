<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "project".
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 *
 * @property Activity[] $activities
 * @property Revenue[] $revenues
 * @property UserHasProject[] $userHasProjects
 * @property User[] $users
 */
class Project extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'project';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 45],
            [['description'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'description' => 'Description',
        ];
    }

    /**
     * Gets query for [[Activities]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getActivities()
    {
        return $this->hasMany(Activity::className(), ['project_id' => 'id']);
    }

    public function getActivitiesFor($activityId)
    {
        return $this->hasMany(Activity::className(), ['project_id' => 'id'])->where(['parent_activity_id' => $activityId]);
    }

    public function getCompletionForActivity($activityId)
    {
        $activity = Activity::findOne($activityId);
        return $activity->getCompletionPercentage();
    }

    /**
     * Gets query for [[Revenues]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRevenues()
    {
        return $this->hasMany(Revenue::className(), ['project_id' => 'id']);
    }

    /**
     * Gets query for [[UserHasProjects]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserHasProjects()
    {
        return $this->hasMany(UserHasProject::className(), ['project_id' => 'id']);
    }

    /**
     * Gets query for [[Users]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['id' => 'user_id'])->viaTable('user_has_project', ['project_id' => 'id']);
    }
}
