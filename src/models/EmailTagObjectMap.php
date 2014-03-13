<?php

/**
 * MOK Common Model
 *
 * This is the model class for table "email_tag_object_map".
 *
 * Columns for 'email_tag_object_map':
 * @property string $tag_id
 * @property string $object
 * @property string $content_attr
 * @property string $href_attr
 * @property string $alt_attr
 *
 * Model relations:
 * @property EmailTag $tag
 */
class EmailTagObjectMap extends VActiveRecord
{
  /**
   * Returns the static model of the specified AR class.
   * @param string $className active record class name.
   * @return EmailTagObjectMap the static model class
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
    return 'email_tag_object_map';
  }

  /**
   * @return array validation rules for model attributes.
   */
  public function rules()
  {
    // NOTE: you should only define rules for those attributes that
    // will receive user inputs.
    return array(
      array('tag_id, object', 'required', 'on' => 'init'),
      array('tag_id, object, content_attr', 'required', 'on' => 'saving, update'),
      array('object, content_attr, href_attr, alt_attr', 'length', 'max'=>255),
        // The following rule is used by search().
      // Please remove those attributes that should not be searched.
      array('tag_id, object, content_attr, href_attr, alt_attr', 'safe', 'on'=>'search'),
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
      'tag' => array(self::BELONGS_TO, 'EmailTag', 'tag_id'),
    );
  }

  /**
   * @return array customized attribute labels (name=>label)
   */
  public function attributeLabels()
  {
    return array(
      'tag_id' => 'Tag',
      'object' => 'Object',
      'content_attr' => 'Content Attr',
      'href_attr' => 'Href Attr',
      'alt_attr' => 'Alt Attr',
    );
  }

  /**
   * Return an array of model object names that can be used
   * when mapping a layout to use the data fields of the model
   * E.g. Selecting the 'Campaign' model will allow the campaign
   * title, description, image URL and link to be dynamically added
   * to a layout using the layout map fields specified
   */
  public function getObjects()
  {
    $objects = array(
      0 => 'Select object...',
    );
    foreach (Yii::app()->VEmailTemplate->mappableObjects as $class) {
      $objects[$class] = $class;
    }
    return $objects;
  }

}
