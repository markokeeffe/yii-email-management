<?php
/**
 * Author:  Mark O'Keeffe

 * Date:    05/11/13
 *
 * [Yii Workbench] EmailBaseController.php
 */

class EmailBaseController extends BackendController {

  /**
   * Email Template class
   * @var \MOK\Email\Emails
   */
  public $emails;

  public function init()
  {
    $this->emails = Yii::app()->Emails;
    parent::init();
  }

}
