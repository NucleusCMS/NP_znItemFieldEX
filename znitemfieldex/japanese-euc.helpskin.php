<?php $url = str_replace('<','',$_GET["url"]);?>
<p>
[�إ�װ���]<br />
<ol>
	<li><a href="<?php echo $url; ?>?action=help">�ץ饰�����ס�������ˡ�ʤɤˤĤ���</a></li>
	<li><a href="<?php echo $url; ?>?action=help&p=ex">��ĥ�ơ��֥롦��ĥ�ե�����ɤĤ���</a></li>
	<li><a href="<?php echo $url; ?>?action=help&p=template">�ƥ�ץ졼�Ȥؤε��ҤˤĤ���</a></li>
	<li><span style="background-color: #eef; font-weight: bold;">������ؤε��ҤˤĤ���</span></li>
</ol>
</p>

<h2>������ؤε��ҤˤĤ���</h2>

<div class="t01"><div class="t02"><div class="t03">
<h6>������ؤε���</h6>
</div></div></div>
������Ǥϡ��ʲ���2�Ĥε�ǽ������ޤ���
<ol>
	<li>�����ƥॹ����ǡ����ꤷ����ĥ�ե�����ɤ��ͤ�ɽ�����ޤ���</li>
	<li>&lt;%blog%&gt;�Τ褦�ʡ������ƥෲ��ɽ�����ޤ����֥����ȥ����ס���Ŧ�о��פ���ꤹ�뤳�Ȥ��Ǥ��ޤ���</li>
</ol>
<br />

<div class="t01"><div class="t02"><div class="t03">
<h6>�����ƥॹ����ǡ����ꤷ����ĥ�ե�����ɤ��ͤ�ɽ��������ˡ</h6>
</div></div></div>
<blockquote><pre>
<%znItemFieldEX(�⡼��,��ĥ�ե������)%>
</pre></blockquote>
<table style="width: auto;">
	<tr><th>�ѥ�᡼��</th><th>����</th></tr>
	<tr>
		<td>�⡼��</td>
		<td>�����ƥॹ����ǻ��Ѥ����硢item</td>
	</tr>
	<tr>
		<td>��ĥ�ե������</td>
		<td>�ƥ�ץ졼�ȤǤλ�����ˡ��Ʊ���Ǥ�����졼��������Ѥ��뤳�Ȥ�Ǥ��ޤ���</td>
	</tr>
</table>
����Ū�ˤϡ��ʲ��Τ褦�ˤʤ�ޤ���
<blockquote><pre>
<%znItemFieldEX(item,products->material->supplier)%>
</pre></blockquote>
�����ƥॹ����ʳ��ǻ��Ѥ�����硢��ɽ���Ȥʤ�ޤ���<br />
<br />

<div class="t01"><div class="t02"><div class="t03">
<h6>�����ƥෲ��ɽ����ˡ</h6>
</div></div></div>
����Ū�ˡ������ɥС��Τ褦�ʾ��ǻȤ����Ȥ����ꤷ����&lt;%blog%&gt;�ϤΥ����ƥෲ��ɽ�����ޤ������λ���<span style="color: #090;">ɽ��������֤ȡ�Ŧ�о��򡢥������ѿ��Υѥ�᡼���ǡ�����Ǥ��ޤ���</span><br />
���̤ϡ���Nucleusɸ��Υ����ƥ���ܤ����աפǷ�ޤ���֤䡢"�ɥ�եȤ�̤������հʳ�"�����ˤ�äƷ�ޤ�Ŧ�о��򡢼�ͳ������Ǥ���褦�ˤ��뤳�Ȥǡ���ĥ�����ե�����ɤˡ���̣��������뤳�Ȥ��Ǥ���褦�ˤʤ�ޤ���<br />
<br />
�ʲ��Τ褦�ʻȤ�������ǽ�Ǥ���
<ul>
	<li>��Select�����פ�"��������"�ˤ��������ƥ�פ�Ŧ�о��Ȥ��ơ���Date�����פ�ȯ�����פ���˥����Ȥ���10��</li>
	<li>�롼�ӥå����塼�֤η�¬���֤�Number�����פ˵�Ͽ���ơ��ֺ�®��Ͽ10���</li>
	<li>����-&gt;���-&gt;ñ������1000�߰ʾ�Ȥ���ʣ����Ŧ�о��⡢�����ǽ</li>
</ul>
INDEXɽ������ƥ�ץ졼�Ȥ����ѤǺ�äƤ����С��ᥤ��ε����Ȥϰ�ä��쥤�����Ȥ�ɽ���Ǥ��ޤ��� �������顢&lt;%itemlink%&gt;�򡢥���å�����С����̥����ƥ�ڡ��������֤褦�ˤ��Ƥ����������Ǥ���<br />
<br />
<blockquote><pre>
<%znItemFieldEX(�⡼��,�����ȥ���,���߽�,Ŧ�о��,�ƥ�ץ졼��,ɽ����)%>
</pre></blockquote>
<table style="width: auto;">
	<tr><th>�ѥ�᡼��</th><th>����</th></tr>
	<tr>
		<td>�⡼��</td>
		<td>�����ƥෲɽ���ξ�硢index</td>
	</tr>
	<tr>
		<td>�����ȥ���</td>
		<td>�½�δ��Ȥʤ�ե�����ɤǤ���</td>
	</tr>
	<tr>
		<td>���߽�</td>
		<td>���礬ASC���߽礬DESC���Ȥʤ�ޤ�</td>
	</tr>
	<tr>
		<td>Ŧ�о��</td>
		<td>�ե�����ɤȡ���ӱ黻�Ҥȡ��ͤ�|�Ƕ��ڤäƻ��ꤷ�ޤ���<br />�黻�Ҥϡ�&gt;��&gt;=��=��!=��&lt;=��&lt;��%�������Ѳ�ǽ�Ǥ���%�ϡ�LIKE '%��%'�Ȥʤ�ޤ���</td>
	</tr>
	<tr>
		<td>�ƥ�ץ졼��</td>
		<td>���Ѥ���ƥ�ץ졼�ȤǤ������Ѥǥƥ�ץ졼�Ȥ�������뤳�Ȥǡ��쥤�����Ȥ�ͳ�˥������ޥ������뤳�Ȥ���ǽ�Ǥ���</td>
	</tr>
	<tr>
		<td>ɽ����</td>
		<td>����ɽ�����Ǥ���</td>
	</tr>
</table>
����Ū�ˤϡ��ʲ��Τ褦�ˤʤ�ޤ���
<blockquote><pre>
<%znItemFieldEX(index,products->material->mname,ASC,products->material->mname|%|������,default/index,30)%>
</pre></blockquote>
<br />