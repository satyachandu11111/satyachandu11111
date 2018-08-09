<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */


namespace Amasty\Faq\Model;

use Amasty\Faq\Api\Data\VisitStatInterface;
use Amasty\Faq\Api\VisitStatRepositoryInterface;
use Amasty\Faq\Model\ResourceModel\VisitStat as Resource;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;

class VisitStatRepository implements VisitStatRepositoryInterface
{
    /**
     * Model data storage
     *
     * @var array
     */
    private $visitStat;

    /**
     * @var VisitStatFactory
     */
    private $visitStatFactory;

    /**
     * @var Resource
     */
    private $visitStatResource;

    /**
     * VisitStatRepository constructor.
     * @param Resource $visitStatResource
     * @param VisitStatFactory $visitStatFactory
     */
    public function __construct(
        Resource $visitStatResource,
        VisitStatFactory $visitStatFactory
    ) {
        $this->visitStatResource = $visitStatResource;
        $this->visitStatFactory = $visitStatFactory;
    }

    /**
     * @inheritdoc
     */
    public function getById($visitId)
    {
        if (!isset($this->visitStat[$visitId])) {
            /** @var \Amasty\Faq\Model\Tag $tag */
            $tag = $this->visitStatFactory->create();
            $this->visitStatResource->load($tag, $visitId);
            if (!$tag->getTagId()) {
                throw new NoSuchEntityException(__('Visit with specified ID "%1" not found.', $visitId));
            }
            $this->visitStat[$visitId] = $tag;
        }

        return $this->visitStat[$visitId];
    }

    /**
     * @inheritdoc
     */
    public function save(VisitStatInterface $visitStat)
    {
        try {
            if ($visitStat->getVisitId()) {
                $visitStat = $this->getById($visitStat->getVisitId())->addData($visitStat->getData());
            }
            $this->visitStatResource->save($visitStat);
            unset($this->visitStat[$visitStat->getTagId()]);
        } catch (\Exception $e) {
            if ($visitStat->getTagId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save visit with ID %1. Error: %2',
                        [$visitStat->getVisitId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new tag. Error: %1', $e->getMessage()));
        }

        return $visitStat;
    }
}
