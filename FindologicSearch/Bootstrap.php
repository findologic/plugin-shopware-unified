<?php

use FindologicSearch\Components\Findologic\Helper;

class Shopware_Plugins_Frontend_FindologicSearch_Bootstrap extends Shopware_Components_Plugin_Bootstrap
{
    const SHOP_KEY = 'findologic_shopkey';

    /**
     * @var string
     */
    private $shopKey;

    /**
     * @var int
     */
    private $sw;

    /**
     * @var helper
     */
    private $helper;

    /**
     * Initialise bootstrap object and set shopware version
     *
     * @param $name
     * @param null $info
     */
    public function __construct($name, $info = null)
    {
        parent::__construct($name, $info);
        $this->sw = (int)Shopware()->Config()->version;
    }

    /**
     * @return array
     */
    public function getCapabilities()
    {
        return array(
            'install' => true,
            'update' => true,
            'enable' => true
        );
    }

    /**
     * Returns module label
     *
     * @return string
     */
    public function getLabel()
    {
        return 'FINDOLOGIC Search';
    }

    /**
     * Returns module version
     *
     * @return string
     */
    public function getVersion()
    {
        return '1.0.2';
    }

    /**
     * Returns module copyright
     *
     * @return string
     */
    public function getCopyright()
    {
        return 'Copyright © ' . date('Y') . ', ' . $this->getAuthor();
    }

    /**
     * Returns module author
     *
     * @return string
     */
    public function getAuthor()
    {
        return 'FINDOLOGIC GmbH';
    }

    /**
     * Retunrs module supplier
     *
     * @return string
     */
    public function getSupplier()
    {
        return 'FINDOLOGIC GmbH';
    }

    /**
     * Returns module description
     *
     * @return string
     */
    public function getDescription()
    {
        return '<p><b>FINDOLOGIC – weit mehr als nur eine Suche!</b></p>
                <br>
                <p><b>Die innovative Suchlösung für eine höhere Conversion-Rate in Ihrem Online-Shop!</b></p>
                <br>
                <p>FFINDOLOGIC ist die ultimative Suchlösung für Ihren Online-Shop! Lassen Sie Ihre Kunden blitzschnell
                das finden, was sie suchen! Denn nur was Kunden finden, das kaufen sie auch. Mit FINDOLOGIC sind
                Umsatzsteigerungen von weit über 25% belegt.</p>
                <br>
                <p>Wie hoch ist Ihre CR?! Mit uns sind Conversion-Rates bis 21% über die interne Suche nachweislich
                möglich!</p>
                <br>
                <p>Ein Auszug der wichtigsten Funktionen der FINDOLOGIC-Suche:</p>
                <br>
                <ul>
                    <li><b>Intelligentes Ranking</b> - relevante Artikel an erster Stelle reihen</li>
                    <li><b>Sinnverwandte Zusammenhänge</b> – verstehen, was der Kunde wünscht</li>
                    <li><b>Automatische Fehlertoleranz</b> - Ergebnisse trotz Schreibfehlern</li>
                    <li><b>Intuitive Filternavigation</b> - verfeinern der Suchergebnisse</li>
                    <li><b>Smart Suggest</b> - Eingabehilfe zu Such-, Kategorie- & Produktvorschlägen</li>
                    <li><b>Promotions für Top-Seller </b> - die Suche als Marketinginstrument</li>
                    <li><b>Pushen von Produkten</b> - Artikel an oberster Stellen reihen</li>
                    <li><b>Product Placement</b> - gezielte Abverkaufsteuerung</li>
                    <li><b>Search-Concepts</b> - eine Vorauswahl für Kategorien treffen</li>
                    <li><b>Landingpages</b> - Einrichtung von Seiten zu Keywords und Suchbegriffen</li>
                    <li><b>Guided Shopping</b> - Produktberater zur individuellen Shopping-Beratung</li>
                    <li><b>Selbstlernende Suche</b> - vom Suchverhalten der User lernen</li>
                    <li><b>Tracking & Reports</b> - prägnante Analysedaten zur CR-Optimierung und Potentialanalyse</li>
                    <li><b>Responsive Design</b> - Seien Sie jetzt „on any device“ optimiert verfügbar</li>
                </ul>
                <br><br>
                <p><b>FINDOLOGIC – increase sales! – with a better search function in your online store!</b></p>
                <br>
                <p>FINDOLOGIC is the ultimate search solution for your online store! Allow your customers to find what
                    they are looking for at lightning speed! Since customers can only purchase what they are able to
                    find. With FINDOLOGIC your profits will increase well over 25% - guaranteed!</p>
                <br>
                <p>What’s your CR? With our technology conversion rates up to 21% in search are possible!</p>
                <br>
                <p>Summary of the most important features:</p>
                <br>
                <ul>
                    <li><b>Intelligent Ranking</b> - relevant results first</li>
                    <li><b>Synonymous Associations</b> – understanding what the customer wants</li>
                    <li><b>Automatic Error Tolerance</b> - results despite misspelling</li>
                    <li><b>Intuitive navigation</b> - refine the search results or categories</li>
                    <li><b>Auto-Complete</b> - input assistance including search-, category- & product suggestions</li>
                    <li><b>Promotions for Top Sellers</b> - use search as a marketing tool</li>
                    <li><b>Product Pushing</b> - promote any product groups you want</li>
                    <li><b>Product Placement</b> - targeted sales control</li>
                    <li><b>Search-Concepts</b> - make a preselection of</li>
                    <li><b>Landingpages</b> - set up targeted pages for keywords or manufacturers</li>
                    <li><b>Guided Shopping</b> - creates shopping experiences guiding to the desired product</li>
                    <li><b>Selflearning Vocabulary</b> - learning from your user’s behaviour</li>
                    <li><b>Tracking & Reports</b> - consise data refering to your CR and potentials</li>
                    <li><b>Responsive Design</b> - optimized on any device</li>
                </ul>';
    }

