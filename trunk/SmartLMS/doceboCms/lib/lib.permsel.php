<?php



class GroupPermSel {

	var $repoDb=NULL;
	var $arr_token=FALSE;

	//class constructor
	function GroupPermSel() {
		$this->getRepoDb();
	}


	function getRepoDb() {
		require_once($GLOBALS['where_framework'].'/modules/org_chart/tree.org_chart.php');

		$this->repoDb=new TreeDb_OrgDb($GLOBALS['prefix_fw'].'_org_chart_tree');

	}


	/**
	 *
	 * @return array with the anonymous and registered (hidden) groups
	 */
	function getRegAnonGroups() {

		$acl=new DoceboACL();
		$acl_manager=$acl->getACLManager();


		$anon_st=$acl->getUserST("/Anonymous");
		$reg_st=$acl->getGroupST("/oc_0");

		$res=array();
		$res[$anon_st]=def("_ANONYMOUS_USERS", "standard", "cms");
		$res[$reg_st]=def("_REGISTERED_USERS", "standard", "cms");

		return $res;

	}


	function getRegAnonGroups_old() {
		include_once($GLOBALS['where_framework']."/lib/lib.acl.php");

		$acl=new DoceboACL();
		$acl_manager=$acl->getACLManager();

		$anon_st=$acl->getGroupST("/cms/anonymous");
		if ($anon_st === FALSE) {
			$anon_st=$acl_manager->registerGroup("/cms/anonymous", "Anonymous users", true);
		}

		$reg_st=$acl->getGroupST("/cms/registered");
		if ($reg_st === FALSE) {
			$reg_st=$acl_manager->registerGroup("/cms/registered", "Registered users", true);
		}

		$res=array();
		$res[$anon_st]=def("_ANONYMOUS_USERS", "standard", "cms");
		$res[$reg_st]=def("_REGISTERED_USERS", "standard", "cms");

		return $res;
	}


	/**
	 *
	 * @return array of folder items with all the groups folders of the tree
	 */
	function getOrgchartGroupList() {

		$res=array();
		$folder=$this->repoDb->getRootFolder();


		$tree=$this->repoDb->getDescendantsId($folder->id);

		if ($tree !== FALSE) {

			$fc=$this->repoDb->getFoldersCollection($tree);

			while($fold=$fc->getNext()) {
				$res[$fold->id]=$fold->otherValues[ORGDB_POS_TRANSLATION];
			}
		}

		return $res;
	}


	/**
	 *
	 * @return array of strings with the path of each element retourned by the getOrgchartGroupList method
	 */
	function getOrgchartAsLevels() {
		include_once($GLOBALS['where_framework']."/lib/lib.acl.php");

		$acl=new DoceboACL();

		$res=$this->getRegAnonGroups();
		$arr=$this->getOrgchartGroupList();

		foreach($arr as $key=>$val) {
			$group_st=$acl->getGroupST("oc_".$key);
			if ($group_st !== FALSE)
				$res[$group_st]=$val;
		}

		return $res;
	}


	/**
	 *
	 * @return array of groups with key=idst and value=groupid without the starting "/"
	 */
	function getGroupsAsLevels() {
		include_once($GLOBALS['where_framework']."/lib/lib.aclmanager.php");

		$res=$this->getRegAnonGroups();

		$data=new GroupDataRetriever($GLOBALS['dbConn'], $GLOBALS['prefix_fw']);
		$data->addPlatformFilter(array("cms"));
		$q=$data->getRows(0, 100000000000); // <- .. :-(
		if ($q) {
			while($row=mysql_fetch_array($q)) {
				$res[$row["idst"]]=substr($row["groupid"], 1);
			}
		}

		return $res;
	}


	function getAllToken() {

		if ($this->arr_token === FALSE) {
			$this->arr_token=array(
				'view' => array( 	'code' => 'view',
				'name' => '_VIEW',
				'image' => 'standard/view.gif')
			);
		}

		return $this->arr_token;
	}

	function getLevels() {

		if ($GLOBALS["cms"]["grpsel_type"] == "group") {
			$levels = $this->getGroupsAsLevels();
		}
		else if ($GLOBALS["cms"]["grpsel_type"] == "orgchart") {
			$levels = $this->getOrgchartAsLevels();
		}

		return $levels;
	}

