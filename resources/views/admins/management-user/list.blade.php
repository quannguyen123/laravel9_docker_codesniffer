@extends('admins.layouts.master')

@section('content')
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>User List</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <a class="btn btn-primary btn-sm" href="{{ route('admin.user.create') }}">
                <i class="fas fa-folder">
                </i>
                Add
              </a>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Default box -->
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">User List</h3>

          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
              <i class="fas fa-minus"></i>
            </button>
            <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
              <i class="fas fa-times"></i>
            </button>
            
          </div>
        </div>
        <div class="card-body p-0">
          <div class="form-group">
            <div class="card-header">
              <div class="card-tools">
                <div class="form-group">
                  <form action="{{ route('admin.user.index') }}" method="GET">
                    <div class="input-group input-group">
                      <input type="text" name="search" class="form-control form-control" placeholder="Name or Email" value="{{$_GET['search']??''}}">
                      <div class="input-group-append">
                          <button type="submit" class="btn btn btn-default">
                              <i class="fa fa-search"></i>
                          </button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
              <table class="table table-striped projects">
                  <thead>
                      <tr>
                          <th style="width: 1%">
                              #
                          </th>
                          <th style="width: 20%">
                              Name
                              <div class="">
                                <div class="col-12">
                                    <a href="{{ request()->fullUrlWithQuery(['orderBy' => 'created_at', 'orderType' => 'asc']) }}">
                                        <img src="{{asset('admin/img/icons/icon-up.svg')}}" />
                                    </a>
                                </div>
                                <div class="col-12">
                                    <a href="{{ request()->fullUrlWithQuery(['orderBy' => 'created_at', 'orderType' => 'desc']) }}">
                                        <img src="{{asset('admin/img/icons/icon-down.svg')}}" />
                                    </a>
                                </div>
                              </div>
                          </th>
                          <th style="width: 30%">
                              Email
                          </th>
                          <th style="width: 8%" class="text-center">
                              Status
                          </th>
                          <th style="width: 10%">
                              Created At
                          </th>
                          <th style="width: 20%">
                          </th>
                      </tr>
                  </thead>
                  <tbody>
                    @foreach($data['users'] as $user)
                      <tr class="item-{{ $user->id }}">
                          <td>
                            {{ $user->id }}
                          </td>
                          <td>
                            <a>
                              {{ $user->name }}
                            </a>
                          </td>
                          <td>
                            {{ $user->email }}
                          </td>
                          
                          <td class="project-state">
                              <span class="badge badge-success">Success</span>
                          </td>

                          <td>
                            {{ $user->created_at }}
                          </td>

                          <td class="project-actions text-right">
                              <a class="btn btn-primary btn-sm" href="{{ route('admin.user.edit', $user->id) }}">
                                  <i class="fas fa-folder">
                                  </i>
                                  View
                              </a>
                              <a class="btn btn-info btn-sm" href="{{ route('admin.user.edit', $user->id) }}">
                                  <i class="fas fa-pencil-alt">
                                  </i>
                                  Edit
                              </a>

                              <td>
                                  <a href="#" 
                                      class="btn btn-danger btn-sm modal-delete" 
                                      data-id={{ $user->id }}
                                      data-href="{{ route('admin.user.destroy', $user->id) }}"
                                      data-method="delete"
                                      data-toggle="modal"
                                      data-title="Title confirm"
                                      data-description="Description confirm"
                                      data-btnNo="Cancel"
                                      data-btnYes="Delete"
                                      data-target="#modalConfirm">
                                    <i class="fas fa-trash"></i>
                                    Delete
                                  </a>
                              </td>


                          </td>
                      </tr>
                    @endforeach
                    
                  </tbody>
                </table>
                {{ $data['users']->links('admins.layouts.pagination') }}


            </div>
          </div>
        </div>
        <!-- /.card-body -->
      </div>
      <!-- /.card -->

    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Delete Warning Modal -->
  @include('admins.layouts.modal-confirm')

@endsection
