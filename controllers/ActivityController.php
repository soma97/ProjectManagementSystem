<?php

namespace app\controllers;

use app\models\AddUserForm;
use app\models\Effort;
use app\models\User;
use app\models\UserHasActivity;
use app\models\UserHasProject;
use Exception;
use Yii;
use app\models\Activity;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ActivityController implements the CRUD actions for Activity model.
 */
class ActivityController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['view', 'create', 'index'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return $this->getAccessRights(false, false);
                        }
                    ],
                    [
                        'actions' => ['update', 'delete'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return $this->getAccessRights(true, false);
                        }
                    ],
                    [
                        'actions' => ['removeuser'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return $this->getAccessRights(false, true);
                        }
                    ]
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function getAccessRights(bool $updateOrDeleteRights, bool $removeUserRights)
    {
        $activityId = Yii::$app->request->getQueryParam('id');
        $activity = null;
        if($activityId == null || ($activity = Activity::findOne($activityId)) == null) {
            return true;
        }
        $userProjectRelation = UserHasProject::findOne(['user_id' => Yii::$app->user->id, 'project_id' => $activity->project_id]);
        if ($userProjectRelation == null) {
            return false;
        }

        if($updateOrDeleteRights && ($userProjectRelation['role'] === 'supervisor' ||
                ($userProjectRelation['role'] === 'participant' && $userProjectRelation['internal'] == false))) {
            return false;
        }

        if($removeUserRights && ($userProjectRelation['role'] !== 'owner' &&
                (UserHasActivity::findOne(['user_id'=> Yii::$app->user->id, 'activity_id' => $activityId]) == null))) {
            return false;
        }

        return true;
    }

    /**
     * Lists all Activity models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Activity::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Activity model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        if(Yii::$app->request->isPost) {
            $effortModel = new Effort();
            $effortModel->activity_id = $id;
            $effortModel->user_id = Yii::$app->user->id;
            if (!$effortModel->load(Yii::$app->request->post()) || !$effortModel->save()) {
                return $this->redirect('/site/error');
            }
        }
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Activity model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($project_id = null, $parent_id = null)
    {
        $model = new Activity();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $model->parent_activity_id = $parent_id==null ? null : $parent_id;
        $model->project_id = $project_id;

        return $this->render('create', [
            'model' => $model
        ]);
    }

    /**
     * Updates an existing Activity model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $newUserModel = new AddUserForm();
        if($newUserModel->load(Yii::$app->request->post())) {
            try {
                $this->findModel($id)->link('users', User::findOne($newUserModel->user), ['role' => $newUserModel->role]);
                return $this->redirect(['view', 'id' => $id]);
            } catch (Exception $ex)
            {
                Yii::$app->session->setFlash('error', "User is already assigned to this activity.");
            }
        }

        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Activity model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $deletedActivity = $this->findModel($id);
        $projectId = $deletedActivity->project_id;
        $deletedActivity->delete();

        return $this->redirect('/project/view?id='.$projectId);
    }

    public function actionRemoveuser($id, $userId)
    {
        UserHasActivity::findOne(['user_id' => $userId, 'activity_id' => $id])->delete();
        return $this->redirect("/activity/view?id=$id");
    }

    /**
     * Finds the Activity model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Activity the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Activity::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
