<?php
/**
 * Created by [azamat/istranger]
 */

namespace common\scheduler\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use common\components\Session;
use common\scheduler\models\SchedulerTask;
use common\scheduler\models\SchedulerTaskProcess;
use common\scheduler\models\search\SchedulerTaskSearch;
use common\scheduler\models\search\SchedulerTaskProcessSearch;
use common\scheduler\exceptions\CouldNotSaveException;
use common\scheduler\presenters\SchedulerPresenter;

/**
 * SchedulerController implements the CRUD actions for SchedulerTask model.
 *
 * @method SchedulerPresenter _getPresenter() Class presenter.
 */
class SchedulerController extends \backend\components\BackendController
{
    /**
     * @var string the ID of the action that is used when the action ID is not specified
     * in the request. Defaults to 'index'.
     */
    public $defaultAction = 'task-index';

    /**
     * @var string Presenter class.
     */
    public $presenterClass = SchedulerPresenter::class;

    /**
     * Returns a list of behaviors that this component should behave as.
     *
     * Detailed documentation and format see in {@link \yii\base\Component::behaviors()}.
     *
     * @return array the behavior configurations.
     */
    public function behaviors(): array
    {
        return array_merge(parent::behaviors(), [
            'verbs' => [
                'class'   => VerbFilter::class,
                'actions' => [
                    'task-delete' => ['post'],
                    'task-start'  => ['post'],
                ],
            ],
        ]);
    }

    /**
     * Lists all tasks.
     *
     * @return string
     */
    public function actionTaskIndex()
    {
        $this->rememberReturnUrl();

        $searchModel     = new SchedulerTaskSearch();
        $dataProvider    = $searchModel->prepareDataProvider(Yii::$app->request->get());
        $lastScheduledAt = $this->_getPresenter()->getLastScheduledTimestamp();

        return $this->render('task/index', [
            'dataProvider'      => $dataProvider,
            'searchModel'       => $searchModel,
            'lastExecInfo'      => $this->_getPresenter()->prepareLastExecInfo($lastScheduledAt),
            'taskInfoFormatter' => $this->_getPresenter()->factoryTaskInfoFormatter(),
        ]);
    }

    /**
     * Displays a single task.
     *
     * @param integer $id
     *
     * @return string
     * @throws NotFoundHttpException  if the model cannot be found
     */
    public function actionTaskView($id)
    {
        $model = $this->_findTaskModel($id);

        $searchModel         = new SchedulerTaskProcessSearch();
        $taskProcessProvider = $searchModel->prepareDataProvider(Yii::$app->request->get(), $id);

        return $this->render('task/view', [
            'model'                  => $model,
            'taskProcessProvider'    => $taskProcessProvider,
            'taskProcessSearchModel' => $searchModel,
            'cronPreviousRunDates'   => $this->_getPresenter()->getPrevRunDates($model),
            'cronNextRunDates'       => $this->_getPresenter()->getNextRunDates($model),
        ]);
    }

    /**
     * Starts specified task.
     *
     * @param integer $id
     *
     * @return \yii\web\Response
     * @throws NotFoundHttpException  if the model cannot be found
     * @throws CouldNotSaveException  if the log model cannot be saved
     */
    public function actionTaskStart($id)
    {
        $model = $this->_findTaskModel($id);

        // Start tasks
        Yii::$app->schedulerService->runTasks([$model], true);

        // Display the message
        Yii::$app->session->addFlash(Session::FLASH_TYPE_SUCCESS, Yii::t('app', 'Task started successfully.'));

        return $this->redirect($this->getReturnUrl(['task-index']));
    }

    /**
     * Displays a single task process.
     *
     * @param integer $id
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionTaskProcessView($id)
    {
        $model = $this->_findTaskProcessModel($id);

        return $this->render('task-process/view', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the SchedulerTask model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @return SchedulerTask the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Exception
     */
    protected function _findTaskModel($id): SchedulerTask
    {
        return static::findOneModel($id, SchedulerTask::class);
    }

    /**
     * Finds the SchedulerTaskProcess model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @return SchedulerTaskProcess the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Exception
     */
    protected function _findTaskProcessModel($id): SchedulerTaskProcess
    {
        return static::findOneModel($id, SchedulerTaskProcess::class);
    }
}
