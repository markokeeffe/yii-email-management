<?php namespace MOK\Email;
/**
 * Author:  Mark O'Keeffe
 * Date:    05/11/13
 */

use Yii;


class Emails extends \CApplicationComponent {

  /**
   * Names of objects that can be mapped to email layouts e.g. 'Content'
   * @var array
   */
  public $mappableObjects = array();

  /**
   * The path to a placeholder image
   * @var string
   */
  public $imagePlaceholder;

  /**
   * MailChimp API
   * @var \MailChimp
   */
  public $mailchimp;

  /**
   * @var string
   */
  public $mailchimpKey;

  /**
   * @var bool
   */
  public $useSubject = false;

  /**
   * @var string
   */
  public $subidModel;

  /**
   * @var string
   */
  public $subidKey;

  /**
   * @var string
   */
  public $subidVal;

  /**
   * Name of an email sender component
   * @var string
   */
  public $emailSenderComponent;

  /**
   * @var array
   */
  private $_subidValues;

  /**
   * @var \MOK\EmailSender\EmailSenderInterface
   */
  private $_emailSender;

  public function init()
  {
    parent::init();
    $this->mailchimp = new \Mailchimp($this->mailchimpKey);

    if ($this->emailSenderComponent) {
      $this->_emailSender = Yii::app()->getComponent($this->emailSenderComponent);
    }
  }

  /**
   * Get an array of subid values based on config options
   *
   * @return array|bool
   */
  public function getSubidValues()
  {

    if ($this->_subidValues === null) {
      if (!$this->subidModel || !$this->subidKey || !$this->subidVal) {
        $this->_subidValues = array();
      } elseif (!class_exists($this->subidModel)) {
        $this->_subidValues = array();
      } else {
        $model = new $this->subidModel;
        $this->_subidValues = \CHtml::listData($model->findAll(), $this->subidKey, $this->subidVal);
      }
    }

    return $this->_subidValues;

  }

  /**
   * Get the subid value by its ID
   *
   * @param $id
   *
   * @return null
   */
  public function getSubidValue($id)
  {
    $values = $this->getSubidValues();
    return (isset($values[$id]) ? $values[$id] : null);
  }

  /**
   * Get the email sender object
   *
   * @return \MOK\EmailSender\EmailSenderInterface
   * @throws \Exception
   */
  public function getEmailSender()
  {
    if ($this->_emailSender === null || !in_array('MOK\EmailSender\EmailSenderInterface', class_implements($this->_emailSender))) {
      throw new \Exception('"emailSenderComponent" must be an implementation of \MOK\EmailSender\EmailSenderInterface');
    }
    return $this->_emailSender;
  }

  /**
   * Get the source code of all layouts into an array,
   * optionally removing from the template source
   *
   * @param      $context \phpQueryObject
   * @param bool $remove
   *
   * @return array
   */
  public function storeLayouts($context, $remove=true)
  {
    $layoutStore = array();

    // Find all layouts in the repeater region
    foreach (pq('layout', $context) as $layout) {
      // Add the layout source to the layout store array
      $layoutStore[] = $layout;

      if ($remove) {
        // Remove the layout source from the email
        pq($layout)->remove();
      }

    }

    return $layoutStore;
  }

  /**
   * Add a saved layout back into email template source
   *
   * @param array         $layoutStore
   * @param \EmailLayout  $layout
   *
   * @throws \CHttpException
   * @return \phpQueryObject
   */
  public function addLayout($layoutStore, $layout)
  {

    // Look through the stored layouts HTML to find the matching label
    foreach ($layoutStore as $stored) {
      // If a saved layout is in the stored layouts, re-add it to the DOM
      if (pq($stored)->attr('label') === $layout->label) {
        // Save the association of this layout in the database
        $this->saveLayout($stored, $layout);
        // Add a layout configuration overlay to the source
        return pq($stored)->clone();
      }
    }
    throw new \CHttpException(500, 'Unable to store layout in template.');
  }

