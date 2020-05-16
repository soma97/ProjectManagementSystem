<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "activity".
 *
 * @property int $id
 * @property string $name
 * @property int $estimated_hours
 * @property int $project_id
 * @property int|null $parent_activity_id
 * @property int $created_at
 * @property int $updated_at
 * @property string $done_until
 *
 * @property Activity $parentActivity
 * @property Activity[] $activities
 * @property Project $project
 * @property Effort[] $efforts
 * @property UserHasActivity[] $userHasActivities
 * @property User[] $users
 */
class Activity extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'activity';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'estimated_hours', 'project_id', 'done_until'], 'required'],
            [['estimated_hours', 'project_id', 'parent_activity_id', 'created_at', 'updated_at'], 'integer'],
            [['done_until'], 'safe'],
            [['name'], 'string', 'max' => 45],
            [['parent_activity_id'], 'exist', 'skipOnError' => true, 'targetClass' => Activity::className(), 'targetAttribute' => ['parent_activity_id' => 'id']],
            [['project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Project::className(), 'targetAttribute' => ['project_id' => 'id']],
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class
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
            'estimated_hours' => 'Estimated Hours',
            'project_id' => 'Project ID',
            'parent_activity_id' => 'Parent Activity ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'done_until' => 'Done Until',
        ];
    }

    /**
     * Gets query for [[ParentActivity]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getParentActivity()
    {
        return $this->hasOne(Activity::className(), ['id' => 'parent_activity_id']);
    }

    /**
     * Gets query for [[Activities]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getActivities()
    {
        return $this->hasMany(Activity::className(), ['parent_activity_id' => 'id']);
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
     * Gets query for [[Efforts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEfforts()
    {
        return $this->hasMany(Effort::className(), ['activity_id' => 'id']);
    }

    /**
     * Gets query for [[UserHasActivities]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserHasActivities()
    {
        return $this->hasMany(UserHasActivity::className(), ['activity_id' => 'id']);
    }

    /**
     * Gets query for [[Users]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['id' => 'user_id'])->viaTable('user_has_activity', ['activity_id' => 'id']);
    }

    public function getCompletionPercentage()
    {
        if($this->getActivities()->count() == 0) {
            $sumHours = 0;
            foreach ($this->getEfforts()->all() as $effort) {
                $sumHours += $effort['hours'];
            }
            return ($sumHours / $this->estimated_hours) * 100;
        }

        $childrenActivities = $this->getActivities();
        $numberOfChildrenActivities = $childrenActivities->count();
        $percentage = 0;
        foreach ($childrenActivities->all() as $activity)
        {
            $percentage += $activity->getCompletionPercentage() / $numberOfChildrenActivities;
        }
        return $percentage;
    }
}
