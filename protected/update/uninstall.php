<?
$this->db->dropTable('{{cron_agent}}');
$this->db->dropTable($this->access->itemChildTable);
$this->db->dropTable($this->access->assignmentTable);
$this->db->dropTable($this->access->itemTable);