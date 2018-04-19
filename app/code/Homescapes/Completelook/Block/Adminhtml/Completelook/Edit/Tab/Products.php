<?php
namespace Homescapes\Completelook\Block\Adminhtml\Completelook\Edit\Tab;

use Homescapes\Completelook\Block\Adminhtml\Completelook\Edit\Tab\Grid\Product;
use Magento\Framework\View\Element\BlockInterface;
use Magento\Framework\Exception\LocalizedException;


class Products extends \Magento\Backend\Block\Template
{
    /**
     * Block template
     *
     * @var string
     */
    protected $_template = 'products/completelook_products.phtml';

    /**
     * @var Slide
     */
    private $blockGrid;

    /**
     * Retrieve instance of grid block
     *
     * @return BlockInterface
     * @throws LocalizedException
     */
    public function getBlockGrid()
    {
        if (!$this->blockGrid) {
            $this->blockGrid = $this->getLayout()->createBlock(
                Product::class,
                'completelook.product.grid'
            );
        }
        return $this->blockGrid;
    }
    
    
    
    
}

