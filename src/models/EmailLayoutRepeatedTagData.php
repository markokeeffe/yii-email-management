<?php

/**
 * MOK Common Model
 *
 * This is the model class for table "email_layout_repeated_tag_data".
 *
 * Columns for 'email_layout_repeated_tag_data':
 * @property string $layout_repeated_id
 * @property string $tag_data_id
 * @property integer $index
 */
class EmailLayoutRepeatedTagData extends VActiveRecord
{
  /**
   * Returns the static model of the specified AR class.
   * @param string $className active record class name.
   * @return EmailLayoutRepeatedTagData the static model class
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
    return 'email_layout_repeated_tag_data';
  }

  /**
   * @return array validation rules for model attributes.
   */
  public function rules()
  {
    // NOTE: you should only define rules for those attributes that
    // will receive user inputs.
    return array(
      array('layout_repeated_id, tag_data_id, index', 'required'),
      array('index', 'numerical', 'integerOnly'=>true),
      array('layout_repeated_id, tag_data_id', 'length', 'max'=>11),
        // The following rule is used by search().
      // Please remove those attributes that should not be searched.
      array('layout_repeated_id, tag_data_id, index', 'safe', 'on'=>'search'),
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
      );
  }

  /**
   * @return array customized attribute labels (name=>label)
   */
  public function attributeLabels()
  {
    return array(
        'layout_repeated_id' => 'Layout Repeated',
        'tag_data_id' => 'Tag Data',
        'index' => 'Index',
      );
  }

}
