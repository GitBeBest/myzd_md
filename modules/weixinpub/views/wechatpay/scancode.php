<?php
$data = $data;
$url=$data->url;
?>
<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1" /> 
    <title>微信支付：扫码支付（模式二）</title>
</head>
<body>
	<div style="margin-left: 10px;color:#556B2F;font-size:30px;font-weight: bolder;">扫描支付模式二</div><br/>
	<img alt="模式二扫码支付" src="http://md.mingyizd.com/weixinpub/qrcode/temporary?url=<?php echo urlencode($url2);?>" style="width:150px;height:150px;"/>
	
</body>
</html>