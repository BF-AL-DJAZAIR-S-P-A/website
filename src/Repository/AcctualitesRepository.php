<?php

namespace App\Repository;

use App\Entity\Acctualites;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Gedmo\Translatable\TranslatableListener;
use Doctrine\ORM\Query;
use Gedmo\Translatable\Query\TreeWalker\TranslationWalker;

/**
 * @extends ServiceEntityRepository<Acctualites>
 */
class AcctualitesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Acctualites::class);
    }

    public function findOnlyTranslated(string $locale): array
    {
        $em = $this->getEntityManager();

        $dql = "
            SELECT a
            FROM App\Entity\Acctualites a
            JOIN App\Entity\Translation t
                WITH t.foreignKey = a.id AND t.locale = :locale AND t.objectClass = :class
            ORDER BY a.date DESC
        ";

        $query = $em->createQuery($dql);
        $query->setParameter('locale', $locale);
        $query->setParameter('class', Acctualites::class);
        $query->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, TranslationWalker::class);
        $query->setHint(TranslatableListener::HINT_TRANSLATABLE_LOCALE, $locale);

        return $query->getResult();
    }



}
