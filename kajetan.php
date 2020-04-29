<?php

if (!defined('_PS_VERSION_'))
    exit;


class Kajetan extends Module
{
    public function __construct()
    {
        $this->name = 'kajetan';
        $this->tab = 'others';
        $this->version = '1.0.0';
        $this->author = 'Kajetan Dziębaj';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Kajetan\'s little module ');
        $this->description = $this->l('A tiny module to show I can code in PS16.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        if (!Configuration::get('FRUIT_TYPE'))
            Configuration::updateValue('FRUIT_TYPE', 'Apple');

    }

    public function install()
    {
        Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'fruit_count` (
                `id_fruit_count` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `id_customer` INT(10) UNSIGNED NULL DEFAULT NULL,
                `fruit_quantity` INT(10) UNSIGNED NULL DEFAULT \'0\',
                PRIMARY KEY (`id_fruit_count`),
                INDEX `id_customer` (`id_customer`),
                CONSTRAINT `FK__ps_customer` FOREIGN KEY (`id_customer`) REFERENCES `ps_customer` (`id_customer`) ON UPDATE CASCADE ON DELETE CASCADE
            )
            ENGINE=InnoDB
            ;
        ');

        if (!parent::install() ||
            !$this->registerHook('header') ||
            !$this->registerHook('customerAccount') ||
            !$this->registerHook('blockMyAccount')) {
            return false;
        }
        return true;
    }


    public function uninstall()
    {
        $sql = array();
        $sql[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'fruit_count`';

        if (!parent::uninstall() || !$this->runSql($sql) ||
            !Configuration::deleteByName('FRUIT_TYPE')) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function runSql($sql)
    {
            foreach ($sql as $s)
                if (!Db::getInstance()->Execute($s))
                    return FALSE;
            return TRUE;
    }






    public function getContent()
    {
        $output = null;

        if (Tools::isSubmit('submit'.$this->name))
        {
            $fruit_type = strval(Tools::getValue('FRUIT_TYPE'));
            if (!$fruit_type
                || empty($fruit_type)
                || !Validate::isName($fruit_type))
                $output .= $this->displayError($this->l('Invalid fruit type'));
            else
            {
                Configuration::updateValue('FRUIT_TYPE', $fruit_type);
                $output .= $this->displayConfirmation($this->l('Settings updated'));
            }
        }

        return $output.$this->displayForm();
    }

    public function displayForm()
    {
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Fruit type settings'),
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Enter fruit type'),
                    'name' => 'FRUIT_TYPE',
                    'hint' => $this->l('Enter the name of the fruit you want your customer to count.'),
                    'empty_message' => $this->l('This field cannot be empty!'),
                    'size' => 200,
                    'required' => true
                )
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right'
            )
        );

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->submit_action = 'submit'.$this->name;
        $helper->toolbar_btn = array(
            'save' =>
                array(
                    'desc' => $this->l('Save'),
                    'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
                        '&token='.Tools::getAdminTokenLite('AdminModules'),
                ),
            'back' => array(
                'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list')
            )
        );

        $helper->fields_value['FRUIT_TYPE'] = Configuration::get('FRUIT_TYPE');

        return $helper->generateForm($fields_form);
    }

    public function hookDisplayHeader($params)
    {
        $this->context->controller->addCSS($this->_path.'css\kajetan.css', 'all');
    }

    public function hookDisplayCustomerAccount($params)
    {
        return $this->hookDisplayMyAccountBlock($params);
    }

    public function hookDisplayMyAccountBlock($params)
    {
        $this->context->smarty->assign(
            array(
                'fruit_count_link' => $this->context->link->getModuleLink('kajetan', 'fruit_count')
            )
        );
        return $this->display(__FILE__, 'fruit_count_button.tpl');
    }
}