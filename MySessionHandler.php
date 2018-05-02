<?php
/**
*
*/
class MySessionHandler implements SessionHandlerInterface
{
	private $link;
	private $lifetime;
	// 开启session
	public function open($save_path, $session_name)
	{
		$this->lifetime = get_cfg_var('session.gc_maxlifetime');
		$this->link = mysqli_connect('localhost','root','root','test','3306');
		mysqli_set_charset($this->link,'utf8');
		if($this->link)
		{
			return true;
		}
		return false;
	}

	// 关闭session
	public function close ()
	{
		return mysqli_close($this->link);
	}

	// 写入session
	public function write($session_id, $session_data)
	{
		$expire = time() + $this->lifetime;
		$session_id = mysqli_escape_string($this->link,$session_id);
		$sql = "SELECT * FROM sessions WHERE session_id='".$session_id."'";
		$result = mysqli_query($this->link, $sql);
		if(mysqli_num_rows($result) == 1)
		{
			$sql = "UPDATE sessions SET session_data='".$session_data."', expire_time=".$expire." where session_id='".$session_id."'";
		}
		else
		{
			$sql = "INSERT INTO sessions VALUES('".$session_id."','".$session_data."',".$expire.")";
		}
		mysqli_query($this->link, $sql);
		return mysqli_affected_rows($this->link) == 1;
	}

	// 读取session
	public function read($session_id)
	{
		$session_id = mysqli_escape_string($this->link,$session_id);
		$sql = "SELECT * FROM sessions WHERE session_id='".$session_id."' and expire_time>".time();
		$result = mysqli_query($this->link, $sql);
		if(mysqli_num_rows($result) == 1)
		{
			return mysqli_fetch_assoc($result)['session_data'];
		}
		return '';
	}

	// 销毁session
	public function destroy($session_id)
	{
		$session_id = mysqli_escape_string($this->link,$session_id);
		$sql = "DELETE FROM sessions WHERE session_id='".$session_id."'";
		mysqli_query($this->link, $sql);
		return mysqli_affected_rows($this->link) == 1;
	}

	public function gc($maxlifetime)
	{
		$time = time() - $maxlifetime;
		$sql = "DELETE FROM sessions WHERE expire_time<".$time;
		mysqli_query($this->link, $sql);
		if(mysqli_affected_rows($this->link) > 1)
		{
			return true;
		}
		return false;
	}
}
