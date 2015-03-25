<?php
/**
 * RPG Settings class.
 */

namespace RPG;

// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/*
 * RPG\Settings:
 * singleton class 
 * - initialization
 * - hooks
 * - libs
 *
 */

if ( ! class_exists( 'RPG\Settings' ) ) {
        
    /**
	 * RPG Settings
	 *
	 */
	class Settings {

        public $name        = "Settings";
        
        /**
		 * Notices to be displayed in the admin
		 * @var array
		 */
		protected $notices = array();
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
			$this->init();
            			
		}

        /* 

         */
        public function init()
        {
            
            
        }

        public function menu()
        {
            
            echo "Settings";
            
        }
    }
    
}