    /**
     * @OA\Post(
     *     path="/api/job/job-apply/{id}",
     *     summary="Ứng tuyển Job",
     *     tags={"Job"},
     *     security={{"bearer":{}}},
     *     description="User register",
     *     @OA\Parameter(
     *          in="path",
     *          name="id",
     *          required=true,
     *          description="Order id",
     *          @OA\Schema(
     *            type="integer"
     *          )
     *     ),
     *      @OA\RequestBody(
     *          @OA\JsonContent(),
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  type="object",
     *                  required={"position", "number_phone", "file_cv"},
     *                  @OA\Property(property="position", type="string", format="string", example="Nhân viên", description ="Vị trí ứng tuyển"),
     *                  @OA\Property(property="number_phone", type="string", format="string"),
     *                  @OA\Property(property="file_cv", type="string", format="binary")
     *              )
     *          ),
     *     ),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */


    /**
     * @OA\Get(
     *     path="/api/job/index",
     *     summary="Danh sách các job",
     *     tags={"Job"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(in="query", name="search", required=false, description="Id phúc lợi", @OA\Schema(type="string")),
     *     @OA\Parameter(in="query", name="search", required=false, description="Id phúc lợi", @OA\Schema(type="string")),
     *     @OA\Parameter(in="query", name="search", required=false, description="Id phúc lợi", @OA\Schema(type="string")),
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