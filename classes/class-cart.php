<?php
namespace AA_ConfPayments;

class Cart {

    /**
     * @var $items
     */
    protected $items;

    /**
     * @var $total
     */
    protected $total;

    function __construct()
    {
        if(!isset($_SESSION['cart_items'])){
            $_SESSION['cart_items'] = array();
        }
    }

    /**
     * Adds item to cart instance
     *
     * @param $item
     * @return mixed
     */
    public function add($item){
        if(empty($_SESSION['cart_items'])){
            array_push($_SESSION['cart_items'], $item);
        } else {
            foreach ($_SESSION['cart_items'] as $key => $cart_item){
                if($item['sku'] == $cart_item['sku']){
                    $_SESSION['cart_items'][$key]['qty'] =  $_SESSION['cart_items'][$key]['qty'] + $item['qty'];
                } else {
                    array_push($_SESSION['cart_items'], $item);
                }
            }
        }

        return $_SESSION['cart_items'];
    }


    /**
     * Update item details
     *
     * @param array $updateditem
     * @return bool
     */
    public function update($updateditem){
        if(!isset($updateditem['ticket_id'])) return false;
        foreach($this->cart_items() as $key => $value){
            if($value['ticket_id'] == $updateditem['ticket_id']){
                foreach ($updateditem as $field => $data){
                    $_SESSION['cart_items'][$key][$field] = $data;
                }
                return $_SESSION['cart_items'][$key];
            };
        }

        return false;
    }

    /**
     * Delete whole item from cart by ticket id
     *
     * @param int
     */
    public function delete($ticketid){
        foreach ($_SESSION['cart_items'] as $key => $cart_item){
            if($cart_item['ticket_id'] == $ticketid){
                unset($_SESSION['cart_items'][$key]);
                break;
            }
        }
    }

    /**
     * Get details of item in cart
     * @param int $ticketid
     * @return array
     */
    public function cart_item($ticketid){
        foreach($this->cart_items() as $item){
            if($item['ticket_id'] == $ticketid){
                return $item;
            };
        }
    }

    protected function clear_items(){
        unset($_SESSION['cart_items']);
    }

    /**
     *
     * Check if cart has items
     * @return bool
     */

    public function has_items(){
        if(!empty($_SESSION['cart_items'])){
            return true;
        }
        return false;
    }

    /**
     * Return cart items or empty array
     *
     * @return array
     */
    public function cart_items(){
        if( isset( $_SESSION['cart_items'] ) ) {
            return $_SESSION['cart_items'];
        }
        return array();
    }

    /**
     * Return cart items or empty array
     *
     * @return array
     */
    public function empty_cart(){
        $this->clear_items();
        if(!isset($_SESSION['cart_items'])){
            return true;
        }

        return false;
    }

    /**
     *
     * Calculate VAT - default UK VAT
     * @param $total
     * @param $vat integer
     * @return float|int
     *
     */
    public function get_total_vat($total, $vat = 20){
        $vat = apply_filters($vat, 'calculate_vat');
        return (($total / 100) * $vat);
    }

    /**
     * Calculate total of all items in cart
     * @return number
     */
    public function getTotal(){
        $total = array();
        foreach($this->cart_items() as $item){
           $t =  $item['qty'] * $item['price'];
           array_push($total, $t);
        }

        // TODO: Add hook or filter for local taxes

        return array_sum($total);
    }
}