<p>
[ヘルプ一覧]<br />
<ol>
	<li><a href="<?php echo $url; ?>">プラグイン概要・設置方法などについて</a></li>
	<li><a href="<?php echo $url; ?>&p=ex">拡張テーブル・拡張フィールドついて</a></li>
	<li><a href="<?php echo $url; ?>&p=template">テンプレートへの記述について</a></li>
	<li><span style="background-color: #eef; font-weight: bold;">スキンへの記述について</span></li>
</ol>
</p>

<h2>スキンへの記述について</h2>

<div class="t01"><div class="t02"><div class="t03">
<h3>スキンへの記述</h3>
</div></div></div>
スキンでは、以下の2つの機能があります。
<ol>
	<li>アイテムスキンで、指定した拡張フィールドの値を表示します。</li>
	<li>&lt;%blog%&gt;のような、アイテム群を表示します。「ソートキー」、「摘出条件」を指定することができます。</li>
</ol>
<br />

<div class="t01"><div class="t02"><div class="t03">
<h3>アイテムスキンで、指定した拡張フィールドの値を表示する方法</h3>
</div></div></div>
<blockquote><pre>
<%znItemFieldEX(モード,拡張フィールド)%>
</pre></blockquote>
<table style="width: auto;">
	<tr><th>パラメータ</th><th>解説</th></tr>
	<tr>
		<td>モード</td>
		<td>アイテムスキンで使用する場合、item</td>
	</tr>
	<tr>
		<td>拡張フィールド</td>
		<td>テンプレートでの指定方法と同じです。リレーションを使用することもできます。</td>
	</tr>
</table>
具体的には、以下のようになります。
<blockquote><pre>
<%znItemFieldEX(item,products->material->supplier)%>
</pre></blockquote>
アイテムスキン以外で使用した場合、非表示となります。<br />
<br />

<div class="t01"><div class="t02"><div class="t03">
<h3>アイテム群の表示方法</h3>
</div></div></div>
基本的に、サイドバーのような場所で使うことを想定した、&lt;%blog%&gt;系のアイテム群を表示します。この時、<span style="color: #090;">表示する順番と、摘出条件を、スキン変数のパラメータで、設定できます。</span><br />
普通は、「Nucleus標準のアイテム項目の日付」で決まる順番や、"ドラフトと未来の日付以外"等々によって決まる摘出条件を、自由に設定できるようにすることで、拡張したフィールドに、意味を持たせることができるようになります。<br />
<br />
以下のような使い方が可能です。
<ul>
	<li>「Selectタイプで"オススメ"にしたアイテム」を摘出条件として、「Dateタイプの発売日」を基準にソートした10件</li>
	<li>ルービックキューブの計測時間を、Numberタイプに記録して、「最速記録10件」</li>
	<li>商品-&gt;材質-&gt;単価が、1000円以上とか、複雑な摘出条件も、設定可能</li>
</ul>
INDEX表示するテンプレートを、専用で作っておけば、メインの記事とは違ったレイアウトで表示できます。 そこから、&lt;%itemlink%&gt;を、クリックすれば、個別アイテムページに飛ぶようにしておくと便利です。<br />
<br />
<blockquote><pre>
<%znItemFieldEX(モード,ソートキー,昇降順,摘出条件,テンプレート,表示数)%>
</pre></blockquote>
<table style="width: auto;">
	<tr><th>パラメータ</th><th>解説</th></tr>
	<tr>
		<td>モード</td>
		<td>アイテム群表示の場合、index</td>
	</tr>
	<tr>
		<td>ソートキー</td>
		<td>並順の基準となるフィールドです。</td>
	</tr>
	<tr>
		<td>昇降順</td>
		<td>昇順がASC、降順がDESC、となります</td>
	</tr>
	<tr>
		<td>摘出条件</td>
		<td>フィールドと、比較演算子と、値を、|で区切って指定します。<br />演算子は、&gt;、&gt;=、=、!=、&lt;=、&lt;、%、が使用可能です。%は、LIKE '%値%'となります。</td>
	</tr>
	<tr>
		<td>テンプレート</td>
		<td>使用するテンプレートです。専用でテンプレートを作成することで、レイアウトを自由にカスタマイズすることが可能です。</td>
	</tr>
	<tr>
		<td>表示数</td>
		<td>最大表示数です。</td>
	</tr>
</table>
具体的には、以下のようになります。
<blockquote><pre>
<%znItemFieldEX(index,products->material->mname,ASC,products->material->mname|%|あいう,default/index,30)%>
</pre></blockquote>
<br />