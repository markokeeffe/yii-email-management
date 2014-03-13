<?php

/**
 * MOK Common Model
 *
 * This is the model class for table "email".
 *
 * Columns for 'email':
 * @property string $id
 * @property string $template_id
 * @property int    $subid_id
 * @property string $title
 * @property string $subject
 * @property int    $is_injectable
 * @property string $created_at
 * @property string $updated_at
 * @property string $fill_form
 *
 * Model relations:
 * @property EmailTemplate $template
 * @property EmailLayoutRepeated[] $repeatedLayouts
 * @property EmailTagData[] $tagDatas
 */
class Email extends VActiveRecord
{

  /**
   * @var EmailLayout
   */
  private $_repeatableLayout;

  public function init()
  {
    parent::init();
    $this->fill_form = ($this->fill_form ? $this->fill_form : '//email/_fillForm');
  }

  /**
   * Set timestamps before validation
   *
   * @return bool
   */
  public function beforeValidate()
  {
    if($this->isNewRecord) {
      $this->created_at = date('Y-m-d H:i:s');
    }

    $this->updated_at = date('Y-m-d H:i:s');

    return parent::beforeValidate();
  }


  /**
   * Returns the static model of the specified AR class.
   * @param string $className active record class name.
   * @return Email the static model class
   */
  public static function model($className=__CLASS__)
  {
    return parent::model($className);
  }

  /**
   * @return string the associated database table name
   */
  public function tableName()
  {
    return 'email';
  }

  /**
   * @return array validation rules for model attributes.
   */
  public function rules()
  {
    // NOTE: you should only define rules for those attributes that
    // will receive user inputs.
    return array(
      array('template_id, title, created_at', 'required'),
      array('is_injectable', 'length', 'max'=>1),
      array('template_id', 'length', 'max'=>11),
      array('subid_id', 'numerical', 'integerOnly' => true),
      array('title, subject, fill_form', 'length', 'max'=>255),
      array('updated_at', 'safe'),
        // The following rule is used by search().
      // Please remove those attributes that should not be searched.
      array('id, template_id, title, subject, created_at, updated_at', 'safe', 'on'=>'search'),
    );
  }

  /**
   * @return array relational rules.
   */
  public function relations()
  {
    // NOTE: you may need to adjust the relation name and the related
    // class name for the relations automatically generated below.
    return array(
      'template' => array(self::BELONGS_TO, 'EmailTemplate', 'template_id'),
      'repeatedLayouts' => array(self::HAS_MANY, 'EmailLayoutRepeated', 'email_id'),
      'tagDatas' => array(self::HAS_MANY, 'EmailTagData', 'email_id'),
    );
  }

  /**
   * @return array customized attribute labels (name=>label)
   */
  public function attributeLabels()
  {
    return array(
      'id' => 'ID',
      'template_id' => 'Template',
      'subid_id' => 'Sub ID',
      'title' => 'Title',
      'subject' => 'Subject',
      'is_injectable' => 'Is Injectable?',
      'created_at' => 'Created At',
      'updated_at' => 'Updated At',
      'fill_form' => 'Auto-fill Form View',
    );
  }

  /**
   * Retrieves a list of models based on the current search/filter conditions.
   * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
   */
  public function search()
  {
    // Warning: Please modify the following code to remove attributes that
    // should not be searched.

    $criteria=new CDbCriteria;

  	$criteria->compare('id',$this->id,true);
		$criteria->compare('template_id',$this->template_id,true);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('subject',$this->subject,true);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('updated_at',$this->updated_at,true);

    return new CActiveDataProvider($this, array(
      'criteria'=>$criteria,
      'pagination'=>array(
        'pageSize'=>Yii::app()->params['page_size'],
      ),
    ));
  }

  /**
   * Get the first repeated layout in this email if there is one
   *
   * @return EmailLayout|null
   */
  public function getRepeatableLayout()
  {
    if ($this->_repeatableLayout === null) {
      foreach ($this->template->layouts as $layout) {
        if ($layout->is_repeatable) {
          $this->_repeatableLayout = $layout;
          break;
        }
      }
    }

    return $this->_repeatableLayout;
  }

  /**
   * Find, or instantiate EmailTagData instances for all tags in a
   * particular layout for this email
   *
   * @param EmailLayout $layout
   * @param int         $layout_repeated_id
   *
   * @return EmailTagData[]
   */
  public function initTagDatas($layout, $layout_repeated_id=null)
  {
    $datas = array();
    foreach ($layout->tags as $tag) {

      if ($layout_repeated_id) {
        $data = $this->initRepeatedTagData($tag, $layout_repeated_id);
      } else {
        $data = $this->initTagData($tag);
      }

      $datas[] = $data;
    }
    return $datas;
  }

  /**
   * Handle a posted auto-fill form and attempt to auto-fill all layouts
   * in this email.
   *
   * @param $post
   */
  public function autoFillLayouts($post)
  {
    $objectIds = array();
    foreach ($this->template->layouts as $layout) {
      if ($layout->fill_behaviour_id && $layout->fillBehaviour->class) {
        if (preg_match('/EmailFill/', $layout->fillBehaviour->class)) {
          $this->autoFillLayout($layout, $post, $objectIds);
        } elseif (preg_match('/EmailReplace/', $layout->fillBehaviour->class)) {
          $this->autoReplaceLayout($layout, $post);
        }
      }
    }

  }

