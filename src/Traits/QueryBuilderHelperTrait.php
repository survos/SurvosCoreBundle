<?php


namespace Survos\CoreBundle\Traits;

trait QueryBuilderHelperTrait
{
    public function getCounts(string $field): array
    {
        $results = $this->createQueryBuilder('s')
            ->groupBy('s.' . $field)
            ->select(["s.$field, count(s) as count"])
            ->getQuery()
            ->getArrayResult();
        $counts = [];
        foreach ($results as $r) {
            assert(is_string($field));
            assert(is_array($r));
//            dump($field, $r, $r['count'], $r['field']);
            $key = $r[$field] ?? ''; // not really...
            if (is_array($key)) {
                continue; // doctrine can't handle arrays for facets, just scalars
                dd($key, $field, $r);
            }

            $count = $r['count'];
            assert(is_integer($key) || is_string($key), json_encode($key));
            assert(is_integer($count));
            $counts[$key] = $count;
        }
//        dd($counts);

        return $counts;
    }

    public function getApproxCount(): ?int
    {
        static $counts = null;

            if (is_null($counts)) {
                $rows = $this->getEntityManager()->getConnection()->fetchAllAssociative(
                    "SELECT n.nspname AS schema_name,
       c.relname AS table_name,
       c.reltuples AS estimated_rows
FROM pg_class c
JOIN pg_namespace n ON n.oid = c.relnamespace
WHERE c.relkind = 'r'
  AND n.nspname NOT IN ('pg_catalog', 'information_schema')  -- exclude system schemas
ORDER BY n.nspname, c.relname;");

                $counts = array_combine(
                    array_map(fn($r) => "{$r['table_name']}", $rows),
                    array_map(fn($r) => (int)$r['estimated_rows'], $rows)
                );
            }
            dump($counts);
            $count = $counts[$this->getClassMetadata()->getTableName()]??-1;

//            // might be sqlite
//            $count =  (int) $this->getEntityManager()->getConnection()->fetchOne(
//                'SELECT reltuples::BIGINT FROM pg_class WHERE relname = :table',
//                ['table' => $this->getClassMetadata()->getTableName()]
//            );
        try {
        } catch (\Exception $e) {
            $count = -1;
        }

        // if no analysis
        if ($count < 0) {
            // Fallback to exact count
            $count = (int)$this->createQueryBuilder('e')
                ->select('COUNT(e.id)')
                ->getQuery()
                ->getSingleScalarResult();
        }

        return $count;
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
