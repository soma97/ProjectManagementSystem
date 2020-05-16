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
                'only' => ['create', 'update', 'delete', 'index'],
                'rules' => [
                    [
                        'actions' => ['create', 'update', 'delete', 'index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
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

    public function beforeAction($action)
    {
        $requestedId = Yii::$app->request->getQueryParam('id');
        if($requestedId != null) {
            $activity = Activity::findOne($requestedId);
            if($activity != null) {
                $userProjectRelation = UserHasProject::findOne(['user_id' => Yii::$app->user->id, 'project_id' => $activity->project_id]);
                if ($userProjectRelation == null) {
                    throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
                }
            }
        }
        return parent::beforeAction($action);
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
        $activity = Activity::findOne($id);
        $owner = UserHasProject::findOne(['user_id'=>Yii::$app->user->id, 'project_id'=> $activity->project_id]);
        if(UserHasActivity::findOne(['user_id'=> Yii::$app->user->id, 'activity_id' => $id]) != null || $owner['role'] === 'owner')
        {
            UserHasActivity::findOne(['user_id'=> $userId, 'activity_id' => $id])->delete();
            return $this->redirect("/activity/view?id=$id");
        }
        throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
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
