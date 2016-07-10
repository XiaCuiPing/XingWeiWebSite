<?php
namespace Core;
class Mail{
	/**
	 *
	 * @param unknown $mailto
	 * @param unknown $subject
	 * @param unknown $message
	 * @param unknown $mailfrom
	 * @param string $charset
	 */
	function sendmail($mailto,$subject,$message,$mailfrom,$charset="UTF-8"){
		// 当发送 HTML 电子邮件时，请始终设置 content-type
		$headers = "From: $mailfrom\nX-Priority: 3\n";
		$headers.= "X-Mailer: DSXCMS\nMIME-Version: 1.0\n";
		$headers.= "Content-type: text/html; charset=$charset\n";
		$headers.= "Content-Transfer-Encoding: base64\n";
	
		if(is_array($mailto)){
			$mailto = implode(',', $mailto);
		}
	
		$subject = '=?'.$charset.'?B?'.base64_encode(str_replace("\r", '', str_replace("\n", '', $subject))).'?=';
	
		$message = '<html><head><title>'.$subject.'</title></head><body><p>'.$message.'</p></body></html>';
		$message = chunk_split(base64_encode(str_replace("\r\n.", " \r\n..", str_replace("\n", "\r\n", str_replace("\r", "\n", str_replace("\r\n", "\n", str_replace("\n\r", "\r", $message)))))));
		return @mail($mailto,$subject,$message,$headers,"-f $mailfrom");
	}
}