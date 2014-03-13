<?php

/**
 * MOK Common Model
 *
 * This is the model class for table "email_template".
 *
 * Columns for 'email_template':
 * @property string $id
 * @property string $title
 * @property string $body
 *
 * Model relations:
 * @property Email[] $emails
 * @property EmailLayout[] $layouts
 */
class EmailTemplate extends VActiveRecord
{
  /**
   * Returns the static model of the specified AR class.
   * @param string $className active record class name.
   * @return EmailTemplate the static model class
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
    return 'email_template';
  }

  /**
   * @return array validation rules for model attributes.
   */
  public function rules()
  {
    // NOTE: you should only define rules for those attributes that
    // will receive user inputs.
    return array(
      array('title, body', 'required'),
      array('title', 'length', 'max'=>255),
      array('is_fixed', 'safe'),
        // The following rule is used by search().
      // Please remove those attributes that should not be searched.
      array('id, title', 'safe', 'on'=>'search'),
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
      'emails' => array(self::HAS_MANY, 'Email', 'template_id'),
      'layouts' => array(self::HAS_MANY, 'EmailLayout', 'template_id', 'order' => '`index`'),
    );
  }

  /**
   * @return array customized attribute labels (name=>label)
   */
  public function attributeLabels()
  {
    return array(
      'id' => 'ID',
      'title' => 'Title',
      'body' => 'Body',
      'is_fixed' => 'Fixed?',
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
		$criteria->compare('title',$this->title,true);
    $criteria->compare('body',$this->body,true);

    return new CActiveDataProvider($this, array(
      'criteria'=>$criteria,
      'pagination'=>array(
        'pageSize'=>Yii::app()->params['page_size'],
      ),
    ));
  }

  /**
   * Add an array of layouts to an email template
   *
   * @param $layoutLabels
   */
  public function addLayouts($layoutLabels)
  {
    foreach ($layoutLabels as $label) {
      $layout = new EmailLayout;
      $layout->label = $label;
      $layout->template_id = $this->id;
      $layout->save();
    }
  }
}