  /**
   * Use an auto fill behaviour class to find an object for a layout.
   * Insert the data from the object into the layout.
   *
   * @param EmailLayout $layout
   * @param array       $post
   * @param array       $objectIds
   *
   * @return CActiveRecord
   */
  private function autoFillLayout($layout, $post, &$objectIds)
  {
    if (!$class = $this->initBehaviour($layout->fillBehaviour->class, $post)) {
      return false;
    }

    $object = $class->getObject($objectIds);
    if (!$object) {
      return false;
    }
    $objectIds[] = $object->id;
    $tagDatas = $this->initTagDatas($layout);
    $this->addDataFromObject($tagDatas, $layout, $object, true);
    return $object;
  }

  /**
   * Use an auto replace behaviour class to insert data into placeholders
   * in a layout's tags.
   *
   * @param EmailLayout $layout
   * @param array       $post
   *
   * @return bool
   */
  private function autoReplaceLayout($layout, $post)
  {

    if (!$class = $this->initBehaviour($layout->fillBehaviour->class, $post)) {
      return false;
    }

    // Get the EmailTagData instances for the layout in this email
    $tagDatas = $this->initTagDatas($layout);

    foreach ($tagDatas as $data) {

      // Find the default data for each tag in the layout
      if($data->tag->tagDefaultData){
        $data->attributes = $data->tag->tagDefaultData->attributes;
      }
      // Replace placeholders with meaningful content
      $data = $class->replace($data, $post);
      $data->save();

    }

    return true;
  }

  /**
   * Initialise an auto fill behaviour class
   *
   * @param $className
   * @param $post
   *
   * @return bool|\MOK\Email\LayoutFillBehaviour|\MOK\Email\LayoutReplaceBehaviour
   */
  private function initBehaviour($className, $post)
  {

    if (!class_exists($className)) {
      return false;
    }

    $class = new $className;

    $class->setParams($post);

    return $class;

  }

  /**
   * Attempt to find an EmailTagData instance for the current tag.
   * If not, return a new instance
   *
   * @param $tag
   *
   * @return EmailTagData
   */
  private function initTagData($tag)
  {
    $attrs = array(
      'email_id' => $this->id,
      'tag_id' => $tag->id,
    );
    $data = EmailTagData::model()->findByAttributes($attrs);

    if (!$data) {
      $data = $this->newTagData($tag);
    }
    return $data;
  }

  /**
   * Attempt to find an EmailTagData instance with related repeated
   * layout. If not, return a new instance of EmailTagData
   *
   * @param EmailTag  $tag
   * @param int       $layout_repeated_id
   *
   * @return EmailTagData
   */
  private function initRepeatedTagData($tag, $layout_repeated_id)
  {
    $data = EmailTagData::model()->with('repeatedLayouts')->find(
      'repeatedLayouts.id = :layout_repeated_id AND
       t.email_id = :email_id AND
       t.tag_id = :tag_id',
      array(
        ':layout_repeated_id' => $layout_repeated_id,
        ':email_id' => $this->id,
        ':tag_id' => $tag->id,
      )
    );

    if (!$data) {
      $data = $this->newTagData($tag);
    }

    return $data;
  }

  /**
   * Create a new EmailTagData instance, loading it with
   * default data if any EmailTagDefaultData exists
   *
   * @param EmailTag $tag
   *
   * @return EmailTagData
   */
  private function newTagData($tag)
  {
    $data = new EmailTagData;
    $data->email_id = $this->id;
    $data->tag_id = $tag->id;

    if($tag->tagDefaultData){
      $data->attributes = $tag->tagDefaultData->attributes;
    }

    return $data;
  }


  /**
   * Set EmailTagData from an instance of an object using a tag object map
   *
   * @param EmailTagData[] $datas
   * @param EmailLayout    $layout
   * @param CActiveRecord  $objectInstance
   * @param bool           $save
   *
   * @throws CException
   */
  public function addDataFromObject(&$datas, $layout, $objectInstance, $save=false)
  {

    // Attempt to insert data into each tag
    foreach ($layout->tags as $tag) {

      if (!$tag || !$map = $tag->tagObjectMap) continue;

      // Find the email data for this particular tag
      foreach ($datas as $data) {

        if (!$data) continue;

        if ($data->tag_id == $map->tag_id) {
          // Set the content
          $data->content = $objectInstance->{$map->content_attr};
          // Has a link URL been specified?
          if ($map->href_attr) {
            if (method_exists($objectInstance, $map->href_attr)) {
              // Set the link URL
              $data->href = $objectInstance->{$map->href_attr}($this->subid_id);
            } else {
              // Set the link URL
              $data->href = $objectInstance->{$map->href_attr};
            }
          }
          // Has an alt value been specified?
          if ($map->alt_attr) {
            // Set the alt value
            $data->alt = $objectInstance->{$map->alt_attr};
          }
        }
      }

    }

    if ($save) {
      foreach ($datas as $data) {
        if (!$data->save()) {
          throw new CException(CHtml::errorSummary($data));
        }
      }
    }

  }

}
