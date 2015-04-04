<p>
[ヘルプ一覧]<br />
<ol>
	<li><a href="<?php echo $_GET["url"]; ?>?action=help">プラグイン概要・設置方法などについて</a></li>
	<li><a href="<?php echo $_GET["url"]; ?>?action=help&p=ex">拡張テーブル・拡張フィールドついて</a></li>
	<li><span style="background-color: #eef; font-weight: bold;">テンプレートへの記述について</span></li>
	<li><a href="<?php echo $_GET["url"]; ?>?action=help&p=skin">スキンへの記述について</a></li>
</ol>
</p>

<h2>テンプレートへの記述について</h2>

<div class="t01"><div class="t02"><div class="t03">
<h6>テンプレートへの記述</h6>
</div></div></div>
「アイテムの本体」に、<br />
<blockquote><pre>&lt;%znItemFieldEX(フィールド名)%&gt;</pre></blockquote>
と、記述します。<br />
フィールド名は、プラグイン管理ページで、「アイテム拡張用テーブル」に設定したものです。（半角英数字及び_）<br />
各フィールドタイプには、以下のような特徴があります。
<table>
	<tr><th>フィールドタイプ</th><th>解説</th></tr>
	<tr>
		<td>Checkbox</td>
		<td>&lt;li&gt;&lt;/li&gt;のみを出力しますので、お好みで、&lt;ul&gt;や、&lt;ol&gt;を付け加えてください。</td>
	</tr>
	<tr>
		<td>Image</td>
		<td>
			&lt;img&gt;タグに、class属性が自動的に振られます。<br />
			値は、"zmifex_フィールド名"です。スタイルシートでカスタマイズしてください。<br />
			リレーションを指定した場合、"-&gt;"部分が"__"に置き換わります。<br />
			例）<br />
			products->material->mname<br />
			↑ "zmifex_products__material__mname" というclassが付きます。
		</td>
	</tr>
	<tr>
		<td>Select</td>
		<td>
			リレーションするフィールド名は、"-&gt;"でつなぎます。詳細は、下記の「リレーションの指定方法」を参照ください。
		</td>
	</tr>
	<tr>
		<td>その他</td>
		<td>データがそのまま表示されます。</td>
	</tr>
</table>
色、大きさなどは、スタイルシートで指定してください。
<br />

<div class="t01"><div class="t02"><div class="t03">
<h6>リレーションの指定方法</h6>
</div></div></div>
<p>
	『Selectタイプフィールドに設定（※1）した、結合先テーブル』の、『任意のフィールド（※2）のデータ』を表示することができます。<br />
	※1 ･･･ 管理ページで設定します。<br />
	※2 ･･･ テンプレートで設定します。<br />
	<span style="color: #090;">ver0.05alphaから、下記のリレーションフィールドの記述は、フィールド一覧のフィールド記述欄に、自動生成されるようになりました。</span>
</p>
<p>
	例１）
	<div style="padding: 5px 0 20px 20px;">
		<div style="font-size: 12px; font-family: 'ＭＳ ゴシック'; line-height: 100%; letter-spacing: 0">
			<span style="color: #090;">
			┌─┬────────┐<br />
			│id│products(Select)│「アイテム拡張用」テーブル<br />
			├─┼────────┤<br />
			</span>
			　　　│　　│<br />
			　┌─┘　　│productsフィールドの設定（管理ページ）で指定<br />
			　↓結合　　↓<br />
			<span style="color: #900;">
			┌─┬──────┬─────┬───────┬───────┬────────┐<br />
			│id│pname(Text) │pic(Image)│desc(Textarea)│price(Number) │material(Select)│「商品」テーブル<br />
			├─┼──────┼─────┼───────┼───────┼────────┤<br />
			</span>
			<br />
		</div>
		といったテーブルとフィールドがある時、<br />
		<br />
		「商品」テーブルの、「pname」フィールドのデータを表示する場合
		<blockquote><pre>&lt;%znItemFieldEX(products-&gt;pname)%&gt;</pre></blockquote>
		<br />
		「商品」テーブルの、「pic」フィールドの画像を表示する場合
		<blockquote><pre>&lt;%znItemFieldEX(products-&gt;pic)%&gt;</pre></blockquote>
		のような記述になります。<br />
	</div>
