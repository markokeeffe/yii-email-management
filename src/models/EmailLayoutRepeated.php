<?php

/**
 * MOK Common Model
 *
 * This is the model class for table "email_layout_repeated".
 *
 * Columns for 'email_layout_repeated':
 * @property string $id
 * @property string $email_id
 * @property string $layout_id
 * @property integer $index
 * @property string $object_id
 *
 * Model relations:
 * @property EmailLayout $layout
 * @property Email $email
 * @property EmailTagData[] $tagDatas
 */
class EmailLayoutRepeated extends VActiveRecord
{
  /**
   * Returns the static model of the specified AR class.
   * @param string $className active record class name.
   * @return EmailLayoutRepeated the static model class
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
    return 'email_layout_repeated';
  }

  /**
   * @return array validation rules for model attributes.
   */
  public function rules()
  {
    // NOTE: you should only define rules for those attributes that
    // will receive user inputs.
    return array(
      array('email_id, layout_id, index', 'required'),
      array('index', 'numerical', 'integerOnly'=>true),
      array('email_id, layout_id, object_id', 'length', 'max'=>11),
        // The following rule is used by search().
      // Please remove those attributes that should not be searched.
      array('id, email_id, layout_id, index, object_id', 'safe', 'on'=>'search'),
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
      'email' => array(self::BELONGS_TO, 'Email', 'email_id'),
      'tagDatas' => array(self::MANY_MANY, 'EmailTagData', 'email_layout_repeated_tag_data(layout_repeated_id, tag_data_id)'),
    );
  }

  /**
   * @return array customized attribute labels (name=>label)
   */
  public function attributeLabels()
  {
    return array(
      'id' => 'ID',
      'email_id' => 'Email',
      'layout_id' => 'Layout',
      'index' => 'Index',
      'object_id' => 'Object',
    );
  }

  /**
   * Set timestamps before validation
   *
   * @return bool
   */
  public function beforeValidate()
  {
    if($this->isNewRecord) {
      $query = Yii::app()->db->createCommand()
        ->select('(MAX(`index`) +1) nextIndex')
        ->from('email_layout_repeated')
        ->where('email_id = :email_id AND layout_id = :layout_id', array(
          ':email_id' => $this->email_id,
          ':layout_id' => $this->layout_id,
        ))
        ->queryRow();
      $this->index = (isset($query['nextIndex']) ? $query['nextIndex'] : 1);
    }
    return parent::beforeValidate();
  }

  /**
   * When deleting a repeated layout instance, also delete any associated
   * EmailTagData[]
   *
   * @return bool
   */
  protected function beforeDelete()
  {
    foreach ($this->tagDatas as $tagData) {
      $tagData->delete();
    }
    return parent::beforeDelete();
  }

}
