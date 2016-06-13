<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
	public function index(){
	    $wechat = new\Org\Wechat\wechatCallbackapiTest();
  		if($_GET['echostr']){
	    	/*存在，接入操作*/
	    	$wechat->valid();
		}else{
		    /*不存在，接入后操作*/
		    $this->responseMsg();
 	    }
	}

	// 自定义接入后操作
    public function responseMsg(){
    	//创建自定义菜单
//    	echo $this->createMenu();

	  	$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
	  	/*extract post data*/
	  	if (!empty($postStr)){
		    libxml_disable_entity_loader(true);
		    $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);

		    $fromUsername = $postObj->FromUserName;  /*开发者微信号*/
		    $toUsername   = $postObj->ToUserName;    /*发送方帐号（一个OpenID）*/
		    $keyword      = trim($postObj->Content); /*用户关键字*/
		    $time         = time();                  /*系统时间*/
		    $event        = $postObj->Event;         /*获取事件类型*/
		    $eventkey     = $postObj->EventKey;      /*获取事件关键字，对应报文key数据*/
		    $msgType      = $postObj->MsgType;       /*用户发送的消息类型*/

		    $textTpl = "<xml>
	                <ToUserName><![CDATA[%s]]></ToUserName>
	                <FromUserName><![CDATA[%s]]></FromUserName>
	                <CreateTime>%s</CreateTime>
	                <MsgType><![CDATA[%s]]></MsgType>
	                <Content><![CDATA[%s]]></Content>
	                <FuncFlag>0</FuncFlag>
	                </xml>";

	        //扫描带参数二维码，公众号做出响应(分为关注和未关注两种情况)
//	        if($event == 'subscribe' && substr($eventkey, 0,8) == 'qrscene_'){
//	        	//未关注扫描，关注后触发本事件
//	        	//保存$eventkey的值区分不同二维码$eventkey = 'qrscene_生成二维码的数值'
	        	$msgType    = "text";
			    $contentStr = "您之前未关注过本账号，并且扫描了带参数的二维码";
			    $resultStr  = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
			    echo $resultStr;
//	        }else if($event == 'SCAN'){
//	        	$msgType    = "text";
//			    $contentStr = "您之前关注过本账号，并且扫描了带参数的二维码";
//			    $resultStr  = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
//			    echo $resultStr;
//	        }

	        //接入多客服
//	        if($keyword == '客服'){
//	        	$textTpl = "<xml>
//				    <ToUserName><![CDATA[%s]]></ToUserName>
//				    <FromUserName><![CDATA[%s]]></FromUserName>
//				    <CreateTime>%s</CreateTime>
//				    <MsgType><![CDATA[transfer_customer_service]]></MsgType>
//				 	</xml>";
//
//			    $resultStr  = sprintf($textTpl, $fromUsername, $toUsername, $time);
//			    echo $resultStr;
//	        }
		    //大写CLICK
//		    if($event == 'CLICK' && $eventkey == "cjzc"){
//	            $msgType    = "text";
//			    $contentStr = "敬请期待mo-微笑";
//			    $resultStr  = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
//			    echo $resultStr;
//		    }
	    }
	}

	//创建菜单
	function createMenu(){
		$Token = $this->getAccessToken();
		$access_token = $Token['access_token'];

 	    $data = '{
		    "button": [
		        {
		            "name": "TT咨询", 
		            "sub_button": [
		                {
		                    "type": "view", 
		                    "name": "快递查询", 
		                    "url": "http://stock1.sina.cn/dpool/stockv2/universal_calendar.php?vt=4"
		                }, 
		                {
		                    "type": "click", 
		                    "name": "线下活动", 
		                    "key": "cjzc"
		                }
		            ]
		        }, 
		        {
		            "type": "view", 
		            "name": "测试下载app", 
		            "url": "http://www.baidu.com"
		        }, 
		        {
		            "name": "更多", 
		            "sub_button": [
		                {
		                    "type": "click", 
		                    "name": "公司介绍", 
		                    "key": "cjzc"
		                }, 
		                {
		                    "type": "click", 
		                    "name": "玩游戏送好礼", 
		                    "key": "cjzc"
		                }, 
		                {
		                    "type": "click", 
		                    "name": "宣传片预告", 
		                    "key": "cjzc"
		                }, 
		                {
		                    "type": "click", 
		                    "name": "联系我们", 
		                    "key": "cjzc"
		                }, 
		                {
		                    "type": "click", 
		                    "name": "意见反馈", 
		                    "key": "cjzc"
		                }
		            ]
		        }
		    ]
		}';

 		$ch = curl_init();
	 	curl_setopt($ch, CURLOPT_URL, "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$access_token);
	 	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	 	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	 	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
	 	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	 	curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
	 	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	 	$tmpInfo = curl_exec($ch);
	 	if (curl_errno($ch)) {
	  		return curl_error($ch);
	 	}
	 	curl_close($ch);
	 	return $tmpInfo;
	}

	public function app_download(){
	  	$this->display("Index/app_download");
	}

	//获取微信的accessToken,需要上传到服务器，本地可能不成功
  	public function getAccessToken(){
	  	// 请求URL地址
	  	// 订阅号
	 //    $appid  = "wx61777497169d1f60";
		// $secret = "c8401c9e56fd521e4127735320199ef8";
		// 服务号
	    $appid  = "wxc31857a1af057b5e";
		$secret = "21d7efc28b0a2bf4055d351e10a9e444";
	  	$url    = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$secret;

	    $ch = curl_init();                              //初始化
	    //针对https抓取
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//跳过证书检查
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true); //从证书中检查SSL加密算法是否存在
	    curl_setopt($ch, CURLOPT_URL, $url);            //设置提交的页面
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);    //返回抓取的内容
	    $data = curl_exec($ch);
	    if(curl_errno($ch)){
	    	var_dump(curl_error($ch));
	    }
	    curl_close($ch);
	    $arr = json_decode($data,true);
	    return $arr;
  	}

  	//获取微信的ServerIp,需要上传到服务器，本地可能不成功
  	public function getServerIp(){
	  	// 请求URL地址
	  	$access_token = $this->getAccessToken();
	  	$url          = "https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token=".$access_token['access_token'];
	    
	    // curl抓取
	    $ch = curl_init();                           // 初始化
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//跳过证书检查
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true); //从证书中检查SSL加密算法是否存在
	    curl_setopt($ch, CURLOPT_URL, $url);         //设置提交的页面
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //返回抓取的内容
	    $data = curl_exec($ch);
	    if(curl_errno($ch)){
	    	var_dump(curl_error($ch));
	    }
	    curl_close($ch);
	    $ip = json_decode($data,true);
	    return $ip;
  	}

  	// 微信接口验证，是否从微信服务器发出，判断合法请求
  	public function interfaceCheck(){
  		$ipList = $this->getServerIp();
  		foreach ($ipList['ip_list'] as $k => $v) {
  			$ipString .= '#'.$v; 
  		}

  		$ip = '101.226.62.811';  //测试ip
  		header("Content-type: text/html; charset=utf-8"); 
  		if(strpos($ipString, $ip)>0){
  			echo "合法请求";
  		}else{
  			echo "非法请求";
  		}
  	}

  	//长链接转短链接（链接太长，响应速度缓慢）
  	public function getShortUrl(){
  		$access_token = $this->getAccessToken();

  		//输入long_url的值，即要转的长链接
  		$data = '{"action":"long2short","long_url":"http:www.baidu.com"}';
  		$url  = 'https://api.weixin.qq.com/cgi-bin/shorturl?access_token='.$access_token['access_token'];
  		// curl抓取
	    $ch = curl_init();                              // 初始化
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//跳过证书检查
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true); //从证书中检查SSL加密算法是否存在
	    curl_setopt($ch, CURLOPT_URL, $url);            //设置提交的页面
	    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	    curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //返回抓取的内容

	    $tmpInfo = curl_exec($ch);
	    if(curl_errno($ch)){
	    	var_dump(curl_error($ch));
	    }
	    curl_close($ch);
	    // return $tmpInfo;
	    print_r($tmpInfo);
  	}

  	//用户管理，获取用户列表（10000以上用户通过next_openid多次获取）
    public function getUserList(){
    	$access_token = $this->getAccessToken();
    	$url = 'https://api.weixin.qq.com/cgi-bin/user/get?access_token='.$access_token['access_token'];

    	$ch = curl_init();                              //初始化
	    //针对https抓取
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//跳过证书检查
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true); //从证书中检查SSL加密算法是否存在
	    curl_setopt($ch, CURLOPT_URL, $url);            //设置提交的页面
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);    //返回抓取的内容
	    $data = curl_exec($ch);
	    if(curl_errno($ch)){
	    	var_dump(curl_error($ch));
	    }
	    curl_close($ch);
	    $userList = json_decode($data,true);
	    // return $userList;
	    print_r($userList);
    }

    //用户管理，通过用户openid获取用户基本信息
    public function getUserMessage(){
    	$access_token = $this->getAccessToken();

    	$openid = 'obcMaxOMbxIO3_JeRc7jHAe2126Y'; //用户openid
    	$lang   = 'zh_CN'; //zh_CN 简体，zh_TW 繁体，en 英语 
    	$url 	= 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access_token['access_token'].'&openid='.$openid.'&lang='.$lang;

    	$ch = curl_init();                              //初始化
	    //针对https抓取
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//跳过证书检查
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true); //从证书中检查SSL加密算法是否存在
	    curl_setopt($ch, CURLOPT_URL, $url);            //设置提交的页面
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);    //返回抓取的内容
	    $data = curl_exec($ch);
	    if(curl_errno($ch)){
	    	var_dump(curl_error($ch));
	    }
	    curl_close($ch);
	    $userMessage = json_decode($data,true);
	    // return $userList;
	    print_r($userMessage);
    }

    /*网页授权接口*/
    /*scope为snsapi_base、snsapi_userinfo时调用接口获取openid*/
    public function pageAccredit(){
    	// $url = 'http://open.weixin.qq.com/connect/oauth2/authorize?appid=wx3baa722d40c17edf&redirect_uri=http://www.wechat.2015tt.net/index.php/Home/Index/pageAccredit&response_type=code&scope=snsapi_base&state=STATE#wechat_redirect';
		$code = $_GET['code'];
		$url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=wx3baa722d40c17edf&secret=d4624c36b6795d1d99dcf0547af5443d&code='.$code.'&grant_type=authorization_code';

		$ch = curl_init();                              //初始化
	    //针对https抓取
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//跳过证书检查
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true); //从证书中检查SSL加密算法是否存在
	    curl_setopt($ch, CURLOPT_URL, $url);            //设置提交的页面
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);    //返回抓取的内容
	    $data = curl_exec($ch);
	    if(curl_errno($ch)){
	    	var_dump(curl_error($ch));
	    }
	    curl_close($ch);
	    $message = json_decode($data,true);
	    // 用户的openid
	    // print_r($message['openid']);

	    //获取用户基本信息
	    $access_token = $message['access_token'];
	    $openid       = $message['openid'];

	    $url2 = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';

	    $ch = curl_init();                              //初始化
	    //针对https抓取
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//跳过证书检查
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true); //从证书中检查SSL加密算法是否存在
	    curl_setopt($ch, CURLOPT_URL, $url2);            //设置提交的页面
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);    //返回抓取的内容
	    $data = curl_exec($ch);
	    if(curl_errno($ch)){
	    	var_dump(curl_error($ch));
	    }
	    curl_close($ch);
	    $userMessage = json_decode($data,true);
	    print_r($userMessage);
    }

    //生成不同的带参数的二维码
  	public function getQRCode(){
  		$access_token = $this->getAccessToken();

  		//输入scene_id的值,目前参数只支持1--100000相当于100000种
  		$data = '{"action_name": "QR_LIMIT_SCENE", "action_info": {"scene": {"scene_id": 123}}}';
  		$url  = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$access_token['access_token'];
  		// curl抓取
	    $ch = curl_init();                              // 初始化
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//跳过证书检查
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true); //从证书中检查SSL加密算法是否存在
	    curl_setopt($ch, CURLOPT_URL, $url);            //设置提交的页面
	    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	    curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //返回抓取的内容

	    $tmpInfo = curl_exec($ch);
	    if(curl_errno($ch)){
	    	var_dump(curl_error($ch));
	    }
	    curl_close($ch);
	    $ticketMessage = json_decode($tmpInfo,true);
	    // print_r($ticketMessage);
	    
	    // 获取二维码
	    $url2 = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=gQH98DoAAAAAAAAAASxodHRwOi8vd2VpeGluLnFxLmNvbS9xL0tVT3RQYWJsWkNnZDlwdVFYVy11AAIEvb_0VgMEAAAAAA==';

	    // curl抓取
	    $ch = curl_init();                              // 初始化
	    //针对https抓取
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//跳过证书检查
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true); //从证书中检查SSL加密算法是否存在
	    curl_setopt($ch, CURLOPT_URL, $url2);           //设置提交的页面
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);    //返回抓取的内容

	    $QRCode = curl_exec($ch);
	    if(curl_errno($ch)){
	    	var_dump(curl_error($ch));
	    }
	    curl_close($ch);
	    header('Content-type: image/JPEG'); //图片流设置格式，防止图片乱码
	    echo $QRCode;
  	}
}