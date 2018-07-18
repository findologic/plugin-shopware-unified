<?php

class Shopware_Controllers_Frontend_Findologic extends Enlight_Controller_Action
{
    public function indexAction()
    {
        // INIT THE BL SYSTEM

        $shopKey = $this->request->get('shopkey');
        $start = $this->request->get('start');
        $length = $this->request->get('count');
        $language = $this->request->get('language');

        /** @var \FinSearchUnified\ShopwareProcess $blController */
        $blController = $this->container->get('fin_search_unified.shopware_process');
        $blController->setShopKey($shopKey);
        if ($length !== null) {
            $xmlDocument = $blController->getFindologicXml($language, $start != null ? $start : 0, $length);
        } else {
            $xmlDocument = $blController->getFindologicXml($language);
        }

        $this->response->setHeader('Content-Type', 'application/xml; charset=utf-8', true);
        $this->response->setBody($xmlDocument);
        $this->container->get('front')->Plugins()->ViewRenderer()->setNoRender();

        return $this->response;
    }
}