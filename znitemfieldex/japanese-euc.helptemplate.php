<p>
[�إ�װ���]<br />
<ol>
	<li><a href="<?php echo $_GET["url"]; ?>?action=help">�ץ饰�����ס�������ˡ�ʤɤˤĤ���</a></li>
	<li><a href="<?php echo $_GET["url"]; ?>?action=help&p=ex">��ĥ�ơ��֥롦��ĥ�ե�����ɤĤ���</a></li>
	<li><span style="background-color: #eef; font-weight: bold;">�ƥ�ץ졼�Ȥؤε��ҤˤĤ���</span></li>
	<li><a href="<?php echo $_GET["url"]; ?>?action=help&p=skin">������ؤε��ҤˤĤ���</a></li>
</ol>
</p>

<h2>�ƥ�ץ졼�Ȥؤε��ҤˤĤ���</h2>

<div class="t01"><div class="t02"><div class="t03">
<h6>�ƥ�ץ졼�Ȥؤε���</h6>
</div></div></div>
�֥����ƥ�����Ρפˡ�<br />
<blockquote><pre>&lt;%znItemFieldEX(�ե������̾)%&gt;</pre></blockquote>
�ȡ����Ҥ��ޤ���<br />
�ե������̾�ϡ��ץ饰��������ڡ����ǡ��֥����ƥ��ĥ�ѥơ��֥�פ����ꤷ����ΤǤ�����Ⱦ�ѱѿ����ڤ�_��<br />
�ƥե�����ɥ����פˤϡ��ʲ��Τ褦����ħ������ޤ���
<table>
	<tr><th>�ե�����ɥ�����</th><th>����</th></tr>
	<tr>
		<td>Checkbox</td>
		<td>&lt;li&gt;&lt;/li&gt;�Τߤ���Ϥ��ޤ��Τǡ������ߤǡ�&lt;ul&gt;�䡢&lt;ol&gt;���դ��ä��Ƥ���������</td>
	</tr>
	<tr>
		<td>Image</td>
		<td>
			&lt;img&gt;�����ˡ�class°������ưŪ�˿����ޤ���<br />
			�ͤϡ�"zmifex_�ե������̾"�Ǥ����������륷���Ȥǥ������ޥ������Ƥ���������<br />
			��졼��������ꤷ����硢"-&gt;"��ʬ��"__"���֤������ޤ���<br />
			���<br />
			products->material->mname<br />
			�� "zmifex_products__material__mname" �Ȥ���class���դ��ޤ���
		</td>
	</tr>
	<tr>
		<td>Select</td>
		<td>
			��졼����󤹤�ե������̾�ϡ�"-&gt;"�ǤĤʤ��ޤ����ܺ٤ϡ������Ρ֥�졼�����λ�����ˡ�פ򻲾Ȥ���������
		</td>
	</tr>
	<tr>
		<td>����¾</td>
		<td>�ǡ��������Τޤ�ɽ������ޤ���</td>
	</tr>
</table>
�����礭���ʤɤϡ��������륷���Ȥǻ��ꤷ�Ƥ���������
<br />

<div class="t01"><div class="t02"><div class="t03">
<h6>��졼�����λ�����ˡ</h6>
</div></div></div>
<p>
	��Select�����ץե�����ɤ�����ʢ�1�ˤ����������ơ��֥�٤Ρ���Ǥ�դΥե�����ɡʢ�2�ˤΥǡ����٤�ɽ�����뤳�Ȥ��Ǥ��ޤ���<br />
	��1 ������ �����ڡ��������ꤷ�ޤ���<br />
	��2 ������ �ƥ�ץ졼�Ȥ����ꤷ�ޤ���<br />
	<span style="color: #090;">ver0.05alpha���顢�����Υ�졼�����ե�����ɤε��Ҥϡ��ե�����ɰ����Υե�����ɵ�����ˡ���ư���������褦�ˤʤ�ޤ�����</span>
</p>
<p>
	�㣱��
	<div style="padding: 5px 0 20px 20px;">
		<div style="font-size: 12px; font-family: '�ͣ� �����å�'; line-height: 100%; letter-spacing: 0">
			<span style="color: #090;">
			������������������������<br />
			��id��products(Select)���֥����ƥ��ĥ�ѡץơ��֥�<br />
			������������������������<br />
			</span>
			��������������<br />
			��������������products�ե�����ɤ�����ʴ����ڡ����ˤǻ���<br />
			������硡����<br />
			<span style="color: #900;">
			����������������������������������������������������������������������������������<br />
			��id��pname(Text) ��pic(Image)��desc(Textarea)��price(Number) ��material(Select)���־��ʡץơ��֥�<br />
			����������������������������������������������������������������������������������<br />
			</span>
			<br />
		</div>
		�Ȥ��ä��ơ��֥�ȥե�����ɤ��������<br />
		<br />
		�־��ʡץơ��֥�Ρ���pname�ץե�����ɤΥǡ�����ɽ��������
		<blockquote><pre>&lt;%znItemFieldEX(products-&gt;pname)%&gt;</pre></blockquote>
		<br />
		�־��ʡץơ��֥�Ρ���pic�ץե�����ɤβ�����ɽ��������
		<blockquote><pre>&lt;%znItemFieldEX(products-&gt;pic)%&gt;</pre></blockquote>
		�Τ褦�ʵ��Ҥˤʤ�ޤ���<br />
	</div>
