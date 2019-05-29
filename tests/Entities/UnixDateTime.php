<?php

namespace MandarinMedien\DoctrineDateTypes\Tests\Entities;

/**
 * Class UnixDateTime
 *
 * @author Enrico Thies <et@mandarin-medien.de>
 *
 * @Entity
 * @Table
 */
class UnixDateTime
{
    /**
     * @Id
     * @Column(type="integer")
     */
    public $id;

    /**
     * @Column(type="unixdatetime", nullable=true)
     */
    public $datetime;

    /**
     * @Column(type="unixdatetime_immutable", nullable=true)
     */
    public $datetimeImmutable;
}
