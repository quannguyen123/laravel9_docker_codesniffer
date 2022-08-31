@if ($paginator->hasPages())
    <div class="row">
        <div class="col-sm-12 col-md-5">
            <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Showing 1 to 10 of 57 entries</div>
        </div>

        <div class="col-sm-12 col-md-7">
            <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                <ul class="pagination float-right">
                    <li class="paginate_button page-item previous @if($paginator->onFirstPage()) disabled @endif" id="example2_previous">
                        <a href="#" aria-controls="example2" data-dt-idx="0" tabindex="0" class="page-link">Previous</a>
                    </li>

                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <li class="paginate_button page-item"><a href="#" aria-controls="example2" data-dt-idx="1" tabindex="0" class="page-link">...</a></li>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <li class="paginate_button page-item active">
                                        <div href="" aria-controls="example2" data-dt-idx="1" tabindex="0" class="page-link">{{ $page }}</div>
                                    </li>
                                @else
                                    <li class="paginate_button page-item">
                                        <a href="{{ $url }}" aria-controls="example2" data-dt-idx="1" tabindex="0" class="page-link">{{ $page }}</a>
                                    </li>
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                    
                    <li class="paginate_button page-item next @if(!$paginator->hasMorePages()) disabled @endif" id="example2_next">
                        <a href="#" aria-controls="example2" data-dt-idx="7" tabindex="0" class="page-link">Next</a>
                    </li>
                </ul>
                <div class="float-right mr-3 row">
                    <p class="float-left mr-2">Number of page</p>
                    <select class="form-control float-left pagination_select" data-href="{{ route('admin.set-cookie') }}" data-method="POST" style="width: auto;">
                        @foreach(config('custom.page-limit') as $item)
                        <option  value={{$item}} @if($paginator->perPage() == $item) selected @endif>{{$item}}</option>
                        @endforeach
                    </select>
                </div>
                
            </div>
        </div>
    </div>

@endif