</p>
<p>
	�ޤ���ʣ����"-&gt;"�ǡ�1�İʾ�Υơ��֥�ȥ�졼����󤵤��뤳�Ȥ��ǽ�Ǥ���<br />
	�㣲��
	<div style="padding: 5px 0 20px 20px;">
		<div style="font-size: 12px; font-family: '�ͣ� �����å�'; line-height: 100%; letter-spacing: 0">
			�嵭���㣱�˲ä�������<br />
			<br />
			<span style="color: #900;">
			����������������������<br />
			����material(Select)���־��ʡץơ��֥�<br />
			����������������������<br />
			</span>
			��������������<br />
			��������������material�ե�����ɤ�����ʴ����ڡ����ˤǻ���<br />
			������硡����<br />
			<span style="color: #009;">
			������������������������������������<br />
			��id��mname(Text) ��desc(Textarea)���ֺ���ץơ��֥�<br />
			������������������������������������<br />
			</span>
			<br />
		</div>
		�Ȥ��ä��ơ��֥�ȥե�����ɤ��������<br />
		<br />
		�־��ʡץơ��֥�Ρ���material�ץե�����ɤ����ꤷ�Ƥ���ֺ���ץơ��֥�Ρ�mname�ץե�����ɤ�ɽ��������
		<blockquote><pre>&lt;%znItemFieldEX(products-&gt;material-&gt;mname)%&gt;</pre></blockquote>
		<br />
		�־��ʡץơ��֥�Ρ���material�ץե�����ɤ����ꤷ�Ƥ���ֺ���ץơ��֥�Ρ�desc�ץե�����ɤ�ɽ��������
		<blockquote><pre>&lt;%znItemFieldEX(products-&gt;material-&gt;desc)%&gt;</pre></blockquote>
		�Τ褦�ʵ��Ҥˤʤ�ޤ���<br />
		���ޤ�Ȥ�ƻ�Ϥʤ����Ȼפ��ޤ�������¤��ϡ�ʣ����"-&gt;"�ǤĤʤ��뤳�Ȥˤ�ꡢ�����Ĥ�Υơ��֥��Ĥʤ��뤳�Ȥ���ǽ�Ǥ���<br />
	</div>
</p>
<p>
	�⤦������������ȡ�<br />
	<blockquote>&lt;%znItemFieldEX(<span style="color: #f00;font-weight: bold;">products</span>-&gt;<span style="color: #090;font-weight: bold;">pic</span>)%&gt;</blockquote>
	������<span style="background-color: #fee;">�����ƥ��ĥ�ѥơ��֥��<span style="color: #f00;font-weight: bold;">products</span>�ե������</span>�ˡʴ����ڡ����ǡ����ꤷ�Ƥ���<span style="background-color: #efe;">���ʥơ��֥�Ρ�<span style="color: #090;font-weight: bold;">pic</span>�ե������</span>�٤Ȥʤꡢ<br />
	<blockquote>&lt;%znItemFieldEX(<span style="color: #f00;font-weight: bold;">products</span>-&gt;<span style="color: #090;font-weight: bold;">material</span>-&gt;<span style="color: #00f;font-weight: bold;">desc</span>)%&gt;</blockquote>
	������<span style="background-color: #fee;">�����ƥ��ĥ�ѥơ��֥��<span style="color: #f00;font-weight: bold;">products</span>�ե������</span>�ˡʴ����ڡ����ǡ����ꤷ�Ƥ���<span style="background-color: #efe;">���ʥơ��֥�Ρ�<span style="color: #090;font-weight: bold;">material</span>�ե������</span>�ˡʴ����ڡ����ǡ����ꤷ�Ƥ���<span style="background-color: #eef;">����ơ��֥�Ρ�<span style="color: #00f;font-weight: bold;">desc</span>�ե������</span>�٤Ȥʤ�ޤ���<br />
</p>

<div class="t01"><div class="t02"><div class="t03">
<h6>��졼�����SQL������</h6>
</div></div></div>
�ƥ�ץ졼�Ȥ˵��Ҥ�����졼�����¤��aaa-&gt;bbb-&gt;ccc-&gt;ddd�ˤǡ�ưŪ��SQL���������Ƥ���ΤǤ����������ƥबɽ������뤿�Ӥ�ưŪ�������Ƥ����ΤǤϡ�̵�̤ʥ����С��إåɤ�ȯ�����Ƥ��ޤ��ޤ��������ǡ�������������SQL�ϡ�����å��夷�ƻȤ��ޤ路�Ƥ��ޤ�������å���ϡ����餫�Υե�������Խ��򤷤����˥��ꥢ����ޤ���
<br />