  /**
   * Insert saved data into a layout's HTML tags
   *
   * @param \phpQueryObject $context
   * @param \EmailLayout $layout
   * @param \EmailTagData[] $tagDatas
   *
   * @return \phpQueryObject
   */
  public function insertLayoutData(&$context, $layout, $tagDatas)
  {
    $tags = $layout->tags;

    // Insert saved data for all singleline tags
    foreach(pq('singleline', $context) as $i => $singleline) {
      $tagData = $this->templateTagData($i+1, 'singleline', $tags, $tagDatas);
      if ($tagData) {
        $this->injectTagData($singleline, 'singleline', $tagData);
      }
    }
    // Insert saved data for all multiline tags
    foreach(pq('multiline', $context) as $i => $multiline) {
      $tagData = $this->templateTagData($i+1, 'multiline', $tags, $tagDatas);
      if ($tagData) {
        $this->injectTagData($multiline, 'multiline', $tagData);
      }
    }
    // Insert saved data for all img tags
    foreach(pq('img[editable=true]', $context) as $i => $img) {
      $tagData = $this->templateTagData($i+1, 'img', $tags, $tagDatas);
      if ($tagData) {
        $this->injectTagData($img, 'img', $tagData);
      }
    }
  }

  /**
   * Send an email to the user stripping out any unnecessary source code
   * and inlining all CSS
   *
   * @param $from
   * @param $to
   * @param $subject
   * @param $body
   */
  public function send($from, $to, $subject, $body)
  {
    $body = $this->asHtml($body);
    $this->getEmailSender()->send($from, $to, $subject, $body);
  }

  /**
   * Send an email using a view file, layout and parameters
   *
   * @param       $from
   * @param       $to
   * @param       $subject
   * @param       $view
   * @param array $params
   * @param null  $layout
   */
  public function sendView($from, $to, $subject, $view, $params = array(), $layout = null)
  {
    $body = $this->getEmailSender()->buildView($view, $params, $layout);
    $this->send($from, $to, $subject, $body);
  }

  /**
   * Return the HTML of an email's source encoded for viewing in the browser
   *
   * @param $source
   *
   * @return string
   */
  public function asHtml($source)
  {
    // Fix the link href attributes to remove '&amp;'
    $source = $this->fixHrefs($source);

    // Remove Campaign Monitor layout tags
    $source = $this->stripLayoutTags($source);

    // Inline the CSS with Campaign Monitor inliner
    $source = $this->inlineCss($source);

    return $source;
  }

  /**
   * Return a conversion of HTML email to text
   *
   * @param $source
   *
   * @return string
   */
  public function asText($source)
  {
    // Fix the link href attributes to remove '&amp;'
    $source = $this->fixHrefs($source);

    // Remove Campaign Monitor layout tags
    $source = $this->stripLayoutTags($source);

    $source = $this->prepareForText($source);

    //output text only version
    $response = $this->mailchimp->helper->generateText('html', array(
      'html' => $source,
    ));
    $text =  $response['text'];

    return $this->tweakText($text);
  }

  /**
   * Un-encode '&amp;' entities from link href's
   *
   * @param $source
   *
   * @return mixed
   */
  public function fixHrefs($source)
  {
    $source = preg_replace_callback('/href=\"([^\"]+)\"/', function($matches) {
      return html_entity_decode($matches[0]);
    }, $source);
    return $source;
  }

  /**
   * Decode HTML encoded EmailVision dynamic tags
   *
   * @param string $source
   *
   * @return string
   */
  public function emvTags($source)
  {

    $replace = array(
      '%5BEMV%20FIELD%5D' => '[EMV FIELD]',
      '%5BEMV%20/FIELD%5D' => '[EMV /FIELD]',
      '%5BEMV%20INCLUDE%5D' => '[EMV INCLUDE]',
      '%5BEMV%20/INCLUDE%5D' => '[EMV /INCLUDE]',
    );

    return str_replace(array_keys($replace), array_values($replace), $source);
  }

