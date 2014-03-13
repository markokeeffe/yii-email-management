<?php

class m131105_095538_create_email_tables extends CDbMigration
{


  public function safeUp()
  {
    $this->createTable('email_template', array(
      'id' => 'integer UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY',
      'title' => 'string NOT NULL',
      'body' => 'text NOT NULL',
      'is_fixed' => 'TINYINT(1) UNSIGNED NOT NULL DEFAULT "0"',
    ));

    $this->createTable('email', array(
      'id' => 'integer UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY',
      'template_id' => 'integer unsigned NOT NULL',
      'title' => 'string NOT NULL',
      'subject' => 'string NOT NULL',
      'fill_form' => 'string',
      'created_at' => 'TIMESTAMP',
      'updated_at' => 'TIMESTAMP',
      'is_injectable' => 'TINYINT(1) UNSIGNED NOT NULL DEFAULT "0"',
    ));
    $this->addForeignKey('email_fk1', 'email', 'template_id', 'email_template', 'id', 'CASCADE', 'CASCADE');

    $this->createTable('email_layout_fill_behaviour', array(
      'id' => 'SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY',
      'class' => 'string NOT NULL',
    ));

    $this->createTable('email_layout', array(
      'id' => 'integer UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY',
      'template_id' => 'integer unsigned NOT NULL',
      'fill_behaviour_id' => 'SMALLINT UNSIGNED',
      'label' => 'string NOT NULL',
      'index' => 'TINYINT(1) UNSIGNED NOT NULL',
      'is_repeatable' => 'TINYINT(1) UNSIGNED NOT NULL DEFAULT "0"',
    ));
    $this->addForeignKey('email_layout_fk1', 'email_layout', 'template_id', 'email_template', 'id', 'CASCADE', 'CASCADE');
    $this->addForeignKey('email_layout_fk2', 'email_layout', 'fill_behaviour_id', 'email_layout_fill_behaviour', 'id', 'CASCADE', 'CASCADE');

    $this->createTable('email_tag', array(
      'id' => 'integer UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY',
      'layout_id' => 'int unsigned NOT NULL',
      'type' => 'CHAR(10) NOT NULL',
      'index' => 'TINYINT(1) UNSIGNED',
      'label' => 'string',
      'image_size' => 'CHAR(10)',
    ));
    $this->addForeignKey('email_tag_fk1', 'email_tag', 'layout_id', 'email_layout', 'id', 'CASCADE', 'CASCADE');

    $this->createTable('email_tag_data', array(
      'id' => 'integer UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY',
      'email_id' => 'int unsigned NOT NULL',
      'tag_id' => 'int unsigned NOT NULL',
      'content' => 'text NOT NULL',
      'href' => 'string',
      'alt' => 'string',
    ));
    $this->addForeignKey('email_tag_data_fk1', 'email_tag_data', 'email_id', 'email', 'id', 'CASCADE', 'CASCADE');
    $this->addForeignKey('email_tag_data_fk2', 'email_tag_data', 'tag_id', 'email_tag', 'id', 'CASCADE', 'CASCADE');

    $this->createTable('email_tag_default_data', array(
      'tag_id' => 'integer UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY',
      'content' => 'text NOT NULL',
      'href' => 'string',
      'alt' => 'string',
    ));
    $this->addForeignKey('email_tag_default_data_fk1', 'email_tag_default_data', 'tag_id', 'email_tag', 'id', 'CASCADE', 'CASCADE');

    $this->createTable('email_tag_object_map', array(
      'tag_id' => 'integer UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY',
      'object' => 'string NOT NULL',
      'content_attr' => 'string NOT NULL',
      'href_attr' => 'string',
      'alt_attr' => 'string',
    ));
    $this->addForeignKey('email_tag_object_map_fk1', 'email_tag_object_map', 'tag_id', 'email_tag', 'id', 'CASCADE', 'CASCADE');

    $this->createTable('email_layout_repeated', array(
      'id' => 'integer UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY',
      'email_id' => 'integer unsigned NOT NULL',
      'layout_id' => 'integer unsigned NOT NULL',
      'index' => 'TINYINT(1) UNSIGNED NOT NULL',
      'object_id' => 'integer unsigned',
    ));
    $this->addForeignKey('email_layout_repeated_fk1', 'email_layout_repeated', 'email_id', 'email', 'id', 'CASCADE', 'CASCADE');
    $this->addForeignKey('email_layout_repeated_fk2', 'email_layout_repeated', 'layout_id', 'email_layout', 'id', 'CASCADE', 'CASCADE');

    $this->createTable('email_layout_repeated_tag_data', array(
      'layout_repeated_id' => 'integer UNSIGNED NOT NULL',
      'tag_data_id' => 'integer UNSIGNED NOT NULL',
      'index' => 'TINYINT(1) UNSIGNED NOT NULL',
    ));

    $this->addPrimaryKey('email_layout_repeated_tag_data_pk', 'email_layout_repeated_tag_data', 'layout_repeated_id, tag_data_id');
    $this->addForeignKey('email_layout_repeated_tag_data_fk1', 'email_layout_repeated_tag_data', 'layout_repeated_id', 'email_layout_repeated', 'id', 'CASCADE', 'CASCADE');
    $this->addForeignKey('email_layout_repeated_tag_data_fk2', 'email_layout_repeated_tag_data', 'tag_data_id', 'email_tag_data', 'id', 'CASCADE', 'CASCADE');

  }

  public function safeDown()
  {

    $this->dropTable('email_layout_repeated_tag_data');
    $this->dropTable('email_layout_repeated');
    $this->dropTable('email_tag_object_map');
    $this->dropTable('email_tag_default_data');
    $this->dropTable('email_tag_data');
    $this->dropTable('email_tag');
    $this->dropTable('email_layout');
    $this->dropTable('email_layout_fill_behaviour');
    $this->dropTable('email');
    $this->dropTable('email_template');

  }

}