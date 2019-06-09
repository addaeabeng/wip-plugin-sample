<?php
$orderid = $this->order->get_order_id();
$order = $this->order->get_order($orderid);
$cart = unserialize($order['cart']);
$details = $order['details'];
?>
<?php echo $_SESSION['alert']; ?>
<h3>Your Order</h3>
<?php foreach ($cart as $cartitem) {
    ?>
    <h4><?php echo $cartitem['name']; ?></h4>
    <?php for ($i = 0; $i < $cartitem['qty']; $i++) { ?>
        <?php if ($order['people'][$i]['ticket'] == $cartitem['ticket_id']) {
            $person = (object)$order['people'][$i];
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
echo $details->name . ' ' . $details->surname . '<br/>';
echo $details->email . '<br/>';
echo $details->mobile . '<br/>';
?>
<h4>Address</h4>
<?php
echo $details->company . '<br/>';
echo $details->address1 . '<br/>';
echo $details->address2 . '<br/>';
echo $details->town . '<br/>';
echo $details->postcode . '<br/>';
echo $details->country . '<br/>';
echo $details->vatno . '<br/>';

?>
<h4>Total</h4>

<?php echo $this->cart->getTotal(); ?>

<form action="" method="post">
    <?php echo wp_nonce_field('aact_continue_to_payment'); ?>
    <button type="submit">Continue to payment</button>
</form>
<form action="" method="post">
    <?php wp_nonce_field('dpc_cancel_order'); ?>
    <input type="hidden" name="orderId" value="<?php echo $order['ID']; ?>">
    <button type="submit">
        Cancel Order
    </button>
</form>