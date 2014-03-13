<?php
/**
 * Author:  Mark O'Keeffe

 * Date:    25/11/13
 *
 * [Yii Workbench] LayoutFillBehaviourInterface.php
 */

namespace MOK\Email;


abstract class LayoutBehaviour {

  private $_params;

  /**
   * Set params from an array
   *
   * @param array $params
   */
  public function setParams($params)
  {
    $this->_params = $params;
  }

  /**
   * Get the value of a param
   *
   * @param string $key
   *
   * @return null|mixed
   */
  public function getParam($key)
  {
    return (isset($this->_params[$key]) ? $this->_params[$key] : null);
  }

}
