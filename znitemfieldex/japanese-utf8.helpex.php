<p>
[ヘルプ一覧]<br />
<ol>
	<li><a href="<?php echo $url; ?>">プラグイン概要・設置方法などについて</a></li>
	<li><span style="background-color: #eef; font-weight: bold;">拡張テーブル・拡張フィールドについて</span></li>
	<li><a href="<?php echo $url; ?>&p=template">テンプレートへの記述について</a></li>
	<li><a href="<?php echo $url; ?>&p=skin">スキンへの記述について</a></li>
</ol>
</p>

<h2>拡張テーブル・拡張フィールドについて</h2>

<div class="t01"><div class="t02"><div class="t03">
<h3>テーブルについて</h3>
</div></div></div>
<ol>
	<li>ブログごとに「アイテム拡張用テーブル」が存在します。<br />
		アイテムの拡張フィールドは、ブログ単位の「アイテム拡張用テーブル」に保存されます。<br />
		「アイテム拡張用テーブル」は、znItemFieldEXをインストールした時に自動的に作成され、ブログを削除した時に自動的に削除されます。<br />
		また、プラグインオプションが、「アンインストール時データを破棄する設定」になっている場合、znItemFieldEXをアンインストールした時に、削除されます。<br />
		<br />
	</li>
	<li>Selectタイプフィールド（リレーション）を使用しない場合、新たに拡張テーブルを作成する必要はありません。<br />
		アイテム拡張用以外の拡張テーブルは、Selectタイプフィールドに結合させるために使用します。（アイテム拡張用テーブルは、Nucleusのアイテムに結合しているイメージです。）<br />
		<br />
		<a name="ssetting"></a>
	</li>
	<li>リレーション用テーブルとして使用するためには、Textタイプフィールドが必要です。<br />
		実際には、リレーション用テーブルの「id」フィールドを結合に使用するのですが、入力する際に、idではその内容を把握し難いため、ラベル代わりにTextフィールドを使用します。<br />
		具体例）<br />

		<div style="font-size: 12px; font-family: 'ＭＳ ゴシック'; line-height: 100%; letter-spacing: 0">
			<span style="color: #090;">
			┌─┬────────┐<br />
			│id│products(Select)│アイテム拡張用テーブル（以下Ａ）<br />
			├─┼────────┤<br />
			</span>
			　　　│　　│<br />
			　┌─┘　　│productsフィールドの設定で指定<br />
			　↓結合　　↓<br />
			<span style="color: #900;">
			┌─┬──────┬─────┬───────┐<br />
			│id│pname(Text) │pic(Image)│price(Number) │リレーション用テーブル（以下Ｂ）<br />
			├─┼──────┼─────┼───────┤<br />
			</span>
			<br />
		</div>

		Ａのproductsフィールド（Selectタイプ）の設定で、Ｂのpnameフィールド（Textタイプ）を登録しています。<br />
		Ａのproductsフィールドのレコードには、Ｂのidフィールドが保存されます。（実際にはidに結合している。）<br />
		Ａにデータを入力する際に、id（数値）が表示されても、Ｂのどのレコードに対応しているのか把握し難いため、Ｂのpneme（Textタイプ）を表示するようにしています。そのために、リレーション用テーブルにはTextタイプフィールドが必要になります。<br />
		<br />
	</li>
</ol>

