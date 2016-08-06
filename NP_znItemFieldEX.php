<?php
class NP_znItemFieldEX extends NucleusPlugin
{
    function getName()              { return 'znItemFieldEX'; }
    function getAuthor()            { return '佐藤（な）'; }
    function getURL()               { return 'http://wa.otesei.com/NP_znItemFieldEX'; }
    function getVersion()           { return '0.14'; }
    function getDescription()       { return 'ブログごとに設定したフィールドを、アイテムに追加するプラグイン'; }
    function supportsFeature($w)    { return in_array ($w, array ('HelpPage','SqlTablePrefix', 'SqlApi')); }
    function getMinNucleusVersion() { return '350';}
    function hasAdminArea()         { return 1; }
    function getTableList()
    {
        $tableList   = array();
        $tableList[] = $this->table_tables;
        $tableList[] = $this->table_fields;
        $tableList[] = $this->table_sql_cache;
        $qid         = sql_query("SELECT tname FROM ".$this->table_tables);
        while ($row = sql_fetch_object($qid)) $tableList[] = $this->table_table . $row->tname;
        return $tableList;
    }
    function getEventList()
    {
        return array(
            'PreItem',
            'QuickMenu',
            'PostAddItem',
            'PreUpdateItem',
            'AddItemFormExtras',
            'EditItemFormExtras',
            'PreDeleteItem',
            'PostAddBlog',
            'PreDeleteBlog',
            'PreSearchResults', 
            'PostPluginOptionsUpdate', //vc
        );
    }
    function init()
    {
        // include language file for this plugin
        $language = str_replace( array('\\','/'), '', getLanguageName());
        $incFile  = (is_file($this->getDirectory().$language.'.php')) ? $language : 'english';
        include_once($this->getDirectory().$incFile.'.php');
        $this->language = $incFile;
        //
        
        $this->table_tables    = sql_table('plug_znitemfieldex_tables');
        $this->table_fields    = sql_table('plug_znitemfieldex_fields');
        $this->table_table     = sql_table('plug_znitemfieldex_table_');
        $this->table_sql_cache = sql_table('plug_znitemfieldex_sql_cache');
        $this->highlight       = $this->getOption('searchtemplate');
        $this->nucleusTemplate = ($this->getOption('nucleusTemplate') == 'yes') ? TRUE : FALSE;
    }
    function install()
    {
        $this->createOption("searchtemplate" , "ハイライトテンプレート", "text", "<span class='highlight'>".'\0</span>');
        //$this->createOption("nucleusTemplate", "拡張フィールドのテンプレート内で、Nucleus標準のテンプレート変数を使用しますか？", "yesno", "no");
        $this->createOption("pagerows"       , "管理ページのレコード一覧で１ページに表示するレコード数", "text", "30");
        $this->createOption("quickmenu"      , "管理ページへのリンクを、クイックメニューに表示しますか？", "yesno", "yes");
        $this->createOption("flg_table_drop" , "アンインストール時、データを破棄しますか？", "yesno", "no");
        $this->createOption('verCheck'       , '最新バージョンの確認をしますか？', 'yesno', 'no'); //version check //vc
        //ブログoption
        $this->createBlogOption('searchField', '検索対象となるフィールド', "textarea", "");
        sql_query("CREATE TABLE IF NOT EXISTS ".$this->table_tables.
            " ( 
            `tid`      INT(11)      NOT NULL AUTO_INCREMENT, 
            `tname`    VARCHAR(255) NOT NULL, 
            `tdesc`    VARCHAR(255) NOT NULL, 
            `ttype`    TINYINT(4)   NOT NULL DEFAULT '0', 
            PRIMARY KEY (tid), 
            KEY tname (tname)
            )");
        sql_query("CREATE TABLE IF NOT EXISTS ".$this->table_fields.
            " ( 
            `fid`      INT(11)      NOT NULL AUTO_INCREMENT, 
            `ftid`     INT(11)      NOT NULL DEFAULT '0', 
            `fname`    VARCHAR(255) NOT NULL, 
            `forder`   INT(11)      NOT NULL DEFAULT '50', 
            `flabel`   VARCHAR(255) NOT NULL, 
            `ftype`    VARCHAR(255) NOT NULL, 
            `fsetting` VARCHAR(255) NOT NULL, 
            PRIMARY KEY (fid), 
            KEY fname (fname)
            )");
        sql_query("CREATE TABLE IF NOT EXISTS ".$this->table_sql_cache.
            " ( 
            `sid`       INT(11)      NOT NULL AUTO_INCREMENT, 
            `sbid`      INT(11)      NOT NULL DEFAULT '0', 
            `spath`     VARCHAR(255) NOT NULL, 
            `ssql`      TEXT         NOT NULL, 
            `sfname`    VARCHAR(255) NOT NULL, 
            `sftype`    VARCHAR(255) NOT NULL, 
            `sfsetting` VARCHAR(255) NOT NULL, 
            PRIMARY KEY (sid), 
            KEY spath (spath)
            )");
        //すでにあるブログの数だけテーブルを作成
        $qid = sql_query("SELECT bnumber FROM ".sql_table('blog'));
        while ($row = sql_fetch_object($qid)) $this->createItemTable($row->bnumber);
        
        //テーブル構造変更
        $this->tableUpgrade();
        
        global $manager; //vc
        $manager->subscriptions['AdminPrePageFoot'][] = postVar('filename'); //vc
    }
    function tableUpgrade()
    {
        sql_query("ALTER TABLE ".$this->table_tables   ." ALTER ttype SET DEFAULT '0'");
        sql_query("ALTER TABLE ".$this->table_fields   ." ALTER ftid  SET DEFAULT '0'");
        sql_query("ALTER TABLE ".$this->table_sql_cache." ALTER sbid  SET DEFAULT '0'");
        
        //ブログ用idカラム
        $result = sql_query("SELECT bnumber FROM ".sql_table('blog'));
        while ($blog = sql_fetch_object($result)) sql_query("ALTER TABLE ".$this->table_table."item_b".$blog->bnumber." ALTER id SET DEFAULT '0'");
        
        //ブログ用テーブルとリレーション用テーブル内の、Number, Selectタイプ拡張フィールド
        $qid = sql_query("SELECT ftid, fname FROM ".$this->table_fields." WHERE ftype='Number' OR ftype='Select'");
        while ($field = sql_fetch_object($qid))
        {
            //このフィールドのテーブル
            $tgtTableName = $this->table_table.quickQuery("SELECT tname AS result FROM ".$this->table_tables." WHERE tid=".intval($field->ftid));
            sql_query("ALTER TABLE ".$tgtTableName." ALTER f__".$field->fname." SET DEFAULT '0'");
        }
    }
    function uninstall()
    {
        if ($this->getOption('flg_table_drop') == 'yes')
        {
            //テーブルを削除
            $qid = sql_query("SELECT tname FROM ".$this->table_tables);
            while ($row = sql_fetch_object($qid)) $this->delete_znItemFieldEX_Table($row->tname);
            sql_query("DROP table IF EXISTS ". $this->table_tables);
            sql_query("DROP table IF EXISTS ". $this->table_fields);
            sql_query("DROP table IF EXISTS ". $this->table_sql_cache);
        }
    }
    function event_PostPluginOptionsUpdate($data)
    {
        global $manager; //vc
        if ($data['context'] != 'global' || $data['plugid'] != $this->GetID() || $this->getOption('verCheck') != "yes") return; //vc
        $this->setOption('verCheck', 'no'); //vc
        $this->plugName = quickQuery('SELECT pfile result FROM '.sql_table('plugin').' WHERE pid='.intval($this->getID())); //vc
        $manager->subscriptions['AdminPrePageFoot'][] = $this->plugName; //vc
    }
    function event_AdminPrePageFoot()
    { //vc
        $this->plugName = (postVar('filename')) ? postVar('filename') : $this->plugName; //vc
        $result         = $this->verCheck(); //vc
        echo '<span style="color: #'.(($result['version'] == $this->getVersion()) ? '00f' : 'f00').'">'.htmlspecialchars($result['message'], ENT_QUOTES,_CHARSET).'</span>'; //vc
    }
    function event_QuickMenu($data)
    {
        global $member;
        if ($this->getOption('quickmenu') != 'yes')         return; // only show when option enabled
        if (!($member->isLoggedIn() && $member->isAdmin())) return; // only show to admins
        array_push($data['options'], array('title' => 'フィールド拡張', 'url' => $this->getAdminURL(),'tooltip' => 'フィールド設定画面へ'));
    }
    /**
     * ブログを追加・削除
     */
    function event_PostAddBlog($data)
    {
        $blogid = $data['blog']->getID();
        $this->createItemTable($blogid);
    }
    function event_PreDeleteBlog($data)
    {
        $blogid = $data['blogid'];
        $this->delete_znItemFieldEX_Table("item_b".$blogid);
    }
    /**
     * 指定blogidでアイテムテーブル作成
     */
    function createItemTable($blogid)
    {
        //アイテム拡張用テーブルのidは、AUTO_INCREMENTでない。
        sql_query("CREATE TABLE IF NOT EXISTS ".$this->table_table."item_b".$blogid." ( 
            `id`  INT(11) NOT NULL DEFAULT '0', 
            PRIMARY KEY (id)
            )");
        //再インストール時に、前のデータが残っている場合は、初期レコードを追加しない。
        $sql_str = "SELECT tname FROM ".$this->table_tables." WHERE tname='item_b".$blogid."'";
        $qid = sql_query($sql_str);
        if (!($qid and @sql_num_rows($qid) > 0))
        {
            $sql_str = "INSERT INTO ".$this->table_tables." SET ".
            "tname ='item_b".$blogid."', ".
            "tdesc ='".getBlogNameFromID($blogid)."', ".
            "ttype = 0";
            sql_query($sql_str);
        }
    }
    /**
     * 指定テーブル削除
     */
    function delete_znItemFieldEX_Table($tname)
    {
        sql_query("DROP table IF EXISTS ".$this->table_table.$tname);
        sql_query("DELETE FROM ".$this->table_tables." WHERE tname='".$tname."'");
    }
    /**
     * アドミン追加・変更フォーム
     */
    function event_AddItemFormExtras($data) { $this->add_edit_Form($data); }
    function event_EditItemFormExtras($data){ $this->add_edit_Form($data, true); }
    function add_edit_Form($data, $edit_flag = false)
    {
        $itemid = ($edit_flag) ? $data['variables']['itemid'] : 0;
        $this->EXTableForm("item_b".$data['blog']->blogid, $itemid, TRUE);
    }
    /**
     * 拡張テーブルへの追加・変更フォーム
     */
    function EXTableForm($tname, $itemid, $itemFlag)
    {
        $this->printImgJs();
        echo '<table>';
        echo '<tr><th colspan="2">znItemFieldEX</th></tr>';
        //拡張テーブルのデータ取得（何らかのトラブルで存在しない場合の処理も必要★★★
        $sql_str = "SELECT * FROM ".$this->table_table.$tname." WHERE id=".$itemid;
        $qid = sql_query($sql_str);
        if ($qid and @sql_num_rows($qid) > 0) $row_item = sql_fetch_array($qid);
        
        $ftid = $this->getIDFromTableName($tname);
        $sql_str = "SELECT * FROM ".$this->table_fields." WHERE ftid='".$ftid."' ORDER BY forder";
        $qid = sql_query($sql_str);
        $tabindex = 10000;
        while ($row = sql_fetch_array($qid))
        {
            echo '<tr><td align="top">'.$row["flabel"].'</td>';
            echo '<td align="top">';
            $this->EXFieldForm($row, $row_item, $tabindex);
            echo '</td></tr>';
            $tabindex++;
        }
        //アイテムデータ無効
        if ($itemFlag)
        {
            echo '<tr><td colspan="2">';
            $this->EXFieldPresenceForm($tname, $itemid);
            echo '</td></tr>';
        }
        echo '</table>';
    }
    /**
     * イメージタイプ用JavaScript出力
     */
    function printImgJs()
    {
?>
<script language="JavaScript" type="text/javascript">
    <!--
    function tgtImage     (fname, img_url)  { document.getElementById("znItemFieldEX_img" + fname).value = img_url; }
    function tgtCollection(fname, crntClctn){ document.getElementById("znItemFieldEX_col" + fname).value = crntClctn; }
    function dispImg(imgObj, colElement, imgElement)
    {
        imgObj.src = colElement.value + imgElement.value;
        if (colElement.value == "") imgObj.style.visibility='hidden'; else imgObj.style.visibility='visible';
    }
    //-->
</script>
<?php
    }
    /**
     * 指定拡張テーブルの、指定itemidに、レコードが存在するか ＆ レコードコントロールフォーム
     */
    function EXFieldPresenceForm($tname, $itemid)
    {
        $qid    = sql_query("SELECT id FROM ".$this->table_table.$tname." WHERE id=".$itemid);
        $flag   = ($qid and @sql_num_rows($qid) > 0) ? TRUE : FALSE;
        $msgAct = ($flag) ? '削除する' : '追加しない';
        $msgEnt = ($flag) ? '存在しています。' : '存在していません。';
        echo '<span style="color: orange; font-weight: bold;">このアイテムには、znItemFieldEXデータが、'.$msgEnt.'</span><br />';
        echo '<input type="checkbox" name="annul_itemdata" id="annul_itemdata" value="no" />';
        echo '<label for="annul_itemdata" style="font-weight: bold;">znItemFieldEXデータを'.$msgAct.'</label>';
    }
    
