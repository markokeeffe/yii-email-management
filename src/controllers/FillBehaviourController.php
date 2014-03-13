<?php

/**
 * MOK Backend Controller
 *
 * A stripped down version of the Yii CRUD Controller with basic views:
 *
 * Index:           Table with search and delete capability
 * Create/Update:   Form with validation
 *
 */
class FillBehaviourController extends EmailBaseController
{

  /**
   * Manages all models.
   */
  public function actionIndex()
  {
    $model=new EmailLayoutFillBehaviour('search');
    $model->unsetAttributes();  // clear any default values
    if(isset($_GET['EmailLayoutFillBehaviour'])){
      $model->attributes=$_GET['EmailLayoutFillBehaviour'];
    }

    $this->render('index', compact('model'));
  }

  /**
   * Creates a new model.
   * If creation is successful, the browser will be redirected to the 'index'.
   */
  public function actionCreate()
  {
    $model=new EmailLayoutFillBehaviour;

    $this->performAjaxValidation($model, 'email-layout-fill-behaviour-form');

    if(isset($_POST['EmailLayoutFillBehaviour']))
    {
      $model->attributes=$_POST['EmailLayoutFillBehaviour'];
      if($model->save()){
        $this->redirect(array('index'));
      }
    }

    $this->render('create', compact('model'));
  }

  /**
   * Updates a particular model.
   * If update is successful, the browser will be redirected to the 'index'.
   * @param integer $id the ID of the model to be updated
   */
  public function actionUpdate($id)
  {
    $model=$this->loadModel($id);

    $this->performAjaxValidation($model, 'email-layout-fill-behaviour-form');

    if(isset($_POST['EmailLayoutFillBehaviour'])){
      $model->attributes=$_POST['EmailLayoutFillBehaviour'];
      if($model->save()){
        $this->redirect(array('index'));
      }
    }

    $this->render('update', compact('model'));
  }

  /**
   * Deletes a particular model.
   * If deletion is successful, the browser will be redirected to the 'index'.
   *
   * @param integer $id the ID of the model to be deleted
   *
   * @throws CHttpException
   */
  public function actionDelete($id)
  {
    if(Yii::app()->request->isPostRequest){
      // we only allow deletion via POST request
      $this->loadModel($id)->delete();

      // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
      if(!isset($_GET['ajax'])){
        $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
      }

    }else{
      throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
    }
  }

  /**
   * Returns the data model based on the primary key given in the GET variable.
   * If the data model is not found, an HTTP exception will be raised.
   *
   * @param $id ID of the model to be loaded
   *
   * @throws CHttpException
   *
   * @return \CActiveRecord
   */
  public function loadModel($id)
  {
    $model=EmailLayoutFillBehaviour::model()->findByPk($id);
    if($model===null)
      throw new CHttpException(404,'The requested page does not exist.');
    return $model;
  }

}
