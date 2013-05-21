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
			 'slug'     => $this->slug($title), 
			 'layout'   => 'post'];

		$md  = '#'.$title."\n\nPost goes here\n\n";
		$md .= "---EOF---\n";
		$md .= json_encode($json, JSON_PRETTY_PRINT);
		
		$filename = 'posts/'.$date.'_'.$json['slug'].'.md';
		file_put_contents($filename, $md);
		system('subl -w ./'.$filename);

		exit("Blog post saved as $filename.\n");
	}
	public function new_page()
	{
		echo "Creating a new static page\nTitle: ";
		$title = trim(fgets(STDIN));
		$slug = $this->slug($title);

		echo "Path [/$slug]: ";
		$path = trim(fgets(STDIN));
		if(empty($path))
			$path = '/'.$slug;

		$json = ['title'    => $title,
			 'slug'     => $slug, 
			 'layout'   => 'page',
			 'path'     => $path];

		$md  = '#'.$title."\n\nPost goes here\n\n";
		$md .= "---EOF---\n";
		$md .= json_encode($json, JSON_PRETTY_PRINT
					| JSON_UNESCAPED_SLASHES);
		
		$filename = 'pages/'.$json['slug'].'.md';
		file_put_contents($filename, $md);
		system('subl -w ./'.$filename);

		exit("Static page saved as $filename.\n");
	}
	public function build()
	{
		$this->copy_site_assets();
		
		$files = $this->write_index();
		$files = $this->checksum();
		var_dump($files);
		echo "Rendering pages ";
		foreach ($files as $file) {
			$this->render_page($file);
			echo '.';
		}
		echo "\n";
		
		$files = $this->write_checksums();
		echo "Finished building site.\n";
	}
	private function copy_site_assets()
	{
		echo "Copying layout assets to build directory ";
		$files = scandir('./src/template');
		foreach ($files as $file) {
			if ($file[0] != '.')
			if (is_dir('./src/template/'.$file)) {
					if (!file_exists("./build/$file"))
						mkdir("./build/$file");
				$this->copy_dir('./src/template/'.$file, 
					'./build/'.$file);
				echo '.';
			}
		}
		echo "\n";
	}
	private function copy_dir($src, $dst)
	{
		$files = scandir($src);
		foreach ($files as $file) {
			if($file[0] != '.') {
				if(is_dir("$src/$file")) {
					if (!file_exists("$dst/$file"))
						mkdir("$dst/$file");
					$this->copy_dir("$src/$file", "$dst/$file");
				} else {
					if (!copy("$src/$file", "$dst/$file")) 
						echo "$src/$file failed.\n";
				}
			}
		}
	}
	public function render_page($file)
	{
		$dest = './build/';

		$filename = pathinfo($file, PATHINFO_FILENAME);
		list($date, $slug) = explode('_', $filename);
		list($year, $month, $day) = explode('-', $date);
		//var_dump($date, $slug, $year, $month, $day);


		$buffer = file_get_contents('./posts/'.$file);
		$pattern = '/[\n]\s*[-]{2,}\s*EOF\s*[-]{2,}\s*[\n]/s';
		list($post, $json) = preg_split($pattern, $buffer);

		$content = $this->prosedown($post);
		$content = Markdown::defaultTransform($content);
		$json = (array) json_decode($json);
		$page_title = $json['title'];
		$layout = './src/template/'.$json['layout'].'.html';

		switch ($json['layout']) {
			case 'post':
				$dest .= "$year/$month/$slug";
				break;
			case 'page':
				$dest .= $json['path']."/$slug";
				break;
		}
		
		ob_start();
		include_once('./src/template/layout.html');
		$site = ob_get_contents();
		ob_end_clean();

		file_put_contents($dest.'.html', $site);
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
		echo "Writing checksums ";
		$files = scandir('./posts/');
		foreach ($files as $file) {
			if ($file[0] != '.') {
				$tmp[$file] = md5_file('./posts/'.$file);
				echo '.';
			}
		}
		echo "\n";
		$json = json_encode($tmp, JSON_PRETTY_PRINT);
		if (file_put_contents('./posts/.checksums', $json))
			return true;
		else
			return false;

	}
	public function checksum()
	{
		$rebuild = [];
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
	private function prosedown($str)
	{
		// Em-dashes
		$str = preg_replace('/([^-\s])-{3}([^-\s])/m', '$1&mdash;$2',$str);
		$str = preg_replace('/(\S+[\s])-{3}([\s])/m', '$1&mdash;$2',$str);

		// En-dashes
		$str = preg_replace('/([^-\s])-{2}([^-\s])/m', '$1&ndash;$2',$str);
		$str = preg_replace('/(\S?[\s])-{2}([\s])/m', '$1&ndash;$2',$str);

		// Dinkus
		$str = preg_replace('/^[ |\t]*([ ]?\*[ ]+\*[ ]+\*)[ \t]*$/m',
			'<p class="scene-break">* * *</p>',$str);

		// Asterism
		$str = preg_replace('/^[ |\t]*([ ]?\*){3}[ \t]*$/m',
			'<p class="scene-break">&#8258;</p>',$str);

		// Horizontal rules
		$str = preg_replace('/^[ |\t]*([ ]?[\*\_\-\=\~][ ]?){3,}[ \t]*$/m',
			'<hr />',$str);

		// Emphasism
		$str = preg_replace('/(_)(?=\S)([^\r]*?\S)\1/',
			"<u>$2</u>",$str);
		$str = preg_replace('/(\*)(?=\S)([^\r]*?\S)\1/',
			"<strong>$2</strong>",$str);
		$str = preg_replace('/(\/)(?=\S)([^\r]*?\S)\1/',
			"<em>$2</em>",$str);
		$str = preg_replace('/\s(\-)(?=\S)([^\r]*?\S)\1\s/',
			" <s>$2</s> ",$str);

	//	str = str.replace(/\r\n/g,"\n");
	//	str = str.replace(/\n\r/g,"\n");
	//	str = str.replace(/\r/g,  "\n");

		//English spacing
		$str = preg_replace('/(\w[\.|\!|\?])[ ]{2}(\S)/m',
			'$1&ensp;$2',$str);

		return $str;	
	}
}