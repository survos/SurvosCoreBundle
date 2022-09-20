<?php


namespace Survos\CoreBundle\Traits;

trait QueryBuilderHelperTrait
{
    public function getCounts($field): array
    {
        $results = $this->createQueryBuilder('s')
            ->groupBy('s.' . $field)
            ->select(["s.$field, count(s) as count"])
            ->getQuery()
            ->getArrayResult();
        $counts = [];
        foreach ($results as $r) {
            $counts[$r[$field]] = $r['count'];
        }

        return $counts;
    }

    public function getCountsWithSortDoesntWork($field, string $alias = 'e', array $orderBy = []): array
    {
        $selectFields = ["$alias.$field, count($alias) as count"];
        $groupByFields = [$alias . '.' . $field];
        $qb = $this->createQueryBuilder($alias);

        foreach ($orderBy as $orderByField => $sortOrder) {
            $selectFields[] = "$alias.$orderByField";
            $groupByFields[] = "$alias.$orderByField";
            $qb
                ->orderBy($alias . '.' . $orderByField, $sortOrder);
        }
        $results = $qb
//            ->groupBy(join(',', $groupByFields))
            ->groupBy($selectFields)
            ->select($selectFields)
            ->getQuery()
            ->getArrayResult();
        $counts = [];
        foreach ($results as $r) {
            $counts[$r[$field]] = $r['count'];
        }
        dd($results);

        return $counts;
    }

    public function findBygetCountsByField($field = 'marking', $filters = [], ?string $idField = 'id'): array
    {
        $qb = $this->createQueryBuilder('article')
            // ->where("h.currentState = 'new'")
            ->select(sprintf('COUNT(article%s) as c, article.%s as field ', $idField ? '.' . $idField : '', $field));

        foreach ($filters as $table => $value) {
            if ($value = $filters[$table]) {
                $qb->join('article.' . $table, $table);
                if (true || $value) {
                    $qb->andWhere("article.$table = :$table")->setParameter($table, $value);
                }
            }
        }

        $counts = [];
        $markingCounts = $qb
            ->groupBy('article.' . $field)
            ->getQuery()
            ->getResult();

        foreach ($markingCounts as $x) {
            $counts[$x['field']] = $x['c'];
        }
        return $counts;
    }
}
