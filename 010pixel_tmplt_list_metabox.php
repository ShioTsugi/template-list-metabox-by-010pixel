<?php
/*
 * Plugin Name: 010Pixel Template List Metabox
 * Plugin URI: http://www.010pixel.com/plugins/010pixel-template-list-metabox/
 * Description: This plugin is to create Template Dropdown List Metabox for Custom Post Types.
 * Author: 010 Pixel
 * Version: 1.1
 * Author URI: http://www.010pixel.com/
 */

	/**
	 *
	 * The plug is consist of 3 classes.
	 *
	 * 1. Template_List_Metabox_Admin_010Pixel
	 * 2. Template_List_Metabox_010Pixel
	 * 3. Load_Template_010Pixel
	 *
	 * The Plugin will check which page user is accessing and load the class accordingly
	 *
	 */

	/*
	 *	V 1.1
	 *	Date: 2015-02-13
	 *	- Removed bug of settings link not displaying in plugin list
	 *	- Added setting page to Settings menu instead of Plugins menu
	 */
?>
<?php
 	// Check if the User is accessing Admin Panel or Front end
	if ( ! is_admin() ) { // User is accessing Front end
	
		$GLOBALS['Load_Template_010Pixel'] = new Load_Template_010Pixel();
		
	} else { // User is accessing Admin Panel
	
		// Create Custom Meta Box
		$GLOBALS['Template_List_Metabox_010Pixel'] = new Template_List_Metabox_010Pixel();
		
		// Manage Template Allowed List Admin Panel
		$GLOBALS['Template_List_Metabox_Admin_010Pixel'] = new Template_List_Metabox_Admin_010Pixel();
		
	}
?>
<?php
	/**
	 *
	 * Template_List_Metabox_010Pixel
	 * ============================================================================
	 * This class is to create metabox for Template List for the Post Types which are
	 * in allowed post types for templates
	 *
	 */
	 
	class Template_List_Metabox_010Pixel {
		
		// Create an Array of Post Types which are allowed to use Templates
		public $metaBoxTitle = 'Template';
		public $PostTypesWithTemplateAllwed = array ();
		
		// Initiate Class
		public function __construct() {
			
			// Add values to Post Types with Allowed Template List from template_post_type_list option
			$this->PostTypesWithTemplateAllwed = get_option('template_post_type_list'); 
			
			// if the value is not array then convert it to array
			if ( !is_array($this->PostTypesWithTemplateAllwed) ) {
				$this->PostTypesWithTemplateAllwed = array($this->PostTypesWithTemplateAllwed);
			}
			
			// Start creating Meta Box
			add_action('add_meta_boxes',array( &$this, 'add_post_template_metabox_template' ));
			
			// Save Data to Database
			add_action('save_post',array( &$this, 'save_post_template' ),10,2);
			
			// Add link in plugins list
			$prefix = is_network_admin() ? 'network_admin_' : '';
			add_filter( $prefix . 'plugin_action_links_' . plugin_basename(__FILE__), array( &$this,'addSettingsLink' ), 10, 2 );
		}
		
		// Add Meta Box to Current Post Type
		public function add_post_template_metabox_template() {
			$currentPostType = get_post_type();
			if ( in_array($currentPostType, $this->PostTypesWithTemplateAllwed) ) {
				add_meta_box('postparentdiv', __($this->metaBoxTitle),  array( &$this, 'post_template_meta_box' ), $currentPostType, 'side', 'core');
			}
		}
		
		// Create Meta Box
		public function post_template_meta_box($post) {
			
			// Check if the Current Post Type is in the Allowed Template Post Types list
			if ( in_array($post->post_type, $this->PostTypesWithTemplateAllwed) ) {
				// Get current page Template Name (if any)
				$template = get_post_meta($post->ID,$post->post_type . '_template',true);
				
				$templateList = '';
				// Security Nonce Key
				$templateList .= '<input type="hidden" name="custom_type_noncename" id="custom_type_noncename" value="' . wp_create_nonce( $post->post_type . $post->ID ) . '" />';
				$templateList .= '<label class="screen-reader-text" for="post_template">' . translate($this->metaBoxTitle) . '</label>';
				$templateList .= '<select name="post_template" id="post_template">';
				$templateList .= '<option value="default">' . translate("Default Template") . '</option>';

				// Calling a Wordpress in-built function to get templates array
				$templates = get_page_templates();
				foreach ( $templates as $template_name => $template_filename ) {
					$templateList .= '<option value="'. $template_filename .'"';
					if ( $template == $template_filename ) { $templateList .= ' selected="selected" '; }
					$templateList .= '>'. $template_name .'</option>';
				}

				$templateList .= '';
				$templateList .= '</select>';
				
				echo $templateList;
			}
		}

		// Save Data to Database
		public function save_post_template($post_id,$post) {
			
		   // verify this came from the our screen and with proper authorization.
			if ( !wp_verify_nonce( @$_POST['custom_type_noncename'], $post->post_type . $post_id )) {
				return $post_id;
			}
			
			// verify if this is an auto save routine. If it is our form has not been submitted, so we dont want to do anything
			if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
				return $post_id;
				
			// Check permissions
			if ( !current_user_can( 'edit_post', $post_id ) )
				return $post_id;
				
 			// Check if the Current Post Type is in the Allowed Template Post Types list and Post Template value is not empty
			if ( in_array($post->post_type, $this->PostTypesWithTemplateAllwed) && !empty($_POST['post_template']) )
			{
				update_post_meta($post->ID,$post->post_type . '_template',$_POST['post_template']);
			}
			
		}
		
		// Add a link to the setting option page
		public static function addSettingsLink( $links ) {
		
			$links[] = '<a href="'.admin_url('plugins.php?page=010pixel_tmplt_list_metabox').'"> '.__( 'Settings', 'sis' ).' </a>';
			
			return $links;
		}
	}

