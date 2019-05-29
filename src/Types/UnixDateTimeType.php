<?php

namespace MandarinMedien\DoctrineDateTypes\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;

/**
 * Class UnixDateTimeType
 *
 * @author Enrico Thies <et@mandarin-medien.de>
 */
class UnixDateTimeType extends Type
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'unixdatetime';
    }

    /**
     * @inheritDoc
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return $platform->getBigIntTypeDeclarationSQL($fieldDeclaration);
    }

    /**
     * @inheritdoc
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (null === $value) {
            return $value;
        }

        if ($value instanceof \DateTimeInterface) {
            return (int) $value->format('U');
        }

        throw ConversionException::conversionFailedInvalidType($value, $this->getName(), ['null', \DateTime::class]);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null || $value instanceof \DateTimeInterface) {
            return $value;
        }

        $val = \DateTime::createFromFormat('U', $value);

        if (!$val) {
            throw ConversionException::conversionFailed($value, $this->getName());
        }

        return $val;
    }

    /**
     * @inheritDoc
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }
}
