<?php
require_once "WechatJSSDK.php";
$jssdk = new WechatJSSDK();
$signPackage = $jssdk->GetSignPackage();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title></title>
</head>
<body>
    <h2>just test</h2>
</body>
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script>
  // 注意：所有的JS接口只能在公众号绑定的域名下调用，公众号开发者需要先登录微信公众平台进入“公众号设置”的“功能设置”里填写“JS接口安全域名”。 
  // 如果发现在 Android 不能分享自定义内容，请到官网下载最新的包覆盖安装，Android 自定义分享接口需升级至 6.0.2.58 版本及以上。
  // 完整 JS-SDK 文档地址：http://mp.weixin.qq.com/wiki/7/aaa137b55fb2e0456bf8dd9148dd613f.html
wx.config({
    debug: false,
    appId: '<?php echo $signPackage["appId"];?>',
    timestamp: <?php echo $signPackage["timestamp"];?>,
    nonceStr: '<?php echo $signPackage["nonceStr"];?>',
    signature: '<?php echo $signPackage["signature"];?>',
    jsApiList: [
        'checkJsApi',
        'openLocation',
        'getLocation',
        'onMenuShareTimeline',
        'onMenuShareAppMessage',
        'hideAllNonBaseMenuItem',
	'showMenuItems',
    ]
});
    wx.ready(function() {
		
        wx.hideAllNonBaseMenuItem();
        wx.showMenuItems({
                menuList: [
                    'menuItem:share:appMessage',
                ] // 要显示的菜单项，所有menu项见附录3
        });

        wx.onMenuShareAppMessage({
          title: '测试：标题',
          desc: '测试：描述',
          link: 'http://www.baidu.com',
          imgUrl: 'http://s2.nuomi.bdimg.com/upload/deal/2014/1/V_L/623682-1391756281052.jpg',
          trigger: function (res) {
            // 不要尝试在trigger中使用ajax异步请求修改本次分享的内容，因为客户端分享操作是一个同步操作，这时候使用ajax的回包会还没有返回
            // alert('用户点击发送给朋友');
          },
          success: function (res) {
             alert('已分享');
          },
          cancel: function (res) {
             alert('已取消');
          },
          fail: function (res) {
             alert(JSON.stringify(res));
          }
        });
		
    });
	

		
</script>
</html>