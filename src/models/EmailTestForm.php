<?php
/**
 * Author:  Mark O'Keeffe

 * Date:    24/02/14
 *
 * [Yii Workbench] EmailTestForm.php
 */

class EmailTestForm extends CFormModel {

  public $email;

  public function rules()
  {
    return array(
      array('email', 'required'),
      array('email', 'email'),
    );
  }

}