  /**
   * Strip Campaign Monitor layout tags from email source
   *
   * @param string $html
   *
   * @return string
   */
  private function stripLayoutTags($html)
  {
    $find = array(
      '/<repeater>/',
      '/<\/repeater>/',
      '/<layout[^>]+>/',
      '/<\/layout>/',
      '/<singleline[^>]+>/',
      '/<\/singleline>/',
      '/<multiline[^>]+>/',
      '/<\/multiline>/',
      '/editable=\"true\"\slabel=\"[^\"]*\"/',
    );

    return preg_replace($find, '', $html);
  }

  /**
   * Prepare email source for conversion to text
   *
   * @param string $html
   *
   * @return string
   */
  private function prepareForText($html)
  {
    // Strip images
    $html = preg_replace('/<a[^<]+<img[^>]+>[^<]*<\/a>/', '', $html);

    return $html;
  }

  /**
   * Tweak converted text
   *
   * @param string $text
   *
   * @return string
   */
  private function tweakText($text)
  {
    // Replace bracketed links with quoted ones
    return preg_replace('/\((http[^\)]+)\)/', PHP_EOL.'$1'.PHP_EOL, $text);
  }

  /**
   * Inline CSS using the Campaign Monitor inliner
   *
   * @param string $html
   *
   * @return string
   */
  private function inlineCss($html)
  {
    $url = 'http://inliner.cm/inline.php';

    //open connection
    $ch = curl_init();

    //set the url, number of POST vars, POST data
    curl_setopt($ch,CURLOPT_URL, $url);
    curl_setopt($ch,CURLOPT_POST, true);
    curl_setopt($ch,CURLOPT_POSTFIELDS, 'code='.urlencode($html));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    //execute post
    $result = curl_exec($ch);

    //close connection
    curl_close($ch);

    if ($result = json_decode($result)) {
      return $result->HTML;
    }

    return $html;
  }

  /**
   * @param \phpQueryObject $context  The layout
   * @param \EmailLayout     $layout   The layout model
   */
  private function saveLayout($context, $layout)
  {

    // Count number of each tag type in this layout
    $tagCounts = array(
      'singleline' => 0,
      'multiline' => 0,
      'img' => 0,
    );

    $object = pq($context)->attr('data-object');

    // Create or update EmailTemplateTag instances for all singleline tags
    foreach(pq('singleline', $context) as $i => $singleline) {
      $this->templateTag($layout->id, $singleline, $i + 1, 'singleline', $tagCounts, $object);
    }
    // Create or update EmailTemplateTag instances for all multiline tags
    foreach(pq('multiline', $context) as $i => $multiline) {
      $this->templateTag($layout->id, $multiline, $i + 1, 'multiline', $tagCounts, $object);
    }
    // Create or update EmailTemplateTag instances for all img tags
    foreach(pq('img[editable=true]', $context) as $i => $img) {
      $this->templateTag($layout->id, $img, $i + 1, 'img', $tagCounts, $object);
      // Ensure all images without src values have placeholders
      if (!pq($img)->attr('src')) {
        pq($img)->attr('src', $this->imagePlaceholder(pq($img)->attr('width'), pq($img)->attr('height')));
      }
    }

    // Does this layout have an auto-fill behaviour specified?
    if ($fillBehaviour = pq($context)->attr('data-auto-fill')) {
      // Save it now
      $this->saveFillBehaviour($layout, $fillBehaviour);
    }

  }

  /**
   * Automatically save an email layout fill behaviour for the current layout
   *
   * @param \EmailLayout  $layout
   * @param string        $fillBehaviour
   */
  private function saveFillBehaviour($layout, $fillBehaviour)
  {
    $model = \EmailLayoutFillBehaviour::model()->findByAttributes(array(
      'class' => 'Email'.$fillBehaviour,
    ));
    if ($model) {
      $layout->fill_behaviour_id = $model->id;
      $layout->save(false, array('fill_behaviour_id'));
    }
  }

