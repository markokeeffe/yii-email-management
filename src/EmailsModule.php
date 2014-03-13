<?php

Yii::setPathOfAlias('Emails', dirname(__FILE__));

class VEmailModule extends CWebModule
{

  /**
   * @var string
   */
  public $cssUrl;

  /**
   * URL to jQuery asset
   * @var string
   */
  public $jQueryUrl;

  /**
   * URL to jQuery UI asset
   * @var string
   */
  public $jQueryUiUrl;

  /**
   * @var array
   */
  public $injectContent;

	public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
			'Emails.models.*',
      'Emails.controllers.*',
		));

	}

	public function beforeControllerAction($controller, $action)
	{
		if(parent::beforeControllerAction($controller, $action)) {
      $this->registerClientScript();
			// this method is called before any module controller action is performed
			// you may place customized code here
			return true;
		} else {
      return false;
    }

	}

  /**
   * Publish assets (re-publish on every request if YII_DEBUG is true)
   *
   * @throws CHttpException
   */
  protected function registerClientScript()
  {
    $cs=Yii::app()->clientScript;

    // Set the assets directory for this extension
    $assets = dirname(__FILE__).DS.'..'.DS.'public'.DS.'assets';
    // Set the directory to the Javascript behaviours
    $behaviours = $assets.DS.'js'.DS.'behaviors'.DS;
    $iframeBehaviours = $assets.DS.'js'.DS.'iframeBehaviors'.DS;

    // Is the assets directory valid?
    if (is_dir($assets)) {

      // Create a base URL for these assets using asset manager
      $baseUrl = Yii::app()->assetManager->publish($assets, false, -1, YII_DEBUG);

      $this->cssUrl = $baseUrl.'/css/';

      // Get the URL to jQuery asset
      if (isset($cs->packages['jquery'])) {
        $this->jQueryUrl = $cs->packages['jquery']['baseUrl'].$cs->packages['jquery']['js'][0];
      } else {
        $jQueryUrl = $cs->corePackages['jquery']['js'][0];
        if (!$jQueryUrl) {
          $cs->registerCoreScript('jquery', CClientScript::POS_END);
          $jQueryUrl = $cs->corePackages['jquery']['js'][0];
        }
        $this->jQueryUrl = $cs->getCoreScriptUrl('jquery').'/'.$jQueryUrl;
      }

      // Get the URL to jQuery UI asset
      if (isset($cs->packages['jquery.ui'])) {
        $this->jQueryUiUrl = $cs->packages['jquery.ui']['baseUrl'].$cs->packages['jquery.ui']['js'][0];
      } else {
        $jQueryUiUrl = $cs->corePackages['jquery.ui']['js'][0];
        if (!$jQueryUiUrl) {
          $cs->registerCoreScript('jquery.ui', CClientScript::POS_END);
          $jQueryUiUrl = $cs->corePackages['jquery.ui']['js'][0];
        }
        $this->jQueryUiUrl = $cs->getCoreScriptUrl('jquery.ui').'/'.$jQueryUiUrl;
      }


      $cs->registerScriptFile($baseUrl.'/js/initIframe.js', CClientScript::POS_END);


      // Process the behaviours directory
      foreach (glob($behaviours.'*') as $jsFile) {
        $jsFile = str_replace($behaviours, '', $jsFile);
        $cs->registerScriptFile($baseUrl.'/js/behaviors/'.$jsFile, CClientScript::POS_END);
      }

      // Process the iFrame behaviours directory
      foreach (glob($iframeBehaviours.'*') as $jsFile) {
        $jsFile = str_replace($iframeBehaviours, '', $jsFile);
        $cs->registerScriptFile($baseUrl.'/js/iframeBehaviors/'.$jsFile, CClientScript::POS_END);
      }

    } else {
      throw new CHttpException(500, __CLASS__ . ' - Error: Couldn\'t find assets to publish.');
    }

  }
}
