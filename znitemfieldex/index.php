<?php

include('../../../config.php');
if (!$member->isLoggedIn()) doError("You\'re not logged in.");
include($DIR_LIBS . 'PLUGINADMIN.php');
include($DIR_LIBS . 'MEDIA.php'); // media classes

class znItemFieldEX_ADMIN {
	/**
	 * 
	 */
	function znItemFieldEX_ADMIN() {
		$this->plugAdmin       = new PluginAdmin('znItemFieldEX');
		$this->plug            = & $this->plugAdmin->plugin;
		$this->plugname        = $this->plug->getName();
		$this->url             = $this->plug->getAdminURL();
		$this->table_tables    = sql_table('plug_znitemfieldex_tables');
		$this->table_fields    = sql_table('plug_znitemfieldex_fields');
		$this->table_table     = sql_table('plug_znitemfieldex_table_');
		$this->table_sql_cache = sql_table('plug_znitemfieldex_sql_cache');
		$this->formRefreshFlag = false;
	}
	/**
	 * 
	 */
	function start($mode, $focus, $msg){
		switch ($mode){
			case 't_overview'://
			case 'tedit':     //
				$modeColor = "_b";
				break;
			case 'f_overview'://
			case 'fedit':     //
				$modeColor = "_r";
				break;
			case 'r_overview'://
			case 'redit':     //
			case 'rsearch':   //
				$modeColor = "_y";
				break;
			case 'verinfo':   //
				$modeColor = "_g";
				break;
		}
		$this->plugAdmin->start(
			'<style>
<!--
.putcode {
	margin: 5px 5px 5px 20px;
	padding: 10px;
	font-size: medium;
	background-color: #ddd;
	overflow: auto;
}
table.menu_bg {
	margin: 0px;
	padding: 0px;
	font-size: 12px;
	border: none;
	width: auto;
}
/**/
a.menu_title             {
	display: block;
	text-decoration: none;
	color: #000;
	margin-left: 3px;
	padding: 4px;
	background-color: #f5f5f5;
	border-top: 1px solid #999; border-right: 1px solid #999; border-left: 1px solid #999;
}
a:visited.menu_title     { text-decoration:none; color: #000; }
a:link.menu_title        { text-decoration:none; color: #000; }
a:hover.menu_title       {
	text-decoration: none;
	color: #000;
	margin-left: 3px;
	padding: 4px;
	background-color: #ddf;
	border-top: 1px solid #0af; border-right: 1px solid #0af; border-left: 1px solid #0af;
}
/**/
a.menu_title_act         {
	display: block;
	text-decoration: none;
	color: #000;
	margin-left: 3px;
	padding: 4px;
	background-color: #bbc;
	border-top: 1px solid #999; border-right: 1px solid #999; border-left: 1px solid #999;
}
a:visited.menu_title_act { text-decoration:none; color: #000; }
a:link.menu_title_act    { text-decoration:none; color: #000; }
a:hover.menu_title_act   {
	text-decoration: none;
	color: #000;
	margin-left: 3px;
	padding: 4px;
	background-color: #ddf;
	border-top: 1px solid #0af; border-right: 1px solid #0af; border-left: 1px solid #0af;
}
/**/
div.znifemenu {
	margin-bottom: 0px;
	margin-top: 10px;
	padding-left: 10px;
	padding-bottom: 0px;
	border-bottom: 1px solid #000;
}
h6 {
	font-size: 16px;
	margin: 0;
	padding: 0;
	border: 0px solid #000;
	color: #000;
	font-weight: normal;
	line-height: 100%; /* IE'._ZNIFEX30.' */
}
.t01 {
	margin: 15px 0px;
	padding: 0px;
	background: url(' . $this->url . 't01' . $modeColor . '.gif) repeat-x;
	font-size: 0px;
}
.t02 {
	margin: 0px;
	padding: 0px;
	background: url(' . $this->url . 't02' . $modeColor . '.gif) left no-repeat;
	font-size: 0px;
}
.t03 {
	margin: 0px;
	padding: 6px 10px 4px 30px;
	background: url(' . $this->url . 't03' . $modeColor . '.gif) right no-repeat;
	height: 18px;
}
-->
</style>'
		);
		echo '<h2>'.$this->plugname.'</h2>';
		$act           = $this->url.'index.php?action=';
		if ( requestVar('tname') and requestVar('tablenavi')!="no" ){
			$row        =  $this->plug->getTableDataFromTableName(requestVar('tname'));
			$temp_table = '<img src="'.$this->url.'arrow.gif" align="top" /><img src="'.$this->url.'sele.gif"  align="top" />'.
				'<a style="color: #00f;" href="'.$act.'f_overview&amp;tname='.requestVar('tname').'&amp;tid='.$row["tid"].'">'.requestVar('tname').' ('.$row["tdesc"].') </a>';
		} else $temp_table = "";
		$menut         = array_fill(0, 9, 'menu_title');
		$menut[$focus] = 'menu_title_act';
		$tNavi         = '<p style="margin-bottom: 10px;"><img src="'.$this->url.'tables.gif" align="top" />';
		$tNavi        .= '<a style="color: #00f;" href="'.$this->url.'?action=t_overview">'._ZNIFEX31.'</a>'.$temp_table.'</p>';
		$temp_h        = '<div class="znifemenu"><table class="menu_bg" cellspacing="0"><tr>';
		$temp_f        = '</tr></table></div>';
		switch ($mode){
			case 't_overview'://
			case 'tedit':     //
			case 'verinfo':   //
				echo $tNavi;
				echo $temp_h;
				$this->menu($this->url.'stru.gif', ''._ZNIFEX32.''                , $menut[0], '#00f', $act.'t_overview');
				$this->menu($this->url.'info.gif', ''._ZNIFEX33.''      , $menut[3], '#00f', $act.'verinfo');
				echo $temp_f;
				break;
			case 'f_overview'://
			case 'fedit':     //
			case 'r_overview'://
			case 'redit':     //
			case 'rsearch':   //
				echo $tNavi;
				echo $temp_h;
				$para     = '&amp;tname='.requestVar('tname').'&amp;tid='.requestVar('tid');
				$this->menu($this->url.'stru.gif', ''._ZNIFEX32.''                , $menut[0], '#00f', $act.'f_overview' .$para);
				$this->menu($this->url.'sele.gif', ''._ZNIFEX34.''                , $menut[1], '#00f', $act.'r_overview' .$para);
				$this->menu($this->url.'sear.gif', ''._ZNIFEX35.''                , $menut[2], '#00f', $act.'rsearch'    .$para);
				$this->menu($this->url.'inse.gif', ''._ZNIFEX36.''                , $menut[3], '#00f', $act.'r_overview' .$para);
				$this->menu($this->url.'empt.gif', ''._ZNIFEX37.''            , $menut[5], '#f00', $act.'rdelete_all'.$para);
				$this->menu($this->url.'drop.gif', ''._ZNIFEX38.''                , $menut[6], '#f00', $act.'tdelete'    .$para);
				$this->menu($this->url.'info.gif', ''._ZNIFEX33.''      , $menut[8], '#00f', $act.'verinfo');
				echo $temp_f;
				break;
		}
		if ($msg) echo "<p>"._MESSAGE.": ".$msg."</p>";
	}
	/**
	 * 
	 */
	function menu($img, $title, $class_name, $col, $href){
		echo '<td style="padding: 0px; border: 0px;">';
		echo '<a class="'.$class_name.'" href="'.$href.'" title="'.$title.'" style="color: '.$col.'">';
		echo '<img src="'.$img.'" align="top" alt="'.$title.'" />'.$title.'</a>';
		echo '</td>';
	}
	/**
	 * 
	 */
	function action_t_overview_bnc(){
		$qid = mysql_query("SELECT * FROM ".$this->table_tables." WHERE ttype=0");
		if ($qid){
			while($row = mysql_fetch_array($qid)){
				//id
				$blogname = getBlogNameFromID( substr($row['tname'], 6) );
				if ($blogname != $row['tdesc']){
					//
					mysql_query("UPDATE ".$this->table_tables." SET tdesc='".$blogname."' WHERE tid=".$row['tid']);
				}
			}
		}
		$this->action_t_overview();
	}
	/**
	 * 
	 */
	function action_t_overview($msg = ''){
		global $CONF, $manager;
		$this->start('t_overview', 0, $msg);
?>
		<div class="t01"><div class="t02"><div class="t03">
		<h6><?php echo _ZNIFEX31; ?></h6>
		</div></div></div>
		<table>
			<thead><tr><th><?php echo _ZNIFEX39; ?></th><th><?php echo _ZNIFEX40; ?></th><th><?php echo _ZNIFEX41; ?></th><th colspan="7"><?php echo _ZNIFEX42; ?></th><th><?php echo _ZNIFEX43; ?></th><th><?php echo _ZNIFEX44; ?></th></tr></thead>
			<tbody>
<?php
		$ttype = array(""._ZNIFEX45."", ""._ZNIFEX46."");
		$qid = mysql_query("SELECT * FROM ".$this->table_tables);
		if ($qid){
			while($row = mysql_fetch_array($qid)){
				$cf = $this->countField($row['tid']);
				$cr = $this->countRecord($row['tname']);
				echo '<tr onmouseover="focusRow(this);" onmouseout="blurRow(this);">';
				echo '<td>'.$row['tname'].'</td><td>'.$row['tdesc'].'</td><td>'.$ttype[$row['ttype']].'</td>';
				$valid_f = ($row['ttype'] == 1) ? true : false;
				$para = '&amp;tname='.$row['tname'].'&amp;tid='.$row['tid'];
				$this->table_act(""._ZNIFEX47.""    , "edit", $valid_f, 'tedit'      .$para);
				$this->table_act(""._ZNIFEX32.""    , "stru", true    , 'f_overview' .$para);
				$this->table_act(""._ZNIFEX34.""    , "sele", true    , 'r_overview' .$para);
				$this->table_act(""._ZNIFEX35.""    , "sear", true    , 'rsearch'    .$para);
				$this->table_act(""._ZNIFEX36.""    , "inse", $valid_f, 'r_overview' .$para.'#radd');
				$this->table_act(""._ZNIFEX37."", "empt", true    , 'rdelete_all'.$para);
				$this->table_act(""._ZNIFEX38.""    , "dele", $valid_f, 'tdelete'    .$para);
				echo '<td>'.($cf + 1).'</td>';
				echo '<td>'.$cr.'</td>';
				echo '</tr>';
			}
		}
?>
			</tbody>
		</table>
		<div class="t01"><div class="t02"><div class="t03">
		<h6><?php echo _ZNIFEX48; ?></h6>
		</div></div></div>
		<form method="post" action="<?php echo $this->url ?>index.php">
			<div>
				<?php $manager->addTicketHidden(); ?>
				<input type="hidden" name="action"    value="tnew" />
				<input type="hidden" name="tablenavi" value="no"   />
				<table>
					<tr>
						<td><?php echo _ZNIFEX39; ?></td>
						<td>
							<input name="tname"  tabindex="10010" size="30" maxlength="200" 
								value="<?php echo ($this->formRefreshFlag) ? "" : requestVar('tname'); ?>" />
							<span style="color: #090;">(<?php echo _ZNIFEX49; ?>_)</span>
						</td>
					</tr>
					<tr>
						<td><?php echo _ZNIFEX40; ?></td>
						<td>
							<input name="tdesc"  tabindex="10020" size="60" maxlength="255" 
								value="<?php echo ($this->formRefreshFlag) ? "" : requestVar('tdesc'); ?>" />
							<span style="color: #090;">(<?php echo _ZNIFEX50; ?>)</span>
						</td>
					</tr>
					<tr><td><?php echo _ZNIFEX51; ?></td><td><input type="submit" tabindex="10030" value="<?php echo _ZNIFEX36; ?>" onclick="return checkSubmit();" /></td></tr>
				</table>
			</div>
		</form>
<?php
		$this->formRefreshFlag = false;
		$this->plugAdmin->end();
	}
	/**
	 * (tid)
	 */
	function countField($tid){
		return quickQuery("SELECT COUNT(*) AS result FROM ".$this->table_fields." WHERE ftid=".$tid);
	}
	/**
	 * (tname)
	 */
	function countRecord($tname){
		return quickQuery("SELECT COUNT(*) AS result FROM ".$this->table_table.$tname);
	}
	/**
	 * 
	 */
	function table_act($tit, $img, $valid_f, $act){
		echo '<td width="16" style="text-align: center; width: 16px;">';
		if ($valid_f){
			echo '<a href="'.$this->url.'index.php?action='.$act.'"><img src="'.$this->url.$img.'.gif'.'" alt="'.$tit.'" title="'.$tit.'" /></a>';
		} else { echo '--'; }
		echo '</td>';
	}
	/**
	 * 
	 */
	function action_tnew() {
		//NG
		if ( $this->table_valid_check(requestVar('tname')) ){
			//table_tableidAUTO_INCREMENT
			mysql_query("CREATE TABLE IF NOT EXISTS ".$this->table_table.requestVar('tname')." ( 
				`id`  INT(11) NOT NULL AUTO_INCREMENT, 
				PRIMARY KEY (id)
				)");
			//table_tables
			$sql_str = "INSERT INTO ".$this->table_tables." SET ".
			"tname ='".requestVar('tname')                     ."', ".
			"tdesc ='".mysql_escape_string(requestVar('tdesc'))."', ".
			"ttype = 1";
			mysql_query($sql_str);
			$msg = '<b style="color: #090;">'._ZNIFEX52.'</b>';
			$this->formRefreshFlag = true;
		} else $msg = '<b style="color: #090;">'._ZNIFEX53.'</b>';
		$this->action_t_overview($msg);
	}
	/**
	 * 
	 */
	function action_tedit($msg = '') {
		global $manager;
		$qid = mysql_query("SELECT * FROM ".$this->table_tables." WHERE tid=".intRequestVar('tid'));
		if ($row = mysql_fetch_object($qid)){
			$this->start('tedit', 0, $msg);
?>
		<div class="t01"><div class="t02"><div class="t03">
		<h6><?php echo _ZNIFEX54; ?></h6>
		</div></div></div>
		<form method="post" action="<?php echo $this->url ?>index.php">
			<div>
				<?php $manager->addTicketHidden(); ?>
				<input type="hidden" name="action"    value="tupdate" />
				<input type="hidden" name="tid"       value="<?php echo intRequestVar('tid'); ?>" />
				<input type="hidden" name="tname_o"   value="<?php echo $row->tname;          ?>" />
				<input type="hidden" name="tablenavi" value="no" />
				<table>
					<tr>
						<td><?php echo _ZNIFEX39; ?></td>
						<td>
							<input name="tname"  tabindex="10010" size="30" maxlength="200" value="<?php echo $row->tname; ?>" />
							<span style="color: #090;">(<?php echo _ZNIFEX49; ?>_)</span>
						</td>
					</tr>
					<tr>
						<td><?php echo _ZNIFEX40; ?></td>
						<td>
							<input name="tdesc"  tabindex="10020" size="60" maxlength="255" value="<?php echo $row->tdesc; ?>" />
						</td>
					</tr>
					<tr><td><?php echo _ZNIFEX47; ?></td><td><input type="submit" tabindex="10030" value="<?php echo _ZNIFEX55; ?>" onclick="return checkSubmit();" /></td></tr>
				</table>
			</div>
		</form>
<?php
			$this->plugAdmin->end();
		} else {
			$this->error(""._ZNIFEX56."(T_T)");
		}
	}
	/**
	 * 
	 */
	function action_tupdate() {
		//NG
		if ( $this->table_valid_check(requestVar('tname'), intRequestVar('tid')) ){
			//table_table()
			$sql_str = "ALTER TABLE `".$this->table_table.requestVar('tname_o')."` RENAME `".$this->table_table.requestVar('tname')."`";
			if (requestVar('tname_o') != requestVar('tname')) mysql_query($sql_str);
			//table_tables
			$sql_str = "UPDATE ".$this->table_tables." SET ".
			"tname ='".requestVar('tname') ."', ".
			"tdesc ='".mysql_escape_string(requestVar('tdesc'))."' ".
			"WHERE tid=".intRequestVar('tid');
			mysql_query($sql_str);
			$msg = '<b style="color: #090;">'._ZNIFEX57.'</b>';
			$this->formRefreshFlag = true;
		} else $msg = '<b style="color: #090;">'._ZNIFEX53.'</b>';
		
		$this->action_t_overview($msg);
	}
	/**
	 * 
	 */
	function action_tdelete() {
		global $manager;
		$this->plugAdmin->start();
?>
		<h2><?php echo _DELETE_CONFIRM ?></h2>
		<form method="post" action="<?php echo $this->url ?>index.php">
			<div>
				<?php $manager->addTicketHidden(); ?>
				<input type="hidden" name="action"    value="tdeleteconfirm" />
				<input type="hidden" name="tid"       value="<?php echo intRequestVar('tid'); ?>" />
				<input type="hidden" name="tname"     value="<?php echo requestVar('tname');  ?>" />
				<input type="hidden" name="tablenavi" value="no"   />
				<input type="submit" tabindex="10"    value="<?php echo _DELETE_CONFIRM_BTN   ?>" />
				<?php echo _ZNIFEX58; ?>:<?php echo requestVar('tname'); ?>
			</div>
		</form>
<?php
		$this->plugAdmin->end();
	}
	/**
	 * 
	 */
	function action_tdeleteconfirm() {
		global $manager;
		mysql_query("DELETE FROM ".$this->table_tables." WHERE tid=".intRequestVar('tid'));  //table_tables
		mysql_query("DELETE FROM ".$this->table_fields." WHERE ftid=".intRequestVar('tid')); //table_fields
		mysql_query("DROP table IF EXISTS ".$this->table_table.requestVar('tname'));         //table_table
		$this->action_t_overview('<b style="color: #090;">'.requestVar('tname').''._ZNIFEX59.'</b>');
	}
	/**
	 * 
	 */
	//
	//
	//
	function table_valid_check($tname, $tid = 0){
		if (strlen($tname) == 0)                              return false; //
		$qid = mysql_query("SELECT * FROM ".$this->table_tables." WHERE tid<>".$tid." AND tname='".$tname."'");
		if ($qid) if (mysql_num_rows($qid) > 0)               return false; //
		if (preg_match("/^[a-z_]+[a-z0-9_]*$/", $tname) == 0) return false; //
		return true;
	}
	/**
	 * 
	 */
	function action_f_overview($msg = '') {
		global $CONF, $manager;
		$this->start('f_overview', 0, $msg);
?>
		<div class="t01"><div class="t02"><div class="t03">
		<h6><?php echo _ZNIFEX60; ?></h6>
		</div></div></div>
		<table>
			<thead>
				<tr>
					<th><?php echo _ZNIFEX61; ?></th>
					<th><?php echo _ZNIFEX62; ?></th>
					<th><?php echo _ZNIFEX63; ?></th>
					<th><?php echo _ZNIFEX41; ?></th>
					<th><?php echo _ZNIFEX64; ?></th>
					<th><?php echo _ZNIFEX65; ?></th>
					<th colspan='2' nowrap="nowrap"><?php echo _ZNIFEX42; ?></th>
					<th><?php echo _ZNIFEX66; ?>id</th>
				</tr>
			</thead>
			<tbody>
			<tr onmouseover="focusRow(this);" onmouseout="blurRow(this);">
				<td>id</td>
				<td>--</td>
				<td>--</td>
				<td>--</td>
				<td>--</td>
				<td>--</td>
				<td style="text-align: center">--</td>
				<td style="text-align: center">--</td>
				<td>--</td>
			</tr>
<?php
		$qid = mysql_query("SELECT * FROM ".$this->table_fields." WHERE ftid='".intRequestVar('tid')."' ORDER BY forder");
		if ($qid){
			while($row = mysql_fetch_array($qid)){
				$url_para = $this->url."index.php?";
				echo '<tr onmouseover="focusRow(this);" onmouseout="blurRow(this);">';
				echo '<td>'.$row['fname']   .'</td>';
				echo '<td>'.$row['forder']  .'</td>';
				echo '<td>'.$row["flabel"]  .'</td>';
				echo '<td>';
				echo $row['ftype'];
				if ($row['ftype'] == 'Text'){
					//Select
					$origin  = array();
					$sql_str = "SELECT * FROM ".$this->table_fields." WHERE fsetting='".$row['fid']."' AND ftype='Select'";
					$qid_sel = mysql_query($sql_str);
					if ($qid_sel){
						if (mysql_num_rows($qid_sel)) echo ' <img src="'.$this->url.'link_orig.gif" align="top" width="16" height="16" /> ';
						while ($row_sel = mysql_fetch_array($qid_sel)){
							$tname = $this->plug->getTableAboutFromID('tname', $row_sel["ftid"]);
							$origin[] = '<a href="'.$this->url.'?action=fedit'.
								'&amp;fid='  .$row_sel["fid"].
								'&amp;tname='.$tname.
								'&amp;tid='  .$row_sel["ftid"].
								'&amp;fname='.$row_sel["fname"].'" title="'.$tname.'::'._ZNIFEX67.''.$row_sel["fname"].''._ZNIFEX68.'">'.$tname.'::'.$row_sel['fname'].'</a>';
						}
						echo implode(", ", $origin);
					}
				}
				echo '</td>';
				echo '<td>'; //
//***** Type *****
				$fsetting = $row['fsetting'];
				switch ($row['ftype']){
					case "Text":
					case "Number":
						$fsetting = "width:".$fsetting."px";
						break;
					case "Textarea":
						$fsetting = "width:".preg_replace("/(\/)/", "px / rows:", $fsetting);
						break;
					case "Image":
						$fsetting = explode("/", $fsetting);
						$fsetting = "media directory:".$fsetting[0]." / ".( ($fsetting[1] == "Image") ? "IMAGE MODE" : "LIST MODE" )." / "._ZNIFEX69.":".$fsetting[2];
						break;
					case "DateTime":
						break;
					case "Radio":
					case "Checkbox":
						$fsetting = nl2br($fsetting);
						break;
					case "Select":
						if ($fsetting){ //
							echo '<img src="'.$this->url.'link.gif" align="top" width="16" height="16" /> ';
							$row_link = $this->plug->getFieldDataFromFieldId($fsetting);
							$tname    = $this->plug->getTableAboutFromID('tname', $row_link["ftid"]);
							$fsetting = '<a href="'.$this->url.'?action=fedit'.
								'&amp;fid='  .$row_link["fid"].
								'&amp;tname='.$tname.
								'&amp;tid='  .$row_link["ftid"].
								'&amp;fname='.$row_link["fname"].'" title="'.$row_link["fname"].''._ZNIFEX70.'">'.$row_link['fname'].'</a>';
						} else $fsetting = '<span style="color: #090;">'._ZNIFEX71.'</span>';
						break;
				}
				echo $fsetting;
				echo '</td>';
				echo '<td>';
				//It is not smart!!
				if (!$templatePathModelArray){
					//,
					$temp = substr(  $this->getTemplateParameter($row['fid'], $row['fname'], $row['ftid']) , 1); //,
					
					//100
					$tempsearch[]  = "/(\[!!\].*)/";
					$tempreplace[] = 'Error!!'._ZNIFEX72.''.$row['fname'];
					//
					$tempsearch[]  = "/(\[\*\*\].*)/";
					$tempreplace[] = 'Error!!'._ZNIFEX73.''.$row['fname'];
					$temp = preg_replace($tempsearch, $tempreplace, $temp);
					
					$templatePathArray = explode(",", $temp);
					foreach ($templatePathArray as $key => $value){        //
						$valueArray = explode("-&gt;", $value);              //
						$tempcount  = count($valueArray);                    //
						$endField   = $valueArray[$tempcount - 1];           //
						$endField   = preg_replace("/<.*?>/", "", $endField);//$endField
						if ($endField != "[".$row['fid']."]".$row['fname']){
							//"$endField->"
							foreach ($templatePathArray as $value2){
								$otherRoute = strstr($value2, $endField."-&gt;");
								if ($otherRoute) break;
							}
							$templatePathArray[$key] .= substr($otherRoute, strlen($endField));
						}
					}
					foreach ($templatePathArray as $key => $value){//
						$temp = preg_replace("/\[.*?\]/", "", $value);
						$templatePathModelArray[] = substr(  $temp, 0, (strlen($temp) - strlen($row['fname']))  );
					}
				}
				foreach ($templatePathModelArray as $key => $value) echo $templatePathModelArray[$key].$row['fname']."<br />";
				echo '</td>';
				$para = '&amp;tid='.$row['ftid'].'&amp;tname='.requestVar('tname').'&amp;fid='.$row['fid'].'&amp;fname='.$row['fname'];
				$this->table_act(""._ZNIFEX47.""    , "edit", true, 'fedit'  .$para);
				$this->table_act(""._ZNIFEX38.""    , "dele", true, 'fdelete'.$para);
				echo '<td>'.$row['fid'].'</td>';
				echo '</tr>';
			}
		}
?>
			</tbody>
		</table>
		<div class="t01"><div class="t02"><div class="t03">
		<h6><?php echo _ZNIFEX74; ?></h6>
		</div></div></div>
		<form method="post" action="<?php echo $this->url ?>index.php">
			<div>
				<?php $manager->addTicketHidden(); ?>
				<input type="hidden" name="action" value="fnew" />
				<input type="hidden" name="tid"    value="<?php echo intRequestVar('tid'); ?>" />
				<input type="hidden" name="tname"  value="<?php echo requestVar('tname');  ?>" />
				<table>
					<tr>
						<td><?php echo _ZNIFEX61; ?></td>
						<td>
							<input name="fname"  tabindex="10010" size="30" maxlength="200" 
								value="<?php echo ($this->formRefreshFlag) ? "" : requestVar('fname'); ?>" />
							<span style="color: #090;">(<?php echo _ZNIFEX49; ?>_)</span>
						</td>
					</tr>
					<tr>
						<td><?php echo _ZNIFEX62; ?></td>
						<td>
							<input name="forder" tabindex="10020" size="5"  maxlength="200" 
								value="<?php echo ($this->formRefreshFlag) ? "" : intRequestVar('forder'); ?>" />
							<span style="color: #090;">(<?php echo _ZNIFEX75; ?>)</span>
						</td>
					</tr>
					<tr>
						<td><?php echo _ZNIFEX63; ?></td>
						<td>
							<input name="flabel" tabindex="10030" size="50" maxlength="200" 
								value="<?php echo ($this->formRefreshFlag) ? "" : requestVar('flabel'); ?>" />
							<span style="color: #090;">(<?php echo _ZNIFEX50; ?>)</span>
						</td>
					</tr>
					<tr>
						<td><?php echo _ZNIFEX41; ?></td>
						<td>
<?php
//***** Type *****
?>
							<select name="ftype" tabindex="10040">
								<option value="Text"     <?php echo ($this->formRefreshFlag) ? "" : ( (requestVar('ftype') == "Text")     ? "selected" : "" ); ?>>Text</option>
								<option value="Number"   <?php echo ($this->formRefreshFlag) ? "" : ( (requestVar('ftype') == "Number")   ? "selected" : "" ); ?>>Number</option>
								<option value="Textarea" <?php echo ($this->formRefreshFlag) ? "" : ( (requestVar('ftype') == "Textarea") ? "selected" : "" ); ?>>Textarea</option>
								<option value="Image"    <?php echo ($this->formRefreshFlag) ? "" : ( (requestVar('ftype') == "Image")    ? "selected" : "" ); ?>>Image</option>
								<option value="DateTime" <?php echo ($this->formRefreshFlag) ? "" : ( (requestVar('ftype') == "DateTime") ? "selected" : "" ); ?>>DateTime</option>
								<option value="Checkbox" <?php echo ($this->formRefreshFlag) ? "" : ( (requestVar('ftype') == "Checkbox") ? "selected" : "" ); ?>>Checkbox</option>
								<option value="Radio" <?php echo ($this->formRefreshFlag) ? "" : ( (requestVar('ftype') == "Radio") ? "selected" : "" ); ?>>Radio</option>
								<option value="Select"   <?php echo ($this->formRefreshFlag) ? "" : ( (requestVar('ftype') == "Select")   ? "selected" : "" ); ?>>Select</option>
							</select>
							<span style="color: #090;"><?php echo _ZNIFEX76; ?></span>
						</td>
					</tr>
					<tr><td><?php echo _ZNIFEX51; ?></td><td><input type="submit" tabindex="10050" value="<?php echo _ZNIFEX36; ?>" onclick="return checkSubmit();" /></td></tr>
				</table>
			</div>
		</form>
<?php
		$this->formRefreshFlag = false;
		$this->plugAdmin->end();
	}
	/**
	 * 
	 */
	//fidfsettingSelect
	function getTemplateParameter($fid, $fname, $ftid, $loop = 0, $tableArray = array()){
		$ret = "";
		//=====
		$loop++;
		if ($loop > 100) return ",[!!]".$fname;                                                //To ensure the loop evasion.//
		if (  in_array($ftid, $tableArray)  ) return ",[**]".$fname;      //
		$tableArray[] = $ftid;		
		if ($this->plug->getTableAboutFromID('ttype', $ftid) == 0) return ","."[$fid]".$fname; //fname
		//ftidText
		$qid_text = mysql_query("SELECT * FROM ".$this->table_fields." WHERE ftype='Text' AND ftid='".$ftid."'");
		if ($qid_text){
			while($row_text = mysql_fetch_array($qid_text)){//Text
				//Select
				$qid_select = mysql_query("SELECT * FROM ".$this->table_fields." WHERE ftype='Select' AND fsetting='".$row_text["fid"]."'");
				if ($qid_select){
					while ($row_select = mysql_fetch_array($qid_select)){//
						$ret = $this->getTemplateParameter($row_select["fid"], $row_select["fname"], $row_select["ftid"], $loop, $tableArray)."-&gt;"."[$fid]".$fname . $ret;
					}
				}
			}
		}
		return (strlen($ret)) ? $ret : ',<img src="'.$this->url.'link_dele.gif" align="top" />'."[$fid]".$fname;
	}
	/**
	 * 
	 */
	function action_fnew() {
		global $member; //
		//NG
		if ( $this->field_valid_check(intRequestVar('tid'), requestVar('fname'), requestVar('flabel')) ){
			//table_table
			$sql_str = "ALTER TABLE ".$this->table_table.requestVar('tname')." ADD `f__".requestVar('fname')."` ";
//***** Type *****
			switch (requestVar('ftype')){
				case "Text":
					$sql_str .= "VARCHAR(255) NOT NULL";
					$fsetting = "300";
					break;
				case "Number":
					$sql_str .= "INT(11)      NOT NULL DEFAULT '0'";
					$fsetting = "300";
					break;
				case "Textarea":
					$sql_str .= "TEXT         NOT NULL";
					$fsetting = "300/8";
					break;
				case "Image":
					$sql_str .= "VARCHAR(255) NOT NULL";
					$fsetting = $member->getID()."/Image";
					break;
				case "DateTime":
					$sql_str .= "DATETIME     NOT NULL";
					$fsetting = "Y-m-d";
					break;
				case "Radio":
				case "Checkbox":
					$sql_str .= "TEXT         NOT NULL";
					$fsetting = "";
					break;
				case "Select":
					$sql_str .= "INT(11)      NOT NULL DEFAULT '0'";
					$fsetting = "";
					break;
				default:
					$sql_str = "";
			}
			sql_query($sql_str);
			//table_fields
			$sql_str = "INSERT INTO ".$this->table_fields." SET ".
			"ftid    = ".intRequestVar('tid')                     ." , ".
			"fname   ='".mysql_escape_string(requestVar('fname') )."', ".
			"forder  = ".intRequestVar('forder')                  ." , ".
			"flabel  ='".mysql_escape_string(requestVar('flabel'))."', ".
			"ftype   ='".requestVar('ftype')                      ."', ".
			"fsetting='".mysql_escape_string($fsetting)           ."'  ";
			mysql_query($sql_str);
			$this->deleteRelationSql();
			$msg = '<b style="color: #090;">'._ZNIFEX77.'</b>';
			$this->formRefreshFlag = true;
		} else $msg = '<b style="color: #090;">'._ZNIFEX78.'(['._ZNIFEX79.']['._ZNIFEX80.']['._ZNIFEX81.']'._ZNIFEX82.')</b>';
		$this->action_f_overview($msg);
	}
	/**
	 * 
	 */
	function action_fedit($msg = '') {
		global $DIR_MEDIA;
		global $manager;
		$qid = mysql_query("SELECT * FROM ".$this->table_fields." WHERE fid=".intRequestVar('fid'));
		if ($row = mysql_fetch_object($qid)){
			$this->start('fedit', 0, $msg);
?>
			<div class="t01"><div class="t02"><div class="t03">
			<h6><?php echo _ZNIFEX83; ?></h6>
			</div></div></div>
			<form method="post" action="<?php echo $this->url ?>index.php" name="fedit">
				<div>
					<?php $manager->addTicketHidden(); ?>
					<input type="hidden" name="action"  value="fupdate" />
					<input type="hidden" name="tid"     value="<?php echo intRequestVar('tid'); ?>" />
					<input type="hidden" name="tname"   value="<?php echo  requestVar('tname'); ?>" />
					<input type="hidden" name="fid"     value="<?php echo intRequestVar('fid'); ?>" />
					<input type="hidden" name="fname_o" value="<?php echo          $row->fname; ?>" />
					<table>
						<tr>
							<td><?php echo _ZNIFEX61; ?></td>
							<td>
								<input name="fname"  tabindex="10010" size="30" maxlength="200" value="<?php echo $row->fname; ?>" />
								<span style="color: #090;">(<?php echo _ZNIFEX49; ?>_)</span>
							</td>
						</tr>
						<tr>
							<td><?php echo _ZNIFEX62; ?></td>
							<td>
								<input name="forder" tabindex="10020" size="5"  maxlength="200" value="<?php echo $row->forder; ?>" />
								<span style="color: #090;">(<?php echo _ZNIFEX75; ?>)</span>
							</td>
						</tr>
						<tr>
							<td><?php echo _ZNIFEX63; ?></td>
							<td>
								<input name="flabel" tabindex="10030" size="50" maxlength="200" value="<?php echo $row->flabel; ?>" />
								<span style="color: #090;">(<?php echo _ZNIFEX50; ?>)</span>
							</td>
						</tr>
						<tr>
							<td><?php echo _ZNIFEX41; ?></td>
							<td>
<?php
//***** Type *****
?>
								<input type="hidden" name="ftype" value="<?php echo $row->ftype; ?>" />
								<?php echo $row->ftype; ?>
								<span style="color: #090;">(<?php echo _ZNIFEX84; ?>)</span>
							</td>
						</tr>
						<tr>
							<td><?php echo _ZNIFEX64; ?></td>
							<td>
<?php
			//
			$fsetting = explode("/", $row->fsetting);
//***** Type *****
			switch ($row->ftype){
				case "Text":     //width
					echo 'width:<input type="text" name="fsetting_w" size="6" value="'.$row->fsetting.'" />px';
					break;
				case "Number":   //width
					echo 'width:<input type="text" name="fsetting_w" size="6" value="'.$row->fsetting.'" />px';
					break;
				case "Textarea": //width/rows
					echo 'width:<input type="text" name="fsetting_w" size="6" value="'.$fsetting[0]  .'" />px ';
					echo  'rows:<input type="text" name="fsetting_h" size="6" value="'.$fsetting[1]  .'" />';
					break;
				case "Image":    //path
					echo ''._ZNIFEX85.':<select name="fsetting_p">';
					$currentCollection = $fsetting[0];
					// get collection list
					$collections = MEDIA::getCollectionList();
					foreach ($collections as $dirname => $description) {
						echo '<option value="'.htmlspecialchars($dirname).'"';
						if ($dirname == $currentCollection) echo ' selected=" selected"';
						echo '>'.htmlspecialchars($description).'</option>';
					}
					echo '</select><br />';
					echo 'UI:<input type="radio" name="fsetting_i" value="Image" id="IMAGE" '.(($fsetting[1]=="Image") ? 'checked="checked"':'').' /><label for="IMAGE">IMAGE MODE</label>';
					echo '   <input type="radio" name="fsetting_i" value="List"  id="LIST"  '.(($fsetting[1]=="List" ) ? 'checked="checked"':'').' /><label for="LIST" >LIST MODE</label><br />';
					echo ''._ZNIFEX69.':<input type="text" name="fsetting_n" size="6" value="'.$fsetting[2].'" />';
					break;
				case "DateTime": //
					echo '<select name="fsetting_d">';
					echo '<option value="Y-m-d"       '.(($row->fsetting=="Y-m-d"      ) ? 'selected="selected"':'').'>2005-12-24</option>';
					echo '<option value="Y-m-d H:i:s" '.(($row->fsetting=="Y-m-d H:i:s") ? 'selected="selected"':'').'>2005-12-24 23:59:59</option>';
					echo '</select>';
					break;
				case "Radio": //
				case "Checkbox": //
					echo '<textarea name="fsetting_c" rows="8">'.htmlspecialchars($row->fsetting).'</textarea>';
					break;
				case "Select":   //[Text]
					//[table][field]
					$sql_str = "SELECT f.fid, f.fname, t.tname FROM ".$this->table_fields." AS f, ".$this->table_tables." AS t ".
					"WHERE t.tid=f.ftid AND f.ftype='Text' AND f.ftid<>".intRequestVar('tid')." AND t.ttype<>0";
					$qid_sel = mysql_query($sql_str);
					echo '<select name="fsetting_s">';
					if ($qid_sel){
						while($row_sel = mysql_fetch_array($qid_sel)){
							echo '<option value="'.$row_sel["fid"].'" '.(($row->fsetting == $row_sel["fid"]) ? "selected" : "").'>';
							echo '['.$row_sel["tname"].']'._ZNIFEX67.'['.$row_sel["fname"].']'._ZNIFEX66.'</option>';
						}
					}
					echo '</select> ';
					echo '<span style="font-size: small; color: #090;"><br />';
					echo ''._ZNIFEX86.'(Text)'._ZNIFEX66.'<br />('._ZNIFEX87.'[id]'._ZNIFEX88.')</span>';
					break;
				case 'Category':
					echo ''._ZNIFEX20.'id'._ZNIFEX21.'';
					break;
			}
?>
							</td>
						</tr>
						<tr><td><?php echo _ZNIFEX47; ?></td><td><input type="submit" tabindex="10040" value="<?php echo _ZNIFEX55; ?>" onclick="return checkSubmit();" /></td></tr>
					</table>
				</div>
			</form>
<?php
			$this->plugAdmin->end();
		} else {
			$this->error(""._ZNIFEX89."(T_T)");
		}
	}
	/**
	 * 
	 */
	function action_fupdate() {
		//NG
		if ( $this->field_valid_check(intRequestVar('tid'), requestVar('fname'), requestVar('flabel'), intRequestVar('fid')) ){
			//table_item()
			$sql_str  = "ALTER TABLE ".$this->table_table.requestVar('tname')." CHANGE f__".requestVar('fname_o')." f__".requestVar('fname')." ";
//***** Type *****
			$ftypeArray = array(
				"Text"     => "VARCHAR(255) NOT NULL",
				"Number"   => "INT(11)      NOT NULL",
				"Textarea" => "TEXT         NOT NULL",
				"Image"    => "VARCHAR(255) NOT NULL",
				"DateTime" => "DATETIME     NOT NULL",
				"Checkbox" => "TEXT         NOT NULL",
				"Radio" => "TEXT         NOT NULL",
				"Select"   => "INT(11)      NOT NULL",
			);
			$fsettingArray = array(
				"Text"     => requestVar('fsetting_w'),
				"Number"   => requestVar('fsetting_w'),
				"Textarea" => requestVar('fsetting_w') . "/" . requestVar('fsetting_h'),
				"Image"    => requestVar('fsetting_p') . "/" . requestVar('fsetting_i') . "/" . requestVar('fsetting_n'),
				"DateTime" => requestVar('fsetting_d'),
				"Checkbox" => requestVar('fsetting_c'),
				"Radio"    => requestVar('fsetting_c'),
				"Select"   => requestVar('fsetting_s'),
			);
			if (requestVar('fname_o') != requestVar('fname')) mysql_query($sql_str.$ftypeArray[requestVar('ftype')]);
			//table_fields
			$sql_str = "UPDATE ".$this->table_fields." SET ".
			"fname   ='".mysql_escape_string(requestVar('fname'))                ."', ".
			"forder  = ".intRequestVar('forder')                                 ." , ".
			"flabel  ='".mysql_escape_string(requestVar('flabel'))               ."', ".
			"ftype   ='".requestVar('ftype')                                     ."', ".
			"fsetting='".mysql_escape_string($fsettingArray[requestVar('ftype')])."'  ".
			"WHERE fid=".intRequestVar('fid');
			mysql_query($sql_str);
			$this->deleteRelationSql();
			$msg = '<b style="color: #090;">'._ZNIFEX57.'</b>';
			$this->formRefreshFlag = true;
		} else $msg = '<b style="color: #090;">'._ZNIFEX90.'(['._ZNIFEX79.']['._ZNIFEX80.']['._ZNIFEX81.']'._ZNIFEX82.')</b>';
		
		$this->action_f_overview($msg);
	}
	/**
	 * 
	 */
	function action_fdelete() {
		global $manager;
		$this->plugAdmin->start();
?>
		<h2><?php echo _DELETE_CONFIRM?></h2>
		<form method="post" action="<?php echo $this->url ?>index.php">
			<div>
				<?php $manager->addTicketHidden(); ?>
				<input type="hidden" name="action" value="fdeleteconfirm" />
				<input type="hidden" name="tid"    value="<?php echo intRequestVar('tid'); ?>" />
				<input type="hidden" name="tname"  value="<?php echo requestVar('tname');  ?>" />
				<input type="hidden" name="fid"    value="<?php echo intRequestVar('fid'); ?>" />
				<input type="hidden" name="fname"  value="<?php echo requestVar('fname');  ?>" />
				<input type="submit" tabindex="10" value="<?php echo _DELETE_CONFIRM_BTN   ?>" />
				<?php echo requestVar('fname'); ?>
			</div>
		</form>
<?php
		$this->plugAdmin->end();
	}
	/**
	 * 
	 */
	function action_fdeleteconfirm() {
		global $manager;
		//table_fields
		mysql_query("DELETE FROM ".$this->table_fields." WHERE fid=".intRequestVar('fid'));
		//table_table
		mysql_query("ALTER TABLE `".$this->table_table.requestVar('tname')."` DROP f__".requestVar('fname'));
		$this->action_f_overview('<b style="color: #090;">'._ZNIFEX91.'</b>');
	}
	/**
	 * 
	 */
	//
	//
	//
	function field_valid_check($tid, $fname, $flabel, $fid = 0){
		$qid = mysql_query("SELECT * FROM ".$this->table_fields." WHERE ftid=".$tid." AND fid<>".$fid." AND (fname='".$fname."' OR flabel='".$flabel."')");
		if ($fname == "id")                                   return false; //"id"
		if (strlen($fname) == 0 or strlen($flabel) == 0)      return false; //
		if ($qid) if (mysql_num_rows($qid) > 0)               return false; //
		if (preg_match("/^[a-z_]+[a-z0-9_]*$/", $fname) == 0) return false; //
		return true;
	}
	/**
	 * SQL
	 */
	//
	//))
	function deleteRelationSql(){
		mysql_query("DELETE FROM ".$this->table_sql_cache." WHERE 1");
	}
	/**
	 * ()
	 */
	function action_r_overview($msg = ''){
		global $CONF, $manager;
		$this->start('r_overview', 1, $msg);
?>
		<div class="t01"><div class="t02"><div class="t03">
		<h6><?php echo _ZNIFEX92; ?></h6>
		</div></div></div>
<?php
		//SQL
		$fieldsArray = array('t0.id AS f0' => 't0.id AS f0'); //
		$joinsArray  = array();
		$rowTable    = $this->plug->getTableDataFromTableName(requestVar('tname')); //
		$fieldName   = ""; //
		if ($rowTable["ttype"] == 0){
			//
			$fieldName .= '<th>item id</th><th>'._ZNIFEX93.'</th>';
			$fieldsArray["i.ititle AS itit"] = "i.ititle AS itit";
			$joinsArray[]                    = " LEFT JOIN ".sql_table('item')." AS i ON t0.id=i.inumber "; //ON t0.idid
		} else $fieldName .= '<th>relation id</th>';
		$asName      = 1;
		$sql_str     = "SELECT * FROM ".$this->table_fields." WHERE ftid='".intRequestVar('tid')."' ORDER BY forder";
		$qid_field   = mysql_query($sql_str);
		while ($row_field = mysql_fetch_array($qid_field)){ //
			if ($row_field["ftype"] == "Select" and $row_field["fsetting"]){
				//Select
				$row_set = $this->plug->getFieldDataFromFieldId($row_field["fsetting"]); //
				$tname   = $this->plug->getTableAboutFromID('tname', $row_set["ftid"]);  //
				if ($row_set){
					//SelectSQL
					$fieldsArray["z".$asName] = "t".$asName.".f__".$row_set["fname"]." AS f".$asName;    //
					$joinsArray[]             = " LEFT JOIN ".$this->table_table.$tname." AS t".$asName. //
					                            " ON t0.f__".$row_field["fname"]."=t".$asName.".id ";
					//
					$fediturl = $this->url.'?action=fedit'.
					            '&amp;fid='   .$row_set["fid"].
					            '&amp;tname=' .$tname.
					            '&amp;tid='   .$row_set["ftid"].
					            '&amp;fname=' .$row_set["fname"];
					$imgtitle = $tname.".".$row_set["fname"].''._ZNIFEX94.'';
					$img = '<a href="'.$fediturl.'"><img src="'.$this->url.'link.gif" width="16" height="16" align="top" title="'.$imgtitle.'" /></a>';
				} else {
					//Select
					$fieldsArray["t0.f__".$row_field["fname"]." AS f".$asName] = "t0.f__".$row_field["fname"]." AS f".$asName;
					$img = '<img src="'.$this->url.'link_dele.gif" width="16" height="16" align="top" /> <span style="color: #f00;">'._ZNIFEX95.'</span>';
				}
			} else {
				//SelectSelect
				$fieldsArray["t0.f__".$row_field["fname"]." AS f".$asName] = "t0.f__".$row_field["fname"]." AS f".$asName;
				$img = '';
			}
			$fieldName .= '<th>'.$row_field["fname"].'<br />('.$row_field["flabel"].')'.$img.'</th>';
			$asName++;
		}
		$sql_str  = " SELECT ".implode(", ", $fieldsArray);
		$sql_str .= " FROM " .$this->table_table.requestVar('tname')." AS t0 ".implode(" ", $joinsArray);
		//
		if (requestVar('query')){
			foreach ($fieldsArray as $key => $value){
				if ($value != "t0.id AS f0"){                           //id
					$tempt      = substr($value, 0, strpos($value, ".")); //  
					$tempf      = explode( " ", strstr($value, ".") );    //
					$tgtField[] = "zzz.".$tempt.$tempf[0];
				}
			}
			$tgtFieldWhere = implode(",", $tgtField);
			$searchclass   = new SEARCH( (requestVar('query')) );
			$where         = $searchclass->boolean_sql_where($tgtFieldWhere);
			$where         = strtr($where, array('i.zzz.'=> ''));
		}
		$sql_str .= ($where) ? " WHERE ".$where : " WHERE 1"; //
		
		//
		$actionUrl   = $this->url.'index.php?action=r_overview&amp;tname='.requestVar('tname');
		$actionUrl  .= '&amp;tid='.intRequestVar('tid').'&amp;query='.requestVar('query').'&amp;p=';
		$qid_record  = mysql_query($sql_str);
		$recordRows  = ($qid_record) ? mysql_num_rows($qid_record) : 0; //
		$pageRows    = (int) $this->plug->getOption('pagerows');        //30
		$currentPage = (!intRequestVar('p')) ? 1 : intRequestVar('p');  //
		$totalPeges  = ceil($recordRows / $pageRows);                   //
		$prevPage    = ($currentPage > 1) ? $currentPage - 1 : 0;
		$nextPage    = $currentPage + 1;
		$pageSwitch  = '<div style="margin: 0; padding: 0px; text-align: right;">';
		$pageSwitch .= ($prevPage) ? '<a href="'.$actionUrl.$prevPage.'">&laquo;Prev</a> |' : '&laquo;Prev |';
		for ($i = 1; $i <= $totalPeges; $i++){
			if ($i == $currentPage){
				$pageSwitch .= ' <strong style="font-size: 130%">'.$currentPage."</strong> |";
			} elseif ($totalPeges < 10 || $i < 4 || $i > ($totalPeges - 3)){
				$pageSwitch .= ' <a href="'.$actionUrl. $i.'">'.$i.'</a> |';
			} else {
				if ($i < ($currentPage - 1) || $i > ($currentPage + 1)){
					if (($i == 4 && ($currentPage > 5 || $currentPage == 1)) || $i == ($currentPage + 2)){
						$pageSwitch .= '...|';
					}
				} else $pageSwitch .= ' <a href="'.$actionUrl. $i.'">'.$i.'</a> |';
			}
		}
		$pageSwitch .= ($totalPeges >= $nextPage) ? ' <a href="'.$actionUrl.$nextPage.'" title="Next">Next&raquo;</a>' : ' Next&raquo;';
		$pageSwitch .= '</div>';
		echo (requestVar('query')) ? '<span style="float: left;">'._ZNIFEX96.''.requestVar('query').' ('.$recordRows.''._ZNIFEX97.')</span>' : '';
		echo $pageSwitch;
		$sql_str .= " ORDER BY t0.id DESC LIMIT ".($currentPage - 1) * $pageRows.", ".$pageRows; //
?>
		<table>
			<thead><tr><?php echo $fieldName; ?><th colspan='2' nowrap="nowrap"><?php echo _ZNIFEX42; ?></th></tr></thead>
			<tbody>
<?php
		$qid_record = mysql_query($sql_str);//
		if ($qid_record){
			while ($row_record = mysql_fetch_array($qid_record)){
				echo '<tr onmouseover="focusRow(this);" onmouseout="blurRow(this);">';
				foreach ($fieldsArray as $key => $value){ //
					//
					$temp    = explode("AS ", ($key == $value) ? $key : $value); //
					$img     = ($key == $value) ? '' : '<img src="'.$this->url.'link.gif" width="16" height="16" align="top" />';
					$bp      = &$manager->getBlog($CONF['DefaultBlog']); //convertBreaks
					$tempdat = $row_record[$temp[1]];
					$id      = ($temp[1] == "f0") ? $tempdat : $id;
					if ($temp[1] == "itit"){
						//
						$tempdat = '<a href="index.php?action=itemedit&amp;itemid='.$id.'" title="'._ZNIFEX98.''.stripslashes($tempdat).'">'.
						           mb_substr($tempdat, 0, 15, _CHARSET) . ((mb_strlen($tempdat, _CHARSET) > 15 ) ? "..." : "").'</a>';
					}
					$tempdat = ($bp->convertBreaks()) ? removeBreaks($tempdat) : $tempdat;
					//<br />
					echo '<td>'.$img.' '.stripslashes(  nl2br($tempdat)  ).'</td>';
				}
				$para = '&amp;tname='.requestVar('tname').'&amp;tid='.intRequestVar('tid').'&amp;id='.$row_record['f0'];
				$this->table_act(""._ZNIFEX47."", "edit", true, 'redit'  .$para);
				$this->table_act(""._ZNIFEX38."", "dele", true, 'rdelete'.$para);
				echo '</tr>';
			}
		}
?>
			</tbody>
		</table>
<?php
		echo $pageSwitch;
?>
		<a name="radd"></a>
		<div class="t01"><div class="t02"><div class="t03">
		<h6><?php echo _ZNIFEX99; ?></h6>
		</div></div></div>
<?php
		if ($rowTable["ttype"] != 0){
			if ($this->countField( intRequestVar('tid') )){
?>
			<form method="post" action="<?php echo $this->url ?>index.php">
				<div>
					<?php $manager->addTicketHidden(); ?>
					<input type="hidden" name="action" value="rnew" />
					<input type="hidden" name="tid"    value="<?php echo intRequestVar('tid'); ?>" />
					<input type="hidden" name="tname"  value="<?php echo requestVar('tname');  ?>" />
					<table>
<?php $this->plug->EXTableForm(requestVar('tname'), 0, FALSE); ?>
						<tr><td><?php echo _ZNIFEX51; ?></td><td><input type="submit" tabindex="10090" value="<?php echo _ZNIFEX36; ?>" onclick="return checkSubmit();" /></td></tr>
					</table>
				</div>
			</form>
<?php
			} else echo '<p style="color: #090">'._ZNIFEX100.'</p>';
		} else echo '<p style="color: #090">'._ZNIFEX101.'</p>';
		
		$this->plugAdmin->end();
	}
	/**
	 * ()
	 */
	function action_rnew() {
		$this->plug->itemdataAdd(requestVar('tname'), 0); //0id
		$this->action_r_overview($msg);
	}
	/**
	 * ()
	 */
	function action_redit($msg = '') {
		global $manager;
		$this->start('redit', 1, $msg);
?>
		<div class="t01"><div class="t02"><div class="t03">
		<h6><?php echo _ZNIFEX102; ?></h6>
		</div></div></div>
		<form method="post" action="<?php echo $this->url ?>index.php">
			<div>
				<?php $manager->addTicketHidden(); ?>
				<input type="hidden" name="action" value="rupdate" />
				<input type="hidden" name="tid"    value="<?php echo intRequestVar('tid'); ?>" />
				<input type="hidden" name="tname"  value="<?php echo requestVar('tname');  ?>" />
				<input type="hidden" name="id"     value="<?php echo intRequestVar('id');  ?>" />
				<table>
<?php $this->plug->EXTableForm(requestVar('tname'), intRequestVar('id'), FALSE); ?>
					<tr><td><?php echo _ZNIFEX47; ?></td><td><input type="submit" tabindex="10090" value="<?php echo _ZNIFEX55; ?>" onclick="return checkSubmit();" /></td></tr>
				</table>
			</div>
		</form>
<?php
		$this->plugAdmin->end();
	}
	/**
	 * 
	 */
	function action_rupdate() {
		$this->plug->itemdataUpd(requestVar('tname'), intRequestVar('id'));
		$this->action_r_overview($msg);
	}
	/**
	 * 
	 */
	function action_rdelete() {
		global $manager;
		$this->plugAdmin->start();
?>
		<h2><?php echo _DELETE_CONFIRM?></h2>
		<form method="post" action="<?php echo $this->url ?>index.php">
			<div>
				<?php $manager->addTicketHidden(); ?>
				<input type="hidden" name="action" value="rdeleteconfirm" />
				<input type="hidden" name="tid"    value="<?php echo intRequestVar('tid'); ?>" />
				<input type="hidden" name="tname"  value="<?php echo requestVar('tname');  ?>" />
				<input type="hidden" name="id"     value="<?php echo intRequestVar('id');  ?>" />
				<input type="submit" tabindex="10" value="<?php echo _DELETE_CONFIRM_BTN   ?>" />
				<?php echo _ZNIFEX103; ?>id:<?php echo intRequestVar('id'); ?>
			</div>
		</form>
<?php
		$this->plugAdmin->end();
	}
	/**
	 * 
	 */
	function action_rdeleteconfirm() {
		global $manager;
		//table_table
		mysql_query("DELETE FROM ".$this->table_table.requestVar('tname')." WHERE id=".intRequestVar('id'));
		$this->action_r_overview('<b style="color: #090;">'._ZNIFEX104.'</b>');
	}
	/**
	 * 
	 */
	function action_rdelete_all() {
		global $manager;
		$this->plugAdmin->start();
?>
		<h2><?php echo _DELETE_CONFIRM?></h2>
		<form method="post" action="<?php echo $this->url ?>index.php">
			<div>
				<?php $manager->addTicketHidden(); ?>
				<input type="hidden" name="action" value="rdeleteconfirm_all" />
				<input type="hidden" name="tid"    value="<?php echo intRequestVar('tid'); ?>" />
				<input type="hidden" name="tname"  value="<?php echo requestVar('tname');  ?>" />
				<input type="submit" tabindex="10" value="<?php echo _DELETE_CONFIRM_BTN   ?>" />
				<?php echo requestVar('tname'); ?> <?php echo _ZNIFEX105; ?>
			</div>
		</form>
<?php
		$this->plugAdmin->end();
	}
	/**
	 * 
	 */
	function action_rdeleteconfirm_all(){
		global $manager;
		//table_table
		mysql_query("DELETE FROM ".$this->table_table.requestVar('tname')." WHERE 1");
		$this->action_r_overview('<b style="color: #090;">'._ZNIFEX106.'</b>');
	}
	/**
	 * 
	 */
	function action_rsearch($msg = '') {
		global $manager;
		$this->start('rsearch', 2, $msg);
?>
		<div class="t01"><div class="t02"><div class="t03">
		<h6><?php echo _ZNIFEX107; ?></h6>
		</div></div></div>
		<form method="post" action="<?php echo $this->url ?>index.php">
			<div>
				<?php $manager->addTicketHidden(); ?>
				<input type="hidden" name="action" value="r_overview" />
				<input type="hidden" name="tid"    value="<?php echo intRequestVar('tid'); ?>" />
				<input type="hidden" name="tname"  value="<?php echo requestVar('tname');  ?>" />
				<table>
					<tr>
						<td><?php echo _ZNIFEX108; ?></td>
						<td><input type="text" name="query" tabindex="10080" size="50" /><span style="color: #090;">(AND ,OR<?php echo _ZNIFEX109; ?>)</span></td>
					</tr>
					<tr><td><?php echo _ZNIFEX35; ?></td><td><input type="submit" tabindex="10090" value="<?php echo _ZNIFEX35; ?>" onclick="return checkSubmit();" /></td></tr>
				</table>
			</div>
		</form>
<?php
		$this->plugAdmin->end();
	}
	/**
	 * 
	 */
	function action_verinfo($msg = '') {
		$this->start('verinfo', 3, $msg);
		$divStyle = "
			font-family: 'Trebuchet MS', 'Bitstream Vera Sans', verdana, lucida, arial, helvetica, sans-serif; 
			padding: 10px; 
			background: url(".$this->url."verinfo_bc.gif) repeat-x; 
			border-top: 3px solid #fff; 
			border-left: 3px solid #fff; 
			border-bottom: 3px solid #666; 
			border-right: 3px solid #666; 
			text-align: right; ";
?>
		<div style="margin: 20px; width: 380px; border: 2px solid #bbb;">
			<div style="<?php echo $divStyle; ?>">
				<img src="<?php echo $this->url.$this->plugname; ?>shade.gif" align="left" />
				<div style="height: 40px;">Version Check [XML-RPC]</div>
				<div style="margin: 7px 0;font-size: 30px; color: #47d; font-weight: bold;">NP_<?php echo $this->plugname; ?></div>
<?php
	$plugver = $this->plug->getVersion();
	echo "--- ver$plugver ---<br /><br />";
	$result = $this->plug->verCheck();
	if ($plugver == $result['version']){
		$msg = '<span style="color: #00f;">'._ZNIFEX110.'</span>';
	} else {
		$msg = '<span style="color: #f90;">'._ZNIFEX111.'</span><br />'.
		       '<span style="color: #f90; font-size: 18px;">ver'.$result['version'].'</span><br />'.
		       '<a href="http://wa.otesei.com/NP_'.$this->plugname.'" target="_blank">'._ZNIFEX112.'</a>';
	}
	echo $msg;
?>
				<br /><br />
				<a href="http://wa.otesei.com/">http://wa.otesei.com/</a>
			</div>
		</div>
<?php
		$this->plugAdmin->end();
	}
	
	/**
	 * 
	 */
	function disallow(){
		global $HTTP_SERVER_VARS;
		ACTIONLOG::add(WARNING, _ACTIONLOG_DISALLOWED . $HTTP_SERVER_VARS['REQUEST_URI']);
		$this->error(_ERROR_DISALLOWED);
	}
	/**
	 * 
	 */
	function action($action) {
		global $manager, $member;
		$member->isAdmin() or $this->disallow();
		$methodName         = 'action_' . $action;
		$aActionsNotToCheck = array( //
			't_overview', 
			'f_overview', 
			'r_overview', 
			't_overview_bnc', 
			'tedit', 
			'fedit', 
			'redit', 
			'fdelete', 
			'tdelete', 
			'rdelete', 
			'rdelete_all', 
			'rsearch', 
			'verinfo', 
		);
		if (!in_array(strtolower($action), $aActionsNotToCheck)) if (!$manager->checkTicket()) $this->error(_ERROR_BADTICKET);
		if (method_exists($this, $methodName)) call_user_func(array(&$this, $methodName)); else $this->error(_BADACTION . " ($action)");
	}
	/**
	 * 
	 */
	function error($msg) {
		$this->plugAdmin->start();
		echo "<h2>Error!</h2>".$msg."<br />";
		echo "<a href='".$this->url."index.php' onclick='history.back()'>"._BACK."</a>";
		$this->plugAdmin->end();
		exit;
	}
}

$myAdmin = new znItemFieldEX_ADMIN();
$myAdmin->action( (requestVar('action')) ? requestVar('action') : 't_overview_bnc' ); //overview

?>