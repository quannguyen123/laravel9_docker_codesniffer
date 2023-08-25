    /**
     * @OA\Post(
     *     path="/api/partner/service/add-to-cart",
     *     tags={"Partner-Service"},
     *     summary="Thêm dịch vụ vào giỏ hàng",
     *     description="",
     *     security={{"bearer":{}}},
     *     @OA\RequestBody(
     *          @OA\JsonContent(),
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  type="object",
     *                  required={"service_id", "quantity"},
     *                  @OA\Property(property="name", type="string", format="string"),
     *                  @OA\Property(property="service_id", type="integer", format="int64"),
     *                  @OA\Property(property="quantity", type="integer", format="int")
     *              )
     *          ),
     *     ),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */


/**
     * @OA\Get(
     *     path="/api/occupation/index",
     *     summary="Danh sách ngành nghề",
     *     tags={"Job"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(in="query", name="search", required=false, description="Id phúc lợi", @OA\Schema(type="string")),
     *     @OA\Parameter(in="query", name="orderBy", required=false, description="Cột sắp xếp", @OA\Schema(type="string")),
     *     @OA\Parameter(in="query", name="orderType", required=false, description="Loại sắp xếp: DESC or ASC", @OA\Schema(type="string")),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */

    /**
     * @OA\Get(
     *     path="/api/job/detail/{id}",
     *     summary="Thông tin chi tiết job",
     *     tags={"Job"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(in="query", name="id", required=true, description="Id vị trí làm việc", @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */