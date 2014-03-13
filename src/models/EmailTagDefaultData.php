<?php

/**
 * MOK Common Model
 *
 * This is the model class for table "email_tag_default_data".
 *
 * Columns for 'email_tag_default_data':
 * @property string $tag_id
 * @property string $content
 * @property string $href
 * @property string $alt
 *
 * Model relations:
 * @property EmailTag $tag
 */
class EmailTagDefaultData extends VActiveRecord
{
  /**
   * Returns the static model of the specified AR class.
   * @param string $className active record class name.
   * @return EmailTagDefaultData the static model class
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
    return 'email_tag_default_data';
  }

  /**
   * @return array validation rules for model attributes.
   */
  public function rules()
  {
    // NOTE: you should only define rules for those attributes that
    // will receive user inputs.
    return array(
      array('tag_id, content', 'required'),
      array('href, alt', 'length', 'max'=>255),
        // The following rule is used by search().
      // Please remove those attributes that should not be searched.
      array('tag_id, content, href, alt', 'safe', 'on'=>'search'),
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
        'content' => 'Content',
        'href' => 'Href',
        'alt' => 'Alt',
      );
  }

}