<div class="t01"><div class="t02"><div class="t03">
<h3>フィールドについて</h3>
</div></div></div>
<ol>
	<li>フィールドには、複数のタイプがあります。<br />
		フィールドを追加したら、<img src="<?php echo $plugin_dir; ?>images/edit.gif" title="編集" />（編集）から、フィールドの設定を行ってください。設定内容は以下の通りです。<br />
		（表2.）<br />
		<a name="ftype"></a>
		<table style="width: auto;">
			<tr><th>タイプ</th><th>用途</th><th>設定内容</th></tr>
			<tr>
				<td>Text</td>
				<td>一行のテキスト（最大255バイト）</td>
				<td>追加・変更フォームで表示される時の横幅</td>
			</tr>
			<tr>
				<td>Number</td>
				<td>数値（-2147483648〜2147483647）</td>
				<td>追加・変更フォームで表示される時の横幅</td>
			</tr>
			<tr>
				<td>Textarea</td>
				<td>複数行のテキスト</td>
				<td>追加・変更フォームで表示される時の横幅と行数</td>
			</tr>
			<tr>
				<td>Image</td>
				<td>画像（255バイトまでの、mediaディレクトリからの相対パスを保持）</td>
				<td>
					デフォルトのmediaディレクトリ（Flash版UIの場合、投稿ページで、ディレクトリを変更できます。）<br />
					投稿ページでの、ユーザインターフェース（Flash版、HTML版のいずれか）を設定しておきます。画像枚数が多い場合、ジャンル別にディレクトリを分けるか、HTML版をお使いください。
				</td>
			</tr>
			<tr>
				<td>DateTime</td>
				<td>日付・時間</td>
				<td>2005-12-25形式、2005-12-25 23:59:59形式の何れか</td>
			</tr>
			<tr>
				<td>Checkbox</td>
				<td>複数選択可能な選択肢（決まった文字列を入力するTextタイプ）</td>
				<td>選択肢を、改行で区切って設定（現バージョンでは、すでにアイテムに入力している文字列を変更・削除しても、アイテムデータは補正されません。）</td>
			</tr>
			<tr>
				<td>Select</td>
				<td>別テーブルのTextタイプフィールドを選択</td>
				<td>Textタイプフィールドを持つテーブルが対象となります。（idでは意味が判別し難いので、Textタイプフィールドをラベル代わりに使用するためです。実際には指定したテーブルの「id」フィールドに結合します。）</td>
			</tr>
		</table>
		<br />
	</li>
	<li>各テーブルには、必ず「id」フィールドが存在します。<br />
		「id」フィールドは、他のデータに結合するために使用しています。（「アイテム拡張用テーブル」の場合、Nucleusのアイテムと結合するために使用）この「id」フィールドは、編集・削除することができません。また、「id」というフィールドを、あらたに追加することもできません。<br />
		<br />
	</li>
	<li>別テーブルから結合先に設定されているTextタイプフィールドには、フィールド一覧のタイプ項目に、下記鎖マークが付き、結合元フィールド編集ページへのリンクが表示されます。<br />
		<img src="<?php echo $plugin_dir; ?>images/help_link_orig.gif" />
	</li>
	<li>フィールド一覧の、Selectタイプフィールドの設定項目には、結合先フィールド編集ページへのリンクが表示されます。<br />
		<img src="<?php echo $plugin_dir; ?>images/help_link_to.gif" />
	</li>
	<li>フィールド一覧の、フィールド記述項目には、テンプレートやスキンで指定する際の（-&gt;で繋がった）フィールド名が自動生成されます。「アイテム拡張用テーブル」と繋がっていない場合、頭に「リンク切れアイコン」が付きます。<br />
		<img src="<?php echo $plugin_dir; ?>images/help_relation.gif" />
	</li>
</ol>

<div class="t01"><div class="t02"><div class="t03">
<h3>レコードについて</h3>
</div></div></div>
<ol>
	<li>リレーションを設定しているフィールド（Selectタイプ）には、<img src="<?php echo $plugin_dir; ?>images/link.gif" align="top" />が付きます。<br />
		<ul>
		<li>一覧のタイトル部分の<img src="<?php echo $plugin_dir; ?>images/link.gif" align="top" />をクリックすると、リレーション先のフィールドの編集画面に飛びます。</li>
		<li>設定していたリレーション先が、変更・削除されてしまった場合、<img src="<?php echo $plugin_dir; ?>images/link_dele.gif" align="top" />が表示されます。</li>
		<li>レコードには、リレーション先のTextタイプフィールドのデータが表示されます。</li>
		<li>リレーション先にデータがないレコードには、<img src="<?php echo $plugin_dir; ?>images/link.gif" align="top" />のみが表示されます。</li>
		</ul>
		<br />
	</li>
	<li>プラグイン管理ページでは、アイテム拡張用テーブルへのレコード追加はできません。<br />
		既存レコードの編集・削除は可能ですが、アイテム拡張用テーブルへのレコード追加は、アイテム投稿から行います。
	</li>
</ol>

<br />