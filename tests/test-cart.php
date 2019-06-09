<?php

class WP_Cart_Test extends WP_UnitTestCase
{

    /**
     *
     * Hold Cart Class
     *
     * @var AA_ConfPayments\Cart
     */
    public $class_instance;

    public function setUp()
    {
        @session_start();
        parent::setUp();

        $this->class_instance = new AA_ConfPayments\Cart();
    }


    public function test_cart_launches(){
        $this->assertInstanceOf('AA_ConfPayments\Cart', $this->class_instance);
    }

    public function test_add_item_to_cart(){
        $item = array(
            'ticket_id' => 111,
            'name' => 'Test Ticket',
            'sku' => 'TEST001',
            'qty' => 1,
            'price' => 10.00
        );
        $this->class_instance->add($item);
        $this->assertCount(1, $this->class_instance->cart_items());
    }

    public function test_remove_all_items_from_cart(){
        $item = array(
            'ticket_id' => 111,
            'name' => 'Test Ticket',
            'sku' => 'TEST001',
            'qty' => 1,
            'price' => 10.00
        );
        $this->class_instance->add($item);
        $this->assertTrue($this->class_instance->empty_cart());
        $this->assertCount(0, $this->class_instance->cart_items());
    }

    public function test_add_cart_total(){
        $item = array(
            'ticket_id' => 111,
            'name' => 'Test Ticket',
            'sku' => 'TEST001',
            'qty' => 1,
            'price' => 10.00
        );
        $this->class_instance->add($item);
        $item = array(
            'ticket_id' => 222,
            'name' => 'Test Ticket',
            'sku' => 'TEST001',
            'qty' => 1,
            'price' => 10.00
        );
        $this->class_instance->add($item);
        $this->assertEquals(20, $this->class_instance->getTotal());
    }

    public function test_delete_item_from_cart(){
        $item = array(
            'ticket_id' => 111,
            'name' => 'Test Ticket',
            'sku' => 'TEST001',
            'qty' => 1,
            'price' => 10.00
        );
        $this->class_instance->add($item);
        $item = array(
            'ticket_id' => 222,
            'name' => 'Test Ticket',
            'sku' => 'TEST001',
            'qty' => 1,
            'price' => 10.00
        );
        $item = array(
            'ticket_id' => 111,
            'name' => 'Test Ticket',
            'sku' => 'TEST001',
            'qty' => 1,
            'price' => 10.00
        );
        $this->class_instance->add($item);
        $item = array(
            'ticket_id' => 222,
            'name' => 'Test Ticket Two',
            'sku' => 'TEST002',
            'qty' => 1,
            'price' => 10.00
        );
        $this->class_instance->add($item);
        $this->class_instance->delete(222);
        $this->assertCount(1, $this->class_instance->cart_items());
    }

    public function get_cart_item(){
        $item = array(
            'ticket_id' => 222,
            'name' => 'Test Ticket Two',
            'sku' => 'TEST002',
            'qty' => 1,
            'price' => 10.00
        );

        $this->class_instance->add($item);

        $this->assertEquals($this->class_instance->cart_item($item), $item);

    }

    public function test_add_item_quantity(){
        $items = array(
            array(
            'ticket_id' => 111,
            'name' => 'Test Ticket',
            'sku' => 'TEST001',
            'qty' => 1,
            'price' => 10.00
            ),
            array(
                'ticket_id' => 111,
                'name' => 'Test Ticket',
                'sku' => 'TEST001',
                'qty' => 1,
                'price' => 10.00
            )
        );

        foreach ($items as $item) {
            $this->class_instance->add($item);
        }

        $the_item = $this->class_instance->cart_item(111);
        $this->assertEquals(2, $the_item['qty']);
    }

    public function test_reduce_item_quantity(){
        $items = array(
            array(
                'ticket_id' => 111,
                'name' => 'Test Ticket',
                'sku' => 'TEST001',
                'qty' => 1,
                'price' => 10.00
            ),
            array(
                'ticket_id' => 111,
                'name' => 'Test Ticket',
                'sku' => 'TEST001',
                'qty' => 1,
                'price' => 10.00
            )
        );

        foreach ($items as $item) {
            $this->class_instance->add($item);
        }

        $itemqty = $this->class_instance->cart_item(111);

        $this->assertEquals(2, $itemqty['qty']);


        $the_item = $this->class_instance->update(array(
            'ticket_id' => 111,
            'qty' => 1
        ));

        $this->assertEquals(1, $the_item['qty']);

        $this->assertFalse($this->class_instance->update(array(
            'ticket_id' => 112,
            'qty' => 1
        )));

    }

    public function tearDown() {
        unset($_SESSION['cart_items']);
    }


}