?>
<?php
	/**
	 *
	 * Template_List_Metabox_Admin_010Pixel
	 * ============================================================================
	 * This class will create Admin Page where all the post types will be visible.
	 * Tick the post types for which template list dropdown metabox is needed.
	 *
	 */

	class Template_List_Metabox_Admin_010Pixel {

		// Initiate Class
		public function __construct() {
			add_action('admin_menu', array( &$this, 'add_tmplt_mngr_page') );
			add_action('admin_init', array( &$this, 'tmplt_mngr_serialize' )); 
		}
		
		// Create Menu to access Template List Metabox Admin Page
		public function add_tmplt_mngr_page() {  
		  
			add_options_page(  
				'Template List Metabox',
				'Template List Metabox',
				'administrator',
				'010pixel_tmplt_list_metabox',
				array( &$this, 'template_list_plugin_options' )
			);  
		  
		}
		
		// Create Template Metabox Settings Page
		public function template_list_plugin_options() { 
			?> 
			<div class="wrap"> 
			 
				<?php screen_icon(); ?>
				<h2>Template Allowed Post Types List</h2> 
				<?php settings_errors(); ?> 
				 
				<form method="post" action="options.php"> 
					<?php
						// Load the form
						settings_fields( 'template_post_type_list' );  
						do_settings_sections( 'template_post_type_list' );  
						submit_button();
					?> 
				</form> 
				 
			</div>
			<?php 
		}
		
		// Create Form for Post Type Lists
		public function tmplt_mngr_serialize() { 
		 
			// If the template post tyoe list options don't exist, create them.  
			if( false == get_option( 'template_post_type_list' ) ) {    
				add_option( 'template_post_type_list' );  
			}
		  
			// Register a section
			add_settings_section(  
				'template_page_list_section',
				'Choose Post Types for which you want to allow Page Templates.',
				array( &$this, 'template_page_list_callback' ),
				'template_post_type_list'
			);
			
			// Introduce the fields for Listing Post Types with checkbox
			add_settings_field(   
				'post_types_list',
				'Post Types',
				array( &$this, 'post_types_list_callback' ),
				'template_post_type_list',
				'template_page_list_section'
			); 
			
			// Register the fields with WordPress 
			register_setting( 
				'template_post_type_list', 
				'template_post_type_list' 
			); 
			 
		}
		
		public function template_page_list_callback() { 
			echo '<hr />'; 
		}
		
		// Post Types Checkbox
		public function post_types_list_callback($args) { 
			
			// Read the options collection  
			$options = get_option('template_post_type_list'); 
			
			// Get the Post Types List
			$post_types=get_post_types(array('public' => true),'objects');
			
			$html = '';
			
			foreach ($post_types  as $post_type ) {
				if ( $post_type->name != 'attachment' ) {
					$html .= '<input 
								type="checkbox" 
								id="template_post_type_list['. $post_type->name .']"
								name="template_post_type_list['. $post_type->name .']" 
								value="'. $post_type->name .'" ' . checked($post_type->name, isset($options[$post_type->name]) ? $options[$post_type->name]: '' , false) . '
							/>&nbsp;';   
					$html .= '<label for="template_post_type_list['. $post_type->name .']">' . $post_type->label .'</label><br />';
				}
			}
			 
			echo $html; 
		}

	}
?>
<?php
	/**
	 *
	 * Load_Template_010Pixel
	 * ============================================================================
	 * This class will be loaded in front end. It will check the post type template
	 * and load relevant template page
	 *
	 */
	 
	class Load_Template_010Pixel {
		
		// Initiate Class
		public function __construct() {
			add_filter( 'single_template', array( $this, 'load_custom_template') );
		}
		
		// Load Custom Template
		public function load_custom_template() {
			
			// Get All post types for which templates are allowed
			$PostTypesWithTemplateAllwed = get_option('template_post_type_list'); 
			
			// Get Current Post ID
			$postId = get_the_ID();
			
			// Get Current Post Type
			$postType = get_post_type( $postId );
			
			// If Current Post Type is in the Post Type with Template allowed list
			// then show the post in the chosen template			
			if ( in_array ( $postType, $PostTypesWithTemplateAllwed) ) {
				
				$templateFile = get_post_meta($postId, $postType . '_template', true);
				
				$template = locate_template( $templateFile, false );
				
				return $template;
			}
			
			return false;
		}
	}
?>