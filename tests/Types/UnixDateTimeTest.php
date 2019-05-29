<?php


namespace MandarinMedien\DoctrineDateTypes\Tests\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Tools\SchemaTool;
use MandarinMedien\DoctrineDateTypes\Tests\Entities\UnixDateTime as Entity;
use MandarinMedien\DoctrineDateTypes\Types;
use PHPUnit\Framework\TestCase;

/**
 * Class UnixDateTimeTest
 *
 * @author Enrico Thies <et@mandarin-medien.de>
 */
class UnixDateTimeTest extends TestCase
{
    protected $em = null;

    public static function setUpBeforeClass(): void
    {
        Type::addType('unixdatetime', Types\UnixDateTimeType::class);
        Type::addType('unixdatetime_immutable', Types\UnixDateTimeImmutableType::class);
    }

    /**
     * @inheritDoc
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function setUp(): void
    {
        $config = new \Doctrine\ORM\Configuration();
        $config->setMetadataCacheImpl(new \Doctrine\Common\Cache\ArrayCache());
        $config->setQueryCacheImpl(new \Doctrine\Common\Cache\ArrayCache());
        $config->setProxyDir(__DIR__ . '/Proxies');
        $config->setProxyNamespace('DoctrineExtensions\Tests\PHPUnit\Proxies');
        $config->setAutoGenerateProxyClasses(true);
        $config->setMetadataDriverImpl($config->newDefaultAnnotationDriver(__DIR__ . '/../Entities'));
        $this->em = \Doctrine\ORM\EntityManager::create(
            [
                'driver' => 'pdo_sqlite',
                'memory' => true,
            ],
            $config
        );
        $schemaTool = new SchemaTool($this->em);
        $schemaTool->dropDatabase();
        $schemaTool->createSchema([
            $this->em->getClassMetadata(Entity::class),
        ]);
        $entity = new Entity();
        $entity->id = 1;
        $entity->datetime = new \DateTime('1234-05-06 07:08:09');
        $entity->datetimeImmutable = new \DateTimeImmutable('1234-05-06 07:08:09');
        $this->em->persist($entity);
        $this->em->flush();
    }

    public function testGetter()
    {
        $entity = $this->em->find(Entity::class, 1);
        $this->assertInstanceOf(\DateTime::class, $entity->datetime);
        $this->assertInstanceOf(\DateTimeImmutable::class, $entity->datetimeImmutable);

        $refDate = \DateTime::createFromFormat('Y-m-d H:i:s', '1234-05-06 07:08:09');

        $this->assertEquals($refDate->format('U'), $entity->datetime->format('U'));
        $this->assertEquals($refDate->format('U'), $entity->datetimeImmutable->format('U'));
    }

    public function testSetter()
    {
        $entity = new Entity();
        $entity->id = 2;
        $entity->datetime = new \DateTime('1234-05-06 07:08:09');
        $entity->datetimeImmutable = new \DateTimeImmutable('1234-05-06 07:08:09');
        $this->em->persist($entity);
        $this->assertNull($this->em->flush());
    }

    /**
     * @dataProvider typeProvider
     */
    public function testTypesThatMapToAlreadyMappedDatabaseTypesRequireCommentHint($type)
    {
        /** @var \Doctrine\DBAL\Platforms\AbstractPlatform $platform */
        $platform = $this->getMockForAbstractClass(AbstractPlatform::class);
        $this->assertTrue(Type::getType($type)->requiresSQLCommentHint($platform));
    }
    public function typeProvider()
    {
        return [
            ['unixdatetime'],
            ['unixdatetime_immutable'],
        ];
    }
}
