<?php

namespace findologicDI\BusinessLogic\Models {


	use Doctrine\ORM\PersistentCollection;
	use FINDOLOGIC\Export\Data\Attribute;
	use FINDOLOGIC\Export\Data\DateAdded;
	use FINDOLOGIC\Export\Data\Description;
	use FINDOLOGIC\Export\Data\Item;
	use FINDOLOGIC\Export\Data\Keyword;
	use FINDOLOGIC\Export\Data\Name;
	use FINDOLOGIC\Export\Data\Ordernumber;
	use FINDOLOGIC\Export\Data\Price;
	use FINDOLOGIC\Export\Data\SalesFrequency;
	use FINDOLOGIC\Export\Data\Summary;
	use FINDOLOGIC\Export\Data\Url;
	use FINDOLOGIC\Export\Exporter;
	use FINDOLOGIC\Export\XML\XMLExporter;
	use findologicDI\ShopwareProcess;
	use Shopware\Bundle\MediaBundle\MediaService;
	use Shopware\Components\Routing\Router;
	use Shopware\Models\Article\Article;
	use Shopware\Models\Article\Detail;
	use Shopware\Models\Article\Image;
	use Shopware\Models\Article\Supplier;
	use Shopware\Models\Category\Category;
	use Shopware\Models\Customer\Group;
	use Shopware\Models\Order\Order;
	use Shopware\Models\Property\Option;
	use Shopware\Models\Property\Value;

	class FindologicArticleModel {

		/**
		 * @var XMLExporter
		 */
		var $exporter;

		/**
		 * @var Item
		 */
		var $xmlArticle;

		/**
		 * @var Article
		 */
		var $baseArticle;

		/**
		 * @var \Shopware\Models\Article\Detail
		 */
		var $baseVariant;

		/**
		 * @var string
		 */
		var $shopKey;

		/**
		 * @var PersistentCollection
		 */
		var $variantArticles;

		/**
		 * @var array
		 */
		var $allUserGroups;

		/**
		 * @var array
		 */
		var $salesFrequency;


		/**
		 * @var \sRewriteTable
		 */
		var $seoRouter;


		/**
		 * FindologicArticleModel constructor.
		 *
		 * @param Article $shopwareArticle
		 * @param string $shopKey
		 * @param array $allUserGroups
		 *
		 * @param array $salesFrequency
		 *
		 * @throws \Exception
		 */
		public function __construct( Article $shopwareArticle, string $shopKey, array $allUserGroups, array $salesFrequency ) {

			$this->exporter       = Exporter::create( Exporter::TYPE_XML );
			$this->xmlArticle     = $this->exporter->createItem( $shopwareArticle->getId() );
			$this->shopKey        = $shopKey;
			$this->baseArticle    = $shopwareArticle;
			$this->baseVariant    = $this->baseArticle->getMainDetail();
			$this->salesFrequency = $salesFrequency;
			$this->allUserGroups  = $allUserGroups;
			$this->seoRouter      = Shopware()->Container()->get( 'modules' )->sRewriteTable();

			// Load all variants
			$this->variantArticles = $this->baseArticle->getDetails();

			// Fill out the Basedata
			$this->setArticleName( $this->baseArticle->getName() );

			if ( trim( $this->baseArticle->getDescription() ) ) {
				$this->setSummary( $shopwareArticle->getDescription() );
			}

			if ( trim( $this->baseArticle->getDescriptionLong() ) ) {
				$this->setDescription( $shopwareArticle->getDescriptionLong() );
			}

			$this->setAddDate();
			$this->setUrls();
			$this->setKeywords();
			$this->setImages();
			$this->setSales();
			$this->setAddProperties();


			$this->setPrices();
			$this->setVariantOrdernumbers();
		}

		public function getXmlRepresentation() {
			return $this->xmlArticle;
		}

		protected function setArticleName( string $name, string $userGroup = null ) {
			$xmlName = new Name();
			$xmlName->setValue( $name, $userGroup );
			$this->xmlArticle->setName( $xmlName );
		}

		protected function setVariantOrdernumbers() {

			/** @var Detail $detail */
			foreach ( $this->variantArticles as $detail ) {

				// Remove inactive variants
				if ( $detail->getActive() == 0 ) {
					continue;
				}
				$this->xmlArticle->addOrdernumber( new Ordernumber( $detail->getNumber() ) );

				if ( $detail->getEan() ) {
					$this->xmlArticle->addOrdernumber( new Ordernumber( $detail->getEan() ) );
				}

				if ( $detail->getSupplierNumber() ) {
					$this->xmlArticle->addOrdernumber( new Ordernumber( $detail->getSupplierNumber() ) );
				}

			}

		}