    /**
     * Returns module support e-mail
     *
     * @return string
     */
    public function getSupport()
    {
        return 'support@findologic.com';
    }

    /**
     * Return module author link
     *
     * @return string
     */
    public function getLink()
    {
        return 'http://www.findologic.com ';
    }

    /**
     * Returns module info
     *
     * @return array
     */
    public function getInfo()
    {
        return array(
            'version' => $this->getVersion(),
            'label' => $this->getLabel(),
            'copyright' => $this->getCopyright(),
            'author' => $this->getAuthor(),
            'supplier' => $this->getSupplier(),
            'description' => $this->getDescription(),
            'support' => $this->getSupport(),
            'link' => $this->getLink()
        );
    }

    /**
     * Installing module
     *
     * @return array
     */
    public function install()
    {
        $this->createConfiguration();

        $this->registerControllers();
        $this->registerEvents();
        return array('success' => true, 'invalidateCache' => array('frontend', 'backend'));
    }

    /**
     * Uninstalling module
     *
     * @return bool
     */
    public function uninstall()
    {
        return true;
    }

    /**
     * The afterInit function registers the custom plugin models and namespaces.
     */
    public function afterInit()
    {
        $ds = DIRECTORY_SEPARATOR;
        $this->registerCustomModels();
        $this->Application()->Loader()->registerNamespace('FindologicSearch', $this->Path());
        $this->Application()->Loader()->registerNamespace('FindologicSearch\Components\Findologic', $this->Path() . $ds . 'Components' . $ds . 'Findologic');
        $this->helper = new Helper($this);
    }

