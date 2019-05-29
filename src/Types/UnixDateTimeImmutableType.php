<?php

namespace MandarinMedien\DoctrineDateTypes\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;

/**
 * Class UnixDateTimeImmutableType
 *
 * @author Enrico Thies <et@mandarin-medien.de>
 */
class UnixDateTimeImmutableType extends UnixDateTimeType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'unixdatetime_immutable';
    }

    /**
     * @inheritdoc
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return $value;
        }

        if ($value instanceof \DateTimeImmutable) {
            return (int)$value->format('U');
        }

        throw ConversionException::conversionFailedInvalidType(
            $value,
            $this->getName(),
            ['null', \DateTimeImmutable::class]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null || $value instanceof \DateTimeImmutable) {
            return $value;
        }

        $dateTime = \DateTimeImmutable::createFromFormat('U', $value);

        if (!$dateTime) {
            $dateTime = date_create_immutable($value);
        }

        if (!$dateTime) {
            throw ConversionException::conversionFailed($value, $this->getName());
        }

        return $dateTime;
    }
}