		protected function setSummary( string $description ) {
			$summary = new Summary();
			$summary->setValue( trim( $description ) );
			$this->xmlArticle->setSummary( $summary );
		}

		protected function setDescription( string $descriptionLong ) {
			$description = new Description();
			$description->setValue( $descriptionLong );
			$this->xmlArticle->setDescription( $description );
		}

		protected function setPrices() {
			$priceArray = array();
			// variant prices per customergroup
			/** @var Detail $detail */
			foreach ( $this->variantArticles as $detail ) {
				if ( $detail->getActive() == 1 ) {
					/** @var \Shopware\Models\Article\Price $price */
					foreach ( $detail->getPrices() as $price ) {
						/** @var \Shopware\Models\Customer\Group $customerGroup */
						$customerGroup = $price->getCustomerGroup();
						if ( $customerGroup ) {
							$priceArray[ $customerGroup->getKey() ][] = $price->getPrice();
						}

					}
				}
			}

			// main prices per customergroup
			foreach ( $this->baseVariant->getPrices() as $price ) {
				if ( $price->getCustomerGroup() ) {
					/** @var \Shopware\Models\Customer\Group $customerGroup */
					$customerGroup = $price->getCustomerGroup();
					if ( $customerGroup ) {
						$priceArray[ $customerGroup->getKey() ][] = $price->getPrice();
					}

				}
			}

			$tax = $this->baseArticle->getTax();

			// searching the loweset price for each customer group
			/** @var Group $userGroup */
			foreach ( $this->allUserGroups as $userGroup ) {
				if ( array_key_exists( $userGroup->getKey(), $priceArray ) ) {
					$price = min( $priceArray[ $userGroup->getKey() ] );
				} else {
					$price = min( $priceArray['EK'] );
				}

				// Add taxes if needed
				if ( $userGroup->getTax() ) {
					$price = $price * ( 1 + (float) $tax->getTax() / 100 );
				}

				$xmlPrice = new Price();
				$xmlPrice->setValue( sprintf( '%.2f', $price ), ShopwareProcess::calculateUsergroupHash( $userGroup->getKey(), $this->shopKey ) );
				$this->xmlArticle->setPrice( $xmlPrice );

				if ( $userGroup->getKey() == 'EK' ) {
					$basePrice = new Price();
					$xmlPrice->setValue( sprintf( '%.2f', $price ) );
					$this->xmlArticle->setPrice( $basePrice );
				}
			}

		}

		protected function setAddDate() {
			$dateAdded = new DateAdded();
			$dateAdded->setDateValue( $this->baseArticle->getAdded() );
			$this->xmlArticle->setDateAdded( $dateAdded );
		}

		protected function setSales() {
			$articleId = $this->baseArticle->getId();
			$key       = array_search( $articleId, array_column( $this->salesFrequency, 'articleId' ) );

			if ( $key != false ) {
				$currentSale      = $this->salesFrequency[ $key ];
				$articleFrequency = $currentSale[1];
			}

			$salesFrequency = new SalesFrequency();
			$salesFrequency->setValue( isset( $articleFrequency ) ? $articleFrequency : 0 );
			$this->xmlArticle->setSalesFrequency( $salesFrequency );

		}

		protected function setUrls() {
			$baseLink = Shopware()->Config()->get( 'baseFile' ) . '?sViewport=detail&sArticle=' . $this->baseArticle->getId();
			$seoUrl   = Shopware()->Modules()->Core()->sRewriteLink( $baseLink, $this->baseArticle->getName() );
			$xmlUrl   = new Url();
			$xmlUrl->setValue( $seoUrl );
			$this->xmlArticle->setUrl( $xmlUrl );
		}

		protected function setKeywords() {
			$articleKeywordsString = $this->baseArticle->getKeywords();
			// Keywords exists
			if ( $articleKeywordsString != '' ) {
				//iterate through string
				$articleKeywords = explode( ',', $articleKeywordsString );
				$xmlKeywords     = array();
				foreach ( $articleKeywords as $keyword ) {
					if ( $keyword != '' ) {
						$xmlKeyword = new Keyword( $keyword );
						array_push( $xmlKeywords, $xmlKeyword );
					}
				}
				if ( count( $xmlKeywords ) > 0 ) {
					$this->xmlArticle->setAllKeywords( $xmlKeywords );
				}

			}
		}