    /**
     * Register module events
     */
    private function registerEvents()
    {
        $this->subscribeEvent(
            'Enlight_Controller_Action_PostDispatch_Frontend', 'onPostDispatchFrontend'
        );
        $this->subscribeEvent(
            'Shopware_Controllers_Backend_Config_Before_Save_Config_Element', 'onConfigSaveForm'
        );
        $this->subscribeEvent(
            'Enlight_Controller_Action_PostDispatch_Frontend_Detail', 'onPostDispatchDetail'
        );
        $this->subscribeEvent(
            'Enlight_Controller_Action_Frontend_Checkout_Finish', 'onCheckoutFinish'
        );
        $this->subscribeEvent(
            'Enlight_Controller_Action_Frontend_Checkout_Cart', 'onCheckoutConfirm'
        );
        $this->subscribeEvent(
            'Enlight_Controller_Action_Frontend_Checkout_Confirm', 'onCheckoutConfirm'
        );
        if ($this->sw === 5) {
            $this->subscribeEvent(
                'Enlight_Controller_Action_Frontend_Checkout_ajaxCart', 'onCheckoutConfirm'
            );
        } else {
            $this->subscribeEvent(
                'Enlight_Controller_Action_Frontend_Checkout_addArticle', 'onCheckoutConfirm'
            );
            $this->subscribeEvent(
                'Enlight_Controller_Action_Frontend_Checkout_ajaxAddArticle', 'onCheckoutConfirm'
            );
        }
    }

    /**
     * Event used for article detail template extending and passing placeholder values for article detail script
     */
    public function onPostDispatchDetail(Enlight_Event_EventArgs $arguments)
    {
        if (!$this->useFindologic($arguments)) {
            return;
        }

        $params = $arguments->getSubject()->Request()->getParams();
        $id = $params['sArticle'];
        if(!$id){
            $orderNumber = $params['ordernumber'];
            $articleByID = Shopware()->Modules()->Articles()->sGetArticleIdByOrderNumber($orderNumber);
        } else {
            $articleByID = Shopware()->Modules()->sArticles()->sGetArticleById($id);
        }
        $controller = $arguments->getSubject();
        $view = $controller->View();
        $requestUrl = $controller->Request()->getRequestUri();
        if (strpos($requestUrl, 'findologic=off') == false) {
            $this->extendTemplate($view, 'detail');
            $view->assign('PRODUCT_ORDERNUMBER', $articleByID['ordernumber']);
            $view->assign('PRODUCT_TITLE', $articleByID['articleName']);
            $view->assign('PRODUCT_CATEGORY', $this->helper->getCategories($articleByID['categoryID']));
            $view->assign('PRODUCT_PRICE', str_replace(',', '.', $articleByID['price']));
        }
    }

    /**
     * Event used for every page template extending and passing placeholder values for every shop page
     *  additionally passes orderID and cart amount placeholder values
     */
    public function onPostDispatchFrontend(Enlight_Event_EventArgs $arguments)
    {
        $controller = $arguments->getSubject();
        $view = $controller->View();

        // Add template directory
        $view->addTemplateDir(
            $this->Path() . 'Views/'
        );

        if (!$this->useFindologic($arguments)) {
            return;
        }

        $sqlGroupKey = "SELECT customergroup FROM s_user where sessionID =? ";
        $groupKey = Shopware()->Db()->fetchone($sqlGroupKey, array(Shopware()->Modules()->sSystem()->sSESSION_ID));
        $shopKey = $this->getShopKey();
        if (!empty($groupKey)) {
            $hash = base64_encode($shopKey ^ $groupKey);
        } else {
            $hash = base64_encode($shopKey ^ 'EK');
        }

        $this->extendTemplate($view, 'index');

        // $placeholder1 - upper case md5-encoded shopkey, $placeholder2 - usergrouphash of specific user
        $view->assign('placeholder1', strtoupper(md5($shopKey)));
        $view->assign('placeholder2', $hash);
    }

