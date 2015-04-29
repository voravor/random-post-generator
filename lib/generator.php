<?php
/**
 * RPG Generator class.
 */

namespace RPG;

require_once VENDOR_PATH . '/autoload.php';

use GuzzleHttp\Client;


// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/*
 * RPG\Generator:
 * singleton class 
 * - initialization
 * - hooks
 * - libs
 *
 */

if ( ! class_exists( 'RPG\Generator' ) ) {
        
    /**
	 * RPG Generator
	 *
	 */
	class Generator {

        public $name        = "Generator";
        
        protected static $instance;
        
        protected $categories;
        protected $tags;
        protected $words;
        protected $decorations = array('i', 'b', 'em', 'mark');
        
                
        /**
		 * Static Singleton Factory Method
		 * @return Generator
		 */
		public static function instance()
        {
			if ( ! isset( self::$instance ) ) {
				$className      = __CLASS__;
				self::$instance = new $className;
			}

			return self::$instance;
		}
        
        /*
         * setup word lists for lorem ipsum functions
         *
         */
		protected function __construct()
        {
            //get dictionary words
            $words          = file(Plugin::instance()->get_plugin_path() . '/includes/_words.txt', FILE_IGNORE_NEW_LINES);
            shuffle($words);
            $words          = array_slice($words, 0, 500);
            
            //get lorem ipsum words
            $lorem          = file(Plugin::instance()->get_plugin_path() . '/includes/_loremipsum.txt', FILE_IGNORE_NEW_LINES);
            $text           = array_merge($words, $lorem);
            shuffle($text);
            
            //mix them all up and store
            $this->words    = $text;

		}

        /**
         * next three functions draw admin submenus for generation
         * and process form submission
         */
        public function post_menu()
        {
            
            global $_REAL_GET, $_REAL_POST, $_REAL_COOKIE, $_REAL_REQUEST;
            
            if($_REAL_POST) {
                $this->generate_posts($_REAL_POST);                
            }
            
            include Plugin::instance()->get_plugin_path() . 'includes/posts.php';
            
        }
        
        public function term_menu()
        {
            
            global $_REAL_GET, $_REAL_POST, $_REAL_COOKIE, $_REAL_REQUEST;
            
            if($_REAL_POST) {
                $this->generate_terms($_REAL_POST);
            }
            
            $taxonomies = get_taxonomies(); 

            include Plugin::instance()->get_plugin_path() . 'includes/terms.php';
            
        }
        
        public function user_menu()
        {
            
            global $_REAL_GET, $_REAL_POST, $_REAL_COOKIE, $_REAL_REQUEST;
            
            if($_REAL_POST) {
                $this->generate_users($_REAL_POST);
            }
            
            $roles = get_editable_roles();

            include Plugin::instance()->get_plugin_path() . 'includes/users.php';
            
        }
        
        private function generate_posts($args)
        {
            
            $number     = $args['num_posts'];
            $is_gallery = $args['is_gallery'];
            $i          = 1;
            
            $terms_args = array(
                'hide_empty' => false,
                'exclude' => 1
            );
            
            $this->categories   = get_categories($terms_args);
            $this->tags         = get_tags($terms_args);
            
            while( $i <= $number ) {
                
                //params
                $type       = 'post';                
                $cats       = $args['max_categories'];
                $tags       = $args['max_tags'];
                $comments   = $args['comments'];
                $format     = $args['post_format'];
                
                //generate
                $this->generate_post($format, $cats, $tags, $comments);
                
                //next
                $i++;
            }
        }
        
        private function generate_post($format, $cats, $tags, $comments)
        {

            $post_author= $this->get_author();

            $content    = $this->loremipsum($format, $post_author, true, true);

            $title      = $this->get_title();
            $excerpt    = $title;

            //random date - TBD: have settings for this?
            $post_date  = rand(1325376000, 1426705715);
            $date       = date("Y-m-d H:i:s", $post_date);
            
            //add random existing categories and tags
            $cat_ids    = $this->get_terms($cats, $this->categories, 'category', 'term_id');
            $tags_input = $this->get_terms($tags, $this->tags, 'post_tag', 'name');

            
            //save post and get new post_id
            $args = array(
                'post_title'        => $title,
                'post_excerpt'      => $excerpt,
                'post_content'      => $content,
                'post_status'       => 'publish',
                'post_date'         => $date,
                'post_type'         => 'post',
                'post_author'       => $post_author,
                'post_category'     => $cat_ids,
                'tags_input'        => $tags_input
            );
            
            $post_id = wp_insert_post($args);

            //add comments
            if($comments) {
                $this->generate_comments($comments, $post_id, $post_date);
            }
            
            //featured image
            if(in_array($format, array('standard', 'gallery', 'video' )) ) {
                $attach_id = $this->generate_image($post_id, $post_author, 720, 480);
                set_post_thumbnail( $post_id, $attach_id );

            }

            //post format
            set_post_format($post_id, $format);
                
            //add rpg metadata
            add_post_meta($post_id, 'rpg-generated', true);

        }
        
        /*
         * search users and return ID of a random author
         *
         */ 
        private function get_author()
        {
            //random author
            $author_args = array(
                'role' => 'author'  
            );
            
            $authors        = get_users($author_args);
            $author_key     = array_rand($authors, 1);
            $post_author    = $authors[ $author_key ]->ID;
            
            return $post_author;
            
        }
        
        private function get_terms($number, $list, $taxonomy = 'post_tag', $property = 'name')
        {
            if($number) {
                $terms = array_rand($list, $number);
                $term_ids    = array();
                
                if(is_array($terms)) {
                    foreach($terms as $key) {
                        $term = $list[ $key ];
                        $term_ids[] = $term->$property;
                    }
                    
                    if($taxonomy == 'category') {
                        $term_ids = implode(',', array_values($term_ids));
                        $term_ids = explode(',', $term_ids);
                    }
                    
                } else {
                    $term = $list[ $terms ];
                    $term_ids = array($term->$property);
                }
            } else {
                $term_ids = array();
            }
            
            return $term_ids;
            
        }
        
        private function generate_comments($comments, $post_id, $post_date)
        {
           
            //random number between 1 and $comments
            $num_comments       = rand(1, $comments);                
            $random_comments    = array();
            $min_sentences      = 2;
            $max_sentences      = 5;
            $min_words          = 4;
            $max_words          = 12;
            $links              = true;
            $decorate           = true;

            $c = 1;
            while($c <= $num_comments) {
                
                $paragraphs = rand(1, 3);
                $i = 1;
                $comment_content = '';
                while($i <= $paragraphs) {
                    $comment_content .= $this->get_paragraph($min_sentences, $max_sentences, $min_words, $max_words, $links, $decorate);   
                    $i++;
                }
                
                $random_child       = mt_rand(0,1);
                
                if(count($random_comments) && $random_child) {
                    $comment_parent = $random_comments[array_rand($random_comments, 1)];
                    
                    //date of child should be after parent
                    $parent             = get_comment($comment_parent, OBJECT);
                    $parent_date        = strtotime($parent->comment_date);
                    $comment_date_int   = rand($parent_date, time());
                    $comment_date       = date("Y-m-d H:i:s",$comment_date_int);
                    
                } else {
                    $comment_parent = 0;
                    
                    $comment_date_int   = rand($post_date, time());
                    $comment_date       = date("Y-m-d H:i:s",$comment_date_int);
                }
                
                //get random contributor
                $contributor_args = array(
                );
                
                $comment_args = array(
                    'comment_post_ID'   => $post_id,
                    'comment_content'   => $comment_content,
                    'comment_parent'    => $comment_parent,
                    'comment_date'      => $comment_date,
                    'comment_type'      => 'comment',
                    'comment_approved'  => 1

                );
                
                $comment = wp_insert_comment($comment_args);
                $random_comments[] = $comment;

                $c++;   
            }
            
        }
        
        private function generate_image($post_id, $post_author, $width, $height)
        {
            
            $img        = $this->get_image();
            $filename   = $img->filename;
            $file       = $img->file;
            
            // Check image file type
            $wp_filetype = wp_check_filetype( $filename, null );
            
            // Set attachment data
            $attachment = array(
                'post_mime_type' => $wp_filetype['type'],
                'post_title'     => sanitize_file_name( $filename ),
                'post_content'   => '',
                'post_status'    => 'inherit',
                'post_author'    => $post_author
            );
            
            // Create the attachment
            $attach_id = wp_insert_attachment( $attachment, $file, $post_id );
            // Include image.php
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            
            // Define attachment metadata
            $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
            
            // Assign metadata to attachment
            wp_update_attachment_metadata( $attach_id, $attach_data );
            
            return $attach_id;

        }
        
        private function generate_gallery($author)
        {
            
            //number of slides
            $num_slides = rand(5, 10);
            
           // \debug($num_slides);
            $attachments = array();
            
            while($i <= $num_slides) {
                
                $attach_id = $this->generate_image(NULL, $author, 720, 480);
                $attachments[] = $attach_id;
                
                $i++;
            }
            
            $ids = implode(',' , $attachments);
            
            return "[gallery ids=" . $ids . "]";
            
            
        }
        
        private function generate_terms($args)
        {

            $num = $args['num_categories'];
            $taxonomy = $args['term_type'];
            
            $wordlist = file(Plugin::instance()->get_plugin_path() . '/includes/_words.txt', FILE_IGNORE_NEW_LINES);
            
            //get current categories
            $cat_args = array(
                'hide_empty' => false
            );
            
            $categories = get_categories($cat_args);
            
            //remove current categories from $wordlist to avoid duplicates
            foreach($categories as $category) {
                if(($key = array_search($category->slug, $wordlist)) !== false) {
                    unset($wordlist[$key]);
                }
            }
            
            //choose random keys
            $words_keys = array_rand($wordlist, $num);
            
            foreach($words_keys as $key ) {
                
                $word = ucfirst( $wordlist[ $key ] );
                
                //make a category out of this word
                $new = wp_insert_term($word, $taxonomy);
    
            }

        }

        private function generate_users($args)
        {
            
            $num    = $args['num_users'];
            $role   = $args['role'];
            $names_list  = file(Plugin::instance()->get_plugin_path() . '/includes/_names.txt', FILE_IGNORE_NEW_LINES);
            
            $user_args = array(
                'role' => $role
            );
			
			
            $users = get_users($user_args);
            
            //remove current users from $names to avoid duplicates
            foreach($users as $user) {
                if(($key = array_search($user->display_name, $names_list)) !== false) {
                    unset($names_list[$key]);
                }
            }
            
            //choose random keys
            $names_keys = array_rand($names_list, $num);
			
			if(!is_array($names_keys) ){
				$names_keys = array($names_keys);
			}
            
            foreach($names_keys as $key) {
                $name 			= $names_list[ $key ];
                $email_address 	= strtolower($name) . "@example.com";
                $password 		= $this->random_string(16);

                $insert = wp_insert_user(
                    array(
                        'ID'          =>    $user_id,
						'user_email'  => 	$email_address,
						'user_pass'   =>	$password,
						'user_login'  =>	$name,	
                        'nickname'    =>    $name,
                        'name'        =>    $name,
                        'display_name' =>   $name,
						'nickname'	  => 	$name,
						'role'		  =>	$role	
                    )
                );                
            }
        }

        private function random_string($length)
        {
            $key = '';
            $keys = array_merge(range(0, 9), range('a', 'z'));
        
            for ($i = 0; $i < $length; $i++) {
                $key .= $keys[array_rand($keys)];
            }
        
            return $key;
        }
        
        private function loremipsum($format, $author, $links, $decorate)
        {
           
           // $content = $this->get_title();
        
            switch($format) {
                
                case 'gallery':
                    $content = $this->generate_gallery($author);
                    $content .= $this->get_paragraph(1, 2, 4, 12, false, false);
                    
                    break;
                
                case 'aside':
                    //1 short paragraph
                    $content .= $this->get_paragraph(1, 1);
                    break;
                    
                case 'video':
                    //1 short paragraph
                    //1 video
                    break;
                
                case 'image':
                    $attachment_id = $this->generate_image(NULL, $author, 720, 480);
                    $imgurl = wp_get_attachment_url($attachment_id);

                    $content = '<a href="' . $image->src . '"><img src="' . $imgurl . '" />';
                    break;
                
                case 'quote':
                    $content = '<blockquote>' . $this->get_paragraph() . '</blockquote>';
                    break;
                
                case 'link':
                    $content = '<a href="http://"' . $this->random_string() . '.com">' . $this->get_sentence() . '</a>';
                    
                    break;
                
                case 'standard':
                    $paragraphs = rand(2, 6);
                    $num_images = rand(0, 3);
                    $i          = 1;
                    
                    while($i <= $paragraphs) {
                         
                        $content .= $this->get_paragraph(2, 5, 4, 12, $links, $decorate);
                        
                        if($num_images) {
                            $image = mt_rand(0, $paragraphs);

                            if( $image  === 1) {
                                $attachment_id  = $this->generate_image(NULL, $author, 320, 240);
                              //  $img            = get_post($attachment_id);
                                $imgurl         = wp_get_attachment_url($attachment_id);

                                $content .= '<img src="' . $imgurl . '" class="size-medium" />';
                            }
                        }
                        
                        $list = mt_rand(0, $paragraphs);
                        if($list === 1) {
                            
                            $content .= $this->get_list('ul');
                            $content .= $this->get_list('ol');
                            $content .= $this->get_list('dl');
                        }

                        $i++;
                    }
                    
                    break;
                default:
                   break;
            }
            
            return $content;

        }
        
        private function get_list($type)
        {
            
            $list = '';
            
            switch($type) {
                
                case 'ul':
                    $list .= '<ul>';
                    
                    //random # of items
                    $num_items = rand(3, 8);
                    $i = 1;
                    while($i <= $num_items) {
                        
                        $list .= '<li>' . $this->get_sentence(2, 10) . '</li>';
                        $i++;   
                    }

                    $list .= '</ul>';

                    break;
                
                
                case 'ol':
                    $list .= '<ol>';
                    //random # of items
                    $num_items = rand(3, 8);
                    $i = 1;
                    while($i <= $num_items) {
                        
                        $list .= '<li>' . $this->get_sentence(2, 10) . '</li>';
                        
                        $i++;   
                    }

                    $list .= '</ol>';
                    
                    break;
                
                
                case 'dl':
                    $list .= '<dl>';
                    //random # of items
                    $num_items = rand(3, 8);
                    $i = 1;
                    while($i <= $num_items) {
                        $list .= '<dt>' . $this->get_sentence(2, 3) . '</dt>';
                        $list .= '<dd>' . $this->get_sentence(2, 10) . '</dd>';
                        
                        $i++;   
                    }

                    $list .= '</dl>';
                    break;
                
                default:
                    break;
            }
                    
            
            
            return $list;   
        }
        
        private function get_image($width = 720, $height = 480)
        {
            $src = 'http://lorempixel.com/' . $width . '/' . $height . '/'; //720/480/';
            $image_data = file_get_contents($src);
        
            $filename   = $this->random_string(32) . '.jpg';
            
            $upload_dir = wp_upload_dir();

            // Check folder permission and define file location
            if( wp_mkdir_p( $upload_dir['path'] ) ) {
                $file = $upload_dir['path'] . '/' . $filename;
            } else {
                $file = $upload_dir['basedir'] . '/' . $filename;
            }

            // Create the image  file on the server
            $upload = file_put_contents( $file, $image_data );
            
            $img            = new \stdClass();
            $img->filename  = $filename;
            $img->file      = $file;
            $img->src       = $upload_dir['url'] . '/' . $filename;
            
            return $img;

        }
        
        private function get_title()
        {
            return $this->get_sentence(1, 8);   
        }
        
        private function get_sentence($min = 4, $max = 12)
        {

            $num_words      = rand($min, $max);
           // \debug($num_words);
            $sentence_keys  = array_rand($this->words, $num_words);
            $sentence       = '';
            $i              = 1;
            
            if(is_array($sentence_keys)) {
                foreach($sentence_keys as $key) {
                    $sentence .= $this->words[ $key ] . ' ';
                }
            } else {
                $sentence = $this->words[ $sentence_keys ];   
            }
            
            return rtrim(ucfirst($sentence)) . '. ' ;
            
        }
        
        private function get_paragraph($min = 2, $max = 5, $min_words = 4, $max_words = 12, $links = false, $decorate = false)
        {
            $num_sentences = rand($min, $max);
           
            $i = 1;
            $paragraph = '';
            while($i <= $num_sentences) {
                
                $sentence = $this->get_sentence($min_words, $max_words);
                
                if($links) {
                    $linked = mt_rand(0,4);
                }
                
                if($decorate) {
                    $decorated = mt_rand(0, 4);
                }

                if($decorated === 1) {
                    //b i or mark
                    $decoration = $this->decorations[array_rand($this->decorations)];
                    $sentence = '<' . $decoration . '>' . $sentence . '</' . $decoration . '>';
                }
                
                if($linked === 1) {
                    $sentence = '<a href="/">' . $sentence . '</a>';
                } 
                $paragraph .= $sentence;
                
                $i++;
            }
            
            return '<p>' . $paragraph . '</p>';
            //paragraph 2 - 8 sentences   
            
        }
    } 
}