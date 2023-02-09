<?php

require_once(__DIR__ . '/../../config.php');

class SystemController
{
	private string $fileName = 'log.txt';

	public function clear() : int
	{
		$contents = '';

		if(!($contents = file_get_contents($this->fileName)))
		{
			return 0;
		}

		$length = strlen($contents);

		if($length > SYSTEM_LOG_SIZE)
		{
			$fd = fopen($this->fileName, 'w');

			if(!$fd)
			{
				return 0;
			}
	
			fwrite($fd, "\n");
			fclose($fd);
		}

		return $length;
	}

	public function concat(string $str1, string $str2) : string
	{
		return $str1 .= $str2;
	}

	public function report(string $class, string $method, string $message) : string
	{
		$string = '';

		$string = $this->concat($string, '[');
		$string = $this->concat($string, date("Y-m-d H:i:s"));
		$string = $this->concat($string, ' | ');
		$string = $this->concat($string, $class);
		$string = $this->concat($string, ', ');
		$string = $this->concat($string, $method);
		$string = $this->concat($string, '] ');
		$string = $this->concat($string, $message);
		$string = $this->concat($string, "\n");

		$this->clear();

		$fd = fopen($this->fileName, 'a');

		if(!$fd)
		{
			return '';
		}

		fwrite($fd, $string);
		fclose($fd);

		return $string;
	}
}

?>