    /**
     * Event used for cart template extending and passing placeholder values for cart tracking script
     */
    public function onCheckoutConfirm(Enlight_Event_EventArgs $arguments)
    {
        if (!$this->useFindologic($arguments)) {
            return;
        }

        $controller = $arguments->getSubject();
        $view = $controller->View();
        $order = array();
        $orderCounter = 0;

        $basket = Shopware()->Modules()->Basket()->sGetBasket();
        $cart = $basket['content'];
        foreach ($cart as $cartItem) {
            if (($cartItem['ordernumber'] !== 'SHIPPINGDISCOUNT') && ($cartItem['ordernumber'] !== 'sw-discount')) {
                $order[$orderCounter]['productordernumber'] = $cartItem['ordernumber'];
                $order[$orderCounter]['productname'] = $cartItem['articlename'];
                $order[$orderCounter]['productcategories'] = array_unique($this->helper->getCartCategories($cartItem['articleID']));
                $order[$orderCounter]['productprice'] = $cartItem['priceNumeric'];
                $order[$orderCounter]['productquantity'] = $cartItem['quantity'];
                $orderCounter++;
            }
        }

        $this->extendTemplate($view, 'cart');
        $this->extendTemplate($view, 'ajax_cart');
        $view->assign('order', $order);

        $cartAmount = 0;
        if ($this->sw === 4) {
            if (($view->sBasket['AmountWithTaxNumeric']) && ($view->sBasket['AmountWithTaxNumeric'] != $cartAmount)) {
                $cartAmount = $view->sBasket['AmountWithTaxNumeric'];
                $this->extendTemplate($view, 'cart');
            } else {
                if (($view->sBasket['sAmount']) && ($view->sBasket['sAmount'] != $cartAmount)) {
                    $cartAmount = $view->sBasket['sAmount'];
                    $this->extendTemplate($view, 'cart');
                }else if(Shopware()->Modules()->Basket()->sGetAmount()['totalAmount']){
                    $cartAmount = Shopware()->Modules()->Basket()->sGetAmount()['totalAmount'];
                }
            }
        } else {
            $basket = Shopware()->Modules()->Basket()->sGetBasket();
            if (($view->sBasket['AmountWithTaxNumeric']) && ($view->sBasket['AmountWithTaxNumeric'] != $cartAmount)) {
                $cartAmount = $view->sBasket['AmountWithTaxNumeric'];
                $this->extendTemplate($view, 'cart');
            } else {
                if (($basket['Amount']) && ($basket['Amount'] != $cartAmount)) {
                    $cartAmount = $basket['Amount'];
                    $this->extendTemplate($view, 'cart');
                }
            }
        }

        $view->assign('CART_AMOUNT', $cartAmount);
    }

    /**
     * Event used for confirmation template extending and passing placeholder values for confirmation tracking script
     */
    public function onCheckoutFinish(Enlight_Event_EventArgs $arguments)
    {
        if (!$this->useFindologic($arguments)) {
            return;
        }

        $controller = $arguments->getSubject();
        $view = $controller->View();
        $orderDetails = array();
        $order = array();
        $orderCounter = 0;
        $basket = Shopware()->Session()->sOrderVariables['sBasket'];
        $content = $basket['content'];
        if ($basket['AmountWithTaxNumeric']) {
            $totAm = $basket['AmountWithTaxNumeric'];
        } else {
            $totAm = $basket['AmountNumeric'];
        }

        foreach ($content as $orderItem) {
            if (($orderItem['ordernumber'] !== 'SHIPPINGDISCOUNT') && ($orderItem['ordernumber'] !== 'sw-discount')) {
                $order[$orderCounter]['productordernumber'] = $orderItem['ordernumber'];
                $order[$orderCounter]['productname'] = $orderItem['articlename'];
                $order[$orderCounter]['productcategories'] = array_unique($this->helper->getCartCategories($orderItem['articleID']));
                $order[$orderCounter]['productprice'] = $orderItem['price'];
                $order[$orderCounter]['productquantity'] = $orderItem['quantity'];

                $orderCounter++;
                $orderDetails['subtotal'] = $totAm - Shopware()->Session()->sOrderVariables['sBasket']['sShippingcostsWithTax'];
                $orderDetails['tax'] = Shopware()->Session()->sOrderVariables['sBasket']['sAmountTax'];
                $orderDetails['total'] = $totAm;
                $orderDetails['shipping'] = Shopware()->Session()->sOrderVariables['sBasket']['sShippingcostsWithTax'];
            } else {
                $orderDetails['discount'] = $orderItem['amountWithTax'];
            }
        }

        $this->extendTemplate($view, 'confirmation');
        $assign = $view->getAssign();
        $orderNumber = $assign['sOrderNumber'];
        if ($orderNumber) {
            $view->assign('ORDER_ID', $orderNumber);
        }

        $view->assign('ORDER_TOTAL', $orderDetails['total']);
        $view->assign('productsinorder', $order);
        $view->assign('ORDER_SUB_TOTAL', $orderDetails['subtotal']);
        $view->assign('ORDER_TAX_AMOUNT', $orderDetails['tax']);
        $view->assign('ORDER_DISCOUNT', $orderDetails['discount']);
        $view->assign('ORDER_SHIPPING_AMOUNT', $orderDetails['shipping']);
    }

