<?php
$tickets = get_posts(array(
    'post_type' => 'conference_ticket',
    'posts_per_page' => -1,
));

if ( count( $tickets ) ) {
    ?>
    <form action="" class="fe-form" method="post">
        <?php wp_nonce_field('aact_select_ticket'); ?>
        <table class="event-ticket-table">
            <tr>
                <th>Ticket Type</th>
                <th>Price (ex. VAT)</th>
                <th>Quantity</th>
            </tr>
            <?php
            $i = 0;
            foreach ($tickets as $t) {
                $meta = aact_post_meta_object($t->ID);
                if (check_early_bird($meta->_cpaa_earlybird_end_date) === true) {
                    $earlybird = true;
                }

                ?>
                <tr>
                    <td class="product"><?php echo $t->post_title; ?><br>
                        <?php if (isset($earlybird)) { ?>
                            <span>Earlybird price!</span>
                        <?php } ?>

                    </td>
                    <td class="product"><?php echo ( isset( $earlybird ) ) ? $meta->_cpaa_earlybird_price : $meta->_cpaa_ticket_price; ?></td>
                    <td class="product">
                        <input value="<?php echo $t->ID; ?>" class="reg-input" type="hidden"
                               name="qty[<?php echo $i; ?>][id]">
                        <input class="reg-input" placeholder="0" type="text"
                               name="qty[<?php echo $i; ?>][qty]" size="1">
                        <?php if ( isset( $earlybird ) ) { ?>
                            <input class="reg-input" value="1" placeholder="0" type="hidden"
                                   name="qty[<?php echo $i; ?>][earlybird]" size="1">
                        <?php } ?>
                    </td>
                </tr>
                <?php
                $i++;
            } ?>
        </table>
        <input class="fe-button" type="submit" value="Add to basket">
    </form>
<?php } else { ?>
    <p>Tickets are no longer on sale.</p>
<?php } ?>
<form action="" method="post">
    <?php wp_nonce_field('aact_clear_cart'); ?>
    <input class="fe-button" type="submit" value="Clear basket">
</form>
