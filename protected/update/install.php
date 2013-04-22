<?
	if($this->isNotInstallEyiiVersion('1.0.0')){
		$this->db->createTable($this->access->itemTable, array(
			'name' => 'varchar(64) not null',
			'type' => 'integer not null',
			'description' => 'text',
			'bizrule' => 'text',
			'data' => 'text',
			'primary key (`name`)'
		),'engine InnoDB');

		$this->db->createTable($this->access->itemChildTable, array(
			'parent' => 'varchar(64) not null',
			'child' => 'varchar(64) not null',
			'primary key (`parent`,`child`)',
			'foreign key (`parent`) references `'.$this->access->itemTable.'` (`name`) on delete cascade on update cascade',
			'foreign key (`child`) references `'.$this->access->itemTable.'` (`name`) on delete cascade on update cascade'
		),'engine InnoDB');

		$this->db->createTable($this->access->assignmentTable, array(
			'itemname' => 'varchar(64) not null',
			'userid' => 'varchar(64) not null',
			'bizrule' => 'text',
			'data' => 'text',
			'primary key (`itemname`,`userid`)',
			'foreign key (`itemname`) references `'.$this->access->itemTable.'` (`name`) on delete cascade on update cascade',
		),'engine InnoDB');

		$Admin = $this->access->createRole('Admin','Admin');
		$User = $this->access->createRole('User','User');
		$Guest = $this->access->createRole('Guest','Guest');



		$EyiiTask = $this->access->createTask('eyii.*','All System Tools Access');
		$Admin->addChild('eyii.*');

		$this->access->createOperation('eyii.Install','Access for update the system');
		$EyiiTask->addChild('eyii.Install');

		$this->access->createOperation('eyii.BackEnd','Access to BackEnd');
		$EyiiTask->addChild('eyii.BackEnd');



		$this->setInstallEyiiVersion('1.0.0');
	}else{		$EyiiTask = $this->access->getAuthItem('eyii.*');
	}

	if($this->isNotInstallEyiiVersion('1.0.1')){
		$this->db->createTable('{{cron_agent}}', array(
			'id' => 'pk',
			'module' => 'varchar(100) not null',
			'function' => 'varchar(100) not null',
			'interval' => 'integer',
			'data' => 'text',
			'nextStart' => 'datetime',
			'lastStart' => 'datetime',
			'status' => 'integer default 0',
		),'engine InnoDB');

		$this->setInstallEyiiVersion('1.0.1');
	}

	if($this->isNotInstallEyiiVersion('1.0.2')){
		$this->access->createOperation('eyii.Settings','Access to edit Settings');
		$EyiiTask->addChild('eyii.Settings');
		$this->setInstallEyiiVersion('1.0.2');
	}