	function getPermissionUi( $form_name, $perm ) {

		require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');

		$lang =& DoceboLanguage::createInstance('standard', "cms");
		$lang_perm =& DoceboLanguage::createInstance('permission', "cms");

		$tokens = $this->getAllToken();


		$levels=$this->getLevels();


		$tb = new TypeOne(0, $lang->def('_TITLE_PERMISSION'), $lang->def('_SUMMARY_PERMISSION'));

		$c_head = array($lang->def('_GROUPS'));
		$t_head = array('');
		foreach($tokens as $k => $token) {
			$c_head[] =  '<img src="'.getPathImage().$token['image'].'" alt="'.$lang_perm->def($token['name']).'"'
						.' title="'.$lang_perm->def($token['name']).'" />';
			$t_head[] = 'image';
		}
		if(count($tokens) > 1) {
			$c_head[] = '<img src="'.getPathImage().'standard/checkall.gif" alt="'.$lang->def('_CHECKALL').'" />';
			$c_head[] = '<img src="'.getPathImage().'standard/uncheckall.gif" alt="'.$lang->def('_UNCHECKALL').'" />';
			$t_head[] = 'image';
			$t_head[] = 'image';
		}
		$tb->setColsStyle($t_head);
		$tb->addHead($c_head);
		loadJsLibraries();
		while(list($lv, $levelname) = each($levels)) {

			$c_body = array($levelname);

			foreach($tokens as $k => $token) {
				$c_body[] =  '<input class="check" type="checkbox" '
							.'id="perm_'.$lv.'_'.$token['code'].'" '
							.'name="perm['.$lv.']['.$token['code'].']" value="1"'
							.( isset($perm[$lv][$token['code']]) ? ' checked="checked"' : '' ).' />'
						.'<label class="access-only" for="perm_'.$lv.'_'.$token['code'].'">'
						.$lang_perm->def($token['name']).'</label>'."\n";
			}
			if(count($tokens) > 1) {

				$c_body[] = '<img class="handover"'
					.' onclick="checkall(\''.$form_name.'\', \'perm['.$lv.']\', true); return false;"'
					.' src="'.getPathImage().'standard/checkall.gif" alt="'.$lang->def('_CHECKALL').'" />';
				$c_body[] = '<img class="handover"'
					.' onclick="checkall(\''.$form_name.'\', \'perm['.$lv.']\', false); return false;"'
					.' src="'.getPathImage().'standard/uncheckall.gif" alt="'.$lang->def('_UNCHECKALL').'" />';
			}
			$tb->addBody($c_body);
		}
		$c_select_all = array('');
		foreach($tokens as $k => $token) {

			$c_select_all[] = '<img class="handover"'
					.' onclick="checkall_fromback(\''.$form_name.'\', \'['.$token['code'].']\', true); return false;"'
					.' src="'.getPathImage().'standard/checkall.gif" alt="'.$lang->def('_CHECKALL').'" />'
				.'<img class="handover"'
					.' onclick="checkall_fromback(\''.$form_name.'\', \'['.$token['code'].']\', false); return false;"'
					.' src="'.getPathImage().'standard/uncheckall.gif" alt="'.$lang->def('_UNCHECKALL').'" />';
		}
		if(count($tokens) > 1) {
			$c_select_all[] = '';
			$c_select_all[] = '';
		}
		$tb->addBody($c_select_all);
		return $tb->getTable();
	}

	function getSelectedPermission($perm_arr=FALSE) {

		if (($perm_arr === FALSE) || (!is_array($perm_arr)))
			$perm_arr=$_POST['perm'];

		$tokens 	= $this->getAllToken();
		$levels = $this->getLevels();
		$perm 		= array();

		while(list($lv, $levelname) = each($levels)) {
			$perm[$lv] = array();
			foreach($tokens as $k => $token) {

				if(isset($perm_arr[$lv][$token['code']])) {
					$perm[$lv][$token['code']] = 1;
				}
			}
		}
		return $perm;
	}


}




?>
