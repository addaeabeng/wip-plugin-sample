<?php
namespace AA_ConfPayments;

/**
 * Class Orders
 * @package AA_ConfPayments
 * @namespace AA_ConfPayments
 */
class Orders
{
    /**
     * Hold Plugin class
     * @var \AA_ConfPayments\Conference_Payments
     */
    private $plugin;

    /**
     * Orders constructor.
     * @param $plugin
     */
    function __construct($plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * Save a new order to the database
     * @param $data
     * @return bool
     */
    public function new_order($data)
    {
        $orderpeople = is_array($data['ticketdetails']) ? $data['ticketdetails'] : null;
        $billing = is_array($data['billingdetails']) ? $data['billingdetails'] : null;
        $cart = is_array($data['cart']) ? $data['cart'] : null;
        $orderId = $this->generate_order_reference();
        $total = $data['total'];

        $attendees = array();

        foreach ($orderpeople as $person) {
            $person['orderID'] = $orderId;
            $pid = $this->add_person($person);
            array_push($attendees, $pid);
        }

        $order_details = array(
            'name' => $billing['name'],
            'surname' => $billing['surname'],
            'company' => $billing['company'],
            'address1' => $billing['address1'],
            'address2' => $billing['address2'],
            'town' => $billing['town'],
            'postcode' => $billing['postcode'],
            'country' => $billing['country'],
            'number' => $billing['number'],
            'email' => $billing['email'],
            'vatno' => $billing['vatno'],
            'orderID' => $orderId,
            'invoiceid' => 'CF' . date('jmY', strtotime("now")), // Add invoice incrementing function
            'status' => 'UNPAID',
            'total' => $total,
            'totalvat' => $this->plugin->cart->get_total_vat($total),
            'totalvat' => 0,
            'currency' => 'GBP',
            'cartitems' => serialize($cart),
            'attendees' => implode(',', $attendees),
            /* 'currency' => $this->checkcurrencycode(),
             'source' => home_url(),
             'user_rawstring' => $user_environment['browser_name'],
             'user_browser' => $user_environment['Browser'],
             'user_device_type' => $user_environment['Device_Type'],
             'user_os' => $user_environment['Platform'],
             'user_browserversion' => $user_environment['Version'],
             'user_ismobile' => $mobiledevice,
             'user_isTablet' => $tabletdevice */
        );

        $post_arr = array(
            'post_title' => 'Order: ' . $_SESSION['orderID'] . ' - ' . $billing['name'],
            'post_content' => 'mmm',
            'post_status' => 'publish',
            'post_type' => 'conf_ticket_order',
            'post_author' => get_current_user_id(),
            'meta_input' => $order_details
        );

        $neworder = wp_insert_post($post_arr, true);

        if (is_wp_error($neworder)) {
            $errors = $neworder->get_error_messages();
            foreach ($errors as $error) {
                echo $error;
            }
            die();
        } else {
            $this->set_order_id($orderId);

            return $this->get_order_id();
        }
    }

    /**
     * Add order ID to current session
     * @param $orderId
     * @return mixed
     */
    public function set_order_id($orderId)
    {
        return $_SESSION['orderID'] = $orderId;
    }

    /**
     * Return order ID
     * @return bool
     */
    public function get_order_id()
    {
        if (isset($_SESSION['orderID'])) return $_SESSION['orderID'];
        return false;
    }

    /**
     * @param $orderid
     * @return bool
     */
    public function get_order($orderid)
    {
        $getorder = get_posts(array(
            'posts_per_page' => 1,
            'post_type' => 'conf_ticket_order',
            'meta_query' => array(
                array(
                    'meta_key' => 'orderID',
                    'meta_value' => $orderid,
                )
            )
        ))[0];

        if (!$getorder) {
            return false;
        }
        $order['ID'] = $getorder->ID;
        $order['details'] = aact_post_meta_object($getorder->ID);
        $order['cart'] = unserialize($order['details']->cartitems);
        $order['people'] = $this->get_order_attendees($order['details']->attendees);

        return $order;
    }

    /**
     * Add new person to database
     * @param $person
     * @return int|\WP_Error
     */
    public function add_person($person)
    {
        $person_data = array(
            'name' => $person['name'],
            'company' => $person['company'],
            'email' => $person['email'],
            'ticket' => $person['ticketid'],
            'orderID' => $person['orderID']
        );

        $post_arr = array(
            'post_title' => 'Order: ' . $_SESSION['orderID'] . ' - ' . $person['name'],
            'post_content' => 'mmm',
            'post_status' => 'publish',
            'post_type' => 'conf_attendee',
            'post_author' => get_current_user_id(),
            'meta_input' => $person_data
        );

        $personPostId = wp_insert_post($post_arr, true);

        if (is_wp_error($personPostId)) {
            return $personPostId;
        }

        return $personPostId;
    }

    /**
     * Update person
     * @param $person
     * @return int|\WP_Error
     */
    public function update_person($person)
    {
        $person_data = array(
            'name' => $person['name'],
            'company' => $person['company'],
            'email' => $person['email'],
            'ticket' => $person['ticketid'],
            'orderID' => $person['orderID']
        );

        $post_arr = array(
            'ID' => $person['refID'],
            'meta_input' => $person_data
        );

        $personPostId = wp_update_post($post_arr, true);

        if (is_wp_error($personPostId)) {
            return $personPostId;
        }

        return $personPostId;
    }

    /**
     * @param $people
     * @return array
     */
    public function get_order_attendees($people)
    {
        $people = explode(',', $people);
        $attendees = get_posts(array(
            'post_type' => 'conf_attendee',
            'post__in' => $people
        ));

        $attendee_list = array();
        foreach ($attendees as $att) {
            $meta = (array)aact_post_meta_object($att->ID);
            $meta['ID'] = $att->ID;
            array_push($attendee_list, $meta);
        }

        return $attendee_list;
    }

    /**
     * Update purchaser details
     * @param $id
     * @param $details
     * @param $cart
     * @param $total
     * @return array
     */
    public function update_details($id, $details, $cart, $total)
    {
        $details = (array)$details;
        foreach ($details as $key => $value) {
            update_post_meta($id, $key, $value);
        }

        update_post_meta($id, 'cartitems', serialize($cart));
        update_post_meta($id, 'total', $total);
        update_post_meta($id, 'totalvat', $this->plugin->cart->get_total_vat($total));

        return $details;
    }

    /**
     * Delete order from database
     * @param $orderId
     * @return bool
     */
    public function delete_order($orderId)
    {
        $order = $this->get_order($orderId);
        foreach ($order['people'] as $person) {
            wp_delete_post($person->ID);
        }
        $deleted = wp_delete_post($order['ID']);
        if ($deleted) {
            unset($_SESSION['orderID']);
            do_action_ref_array('dpc_order_deleted',
                array(
                    $deleted,
                ));
            return true;
        }
        return false;
    }

    /**
     * Update order details
     * @param $data
     */
    public function update_order($data)
    {
        $orderId = $data['ID'];
        $orderpeople = is_array($data['ticketdetails']) ? $data['ticketdetails'] : null;
        $billing = is_array($data['billingdetails']) ? $data['billingdetails'] : null;
        $cart = is_array($data['cart']) ? $data['cart'] : null;
        $total = $data['total'];
        $oldorder = $this->get_order($this->get_order_id());

        $this->update_details($orderId, $billing, $cart, $total);

        $attendees = array();

        foreach ($oldorder['people'] as $key => $person) {
            if ($person['ID'] == $orderpeople[$key]['refID']) {
                $pid = $this->update_person($orderpeople[$key]);
                array_push($attendees, $pid);
            }
        }
    }

    /**
     *
     * Get all orders
     *
     */

    public function get_orders()
    {

    }

    /**
     * Generate random order reference3
     *
     * @return string
     */
    public function generate_order_reference()
    {
        return 'AAC-' . substr("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ", mt_rand(0, 50), 1) . substr(md5(time()), 25);
    }

    /**
     *
     * Modify columns displayed in Wordpress Admin
     *
     * @param $columns
     * @return array
     */
    public function edit_conf_ticket_order_columns($columns)
    {
        $columns = array();
        unset($columns['cb']);
        unset($columns['title']);
        unset($columns['cb']);
        $columns['cb'] = '<input type="checkbox" />';
        $columns['title'] = 'Order';
        $columns['status'] = 'Status';
        $columns['total'] = 'Total';
        $columns['orderid'] = 'Order ID';
        $columns['txnid'] = 'Transcation';
        $columns['date'] = 'Date';

        return $columns;
    }

    /**
     * Display custom data in custom columns
     *
     * @param $columns
     */
    public function conf_ticket_order_columns_content($columns)
    {
        $post_id = get_the_ID();

        switch ($columns) {
            case 'status':
                echo(get_post_meta($post_id, 'status', true));
                break;
            case 'total':
                if (empty(get_post_meta($post_id, 'total', true))) echo __('Unknown');
                else
                    echo get_post_meta($post_id, 'total', true);
                break;
            case 'orderid':
                echo get_post_meta($post_id, 'orderID', true);
                break;
            case 'txnid':
                echo get_post_meta($post_id, 'txnid', true);
                break;
            default:
                break;
        }
    }
}