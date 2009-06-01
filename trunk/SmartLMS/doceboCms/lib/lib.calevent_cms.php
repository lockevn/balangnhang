<?php
class DoceboCalEvent_cms extends DoceboCalEvent_core {


	var $cal_id;

	function setCalId($cal_id) {
		$this->cal_id =(int)$cal_id;
	}

	function assignVar() {
		$this->id=importVar("id");
		$this->cal_id =importVar('cal_id');
		$this->calEventClass=importVar("calEventClass");
		$this->start_year=importVar("start_year");
		$this->start_month=importVar("start_month");
		$this->start_day=importVar("start_day");

		$this->_year=$this->start_year;
		$this->_month=$this->start_month;
		$this->_day=$this->start_day;

		$this->start_hour=importVar("start_hour");
		$this->start_min=importVar("start_min");
		$this->start_sec=importVar("start_sec");
		$this->end_year=importVar("end_year");
		$this->end_month=importVar("end_month");
		$this->end_day=importVar("end_day");
		$this->end_hour=importVar("end_hour");
		$this->end_min=importVar("end_min");
		$this->end_sec=importVar("end_sec");

		$this->title=importVar("title");
		$this->description=importVar("event_description");

		$this->_owner=importVar("_owner");;
		if (!$this->_owner) $this->_owner==$GLOBALS["current_user"]->getIdSt();

		$this->category=importVar("category");
		$this->private=importVar("private");
	}

	function getForm() {
		// Edit for CMS:
		// set "defvalue":"" of private field so by default is not private.
		$form_obj='{
		"form":[
			{"type":"structure","value":"row","permissions":"2"},
			{"type":"structure","value":"cell","field_class":"label","permissions":"2"},
			{"type":"label","value":"_PRIVATE","permissions":"2"},
			{"type":"structure","value":"/cell","permissions":"2"},
			{"type":"structure","value":"cell","field_class":"field","permissions":"2"},
			{"type":"checkbox","id":"private","permissions":"2","defvalue":""},
			{"type":"structure","value":"/cell","permissions":"2"},
			{"type":"structure","value":"/row","permissions":"2"},
			{"type":"structure","value":"row"},
			{"type":"structure","value":"cell","field_class":"label"},
			{"type":"label","value":"_START"},
			{"type":"structure","value":"/cell"},
			{"type":"structure","value":"cell","field_class":"field"},
			{"type":"day","id":"start_day"},
			{"type":"string","value":"/"},
			{"type":"month","id":"start_month"},
			{"type":"string","value":"/"},
			{"type":"year","id":"start_year"},
			{"type":"string","value":"&nbsp;"},
			{"type":"hour","id":"start_hour"},
			{"type":"string","value":":"},
			{"type":"min","id":"start_min"},
			{"type":"string","value":":"},
			{"type":"sec","id":"start_sec"},
			{"type":"structure","value":"/cell"},
			{"type":"structure","value":"/row"},
			{"type":"structure","value":"row"},
			{"type":"structure","value":"cell","field_class":"label"},
			{"type":"label","value":"_END"},
			{"type":"structure","value":"/cell"},
			{"type":"structure","value":"cell","field_class":"field"},
			{"type":"day","id":"end_day"},
			{"type":"string","value":"/"},
			{"type":"month","id":"end_month"},
			{"type":"string","value":"/"},
			{"type":"year","id":"end_year"},
			{"type":"string","value":"&nbsp;"},
			{"type":"hour","id":"end_hour"},
			{"type":"string","value":":"},
			{"type":"min","id":"end_min"},
			{"type":"string","value":":"},
			{"type":"sec","id":"end_sec"},
			{"type":"structure","value":"/cell"},
			{"type":"structure","value":"/row"},
			{"type":"structure","value":"row"},
			{"type":"structure","value":"cell","field_class":"label"},
			{"type":"label","value":"_CATEGORY"},
			{"type":"structure","value":"/cell"},
			{"type":"structure","value":"cell","field_class":"field"},	{"type":"select","id":"category","value":["_GENERIC","_VIDEOCONFERENCE","_MEETING","_CHAT","_PUBLISHING","_ASSESSMENT"],"key":["a","b","c","d","e","f"],"translatevalue":"1"},
			{"type":"structure","value":"/cell"},
			{"type":"structure","value":"/row"},
			{"type":"structure","value":"row"},
			{"type":"structure","value":"cell","field_class":"label"},
			{"type":"label","value":"_SUBJECT"},
			{"type":"structure","value":"/cell"},
			{"type":"structure","value":"cell","field_class":"field"},
			{"type":"text","id":"title","style":"width:300px"},
			{"type":"structure","value":"/cell"},
			{"type":"structure","value":"/row"},
			{"type":"structure","value":"row"},
			{"type":"structure","value":"cell","field_class":"label"},
			{"type":"label","value":"_DESCR"},
			{"type":"structure","value":"/cell"},
			{"type":"structure","value":"cell","field_class":"field"},
			{"type":"textarea","id":"event_description"},
			{"type":"structure","value":"/cell"},
			{"type":"structure","value":"/row"}
		]

		}';