    // extra javascript for input and textarea fields
    function parse_jsinput($which)
    {
        global $CONF;
        
        $ret = 'name="'.$which.'" id="input'.$which.'"';
        if ($CONF['DisableJsTools'] != 1)
        {
            $ret .= 'onkeyup="storeCaret(this); updPreview(\''.$which.'\'); doMonitor();" onclick="storeCaret(this);" onselect="storeCaret(this);"';
        }
        else if ($CONF['DisableJsTools'] == 0)
        {
            $ret .= 'onkeyup="doMonitor();" onkeypress="shortCuts();"';
        }
        else
        {
            $ret .= 'onkeyup="doMonitor();"';
        }
        return $ret;
    }
    /**
     * 拡張フィールド単位フォーム
     */
    function EXFieldForm(
        $row,      //フィールドデータ
        $row_item, //レコード
        $tabindex  //タブインデックス
    )
    {
        global $manager, $CONF;
        switch ($row["ftype"])
        {
            case "Text":     //width
            case "Number":   //width
                echo '<input tabindex="'.$tabindex.'" type="text" name="f__'.$row["fname"].'" style="width: '.$row["fsetting"].
            'px;" maxlength="255" value="'.htmlspecialchars($row_item["f__".$row["fname"]], ENT_QUOTES, _CHARSET).'" /><br />';
                break;
            case "Textarea": //width/rows
                $fsetting = explode("/", $row["fsetting"]);
                $js = $this->parse_jsinput('f__'.$row["fname"]);
                echo '<textarea tabindex="'.$tabindex.'" '.$js.' style="width: '.$fsetting[0].'px;" rows="'.$fsetting[1].'" >';
                //アイテム拡張の場合、ブログの設定にならう。
                //拡張テーブルの場合、デフォルトブログの設定にならう。
                if (intRequestVar('blogid'))
                {
                    $blogid = intRequestVar('blogid');
                }
                else if(intRequestVar('itemid'))
                {
                    $blogid = getBlogIDFromItemID(intRequestVar('itemid'));
                }
                else
                {
                    $blogid = $CONF['DefaultBlog'];
                }
                $blog = & $manager->getBlog($blogid);
                $textareabody = $row_item["f__".$row["fname"]];
                $textareabody = ($blog->convertBreaks()) ? removeBreaks($textareabody) : $textareabody;
                echo htmlspecialchars($textareabody, ENT_QUOTES, _CHARSET);
                echo '</textarea><br />';
                break;
            case "DateTime": //表示形式
                $temp = ($row["fsetting"] == "Y-m-d") ? substr($row_item["f__".$row["fname"]], 0, 10) : $row_item["f__".$row["fname"]];
                echo ($row["fsetting"] == "Y-m-d") ? '(2005-12-24形式) ' : '(2005-12-24 23:59:59形式)';
                echo '<input tabindex="'.$tabindex.'" type="text" name="f__'.$row["fname"].'" size="25" maxlength="20" value="'.$temp.'" /><br />';
                break;
            case "Checkbox": //選択肢（複数可）
                $fsetting  = $this->preg_split_trim($row["fsetting"]);
                $itemArray = $this->preg_split_trim($row_item["f__".$row["fname"]]);
                $i = 0;
                foreach ($fsetting as $value)
                {
                    $checked = (in_array($value, $itemArray)) ? 'checked' : '';
                    echo '<input type="checkbox" name="f__'.$row["fname"].$i.'" id="i'.$row["fname"].'_'.$i.'" value="'.urlencode($value).'" '.$checked.' /> ';
                    echo '<label for="i'.$row["fname"].'_'.$i++.'">'.$value.'</label> ';
                }
                echo '<br />';
                break;
            case "Radio": //選択肢（複数不可）
                $fsetting = $this->preg_split_trim($row["fsetting"]);
                $itemVal  = $row_item["f__".$row["fname"]];
                $i = 0;
                foreach ($fsetting as $element)
                {
                    list($elementLabel, $elementValue) = $this->degradationElement($element);
                    $checked = ($elementValue == $itemVal) ? 'checked' : '';
                    echo '<input type="Radio" name="f__'.$row["fname"].'" id="i'.$row["fname"].$i.'" value="'.urlencode($elementValue).'" '.$checked.' /> ';
                    echo '<label for="i'.$row["fname"].$i++.'">'.$elementLabel.'</label> ';
                }
                echo '<br />';
                break;
            case "Select2": //選択肢（複数不可）
                $fsetting = $this->preg_split_trim($row["fsetting"]);
                $itemVal  = $row_item["f__".$row["fname"]];
                $i = 0;
                echo '<select tabindex="'.$tabindex.'" name="f__'.$row["fname"].'"><option value="">選択して下さい</option>';
                foreach ($fsetting as $element)
                {
                    list($elementLabel, $elementValue) = $this->degradationElement($element);
                    echo '<option value="'.$elementValue.'" '.(($elementValue == $itemVal) ? "selected" : "").'>';
                    echo $elementLabel.'</option>';
                }
                echo '</select> ';
                break;
            case "Select":   //リレーション
                if ($row["fsetting"]) $row_set = $this->getFieldDataFromFieldId($row["fsetting"]); //リンク先フィールドデータ(Textタイプ)
                $temp_tname = $this->getTableAboutFromID('tname', $row_set["ftid"]);
                $sql_str    = "SELECT * FROM ".$this->table_table.$temp_tname;
                $qid_sel    = sql_query($sql_str);
                echo '<select tabindex="'.$tabindex.'" name="f__'.$row["fname"].'"><option value="">選択して下さい</option>';
                if ($qid_sel)
                {
                    while($row_sel = sql_fetch_array($qid_sel))
                    {
                        echo '<option value="'.$row_sel["id"].'" '.(($row_item["f__".$row["fname"]] == $row_sel["id"]) ? "selected" : "").'>';
                        echo $row_sel["f__".$row_set["fname"]].'</option>';
                    }
                }
                echo '</select> ';
                break;
            case 'Image':    //コレクション/モード   (ファイル名のみを受け取り、create_sqlでディレクトリ名を付加する。)
                /*
                $fsetting = explode("/", $row["fsetting"]);
                $temlVal  = explode("/", $row_item["f__".$row["fname"]]); //初期値指定（ディレクトリとファイルに分解）
                $colDire  = $temlVal[0];
                $imgInit  = $temlVal[1];
                $colInit  = ($colDire) ? $colDire : $fsetting[0]; //既に入力した画像のコレクションを優先。デフォルトは、$fsetting[0]
                $UIMode   = $fsetting[1]; //Image or List
                */
                list($defaultCol, $UIMode, $amount) = explode("/", $row["fsetting"]);
                list($colDire, $imgInit) = explode("/", $row_item["f__".$row["fname"]]); //初期値指定（ディレクトリとファイルに分解）メディアディレクトリって階層構造？★★★
                $colInit = ($colDire) ? $colDire : $defaultCol; //既に入力した画像のコレクションを優先。デフォルトは、fsettingのコレクション
                if ($UIMode == 'Media')
                {
                    echo '<input type="input" name="img_f__'.$row["fname"].'" value="'.$row_item["f__".$row["fname"]].'"
                        id="inputimg_f__'.$row["fname"].'"
                        onkeyup="storeCaret(this); updPreview(\'img_f__'.$row["fname"].'\'); doMonitor();"
                        onclick="storeCaret(this);"
                        onselect="storeCaret(this);"
                        size="40"
                     />
                    <span class="jsbutton"
                        onmouseover="BtnHighlight(this);"
                        onmouseout="BtnNormal(this);"
                        onclick="addMedia()" >
                        <img src="images/button-media.gif" alt="" width="16" height="16"/>
                    </span>
                    '."\n";
                } else {
                    //--- Flash UI
                    echo '<input type="input" name="col_f__'.$row["fname"].'" value="'.$colInit.'" id="znItemFieldEX_col'.$row["fname"].'" /> '."\n";
                    echo '<input type="input" name="img_f__'.$row["fname"].'" value="'.$imgInit.'" id="znItemFieldEX_img'.$row["fname"].'" /><br />'."\n";
                    $this->putImageTypeForm($colInit, $row["fname"], $imgInit, $UIMode, $amount);
                }
                break;
            case 'Category':
                echo 'カテゴリidが自動設定されます。';
                break;
            default:
        }
    }
    /**
     * エレメントの分解：ラベル[値] | ラベル::値
     */
    function degradationElement($element)
    {
        $temp = preg_replace('/(.*?)\[(.*?)\]/', '$1::$2' , $element);
        list($elementLabel, $elementValue) = explode('::', $temp);
        $elementValue  = (!$elementValue) ? $elementLabel : $elementValue;
        return array($elementLabel, $elementValue);
    }
    /**
     * コレクション内の画像一覧
     */
    function collectionImageList($collection, $amount = 5)
    {
        global $DIR_MEDIA, $CONF;
        $ImageList = array();
        $mediaPath = $DIR_MEDIA.$collection;
        $extension = array('jpg', 'png', 'gif');
        $dir_name  = dir($mediaPath);
        $i         = 0;
        while ($file_name = $dir_name->read())
        {
            if (!is_dir($mediaPath.$file_name))
            {
                $timestamp = filemtime($mediaPath.$file_name) + '_' + ($i++); //同じタイムスタンプも存在する可能性があるので、お尻に連番を付ける。
                $temp = explode('.', strtolower($file_name)); //小文字にしてから分解
                if ( in_array($temp[1], $extension) ) $ImageList[$timestamp] = $file_name;
            }
        }
        krsort($ImageList); //キー（タイムスタンプ）で降順ソート
        $dissCount = count($ImageList) - $amount;
        for ($i = 0; $i < $dissCount; $i++) array_pop($ImageList); //配列のお尻から削除
        return $ImageList;
    }
    /**
     * Flash UI用
     */
    function doAction($actionType)
    {
        global $DIR_MEDIA, $DIR_LIBS, $CONF;
        
        if (!$this->checkTicket()) return 'Error'; //独自チケット確認（doActionは別セッションなので、チケット確認からメンバー生成）
        
        require_once $DIR_LIBS . 'MEDIA.php'; // media classes
        
        switch (requestVar('mode'))
        {
            case "up": //アップロード受取
                $this->media_upload();
                break;
            default:   //XML出力
                $mediaPath      = requestVar('mediaPath');
                $xml_str        = '<'.'?'.'xml version="1.0" encoding="UTF-8"'.'?'.">\n";
                $xml_str       .= "<ImageTypeFormSetting>\n";
                $collections    = MEDIA::getCollectionList();
                foreach ($collections as $dirname => $description)
                {
                    $xml_str .= '<collection listLabel="'.htmlspecialchars($description, ENT_QUOTES, _CHARSET).'" listData="'.$dirname.'" />'."\n";
                }
                $xml_str  .= "</ImageTypeFormSetting>\n";
                $xml_str  .= '<amount type="'.intRequestVar('amount').'" />'."\n";
                $xml_str  .= "<ImageTypeForm>\n";
                $temp_f   = ( substr($mediaPath, -1) == "/" ) ? "" : "/"; //お尻に/を
                $ImageList = $this->collectionImageList($mediaPath.$temp_f, intRequestVar('amount'));
                foreach ($ImageList as $file_name)
                {
                    $file_url = $CONF['MediaURL'].$mediaPath.$temp_f.$file_name;
                    $xml_str .= '<pic fileName="'.$file_name.'" img_url="'.$file_url.'" />'."\n";
                }
                $xml_str .= '</ImageTypeForm>';
                $xml_str  = mb_convert_encoding($xml_str, "UTF-8", _CHARSET);
                header("Content-Type: application/xml");
                echo $xml_str;
        }
    }
    /**
     * Checks the ticket that was passed along with the current request
     */
    function checkTicket() 
    {
        global $member;
        global $manager;
        
        // get ticket from request
        $ticket = requestVar('ticket');
        
        // no ticket -> don't allow
        if ($ticket == '')
            return false;
            
        // remove expired tickets first
        $manager->_cleanUpExpiredTickets();
        
        $memberId = intRequestVar('mid');
        
        // check if ticket is a valid one
        $query = 'SELECT COUNT(*) as result FROM ' . sql_table('tickets') . ' WHERE member=' . intval($memberId). ' and ticket=\''.addslashes($ticket).'\'';
        if (quickQuery($query) == 1)
        {
            // [in the original implementation, the checked ticket was deleted. This would lead to invalid
            //  tickets when using the browsers back button and clicking another link/form
            //  leaving the keys in the database is not a real problem, since they're member-specific and 
            //  only valid for a period of one hour
            // ]
            // sql_query('DELETE FROM '.sql_table('tickets').' WHERE member=' . intval($memberId). ' and ticket=\''.addslashes($ticket).'\'');
            $member = MEMBER::createFromID($memberId); //チケット確認がとれたらメンバー生成
            return true;
        } else {
            // not a valid ticket
            return false;
        }

    }
    /**
      * accepts a file for upload
      */
    function media_upload()
    {
        global $DIR_MEDIA, $member, $CONF;

        $uploadInfo = postFileInfo('Filedata'); //Filedata
        
        $filename = $uploadInfo['name'];
        $filetype = $uploadInfo['type'];
        $filesize = $uploadInfo['size'];
        $filetempname = $uploadInfo['tmp_name'];
        
        if ($filesize > $CONF['MaxUploadSize'])
            $this->media_doError(_ERROR_FILE_TOO_BIG); //ファイルが大きすぎます!
        
        // check file type against allowed types
        $ok = 0;
        $allowedtypes = explode (',', $CONF['AllowedTypes']);
        foreach ( $allowedtypes as $type ) 
            if (eregi("\." .$type. "$",$filename)) $ok = 1;    
        if (!$ok) $this->media_doError(_ERROR_BADFILETYPE . ' : ' . $filename); //このファイルタイプは認められていません
            
        if (!is_uploaded_file($filetempname)) 
            $this->media_doError(_ERROR_BADREQUEST); //不正なアップロード要求です

        // prefix filename with current date (YYYY-MM-DD-)
        // this to avoid nameclashes
        if ($CONF['MediaPrefix'])
            $filename = strftime("%Y%m%d-", time()) . $filename;

        $collection = requestVar('collection');
        $res = MEDIA::addMediaObject($collection, $filetempname, $filename);

        if ($res) $this->media_doError($res);
        
        // shows updated list afterwards
        //media_select();
    }
    function media_doError($msg)
    {
        ACTIONLOG :: add(0, 'znItemFieldEX:'.$msg);
    }
    /**
     * FlashによるImageタイプフィールドのフォーム
     */
    function putImageTypeForm(
        $mediaPath, //コレクション
        $fname,     //フィールド名
        $initial,   //初期選択ファイル名
        $UIMode,    //UI Mode : Image or List
        $amount = 5 //amount
    )
    {
        global $CONF, $manager, $member;
        $xmlUrl    = urlencode($CONF["ActionURL"]."?action=plugin&name=znItemFieldEX"); //アップロード先URLにも使用（mode=up）&を&amp;にすると読み込めない
        $mediaPath = urlencode($mediaPath);
        $f_width   = 302;
        $f_height  = 200;
        $f_cache   = "?r".chr(rand(65,122)).chr(rand(65,122))."=".chr(rand(65,122));
        $url_swf   = $this->getAdminURL().'ImageTypeForm.swf';
        $flashvars = 
            'mid='      .$member->getID()           .'&amp;'.
            'ticket='   .$manager->_generateTicket().'&amp;'.
            'xmlUrl='   .$xmlUrl                    .'&amp;'.
            'UIMode='   .$UIMode                    .'&amp;'.
            'mediaPath='.$mediaPath                 .'&amp;'.
            'fname='    .$fname                     .'&amp;'.
            'amount='   .$amount                    .'&amp;'.
            'initial='  .$initial;
        echo '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" '.
        'codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" '.
        'width="'.$f_width.'" height="'.$f_height.'" id="ifex" align="middle">'.
        '<param name="allowScriptAccess" value="sameDomain" />'.
        '<param name="movie" value="'.$url_swf.$f_cache.'" />'.
        '<param name="flashvars" value="'.$flashvars.'" />'.
        '<param name="quality" value="high" />'.
        '<param name="bgcolor" value="#ffffff" />'.
        '<embed src="'.$url_swf.$f_cache.'" quality="high" bgcolor="#ffffff" width="'.$f_width.'" height="'.$f_height.'" name="ifex" '.
        'align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" '.
        'pluginspage="http://www.macromedia.com/go/getflashplayer" flashvars="'.$flashvars.'" />'.
        '</object>';
    }
    /**
     * アイテムを追加した時
     */
    function event_PostAddItem($data)
    {
        global $blogid;
        if (requestVar('annul_itemdata') != 'no') $this->itemdataAdd("item_b".$blogid, $data['itemid']);
    }
    /**
     * アイテムを変更した時
     */
    function event_PreUpdateItem($data)
    {
        $bid = $data['blog']->blogid;
        $sql_str = "SELECT * FROM ".$this->table_table."item_b".$bid." WHERE id=".$data["itemid"];
        $qid = sql_query($sql_str);
        if ($qid and @sql_num_rows($qid) == 1)
        {
            if (requestVar('annul_itemdata') == 'no')
            {
                $this->itemdataDel("item_b".$bid, $data['itemid']);
                return;
            }
            $this->itemdataUpd("item_b".$bid, $data['itemid']);
        } else {
            $this->itemdataAdd("item_b".$bid, $data['itemid']);
        }
    }
    /**
     * アイテムを削除した時
     */
    function event_PreDeleteItem($data)
    {
        $blogid = getBlogIDFromItemID($data['itemid']);
        $this->itemdataDel("item_b".$blogid, $data['itemid']);
    }
    /**
     * 追加・変更・削除
     */
    function itemdataAdd($tname, $itemid)
    {
        //$itemid=0の場合、id指定をしない。つまり、AUTO_INCREMENT
        //アイテム拡張用テーブルのidは、AUTO_INCREMENTでない。
        $sql2 = $this->create_sql($tname);
        if (strlen($sql2) == 0) // 設定するものがないので終了
            return ;
        $sql_str = "INSERT INTO ".$this->table_table.$tname
                 . " SET ".(($itemid > 0) ? "id=".$itemid.", " : "")
                 . $sql2;
        sql_query($sql_str);
    }
    function itemdataUpd($tname, $itemid)
    {
        $sql_str = "UPDATE ".$this->table_table.$tname." SET ".$this->create_sql($tname)." WHERE id=".$itemid;
        sql_query($sql_str);
    }
    function itemdataDel($tname, $itemid)
    {
        $sql_str = "DELETE FROM ".$this->table_table.$tname." WHERE id=".$itemid;
        sql_query($sql_str);
    }
    /**
     * 共通SQL文作成
     */
    function create_sql($tname)
    {
        $ftid     = $this->getIDFromTableName($tname);
        $qid      = sql_query("SELECT * FROM ".$this->table_fields." WHERE ftid=".$ftid." ORDER BY forder");
        $setArray = array();
        if ($qid)
        {
            while($row = sql_fetch_array($qid))
            {
                switch ($row["ftype"])
                {
                    case "Textarea":
                        //アイテム拡張の場合、ブログの設定にならう。
                        //拡張テーブルの場合、デフォルトブログの設定にならう。
                        global $manager, $CONF;
                        if (intRequestVar('blogid'))
                        {
                            $blogid = intRequestVar('blogid');
                        }
                        else if(intRequestVar('itemid'))
                        {
                            $blogid = getBlogIDFromItemID(intRequestVar('itemid'));
                        }
                        else
                        {
                            $blogid = $CONF['DefaultBlog'];
                        }
                        $blog = &$manager->getBlog($blogid);
                        $textareabody = requestVar("f__".$row["fname"]);
                        $textareabody = ($blog->convertBreaks()) ? addBreaks($textareabody) : $textareabody;
                        $setArray[] = "f__".$row["fname"]."='".sql_real_escape_string($textareabody)."'";
                        break;
                    case "Checkbox": //選択肢
                        $fsetting  = $this->preg_split_trim($row["fsetting"]);
                        $tempArray = array();
                        $i = 0;
                        foreach ($fsetting as $value)
                        {
                            if (requestVar("f__".$row["fname"].$i++) == urlencode($value)) $tempArray[] = $value;
                        }
                        $setArray[] = "f__".$row["fname"]."='".implode("\n", $tempArray)."'";
                        break;
                    case "Image": //画像
                        //$fsetting    = explode("/", $row["fsetting"]);
                        
                        
                        list($defaultCol, $UIMode, $amount) = explode("/", $row["fsetting"]);
                        if ($UIMode == 'Media')
                        {
                            $temp = requestVar("img_f__".$row["fname"]); //書式 : <%image(thumbnail/1_vl05.gif|165|150|f)%>
                            if (preg_match('/<%image.*?%>/', $temp))
                            {
                                preg_match('/image\((.*?)\|/', $temp, $matches);
                                $tempArray = explode("/", $matches[1]);
                                if (count($tempArray) == 1)
                                {
                                    //ディレクトリ指定がないので補足
                                    //Private Collectionはメンバーid
                                    global $member;
                                    $temp = $member->getID().'/'.$matches[1];
                                } else {
                                    //ディレクトリ指定があるならそのまま
                                    $temp = $matches[1];
                                }
                            }
                        } else {
                            $temp = requestVar("col_f__".$row["fname"])."/".requestVar("img_f__".$row["fname"]);
                            $temp = (requestVar("img_f__".$row["fname"])) ? $temp : ""; //ファイル名が空の場合、コレクション名も空白にする
                        }
                        $setArray[] = "f__".$row["fname"]."='".$temp."'";
                        
                        break;
                    case "Radio": //選択肢
                    case "Select2": //選択肢
                        $fsetting   = $this->preg_split_trim($row["fsetting"]);
                        $value      = urldecode(requestVar("f__".$row["fname"]));
                        $fsetValArray = array();
                        foreach ($fsetting as $val)
                        {
                            list(, $elementValue) = $this->degradationElement($val);
                            $fsetValArray[] = $elementValue;
                        }
                        $value      = (in_array($value, $fsetValArray)) ? $value : '';
                        $setArray[] = "f__".$row["fname"]."='".sql_real_escape_string($value)."'";
                        break;
                    case "Category": //隠しフィールドタイプ（catidが入る）
                        $setArray[] = "f__".$row["fname"]."='".sql_real_escape_string(requestVar('catid'))."'";
                        break;
                    default:
                        $setArray[] = "f__".$row["fname"]."='".sql_real_escape_string(requestVar("f__".$row["fname"]))."'";
                }
            }
        }
        $sql_str .= implode(", ", $setArray);
        return $sql_str;
    }
    /**
     * テンプレート
     */
    function doTemplateVar(&$item)
    {
        // doTemplateVar(&$item, $fname, $format='', $templateName='', $templateParseFlag='')
        $args = func_get_args();
        $args[0] = $item->itemid;
        echo call_user_func_array(array($this, "getItemFieldEX"), $args);
    }
    /**
     * アイテムに対応する拡張フィールドを返す（別プラグインからも利用可能）ItemFieldEX API
     */
    function getItemFieldEX($itemid, $fieldPath, $format='', $templateName='', $templateParseFlag='')
    {
        $blogid = getBlogIDFromItemID($itemid);
        if (strstr($fieldPath, "->"))
        {
            return $this->relation($blogid, $fieldPath, $itemid, $format, $templateName, $templateParseFlag);
        } else {
            $sql_str = "SELECT f.ftype, f.fsetting, f.flabel ". //v0.12.4
                "FROM ".$this->table_fields." AS f, ".$this->table_tables." AS t ".
                "WHERE t.tname='item_b".$blogid."' AND t.tid=f.ftid AND f.fname='".$fieldPath."'";
            $qid_blog = sql_query($sql_str);
            if ($qid_blog and @sql_num_rows($qid_blog) == 1)
            {
                $row_blog = sql_fetch_array($qid_blog);//フィールドデータ
                $sql_str  = "SELECT * FROM ".$this->table_table."item_b".$blogid." WHERE id=".$itemid;
                $qid_item = sql_query($sql_str);
                if ($qid_item and @sql_num_rows($qid_item) == 1)
                {
                    $row_item = sql_fetch_array($qid_item);
                    return $this->DispEachType(
                        $itemid, 
                        $row_blog["ftype"], 
                        $row_blog["fsetting"], 
                        $row_item["f__".$fieldPath], 
                        $fieldPath, 
                        $format, 
                        $templateName, 
                        $templateParseFlag, //v0.12.4
                        $row_blog["flabel"] //v0.12.4
                    );
                } // else return "フィールドデータなし！！";
            }
        }
    }
    /**
     * テンプレート(リレーションの場合)
     */
    function relation($blogid, $path, $itemid, $format='', $templateName='', $templateParseFlag='')
    {
        $qid = $this->getRelationSql($blogid, $path);
        if ($qid and @sql_num_rows($qid) > 0)
        {
            $row     = sql_fetch_array($qid);
            $linkDat = array($row["ssql"], $row["sfname"], $row["sftype"], $row["sfsetting"]);
        } else {
            $linkDat = $this->createRelationSql($blogid, $path);
            $this->setRelationSql($blogid, $path, $linkDat);
        }
        $qid = sql_query($linkDat[0].$itemid);
        if ($qid and @sql_num_rows($qid) > 0)
        {
            $row = sql_fetch_array($qid);
            return $this->DispEachType($itemid, $linkDat[2], $linkDat[3], $row[$linkDat[1]], $path, $format, $templateName, $templateParseFlag);
        }
    }
    /**
     * SQLキャッシュ保存
     */
    function setRelationSql($blogid, $path, $linkDat)
    {
        $sql_str = "INSERT INTO ".$this->table_sql_cache." SET ".
        "sbid      = ".$blogid.", ".
        "spath     ='".$path."', ".
        "ssql      ='".$linkDat[0]."', ".
        "sfname    ='".$linkDat[1]."', ".
        "sftype    ='".$linkDat[2]."', ".
        "sfsetting ='".$linkDat[3]."' ";
        sql_query($sql_str);
    }
    /**
     * SQLキャッシュ呼出
     */
    function getRelationSql($blogid, $path)
    {
        $sql_str = "SELECT * FROM ".$this->table_sql_cache." WHERE sbid=".$blogid." AND spath='".$path."'";
        $qid     = sql_query($sql_str);
        return $qid;
    }
    /**
     * リレーションSQL用のパーツ作成(itemidを含まない)：不正な指定の場合の対処をしていません。
     */
    function createRelationSqlParts($blogid, $path)
    {
        $tname       = "item_b".$blogid;
        $fnameArray  = explode("->", $path);
        $whereArray  = array();
        $tableArray  = array();
        $loopCounter = 1;
        foreach ($fnameArray as $value)
        {
            $row_value = $this->getFieldDataFromTableName_FieldName($tname, $value);                     //$valueのフィールドデータ
            if ($loopCounter < count($fnameArray))                                                       //最後のフィールドはエスケープ
            {
                $row_link     = $this->getFieldDataFromFieldId($row_value["fsetting"]);                    //リンク先フィールドデータ
                $temp         = $this->getTableAboutFromID('tname', $row_link["ftid"]);                    //リンク先のテーブル名を得る
                $whereArray[] = $this->table_table.$tname.".f__".$value."=".$this->table_table.$temp.".id";//WHERE設定
                $tname        = $temp;                                                                     //次処理するテーブル
                $tableArray[] = $this->table_table.$tname;                                                 //テーブル登録
            }
            $loopCounter++;
        }
        return array("tableArray" => $tableArray, "whereArray" => $whereArray, "row_value" => $row_value);
    }
    /**
     * リレーションSQL作成(itemidを含まない)
     */
    function createRelationSql($blogid, $path)
    {
        $tname        = "item_b".$blogid;
        $sqlParts     = $this->createRelationSqlParts($blogid, $path);
        $tableArray   = $sqlParts["tableArray"];
        $tableArray[] = $this->table_table.$tname;
        $whereArray   = $sqlParts["whereArray"];
        $whereArray[] = $this->table_table.$tname.".id=";
        $temp         = $this->getTableAboutFromID('tname', $sqlParts["row_value"]["ftid"]);       //最後のフィールドのテーブル名
        $sql_str      = " SELECT ".$this->table_table.$temp.".f__".$sqlParts["row_value"]["fname"];//最後のテーブルの最後のフィールドが目的の値
        $sql_str     .= " FROM "  .implode(", ",    array_unique($tableArray));
        $sql_str     .= " WHERE " .implode(" AND ", array_unique($whereArray));
        return array(
            $sql_str, 
            "f__".$sqlParts["row_value"]["fname"], 
            $sqlParts["row_value"]["ftype"], 
            $sqlParts["row_value"]["fsetting"]
        );
    }
    /**
     * タイプ別表示
     */
    //検索queryが投げられている場合、該当するフィールドタイプのものはハイライトすること
    //ハイライトするフィールドタイプ（文字列）
    //Textタイプ、Textareaタイプ、Numberタイプ、Checkboxタイプ
    //$format:表示設定
    //  NumberType   : 区切り設定
    //  ImageType    : 縦横サイズ設定（縦横比率を保つ表示のみサポート）
    //    w=100
    //    h=100
    //  DateTimeType : 日付フォーマット
    //  Textarea     : htmlspecialchars
    //    
    function DispEachType($itemid, $ftype, $fsetting, $rdata, $path, $format='', $templateName='', $templateParseFlag='', $label='') //v0.12.4
    {
        global $CONF, $query, $DIR_MEDIA, $manager; //global $itemid じゃなくしたけど大丈夫かな？
        global $blog;
        //INDEXスキンの場合、$queryに検索ワードが入っている。
        //ITEMスキンの場合、$queryにSQL文が入っている。ITEMスキンの場合、ハイライトしないように。
        $expression = array();
        if (!$itemid and $query)
        {
            $i       = 0;
            $ex_temp = explode(' ', $query);
            foreach ($ex_temp as $value)
            {
                //検索ワード２つ目以降の、検索記号（,-）を削除
                if ($i == 0) $expression[] = $value; else $expression[] = str_replace(array(",", "-"), "", $value);
                $i++;
            }
        }
        if ($templateName) //縮小画像の表記があるか否かを確認する為に、事前に読み込んでおく
        {
            if (!(strpos($templateName, '=') === false)) //「=」が入る場合、テンプレート動的指定
            {
                $whereArray = explode('|', $templateName);
                foreach ($whereArray as $val) //a=a, b=b
                {
                    $setArray = explode('=', $val);
                    if ($rdata == $setArray[0]) $templateName = $setArray[1]; //合った条件のテンプレートに
                }
            }
            
            $itemTemplate = $this->getTemplateData($templateName, 'ITEM');
        }
        switch ($ftype)
        {
            case "Text":
                if ($rdata == '') return;
                //$queryが空でも$expressionは空とは限らない。
                $rdata = (!$itemid and $query) ? highlight($rdata, $expression, $this->highlight) : $rdata;
                $putData = array('fieldex' => $rdata);
                break;
            case "Textarea":
                if ($rdata == '') return;
                //$queryが空でも$expressionは空とは限らない。
                switch ($format)
                {
                    case 'htmlspecialchars':
                        $rdata = htmlspecialchars($rdata, ENT_QUOTES, _CHARSET);
                        $rdata = nl2br($rdata);
                        break;
                    case 'removeBreaks':
                        $rdata = removeBreaks($rdata);
                        break;
                    case 'removeBreaks_htmlspecialchars':
                        $rdata = removeBreaks($rdata);
                        $rdata = htmlspecialchars($rdata, ENT_QUOTES, _CHARSET);
                        $rdata = nl2br($rdata);
                        break;
                    case 'removeBreaks_htmlspecialchars_space':
                        $rdata = removeBreaks($rdata);
                        $rdata = htmlspecialchars($rdata, ENT_QUOTES, _CHARSET);
                        $rdata = preg_replace('/\t/', '&nbsp;&nbsp;&nbsp;&nbsp;', $rdata); //\sだと改行も含まれてしまう。
                        $rdata = nl2br($rdata);
                        break;
                }
                $rdata = (!$itemid and $query) ? highlight($rdata, $expression, $this->highlight) : $rdata;
                $temp = new stdClass;
                $temp->body = &$rdata;
                $params = array('item' => &$temp);
                $manager->notify('PreItem', $params);
                
                //BODYACTIONS
                global $currentTemplateName;
                $item     = $this->getitem($itemid); //アイテム情報を得る。
                $template = & $manager->getTemplate($currentTemplateName);
                $actions  = new BODYACTIONS($blog);
                $parser   = new PARSER($actions->getDefinedActions(), $actions);
                $actions->setTemplate($template);
                //$actions->setHighlight($this->strHighlight);
                $actions->setCurrentItem($item);
                //$actions->setParser($parser);
                ob_start();
                $parser->parse($rdata);
                $rdata = ob_get_contents();
                ob_end_clean();
                
                
                /*
                global $currentTemplateName;
                $template       = & $manager->getTemplate($currentTemplateName);
                $item           = $this->getitem($itemid); //
                $actions        = new ITEMACTIONS($blog);
                $templateParser = new PARSER(array('image','media','popup'), $actions);
                $actions->setParser($templateParser);
                $actions->setCurrentItem($item);
                $actions->setTemplate($template);
                ob_start();
                $templateParser->parse($rdata);
                $rdata = ob_get_contents();
                ob_end_clean();
                */
                
                $putData = array('fieldex' => $rdata);
                break;
            case "Number": //書式変更に対応した際には、highlightとの関係を修正する必要があるかもしれない。
                if ($format)
                {
                    $formatPara    = explode("|", $format);// 2|46|44 number_format($rdata, 2, '.', ',');
                    $decimals      = $formatPara[0];
                    $dec_point     = chr($formatPara[1]); //無効な文字の場合、デフォルトを設定してあげた方が良いかなぁ。
                    $thousands_sep = chr($formatPara[2]); //
                    $zeroDispFlag  = ($formatPara[3] == 'ZERODISP') ? TRUE : FALSE; //ゼロの時、表示するかどうか
                    if (!$zeroDispFlag && $rdata == 0) return;
                    $rdata         = number_format($rdata, $decimals, $dec_point, $thousands_sep);
                } elseif ($rdata == 0) return;
                //$queryが空でも$expressionは空とは限らない。
                $rdata   = (!$itemid and $query) ? highlight($rdata, $expression, $this->highlight) : $rdata;
                //書式設定している場合、ハイライトさせるのは、簡単ではない。プラグインは、できるだけ軽くしたい。
                $putData = array('fieldex' => $rdata);
                break;
            case "Checkbox":
                if ($rdata == '') return;
                $Checkbox = $this->preg_split_trim($rdata);
                if ($format)
                {
                    if ($format{0} == '$') //頭が
                    {
                        $disp = explode("$", $format);
                        //$disp[0] : 不要（頭は$のはずだから）
                        //$disp[1] : 一致時  表示文字列
                        //$disp[2] : 不一致時表示文字列
                        //$disp[3] : 条件式文字列
                        $where  = explode("||", $disp[3]);
                        $inFlag = FALSE;
                        foreach ($where as $value) if (in_array($value, $Checkbox)) $inFlag = TRUE;
                        $rdata = ($inFlag) ? $disp[1] : $disp[2];
                    } else $rdata = implode($format, $Checkbox);
                } else {
                    $rdata = '';
                    foreach ($Checkbox as $value) $rdata .= '<li>'.$value.'</li>';
                }
                //$queryが空でも$expressionは空とは限らない。
                $rdata = (!$itemid and $query) ? highlight($rdata, $expression, $this->highlight) : $rdata;
                $putData = array('fieldex' => $rdata);
                break;
            case 'Radio': //あえてハイライトしない。Path文字列などの用途を想定して。
            case 'Select2': //
                if ($rdata == '') return;
                $putData = array('fieldex' => $rdata);
                break;
            case "DateTime":
                if ($rdata == '0000-00-00 00:00:00') return;
                if ($format)
                {
                    //$format = 'Y年m月d日（[%漢字曜日%]）';
                    sscanf($rdata, '%d-%d-%d %d:%d:%d', $y, $m, $d, $h, $i, $s);
                    $rdata = date($format, mktime($h, $i, $s, $m, $d, $y));
                    //漢字表記曜日対応
                    $jcweek   = array('日','月','火','水','木','金','土');
                    $weekData = $jcweek[date('w', mktime($h,$i,$s,$m,$d,$y))];
                    $rdata    = preg_replace('/\[%漢字曜日%\]/', $weekData, $rdata);
                } else {
                    $rdata = (($fsetting == "Y-m-d") ? substr($rdata, 0, 10) : $rdata);
                }
                //$queryが空でも$expressionは空とは限らない。
                $rdata   = (!$itemid and $query) ? highlight($rdata, $expression, $this->highlight) : $rdata;
                $putData = array('fieldex' => $rdata);
                break;
            case "Image":
                if ($rdata == '') return;
                if ($format)
                {
                    $formatPara = explode("=", $format);
                    $attribute  = $formatPara[0];
                    $value      = $formatPara[1];
                    $imgInfo    = @getimagesize($DIR_MEDIA.$rdata);
                    $wInfo      = $imgInfo[0];
                    $hInfo      = $imgInfo[1];
                    //$wid, $heiは、サムネイルサイズにも使用
                    $wid        = ($attribute == 'w') ? $value : floor(($value * $wInfo) / $hInfo + 0.5);
                    $hei        = ($attribute == 'w') ? floor(($value * $hInfo) / $wInfo + 0.5) : $value;
                    
                    $imgSize = 'width="'.$wid.'" height="'.$hei.'"';
                } else $imgSize = '';
                $fnameArray = explode("->", $path);
                $className  = implode("__", $fnameArray);
                $mediaPath  = $rdata;
                $rdata   = '<img src="'.$CONF['MediaURL'].$rdata.'" class="znifex_'.$className.'" '.$imgSize.' />';
                if ( strpos($itemTemplate, '<%thumbnail%>') === false )
                {
                    //テンプレートにサムネイルテンプレート変数がない場合、指定イメージをそのまま出力
                    $trdata = '';
                } else {
                    //テンプレートにサムネイルテンプレート変数がある場合、サムネイルを作成（呼び出し）
                    $timgArray  = explode("/", $mediaPath); //スラッシュを、
                    $timgPath   = implode("_", $timgArray); //アンダーバーに変換
                    //$attribute.$value : フォーマット文字列接頭辞を付加（サムネイルなのにフォーマット指定ない場合は付加されないだけなので問題ない）
                    $timgPath   = 'fieldexthumbnail/'.$attribute.$value.'_'.$timgPath;
                    $trdata     = '<img src="'.$CONF['MediaURL'].$timgPath.'" class="znifex_'.$className.'" '.$imgSize.' />';
                    //ディレクトリ確認（なければ作成）
                    if (!is_dir ($DIR_MEDIA.'fieldexthumbnail'))
                    {
                        $old    = umask(0);
                        $result = mkdir($DIR_MEDIA.'fieldexthumbnail', 0777);
                        umask($old);
                        if (!$result) $trdata = $rdata; //ディレクトリ作成できない場合、サムネイル表示できないので、オリジナルを表示
                    }
                    if ($trdata != $rdata && !file_exists($DIR_MEDIA.$timgPath) ) //ディレクトリが作成できない状況でもなく、サムネイルがない場合、作成
                    {
                        $result = $this->createThumbnail($DIR_MEDIA.$mediaPath, $DIR_MEDIA.$timgPath, $wid, $hei);
                        if (!$result) $trdata = $rdata; //サムネイル作成できない場合、オリジナルを表示
                    }
                }
                $putData = array('fieldex' => $rdata, 'imgurl' => $CONF['MediaURL'].$mediaPath, 'thumbnail' => $trdata);
                break;
            case "Select":
                if ($rdata == '') return;
                break;
            default:
        }
        $putData['label'] = $label; //v0.12.4
        if ($templateName)
        {
            $rdata = $this->znifexTemplateFill( $itemTemplate, $putData ); //指定テンプレートで
            if (strtolower($templateParseFlag) == 'true')
            {
                //Nucleus標準テンプレートを使用する設定の場合のみ実行
                $item           = $this->getitem($itemid); //アイテム情報を得る。
                $actions        = new ITEMACTIONS($blog);
                $templateParser = new PARSER($actions->getDefinedActions(), $actions);
                $actions->setParser($templateParser);
                $actions->setCurrentItem($item);
                //$actions->setTemplate($itemTemplate);
                
                ob_start();
                $templateParser->parse($rdata);
                $rdata = ob_get_contents();
                ob_end_clean();
            }
        }
        return $rdata;
    }
    function getitem($itemid)
    {
        $itemid = intval($itemid);
        $query =  'SELECT i.idraft as draft, i.inumber as itemid, i.iclosed as closed, '
               . ' i.ititle as title, i.ibody as body, m.mname as author, '
               . ' i.iauthor as authorid, i.itime, i.imore as more, i.ikarmapos as karmapos, '
               . ' i.ikarmaneg as karmaneg, i.icat as catid, i.iblog as blogid '
               . ' FROM '.sql_table('item').' as i, '.sql_table('member').' as m, ' . sql_table('blog') . ' as b '
               . ' WHERE i.inumber=' . $itemid
               . ' and i.iauthor=m.mnumber '
               . ' and i.iblog=b.bnumber'
               . ' LIMIT 1';
        $res = sql_query($query);
        if (sql_num_rows($res) == 1)
        {
            $item = sql_fetch_object($res);
            $item->timestamp = strtotime($item->itime);    // string timestamp -> unix timestamp
            return $item;
        } else return 0;
    }
    function znifexTemplateFill($template, $values)
    {
        if (sizeof($values) == 0) return $template;
        for(reset($values); $key = key($values); next($values)) $template = str_replace("<%$key%>",$values[$key],$template);
        return $template;
    }
    /**
     * サムネイル作成
     */
    //I referred to NP_CustomThumbnail
    //http://sangatsu.com/index.php?itemid=112
    function createThumbnail($oPath, $tPath, $tWid, $tHei)
    {
        $oInfo      = getimagesize($oPath);
        if (!$oInfo) return false;
        $imgTypeStr = array(1 => 'gif', 2 => 'jpeg', 3 => 'png');
        $oWid       = $oInfo[0];
        $oHei       = $oInfo[1];
        $tWid       = ($tWid) ? $tWid : 100;                                  //デフォルトは100
        $tHei       = ($tHei) ? $tHei : floor(($tWid * $oHei) / $oWid + 0.5); //デフォルトは横幅100で比率を保った値
        $imgType    = $oInfo[2];
        if ($imgType < 1 || $imgType > 3) return false;
        eval('$oImg = @imagecreatefrom'.$imgTypeStr[$imgType].'($oPath);');
        if (!$oImg) return false;
        if ($imgType == 1) //gifの場合のみ
        {
            $trans     = imagecolortransparent($oImg);                    //透明色
            $colors    = imagecolorstotal($oImg);                         //色数
            $transFlag = ($trans >= 0 && $trans < $colors) ? true : false;//透明色が、色数内か否か
        } else $transFlag = false;
        if ($tImg = @imagecreatetruecolor($tWid, $tHei))
        {
            if ($imgType == 1) //gifの場合のみ
            {
                if ($transFlag)
                {
                    $transColor = imagecolorsforindex($oImg, $trans);
                    imagefill($tImg, 0, 0, imagecolorallocate($tImg, $transColor['red'], $transColor['green'], $transColor['blue']));
                    imagecopyresampled($tImg, $oImg, 0, 0, 0, 0, $tWid, $tHei, $oWid, $oHei);
                    imagetruecolortopalette($tImg, false, $colors);
                    //パレットは、変えましたが、同じ色のカウントを保ちます。私たちは、最も近くで色が正しい透明色であると予想します。
                    imagecolortransparent($tImg, imagecolorclosest($tImg, $transColor['red'], $transColor['green'], $transColor['blue']));
                } else imagecopyresampled($tImg, $oImg, 0, 0, 0, 0, $tWid, $tHei, $oWid, $oHei);
            } else {
                if (function_exists('imagesavealpha') && $imgType == 3) //pngの場合、アルファチャンネルを保存
                {
                    imagealphablending($tImg, false);
                    imagesavealpha($tImg, true);
                }
                imagecopyresampled($tImg, $oImg, 0, 0, 0, 0, $tWid, $tHei, $oWid, $oHei);
            }
        } else {
            $tImg = imagecreate($tWid, $tHei);
            if ($imgType == 1 && $transFlag) //gifの場合のみ
            {
                imagepalettecopy($tImg, $oImg);
                imagefill($tImg, 0, 0, $trans);
                imagecolortransparent($tImg, $trans);
            }
            imagecopyresized($tImg, $oImg, 0, 0, 0, 0, $tWid, $tHei, $oWid, $oHei);
        }
        eval("image".$imgTypeStr[$imgType].'($tImg, $tPath);');
        imagedestroy($tImg);
        imagedestroy($oImg);
        return true;
    }
    /**
     * 指定したテンプレートの各種データを得る
     */
    function getTemplateData($templateName, $tpartName)
    {
        $sql_str  = "SELECT t.tcontent as result FROM ".sql_table('template_desc')." AS dt, ".sql_table('template')." AS t WHERE ".
            "dt.tdname   ='".$templateName."' AND ".
            " t.tdesc    =dt.tdnumber"    ."  AND ".
            " t.tpartname='".$tpartName."'";
        return quickQuery($sql_str);
    }
    /**
     * スキン
     */
    function doSkinVar(
        $skinType, 
        $mode = '', //item              or archivelist | archivelist    or index | archive | futureindex | futurearchive
        $p01  = '', //fieldPath                                            sort key
        $p02  = '', //Format                                               ASC or DESC
        $p03  = '', //templateName                                         WHERE (i.field|=|value)
        $p04  = '', //templateParseFlag                                    Template
        $p05  = '', //                                                     amount
        $p06  = '', //                                                     otherblogid
        $p07  = ''  //                                                     allCatFlag
    )
    {
        switch ($mode)
        {
            case "index":
                $sortkey     = ($p01) ? $p01 : 'i.itime';
                $sort        = ($p02) ? $p02 : 'DESC';
                $proviso     = ($p03) ? $p03 : '';
                $template    = ($p04) ? $p04 : 'default/index';
                $amount      = ($p05) ? $p05 : 10;
                $otherblogid = ($p06) ? $p06 : 0;
                $allCatFlag  = ($p07) ? TRUE : FALSE;
                $this->skinIndexMode('blog', $sortkey, $sort, $proviso, $template, $amount, $otherblogid, $allCatFlag);
                break;
            case "item":
                $fieldPath         = ($p01) ? $p01 : '';
                $format            = ($p02) ? $p02 : '';
                $templateName      = ($p03) ? $p03 : '';
                $templateParseFlag = ($p04) ? $p04 : '';
                if ($skinType == 'item') $this->skinItemMode($fieldPath, $format, $templateName, $templateParseFlag);
                break;
        }
    }
    /**
     * スキン：INDEXモード
     */
    function skinIndexMode(
        $type    ,   //_preBlogContent, _postBlogContent に渡すタイプ
        $sortkey ,   //sort key
        $sort    ,   //ASC or DESC
        $proviso ,   //WHERE (i.field|=|value)
        $template,   //Template
        $amount  ,   //amount
        $otherbid,   //otherblogid
        $allCatFlag  //カレントカテゴリの絞込み解除
    )
    {
        global $manager;
        if ($otherbid)
        {
            $blogid = $otherbid;
            $blog   = &$manager->getBlog($blogid);
        } else {
            global $blog, $blogid;
        }
        $params = array('blog' => &$blog, 'type' => $type);
        $manager->notify('PreBlogContent',$params);
        
        $blogid            = intval($blogid);
        $boolMarkArray     = array();         //論理演算子保持用
        $multiProvisoArray = array();         //複数条件を設定した場合の分割用
        $provisoArray      = array();         //条件生成用
        $whereArray        = array();         //リレーションなどその他のWHERE生成用
        $tableArray        = array();
        $itemEXTable       = $this->table_table."item_b".$blogid;
        
        //=====条件 AND (条件)
        $multiProvisoArray = preg_split('/(&&|\|\|)/', $proviso); /* 論理演算子 &&、|| */
        if ($proviso)
        {
            foreach ($multiProvisoArray as $val)
            {
                $temp           = explode("|", $val);
                $provisoField   = $temp[0]; //条件フィールド
                $provisoCompare = $temp[1]; //条件演算子
                $provisoValue   = $temp[2]; //条件値（Numberタイプを''で囲んでいても”演算”には問題ないみたい。）
                $provisoValue   = ($provisoValue == '[date]') ? date('Y-m-d H:i:s') : $provisoValue;
                $mark           = ($provisoCompare == "%") ? "%"      : "";                    //条件演算子によって、
                $calculate      = ($provisoCompare == "%") ? " LIKE " : $provisoCompare;       //あいまいな条件指定などもできるように
                if (substr($provisoField, 0, 2) == "i.")                                       //Nucleus標準アイテムのフィールドが指定された場合
                {
                    $provisoArray[] = $provisoField.$calculate."'".$mark.$provisoValue.$mark."'";
                } else {
                    if (strstr($provisoField, "->"))                                             //リレーションが指定された場合
                    {
                        $sqlParts       = $this->createRelationSqlParts($blogid, $provisoField);
                        $tableArray     = $sqlParts["tableArray"];
                        $tableArray[]   = $itemEXTable;
                        $whereArray[]   = $sqlParts["whereArray"];
                        $whereArray[]   = $itemEXTable.".id=i.inumber";
                        $temp           = $this->getTableAboutFromID('tname', $sqlParts["row_value"]["ftid"]); //最後のフィールドのテーブル名
                        $provisoArray[] = $this->table_table.$temp.".f__".$sqlParts["row_value"]["fname"].' '.$calculate."'".$mark.$provisoValue.$mark."'";
                    } else {                                                                     //アイテム拡張テーブルのフィールドが指定された場合
                        $tableArray[]   = $itemEXTable;
                        $whereArray[]   = $itemEXTable.".id=i.inumber";
                        $provisoArray[] = $itemEXTable.".f__".$provisoField.' '.$calculate."'".$mark.$provisoValue.$mark."'";
                    }
                }
            }
        }
        
        //↑ここまでで、$whereArray, $provisoArray, $tableArray を生成
        //$provisoArrayと、$proviso から、$provisoWhere を生成
        $countProviso = count($provisoArray);
        switch ($countProviso)
        {
            case 0:  //条件がない
                $provisoWhere = '';
                break;
            case 1:  //条件が1つ
                //$provisoWhere = '(' . $provisoArray[0] . ')';
                $provisoWhere = $provisoArray[0];
                break;
            default: //条件が複数
                $provisoWhere = '(' . $provisoArray[0];
                preg_match_all('/(&&|\|\|)/', $proviso, $provisoMark, PREG_SET_ORDER);
                for ($i = 1; $i < $countProviso; $i++) $provisoWhere .= (($provisoMark[$i-1][0] == '&&') ? ' AND ' : ' OR ') . $provisoArray[$i];
                $provisoWhere .= ')';
                
        }
        //$provisoWhere = '(' . implode(" AND ", $provisoArray) . ')';
        
        //=====ソートキー
        if (substr($sortkey, 0, 2) == "i.")                                            //Nucleus標準アイテムのフィールドが指定された場合
        {
            $orderby = $sortkey;
        } else {
            if (strstr($sortkey, "->"))                                                  //リレーションが指定された場合
            {
                $sqlParts     = $this->createRelationSqlParts($blogid, $sortkey);
                $tableArray   = array_merge($tableArray, $sqlParts["tableArray"]);
                $tableArray[] = $itemEXTable;
                $whereArray[] = $sqlParts["whereArray"];
                $whereArray[] = $itemEXTable.".id=i.inumber";
                $temp         = $this->getTableAboutFromID('tname', $sqlParts["row_value"]["ftid"]); //最後のフィールドのテーブル名
                $orderby      = $this->table_table.$temp.".f__".$sqlParts["row_value"]["fname"];
            } else {                                                                     //アイテム拡張テーブルのフィールドが指定された場合
                $tableArray[] = $itemEXTable;
                $whereArray[] = $itemEXTable.".id=i.inumber";
                $orderby      = $itemEXTable.".f__".$sortkey;
            }
        }
        
        //NP_MultipleCategoriesサブカテゴリ選択対応
        global $subcatid;
        if ($subcatid > 0)
        {
            if ($manager->pluginInstalled('NP_MultipleCategories'))
            {
                $tableArray[] = sql_table('plug_multiple_categories')." AS mc ";
                $whereArray[] = "i.inumber=mc.item_id";
                $whereArray[] = "mc.subcategories REGEXP '(^|,)".intval($subcatid)."(,|$)' ";
            }
        }
        /*
        */
        
        //SQL生成
        $table    = implode(", ", array_unique($tableArray));
        $temp     = $blog->getSqlBlog('');
        $temp     = ($allCatFlag) ? preg_replace('/and\si\.icat=[0-9]*?\s/', '', $temp) : $temp; //カレントカテゴリの絞込み解除
        $temp     = explode("WHERE", $temp);
        $temp2    = explode("ORDER BY", $temp[1]);
        $sql1     = $temp[0].(($table) ? ", " : "").$table.' WHERE ';
        $sql2     = ' ORDER BY '.$orderby.' '.$sort.' LIMIT 0,'.$amount;
        $where    = implode(" AND ", array_unique($whereArray));
        $sqlquery = $sql1.$temp2[0].(($where) ? " AND ".$where : "").(($provisoWhere) ? " AND ".$provisoWhere : '').$sql2;
        
        //echo $sqlquery.'<br />';
        $blog->showUsingQuery($template, $sqlquery, '', 1, 1);
        $params = array('blog' => &$blog, 'type' => $type);
        $manager->notify('PostBlogContent',$params);
    }
    /**
     * スキン：ITEMモード
     */
    function skinItemMode($fieldPath, $format, $templateName='', $templateParseFlag='')
    {
        global $itemid;
        echo $this->getItemFieldEX($itemid, $fieldPath, $format, $templateName, $templateParseFlag);
    }
    /**
     * 検索前イベント
     */
    function event_PreSearchResults($data)
    {
        foreach($data['blogs'] as $blogid)
        {
            $array = $this->_PreSearchResults($data, $blogid);
            $data['items'] = array_unique(array_merge($data['items'], $array));
        }
    }
    /**
     * ブログ別検索結果
     */
    //検索対象ではないフィールドタイプを除外すること：$tgtFieldWhereからのみ外してあげればＯＫ
    function _PreSearchResults(&$data, $blogid)
    {
        global $blog;
        //$blogid        = $blog->getID();
        $fieldArray    = $this->getSearchField($blogid);
        if ($fieldArray === FALSE) return; //検索対象フィールド（ブログオプションから）が未指定なら終了
        $tgtField      = array();          //SQL構築用、検索対象フィールド
        $relationField = array();          //SQL構築用、リレーションのPath
        $blogs         = $data['blogs'];
        $query         = $data['query'];
        //$items         = & $data['items'];
        $sqlquery      = 'SELECT i.inumber AS itemid FROM '.sql_table('item').' AS i ';
        $sqlquery     .= 'LEFT JOIN '.$this->table_table."item_b".$blogid.' AS ex ON i.inumber = ex.id ';
        foreach ($fieldArray as $value)                                                        //リレーションか、そうでないかで処理を分ける
        {
            if (strstr($value, "->"))
            {
                $relationField[] = $value;
            } else {
                $row_value = $this->getFieldDataFromTableName_FieldName("item_b".$blogid, $value); //検索対象となるフィールドタイプの場合、追加する
                if ($this->checkSearchField($row_value["ftype"])) $tgtField[] = "zzz.ex.f__".$value;
            }
        }
        //リレーション
        //・WHERE【イ】検索対象設定（リレーションの最後のフィールド）
        //・ON   【ロ】リレーション関係設定
        //・FROM 【ハ】登場するテーブル（別名）設定（アイテム拡張テーブルはいらない）
        $relationWhere = "";                                                                   //      リレーション関係設定
        $asNumber      = 1;                                                                    //      最後のフィールド検知用
        foreach ($relationField as $path) //ターゲット単位（テンプレート）
        {
            $tname       = "item_b".$blogid;
            $fnameArray  = explode("->", $path);                                                 //      リレーション分解
            $loopCounter = 1;
            foreach ($fnameArray as $value) //->単位（$valueのテーブルは$asNumber - 1）
            {
                $row_value = $this->getFieldDataFromTableName_FieldName($tname, $value);           //      $valueのフィールドデータ
                if ($loopCounter < count($fnameArray))                                             //      最後のフィールドはエスケープ
                {
                    $row_link       = $this->getFieldDataFromFieldId($row_value["fsetting"]);        //      リンク先フィールドデータ
                    $temp           = $this->getTableAboutFromID('tname', $row_link["ftid"]);        //      リンク先のテーブル名を得る
                    $tempAs         = ($loopCounter == 1) ? "ex" : "t".($asNumber - 1);              //アイテム拡張テーブル（$loopCounter=1）に限っては、ex
                    $tname          = $temp;                                                         //      次処理するリンク先のテーブル
                    $sqlquery      .= " LEFT JOIN ".$this->table_table.$tname." AS t".$asNumber;     //【ハ】登場するテーブル登録（別名）（アイテム拡張のリンク先テーブルは、最初$asNumber=1）
                    $sqlquery      .= ' ON '.$tempAs.".f__".$value."="."t".$asNumber.".id ";         //【ロ】リレーション関係設定（自テーブルは、$asNumber - 1）
                }
                $asNumber++;                                                                       //      全体通し（最後は増やす必要がない）
                $loopCounter++;                                                                    //      ->単位
            }//end ->単位
            if ($this->checkSearchField($row_value["ftype"]))                                    //【イ】$row_valueが、検索対象となるフィールドタイプの場合、追加する
            {
                $tgtField[] = "zzz.t".($asNumber - 2).".f__".$value;                               //      検索対象設定（リレーションの最後のフィールド。最後のテーブルは、$asNumber-2）
            }
        }//end ターゲット単位（テンプレート）
        
        $tgtFieldWhere = implode(",", $tgtField);//★echo '%%%% '.$tgtFieldWhere.' %%%%';
        $searchclass   = new SEARCH($query);
        $where         = $searchclass->boolean_sql_where($tgtFieldWhere);
        $where         = strtr($where, array('i.zzz.'=> ''));
        $sqlquery     .= ' WHERE i.idraft = 0 AND i.itime<='.mysqldate($blog->getCorrectTime()).' AND i.iblog IN ('.implode(',', $blogs).') AND '.$where;
        $res           = sql_query($sqlquery);
        $array         = array();
        while ($itemid = sql_fetch_row($res)) array_push($array, $itemid[0]);
        return $array;
    }
    /**
     * 検索対象となるフィールドタイプかどうか確認
     */
    function checkSearchField($ftype)
    {
        //Radioは検索対象としない。
        switch ($ftype)
        {
            case "Text":
            case "Textarea":
            case "Number":
            case "Checkbox":
            case "Select2":
            case "DateTime":
                $ret = true;
                break;
            default:
                $ret = false;
        }
        return $ret;
    }
    /**
     * 検索対象となるフィールドの一覧を得る
     */
    function getSearchField($blogid)
    {
        //ブログごとに、検索フィールドを設定する必要がある。
        /*
        $fieldArray = array();
        $tcontent   = $this->getTemplateData($this->getOption('searchtemplate'), "ITEM");
        preg_match_all("/<%znItemFieldEX\((.*?)\)%>/", $tcontent, $fieldList, PREG_PATTERN_ORDER);
        foreach ($fieldList[1] as $value)
        {
            //テンプレートのパラメータには、書式パラメータが指定してある場合があるので、フィールドだけ摘出
            $temp = explode(",", $value);
            $fieldArray[] = $temp[0];
        }
        */
        $fieldOption = trim($this->getBlogOption($blogid, 'searchField'));
        if ($fieldOption == "") return FALSE; //$fieldOption=""でも、count($fieldArray)は1になる。FALSEかどうかで判定すること。
        //$fieldArray  = $this->preg_split_trim($fieldOption);
        $fieldArray = array_map('trim', preg_split("/[\r\n]+/", $fieldOption));
        return $fieldArray;
    }
    /**
     * 改行で分離して、ホワイトスペースを削除して、配列にする
     */
    function preg_split_trim($str)
    {
        $tempArray = preg_split("/[\r\n]+/", $str);
        $retArray  = array();
        foreach ($tempArray as $value) $retArray[] = trim($value);
        return $retArray;
    }
    /**
     * テーブル名・フィールド名からフィールドデータを得る
     */
    function getFieldDataFromTableName_FieldName($tname, $fname)
    {
        $ftid    = $this->getIDFromTableName($tname);
        $sql_str = "SELECT * FROM ".$this->table_fields." WHERE ftid=".$ftid." AND fname='".$fname."'";
        $qid_set = sql_query($sql_str);
        return sql_fetch_array($qid_set);
    }
    /**
     * fidからフィールドデータを得る
     */
    function getFieldDataFromFieldId($id)
    {
        $sql_str = "SELECT * FROM ".$this->table_fields." WHERE fid=".$id;
        $qid_set = sql_query($sql_str);
        return sql_fetch_array($qid_set);
    }
    /**
     * テーブル名からテーブルデータを得る
     */
    function getTableDataFromTableName($tname)
    {
        $sql_str = "SELECT * FROM ".$this->table_tables." WHERE tname='".$tname."'";
        $qid_set = sql_query($sql_str);
        return sql_fetch_array($qid_set);
    }
    /**
     * テーブル名からテーブルidを得る
     */
    function getIDFromTableName($name)
    {
        return quickQuery('SELECT tid as result FROM '.$this->table_tables." WHERE tname='".$name."'");
    }
    /**
     * テーブルidからテーブル群テーブルの任意のカラムのレコードを得る
     */
    function getTableAboutFromID($column, $id)
    {
        return quickQuery('SELECT '.$column.' as result FROM '.$this->table_tables.' WHERE tid='.intval($id));
    }
    /**
     * Version Check Service(XML-RPC)
     */
    function verCheck()
    {
        //$xmlrpc_valid_parents
        //nucleus/では有効なのに、各管理ページ（plugins/znitemfieldex/など）では、空？になっている。バグだよねぇ、これって。
        //管理ページから、doAction経由で別プロセスとして呼び出してもダメだった。
        //ということで、ここで設定してあげると、正常に動作する。グローバル変数で良かったよ。
        global $xmlrpc_valid_parents;
        $xmlrpc_valid_parents = array(
            'BOOLEAN' => array('VALUE'),
            'I4' => array('VALUE'),
            'INT' => array('VALUE'),
            'STRING' => array('VALUE'),
            'DOUBLE' => array('VALUE'),
            'DATETIME.ISO8601' => array('VALUE'),
            'BASE64' => array('VALUE'),
            'ARRAY' => array('VALUE'),
            'STRUCT' => array('VALUE'),
            'PARAM' => array('PARAMS'),
            'METHODNAME' => array('METHODCALL'),        
            'PARAMS' => array('METHODCALL', 'METHODRESPONSE'),
            'MEMBER' => array('STRUCT'),
            'NAME' => array('MEMBER'),
            'DATA' => array('ARRAY'),
            'FAULT' => array('METHODRESPONSE'),
            'VALUE' => array('MEMBER', 'DATA', 'PARAM', 'FAULT'),
        );
        
        global $DIR_LIBS;
        $this->plugName = 'NP_znItemFieldEX';
        if (!class_exists(xmlrpcmsg)) include($DIR_LIBS . "xmlrpc.inc.php");                                      //ファイル読み込み
        $service = new xmlrpc_client('/xmlrpc/verCheckService.php', 'wa.otesei.com', 80);                         //URL設定プロパティセット
        $para    = array(new xmlrpcval($this->plugName, 'string'), new xmlrpcval($this->getVersion(), 'string')); //パラメータ
        //$service->debug = true;
        $res     = $service->send(new xmlrpcmsg('versioncheck.ping', $para), 20);                                 //送信
        if ($res && !$res->faultCode())
        {
            $struct  = $res->value();
            $version = $struct->structmem('version');
            $message = $struct->structmem('message');
            return array('version' => $version->scalarval(), 'message' => $message->scalarval());
        }
        return array('version' => '', 'message' => 'Version Check :: Error');
    }
}
