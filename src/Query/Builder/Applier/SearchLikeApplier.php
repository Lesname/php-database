<?php
declare(strict_types=1);

namespace LessDatabase\Query\Builder\Applier;

use Doctrine\DBAL\Query\QueryBuilder;
use LessValueObject\String\Format\SearchTerm;

final class SearchLikeApplier implements Applier
{
    // List of words that are common that using a search on it breaks more than it fixes
    // List is mostly made by checking common words in book title's
    private const SKIP_WORD_LIST = [
        'van',
        'een',
        'het',
        'met',
        'uit',
        'als',
        'tot',
        'der',
        'den',
        'aan',
        'mijn',
        'naar',
        'voor',
        'over',
    ];

    /**
     * @param SearchTerm $term
     * @param array<string, int> $fields
     * @param bool $order
     */
    public function __construct(private SearchTerm $term, private array $fields, private bool $order = true)
    {}

    public function apply(QueryBuilder $builder): QueryBuilder
    {
        $term = preg_replace('/[^[:alnum:][:space:]]/u', ' ', (string)$this->term);
        assert(is_string($term));

        $term = preg_replace('/\s+/', ' ', $term);
        assert(is_string($term));

        $term = trim($term);

        $searcher = $order = [];

        if (str_contains($term, ' ')) {
            $builder->setParameter("term_full", $term);
            $liker = "concat('%', :term_full, '%')";

            foreach ($this->fields as $field => $weight) {
                $fullWeight = $weight * 2;

                $searcher[] = "({$field} like {$liker})";
                $order[] = "if({$field} like {$liker}, {$fullWeight}, 0)";
            }
        }

        foreach (explode(' ', $term) as $i => $part) {
            // Skip common words
            if (in_array(strtolower($part), self::SKIP_WORD_LIST, true)) {
                continue;
            }

            // Skip letters, to general
            if (strlen($part) <= 2) {
                continue;
            }

            $builder->setParameter("term_{$i}", $part);
            $liker = "concat('%', :term_{$i}, '%')";

            foreach ($this->fields as $field => $weight) {
                $searcher[] = "({$field} like {$liker})";
                $order[] = "if({$field} like {$liker}, {$weight}, 0)";
            }
        }

        if (count($searcher) > 0) {
            $builder->andWhere('(' . implode(' OR ', $searcher) . ')');

            if ($this->order) {
                $builder->addOrderBy('(' . implode(' + ', $order) . ')', 'DESC');
            }
        }

        return $builder;
    }
}
