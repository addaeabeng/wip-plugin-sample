<?php
$txn_info = unserialize(base64_decode($_GET['awtxn']));
$order = get_posts(
    array(
        'post_type' => 'conf_ticket_order',
        'meta_query' => array(
            array(
                'key'     => 'orderID',
                'value'   => $txn_info['orderno'],
            )
        ),
        'showposts' => 1
    )
);
$orderID = $order[0]->ID;
update_post_meta($orderID, 'status', 'PAID');
update_post_meta($orderID, 'transactionID', $txn_info['transactionID']);
update_post_meta($orderID, 'order_postID', $txn_info['order_postID']);
update_post_meta($orderID, 'complete_url', $txn_info['complete_url']);
?>
<?php
$orderid = $this->order->get_order_id();
$order = $this->order->get_order($orderID);
$cart = unserialize($order['cart']);
$details = $order['details'];
?>
<h3>Transaction Details</h3>
<p><strong>Transaction ID: </strong><?php echo $txn_info['transactionID']; ?></p>
<p><strong>Total:</strong> <?php echo $txn_info['total']; ?></p>
<p><strong>Order No: </strong><?php echo $txn_info['orderno']; ?></p>
<h3>Your Order</h3>
<?php foreach($cart as $cartitem){
    ?>
    <h4><?php echo $cartitem['name']; ?></h4>
    <?php for ($i = 0; $i < $cartitem['qty']; $i++){ ?>
        <?php if($order['people'][$i]['ticket'] == $cartitem['ticket_id']) {
            $person = (object) $order['people'][$i];

            ?>
            <p>
                <strong>Name: </strong> <?php echo $person->name; ?> <br>
                <strong>Company:</strong><?php echo $person->company; ?> <br>
                <strong>Email:</strong> <?php echo $person->email; ?> <br>
                <strong>Ticket: </strong><?php echo $cartitem['name']; ?><br>
            </p>

        <?php } ?>
    <?php } ?>
    <hr>
<?php } ?>
<h3>Billing Details</h3>
<h4>Name and contact details</h4>
<?php
echo $details->name.' '.$details->surname.'<br/>';
echo $details->email.'<br/>';
echo $details->mobile.'<br/>';
?>
<h4>Address</h4>
<?php
echo $details->company.'<br/>';
echo $details->address1.'<br/>';
echo $details->address2.'<br/>';
echo $details->town.'<br/>';
echo $details->postcode.'<br/>';
echo $details->country.'<br/>';
echo $details->vatno.'<br/>';

?>
<h4>Totals</h4>
<p><strong>Subtotal: </strong><?php echo $txn_info['subtotal']; ?></p>
<p><strong>VAT: </strong><?php echo $txn_info['vat']; ?></p>
<p><strong>Total: </strong><?php echo $txn_info['total']; ?></p>


<a href="<?php echo home_url(); ?>">Back to Home</a>


