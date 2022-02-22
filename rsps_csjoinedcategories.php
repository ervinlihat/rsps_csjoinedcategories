<?php
/**
* 2007-2021 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2021 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class Rsps_csjoinedcategories extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'rsps_csjoinedcategories';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Szilamer';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Resolution-Studio Category Joining');
        $this->description = $this->l('The module is created for a category joining with another category');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall this module?');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        Configuration::updateValue('CATEGORYJOIN_LIVE_MODE', false);
        include(dirname(__FILE__).'/sql/install.php');
        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('backOfficeHeader') &&
            $this->registerHook('displayRightColumnProduct') &&
            $this->registerHook('displayShoppingCartFooter') &&
            $this->registerHook('displayHeader') &&
            $this->registerHook('displayFooterProduct') &&
            $this->registerHook('displayTop');
    }

    public function uninstall()
    {
        Configuration::deleteByName('CATEGORYJOIN_LIVE_MODE');

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
         
		if (Tools::getValue('add')) {
			$this->insertAction();
		}
		if (Tools::getValue('deleteItem')) {
			$this->deleteAction();
		}
		
		
        $sql_query=Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS("SELECT * FROM ps_categoryjoin");
        $this->context->smarty->assign([
            'sql_query' => $sql_query,
        ]);
        
        return $this->display(__FILE__,'views/templates/admin/configure.tpl');
        
    }
    

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitCategoryjoinModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Live mode'),
                        'name' => 'CATEGORYJOIN_LIVE_MODE',
                        'is_bool' => true,
                        'desc' => $this->l('Use this module in live mode'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-envelope"></i>',
                        'desc' => $this->l('Enter a valid email address'),
                        'name' => 'CATEGORYJOIN_ACCOUNT_EMAIL',
                        'label' => $this->l('Email'),
                    ),
                    array(
                        'type' => 'password',
                        'name' => 'CATEGORYJOIN_ACCOUNT_PASSWORD',
                        'label' => $this->l('Password'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            'CATEGORYJOIN_LIVE_MODE' => Configuration::get('CATEGORYJOIN_LIVE_MODE', true),
            'CATEGORYJOIN_ACCOUNT_EMAIL' => Configuration::get('CATEGORYJOIN_ACCOUNT_EMAIL', 'contact@prestashop.com'),
            'CATEGORYJOIN_ACCOUNT_PASSWORD' => Configuration::get('CATEGORYJOIN_ACCOUNT_PASSWORD', null),
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }

    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name) {
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
        
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookDisplayHeader()
    {
        $this->context->controller->addJS($this->_path.'/views/js/front.js');
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');
    }
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path.'/views/js/front.js');
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');

    }

    public function renderWidget($hookName, array $params)
    {
        
        $tplFile = 'product-categoryjoin-miniature.tpl';
        
		return $this->fetch('module:rsps_csjoinedcategories/views/templates/front/' . $tplFile);
    }


    public function hookDisplayFooterProduct()
    {
        $id_product = (int)Tools::getValue('id_product');

        $strsql = '
            SELECT 
                cj.id_category_B, cj.title
            
            FROM ps_categoryjoin cj 

            LEFT JOIN ps_category_product cp1 ON cj.id_category_A = cp1.id_category

            WHERE 
                cp1.id_product ='.$id_product.'

        ';

        
        $s=Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($strsql);       
        
        $arrProducts = array();

        $a=array();
         foreach($s as $value){

            $products=Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
            SELECT
                cp2.id_product, p.name, p.link_rewrite, i.cover, pro.price, a.id_product_attribute, i.id_image, a.quantity, c.id_parent

            FROM  ps_category_product cp2

            INNER JOIN ps_product_lang p ON cp2.id_product= p.id_product
            INNER JOIN ps_image i ON cp2.id_product=i.id_product
            INNER JOIN ps_product pro ON cp2.id_product=pro.id_product
            INNER JOIN ps_product_attribute a ON cp2.id_product=a.id_product
            INNER JOIN ps_category_lang cl ON cl.id_category = cp2.id_category
            INNER JOIN ps_category c ON c.id_category = cl.id_category
            INNER JOIN ps_stock_available sa ON sa.id_product=pro.id_product

            WHERE cp2.id_category ='.$value["id_category_B"].' AND p.id_lang = 2 AND a.quantity > 0  AND i.cover=1

            GROUP BY cp2.id_product 
            ORDER BY 
            RAND() 
            LIMIT 10');
         
            
        $arrProducts[$value["id_category_B"]]["title"]=$value["title"];
        $arrProducts[$value["id_category_B"]]['product_details'] = $products;
    

        }
        foreach($arrProducts as $key => $value){
        foreach($value['product_details'] as $key1 => $id_parent_value){
            $id_parent=Db::getInstance()->getValue
               ('
               SELECT DISTINCT(c.id_parent) 

               from ps_category c 

               JOIN ps_category_lang cl ON cl.id_category=c.id_category

               WHERE c.id_category ='.$id_parent_value['id_parent']);
           
               
            $id_parent_link=Db::getInstance()->getValue
            ('
            SELECT link_rewrite

            from ps_category_lang 

            WHERE id_category ='.$id_parent);
           
        
             $arrProducts[$key]['product_details'][$key1]['id_parent_default']=$id_parent_link;
            
            }
            
        }

               

     $this->context->smarty->assign([
          'product_FromCat_B' =>  $arrProducts,
          
     ]);

     $tplFile = 'product-categoryjoin-miniature.tpl';
        
		return $this->fetch('module:rsps_csjoinedcategories/views/templates/front/' . $tplFile);

    }


    public function deleteAction(){

        if(isset($_POST['deleteItem'])){
            $rowId=$_POST['deleteItem'];  
            Db::getInstance()->execute('DELETE FROM ps_categoryjoin WHERE id='.$rowId );
            return true;
        }
        return false;
    }

    public function insertAction(){
        $title=Tools::getValue('title');
        $catA=Tools::getValue('cat_A');
        $catB=Tools::getValue('cat_B');
        if(!empty($title) || !empty($cat_A) || !empty($cat_B) ){
            if(Configuration::updateValue('CATEGORY_JOIN','updaated')){
                
                  Db::getInstance()->execute("INSERT INTO `"._DB_PREFIX_."categoryjoin` (`id`, `title`, `id_category_A`, `id_category_B`) VALUES (0,'$title','$catA','$catB')");
                return true;
                  
            }
            else{
                return false;
            }
        }
    }

    
    

}
