<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class IngredientService {
    /**
     * Retorna todos los padres de 1 o más ingredientes mediante una consulta SQL recursiva
     * @param array $ingredientIds
     * @return \Illuminate\Support\Collection
     */
    static public function getParentIngredients($ingredientIds)
    {
        if (count($ingredientIds) === 0) {
            return collect();
        }

        $placeholder = implode(',', array_fill(0, count($ingredientIds), '?'));

        $query = "
            WITH RECURSIVE cte
            AS
            (
                SELECT id, name
                FROM ingredients
                WHERE id IN($placeholder)

                UNION ALL

                SELECT i.id, i.name
                FROM cte
                JOIN ingredient_ingredient pivot ON cte.id = pivot.child_id
                JOIN ingredients i ON pivot.parent_id = i.id
                WHERE NOT i.id IN(1, 2, 3)
            )
            SELECT DISTINCT id, name from cte;
        ";

        return collect(DB::select($query, $ingredientIds));
    }

    /**
     * Retorna todos los hijos de 1 o más ingredientes mediante una consulta SQL recursiva
     * @param array $ingredientIds
     * @return \Illuminate\Support\Collection
     */
    static public function getChildrenIngredients($ingredientIds) {
        if (count($ingredientIds) === 0) {
            return collect();
        }

        $placeholder = implode(',', array_fill(0, count($ingredientIds), '?'));

        $query = "
            WITH RECURSIVE cte
            AS
            (
                SELECT id, name
                FROM ingredients
                WHERE id IN($placeholder)

                UNION all

                SELECT i.id, i.name
                FROM cte
                JOIN ingredient_ingredient pivot ON cte.id = pivot.parent_id
                JOIN ingredients i ON pivot.child_id = i.id
            )

            SELECT DISTINCT id, name from cte;
        ";

        return collect(DB::select($query, $ingredientIds));
    }
}
