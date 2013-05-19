<?php

namespace Augustus;

class Augustus {
	public function new_post()
	{
		echo "Creating a new post\nTitle: ";
		$title = trim(fgets(STDIN));

		echo "Publish date [".date('Ymd')."]: ";
		$date = trim(fgets(STDIN));
		if(empty($date))
			$date = date('Ymd');

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
		
		$filename = 'posts/'.$date.'.'.$json['slug'].'.md';
		file_put_contents($filename, $md);
		system('subl -w ./'.$filename);

		exit("Blog post saved as $filename.\n");
	}
	public function build()
	{
		exit("EHRMERGERD, BUILDING SHIT!\n");
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
//		if (!in_array('a', $options))
//			exit ("Invalid options. See `help` for details.\n");
	}
	private function slug($str)
	{
		$str = strtolower($str);
		$str = str_replace(' ', '-', $str);
		$str = preg_replace('/[^a-z0-9|\.|\-|_]/','',$str);
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