    /**
     * Register modules controller
     *
     * @throws Exception
     */
    public function registerControllers()
    {
        $this->registerController('Frontend', 'Findologic');
    }

    /**
     * Validated that each shop has its own shop key. Trims shop key value.
     */
    public function onConfigSaveForm(Enlight_Event_EventArgs $arguments)
    {
        $params = $arguments->getSubject()->Request()->getParams();
        if ($params['name'] !== 'FindologicSearch') {
            return null;
        }

        $values = $arguments->getReturn();
        $keys = array();

        /**
         * @var int $shopId
         * @var Shopware\Models\Config\Value $value
         */
        foreach($values as $shopId => &$value) {
            $value->setValue(trim($value->getValue()));
            $val = $value->getValue();
            if (!$val) {
                continue;
            }

            if (isset($keys[$val])) {
                throw new \Exception('Each shop must have its own shop key!');
            }

            $keys[$val] = 1;
        }

        return $values;
    }

    /**
     * Create plugin configuration fields
     */
    public function createConfiguration()
    {
        $form = $this->Form();
        $form->setElement('text', self::SHOP_KEY, array(
                'label' => 'FINDOLOGIC Shop Key',
                'value' => '',
                'scope' => Shopware\Models\Config\Element::SCOPE_SHOP,
                'description' => 'Enter shopkey from FINDOLOGIC',
                'required' => true
            )
        );
    }

    /**
     * Extends passed template with modules header.tpl
     *
     * @param $view
     * @param $controller
     */
    private function extendTemplate($view, $controller)
    {
        $view->extendsTemplate("frontend/findologic_search_sw{$this->sw}/$controller/header.tpl");
    }

    /**
     * Returns shopkey
     *
     * @return bool|string
     */
    private function getShopKey()
    {
        if (!$this->shopKey) {
            $shopName = Shopware()->Shop()->getName();
            $config = Shopware()->Plugins()->Frontend()->FindologicSearch()->Config();
            $shop = Shopware()->Models()->getRepository('Shopware\Models\Shop\Shop')
                ->findOneBy(array('name' => $shopName));
            $shopId = $shop->getId();

            $configShop = Shopware()->Models()->getRepository('Shopware\Models\Config\Value')
                ->findOneBy(array('value' => $config[self::SHOP_KEY]));
            if ($configShop && $configShop->getShop()->getId() == $shopId) {
                $this->shopKey = trim($config[self::SHOP_KEY]);
            } else {
                $this->shopKey = false;
            }
        }

        return $this->shopKey;
    }

    /**
     * Checking for findologic parameter in request
     *
     * @param Enlight_Event_EventArgs $arguments
     * @return bool
     */
    private function useFindologic(Enlight_Event_EventArgs $arguments)
    {
        if (!$this->getShopKey()) {
            return false;
        }

        $requestUrl = $arguments->getSubject()->Request()->getRequestUri();
        return strpos($requestUrl, 'findologic=off') === false;
    }
}
