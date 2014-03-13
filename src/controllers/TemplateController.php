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
class TemplateController extends EmailBaseController
{

  /**
   * Manages all models.
   */
  public function actionIndex()
  {
    $model=new EmailTemplate('search');

    $model->unsetAttributes();  // clear any default values
    if(isset($_GET['EmailTemplate'])){
      $model->attributes=$_GET['EmailTemplate'];
    }
    $this->render('index', compact('model'));
  }

  /**
   * Creates a new model.
   * If creation is successful, the browser will be redirected to the 'index'.
   */
  public function actionCreate()
  {
    $model=new EmailTemplate;

    $this->performAjaxValidation($model, 'email-template-form');

    if(isset($_POST['EmailTemplate']))
    {
      $model->attributes=$_POST['EmailTemplate'];
      if($model->save()){
        $this->redirect(array('configure', 'id' => $model->id));
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

    $this->performAjaxValidation($model, 'email-template-form');

    if(isset($_POST['EmailTemplate'])){
      $model->attributes=$_POST['EmailTemplate'];
      if($model->save()){
        $this->redirect(array('index'));
      }
    }

    $this->render('update', compact('model', 'cm_templates'));
  }

  /**
   * Generate a preview of the email template with the layout sections
   * removed and added to a list. The user can select the desired layout
   * sections to use in this version of the template and re-order them
   * as necessary. A model object's attributes can be mapped to the fields
   * in a layout section so that the data can be dynamically added to the
   * email.
   *
   * @param int $id The email template ID
   *
   */
  public function actionConfigure($id)
  {
    $model=$this->loadModel($id);

    // Parse the email source code
    phpQuery::newDocument($model->body);

    $layout_model = new EmailLayout;

    $context = pq('repeater:first');

    // Array to store the names af each layout in the repeater section
    $layouts = array();

    // Find each layout in the repeater section and add the layout name
    foreach (pq('layout', $context) as $layout) {
      $layouts[] = pq($layout)->attr('label');
    }

    if ($model->is_fixed && !count($model->layouts)) {
      // Remove any currently associated layouts
      $layout_model->deleteAllByAttributes(array(
        'template_id' => $this->id,
      ));
      $model->addLayouts($layouts);
    }

    $this->render('configure', compact('model', 'layout_model', 'layouts'));
  }

  /**
   * Generate full HTML source of the email template with overlays for
   * managing layouts
   *
   * @param $id
   */
  public function actionBuildSource($id)
  {
    // Load the model
    $model=$this->loadModel($id);

    // Parse the email source code
    phpQuery::newDocument($model->body);

    $context = pq('repeater:first');

    // Strip out the source of each layout into an array
    $layoutStore = $this->emails->storeLayouts($context, !$model->is_fixed);

    $container = pq($this->renderPartial('_layoutContainer', compact('model'), true));

    // Cycle through the model's saved email template layouts and re-add to source
    foreach ($model->layouts as $layout) {

      // Get the source HTML of the layout using data in the database
      $layoutSource = $this->emails->addLayout($layoutStore, $layout);

      $view = $this->renderPartial('_layoutOverlay', array(
        'model' => $model,
        'layout' => $layout,
      ), true);

      // Add a unique ID to the layout using its name and index
      pq($layoutSource)
        ->attr('id', 'layouts_'.$layout->id)
        // Add an overlay to the layout with its name and some controls
        ->append($view);

      if ($model->is_fixed) {
        pq($context)->find('layout[label="'.$layout->label.'"]')->replaceWith($layoutSource);
      } else {
        pq($container)->append($layoutSource);
      }

    }

    pq($context)->append($container);

    // Get the complete email source code
    $source = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"><html>'
      .pq('html')->html()
      .'</html>';

    echo $source;

  }

  /**
   * Add an available email template layout to the email template
   *
   * @param int     $id The email template ID
   * @param string  $label
   */
  public function actionAddLayout($id, $label)
  {
    $model=$this->loadModel($id);

    $layout = new EmailLayout;
    $layout->label = $label;
    $layout->template_id = $model->id;
    // Add it to the bottom of the layouts
    $layout->index = count($model->layouts) + 1;
    $layout->save();

    $this->redirect(array('configure', 'id' => $id));

  }

  /**
   * Sort the layouts within an email template
   *
   * @param int $id The email template ID
   *
   */
  public function actionSort($id)
  {
    $model=$this->loadModel($id);

    // If no layout IDs have been submitted, then delete all layouts
    if (!isset($_POST['layouts'])) {
      EmailLayout::model()->deleteAllByAttributes(
        array('template_id' => $model->id)
      );
      return;
    }

    // Check for removed layouts
    foreach ($model->layouts as $layout) {
      if (!in_array($layout->id, $_POST['layouts'])) {
        $layout->delete();
      }
    }

    // Update the order of all layouts
    $transaction = Yii::app()->db->beginTransaction();
    try
    {
      foreach ($_POST['layouts'] as $i => $layout_id) {
        $layout = EmailLayout::model()->findByPk($layout_id);
        $layout->index = $i+1;
        if (!$layout->save()) {
          throw new CException(CHtml::errorSummary($layout));
        }
      }

      $transaction->commit();
    }
    catch (Exception $e)
    {
      $this->rtn('error', $e->getMessage());
      $transaction->rollback();
    }

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
   * @param $id
   * @throws CHttpException
   * @return EmailTemplate
   */
  public function loadModel($id)
  {
    $model=EmailTemplate::model()->with('layouts')->findByPk($id);
    if($model===null)
      throw new CHttpException(404,'The requested page does not exist.');
    return $model;
  }

}
