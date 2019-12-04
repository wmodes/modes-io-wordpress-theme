<?php
/**
 * Modes-io-understap Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package modes-io-understap
 */

// Increase maximum file size
@ini_set( 'upload_max_size' , '64M' );
@ini_set( 'post_max_size', '64M');
@ini_set( 'max_execution_time', '300' );

//**********************************************************
//
// Custom templates
//
//**********************************************************/

// We don't need to do one damn thing here to add custom templates.
// We don't have to hook into single_template to identify those files.
// We don't have to create a custom taxonomy.
//
// All we have to do is create a template file with the following 
// at the top in php tags:
//
// /*
//  * Template Name: Screen
//  * Template Post Type: post, page
//  */
//   
// get_header();
//
// At that point, it will be read by WP as a template file, and will
// show up in the "Post Attributes" or "Page Attributes" section of 
// the post or page. This has been a feature of WP for pages since forever,
// and for posts since 4.7 in 2016.


//**********************************************************
//
// Enqueue scripts and styles.
//
//**********************************************************/

function modes_io_theme_enqueue_styles() {
	wp_enqueue_style( 'understrap-style', 
        get_template_directory_uri() . '/style.css' );
	wp_enqueue_style( 'modes-io-understap-style',
		get_stylesheet_directory_uri() . '/style.css',
		array( 'understrap-style' )
    );
}
add_action( 'wp_enqueue_scripts', 'modes_io_theme_enqueue_styles' );

function modes_io_theme_enqueue_scripts() {
    wp_enqueue_script( 'modes-io-understrap-js', 
        get_stylesheet_directory_uri() . '/js/theme.js', 
        array( 'jquery' ), '', true 
    );
}
add_action( 'wp_enqueue_scripts', 'modes_io_theme_enqueue_scripts' );

function modes_io_after_theme_setup() {
    // Add support for wide images
    add_theme_support( 'align-wide' );
    // Remove parent theme's excerpt generation function
    remove_filter( 'wp_trim_excerpt', 'understrap_all_excerpts_get_more_link' );
    // Add editor theme file
    add_editor_style( 'editor-style.css' );
    // add default "Link to:" for media 
    // doesn't work with Gutenberg blocks
    // update_option( 'image_default_link_type', 'file' );
}
add_action( 'after_setup_theme', 'modes_io_after_theme_setup' );

//*********************************************************
//
// Presentation of Titles, Excerpts and Read More
//
//*********************************************************

// Term Title: remove "Category", "Tag", etc
//
add_filter( 'get_the_archive_title', function ($title) {    
    if ( is_category() ) {    
            $title = single_cat_title( '', false );    
        } elseif ( is_tag() ) {    
            $title = single_tag_title( '', false );    
        } elseif ( is_author() ) {    
            $title = '<span class="vcard">' . get_the_author() . '</span>' ;    
        } elseif ( is_tax() ) { //for custom post types
            $title = sprintf( __( '%1$s' ), single_term_title( '', false ) );
        }    
    return $title;    
});

// Excerpt: change the length
//
function mytheme_custom_excerpt_length( $length ) {
    return 30;
}
add_filter( 'excerpt_length', 'mytheme_custom_excerpt_length', 999 );

// Read More: Replace with simple elipses
//
// New "Read More" link replaces old
//  Note that we removed the old parent theme's filter above
function modes_io_excerpts_get_more_link( $post_excerpt ) {
    if ( ! is_admin() ) {
        $post_excerpt = $post_excerpt . '...';
    }
    return $post_excerpt;
}
add_filter('wp_trim_excerpt', 'modes_io_excerpts_get_more_link');


//*********************************************************
//
// Category Importance
//
//*********************************************************

