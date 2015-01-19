<?php

require_once "../dbaccessor.php";
 
class PageManager extends DBAccessor {

	function __construct() {	
		parent::__construct();				
	}
	
	function __destruct() {		
		parent::__destruct();	
	}
	
	function getPages() {		
		$parent = $this->getInt('node');
		$query = "SELECT r.id, r.menu_title, r.parent, r.file_name," .
				" (SELECT COUNT(*) FROM cms2_pages WHERE parent = r.id) AS children FROM cms2_pages AS r WHERE r.parent = $parent";
		$all_pages = $this->getAssocList($query);
		$pages = array();		
		for($i = 0; $i < count($all_pages); $i++) {
			$page = $all_pages[$i];			
			$page_info = array();
			$page_info['id'] = $page['id'];
			$page_info['filename'] = $page['file_name'];
			$page_info['label'] = $page['menu_title'];
			$children = intval($page['children']);					

			if($children > 0) {
				$page_info['leaf'] = false;
			} else {
				$page_info['leaf'] = true;
			}
			$pages[] = $page_info;							
		}
		echo json_encode($pages);
	}
	
	
	function getPageInfo() {
		$id = $this->getInt('id');
		$query = "SELECT menu_title, content, file_name FROM cms2_pages WHERE id = $id";
		$page_info = $this->getAssocList($query);
		echo json_encode($page_info);
	}
	
	function savePageInfo() {
		$id = $this->getInt('id');
		$menu_title = $this->getString('menu_title');
		$filename =$this->getString('file_name');
		$content = $this->getString('text');
		$query = "UPDATE cms2_pages SET menu_title = '$menu_title', file_name = '$filename', content = '$content' WHERE id = $id";
		$this->execute($query);
		$result = array('result' => 1);
		//$this->generateSiteMap();
		echo json_encode($result);
	}
	
	function addPage() {
		$parent = $this->getInt('parent');
		$title = $this->getString('title');
		$result = array ();
		include"../translit.php";
		$title_latin = get_translit_singleline($title); 

		
		$query = "INSERT INTO cms2_pages (id, title, menu_title, content, parent, file_name, outer_script, page_status, menu_status, page_type ) " .
				 "VALUES(DEFAULT, '$title', '$title', '', $parent,'$title_latin', '', 0, 1, 'pages')";
				 
		$id = $this->createRecord($query);
		$result[] = array('result' => '1', 'id' => $id);
		//$this->generateSiteMap();
		echo json_encode($result);		 
	}
	
	function deletePage() {
		$id = $this->getInt('id');
		
		$all_query = "SELECT id, parent FROM cms2_pages";
		$all_pages = $this->getAssocList($all_query);
		$pages = array();
		$pages[] = $id;
		$this->getPageTree($id, $pages, $all_pages);
		
		$query = "DELETE FROM cms2_pages WHERE id IN (" . implode(',', $pages) . ")";	
		$result = $this->execute($query);
		//$this->generateSiteMap();
		echo json_encode($result);
	}
	
	function getPageTree($parent, &$pages, $all_pages) {
		for($i = 0; $i < count($all_pages); $i++) {
			$page = $all_pages[$i];
			$page_id = $page['id'];
			$page_parent =  $page['parent'];
			if($parent == $page_parent) {
				$pages[] = $page_id;
				$this->getPageTree($page_id, $pages, $all_pages);
			}
		}
	}
	function generateSiteMap() 
	{
		$xml=new DomDocument('1.0','utf-8');
		$urlset = $xml->appendChild($xml->createElement('urlset'));
		$urlset->setAttribute('xmlns','http://www.sitemaps.org/schemas/sitemap/0.9');
		$url_str = 'http://trinitymebel.ru';
		$url = $xml->createElement('url');
		$url->appendChild($xml->createElement('loc', $url_str));
		$urlset->appendChild($url);
		$query = "SELECT distinct file_name from cms2_pages where parent=0 and page_type = 'pages' and file_name <> 'ofisnaya_mebel' and file_name <> 'index' order by id asc";
		$pages = $this->getAssocList($query);
		for($i = 0; $i < count($pages); $i++) {
			$page = $pages[$i];
			$url_str = 'http://trinitymebel.ru/'.$page['file_name'].'.html';
			$url = $xml->createElement('url');
			$url->appendChild($xml->createElement('loc', $url_str));
			$urlset->appendChild($url);
		}
	
	
		$query = "SELECT g1_latin, g2_latin, g3_latin as file_name from trinity_catalog_groups order by id asc";
		$pages = $this->getAssocList($query);
		$g1_latin = array ();
		$g2_latin = array ();
		for($i = 0; $i < count($pages); $i++) {
			$page = $pages[$i];
			
			if (!in_array($page['g1_latin'], $g1_latin))
			{
				$g1_latin[]= $page['g1_latin'];
				if ($page['g1_latin'])
				{
					$url_str = 'http://trinitymebel.ru'.$page['g1_latin'];
					$url = $xml->createElement('url');
					$url->appendChild($xml->createElement('loc', $url_str));
					$urlset->appendChild($url);
				}
			}
			
			if (!in_array($page['g2_latin'], $g2_latin))
			{
				$g2_latin[]= $page['g2_latin'];
				if ($page['g2_latin'])
				{
					$url_str = 'http://trinitymebel.ru'.$page['g2_latin'];
					$url = $xml->createElement('url');
					$url->appendChild($xml->createElement('loc', $url_str));
					$urlset->appendChild($url);
				}
			}
			
			if ($page['file_name'])
			{
				$url_str = 'http://trinitymebel.ru'.$page['file_name'];
				$url = $xml->createElement('url');
				$url->appendChild($xml->createElement('loc', $url_str));
				$urlset->appendChild($url);
			}
		}
	
		$query = "SELECT g_latin as file_name from trinity_catalog order by id asc";
		$pages = $this->getAssocList($query);
		for($i = 0; $i < count($pages); $i++) {
			$page = $pages[$i];
			if ($page['file_name'])
			{
				$url_str = 'http://trinitymebel.ru'.$page['file_name'].'.html';
				$url = $xml->createElement('url');
				$url->appendChild($xml->createElement('loc', $url_str));
				$urlset->appendChild($url);
			}
		}
	
	
		$xml->formatOutput = true;
		$xml->save('sitemap.xml');
		copy('sitemap.xml', '../../sitemap.xml');
	}
	
}

?>
