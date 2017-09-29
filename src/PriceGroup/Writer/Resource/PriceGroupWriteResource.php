<?php declare(strict_types=1);

namespace Shopware\PriceGroup\Writer\Resource;

use Shopware\Context\Struct\TranslationContext;
use Shopware\Framework\Write\Field\SubresourceField;
use Shopware\Framework\Write\Field\TranslatedField;
use Shopware\Framework\Write\Field\UuidField;
use Shopware\Framework\Write\Flag\Required;
use Shopware\Framework\Write\WriteResource;

class PriceGroupWriteResource extends WriteResource
{
    protected const UUID_FIELD = 'uuid';
    protected const NAME_FIELD = 'name';

    public function __construct()
    {
        parent::__construct('price_group');

        $this->primaryKeyFields[self::UUID_FIELD] = (new UuidField('uuid'))->setFlags(new Required());
        $this->fields[self::NAME_FIELD] = new TranslatedField('name', \Shopware\Shop\Writer\Resource\ShopWriteResource::class, 'uuid');
        $this->fields['translations'] = (new SubresourceField(\Shopware\PriceGroup\Writer\Resource\PriceGroupTranslationWriteResource::class, 'languageUuid'))->setFlags(new Required());
        $this->fields['discounts'] = new SubresourceField(\Shopware\PriceGroupDiscount\Writer\Resource\PriceGroupDiscountWriteResource::class);
    }

    public function getWriteOrder(): array
    {
        return [
            \Shopware\PriceGroup\Writer\Resource\PriceGroupWriteResource::class,
            \Shopware\PriceGroup\Writer\Resource\PriceGroupTranslationWriteResource::class,
            \Shopware\PriceGroupDiscount\Writer\Resource\PriceGroupDiscountWriteResource::class,
        ];
    }

    public static function createWrittenEvent(array $updates, TranslationContext $context, array $errors = []): \Shopware\PriceGroup\Event\PriceGroupWrittenEvent
    {
        $event = new \Shopware\PriceGroup\Event\PriceGroupWrittenEvent($updates[self::class] ?? [], $context, $errors);

        unset($updates[self::class]);

        if (!empty($updates[\Shopware\PriceGroup\Writer\Resource\PriceGroupWriteResource::class])) {
            $event->addEvent(\Shopware\PriceGroup\Writer\Resource\PriceGroupWriteResource::createWrittenEvent($updates, $context));
        }
        if (!empty($updates[\Shopware\PriceGroup\Writer\Resource\PriceGroupTranslationWriteResource::class])) {
            $event->addEvent(\Shopware\PriceGroup\Writer\Resource\PriceGroupTranslationWriteResource::createWrittenEvent($updates, $context));
        }
        if (!empty($updates[\Shopware\PriceGroupDiscount\Writer\Resource\PriceGroupDiscountWriteResource::class])) {
            $event->addEvent(\Shopware\PriceGroupDiscount\Writer\Resource\PriceGroupDiscountWriteResource::createWrittenEvent($updates, $context));
        }

        return $event;
    }
}