<?php 

	$GLOBALS['TRANSLITER']['alfavitlower'] = explode(',', '., ,¸,é,ö,ó,ê,å,í,ã,ø,ù,ç,õ,ú,ô,û,â,à,ï,ð,î,ë,ä,æ,ý,ÿ,÷,ñ,ì,è,ò,ü,á,þ');
	$GLOBALS['TRANSLITER']['alfavitupper'] = explode(',', '., ,¨,É,Ö,Ó,Ê,Å,Í,Ã,Ø,Ù,Ç,Õ,Ú,Ô,Û,Â,À,Ï,Ð,Î,Ë,Ä,Æ,Ý,ß,×,Ñ,Ì,È,Ò,Ü,Á,Þ');
	$GLOBALS['TRANSLITER']['alfavit_trans'] = explode(',', '_,_,e,y,c,u,k,e,n,g,sh,shh,z,h,,f,y,v,a,p,r,o,l,d,j,e,ya,ch,s,m,i,t,,b,u');

	function get_translit_singleline($source_str)
	{
		$ret = strtotranslit(trim($source_str));
		return $ret;
	}

	function get_translit_multiline($source_str)
	{
		$ret = array();
		foreach(preg_split("/(\r?\n)/", $source_str) as $line)
		{
			$ret[] = strtotranslit(trim($line));
		}
		return $ret;
	}

	function strtotranslit($word) 
	{
		$ret = str_replace($GLOBALS['TRANSLITER']['alfavitlower'], 
			$GLOBALS['TRANSLITER']['alfavit_trans'], 
			strtolower_cyr($word));
		return preg_replace("/[^A-Za-z0-9 _]/","",$ret);
	}

	function strtolower_cyr($word) 
	{
		return str_replace($GLOBALS['TRANSLITER']['alfavitupper'], 
			$GLOBALS['TRANSLITER']['alfavitlower'], 
			$word);
	}

	function strtoupper_cyr($word) 
	{
		return str_replace($GLOBALS['TRANSLITER']['alfavitlower'], 
			$GLOBALS['TRANSLITER']['alfavitupper'], 
			$word);
	}


	if(isset($_GET['transl_source']))
	{
		echo get_translit_singleline($_GET['transl_source']);
	}

?>