		return $form_obj;
	}

	function store() {
	if ($this->getPerm()) {
		$start_date=$this->start_year."-".$this->start_month."-".$this->start_day." ".$this->start_hour.":".$this->start_min.":".$this->start_sec;

		$end_date=$this->end_year."-".$this->end_month."-".$this->end_day." ".$this->end_hour.":".$this->end_min.":".$this->end_sec;

		if (!$this->id) {
			$query="INSERT INTO ".$GLOBALS['prefix_fw']."_calendar SET create_date=NOW(),";
		} else {
			$query="UPDATE ".$GLOBALS['prefix_fw']."_calendar SET ";
		};

		$query.="class='".$this->calEventClass."',";
		$query.="start_date='".$start_date."',";
		$query.="end_date='".$end_date."',";
		$query.="title='".$this->title."',";
		$query.="description='".$this->description."',";
		$query.="category='".$this->category."',";
		$query.="type='".$this->type."',";
		$query.="private='".$this->private."',";
		$query.="visibility_rules='".$this->visibility_rules."',";
		$query.="_year='".$this->_year."',";
		$query.="_month='".$this->_month."',";
		$query.="_day='".$this->_day."',";
		$query.="_owner='".$this->_owner."'";

		if ($this->id) $query.=" WHERE id='".$this->id."'";

		$result=mysql_query($query);
		if (mysql_error()) die(mysql_error()."<br />".$query);

		if (!$this->id) {
			$this->id=mysql_insert_id();
			$query="INSERT INTO ".$GLOBALS['prefix_cms']."_calendar_item SET event_id='".$this->id."',calendar_id='".$this->cal_id."'";
			$result=mysql_query($query);
		};
		return $query;
		return $this->id;
	} else {
		return 'noperm';
		return '0';
	}
	}

	function del() {
		if ($this->getPerm()) {
			$query="DELETE FROM ".$GLOBALS['prefix_fw']."_calendar WHERE id='".$this->id."'";
			$result=mysql_query($query);

			$query="DELETE FROM ".$GLOBALS['prefix_cms']."_calendar_item WHERE event_id='".$this->id."'";
			$result=mysql_query($query);
		};
	}

	function getPerm() {
		$res =0;

		$role_id ="/cms/calendar/".$this->cal_id."/admin";
		$can_admin =$GLOBALS["current_user"]->matchUserRole($role_id);

		if ($can_admin) {
			$res =2;
		}
		else {
			$role_id ="/cms/calendar/".$this->cal_id."/edit";
			$can_edit =$GLOBALS["current_user"]->matchUserRole($role_id);

			if ($can_edit) {
				$res =1;
			}
		}

		return $res;
	}
}
?>