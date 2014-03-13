<?php

class m140127_102008_email_subid extends CDbMigration
{


	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
    $this->alterColumn('email', 'subject', 'string');
    $this->addColumn('email', 'subid_id', 'int');
	}

	public function safeDown()
	{
    $this->alterColumn('email', 'subject', 'string NOT NULL');
    $this->dropColumn('email', 'subid_id');
	}

}