<?php

namespace FinSearchUnified\Tests\Bundle\SearchBundleFindologic\FacetHandler;

use FinSearchUnified\Bundle\SearchBundleFindologic\FacetHandler\RangeFacetHandler;
use Shopware\Bundle\SearchBundle\Condition\ProductAttributeCondition;
use Shopware\Bundle\SearchBundle\ConditionInterface;
use Shopware\Bundle\SearchBundle\Criteria;
use Shopware\Bundle\SearchBundle\Facet\ProductAttributeFacet;
use Shopware\Bundle\SearchBundle\FacetResult\RangeFacetResult;
use Shopware\Bundle\SearchBundle\FacetResultInterface;
use FinSearchUnified\Tests\TestCase;
use SimpleXMLElement;

class RangeFacetHandlerTest extends TestCase
{
    /**
     * @dataProvider filterProvider
     *
     * @param string $type
     * @param bool $doesSupport
     */
    public function testSupportsFilter($type, $doesSupport)
    {
        $data = '<?xml version="1.0" encoding="UTF-8"?><searchResult></searchResult>';
        $filter = new SimpleXMLElement($data);

        $filter->addChild('name', 'attr6');
        $filter->addChild('type', $type);

        $facetHandler = new RangeFacetHandler();
        $result = $facetHandler->supportsFilter($filter);

        $this->assertSame($doesSupport, $result);
    }

    public function filterProvider()
    {
        return [
            'Filter with "select" type' => ['select', false],
            'Filter with "label" type' => ['label', false],
            'Filter with "color" type' => ['color', false],
            'Filter with "image" type' => ['image', false],
            'Filter with "range-slider" type' => ['range-slider', true],
        ];
    }

    /**
     * @dataProvider rangeFacetResultProvider
     *
     * @param array $filterData
     * @param string $field
     * @param string $label
     * @param ConditionInterface|null $condition
     * @param FacetResultInterface|null $facetResult
     */
    public function testGeneratesPartialFacetBasedOnFilterDataAndActiveConditions(
        array $filterData,
        $field,
        $label,
        ConditionInterface $condition = null,
        FacetResultInterface $facetResult = null
    ) {
        $facet = new ProductAttributeFacet(
            $field,
            ProductAttributeFacet::MODE_RANGE_RESULT,
            $field,
            $label
        );
        $criteria = new Criteria();
        if ($condition !== null) {
            $criteria->addCondition($condition);
        }

        $filter = $this->generateFilter($filterData);

        $facetHandler = new RangeFacetHandler();
        $result = $facetHandler->generatePartialFacet($facet, $criteria, $filter);

        $this->assertEquals($facetResult, $result);
    }

    public function rangeFacetResultProvider()
    {
        return [
            'Total range boundaries are the same' => [
                [
                    'name' => 'attr6',
                    'display' => 'Length',
                    'select' => 'single',
                    'type' => 'range-slider',
                    'attributes' => [
                        'totalRange' => [
                            'min' => 4.20,
                            'max' => 4.20
                        ]
                    ]
                ],
                'attr6',
                'Length',
                null,
                null
            ],
            'Price filter is not selected yet' => [
                [
                    'name' => 'price',
                    'display' => 'Preis',
                    'select' => 'single',
                    'type' => 'range-slider',
                    'attributes' => [
                        'totalRange' => [
                            'min' => 4.20,
                            'max' => 69.00
                        ],
                        'selectedRange' => [
                            'min' => 4.20,
                            'max' => 69.00
                        ]
                    ]
                ],
                'price',
                'Preis',
                null,
                new RangeFacetResult(
                    'price',
                    false,
                    'Preis',
                    4.20,
                    69.00,
                    4.20,
                    69.00,
                    'min',
                    'max'
                )
            ],
            'Range filter is active' => [
                [
                    'name' => 'attr6',
                    'display' => 'Length',
                    'select' => 'single',
                    'type' => 'range-slider',
                    'attributes' => [
                        'totalRange' => [
                            'min' => 4.20,
                            'max' => 69.00
                        ],
                        'selectedRange' => [
                            'min' => 4.20,
                            'max' => 6.09
                        ]
                    ]
                ],
                'attr6',
                'Length',
                new ProductAttributeCondition('attr6', ConditionInterface::OPERATOR_EQ, ['min' => 4.20, 'max' => 6.09]),
                new RangeFacetResult(
                    'attr6',
                    true,
                    'Length',
                    4.20,
                    69.00,
                    4.20,
                    6.09,
                    'minattr6',
                    'maxattr6'
                )
            ]
        ];
    }

    /**
     * @param array $filterData
     *
     * @return SimpleXMLElement
     */
    public function generateFilter(array $filterData)
    {
        $data = '<?xml version="1.0" encoding="UTF-8"?><searchResult></searchResult>';
        $filter = new SimpleXMLElement($data);

        // Loop through the data to generate filter xml
        foreach ($filterData as $key => $value) {
            if (is_array($value)) {
                $attributes = $filter->addChild($key);
                foreach ($value as $range => $itemData) {
                    $range = $attributes->addChild($range);
                    foreach ($itemData as $k => $v) {
                        $range->addChild($k, $v);
                    }
                }
            } else {
                $filter->addChild($key, $value);
            }
        }

        return $filter;
    }
}