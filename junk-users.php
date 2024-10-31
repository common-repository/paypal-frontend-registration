<?php 

global $wpdb;   


if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Create a new table class that will extend the WP_List_Table
 */
class junk_user_List_Table extends WP_List_Table
{

    public function __construct() {

        parent::__construct(
            array(
                'singular' => 'singular_form',
                'plural'   => 'plural_form',
                'ajax'     => false
            )
        );

    }

    /**
     * Prepare the items for the table to process
     *
     * @return Void
     */
    public function prepare_items()
    {
        $this->process_bulk_action();
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();

        $data = $this->table_data();
        usort( $data, array( &$this, 'sort_data' ) );

        $perPage = 20;
        $currentPage = $this->get_pagenum();
        $totalItems = count($data);

        $this->set_pagination_args( array(
            'total_items' => $totalItems,
            'per_page'    => $perPage
        ) );

        $data = array_slice($data,(($currentPage-1)*$perPage),$perPage);

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $data;
        
    }

    /**
     * Override the parent columns method. Defines the columns to use in your listing table
     *
     * @return Array
     */
    public function get_columns()
    {
        $columns = array(
            'cb'    => '<input type="checkbox" />',
            'username'       => 'Username',
            'firstname'      => 'First name',
            'lastname'       => 'Last name',
            'email'          => 'Email',
        );

        return $columns;
    }

    /**
     * Define which columns are hidden
     *
     * @return Array
     */
    public function get_hidden_columns()
    {
        return array();
    }

    /**
     * Define the sortable columns
     *
     * @return Array
     */
    public function get_sortable_columns()
    {
        return array('id' => array('id', false));
    }

    public function get_bulk_actions() {

        return array(
                'delete' => __( 'Delete', 'table-delete' )
        );

    }

    

    public function process_bulk_action() {
         // security check!
        if ( isset( $_POST['_wpnonce'] ) && ! empty( $_POST['_wpnonce'] ) ) {

            $nonce  = filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING );
            $action = 'bulk-' . $this->_args['plural'];
            $junk_users = isset( $_REQUEST['junk_user'] ) ? wp_parse_id_list( wp_unslash( $_REQUEST['junk_user'] ) ) : array();
            


            //print_r($request_ids);
            if ( ! wp_verify_nonce( $nonce, $action ) )
                wp_die( 'Nope! Security check failed!' );

        }
        function delete_junk_user($junk_user_id)
        {
            global $wpdb;   
            $table_name = PRO_TABLE_PREFIX."temp_users";
            $wpdb->show_errors();
            $wpdb->delete( $table_name, array( 'id' => $junk_user_id ) );
            return true;
        } 
        $action = $this->current_action();

        switch ( $action ) 
        {
            case 'delete':
                $i = 0; 
               foreach ( $junk_users as $junk_user_id ) 
               {
                    if (delete_junk_user( $junk_user_id))
                    {
                        $i++;
                    }
                }
                printf( '<div id="message" class="updated notice is-dismissible" style="background: #ff9494;"><p>%d junk user deleted</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>', __(  $i, 'table-subline' ));
                return;

        }

        return;
    }

    function column_cb($item) 
    {
            return sprintf(
                '<input type="checkbox" name="junk_user[]" value="%s" />',$item['id']
            );
    }


    function no_items() {
      _e( 'No Junk Users found, dude.' );
    }

    


    /**
     * Get the table data
     *
     * @return Array
     */
    private function table_data()
    {
        
        global $wpdb;
        $table_name = PRO_TABLE_PREFIX."temp_users";
        $data=array();
        $wk_post=$wpdb->get_results("SELECT id,username,firstname,lastname,email FROM $table_name");
 
        $i=0;
 
        foreach ($wk_post as $wk_posts) {
 
            $id[]=$wk_posts->id;
            $username[]=$wk_posts->username;
            $firstname[]=$wk_posts->firstname;
            $lastname[]=$wk_posts->lastname;
            $email[]=$wk_posts->email;
 
            $data[] = array(
                    'id'        => $id[$i],
                    'username'  =>   $username[$i],
                    'firstname' =>   $firstname[$i],
                    'lastname'  =>   $lastname[$i],
                    'email'     =>   $email[$i]
                    );
 
            $i++;
 
        }
 
        return $data;
        //return $posts;
    }

    /**
     * Define what data to show on each column of the table
     *
     * @param  Array $item        Data
     * @param  String $column_name - Current column name
     *
     * @return Mixed
     */
    public function column_default( $item, $column_name )
    {
        switch( $column_name ) {
            case 'username':
            case 'firstname':
            case 'lastname':
            case 'email':

            return $item[ $column_name ];

            default:
                return print_r( $item, true ) ;
        }
    }

    /**
     * Allows you to sort the data by the variables set in the $_GET
     *
     * @return Mixed
     */
    
}






$exampleListTable = new junk_user_List_Table();

printf( '<div class="wrap" id="wpse-list-table"><h2>%s</h2>', __( 'Junk Users', 'table-title' ) );
printf( '<div class="updated" style=" background-color: #0074A2;"><p><strong style="color:#FFF;">%s</strong></p></div>', __( 'These are the users who tried to register but somehow not completed the payment. If you would like to allow them to register again, please delete the user from the below list.', 'table-subline' ) );




echo '<form id="wpse-list-table-form" method="post">';

$page  = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRIPPED );
$paged = filter_input( INPUT_GET, 'paged', FILTER_SANITIZE_NUMBER_INT );

printf( '<input type="hidden" name="page" value="%s" />', $page );
printf( '<input type="hidden" name="paged" value="%d" />', $paged );

$exampleListTable->prepare_items(); // this will prepare the items AND process the bulk actions
$exampleListTable->display();

echo '</form>';

echo '</div>';



?>