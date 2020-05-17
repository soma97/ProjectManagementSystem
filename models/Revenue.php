<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "revenue".
 *
 * @property int $id
 * @property string $description
 * @property float $amount
 * @property int $created_at
 * @property int $updated_at
 * @property int $project_id
 * @property string $type
 *
 * @property Project $project
 */
class Revenue extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'revenue';
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
    public function rules()
    {
        return [
            [['description', 'amount', 'project_id', 'type'], 'required'],
            [['amount'], 'number', 'min' => 0],
            [['created_at', 'updated_at', 'project_id'], 'integer'],
            [['description'], 'string', 'max' => 1000],
            [['type'], 'string', 'max' => 10],
            [['project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Project::className(), 'targetAttribute' => ['project_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'description' => 'Description',
            'amount' => 'Amount [€]',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'project_id' => 'Project ID',
            'type' => 'Type',
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
}