		protected function setImages() {
			$articleMainImages = $this->baseArticle->getImages()->getValues();
			$mediaService      = Shopware()->Container()->get( 'shopware_media.media_service' );
			$baseLink          = Shopware()->Modules()->Core()->sRewriteLink();
			$imagesArray       = array();
			/** @var Image $articleImage */
			foreach ( $articleMainImages as $articleImage ) {
				if ( $articleImage->getMedia() != null ) {
					$imageDetails = $articleImage->getMedia()->getThumbnailFilePaths();
					if ( count( $imageDetails ) > 0 ) {
						$imagePath = $mediaService->getUrl( array_values( $imageDetails )[0] );
						if ( $imagePath != '' ) {
							$xmlImage = new \FINDOLOGIC\Export\Data\Image( $imagePath );
							array_push( $imagesArray, $xmlImage );
						}

					}
				}

			}
			if ( count( $imagesArray ) > 0 ) {
				$this->xmlArticle->setAllImages( $imagesArray );
			} else {
				$noImage  = $baseLink . 'templates/_default/frontend/_resources/images/no_picture.jpg';
				$xmlImage = new \FINDOLOGIC\Export\Data\Image( $noImage );
				array_push( $imagesArray, $xmlImage );
				$this->xmlArticle->setAllImages( $imagesArray );
			}
		}

		protected function setAddProperties() {

			// Categories to XML Output
			/** @var Attribute $xmlCatProperty */
			$xmlCatProperty = new Attribute( 'cat_url' );

			$catPathArray = array();

			/** @var Category $category */
			foreach ( $this->baseArticle->getAllCategories() as $category ) {
				//Hide inactive categories
				if ( ! $category->getActive() ) {
					continue;
				}
				$catPath = $this->seoRouter->sCategoryPath( $category->getId() );
				array_push( $catPathArray, '/' . implode( '/', $catPath ) );
			}

			$xmlCatProperty->setValues( array_unique( $catPathArray ) );

			$this->xmlArticle->addAttribute( $xmlCatProperty );

			// Supplier
			/** @var Supplier $articleSupplier */
			$articleSupplier = $this->baseArticle->getSupplier();
			if ( isset( $articleSupplier ) ) {
				$xmlSupplier = new Attribute( 'brand' );
				$xmlSupplier->setValues( [ $articleSupplier->getName() ] );
				$this->xmlArticle->addAttribute( $xmlSupplier );
			}

			// Filter Values
			$filters = $this->baseArticle->getPropertyValues();

			/** @var Value $filter */
			foreach ( $filters as $filter ) {

				/** @var Option $option */
				$option    = $filter->getOption();
				$xmlFilter = new Attribute( $option->getName(), [ $filter->getValue() ] );
				$this->xmlArticle->addAttribute( $xmlFilter );
			}

			// Additionaltext Varianten
			$temp = [];
			/** @var Detail $variant */
			foreach ( $this->variantArticles as $variant ) {
				if ( ! $variant->getActive() == 0 ) {
					continue;
				}
				if ( ! empty( $variant->getAdditionalText() ) ) {
					foreach ( explode( ' / ', $variant->getAdditionalText() ) as $value ) {
						$temp[] = $value;
					}
				}
			}

			/* @var $configurator \Shopware\Models\Article\Configurator\Set */
			$configurator = $this->baseArticle->getConfiguratorSet();

			if ( $configurator ) {
				/* @var $option \Shopware\Models\Article\Configurator\Option */
				$options   = $configurator->getOptions();
				$optValues = [];
				foreach ( $options as $option ) {
					$optValues[ $option->getGroup()->getName() ][] = $option->getName();
				}
				//add only options from active variants
				foreach ( $optValues as $key => $value ) {
					if ( ! empty( $temp ) ) {

						$xmlConfig = new Attribute( $key, array_intersect( $value, $temp ) );
						$this->xmlArticle->addAttribute( $xmlConfig );
					} else {
						$xmlConfig = new Attribute( $key,  $value  );
						$this->xmlArticle->addAttribute( $xmlConfig );
					}
				}
			}

		}


	}
}