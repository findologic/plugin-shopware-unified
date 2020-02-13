<?php

namespace FinSearchUnified\Bundle\SearchBundleFindologic\FacetHandler;

use FinSearchUnified\Bundle\SearchBundleFindologic\PartialFacetHandlerInterface;
use Shopware\Bundle\SearchBundle\Criteria;
use Shopware\Bundle\SearchBundle\FacetInterface;
use Shopware\Bundle\SearchBundle\FacetResult\RangeFacetResult;
use SimpleXMLElement;

class RangeFacetHandler implements PartialFacetHandlerInterface
{
    /**
     * @param FacetInterface $facet
     * @param Criteria $criteria
     * @param SimpleXMLElement $filter
     *
     * @return RangeFacetResult|null
     */
    public function generatePartialFacet(FacetInterface $facet, Criteria $criteria, SimpleXMLElement $filter)
    {
        $min = (float)$filter->attributes->totalRange->min;
        $max = (float)$filter->attributes->totalRange->max;

        if ($min === $max) {
            // return null;
        }

        $activeMin = (float)$filter->attributes->selectedRange->min;
        $activeMax = (float)$filter->attributes->selectedRange->max;

        $conditionName = $facet->getName();
        $minFieldName = 'min' . $conditionName;
        $maxFieldName = 'max' . $conditionName;

        if ((string)$filter->name === 'price') {
            $minFieldName = 'min';
            $maxFieldName = 'max';
            $conditionName = 'price';
        }

        return new RangeFacetResult(
            $conditionName,
            $criteria->hasCondition($conditionName),
            $facet->getLabel(),
            $min,
            $max,
            $activeMin,
            $activeMax,
            $minFieldName,
            $maxFieldName
        );
    }

    /**
     * @param SimpleXMLElement $filter
     *
     * @return bool
     */
    public function supportsFilter(SimpleXMLElement $filter)
    {
        return ((string)$filter->type === 'range-slider');
    }
}
