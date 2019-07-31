<?php
namespace App\Common;

/**
 * 消息发送类
 */
class TcSmsSend {
	protected $API_KEY;
	protected $API_USER;

	public function __construct() {
		$this->setAuth();
	}

	/**
	 * 初始化验证信息
	 * @param string $API_USER API用户名
	 * @param string $API_KEY  API密钥
	 */
	public function setAuth($API_USER = '2327af3324880240', $API_KEY = 'i0VHzfxJOgVNC1bIVZTpH1TOXCqSYEMz') {
		$this->API_USER = $API_USER;
		$this->API_KEY  = $API_KEY;
	}

	private function _err($result, $data) {
		M("SmsSendLog")->add(array(
			"page" => I('server.REQUEST_URI'),
			"send_template_id" => $data['templateId'],
			'phone' => $data['phone'],
			'send_time' => time(),
			'status' => 'error'
		));
		\Think\Log::write("短信发送失败:" . $result . "\r\n", 'ERR', '', LOG_PATH . 'send_' . date('y_m_d') . '.log');
	}

	/**
	 * 请求API，发送邮件
	 * @param  string $data    	要发送的数据
	 * @param  string $API_URL  API地址
	 * @return boolean          发送结果
	 */
	private function _request($data, $API_URL) {
		$options = array(
			'http' => array(
				'method'  => 'POST',
				'header'  => "Content-Type: application/x-www-form-urlencoded",
				'content' => http_build_query($data),
			));

		$context = stream_context_create($options);
		$result  = file_get_contents($API_URL, false, $context);
		if ($result) {
            return true;
//			$_result = json_decode($result, true);
//            if ($_result) {
//                $_result = $_result['message'] == 'success';
//                return $_result;
//            }
//			$this->_err($result,$data);
		} else {
			//$this->_err('没有返回数据',$data);
		}

		return false;
	}

	/**
	 * 发送短信
	 * @param  array  $vars     模板参数
	 * @param  string $phone    收信人
	 * @param  string $template 模板名称
	 * @return boolean          发送结果
	 */
	public function send($vars, $phone, $template) {
		$API_URL = 'http://www.sendcloud.net/smsapi/send';

		if (is_array($vars)) {
			$vars = json_encode($vars);
		}

		$param = array(
			'smsUser'    => $this->API_USER,
			'templateId' => $template,
			'phone'      => $phone,
			'vars'       => $vars,
		);

		if ($vars === null) {
			unset($param['vars']);
		}

		$sParamStr = "";
		ksort($param);
		foreach ($param as $sKey => $sValue) {
			$sParamStr .= $sKey . '=' . $sValue . '&';
		}

		$sParamStr  = trim($sParamStr, '&');
		$sSignature = md5($this->API_KEY . "&" . $sParamStr . "&" . $this->API_KEY);

		$param = array(
			'smsUser'    => $this->API_USER,
			'templateId' => $template,
			'phone'      => $phone,
			'vars'       => $vars,
			'signature'  => $sSignature,
		);

		if ($vars === null) {
			unset($param['vars']);
		}

		return $this->_request($param, $API_URL);
	}

	/**
	 * [sendn description]
	 * @param  array  $vars     各手机号及模板变量
	 * @param  string $template 短信模板
	 * @return boolean          发送结果
	 */
	public function sendn($vars, $template) {
		if (count($vars)) {
			foreach ($vars as $phone => $var) {
				$this->send($var, $phone, $template);
			}

			return true;
		}

		return false;
	}

	//自定义回调（用于发送）
	public function __call($method, $args) {
		if (strtolower(substr($method, 0, 6)) == 'sendby' && strlen($method) > 6) {
			//解析模板名称
			$template = intval(substr($method, 6));

			//反射回调函数
			if (count($args) == 1) {
				$method = new \ReflectionMethod($this, 'sendn');
			} else {
				$method = new \ReflectionMethod($this, 'send');
			}

			$args[] = $template;

			return $method->invokeArgs($this, $args);

		} else {
			E(__CLASS__ . ':' . $method . L('_METHOD_NOT_EXIST_'));
		}

	}

	public function getTemplateList(){
		$API_URL="";
	}
}
