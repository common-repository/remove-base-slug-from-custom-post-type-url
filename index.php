<?php
/*
Plugin Name:  Remove base slug from custom-post-type URL
Description:  (Temporarily stopped !!!!!!!!!! you might also try <a href="https://wordpress.org/plugins/add-hierarchy-parent-to-post/">Add hierarchy (parent) to posts</a> plugin).     You can have links of custom-post-types  without their $slug in the front (for example, 'product' type url will no longer be: example.com/product/ball  , but becomes: example.com/ball;  Useful when you dont want to use default post;   (P.S.  OTHER MUST-HAVE PLUGINS FOR EVERYONE: http://bitly.com/MWPLUGINS  )
Version: 1.3
Author: TazoTodua
Author URI: http://www.protectpages.com/profile
Plugin URI: http://www.protectpages.com/
Donate link: http://paypal.me/tazotodua
*/



define('version__RBSFCU',	 	1.3);
define('pluginpage__RBSFCU',	'custom-post-type-slug-remove-RBSFCU');
define('plugin_settings_page__RBSFCU', 	 admin_url( 'options-general.php'). '?page='.pluginpage__RBSFCU  );
define('chosen_remtype__RBSFCU',		get_option('remove_slug_type_RBSFCU','test') );
define('chosen_regtype__RBSFCU',		get_option('chosen_reg_type_RBSFCU','') );
define('chosen_method__RBSFCU',			get_option('chosen_method_RBSFCU', 'a') );

return;
	
//Activation
	register_activation_hook( __FILE__, 'install__RBSFCU');		function install__RBSFCU()	{	flush_rewrite_rules(); }

//Deactivation
	register_deactivation_hook( __FILE__,'uninstall__RBSFCU');	function uninstall__RBSFCU(){ 	flush_rewrite_rules(); }	


// Register post type
if(chosen_regtype__RBSFCU != "") {

	add_action( 'init', 'mycustopost__RBSFCU');
	
	function mycustopost__RBSFCU() {
		$arr= (stripos(chosen_regtype__RBSFCU,',') !== false) ? array_filter(explode(',', chosen_regtype__RBSFCU)) : array( trim(chosen_regtype__RBSFCU) );
		foreach($arr as $eachArr){
			$args = array('label'		=> __('_'), 'description' => __(''),
						'labels'  		=> array( 'name' =>strtoupper($eachArr) .' pages', 'all_items' =>'All '. strtoupper($eachArr).' pages'),
				'supports'				=> array('title','editor', 'thumbnail', 'author', 'excerpt' , 'comments', 'revisions', 'trackbacks', 'custom-fields', 'page-attributes', 'post-formats' ),
				'taxonomies'			=> array('category','post_tag'),					'hierarchical'	=> true,
				'public'				=> true,	'show_ui'			=> true,	'show_in_menu'	=> true,
				'show_in_nav_menus'		=> true,	'show_in_admin_bar'	=> true,	'menu_position'	=> 14,
				'menu_icon'				=> '',		'can_export'		=> true,	'has_archive'	=> true,
				'exclude_from_search'	=> false,	'publicly_queryable'=> true,	'capability_type'=> 'page',
				//'permalink_epmask'=>EP_PERMALINK, 
				'rewrite' => array('with_front'=>false, 'slug' => $eachArr)
				
			);
			register_post_type( $eachArr, $args );
		}
	}
	
}

	
	
$GLOBALS['array_of_types__RBSFCU']	= array_filter((stripos(chosen_regtype__RBSFCU,',') !== false) ? array_filter(explode(',', chosen_remtype__RBSFCU)) : array( trim(chosen_remtype__RBSFCU) ) );

if(chosen_method__RBSFCU=='a'){	

	add_filter( 'register_post_type_args', 'filter_func__RBSFCU', 10, 2);
	function filter_func__RBSFCU($args=array(),$post_type='' ){
		if(defined("chosen_remtype__RBSFCU") && chosen_remtype__RBSFCU !="" ){	 
			foreach($GLOBALS['array_of_types__RBSFCU']	as $each_ptype){
				if($post_type==$each_ptype){
					//$args['rewrite']['slug']='/';
				}
			}
		}
		return $args;
	}
	
}

if(chosen_method__RBSFCU=='b'){
	function remove_cpt_slug__RBSFCU( $post_link, $post, $leavename ) { 
		foreach($GLOBALS['array_of_types__RBSFCU']	 as $each){
			if ( $each != $post->post_type || 'publish' != $post->post_status ) {
				return $post_link;
			}
			$post_link = str_replace( '/' . $post->post_type . '/', '/', $post_link );
		}
		return $post_link;
	}
	add_filter( 'post_type_link', 'remove_cpt_slug__RBSFCU', 10, 3 );

	function parse_request__RBSFCU( $query ) {
		// Only noop the main query
		if ( ! $query->is_main_query() )
			return;
		// Only noop our very specific rewrite rule match
		if ( 2 != count( $query->query ) || ! isset( $query->query['page'] ) ) {
			return;
		}
		// 'name' will be set if post permalinks are just post_name, otherwise the page rule will match
		if ( ! empty( $query->query['name'] ) ) {
			$query->set( 'post_type', array_merge(array( 'post', 'page' ), $GLOBALS['array_of_types__RBSFCU'])  );
		}
	}
	add_action( 'pre_get_posts', 'parse_request__RBSFCU' );
	
}


	
				/*   in future, may be needed
				add_action('init', 'rewrite__RBSFCU');
				function rewrite__RBSFCU($flush=false) {
					if(isset($_GET['r']))  { vx(get_option('rewrite_rules')); } 
						
					if (is_admin()) return;
					global $wp_rewrite;
					$n=$wp_rewrite->extra_rules_top;
					$wp_rewrite->extra_rules_top=array();
					foreach($n as $key=>$value){
						if(substr($key,0,4) =='geo/'){
							$wp_rewrite->extra_rules_top[substr($key,4)]  =  $value;
						}
						else{
							$wp_rewrite->extra_rules_top[$key] =  $value;
						}
					}
					if($flush) { $wp_rewrite->flush_rules(); } flush_rewrite_rules();
				}
				*/
	
	
	
	
	