</p>
<p>
	また、複数の"-&gt;"で、1つ以上のテーブルとリレーションさせることも可能です。<br />
	例２）
	<div style="padding: 5px 0 20px 20px;">
		<div style="font-size: 12px; font-family: 'ＭＳ ゴシック'; line-height: 100%; letter-spacing: 0">
			上記、例１に加え、更に<br />
			<br />
			<span style="color: #900;">
			─┬────────┐<br />
			　│material(Select)│「商品」テーブル<br />
			─┼────────┤<br />
			</span>
			　　　│　　│<br />
			　┌─┘　　│materialフィールドの設定（管理ページ）で指定<br />
			　↓結合　　↓<br />
			<span style="color: #009;">
			┌─┬──────┬───────┐<br />
			│id│mname(Text) │desc(Textarea)│「材質」テーブル<br />
			├─┼──────┼───────┤<br />
			</span>
			<br />
		</div>
		といったテーブルとフィールドがある時、<br />
		<br />
		「商品」テーブルの、「material」フィールドに設定している「材質」テーブルの「mname」フィールドを表示する場合
		<blockquote><pre>&lt;%znItemFieldEX(products-&gt;material-&gt;mname)%&gt;</pre></blockquote>
		<br />
		「商品」テーブルの、「material」フィールドに設定している「材質」テーブルの「desc」フィールドを表示する場合
		<blockquote><pre>&lt;%znItemFieldEX(products-&gt;material-&gt;desc)%&gt;</pre></blockquote>
		のような記述になります。<br />
		あまり使い道はないかと思いますが、構造上は、複数の"-&gt;"でつなげることにより、いくつものテーブルをつなげることが可能です。<br />
	</div>
</p>
<p>
	もう一度整理すると、<br />
	<blockquote>&lt;%znItemFieldEX(<span style="color: #f00;font-weight: bold;">products</span>-&gt;<span style="color: #090;font-weight: bold;">pic</span>)%&gt;</blockquote>
	↑　『<span style="background-color: #fee;">アイテム拡張用テーブルの<span style="color: #f00;font-weight: bold;">products</span>フィールド</span>に（管理ページで）設定してある<span style="background-color: #efe;">商品テーブルの、<span style="color: #090;font-weight: bold;">pic</span>フィールド</span>』となり、<br />
	<blockquote>&lt;%znItemFieldEX(<span style="color: #f00;font-weight: bold;">products</span>-&gt;<span style="color: #090;font-weight: bold;">material</span>-&gt;<span style="color: #00f;font-weight: bold;">desc</span>)%&gt;</blockquote>
	↑　『<span style="background-color: #fee;">アイテム拡張用テーブルの<span style="color: #f00;font-weight: bold;">products</span>フィールド</span>に（管理ページで）設定してある<span style="background-color: #efe;">商品テーブルの、<span style="color: #090;font-weight: bold;">material</span>フィールド</span>に（管理ページで）設定してある<span style="background-color: #eef;">材質テーブルの、<span style="color: #00f;font-weight: bold;">desc</span>フィールド</span>』となります。<br />
</p>

<div class="t01"><div class="t02"><div class="t03">
<h6>リレーションSQLの生成</h6>
</div></div></div>
テンプレートに記述したリレーション構造（aaa-&gt;bbb-&gt;ccc-&gt;ddd）で、動的にSQLを生成しているのですが、アイテムが表示されるたびに動的生成していたのでは、無駄なオーバーヘッドが発生してしまいます。そこで、一度生成したSQLは、キャッシュして使いまわしています。キャッシュは、何らかのフィールド編集をした時にクリアされます。
<br />