// cat_import: Add the field into the add and edit forms
//
function new_category_fields($term) {
    // we check the name of the action because we need to have different output
    // if you have other taxonomy name, replace category with the name of your taxonomy. ex: book_add_form_fields, book_edit_form_fields
    if (current_filter() == 'category_edit_form_fields') {
        $cat_import = get_term_meta($term->term_id, 'cat_import', true);
        ?>
        <tr class="form-field">
            <th valign="top" scope="row"><label for="term_fields[cat_import]"><?php _e('Importance'); ?></label></th>
            <td>
                <input type="text" size="40" value="<?php echo esc_attr($cat_import); ?>" id="term_fields[cat_import]" name="term_fields[cat_import]"><br/>
                <span class="description"><?php _e('Please provide category importance'); ?></span>
            </td>
        </tr>   
    <?php } elseif (current_filter() == 'category_add_form_fields') {
        ?>
        <div class="form-field">
            <label for="term_fields[cat_import]"><?php _e('Importance'); ?></label>
            <input type="text" size="40" value="" id="term_fields[cat_import]" name="term_fields[cat_import]">
            <p class="description"><?php _e('Please provide category importance'); ?></p>
        </div>  
    <?php
    }
}
// Add the fields, using our callback function  
// if you have other taxonomy name, replace category with the name of your taxonomy. ex: book_add_form_fields, book_edit_form_fields
add_action('category_add_form_fields', 'new_category_fields', 10, 2);
add_action('category_edit_form_fields', 'new_category_fields', 10, 2);

// cat_import: Save the fields values into options
//
function new_save_category_fields($term_id) {
    if (!isset($_POST['term_fields'])) {
        return;
    }

    foreach ($_POST['term_fields'] as $key => $value) {
        update_term_meta($term_id, $key, sanitize_text_field($value));
    }
}
// Save the fields values, using our callback function
// if you have other taxonomy name, replace category with the name of your taxonomy. ex: edited_book, create_book
add_action('edited_category', 'new_save_category_fields', 10, 2);
add_action('create_category', 'new_save_category_fields', 10, 2);

// cat_import: Example 
// When we use this somewhere, get the saved info by using get_term_meta 
// built-in function:
//
// get_term_meta($term_id, 'cat_import', true);


//*********************************************************
//
// Post Importance
//
//*********************************************************

// importance: Add columns to management page
//
function add_columns( $columns ) {
    $columns['importance'] = 'Importance';
    $columns['calc_import'] = 'Weighted';
    return $columns;
}
add_filter( 'manage_post_posts_columns', 'add_columns' ); 

// importance: Set content for columns in management page
//
function columns_content( $column_name, $post_id ) {
    if ( $column_name == 'importance' ) {
        $importance = get_post_meta( $post_id, 'importance', true );
        if ( $importance ) {
            echo esc_html( $importance );
        } else {
            esc_html_e( 'N/A', 'importance' );
        }
    }
    elseif ( $column_name == 'calc_import' ) {
        $weighted = get_post_meta( $post_id, 'calc_import', true );
        if ( $weighted ) {
            echo esc_html( $weighted );
        } else {
            esc_html_e( 'N/A', 'importance' );
        }
    }
}
add_action( 'manage_posts_custom_column', 'columns_content', 10, 2 );

// importance: add to quick edit screen
//
function quick_edit_add( $column_name, $post_type ) {
    if ( 'importance' != $column_name ) {
        return;
    }
    printf( '
        <input type="text" name="importance" class="importance" value=""> %s',
        'Importance'
    );
}
add_action( 'quick_edit_custom_box', 'quick_edit_add', 10, 2 );

// importance: Save quick edit data
//
function save_post_import( $post_id ) {
    // if we are autosaving, do nothing
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return $post_id;
    }
    // if author doesn't have privs, do nothing
    if ( ! current_user_can( 'edit_post', $post_id )) {
        return $post_id;
    }
    // if we are on the edit.php screen
    $current_screen = get_current_screen();
    // if ( 'edit' === $current_screen->parent_base ) {
    // if ( $current_screen->id == 'edit-post' ) {
    if (isset($_POST['importance']) ) {
        $post_import = empty( $_POST['importance'] ) ? 10 : $_POST['importance'];
        // $post_import = $_POST['importance'];
        update_post_meta( $post_id, 'importance', $post_import );
    }
    // we automatically calculate new calc_import values
    update_calc_import( $post_id );
}
add_action( 'save_post', 'save_post_import' );


