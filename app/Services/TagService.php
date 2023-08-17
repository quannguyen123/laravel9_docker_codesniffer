<?php

namespace App\Services;

use App\Models\TagSuggest;
use App\Repositories\TagRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TagService {
    /**
     * function contructer
     *
     * @param TagRepository $repository
     */
    public function __construct(
        protected readonly TagRepository $repository
    ) {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($request)
    {
        $filters = [];
        if (!empty($request->orderBy) && !empty($request->orderType)) {
            $filters = [
                'orderBy' => $request->orderBy,
                'orderType' => $request->orderType,
            ];
        }

        if (!empty($request->search)) {
            $filters['search'] = $request->search;
        }

        return $this->repository->search($filters);
    }

    public function publicSearchTag($request)
    {
        $filters = [];
        if (!empty($request->orderBy) && !empty($request->orderType)) {
            $filters = [
                'orderBy' => $request->orderBy,
                'orderType' => $request->orderType,
            ];
        }

        if (!empty($request->search)) {
            $filters['search'] = $request->search;
        }

        return $this->repository->publicSearchTag($filters);
    }

    public function getTagSuggest($request) {
        $requestData = $request->only(['occupation_id', 'job_title_id']);

        $tagId = TagSuggest::where('occupation_id', $requestData['occupation_id'])->where('job_title_id', $requestData['job_title_id'])->fluck('tag_id');

        $tags = $this->repository->select('name', 'count_job', 'status')->findWhereIn('id', $tagId);

        return $tags;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $requestData = $request->only(['name', 'occupation_ids', 'job_title_ids']);
        
        $tag = [
            'name' => $requestData['name'],
            'status' => config('custom.status.active'),
            'created_by' => Auth::guard('api-admin')->user()->id
        ];

        $tag = $this->repository->create($tag);

        $occupationIds = (array)$requestData['occupation_ids'];
        $jobTitleIds = (array)$requestData['job_title_ids'];

        $tagSuggest = [];

        if (!empty($occupationIds) && !empty($jobTitleIds)) {
            foreach ($occupationIds as $occupationId) {
                foreach ($jobTitleIds as $jobTitleId) {
                    $tagSuggest[] = [
                        'tag_id' => $tag->id,
                        'occupation_id' => $occupationId,
                        'job_title_id' => $jobTitleId,
                    ];
                }
            }
        } elseif (!empty($occupationIds) && empty($jobTitleIds)) {
            foreach ($occupationIds as $occupationId) {
                $tagSuggest[] = [
                    'tag_id' => $tag->id,
                    'occupation_id' => $occupationId,
                ];
            }
        } elseif (empty($occupationIds) && !empty($jobTitleIds)) {
            foreach ($jobTitleIds as $jobTitleId) {
                $tagSuggest[] = [
                    'tag_id' => $tag->id,
                    'job_title_id' => $jobTitleId,
                ];
            }
        }

        TagSuggest::insert($tagSuggest);

        return $tag;
    }

    public function changeStatus($tag, $status) {
        $tag['status'] = config('custom.status.' . $status);
        $tag->save();

        return [true, $tag, 'Success'];
    }

    public function update(Request $request, $tag)
    {
        $requestData = $request->only(['name', 'occupation_ids', 'job_title_ids']);
        
        $tag['name'] = $requestData['name'];
        $tag['updated_by'] = Auth::guard('api-admin')->user()->id;

        $tag->save();

        $occupationIds = (array)$requestData['occupation_ids'];
        $jobTitleIds = (array)$requestData['job_title_ids'];

        $tagSuggest = [];
        if (!empty($occupationIds) && !empty($jobTitleIds)) {
            foreach ($occupationIds as $occupationId) {
                foreach ($jobTitleIds as $jobTitleId) {
                    $tagSuggest[] = [
                        'tag_id' => $tag->id,
                        'occupation_id' => $occupationId,
                        'job_title_id' => $jobTitleId,
                    ];
                }
            }
        } elseif (!empty($occupationIds) && empty($jobTitleIds)) {
            foreach ($occupationIds as $occupationId) {
                $tagSuggest[] = [
                    'tag_id' => $tag->id,
                    'occupation_id' => $occupationId,
                ];
            }
        } elseif (empty($occupationIds) && !empty($jobTitleIds)) {
            foreach ($jobTitleIds as $jobTitleId) {
                $tagSuggest[] = [
                    'tag_id' => $tag->id,
                    'job_title_id' => $jobTitleId,
                ];
            }
        }

        if (!empty($tagSuggest)) {
            // remove all TagSuggest
            TagSuggest::where('tag_id', $tag->id)->delete();
        }

        TagSuggest::insert($tagSuggest);

        return [true, $tag, 'Success'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($tag)
    {
        // remove all TagSuggest
        TagSuggest::where('tag_id', $tag->id)->delete();

        $tag['deleted_by'] = Auth::guard('api-admin')->user()->id;
        $tag['deleted_at'] = date("Y-m-d H:i:s", time());
        $tag->save();

        return [true, [], 'Success'];
    }
}