<?php

namespace Augustus;
		require_once('./src/markdown.php');
		use \Michelf\Markdown;

class Augustus {
	private $options = ['forced' => false];

	public function new_post()
	{
		echo "Creating a new post\nTitle: ";
		$title = trim(fgets(STDIN));

		echo "Publish date [".date('Y-m-d')."]: ";
		$date = trim(fgets(STDIN));
		if(empty($date))
			$date = date('Y-m-d');

		echo "Category [Uncategorized]: ";
		$category = trim(fgets(STDIN));
		if(empty($category))
			$category = 'Uncategorized';

		echo "Tags (separate by commas): ";
		$tags = array_map('trim',(explode(',', fgets(STDIN))));

		$json = ['title'    => $title,
			 'category' => $category,
			 'tags'     => $tags,
			 'pubdate'  => $date,
			 'slug'     => $this->slug($title) ];

		$md  = '#'.$title."\n\nPost goes here\n\n";
		$md .= "---EOF---\n";
		$md .= json_encode($json, JSON_PRETTY_PRINT);
		
		$filename = 'posts/'.$date.'_'.$json['slug'].'.md';
		file_put_contents($filename, $md);
		system('subl -w ./'.$filename);

		exit("Blog post saved as $filename.\n");
	}
	public function build()
	{
		$files = $this->write_index();
		$files = $this->checksum();
		$this->render_page();
		//$files = $this->write_checksums();
		echo "Finished building site.\n";
	}
	public function render_page()
	{
		$buffer = file_get_contents('./posts/blubb.txt');
		$pattern = '/[\n]\s*[-]{2,}\s*EOF\s*[-]{2,}\s*[\n]/s';
		$post = preg_split($pattern, $buffer);

		$content = Markdown::defaultTransform($post[0]);
		$json = (array) json_decode($post[1]);
		$page_title = $json['title'];
		
		ob_start();
		include_once('./src/template/layout.html');
		$site = ob_get_contents();
		ob_end_clean();

		file_put_contents('./build/index.html', $site);
	}
	public function write_index()
	{
		echo "Writing indicies ";
		$files = scandir('./posts/');
		foreach ($files as $file) {
			if ($file[0] != '.') {
				$tmp = file_get_contents('./posts/'.$file);
				$pattern = '/[\n]\s*[-]{2,}\s*EOF\s*[-]{2,}\s*[\n]/s';
				$post = preg_split($pattern, $tmp);
				$json = (array) json_decode($post[1]);
				$cats[$json['category']]['slug'] = 
					$this->slug($json['category']);
				$cats[$json['category']]['files'][] = $file;
				foreach ($json['tags'] as $tag) {
					$tags[$tag]['slug'] = $this->slug($tag);
					$tags[$tag]['files'][] = $file;
					echo '.';
				}
				echo '.';
			}
		}
		echo " OK\n";
		$cats = json_encode($cats, JSON_PRETTY_PRINT);
		$tags = json_encode($tags, JSON_PRETTY_PRINT);
		if (file_put_contents('./posts/.categories', $cats)  &&
			file_put_contents('./posts/.tags', $tags))
			return true;
		else
			return false;		
	}
	public function write_checksums()
	{
		$files = scandir('./posts/');
		foreach ($files as $file) {
			if ($file[0] != '.')
				$tmp[$file] = md5_file('./posts/'.$file);
		}
		$json = json_encode($tmp, JSON_PRETTY_PRINT);
		if (file_put_contents('./posts/.checksums', $json))
			return true;
		else
			return false;

	}
	public function checksum()
	{
		if ($this->options['forced'] == true) {
			echo "Skipping checksums, build forced.\n";
			$tmp = scandir('./posts/');
			foreach ($tmp as $file) {
				if ($file[0] != '.') {
					$rebuild[] = $file;
				}
			}
			return $rebuild;
		}

		$files = file_get_contents('./posts/.checksums');
		$files = (array) json_decode($files);

		$rebuild = false;
		$tmp = array_diff(scandir('./posts/'), array_keys($files));
		echo "Checking for new posts ";
		foreach ($tmp as $file) {
			if ($file[0] != '.') {
				$rebuild[] = $file;
				echo '.';
			}
		}
		echo "\n";

		echo "Checking checksums for updated posts ";
		foreach ($files as $file => $checksum) {
			if($checksum != md5_file('./posts/'.$file))
				$rebuild[] = $file;
			echo '.';
		}
		echo "\n";
		return $rebuild;
	}
	public function rm_post($var)
	{

	}
	public function edit_post($var)
	{

	}

	public function new_category()
	{

	}
	public function edit_category()
	{

	}
	public function rm_category()
	{

	}

	public function set_options($options)
	{
		if (in_array('f', $options))
			$this->options['forced'] = true;
	}
	private function slug($str)
	{
		$str = strtolower($str);
		$str = str_replace(' ', '-', $str);
		$str = preg_replace('/[^a-z0-9|\-|_]/','',$str);
		$words = explode('-',$str);
//		$str = substr($str, 0, $this->config->get('alias_length'));
		if(count($words) >= 3) {
			$word = strrchr($str, '-');
			if(!in_array(str_replace('-', '', $word), $words)) {
				$end = strrpos($str, $word);
				$str = substr($str, 0, $end);
			}
		}
		return $str;
	}

}