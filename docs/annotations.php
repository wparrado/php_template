<?php

declare(strict_types=1);
/**
 * @OA\Info(
 *   title="PHP Template API",
 *   version="1.0.0",
 *   description="Hexagonal Laravel template API",
 *
 *   @OA\Contact(email="dev@example.com", name="Maintainer")
 * )
 */

/**
 * @OA\Schema(
 *   schema="Item",
 *   type="object",
 *   required={"id","name"},
 *
 *   @OA\Property(property="id", type="integer", format="int64"),
 *   @OA\Property(property="name", type="string")
 * )
 */

/**
 * @OA\Get(
 *   path="/api/v1/items",
 *   summary="List items",
 *   tags={"Items"},
 *
 *   @OA\Response(
 *     response=200,
 *     description="Successful response",
 *
 *     @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Item"))
 *   )
 * )
 */

/**
 * @OA\Get(
 *   path="/api/health",
 *   summary="Health check",
 *   tags={"Health"},
 *
 *   @OA\Response(
 *     response=200,
 *     description="OK",
 *
 *     @OA\JsonContent(
 *
 *       @OA\Property(property="status", type="string"),
 *       @OA\Property(property="timestamp", type="string", format="date-time")
 *     )
 *   )
 * )
 */