//settings page 	
	add_action('admin_menu',  function() {
		add_submenu_page('options-general.php', 'Remove Slug',	'Remove Slug',	'manage_options', pluginpage__RBSFCU,  'my_subfunc1__RBSFCU'); 
		register_setting( 'RBSFCU_settings_group', 'remove_slug_type_RBSFCU');
		register_setting( 'RBSFCU_settings_group', 'chosen_reg_type_RBSFCU');
		register_setting( 'RBSFCU_settings_group', 'chosen_method_RBSFCU');
	} ); 

	function my_subfunc1__RBSFCU(){
		if(isset($_GET['isactivation'])) { echo '<script>alert("If you are using multi-site, you should set these options per sub-site one-by-one");</script>'; }
		
		 flush_rewrite_rules();
?>
<div class="clear"></div>
<div id="welcome-panel" class="welcome-panel">
	<div class="welcome-panel-content">
	<h3>Plugin Settings Page!</h3>
	<p class="about-description">Choose desired options</p>
	<div class="welcome-panel-column-container">
		<div class="welcome-panel-column" style="width:50%;">
			<h4>_</h4>
			<form method="post" action="options.php">
		    <?php settings_fields( 'RBSFCU_settings_group' ); ?>
		    <?php do_settings_sections( 'RBSFCU_settings_group' ); ?>
			<div>
			<code>(p.s. if you already have published 'posts' and want to convert them into 'custom-post-types', use <a href="https://wordpress.org/plugins/convert-post-types/">Convert PostTypes plugin</a>).</code>
			
			<br/> Note !! Using this plugin might break regular posts, so after configuring & saving this plugin, then check if posts/pages are working (viewable) again well. You might also try <a href="https://wordpress.org/plugins/add-hierarchy-parent-to-post/">Add hierarchy (parent) to posts</a> plugin.
			</div>
		    <table class="form-table">
		        <tr valign="top">
		        <th scope="row">Enter post_type you want to remove it's slug (you can use comma separated list)</th>
		        <td><input type="text" name="remove_slug_type_RBSFCU" value="<?php echo trim(get_option('remove_slug_type_RBSFCU')); ?>" /></td>
		        </tr>
		        <tr valign="top">
		        <th scope="row">Use removal method (on some site, one of these works)</th>
		        <td>Default: <input type="radio" value="a" name="chosen_method_RBSFCU" value="<?php echo get_option('chosen_method_RBSFCU'); ?>" <?php echo checked(chosen_method__RBSFCU, 'a');?> /> |  Experimental: <input type="radio" value="b" name="chosen_method_RBSFCU" value="<?php echo get_option('chosen_method_RBSFCU'); ?>"  <?php echo checked(chosen_method__RBSFCU, 'b');?>/></td>
		        </tr>
		        <tr valign="top">
		        <th scope="row">You can add(register) that post type at first (if not already registered)</th>
		        <td><input type="text" name="chosen_reg_type_RBSFCU" value="<?php echo get_option('chosen_reg_type_RBSFCU'); ?>" /></td>
		        </tr>
		    </table>
		    <?php 
			submit_button( 'Save Settings', 'primary', 'wpdocs-save-settings', true,  $attrib= array( 'id' => 'aeop-submit-button' )   );
		    ?>
		    <p>If something doesnt work, go to Settings&lt;Permalinks and click "Save".  If still issues, you can just uninstall plugin and resave permalinks.</p>
			</form>
		</div>
	
	<div class="welcome-panel-column welcome-panel-last">
		<h4>More Actions</h4>
		<ul>
			<li><div class="welcome-icon welcome-widgets-menus">Found this plugin Useful ? <BR/><a href="http://paypal.me/tazotodua">You can donate</a></div></li>
		</ul>
	</div>
	</div>
	</div>
</div>
<?php } 
		
	
	
								
								//===========  links in Plugins list ==========//
								add_filter( "plugin_action_links_".plugin_basename( __FILE__ ), function ( $links ) {   $links[] = '<a href="'.plugin_settings_page__RBSFCU.'">Settings</a>'; $links[] = '<a href="http://paypal.me/tazotodua">Donate</a>';  return $links; } );
								//REDIRECT SETTINGS PAGE (after activation)
								add_action( 'activated_plugin', function($plugin ) { if( $plugin == plugin_basename( __FILE__ ) ) { exit( wp_redirect( plugin_settings_page__RBSFCU.'&isactivation'  ) ); } } );
?>