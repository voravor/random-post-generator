<?php
/**
 * Main RPG class.
 */

namespace RPG;

// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/*
 * RPG\Plugin:
 * singleton class 
 * - initialization
 * - hooks
 * - libs
 *
 */

if ( ! class_exists( 'RPG\Plugin' ) ) {
        
    /**
	 * RPG Plugin
	 *
	 */
	class Plugin {
		
		const VERSION             = '0.0.1';
        
        public $name        = "RPG";
        
        /**
		 * Notices to be displayed in the admin
		 * @var array
		 */
		protected $notices = array();
        
        //needed for path manipulation
        public $plugin_dir;
		public $plugin_path;
		public $plugin_url;
		public $plugin_name;
        
        protected static $instance;
       
                
        /**
		 * Static Singleton Factory Method
		 * @return App
		 */
		public static function instance()
        {
			if ( ! isset( self::$instance ) ) {
				$className      = __CLASS__;
				self::$instance = new $className;
			}

			return self::$instance;
		}
        
        /**
		 * Initializes plugin variables and sets up WordPress hooks/actions.
		 */
		protected function __construct()
        {
			$this->plugin_path = trailingslashit( dirname( dirname( __FILE__ ) ) );
			$this->plugin_dir  = trailingslashit( basename( $this->plugin_path ) );
			$this->plugin_url  = plugins_url( $this->plugin_dir );
            
			add_action( 'init', array( $this, 'load_text_domain' ), 1 );

			$this->add_hooks();
            			
		}
        
        
        /* void, no args
         * called when plugin is activated
         *
         * @return void
         */
        public function install()
        {
            \debug( "install");   
        }
        
        /* void, no args
         * called when plugin is de-activated
         * useful for cleanup
         */
        public function uninstall()
        {
            \debug("uninstall");   
        }
        
        /**
		 * Load the text domain. Not utilized yet...
		 * Text domains: for i18n/l10n
		 *
		 * @return void
		 */
		public function load_text_domain()
        {
			load_plugin_textdomain( 'random_post_generator', false, $this->plugin_dir . 'lang/' );
		}
        
        /* void, no args
         * just returns the file path to this plugin
         *
         * @return void
         */
        public function get_plugin_path()
        {
            return $this->plugin_path;   
        }
        
        /**
		 * Add filters and actions, all in one place
		 * If you add more filters/actions, put them here for cleanliness
		 * void, no args
		 *
		 * @return void
		 */
		protected function add_hooks()
        {
			add_action( 'init', array( $this, 'init' ), 10 );            
			add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
                        
            //styles and js
        //    add_action('wp_head', array($this, 'head') );
        //    add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));

           // add_action( 'admin_head', array(Content::instance(),'add_script_to_admin_head') );
           
        
        }

        
        /* Init, called in wp init hook
         * void, no args
         *
         * sets up data types, e.g., post types and taxonomies
         *
         */
        public function init()
        {
            $this->plugin_name = __( 'Random Post Generator', 'random-post-generator' );
            
        }
        
        /*
         * void, no args
         *
         * creates Helium admin menu and submenu pages
         *
         */
        public function add_admin_menu()
        {
            
            add_menu_page( 'RPG', 'RPG', 'administrator', 'rpg-menu', array($this, 'admin_menu'), '', NULL );
            add_submenu_page('rpg-menu', 'RPG Settings', 'Settings', 'administrator', 'rpg-settings', array(Settings::instance(), 'menu'));
            add_submenu_page('rpg-menu', 'RPG Posts', 'Posts', 'administrator', 'rpg-posts', array(Generator::instance(), 'post_menu'));
            add_submenu_page('rpg-menu', 'RPG Terms', 'Terms', 'administrator', 'rpg-terms', array(Generator::instance(), 'term_menu'));
            add_submenu_page('rpg-menu', 'RPG Users', 'Users', 'administrator', 'rpg-users', array(Generator::instance(), 'user_menu'));

        }
        
        /* main Helium Admin menu page
         *
         */
        public function admin_menu()
        {
            include $this->get_plugin_path() . '/includes/welcome.php';  
        }


        
    }
    
}