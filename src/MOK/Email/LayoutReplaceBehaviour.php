<?php
/**
 * Author:  Mark O'Keeffe

 * Date:    25/11/13
 *
 * [Yii Workbench] LayoutFillBehaviourInterface.php
 */

namespace MOK\Email;


abstract class LayoutReplaceBehaviour extends LayoutBehaviour {

  /**
   * Return an instance of a mapped object by performing a specific DB query
   *
   * @param \EmailTagData $data
   * @param array         $post
   *
   * @return
   */
  abstract function replace($data, $post);

}
