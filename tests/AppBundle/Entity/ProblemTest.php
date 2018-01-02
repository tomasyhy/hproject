<?php
/**
 * Created by PhpStorm.
 * User: tom
 * Date: 02.12.17
 * Time: 16:20
 */

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Problem;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProblemTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        exec('bin/console doctrine:schema:update --force --env=test');
        exec('php bin/console doctrine:fixtures:load --no-interaction --env=test');

        $this->em = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testCreate(): void
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['enabled' => User::ENABLED]);

        $problem = new Problem();
        $problem->setDescription('description');
        $problem->setTitle('title');
        $problem->setUser($user);

        $this->em->persist($problem);
        $this->em->flush();

        $problems = $this->em->getRepository(Problem::class)->findAll();

        $this->assertCount(1, $problems);
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        $this->em->close();
        $this->em = null; // avoid memory leaks
    }
}