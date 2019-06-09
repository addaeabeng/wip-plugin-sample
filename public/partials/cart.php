<table>
    <?php
    if ($this->plugin->cart->has_items()) { ?>
        <tr>
            <th>Item</th>
            <th>Qty</th>
            <th>Price</th>
        </tr>
        <?php foreach ($this->plugin->cart->cart_items() as $item) {
            ?>
            <tr>
                <td>
                    <?php echo $item['name']; ?>
                </td>
                <td>
                    <?php echo $item['qty']; ?>
                </td>
                <td>
                    <?php echo $item['price'] * $item['qty']; ?>
                </td>
            </tr>
            <?php
        } ?>
        <tr>
            <td colspan="2">
                Total
            </td>
            <td>
                <?php echo $this->plugin->cart->getTotal(); ?>
            </td>
        </tr>
    <?php } else { ?>
        <tr>
            <td>Your cart is currently empty</td>
        </tr>
    <?php } ?>
</table>