// importance: Write javascript function to set checked to importance field
//
function quick_edit_javascript() {
    global $current_screen;
    if ( 'post' != $current_screen->post_type ) {
        return;
    }
    print ('
        <script type="text/javascript">
            function field_importance(fieldValue) {
                inlineEditPost.revert();
                jQuery(".importance").val(fieldValue ? fieldValue : 10);
            }
        </script>
    ');
}
add_action( 'admin_footer', 'quick_edit_javascript' );

// importance: Pass importance data to javascript function
//
function expand_quick_edit_link( $actions, $post ) {
    global $current_screen;
    if ( 'post' != $current_screen->post_type ) {
        return $actions;
    }
    $data                               = empty( $post->importance ) ? 10 : $post->importance;
    $actions['inline hide-if-no-js']    = '<a href="#" class="editinline" title="';
    $actions['inline hide-if-no-js']    .= esc_attr( 'Edit this item inline' ) . '"';
    $actions['inline hide-if-no-js']    .= " onclick=\"field_importance('{$data}')\" >";
    $actions['inline hide-if-no-js']    .= 'Quick Edit';
    $actions['inline hide-if-no-js']    .= '</a>';
    return $actions;
}
add_filter( 'post_row_actions', 'expand_quick_edit_link', 10, 2 );


//*********************************************************
//
// Calculated (weighted) Importance
//
//*********************************************************

/**
 * is_edit_page 
 * function to check if the current page is a post edit page
 * 
 * @author Ohad Raz <admin@bainternet.info>
 * 
 * @param  string  $new_edit what page to check for accepts new - new post page ,edit - edit post page, null for either
 * @return boolean
 */
function is_edit_page($new_edit = null){
    global $pagenow;
    //make sure we are on the backend
    if (!is_admin()) return false;
    // is this an edit or add page?
    if($new_edit == "edit")
        return in_array( $pagenow, array( 'post.php',  ) );
    elseif($new_edit == "new") //check for new post page
        return in_array( $pagenow, array( 'post-new.php' ) );
    else //check for either new or edit
        return in_array( $pagenow, array( 'post.php', 'post-new.php' ) );
}

// calc_import: Update calculated importance for a post
//
function update_calc_import( $post_id ) {
    if (! is_edit_page('new')){
        // get importance from post
        $post_import = get_post_meta( $post_id, 'importance', true );
        if ( $post_import == '' ) {
            $post_import = 10;
            update_post_meta( $post_id, 'importance', $post_import);
        }
        // get cat_import from category
        $term_list = wp_get_post_terms($post_id, 'category', ['fields' => 'all']);
        foreach($term_list as $term) {
            if( get_post_meta($post_id, '_yoast_wpseo_primary_category', true) == $term->term_id ) {
                $name =  $term->name;
                // print_r( $term );
                $link = get_category_link( $term->term_id );
            }
        }
        $cat_import = get_term_meta($term->term_id, 'cat_import', true);
        if ( ! $cat_import) {
            $cat_import = 0;
            update_term_meta($term->term_id, 'cat_import', $cat_import);
        }
        // calculate calc_import
        $calc_import = $post_import * $cat_import / 100 ;
        // update meta data for post
        update_post_meta( $post_id, 'calc_import', $calc_import );
    }
}

// calc_import: Add bulk actions to post admin screen
//
function modes_io_add_bulk_actions( $bulk_array ) {
    $bulk_array['modes_io_update_calc_import'] = 'Update Weighted Importance';
    return $bulk_array;
}
add_filter( 'bulk_actions-edit-post', 'modes_io_add_bulk_actions' );

// calc_import: handle our new bulk actions
//
function modes_io_bulk_action_handler( $redirect, $doaction, $object_ids ) {
    // let's remove query args first
    $redirect = remove_query_arg( array( 'modes_io_calc_updated' ), $redirect );
    // do something for Update Calc bulk action
    if ( $doaction == 'modes_io_update_calc_import' ) {
        foreach ( $object_ids as $post_id ) {
            update_calc_import( $post_id );
        }
        // do not forget to add query args to URL because we will show notices later
        $redirect = add_query_arg(
            'modes_io_calc_updated', // just a parameter for URL (we will use $_GET['misha_make_draft_done'] )
            count( $object_ids ), // parameter value - how much posts have been affected
            $redirect );
 
    }
    return $redirect;
}
add_filter( 'handle_bulk_actions-edit-post', 'modes_io_bulk_action_handler', 10, 3 );

// Provide custom notices for bulk actions
//
function modes_io_bulk_action_notices() {
    // first of all we have to make a message,
    if( ! empty( $_REQUEST['modes_io_calc_updated'] ) ) {
        printf( '<div id="message" class="updated notice is-dismissible"><p>' .
            _n( 'Calculated weight of %s post has been changed.',
            'Calculated weights of %s posts have been changed.',
            intval( $_REQUEST['modes_io_calc_updated'] )
        ) . '</p></div>', intval( $_REQUEST['modes_io_calc_updated'] ) );
    }
}
add_action( 'admin_notices', 'modes_io_bulk_action_notices' );

// TODO: Add cat_import to category table 
//


//*********************************************************
//
// New sort order
//
//*********************************************************

// Before we display archive or home, update calculated importance
// and then sort by that field, with a secondary sort on updated date
//
function sort_by_calc_and_date($query){
    if(is_archive() or is_home()) {
        // Deal with diff number of posts on page 1
        //
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        // are we on page one?
        if ( $paged == 1 ) {
            $ppp = 20;
            $offset = 0;
        }
        else {
            $ppp = 21;
            $offset = 20 + ($ppp * ($paged - 2));
        }
        // If this is not a blog, we sort it one way 
        //
        if ( ! is_category( 'blog' )) {
            // now sort by that calc_import and date
            $query->set(
                'meta_query',
                array(
                    'relation' => 'OR',
                        array(
                            'key' => 'calc_import', 
                            'compare' => 'NOT EXISTS'
                        ),
                        array(
                            'key' => 'calc_import', 
                            'compare' => '!=',
                            'value' => '0'
                        )
                )
            );
            // sort first by importance and then by modified date
            $query->set('orderby', 'meta_value_num modified');
            $query->set('order', 'DESC');
        }
        // otherwise we are a blog, where we sort by DESC date
        //
        else {
            $query->set('orderby', 'date');
            $query->set('order', 'DESC');
        }
        // only posts that are published
        $query->set('post_status', 'publish');
        $query->set('posts_per_page', $ppp);
        $query->set('offset', $offset);
    }   
};
// sort the posts first
add_action( 'pre_get_posts', 'sort_by_calc_and_date'); 



//*********************************************************
//
// Primary category button for front and archive pages
//
//*********************************************************

// Helper: Get the primary category if one exists 
//
function get_category_button($post_id) {
    $term_list = wp_get_post_terms($post_id, 'category', ['fields' => 'all']);
    foreach($term_list as $term) {
        if( get_post_meta($post_id, '_yoast_wpseo_primary_category', true) == $term->term_id ) {
            $name =  $term->name;
            // print_r( $term );
            $link = get_category_link( $term->term_id );
        }
    }
    if ($name) {
        printf ('<a class="btn btn-secondary cat-link-btn" href="%s" role="button" style="display:none;">%s</a>', $link, $name);
    }
};


