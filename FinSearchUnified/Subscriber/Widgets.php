<?php

namespace FinSearchUnified\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Controller_ActionEventArgs;
use Enlight_Controller_Request_Request;
use Enlight_Hook_HookArgs;
use FinSearchUnified\Helper\StaticHelper;
use Shopware\Components\Routing\Context;
use Shopware\Components\Routing\Matchers\RewriteMatcher;
use Shopware_Controllers_Widgets_Listing;
use Zend_Cache_Core;
use Zend_Cache_Exception;

class Widgets implements SubscriberInterface
{
    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            'Shopware_Controllers_Widgets_Listing::listingCountAction::before' => 'beforeListingCountAction',
            'Enlight_Controller_Action_PreDispatch_Widgets' => 'onWidgetsPreDispatch'
        ];
    }

    public function beforeListingCountAction(Enlight_Hook_HookArgs $args)
    {
        if (!StaticHelper::useShopSearch()) {
            /** @var Shopware_Controllers_Widgets_Listing $subject */
            $subject = $args->getSubject();

            $request = $subject->Request();

            if (!$request->getParam('sSearch') && !$request->getParam('sCategory')) {
                $subject->Request()->setParam('sSearch', ' ');
            }
        }
    }

    /**
     * @var Zend_Cache_Core
     */
    private $cache;

    /**
     * @var RewriteMatcher
     */
    private $rewrite;

    public function __construct()
    {
        $this->cache = Shopware()->Container()->get('cache');
        $this->rewrite = Shopware()->Container()->get('shopware.routing.matchers.rewrite_matcher');
    }

    /**
     * @param Enlight_Controller_ActionEventArgs $args
     * @throws Zend_Cache_Exception
     */
    public function onWidgetsPreDispatch(Enlight_Controller_ActionEventArgs $args)
    {
        /** @var Enlight_Controller_Request_Request $request */
        $request = $args->get('request');
        $this->parseReferUrl($request);
        $referrer = $request->getHeader('referer');
        var_dump(['$referrer' =>$referrer]);
        if (strpos($referrer, 'search')) {
            Shopware()->Session()->isSearchPage = true;
            Shopware()->Session()->isCategoryPage = false;
        }
        else {
        Shopware()->Session()->isSearchPage = false;
        $cacheKey = md5($referrer);
        $isCategoryPage = $this->cache->test($cacheKey);
        var_dump(['$isCategoryPage' => $isCategoryPage]);
        if ($isCategoryPage != false) {
            Shopware()->Session()->isCategoryPage = true;
            return;
        }
        $this->cache->save($cacheKey, $isCategoryPage);

        $Shop = Shopware()->Container()->get('shop');
        $Config = Shopware()->Container()->get('config');
        $context = Context::createFromShop( $Shop, $Config);
        var_dump(['$context' => $context]);
        $rewrite = $this->rewrite->match($referrer, $context );
        var_dump(['$rewrite' => $rewrite]);
        if(is_string($rewrite)){
            Shopware()->Session()->isCategoryPage = false;
        }
        else if(is_array($rewrite)){
            $rewrite['module'] = 'frontend';
            $rewrite['controller'] = 'cat';
            $rewrite['action'] = 'index';
            Shopware()->Session()->isCategoryPage = true;
        }
        else
        {
            Shopware()->Session()->isCategoryPage = false;
        }
        }
    }

    private function parseReferUrl($referrer){
        $value = parse_url($referrer, PHP_URL_PATH);
        $path = explode('/',$value['path']);
        unset($path[0]);

        $basePath = Shopware()->Container()->get('shop')->getBasePath();
        //$basePath = Shopware()->Container()->get('shop')->getBaseUrl(rtrim(Shopware()->Shop()->getBasePath(), ' / '));
        var_dump(['$basePath' => $basePath]);
        foreach($path as $key => $value){
            if($value == $basePath)
                unset($path[$key]);
        }
        $str = implode("/",$path);

        return $str;
    }
}
