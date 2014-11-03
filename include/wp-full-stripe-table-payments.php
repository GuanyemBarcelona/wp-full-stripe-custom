<?php

class WPFS_Payments_Table extends WP_List_Table
{
    function __construct()
    {
        parent::__construct(array(
            'singular' => 'Payment', //Singular label
            'plural' => 'Payments', //plural label, also this well be one of the table css class
            'ajax' => false //We won't support Ajax for this table
        ));
    }

    /**
     * Add extra markup in the toolbars before or after the list
     * @param string $which, helps you decide if you add the markup after (bottom) or before (top) the list
     */
    function extra_tablenav($which)
    {
        if ($which == "top")
        {
            //The code that goes before the table is here
            echo '<div class="wrap">';
        }
        if ($which == "bottom")
        {
            //The code that goes after the table is there
            echo '</div>';
        }
    }

    /**
     * Define the columns that are going to be used in the table
     * @return array $columns, the array of columns to use with the table
     */
    function get_columns()
    {
        return $columns = array(
            'eventID' => __('Stripe ID'),
            'description' => __('Description'),
            'paid' => __('Paid'),
            'livemode' => __('Live mode'),
            'amount' => __('Amount'),
            'fee' => __('Stripe Fee'),
            'address' => __('Address'),
            'created' => __('Date')
        );
    }

    /**
     * Decide which columns to activate the sorting functionality on
     * @return array $sortable, the array of columns that can be sorted by the user
     */
    public function get_sortable_columns()
    {
        return $sortable = array(
            'paid' => array('paid', false),
            'amount' => array('amount', false),
            'fee' => array('fee', false),
            'created' => array('created', false)
        );
    }

    /**
     * Prepare the table with different parameters, pagination, columns and table elements
     */
    function prepare_items()
    {
        global $wpdb;
        $screen = get_current_screen();

        // Preparing your query
        $query = "SELECT * FROM " . $wpdb->prefix . 'fullstripe_payments';

        //Parameters that are going to be used to order the result
        $orderby = !empty($_REQUEST["orderby"]) ? mysql_real_escape_string($_REQUEST["orderby"]) : 'ASC';
        $order = !empty($_REQUEST["order"]) ? mysql_real_escape_string($_REQUEST["order"]) : '';
        if (!empty($orderby) && !empty($order))
        {
            $query .= ' ORDER BY ' . $orderby . ' ' . $order;
        }

        //Number of elements in your table?
        $totalitems = $wpdb->query($query); //return the total number of affected rows
        //How many to display per page?
        $perpage = 10;
        //Which page is this?
        $paged = !empty($_GET["paged"]) ? mysql_real_escape_string($_GET["paged"]) : '';
        //Page Number
        if (empty($paged) || !is_numeric($paged) || $paged <= 0)
        {
            $paged = 1;
        }
        //How many pages do we have in total?
        $totalpages = ceil($totalitems / $perpage);
        //adjust the query to take pagination into account
        if (!empty($paged) && !empty($perpage))
        {
            $offset = ($paged - 1) * $perpage;
            $query .= ' LIMIT ' . (int)$offset . ',' . (int)$perpage;
        }

        // Register the pagination
        $this->set_pagination_args(array(
            "total_items" => $totalitems,
            "total_pages" => $totalpages,
            "per_page" => $perpage,
        ));
        //The pagination links are automatically built according to those parameters

        //Register the Columns
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);

        // Fetch the items
        $this->items = $wpdb->get_results($query);
    }

    /**
     * Display the rows of records in the table
     * @return string, echo the markup of the rows
     */
    function display_rows()
    {
        //Get the records registered in the prepare_items method
        $records = $this->items;

        //Get the columns registered in the get_columns and get_sortable_columns methods
        list($columns, $hidden) = $this->get_column_info();

        //Get the correct currency symbol to use
        $options = get_option('fullstripe_options');
        $currencySymbol = '$';
        if ( $options['currency'] === 'eur' )
        {
            $currencySymbol = '€';
        }
        elseif ( $options['currency'] === 'gbp' )
        {
            $currencySymbol = '£';
        }

        //Loop for each record
        if (!empty($records))
        {
            foreach ($records as $rec)
            {
                //Open the line
                echo '<tr id="record_' . $rec->paymentID . '">';
                foreach ($columns as $column_name => $column_display_name)
                {
                    //Style attributes for each col
                    $class = "class='$column_name column-$column_name'";
                    $style = "";
                    if (in_array($column_name, $hidden)) $style = ' style="display:none;"';
                    $attributes = $class . $style;

                    //Display the cell
                    switch ($column_name)
                    {
                        case "eventID":
                            $stripeLink = "<a href='https://dashboard.stripe.com/";
                            if ($rec->livemode == 0) $stripeLink .= 'test/';
                            $stripeLink .= "charges/" . $rec->eventID . "''>$rec->eventID</a>";
                            echo '<td ' . $attributes . '>' . $stripeLink . '</td>';
                            break;
                        case "description":
                            echo '<td ' . $attributes . '>' . stripslashes($rec->description) . '</td>';
                            break;
                        case "paid":
                            $isPaid = $rec->paid == 1 ? 'Yes' : 'No';
                            echo '<td ' . $attributes . '>' . $isPaid  . '</td>';
                            break;
                        case "livemode":
                            $isLive = $rec->livemode == 1 ? 'Yes' : 'No';
                            echo '<td ' . $attributes . '>' . $isLive . '</td>';
                            break;
                        case "amount":
                            echo '<td ' . $attributes . '>' . $currencySymbol . sprintf('%0.2f', $rec->amount/100) . '</td>';
                            break;
                        case "fee":
                            echo '<td ' . $attributes . '>' . $currencySymbol . sprintf('%0.2f',$rec->fee/100) . '</td>';
                            break;
                        case "address":
                            $address = $this->format_address($rec);
                            echo '<td ' . $attributes . '>' . $address . '</td>';
                            break;
                        case "created":
                            echo '<td ' . $attributes . '>' . date('F jS Y H:i', strtotime($rec->created))  . '</td>';
                            break;
                    }
                }

                //Close the line
                echo'</tr>';
            }
        }
    }

    private function format_address($rec)
    {
        if ($rec->addressLine1 == "") return "";

        $address = $rec->addressLine1 . ($rec->addressLine2 == "" ? "" : ", $rec->addressLine2");
        $address .= $rec->addressCity == "" ? "" : ", $rec->addressCity";
        $address .= $rec->addressState == "" ? "" : ", $rec->addressState";
        $address .= $rec->addressZip == "" ? "" : ", $rec->addressZip";
        $address .= $rec->addressCountry == "" ? "" : ", $rec->addressCountry";

        return $address;

    }

}
