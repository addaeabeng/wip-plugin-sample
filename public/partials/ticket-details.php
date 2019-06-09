<?php
if($this->cart->has_items()){
    $items = $this->cart->cart_items();
    if(isset($_SESSION['orderID'])){
        $orderid = $this->order->get_order_id();
        $currentorder = $this->order->get_order($orderid);
        $currentorder['details'] = (array) $currentorder['details'];
    }
    ?>
    <form action="" class="fe-form" method="post">
        <?php wp_nonce_field('aact_add_order_details'); ?>
        <?php foreach($items as $item){
            echo '<pre>';
            print_r($item);
            echo '</pre>';
            ?>
            <h3><?php echo $item['name']; ?></h3>
            <?php for($i = 0; $i < $item['qty']; $i++){ ?>
              <div class="row">
                  <div class="col-sm-4">
                      <label for="">Name</label>
                      <input type="text" name="ticketdetails[<?php echo $i; ?>][name]"
                          value="<?php
                          if(isset($currentorder['people'][$i]['name'])){
                              echo $currentorder['people'][$i]['name'];
                          }
                          ?>"
                      />
                  </div>

                  <div class="col-sm-4">
                      <label for="">Company</label>
                      <input type="text" name="ticketdetails[<?php echo $i; ?>][company]"
                             value="<?php
                             if(isset($currentorder['people'][$i]['company'])){
                                 echo $currentorder['people'][$i]['company'];
                             }
                             ?>"
                      />
                  </div>


                  <div class="col-sm-4">
                      <label for="">E-mail</label>
                      <input type="email" name="ticketdetails[<?php echo $i; ?>][email]" value="<?php
                      if(isset($currentorder['people'][$i]['email'])){
                          echo $currentorder['people'][$i]['email'];
                      }
                      ?>" />
                      <input type="hidden" name="ticketdetails[<?php echo $i; ?>][ticketid]" value="<?php echo $item['ticket_id']; ?>">
                      <?php if(isset($currentorder['people'][$i]['ID'])){ ?>
                          <input type="hidden" name="ticketdetails[<?php echo $i; ?>][refID]" value="<?php echo $currentorder['people'][$i]['ID']; ?>">
                      <?php } ?>

                  </div>
                  <?php do_action('aact_details_add_fields', 'ticketdetails['.$i.']'); ?>
              </div>
          <?php  }
            ?>
            <h3>Billing Details</h3>
            <div class="row">
                <div class="col-sm-6">
                    <label for="">Name</label>
                    <input type="text" name="billing[0][name]"
                           value="<?php
                           if(isset($currentorder['details']['name'])){
                               echo $currentorder['details']['name'];
                           }
                           ?>"
                    >
                </div>
                <div class="col-sm-6">
                    <label for="">Surname</label>
                    <input type="text" name="billing[0][surname]"
                           value="<?php
                           if(isset($currentorder['details']['surname'])){
                               echo $currentorder['details']['surname'];
                           }
                           ?>"
                    >
                </div>
            </div>
            <div class="col-sm-6">
                <label for="">Company:</label>
                <input type="text" name="billing[0][company]"
                       value="<?php
                       if(isset($currentorder['details']['company'])){
                           echo $currentorder['details']['company'];
                       }
                       ?>"
                />
            </div>

            <div class="col-sm-6">
                <label for="">VAT No:</label>
                <input type="text" name="billing[0][vatno]"
                       value="<?php
                       if(isset($currentorder['details']['vatno'])){
                           echo $currentorder['details']['vatno'];
                       }
                       ?>"
                />
            </div>
            <div class="row">
                <div class="col-6">
                    <label for="">Contact Number</label>
                    <input type="text" name="billing[0][number]"
                           value="<?php
                           if(isset($currentorder['details']['number'])){
                               echo $currentorder['details']['number'];
                           }
                           ?>"
                    >
                </div>
                <div class="col-6">
                    <label for="">E-mail</label>
                    <input type="text" name="billing[0][email]"
                           value="<?php
                           if(isset($currentorder['details']['email'])){
                               echo $currentorder['details']['email'];
                           }
                           ?>"
                    >
                </div>
                </div>
            <div class="row">
                <div class="col-6">
                    <label for="">Address 1</label>
                    <input type="text" name="billing[0][address1]"
                           value="<?php
                           if(isset($currentorder['details']['address1'])){
                               echo $currentorder['details']['address1'];
                           }
                           ?>"
                    >
                </div>
                <div class="col-6">
                    <label for="">Address 2</label>
                    <input type="text" name="billing[0][address2]"
                           value="<?php
                           if(isset($currentorder['details']['address2'])){
                               echo $currentorder['details']['address2'];
                           }
                           ?>"
                    >
                </div>
            </div>

            <div class="row">
                <div class="col-6">
                    <label for="">Town</label>
                    <input type="text" name="billing[0][town]"
                           value="<?php
                           if(isset($currentorder['details']['town'])){
                               echo $currentorder['details']['town'];
                           }
                           ?>"
                    >
                </div>
                <div class="col-6">
                    <label for="">State/County</label>
                    <input type="text" name="billing[0][state]"
                           value="<?php
                           if(isset($currentorder['details']['state'])){
                               echo $currentorder['details']['state'];
                           }
                           ?>"
                    >
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <label for="">Postcode</label>
                    <input type="text" name="billing[0][postcode]"
                           value="<?php
                           if(isset($currentorder['details']['postcode'])){
                               echo $currentorder['details']['postcode'];
                           }
                           ?>"
                    >
                </div>
                <div class="col-6">
                    <label for="billing[0][country]">Country</label>
                    <select name="billing[0][country]" id="">
                    <?php foreach($this->plugin->resources->get_countries() as $key => $value){ ?>
                        <option value="<?php echo $key; ?>" class="form-control"
                            <?php
                            if($currentorder['details']['country'] == $key){
                                echo 'selected';
                            }
                            ?>
                        ><?php echo $value; ?></option>
                    <?php } ?>
                    </select>

                </div>
            </div>

        <?php } ?>
        Please confirm that you have read and agree to our <a href="<?php echo get_permalink(get_page_by_path('terms-conditions')) ?>">terms and conditions</a> <input
            type="checkbox" name="billing[0][termsaccepted]" data-rule-required="true">
        <input type="submit" value="continue to payment">

    </form>
<?php } else { ?>
    <p>Tickets are no longer on sale.</p>
<?php } ?>
<form action="" method="post">
    <?php wp_nonce_field('aact_clear_cart'); ?>
    <input class="fe-button" type="submit" value="Clear basket">
</form>
