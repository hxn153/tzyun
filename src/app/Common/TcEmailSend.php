<?php
namespace App\Common;

/**
 * 邮件发送类
 */
class TcEmailSend {
	protected $API_KEY = "OL8n7pELEOSb99jq";
	protected $API_USER;

	public function __construct() {
		$this->setAuth('trigger');
	}

	/**
	 * 初始化API_USER
	 * @param string $sendType API_USER类型
	 */
	public function setAuth($sendType) {
		if ($sendType == 'trigger') {
			$this->API_USER = '5f7fc778ac0e1eda61fe719435aa82d5';
		} else if ($sendType == 'batch') {
			$this->API_USER = 'e509bd324551c5bd382955c08af9a30b';
		}
	}

	private function _err($result) {
		$result .= "[{$this->API_USER}]";
		\PhalApi\DI()->Logger->info("邮件发送失败:" . $result . "\r\n", 'ERR', '', LOG_PATH . 'send_' . date('y_m_d') . '.log');
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
				'header'  => 'Content-Type: application/x-www-form-urlencoded',
				'content' => $data,
			));

		$context = stream_context_create($options);

		$result = file_get_contents($API_URL, FILE_TEXT, $context);

		if ($result) {
			$_result = json_decode($result, true);
			if ($_result) {

				if (!$_result) {
					$this->_err($result);
				}

				$_result = $_result['message'] == 'success';

				return $_result;
			}

			$this->_err($result);
		} else {
			$this->_err('没有返回数据');
		}

		return false;
	}

	/**
	 * 普通发送
	 * @param  string $body 邮件正文
	 * @param  string/array $to 收件人（字符串表示一个人，数组表示多人）
	 * @param  string $subject 邮件主题
	 * @param  string $from 发件人
	 * @param  string $fromname 发件人名称
	 * @return boolean 发送结果
	 */
	public function send($body, $to, $subject = '', $from = 'service@ynnic.net', $fromname = "天成科技") {
		$API_URL = "http://www.sendcloud.net/webapi/mail.send.json";

		if (is_array($to)) {
			$to = explode(';', $to);
		}

		$param = array(
			'api_user'      => $this->API_USER,
			'api_key'       => $this->API_KEY,
			'from'          => $from,
			'fromname'      => $fromname,
			'to'            => $to,
			'subject'       => $subject,
			'html'          => $body,
			'resp_email_id' => 'true',
		);

		$data = http_build_query($param);

		return $this->_request($data, $API_URL);
	}
	/**
	 * 模板发送
	 * @param  string $body 邮件正文
	 * @param  string/array $to 收件人（字符串表示一个人，数组表示多人）
	 * @param  string $template 模板名称
	 * @param  string $subject 邮件主题
	 * @param  string $from 发件人
	 * @param  string $fromname 发件人名称
	 * @return boolean 发送结果
	 */
	public function sendTemplate($vars, $to, $template, $subject = '', $from = 'service@ynnic.net', $fromname = "天成科技") {
		$API_URL = 'http://www.sendcloud.net/webapi/mail.send_template.json';
		if (is_string($to)) {
			$to = array($to);
		}

		if ($vars !== null) {
			foreach ($vars as $key => &$value) {
				$value = array($value);
			}

			$vars = json_encode(
				array(
					"to"  => $to,
					"sub" => $vars,
				)
			);
		} else {
			$vars = json_encode(
				array(
					"to" => $to,
				)
			);
		}

		$param = array(
			'api_user'             => $this->API_USER, # 使用api_user和api_key进行验证
			'api_key'              => $this->API_KEY,
			'from'                 => $from, # 发信人，用正确邮件地址替代
			'fromname'             => $fromname,
			'substitution_vars'    => $vars,
			'template_invoke_name' => $template,
			'subject'              => $subject,
			'resp_email_id'        => 'true',
		);

		$data = http_build_query($param);

		return $this->_request($data, $API_URL);
	}

	//自定义回调（用于模板发送）
	public function __call($method, $args) {
		if (strtolower(substr($method, 0, 6)) == 'sendby' && strlen($method) > 6) {
			//解析模板名称

			$method   = parse_name(substr($method, 6));
			$template = 'tc_' . $method;
			$alen     = count($args);
			if ($alen >= 2) {
				$_args = array();
				//插入参数（将模板名称插入到第三个参数位置）
				for ($i = 0; $i < $alen + 1; $i++) {
					if ($i == 2) {
						$_args[] = $template;
					} else if ($i > 2) {
						$_args[] = $args[$i - 1];
					} else {
						$_args[] = $args[$i];
					}
				}
				//反射回调函数
				$method = new \ReflectionMethod($this, 'sendTemplate');
				return $method->invokeArgs($this, $_args);
			} else {
				E(__CLASS__ . ':' . $method . L('_PARAM_ERROR_'));
			}

		} else {
			E(__CLASS__ . ':' . $method . L('_METHOD_NOT_EXIST_'));
		}

	}
}