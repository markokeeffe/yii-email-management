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
class EmailController extends EmailBaseController
{

  /**
   * Manages all models.
   */
  public function actionIndex()
  {
    $model=new Email('search');

    $model->unsetAttributes();  // clear any default values
    if(isset($_GET['Email'])){
      $model->attributes=$_GET['Email'];
    }

    $this->render('index', compact('model'));
  }

  /**
   * Creates a new model.
   * If creation is successful, the browser will be redirected to the 'index'.
   */
  public function actionCreate()
  {
    $model=new Email;

    $this->performAjaxValidation($model, 'email-form');

    $templates = EmailTemplate::model()->findAll();

    $fillable = $this->getFillable($templates);

    if(isset($_POST['Email']))
    {
      $model->attributes=$_POST['Email'];
      if($model->save()){
        $this->redirect(array('content', 'id' => $model->id));
      }
    }

    $this->render('create', compact('model', 'templates', 'fillable'));
  }

  /**
   * Updates a particular model.
   * If update is successful, the browser will be redirected to the 'index'.
   * @param integer $id the ID of the model to be updated
   */
  public function actionUpdate($id)
  {
    $model=$this->loadModel($id);

    $this->performAjaxValidation($model, 'email-form');

    $templates = EmailTemplate::model()->findAll();

    $fillable = $this->getFillable($templates);


    if(isset($_POST['Email'])){
      $model->attributes=$_POST['Email'];
      if($model->save()){
        $this->redirect(array('index'));
      }
    }

    $this->render('update', compact('model', 'templates', 'fillable'));
  }

  /**
   * Manage the email content
   *
   * @param int $id The ID of the email
   *
   */
  public function actionContent($id)
  {
    $model=$this->loadModel($id);
    $this->render('content', compact('model'));
  }

  /**
   * Show the source code of an email
   *
   * @param int $id The email ID
   *
   */
  public function actionSource($id)
  {
    $model = $this->loadModel($id);
    $this->render('source', compact('model'));
  }

