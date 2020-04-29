<?php
class KajetanFruit_countModuleFrontController extends ModuleFrontController
{
    public function postProcess()
    {
        if (Tools::isSubmit('increment'))
        {
            $sql = 'UPDATE `' . _DB_PREFIX_ . 'fruit_count` SET fruit_quantity = fruit_quantity + 1 WHERE id_customer = ' . $this->context->customer->id;
            Db::getInstance()->execute($sql);
        }
        if (Tools::isSubmit('decrement'))
        {
            $sql = 'UPDATE `' . _DB_PREFIX_ . 'fruit_count` SET fruit_quantity = fruit_quantity - 1 WHERE id_customer = ' . $this->context->customer->id;
            Db::getInstance()->execute($sql);
        }
    }

    public function init(){
        parent::init();
    }

    public function initContent()
    {
        $this->context =Context::getContext();
        $this->display_column_left = false;
        $this->display_column_right = false;

        $sql = 'SELECT fruit_quantity FROM '._DB_PREFIX_.'fruit_count WHERE id_customer = ' . $this->context->customer->id;
        $fruit_quantity = Db::getInstance()->getValue($sql);

        if ($fruit_quantity === false) {
            Db::getInstance()->insert('fruit_count', array('id_customer' => $this->context->customer->id));
            $fruit_quantity = 0;
        }

        $this->context->smarty->assign(
            array(
                'fruit_quantity' => $fruit_quantity,
                'fruit_type' => $default_lang = Configuration::get('FRUIT_TYPE')
            )
        );

        parent::initContent();
        $this->setTemplate('fruit_count.tpl');
    }
}