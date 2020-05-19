<?php

namespace app\controllers;

use app\models\Activity;
use app\models\AddUserForm;
use app\models\Revenue;
use app\models\User;
use app\models\UserHasActivity;
use app\models\UserHasProject;
use kartik\mpdf\Pdf;
use ProjectAccessControl;
use Yii;
use app\models\Project;
use app\models\ProjectSearch;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ProjectController implements the CRUD actions for Project model.
 */
class ProjectController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
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
                        'actions' => ['update', 'delete', 'removeuser'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return $this->getAccessRights(true, false);
                        }
                    ],
                    [
                        'actions' => ['activities', 'report', 'efforts', 'revenue'],
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

    public function getAccessRights(bool $ownerRights, bool $reportRights)
    {
        $projectId = Yii::$app->request->getQueryParam('id');
        if($projectId == null) {
            return true;
        }
        $userProjectRelation = UserHasProject::findOne(['user_id' => Yii::$app->user->id, 'project_id' => $projectId]);
        if ($userProjectRelation == null) {
            return false;
        }
        if($ownerRights && $userProjectRelation['role'] !== 'owner')
        {
            return false;
        }
        if($reportRights && ($userProjectRelation['role'] !== 'owner' && $userProjectRelation['role'] !== 'supervisor'))
        {
            return false;
        }
        return true;
    }

    /**
     * Lists all Project models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProjectSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Project model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Project model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Project();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            User::findOne(Yii::$app->user->id)->link('projects', $model, ['role' => 'owner', 'internal' => true]);
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Project model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $newUserModel = new AddUserForm();
        if($newUserModel->load(Yii::$app->request->post())) {
            if($newUserModel->role === 'participant') {
                $newUserModel->internal = $newUserModel->internal ?: false; // ELVISSSSSSSSSSSSSSSSSSSSSSSSSSS :)
            } else{
                $newUserModel->internal = true;
            }
            $this->findModel($id)->link('users', User::findOne($newUserModel->user), ['role' => $newUserModel->role, 'internal' => $newUserModel->internal]);
            return $this->redirect(['view', 'id' => $id]);
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
     * Deletes an existing Project model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }


    public function actionRemoveuser($id, $userId)
    {
        UserHasProject::findOne(['user_id' => $userId, 'project_id' => $id])->delete();
        UserHasActivity::deleteAll(['user_id' => $userId]);
        return $this->redirect("/project/view?id=$id");
    }

    public function actionRevenue($id)
    {
        $owner = UserHasProject::findOne(['user_id'=>Yii::$app->user->id, 'project_id'=> $id]);
        if($owner == null || ($owner['role'] !== 'owner' && $owner['role'] !== 'supervisor'))
        {
            throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }

        $postModel = new Revenue();
        if(Yii::$app->request->isPost && $postModel->load(Yii::$app->request->post()))
        {
            $postModel->project_id = $id;
            $postModel->save();
        }

        return $this->render('revenue', [
            'model' => $this->findModel($id),
            'role' => $owner->role
        ]);
    }

    public function actionActivities($id) {
        return $this->render('activities', [
            'model' => $this->findModel($id)
        ]);
    }

    public function actionEfforts($id) {
        return $this->render('efforts', [
            'model' => $this->findModel($id)
        ]);
    }

    public function actionReport($id, $target)
    {
        try {
            $content = $this->renderPartial($target, [
                'model' => $this->findModel($id),
                'role' => 'none'
            ]);
        } catch(\Exception $ex){
           return $this->redirect('/site/error');
        }

        $pdf = new Pdf([
            'mode' => Pdf::MODE_CORE,
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_BROWSER,
            'content' => $content,
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            'cssInline' => '.kv-heading-1{font-size:18px}',
            'options' => ['title' => "Report for $target"],
            'methods' => [
                'SetHeader'=>["Report for $target"],
                'SetFooter'=>['{PAGENO}'],
            ]
        ]);

        return $pdf->render();
    }

    /**
     * Finds the Project model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Project the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Project::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
