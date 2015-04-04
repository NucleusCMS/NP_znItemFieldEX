<p>
[ヘルプ一覧]<br />
<ol>
	<li><span style="background-color: #eef; font-weight: bold;">プラグイン概要・設置方法などについて</span></li>
	<li><a href="<?php echo $_GET["url"]; ?>?action=help&p=ex">拡張テーブル・拡張フィールドついて</a></li>
	<li><a href="<?php echo $_GET["url"]; ?>?action=help&p=template">テンプレートへの記述について</a></li>
	<li><a href="<?php echo $_GET["url"]; ?>?action=help&p=skin">スキンへの記述について</a></li>
</ol>
</p>

<h2>プラグイン概要・設置方法などについて</h2>

<div class="t01"><div class="t02"><div class="t03">
<h6>プラグインの概要</h6>
</div></div></div>

<img src="<?php echo $_GET["url"]; ?>znItemFieldEX.gif" align="left" style="margin: 0 8px 3px 0" />
アイテムのフィールド（項目）を拡張するプラグインです。<br />
タイトル、本文、続き、日付、カテゴリ、投稿者などの、Nucleusの標準機能として用意されているアイテムの項目以外に、好きな項目を追加し、拡張することができます。フィールドの拡張は、ブログ単位で行います。（拡張テーブルを作成し、リレーションさせることも可能です。）<br />
追加できるフィールドのタイプは、以下の通りです。<br />
（表1.）<br />
<a name="ftype"></a>
<table style="width: auto;">
	<tr><th>タイプ</th><th>用途</th></tr>
	<tr>
		<td>Text</td>
		<td>一行のテキスト（最大255バイト）</td>
	</tr>
	<tr>
		<td>Number</td>
		<td>数値（-2147483648〜2147483647）</td>
	</tr>
	<tr>
		<td>Textarea</td>
		<td>
			複数行のテキスト<br />
			改行に、&lt;br /&gt;を挿入するかどうかの設定は、「ブログの設定」にならいます。（管理ページで編集する場合、デフォルトブログの設定にならいます。）
		</td>
	</tr>
	<tr>
		<td>Image</td>
		<td>画像（mediaディレクトリ内のファイルを選択して入力します）</td>
	</tr>
	<tr>
		<td>DateTime</td>
		<td>日付・時間</td>
	</tr>
	<tr>
		<td>Checkbox</td>
		<td>複数選択可能な選択肢（決まった文字列を入力するTextタイプ）</td>
	</tr>
	<tr>
		<td>Select</td>
		<td>「拡張した別テーブルの内容」を選択（リレーション機能）<br />
			詳細は、<a href="<?php echo $_GET["url"]; ?>?action=help&p=ex">「拡張テーブル・拡張フィールドついて」</a>を参照ください。
		</td>
	</tr>
</table>

<div class="t01"><div class="t02"><div class="t03">
<h6>設置方法</h6>
</div></div></div>

<ol>
	<li>プラグイン管理ページにて、拡張フィールドを設定<br />
		詳細は、<a href="<?php echo $_GET["url"]; ?>?action=help&p=ex">「拡張テーブル・拡張フィールドついて」</a>を参照ください。<br />
		<br />
	</li>
	<li>プラグイン管理ページにて、拡張テーブルを設定（リレーション機能を使う場合のみ）<br />
		詳細は、<a href="<?php echo $_GET["url"]; ?>?action=help&p=ex">「拡張テーブル・拡張フィールドについて」</a>を参照ください。<br />
		<br />
	</li>
	<li>テンプレート編集<br />
		詳細は、<a href="<?php echo $_GET["url"]; ?>?action=help&p=template">「テンプレートへの記述について」</a>を参照ください。<br />
		<br />
	</li>
	<li>アイテム投稿（追加・編集）にて、各アイテムごとにデータを入力<br />
		<ul>
			<li>znItemFieldEXデータの追加・編集・削除<br />
				znItemFieldEXデータを追加したくないアイテムがある場合、「znItemFieldEXデータを追加しない」にチェックを入れてください。<br />
				すでにznItemFieldEXデータを入力したアイテムから、znItemFieldEXデータを削除する場合、「znItemFieldEXデータを削除」にチェックを入れてください。（既存データの編集・削除のみ、管理ページのレコード編集から行うこともできます。）
			</li>
			<li>Imageタイプフィールドは、Flash、HTMLの2つのインターフェースを用意しました。Flash UIを使用する場合、「Flash Player 8以上」、「JavaScript」が使用できるブラウザが必要です。</li>
			<li>Imageタイプフィールドで使用できる画像フォーマットは、jpg、gif、pngです。</li>
		</ul>
	</li>
</ol>