  /**
   * Update an existing or create a new 'EmailTemplateTag' model instance
   * for each template tag in the selected layout.
   *
   * @param int             $layout_id The layout ID
   * @param \phpQueryObject $context
   * @param int             $index     The index of the tag within the layout
   * @param string          $type      (singleline|multiline|img)
   * @param array           $tagCounts The current count of all tags in the email template
   *                                   up to this template
   * @param string          $object
   *
   * @return \EmailTag
   */
  private function templateTag($layout_id, $context, $index, $type, &$tagCounts, $object=null)
  {
    $index = $tagCounts[$type] + $index;
    $model = \EmailTag::model()->find(
      'layout_id = :layout_id AND `index` = :index AND type = :type',
      array(
        ':layout_id' => $layout_id,
        ':index' => $index,
        ':type' => $type,
      )
    );
    if (!$model) {
      $model = new \EmailTag;
      $model->attributes = array(
        'layout_id' => $layout_id,
        'index' => $index,
        'type' => $type,
        'label' => pq($context)->attr('label')
      );
      if ($type=='img') {
        $model->image_size = pq($context)->attr('width').'x'.pq($context)->attr('height');
      }
      $model->save();
    }

    if ($object) {
      $this->templateTagMap($context, $model, $object);
    }

    return $model;
  }


  /**
   * Save an EmailTagObject map for this tag if the necessary meta
   * attributes are provided in the template source.
   *
   * @param \phpQueryObject $context
   * @param \EmailTag       $model
   * @param string          $object
   */
  private function templateTagMap($context, $model, $object)
  {
    // The content attribute is required
    if (!$content = pq($context)->attr('data-content')) {
      return;
    }

    // Does a mapping already exist?
    if (!$map = \EmailTagObjectMap::model()->findByPk($model->id)) {
      // Create a new mapping
      $map = new \EmailTagObjectMap('saving');
    }

    // Set required attributes
    $map->attributes = array(
      'tag_id' => $model->id,
      'object' => $object,
      'content_attr' => $content,
    );

    // Set optional href attribute
    if ($href = pq($context)->attr('data-href')) {
      $map->href_attr = $href;
    }

    // Set optional alt attribute
    if ($alt = pq($context)->attr('data-alt')) {
      $map->alt_attr = $alt;
    }

    $map->save();
  }

  /**
   * Get an EmailTagData model instance for a particular tag in a layout
   *
   * @param int             $index
   * @param string          $type
   * @param \EmailTag[]     $tags
   * @param \EmailTagData[] $tagDatas
   *
   * @throws \CHttpException
   * @return \EmailTagData
   */
  private function templateTagData($index, $type, $tags, $tagDatas)
  {
    // Loop through the layout's tags to find one with matching
    // type and index
    foreach ($tags as $tag) {
      if ($tag->index == $index && $tag->type == $type) {
        $tagId = $tag->id;
        break;
      }
    }
    if (!isset($tagId)) {
      throw new \CHttpException(500, 'Invalid template tag index.');
    }
    // Loop through saved tag data to see if we have any data to insert
    // into this particular tag
    foreach ($tagDatas as $tagData) {
      if ($tagData->tag_id == $tagId) {
        return $tagData;
      }
    }
    return false;
  }

  /**
   * Inject saved data into HTML tag source code
   *
   * @param \phpQueryObject $context
   * @param string          $type
   * @param \EmailTagData   $tagData
   */
  private function injectTagData($context, $type, $tagData)
  {
    switch ($type) {
      case 'singleline' :
      case 'multiline' :
        $content = $tagData->content;
        if ($tagData->href) {
          $content = '<a href="'.$tagData->href.'">'.$content.'</a>';
        }
        pq($context)->html($content);
        break;
      case 'img' :
        pq($context)->attr('src', $tagData->content);
        pq($context)->attr('alt', $tagData->alt);
        if ($tagData->href) {
          $html = pq($context)->htmlOuter();
          pq($context)->replaceWith('<a href="'.$tagData->href.'">'.$html.'</a>');
        }
        break;
    }
  }

  /**
   * Generate a placeholder image using the specified dimensions.
   * If no height is specified, 60% af the width is specified.
   *
   * @param int      $width  The image width
   * @param bool|int $height The optional image height
   *
   * @return string The image placeholder URL
   */
  public function imagePlaceholder($width, $height = false)
  {
    if (!$height) {
      $height = $width * 0.6;
    }

    return str_replace(
      array('{width}', '{height}'),
      array($width, $height),
      Yii::app()->params['image_placeholder']
    );
  }

}
