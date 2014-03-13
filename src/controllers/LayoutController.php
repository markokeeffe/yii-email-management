<?php

/**
 * MOK Email Template Layout Controller
 */
class LayoutController extends EmailBaseController
{

  /**
   * @var EmailLayout
   */
  public $model;

  /**
   * @var Email
   */
  public $emailModel;

  /**
   * Map an object to an email layout.
   * E.g. If the layout 'Text with left-aligned image' is to be used to
   * dynamically load data from the 'campaign' table, The 'Campaign' object
   * can be mapped to the layout by selecting which fields from the campaign
   * table are used to populate the template tags within the layout.
   *
   * @param int    $id         The Layout ID
   * @param string $objectName The name of the object class to map
   *
   * @throws CHttpException
   */
  public function actionMapObject($id, $objectName)
  {

    // Load the EmailLayout model
    $model = $this->loadModel($id);

    if (!$objectName) {
      $model->deleteTagMaps();
      return;
    }

    if (!class_exists($objectName)) {
      throw new CHttpException(400, 'Invalid object type.');
    }

    // Get an array of EmailTagObjectMap models ready
    $tagMaps = $model->getTagMaps($objectName);

    if (isset($_POST['EmailTagObjectMap'])) {

      if ($this->validateObjectMaps($tagMaps, $_POST['EmailTagObjectMap'])) {

        // Save the changes to the database
        $this->saveValidated($tagMaps);

        $message = 'Object Mapping saved.';

        $this->ajaxReturn('modal', array(
          'heading' => 'Object Mapping Saved',
          'body' => $this->renderPartial('_layoutUpdated', compact('model', 'message'), true),
        ));

      }

    }

    // Instantiate the chosen object
    $object = new $objectName;

    // Create an array of attribute keys and labels for the selected
    // data object.
    $attrs = array(
      0 => 'Select attribute...',
    );
    // Do not add any ID fields to the attributes array
    foreach ($object->attributeLabels() as $key => $val) {
      if ($key !== 'id' && !preg_match('/\_id/', $key)) {
        $attrs[$key] = $val;
      }
    }

    $this->ajaxReturn('modal', array(
      'heading' => 'Map Layout to Object',
      'body' => $this->renderPartial('_mapLayout', compact('model', 'object', 'objectName', 'attrs', 'tagMaps'), true),
    ));

  }

  /**
   * Select an 'auto-fill behaviour' for a layout
   *
   * @param $id
   * @param $fill_behaviour_id
   */
  public function actionFillBehaviour($id, $fill_behaviour_id)
  {
    $model = $this->loadModel($id);
    $model->fill_behaviour_id = $fill_behaviour_id;
    $model->save(false, array('fill_behaviour_id'));

    $message = 'Fill Behaviour saved.';

    $this->ajaxReturn('modal', array(
      'heading' => 'Fill Behaviour Saved',
      'body' => $this->renderPartial('_layoutUpdated', compact('model', 'message'), true),
    ));
  }