    /**
   * Show the text only version of an email
   *
   * @param int $id The email ID
   *
   */
  public function actionTextOnly($id)
  {
    $model = $this->loadModel($id);
    $this->render('textOnly', compact('model'));
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
   * Send a test to the user's email address
   *
   * @param $id
   */
  public function actionSendTest($id)
  {
    $model = $this->loadModel($id);
    $formModel = new EmailTestForm;

    if (isset(Yii::app()->user->email)) {
      $formModel->email = Yii::app()->user->email;
    }

    if (isset($_POST['EmailTestForm'])) {

      $formModel->attributes = $_POST['EmailTestForm'];

      $source = $this->generateSource($model);

      // send($from, $to, $subject, $body)
      $this->emails->send(
        'mark.ok@me.com',
        $formModel->email,
        'TEST: '.$model->title,
        $source
      );

      Yii::app()->user->setFlash('success', 'Test sent to '.$formModel->email);
    }

    $this->render('sendTest', compact('model', 'formModel'));
  }

  /**
   * Generate full HTML source of the email template with overlays for
   * managing layouts
   *
   * @param        $id
   * @param string $type
   * @param null   $overlays
   * @param null   $editing_id
   *
   * @throws CHttpException
   */
  public function actionBuildSource($id, $type='preview', $overlays=null, $editing_id=null)
  {

    if ($type != 'preview') {
      $overlays = false;
    }

    // Load the model
    $model=$this->loadModel($id);

    // Get the email source code
    $source = $this->generateSource($model, $overlays, $editing_id);

    switch ($type) {
      case 'preview' :
        echo $source;
        break;
      case 'source' :
        echo '<pre>'.PHP_EOL.CHtml::encode($this->emails->asHtml($source)).PHP_EOL.'</pre>';
        break;
      case 'text' :
        echo '<pre>'.PHP_EOL.$this->emails->asText($source).PHP_EOL.'</pre>';
        break;
      default :
        throw new CHttpException(500, 'Invalid email source type.');
    }

  }

  /**
   * Add a new EmailLayoutRepeated to an EmailLayout
   *
   * @param $id
   * @param $layout_id
   */
  public function actionAddRepeated($id, $layout_id)
  {
    $model = $this->loadModel($id);
    $repeated = new EmailLayoutRepeated();
    $repeated->email_id = $model->id;
    $repeated->layout_id = $layout_id;
    if ($repeated->save()) {
      $this->ajaxReturn('modal', array(
        'heading' => 'Repeatable Added',
        'body' => $this->renderPartial('_repeatableAdded', compact('layout_id'), true),
      ));
    }
  }

  /**
   * Sort the repeated layout items in a repeating layout
   *
   * @param $id
   * @param $layout_id
   */
  public function actionSortRepeated($id, $layout_id)
  {
    $model = $this->loadModel($id);

    $attrs = array(
      'email_id' => $model->id,
      'layout_id' => $layout_id,
    );

    // If no layout IDs have been submitted, then delete all layouts
    if (!isset($_POST['layouts'])) {
      EmailLayoutRepeated::model()->deleteAllByAttributes($attrs);
      return;
    }

    // Check for removed layouts
    foreach ($model->repeatedLayouts as $layout) {
      if (!in_array($layout->id, $_POST['layouts'])) {
        $layout->delete();
      }
    }

    // Update the order of all layouts
    $transaction=$model->dbConnection->beginTransaction();
    try
    {
      foreach ($_POST['layouts'] as $i => $layout_id) {
        $layout = EmailLayoutRepeated::model()->findByPk($layout_id);
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
   * Auto Fill content in any mapped layouts with auto-fill behaviours
   *
   * @param $id
   */
  public function actionAutoFill($id)
  {
    $model = $this->loadModel($id);

    $model->autoFillLayouts($_POST);

    $this->redirect(array('content', 'id' => $id));
  }

  /**
   * Returns the data model based on the primary key given in the GET variable.
   * If the data model is not found, an HTTP exception will be raised.
   * @param $id
   * @throws CHttpException
   * @return Email
*/
  public function loadModel($id)
  {
    $model=Email::model()->findByPk($id);
    if($model===null)
      throw new CHttpException(404,'The requested page does not exist.');
    return $model;
  }

  /**
   * Generate the HTML source for an email and return it as a string
   *
   * @param Email $model
   * @param bool  $overlays
   * @param int   $editing_id
   *
   * @return string
   */
  public function generateSource($model, $overlays = null, $editing_id = null)
  {
    // Parse the email source code
    phpQuery::newDocument($model->template->body);

    $context = pq('repeater:first');

    // Strip out the source of each layout into an array
    $layoutStore = $this->emails->storeLayouts($context, !$model->template->is_fixed);

    // Cycle through the model's saved email template layouts and re-add to source
    foreach ($model->template->layouts as $layout) {

      if ($layout->is_repeatable) {
        $layoutSource = $this->buildRepeatableLayout($model, $layout, $layoutStore, $overlays, $editing_id);
      } else {
        $layoutSource = $this->buildLayout($model, $layout, $layoutStore, $overlays);
      }

      if ($model->template->is_fixed) {
        pq($context)->find('layout[label="'.$layout->label.'"]')->replaceWith($layoutSource);
      } else {
        pq($context)->append($layoutSource);
      }

    }

    // Get the complete email source code
    $source = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"><html>'
      .pq('html')->html()
      .'</html>';

    // Ensure email vision fields are not encoded
    $source = $this->emails->emvTags($source);

    return $source;
  }

  /**
   * Get an array of template IDs that are fillable
   *
   * @param $templates
   *
   * @return array
   */
  private function getFillable($templates)
  {
    $fillable = array();
    foreach ($templates as $t) {
      foreach ($t->layouts as $l) {
        if ($l->fill_behaviour_id) {
          $fillable[] = $t->id;
        }
      }
    }
    return $fillable;
  }

  /**
   * Find the HTML of a layout in the $layoutStore, add in any saved
   * data, and add controls in an overlay if required
   *
   * @param Email       $model
   * @param EmailLayout $layout
   * @param array       $layoutStore
   * @param bool        $overlays
   *
   * @return phpQueryObject
   */
  private function buildLayout($model, $layout, $layoutStore, $overlays)
  {
    // Match the source HTML of a layout in the $layoutStore to this model
    $layoutSource = $this->emails->addLayout($layoutStore, $layout);

    // Get EmailTagData[] for this layout
    $tagDatas = $model->initTagDatas($layout);

    // Insert data from the $tagDatas into the layout HTML
    $this->emails->insertLayoutData($layoutSource, $layout, $tagDatas);

    if ($overlays) {
      // Add overlay controls to the layout HTML
      $this->addLayoutOverlays($layoutSource, $model, $layout);
    }

    return $layoutSource;
  }

  /**
   * Find the HTML of a layout in the $layoutStore, add in any saved
   * data, and add controls in an overlay if required
   *
   * @param Email               $model
   * @param EmailLayoutRepeated $layoutRepeated
   * @param array               $layoutStore
   * @param bool                $overlays
   *
   * @return phpQueryObject
   */
  private function buildLayoutRepeated($model, $layoutRepeated, $layoutStore, $overlays)
  {

    $layout = $layoutRepeated->layout;
    $layoutRepeated->layout->parentId = $layout->id;

    // Match the source HTML of a layout in the $layoutStore to this model
    $layoutSource = $this->emails->addLayout($layoutStore, $layout);

    // Get EmailTagData[] for this layout
    $tagDatas = $model->initTagDatas($layout, $layoutRepeated->id);

    // Insert data from the $tagDatas into the layout HTML
    $this->emails->insertLayoutData($layoutSource, $layout, $tagDatas);

    if ($overlays) {
      // Add overlay controls to the layout HTML
      $this->addLayoutOverlays($layoutSource, $model, $layout, $layoutRepeated);
    }

    return $layoutSource;
  }

  /**
   * Wrap a layout with a 'repeatable layout' container, load any repeating
   * contents inside, and place overlays to manage the container, and each
   * of the layouts inside
   *
   * @param Email       $model
   * @param EmailLayout $layout
   * @param array       $layoutStore
   * @param bool        $overlays
   * @param             $editing_id
   *
   * @return phpQueryObject
   */
  private function buildRepeatableLayout($model, $layout, $layoutStore, $overlays, $editing_id)
  {
    $container = pq($this->renderPartial('_repeatableLayoutContainer', compact('model', 'layout', 'editing_id'), true));

    $layoutRepeateds = EmailLayoutRepeated::model()->with('layout')->findAll(array(
      'condition' => 'email_id = :email_id AND layout_id = :layout_id',
      'params' => array(
        ':email_id' => $model->id,
        ':layout_id' => $layout->id,
      ),
      'order' => 't.`index`',
    ));

    if (empty($layoutRepeateds)) {
      $layoutSource = $this->buildLayout($model, $layout, $layoutStore, $overlays);
      pq($layoutSource)->addClass('placeholder');
      pq('.items', $container)->append($layoutSource);
    } else {
      foreach ($layoutRepeateds as $layoutRepeated) {
        $layoutSource = $this->buildLayoutRepeated($model, $layoutRepeated, $layoutStore, $overlays);
        pq('.items', $container)->append($layoutSource);
      }

    }

    if ($overlays) {
      return $container;
    }

    return pq('.items', $container)->contents();
  }

  /**
   * Add overlay controls to the HTML of an email layout
   *
   * @param phpQueryObject $layoutSource
   * @param Email          $model
   * @param EmailLayout    $layout
   * @param null           $layoutRepeated
   */
  private function addLayoutOverlays(&$layoutSource, $model, $layout, $layoutRepeated=null)
  {
    $view = $this->renderPartial('_layoutOverlay', compact('model', 'layout', 'layoutRepeated'), true);

    if ($layoutRepeated) {
      pq($layoutSource)->attr('id', 'layouts_'.$layoutRepeated->id);
    }
    // Add a unique ID to the layout using its name and index
    pq($layoutSource)->append($view);
  }

}
