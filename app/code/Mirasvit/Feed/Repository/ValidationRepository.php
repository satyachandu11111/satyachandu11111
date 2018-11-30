<?php

namespace Mirasvit\Feed\Repository;

use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\ObjectManagerInterface;
use Mirasvit\Feed\Api\Data\ValidationInterface;
use Mirasvit\Feed\Api\Repository\ValidationRepositoryInterface;
use Mirasvit\Feed\Api\Data\ValidationInterfaceFactory;
use Mirasvit\Feed\Model\ResourceModel\Validation\CollectionFactory;
use Mirasvit\Feed\Validator\ValidatorInterface;

class ValidationRepository implements ValidationRepositoryInterface
{
    /**
     * @var ValidationInterface[]
     */
    private $validationRegistry = [];

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var ValidationInterfaceFactory
     */
    private $feedValidationFactory;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var ValidatorInterface[]
     */
    private $validators;
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    public function __construct(
        ObjectManagerInterface $objectManager,
        EntityManager $entityManager,
        ValidationInterfaceFactory $feedValidationFactory,
        CollectionFactory $collectionFactory,
        $validators = []
    ) {
        $this->objectManager = $objectManager;
        $this->entityManager = $entityManager;
        $this->feedValidationFactory = $feedValidationFactory;
        $this->collectionFactory = $collectionFactory;
        $this->validators = $validators;
    }

    /**
     * {@inheritdoc}
     */
    public function getCollection()
    {
        return $this->collectionFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        return $this->feedValidationFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        if (isset($this->validationRegistry[$id])) {
            return $this->validationRegistry[$id];
        }

        $validation = $this->create();
        $validation = $this->entityManager->load($validation, $id);

        if ($validation->getId()) {
            $this->validationRegistry[$id] = $validation;
        } else {
            return false;
        }

        return $validation;
    }

    /**
     * {@inheritdoc}
     */
    public function save(ValidationInterface $model)
    {
        return $this->entityManager->save($model);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(ValidationInterface $model)
    {
        return $this->entityManager->delete($model);
    }

    /**
     * {@inheritdoc}
     */
    public function getValidators()
    {
        return $this->validators;
    }

    /**
     * {@inheritdoc}
     */
    public function getValidatorByCode($code)
    {
        if (isset($this->validators[$code])) {
            return $this->validators[$code];
        }

        $validator = false;
        foreach ($this->validators as $validatorInstance) {
            if ($validatorInstance->getCode() == $code) {
                $validator = $validatorInstance;
                $this->validators[$code] = $validator;
                break;
            }
        }

        return $validator;
    }

    /**
     * {@inheritDoc}
     */
    public function getSchemaValidationService($schemaType)
    {
        return $this->objectManager->get('Mirasvit\Feed\Service\Validation\\'.ucfirst($schemaType).'SchemaValidation');
    }
}
