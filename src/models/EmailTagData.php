<?php

/**
 * MOK Common Model
 *
 * This is the model class for table "email_tag_data".
 *
 * Columns for 'email_tag_data':
 * @property string $id
 * @property string $email_id
 * @property string $tag_id
 * @property string $content
 * @property string $href
 * @property string $alt
 *
 * Model relations:
 * @property EmailLayoutRepeated[] $repeatedLayouts
 * @property EmailTag $tag
 * @property Email $email
 */
class EmailTagData extends VActiveRecord
{
  /**
   * Returns the static model of the specified AR class.
   * @param string $className active record class name.
   * @return EmailTagData the static model class
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
    return 'email_tag_data';
  }

  /**
   * @return array validation rules for model attributes.
   */
  public function rules()
  {
    // NOTE: you should only define rules for those attributes that
    // will receive user inputs.
    return array(
      array('email_id, tag_id, content', 'required'),
      array('email_id, tag_id', 'length', 'max'=>10),
      array('href, alt', 'length', 'max'=>255),
        // The following rule is used by search().
      // Please remove those attributes that should not be searched.
      array('id, email_id, tag_id, content, href, alt', 'safe', 'on'=>'search'),
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
        'repeatedLayouts' => array(self::MANY_MANY, 'EmailLayoutRepeated', 'email_layout_repeated_tag_data(tag_data_id, layout_repeated_id)'),
        'tag' => array(self::BELONGS_TO, 'EmailTag', 'tag_id'),
        'email' => array(self::BELONGS_TO, 'Email', 'email_id'),
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
      'tag_id' => 'Tag',
      'content' => 'Content',
      'href' => 'Href',
      'alt' => 'Alt',
    );
  }

}
