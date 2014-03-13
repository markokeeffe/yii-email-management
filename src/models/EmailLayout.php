<?php

/**
 * MOK Common Model
 *
 * This is the model class for table "email_layout".
 *
 * Columns for 'email_layout':
 * @property string $id
 * @property string $template_id
 * @property string $label
 * @property integer $index
 * @property integer $is_repeatable
 * @property integer $fill_behaviour_id
 *
 * @property string $mappedObject
 * @property EmailTagDefaultData[] $defaultDatas
 *
 * Model relations:
 * @property EmailTemplate $template
 * @property EmailLayoutRepeated[] $repeatedLayouts
 * @property EmailTag[] $tags
 * @property EmailLayoutFillBehaviour $fillBehaviour
 */
class EmailLayout extends VActiveRecord
{

  /**
   * Does this layout have a parent layout? Store it's ID here
   * @var null|int
   */
  public $parentId;

  /**
   * Name of object mapped to this layout
   * @var null|string
   */
  private $_mappedObject;

  /**
   * Default tag data
   * @var EmailTagDefaultData[]
   */
  private $_defaultDatas;

  /**
   * Associated tag object maps
   * @var EmailTagObjectMap[]
   */
  private $_tagMaps;

  /**
   * The mapped object model instance
   * @var CModel
   */
  public $object;

  /**
   * Returns the static model of the specified AR class.
   * @param string $className active record class name.
   * @return EmailLayout the static model class
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
    return 'email_layout';
  }

  /**
   * @return array validation rules for model attributes.
   */
  public function rules()
  {
    // NOTE: you should only define rules for those attributes that
    // will receive user inputs.
    return array(
      array('template_id, label, index', 'required'),
      array('index, is_repeatable', 'numerical', 'integerOnly'=>true),
      array('template_id', 'length', 'max'=>11),
      array('label', 'length', 'max'=>255),
        // The following rule is used by search().
      // Please remove those attributes that should not be searched.
      array('id, template_id, label, index, is_repeatable', 'safe', 'on'=>'search'),
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
      'repeatedLayouts' => array(self::HAS_MANY, 'EmailLayoutRepeated', 'layout_id', 'order' => 'repeatedLayouts.index'),
      'tags' => array(self::HAS_MANY, 'EmailTag', 'layout_id'),
      'fillBehaviour' => array(self::BELONGS_TO, 'EmailLayoutFillBehaviour', 'fill_behaviour_id'),
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
        'label' => 'Label',
        'index' => 'Index',
        'is_repeatable' => 'Is Repeatable',
      );
  }

  /**
   * Set an index before creating
   *
   * @return bool
   */
  public function beforeValidate()
  {
    if($this->isNewRecord) {
      $query = Yii::app()->db->createCommand()
        ->select('(MAX(`index`) +1) nextIndex')
        ->from('email_layout')
        ->where('template_id = :template_id', array(
          ':template_id' => $this->template_id,
        ))
        ->queryRow();
      $this->index = (isset($query['nextIndex']) ? $query['nextIndex'] : 1);
    }
    return parent::beforeValidate();
  }

  /**
   * Get the name of an object mapped to this layout, or false
   *
   * @return bool|string
   */
  public function getMappedObject()
  {
    if ($this->_mappedObject === null) {
      $model = EmailTagObjectMap::model()->with('tag')->find(
        'tag.layout_id = :layout_id', array(
        ':layout_id' => $this->id
      ));
      if ($model) {
        $this->_mappedObject = $model->object;
      }
    }

    return $this->_mappedObject;
  }

  /**
   * Get any associated default tag data
   *
   * @return EmailTagDefaultData[]
   */
  public function getDefaultDatas()
  {
    if ($this->_defaultDatas === null) {

      $datas = array();

      foreach ($this->tags as $tag) {
        $data = EmailTagDefaultData::model()->findByPk($tag->id);
        if (!$data) {
          $data = new EmailTagDefaultData;
          $data->attributes = array(
            'tag_id' => $tag->id,
          );
        }
        $datas[] = $data;
      }

      $this->_defaultDatas = $datas;
    }

    return $this->_defaultDatas;
  }

  /**
   * Get an array of EmailTemplateTagMap models for the tags in this layout
   *
   * @param string $objectName
   *
   * @return EmailTagObjectMap[]
   */
  public function getTagMaps($objectName)
  {

    if ($this->_tagMaps === null) {
      // An array of layout tag model instances for this layout
      $tagMaps = array();

      foreach ($this->tags as $tag) {
        $tagMap = EmailTagObjectMap::model()->findByPk($tag->id);
        if (!$tagMap) {
          $tagMap = new EmailTagObjectMap('init');
          $tagMap->attributes = array(
            'tag_id' => $tag->id,
            'object' => $objectName,
          );
        }
        $tagMaps[] = $tagMap;
      }
      $this->_tagMaps = $tagMaps;
    }

    return $this->_tagMaps;
  }

  /**
   * Delete all tag maps associated with this layout
   */
  public function deleteTagMaps()
  {
    foreach ($this->tags as $tag) {
      $tag->tagObjectMap->delete();
    }
  }

  /**
   * Empty repeated layouts from this layout
   */
  public function clearRepeated()
  {
    foreach ($this->repeatedLayouts as $repeatedLayout) {
      $repeatedLayout->delete();
    }
  }

}