<div class="t01"><div class="t02"><div class="t03">
<h6>プラグイン管理ページの使い方</h6>
</div></div></div>
phpMyAdmin風な画面構成になっていますが、一部独自の構成になっています。<br />
基本的には、「上部のメニュー」は、全体に関係するもの。「一覧内のアイコン」は、その行に対しての操作。となります。（一部例外あり）<br />
プラグイン管理ページには、主に以下の４つの機能があります。
<ol>
	<li style="background-color: #eef;">テーブル追加・編集・削除</li>
	<li style="background-color: #fee;">各テーブルのフィールド追加・編集・削除</li>
	<li style="background-color: #ffb;">各テーブルのレコード追加・編集・削除</li>
	<li style="background-color: #efe;">ヘルプ</li>
</ol>
ヘルプ以外は、どれも似た画面構成になっているので、各画面でタイトルの色を変えてあります。

<div class="t01"><div class="t02"><div class="t03">
<h6>拡張フィールドAPI</h6>
</div></div></div>
別プラグインなどから、NP_znItemFieldEXで拡張したフィールドを利用するためのAPIです。以下のように、アイテムidとフィールド名を渡すと、そのフィールドの値（表示文字列）を返します。
<blockquote><pre>
if ($manager->pluginInstalled('NP_znItemFieldEX') and 
&nbsp;&nbsp;&nbsp;&nbsp;$plugin = &$manager-&gt;getPlugin('NP_znItemFieldEX')) {
&nbsp;&nbsp;&nbsp;&nbsp;echo <span style="color: #090">$plugin->getItemFieldEX($itemid,"products-&gt;material-&gt;supplier");</span>
}
</pre></blockquote>

<div class="t01"><div class="t02"><div class="t03">
<h6>オプション</h6>
</div></div></div>

<p>プラグインオプション</p>
<table>
<tr>
	<th>オプション</th>
	<th>解説</th>
</tr>
<tr>
	<td>検索対象となるフィールドを記述したテンプレート</td>
	<td>
		指定したテンプレートの「アイテムの本体」で使用されているフィールドが検索対象となります。また、このテンプレートの「ハイライト表示」を検索結果に使用します。
	</td>
</tr>
<tr>
	<td>管理ページへのリンクを、クイックメニューに表示しますか？</td>
	<td>
		Nucleus Admin のサイドバーのクイックメニューに、管理ページへのリンクを表示するかどうかの設定です。
	</td>
</tr>
<tr>
	<td>アンインストール時、データを破棄しますか？</td>
	<td>
		プラグインをアンインストールした際に、フィールドデータ、アイテムデータを破棄するかどうかの設定です。
	</td>
</tr>
</table>

<div class="t01"><div class="t02"><div class="t03">
<h6>検索について</h6>
</div></div></div>
プラグインオプションで指定したテンプレートの「アイテムの本体」で使用しているフィールドが検索対象となります。（ただし、Imageタイプフィールドと、Selectタイプフィールド※は検索対象となりません。）<br />
※･･･もちろん、リレーション先も検索対象となります。ここで言うSelectタイプフィールドとは、「表示するフィールドタイプがSelectタイプフィールドの場合」です。<br />
<br />
NP_znItemFieldEXで拡張したフィールドを、検索の対象にするためには、Andy氏作成のNP_ExtensibleSearchが必要です。<br />
<a href="http://japan.nucleuscms.org/bb/viewtopic.php?p=10030">Nucleus(JP)フォーラム - 検索機能をプラグインで拡張可能に</a><br />
素晴らしいプラグインをありがとうございます。<br />

<div class="t01"><div class="t02"><div class="t03">
<h6>今後のバージョンアップ予定（TODO代わりに列挙してますので、意味不明なものも。）</h6>
</div></div></div>
大分片付きました。
<ul>
	<li>管理ページ関連
		<ul>
			<li>テンプレート等のフィールド名が不正だと、PHPがエラーを吐くのを何とかする</li>
		</ul>
	</li>
	<li>テンプレート関連
		<ul>
			<li>画像サイズ指定（これは是非！）</li>
			<li>数値タイプフィールドの表示形式（10,000など）テンプレートのパラメータで渡す？フィールドの設定で指定する？</li>
			<li>数値タイプフィールドの前後のデータとの比較</li>
		</ul>
	</li>
	<li>ブログ閲覧関連
		<ul>
			<li>ブログ上での、検索機能（Numberタイプで、指定値以下・以上。DateTimeタイプで、指定日以前・以降のような条件指定）</li>
			<li>グラフ出力</li>
		</ul>
	</li>
</ul>
<p>
	ご意見、ご要望などがございましたら、（対応できるかどうかは分かりませんが）お気軽にご連絡下さい。バグレポートなどもいただけるとうれしいです。<br />
	【連絡先】<br />
	<ul>
		<li>メールアドレス：satona@gmail.com</li>
		<li>Nucleus(JP)フォーラム：アカウント名、佐藤（な）</li>
		<li>mixi：名前、佐藤（な）</li>
		<li>もちろんブログへのコメントも大歓迎です。：-）</li>
	</ul>
</p>
ご意見をくださった皆様、ありがとうございます。（感謝！）
<br />