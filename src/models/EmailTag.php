<?php

/**
 * MOK Common Model
 *
 * This is the model class for table "email_tag".
 *
 * Columns for 'email_tag':
 * @property string $id
 * @property string $layout_id
 * @property string $type
 * @property integer $index
 * @property string $label
 * @property string $image_size
 *
 * Model relations:
 * @property EmailLayout $layout
 * @property EmailTagData[] $tagDatas
 * @property EmailTagDefaultData $tagDefaultData
 * @property EmailTagObjectMap $tagObjectMap
 */
class EmailTag extends VActiveRecord
{
  /**
   * Returns the static model of the specified AR class.
   * @param string $className active record class name.
   * @return EmailTag the static model class
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
    return 'email_tag';
  }

  /**
   * @return array validation rules for model attributes.
   */
  public function rules()
  {
    // NOTE: you should only define rules for those attributes that
    // will receive user inputs.
    return array(
      array('layout_id, type', 'required'),
      array('index', 'numerical', 'integerOnly'=>true),
      array('layout_id, type, image_size', 'length', 'max'=>10),
      array('label', 'length', 'max'=>255),
        // The following rule is used by search().
      // Please remove those attributes that should not be searched.
      array('id, layout_id, type, index, label, image_size', 'safe', 'on'=>'search'),
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
        'layout' => array(self::BELONGS_TO, 'EmailLayout', 'layout_id'),
        'tagDatas' => array(self::HAS_MANY, 'EmailTagData', 'tag_id'),
        'tagDefaultData' => array(self::HAS_ONE, 'EmailTagDefaultData', 'tag_id'),
        'tagObjectMap' => array(self::HAS_ONE, 'EmailTagObjectMap', 'tag_id'),
      );
  }

  /**
   * @return array customized attribute labels (name=>label)
   */
  public function attributeLabels()
  {
    return array(
        'id' => 'ID',
        'layout_id' => 'Layout',
        'type' => 'Type',
        'index' => 'Index',
        'label' => 'Label',
        'image_size' => 'Image Size',
      );
  }

}
