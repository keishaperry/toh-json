<?php

class Toh_Json_Bonus_List_Table extends WP_List_Table {
 function __construct() {
    parent::__construct( array(
   'singular'=> 'wp_list_text_bonus', //Singular label
   'plural' => 'wp_list_test_bonuses', //plural label, also this well be one of the table css class
   'ajax'   => false //We won't support Ajax for this table
   ) );
 }


/**
 * Add extra markup in the toolbars before or after the list
 * @param string $which, helps you decide if you add the markup after (bottom) or before (top) the list
 */
function extra_tablenav( $which ) {
    if ( $which == "top" ){
       //The code that goes before the table is here
       echo"Hello, I'm before the table";
    }
    if ( $which == "bottom" ){
       //The code that goes after the table is there
       echo"Hi, I'm after the table";
    }
 }

 /**
 * Define the columns that are going to be used in the table
 * @return array $columns, the array of columns to use with the table
 */
function get_columns() {
    return $columns= array(
       'col_link_id'=>__('ID'),
       'col_link_name'=>__('Name'),
       'col_link_url'=>__('Url'),
       'col_link_description'=>__('Description'),
       'col_link_visible'=>__('Visible')
    );
 }
 /**
 * Decide which columns to activate the sorting functionality on
 * @return array $sortable, the array of columns that can be sorted by the user
 */
public function get_sortable_columns() {
    return $sortable = array(
       'col_link_id'=>'link_id',
       'col_link_name'=>'link_name',
       'col_link_visible'=>'link_visible'
    );
 }
/**
 * Prepare the table with different parameters, pagination, columns and table elements
 */
function prepare_items() {
    global $_wp_column_headers;
    $screen = get_current_screen();
 
    $query = get_posts( array(
        'numberposts' => -1,
        'post_type'   => array('toh_bonus'),
        'post_status'   => array('pending','draft','auto-draft'),
        'fields'      => array('ids','title'),
    ) );
 
    /* -- Ordering parameters -- */
        //Parameters that are going to be used to order the result
        $orderby = !empty($_GET["orderby"]) ? mysql_real_escape_string($_GET["orderby"]) : 'ASC';
        $order = !empty($_GET["order"]) ? mysql_real_escape_string($_GET["order"]) : ’;
        if(!empty($orderby) & !empty($order)){ $query.=' ORDER BY '.$orderby.' '.$order; }
 
    /* -- Pagination parameters -- */
         //Number of elements in your table?
         $totalitems = $wpdb->query($query); //return the total number of affected rows
         //How many to display per page?
         $perpage = 5;
         //Which page is this?
         $paged = !empty($_GET["paged"]) ? mysql_real_escape_string($_GET["paged"]) : ’;
         //Page Number
         if(empty($paged) || !is_numeric($paged) || $paged<=0 ){ $paged=1; } //How many pages do we have in total? 
         $totalpages = ceil($totalitems/$perpage); //adjust the query to take pagination into account 
         if(!empty($paged) && !empty($perpage)){ $offset=($paged-1)*$perpage; $query.=' LIMIT '.(int)$offset.','.(int)$perpage; } 
         /* -- Register the pagination -- */ $this->set_pagination_args( array(
          "total_items" => $totalitems,
          "total_pages" => $totalpages,
          "per_page" => $perpage,
       ) );
       //The pagination links are automatically built according to those parameters
 
    /* -- Register the Columns -- */
       $columns = $this->get_columns();
       $_wp_column_headers[$screen->id]=$columns;
 
    /* -- Fetch the items -- */
       $this->items = $wpdb->get_results($query);
 }

/**
 * Display the rows of records in the table
 * @return string, echo the markup of the rows
 */
function display_rows() {

    //Get the records registered in the prepare_items method
    $records = $this->items;
 
    //Get the columns registered in the get_columns and get_sortable_columns methods
    list( $columns, $hidden ) = $this->get_column_info();
 
    //Loop for each record
    if(!empty($records)){foreach($records as $rec){
 
       //Open the line
         echo '< tr id="record_'.$rec->link_id.'">';
       foreach ( $columns as $column_name => $column_display_name ) {
 
          //Style attributes for each col
          $class = "class='$column_name column-$column_name'";
          $style = "";
          if ( in_array( $column_name, $hidden ) ) $style = ' style="display:none;"';
          $attributes = $class . $style;
 
          //edit link
          $editlink  = '/wp-admin/link.php?action=edit&link_id='.(int)$rec->link_id;
 
          //Display the cell
          switch ( $column_name ) {
             case "col_link_id":  echo '< td '.$attributes.'>'.stripslashes($rec->link_id).'< /td>';   break;
             case "col_link_name": echo '< td '.$attributes.'>'.stripslashes($rec->link_name).'< /td>'; break;
             case "col_link_url": echo '< td '.$attributes.'>'.stripslashes($rec->link_url).'< /td>'; break;
             case "col_link_description": echo '< td '.$attributes.'>'.$rec->link_description.'< /td>'; break;
             case "col_link_visible": echo '< td '.$attributes.'>'.$rec->link_visible.'< /td>'; break;
          }
       }
 
       //Close the line
       echo'< /tr>';
    }}
 }

}