  /**
   * Edit the content for a specific email layout
   *
   * @param int $id       The email layout ID
   * @param int $email_id The email ID
   * @param int $object_id
   * @param int $layout_repeated_id
   */
  public function actionEditContent($id, $email_id, $object_id=null, $layout_repeated_id=null)
  {
    $model = $this->loadModel($id);
    $email = $this->loadEmailModel($email_id);
    $datas = $email->initTagDatas($model, $layout_repeated_id);

    if (isset($_POST['EmailTagData'])) {
      if ($this->validateEmailTagDatas($datas, $_POST['EmailTagData'])) {

        // Save the changes to the database
        $this->saveValidated($datas, $layout_repeated_id);

        // Is this a repeated layout? Has an object been mapped to it?
        if ($layout_repeated_id && $object_id) {
          // Update the repeated layout with the object ID
          $layoutRepeated = $this->loadLayoutRepeatedModel($layout_repeated_id);
          $layoutRepeated->object_id = $object_id;
          $layoutRepeated->save(false, array('object_id'));
        }

        $message = 'Data for the '.$model->label.' layout saved.';

        $this->ajaxReturn('modal', array(
          'heading' => 'Email Data Saved',
          'body' => $this->renderPartial('_layoutUpdated', compact('model', 'email', 'layout_repeated_id', 'message'), true),
        ));
      }
    }

    // Are we getting data from a particular item that was found in search?
    if ($model->mappedObject && $object_id) {
      // Create instance of the object class
      $objectInstance = new $model->mappedObject;
      // Attempt to find it in the database
      $objectInstance = $objectInstance->findByPk($object_id);
      // Add the data to the EmailTagDatas[]
      if ($objectInstance) {
        $email->addDataFromObject($datas, $model, $objectInstance);
      }
    }

    $this->ajaxReturn('success', $this->renderPartial('_editContent', compact(
      'model',
      'email',
      'datas'
    ), true));

  }

  /**
   * Inject content into a repeated layout from an array of object IDs
   *
   * @param int    $id
   * @param int    $email_id
   * @param string $object_ids
   *
   * @throws CHttpException
   */
  public function actionInjectContent($id, $email_id, $object_ids)
  {
    $model = $this->loadModel($id);
    if (!$model->is_repeatable || !$model->mappedObject) {
      throw new CHttpException(500, 'You cannot inject content into this layout.');
    }
    $email = $this->loadEmailModel($email_id);

    // Delete all repeated layouts
    $model->clearRepeated();

    // Create instance of the object class
    $objectInstance = new $model->mappedObject;

    foreach (explode(',', $object_ids) as $object_id) {
      $this->injectObject($model, $email, $objectInstance, $object_id);
    }
  }

  /**
   * Edit the placeholder content in the template html
   *
   * @param int $id The template ID
   *
   */
  public function actionEditDefaultContent($id)
  {
    $model = $this->loadModel($id);
    $datas = $model->defaultDatas;

    if (isset($_POST['EmailTagDefaultData'])) {
      $valid = true;
      foreach ($_POST['EmailTagDefaultData'] as $i => $data) {
        if (isset($datas[$i])) {
          $datas[$i]->attributes = $data;
          if (!$datas[$i]->validate()) {
            $valid = false;
          }
        }
      }

      if ($valid) {
        foreach ($datas as $data) {
          $data->save();
        }

        $message = 'Email Data Saved';

        $this->rtn('modal', array(
          'heading' => $message,
          'body' => $this->renderPartial('_layoutUpdated', compact('model', 'message'), true),
        ));
      }
    }

    $this->ajaxReturn('success', $this->renderPartial('_editContent', compact('model', 'datas'), true));

  }

  /**
   *
   *
   * @param EmailLayout   $model
   * @param Email         $email
   * @param CActiveRecord $objectInstance
   * @param int           $object_id
   *
   * @return bool
   */
  private function injectObject($model, $email, $objectInstance, $object_id)
  {
    $transaction = Yii::app()->db->beginTransaction();
    try {
      // Attempt to find the object in the database
      $object = $objectInstance->findByPk($object_id);
      // The object exists
      if ($object) {
        // Create a new EmailLayoutRepeated for this object
        $layoutRepeated = new EmailLayoutRepeated;
        $layoutRepeated->email_id = $email->id;
        $layoutRepeated->layout_id = $model->id;
        $layoutRepeated->object_id = $object_id;
        $layoutRepeated->save();
        // Create some EmailTagData[] for this repeated layout
        $datas = $email->initTagDatas($model, $layoutRepeated->id);
        // Add the data from the object into the EmailTagData[]
        $email->addDataFromObject($datas, $model, $object, true);
        // Save the changes to the database
        $this->saveValidated($datas, $layoutRepeated->id);
      }
    } catch (CException $e) {
      $transaction->rollback();
      return false;
    }
    $transaction->commit();
    return true;

  }

