<?php

/*
* Creating a function to create our CPT
*/
 
function video_post_type() {
 
// Set UI labels for Custom Post Type
    $labels = array(
        'name'                => _x( 'Videos', 'Post Type General Name', 'twentythirteen' ),
        'singular_name'       => _x( 'Video', 'Post Type Singular Name', 'twentythirteen' ),
        'menu_name'           => __( 'Videos', 'twentythirteen' ),
        'parent_item_colon'   => __( 'Parent Video', 'twentythirteen' ),
        'all_items'           => __( 'All Videos', 'twentythirteen' ),
        'view_item'           => __( 'View Video', 'twentythirteen' ),
        'add_new_item'        => __( 'Add New Video', 'twentythirteen' ),
        'add_new'             => __( 'Add New', 'twentythirteen' ),
        'edit_item'           => __( 'Edit Video', 'twentythirteen' ),
        'update_item'         => __( 'Update Video', 'twentythirteen' ),
        'search_items'        => __( 'Search Video', 'twentythirteen' ),
        'not_found'           => __( 'Not Found', 'twentythirteen' ),
        'not_found_in_trash'  => __( 'Not found in Trash', 'twentythirteen' ),
    );
     
// Set other options for Custom Post Type
     
    $args = array(
        'label'               => __( 'videos', 'twentythirteen' ),
        'description'         => __( 'Video news and reviews', 'twentythirteen' ),
        'labels'              => $labels,
        // Features this CPT supports in Post Editor
        'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions' ),
        // You can associate this CPT with a taxonomy or custom taxonomy. 
        'taxonomies'          => array( 'genres' ),
        /* A hierarchical CPT is like Pages and can have
        * Parent and child items. A non-hierarchical CPT
        * is like Posts.
        */ 
        'hierarchical'        => false,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_nav_menus'   => true,
        'show_in_admin_bar'   => true,
        'menu_position'       => 5,
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'capability_type'     => 'page',
    );
     
    // Registering your Custom Post Type
    register_post_type( 'videos', $args );


}
 
/* Hook into the 'init' action so that the function
* Containing our post type registration is not 
* unnecessarily executed. 
*/
 
add_action( 'init', 'video_post_type', 0 );

function videos_transcoding_menu() { 
    add_submenu_page(
    	'edit.php?post_type=videos', 
    	'Transcoding', 
    	'Transcoding', 
    	'manage_options', 
    	'transcoding-list',
    	'tt_render_list_page'
    ); 
}
add_action('admin_menu', 'videos_transcoding_menu'); 

// function my_custom_submenu_page_content() {
//     echo '<div class="wrap">';
//         echo '<h2>Transcoding</h2>';
//     echo '</div>';
// }

add_filter('pre_option_link_manager_enabled', '__return_true');
/*
  Plugin Name: WP Admin Custom List Table
 */

if (!class_exists('WP_List_Table')) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Transcoding_List_Table extends WP_List_Table {

    function __construct() {
        global $status, $page;

        //Set parent defaults
        parent::__construct(array(
            'singular' => 'Nice Link', //singular name of the listed records
            'plural' => 'Nice Links', //plural name of the listed records
            'ajax' => false        //does this table support ajax?
        ));
    }

    function column_default($item, $column_name) {
        switch ($column_name) {
            case 'id':
            case 'attachment_id':
                return $item->$column_name;
            default:
                return $item->$column_name;
                //return "col name = $column_name , " . print_r($item, true); //Show the whole array for troubleshooting purposes
        }
    }

    function get_columns() {
        return $columns = array(
            'id' => __('ID'),
            'attachment_id' => __('attachment_id'),
            'resize' => __('resize'),
            'path' => __('path'),
            'converted' => __('converted')
        );
    }
  
    function prepare_items() {
        global $wpdb; //This is used only if making any database queries

        /* -- Preparing your query -- */
        $table_name = $wpdb->prefix . 'streampress_videos';
        $query = "SELECT * FROM $table_name";

        //$query = "SELECT * FROM $wpdb->streampress_videos";

        /* -- Ordering parameters -- */
        //Parameters that are going to be used to order the result
        $orderby = !empty($_GET["orderby"]) ? mysql_real_escape_string($_GET["orderby"]) : 'ASC';
        $order = !empty($_GET["order"]) ? mysql_real_escape_string($_GET["order"]) : '';
        if (!empty($orderby) & !empty($order)) {
            $query.=' ORDER BY ' . $orderby . ' ' . $order;
        }
        //

        $totalitems = $wpdb->query($query);

        /**
         * First, lets decide how many records per page to show
         */
        $perpage = 5;

        //Which page is this?
        $paged = !empty($_GET["paged"]) ? mysql_real_escape_string($_GET["paged"]) : '';
        //Page Number
        if (empty($paged) || !is_numeric($paged) || $paged <= 0) {
            $paged = 1;
        }


        //How many pages do we have in total?
        $totalpages = ceil($totalitems / $perpage);
        //adjust the query to take pagination into account
        if (!empty($paged) && !empty($perpage)) {
            $offset = ($paged - 1) * $perpage;
            $query.=' LIMIT ' . (int) $offset . ',' . (int) $perpage;
        }

        /* -- Register the pagination -- */
        $this->set_pagination_args(array(
            "total_items" => $totalitems,
            "total_pages" => $totalpages,
            "per_page" => $perpage,
        ));

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = array();

        $this->_column_headers = array($columns, $hidden, $sortable);

        $this->items = $wpdb->get_results($query);
    }

}

// function tt_add_menu_items() {
//     add_menu_page('Example Plugin List Table', 'List Table Example', 'activate_plugins', 'tt_list_test', 'tt_render_list_page');
// }

// add_action('admin_menu', 'tt_add_menu_items');

function tt_render_list_page() {

    //Create an instance of our package class...
    $testListTable = new Transcoding_List_Table();
    //Fetch, prepare, sort, and filter our data...
    $testListTable->prepare_items();
    ?>
    <div class="wrap">

        <div id="icon-users" class="icon32"><br/></div>
        <h2>Transcoding Queue</h2>       

        <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
        <form id="movies-filter" method="get">
            <!-- For plugins, we also need to ensure that the form posts back to our current page -->
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
            <!-- Now we can render the completed list table -->
    <?php $testListTable->display() ?>
        </form>

    </div>
    <?php }
?>