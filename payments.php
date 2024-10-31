<?php 



if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Create a new table class that will extend the WP_List_Table
 */
class junk_user_List_Table extends WP_List_Table
{
    /**
     * Prepare the items for the table to process
     *
     * @return Void
     */
    public function prepare_items()
    {
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
            'username'       => 'Username',
            'firstname'      => 'First name',
            'lastname'       => 'Last name',
            'email'          => 'Email',
            'payment'        => 'Payment',
            'transaction_id' => 'Transaction ID',
            'payment_date'   => 'Date'
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

	
	function no_items() {
	  _e( 'No Payments, dude.' );
	}

    /**
     * Get the table data
     *
     * @return Array
     */
    private function table_data()
    {
        
        global $wpdb;

        $args = array(
                    'meta_query' => array
                        (
                            array(
                                'key' => 'transaction_id',
                                'compare' => 'EXISTS'
                                )
                        )
                    );
        $member_arr = get_users($args);

   

 
        $i=0;
 
        foreach ($member_arr as $user) {
 
            $id[]=$user->id;
            $username[]=$user->user_login;
            $firstname[]=get_user_meta ( $user->ID, 'first_name' , true);
            $lastname[]=get_user_meta ( $user->ID, 'last_name' , true);
            $email[]=$user->user_email;
            $payment[]=get_user_meta ( $user->ID, 'payment' , true);
            $transaction_id[]=get_user_meta ( $user->ID, 'transaction_id' , true);
            $payment_date[]=get_user_meta ( $user->ID, 'payment_date' , true);
 
            $data[] = array(
                    'id'  		       =>   $id[$i],
                    'username'	       =>   $username[$i],
                    'firstname'        =>   $firstname[$i],
                    'lastname' 	       =>   $lastname[$i],
                    'email' 	       =>   $email[$i],
                    'payment'          =>   $payment[$i],
                    'transaction_id'   =>   $transaction_id[$i],
                    'payment_date'    =>   $payment_date[$i]
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
            case 'payment':
            case 'transaction_id':
            case 'payment_date':
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
        $exampleListTable->prepare_items();
        ?>
           <div class="wrap">
              <h2>Payments</h2>
           		 <?php $exampleListTable->display(); ?>
            </div>
        
 