  /**
   * Save the submitted layout mapping values for the selected layout
   *
   * @param EmailTagObjectMap[] $tagMaps
   * @param array $post
   *
   * @return bool
   */
  private function validateObjectMaps(&$tagMaps, $post)
  {
    $valid = true;

    // Cycle through the posted layout maps
    foreach ($tagMaps as $i => $tagMap) {

      if (!isset($post[$i])) continue;

      $attrs = $post[$i];

      // Unset any zero value fields
      if ($attrs['content_attr'] === '0') {
        unset($attrs['content_attr']);
      }
      if (isset($attrs['href_attr']) && !$attrs['href_attr']) {
        unset($attrs['href_attr']);
      }
      if (isset($attrs['alt_attr']) && !$attrs['alt_attr']) {
        unset($attrs['alt_attr']);
      }

      $tagMaps[$i]->scenario = 'saving';
      $tagMaps[$i]->attributes = $attrs;

      if (!$tagMaps[$i]->validate()) {
        $valid = false;
      }

    }

    return $valid;

  }

  /**
   * Validate an array of EmailTagData models
   *
   * @param EmailTagData[]  $emailDatas
   * @param array           $post
   *
   * @return bool
   */
  private function validateEmailTagDatas(&$emailDatas, $post)
  {
    $valid = true;

    foreach ($emailDatas as $i => $emailData) {

      if (!isset($post[$i])) continue;

      $emailDatas[$i]->attributes = $post[$i];
      if (!$emailDatas[$i]->validate()) {
        $valid = false;
      }

    }

    return $valid;
  }

  /**
   * Save an array of validated models
   *
   * @param CActiveRecord[] $models
   * @param int             $layout_repeated_id
   */
  private function saveValidated($models, $layout_repeated_id=null)
  {
    foreach ($models as $model) {
      $model->save();

      if ($layout_repeated_id) {
        $repeatedData = EmailLayoutRepeatedTagData::model()->findByPk(array(
          'layout_repeated_id' => $layout_repeated_id,
          'tag_data_id' => $model->id
        ));
        if(!$repeatedData){
          $repeatedData = new EmailLayoutRepeatedTagData();
          $repeatedData->attributes = array(
            'layout_repeated_id' => $layout_repeated_id,
            'tag_data_id' => $model->id,
            'index' => $model->tag->index
          );
          $repeatedData->save();
        }
      }
    }
  }

  /**
   * Toggle a layout as 'is_repeatable'
   *
   * @param $id
   */
  public function actionToggleIsRepeatable($id){
    $model = $this->loadModel($id);
    $model->is_repeatable = ($model->is_repeatable ? 0 : 1);
    $model->save();
  }



  /**
   * Returns the data model based on the primary key given in the GET variable.
   * If the data model is not found, an HTTP exception will be raised.
   * @param $id
   *
   * @throws CHttpException
   * @return EmailLayout
   */
  public function loadModel($id)
  {
    $model=EmailLayout::model()->with('template')->findByPk($id);
    if($model===null)
      throw new CHttpException(404,'The requested page does not exist.');
    $this->model = $model;
    return $model;
  }

  /**
   * Load an email model by ID
   *
   * @param $id
   *
   * @return Email
   * @throws CHttpException
   */
  public function loadEmailModel($id)
  {
    $model = Email::model()->findByPk($id);
    if (!$model) {
      throw new CHttpException(400, 'Invalid email ID.');
    }
    $this->emailModel = $model;
    return $model;
  }

  /**
   * Load an EmailLayoutRepeated model by ID
   *
   * @param $id
   *
   * @return EmailLayoutRepeated
   * @throws CHttpException
   */
  public function loadLayoutRepeatedModel($id)
  {
    $model = EmailLayoutRepeated::model()->findByPk($id);
    if (!$model) {
      throw new CHttpException(400, 'Invalid email ID.');
    }
    return $model;
  }


}
