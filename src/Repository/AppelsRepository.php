<?php

namespace App\Repository;

use App\Entity\Appels;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Gedmo\Translatable\TranslatableListener;
use Doctrine\ORM\Query;
use Gedmo\Translatable\Query\TreeWalker\TranslationWalker;

/**
 * @extends ServiceEntityRepository<Appels>
 */
class AppelsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Appels::class);
    }


    public function findOnlyTranslated(string $locale): array
        {
            $em = $this->getEntityManager();

            $dql = "
                SELECT a
                FROM App\Entity\Appels a
                JOIN App\Entity\Translation t
                    WITH t.foreignKey = a.id AND t.locale = :locale AND t.objectClass = :class
                ORDER BY a.date DESC
            ";

            $query = $em->createQuery($dql);
            $query->setParameter('locale', $locale);
            $query->setParameter('class', Appels::class);
            $query->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, TranslationWalker::class);
            $query->setHint(TranslatableListener::HINT_TRANSLATABLE_LOCALE, $locale);

            return $query->getResult();
        }

        public function findAllOnlyTranslated(string $locale): array
        {
            $em = $this->getEntityManager();

            $dql = "
                SELECT a
                FROM App\Entity\Appels a
                JOIN App\Entity\Translation t
                    WITH t.foreignKey = a.id AND t.locale = :locale AND t.objectClass = :class
                ORDER BY a.date DESC
            ";

            $query = $em->createQuery($dql);
            $query->setParameter('locale', $locale);
            $query->setParameter('class', Appels::class);
            $query->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, TranslationWalker::class);
            $query->setHint(TranslatableListener::HINT_TRANSLATABLE_LOCALE, $locale);
            $query->setMaxResults(4);

            return $query->getResult();
        }


    //    /**
    //     * @return Appels[] Returns an array of Appels objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Appels
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
