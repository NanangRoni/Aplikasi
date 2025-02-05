@extends('layouts.admin')
@section('content')
@can('permission_create')
<div style="margin-bottom: 10px;" class="row">
    <div class="col-lg-12">
        <a class="btn btn-success" href="{{ route("admin.permissions.create") }}">
            Add Permission
        </a>
    </div>
</div>
@endcan
<div class="card">
    <div class="card-header">
        Permission List
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-Permission">
                <thead>
                    <tr>
                        <th>
                            ID
                        </th>
                        <th>
                            Permission
                        </th>
                        <th>
                            Action
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($permissions as $key => $permission)
                    <tr data-entry-id="{{ $permission->id }}">
                        <td>
                            {{ $permission->id ?? '' }}
                        </td>
                        <td>
                            {{ $permission->title ?? '' }}
                        </td>
                        <td>
                            @can('permission_show')
                            <a class="btn btn-xs btn-primary"
                                href="{{ route('admin.permissions.show', $permission->id) }}">
                                View
                            </a>
                            @endcan

                            @can('permission_edit')
                            <a class="btn btn-xs btn-info"
                                href="{{ route('admin.permissions.edit', $permission->id) }}">
                                Edit
                            </a>
                            @endcan
                            @can('permission_delete')
                            <form action="{{ route('admin.permissions.destroy', $permission->id) }}" method="POST"
                                onsubmit="return confirm('Anda Yakin Menghapus Data?');"
                                style="display: inline-block;">
                                <input type="hidden" name="_method" value="DELETE">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="submit" class="btn btn-xs btn-danger" value="Delete">
                            </form>
                            @endcan
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
@section('scripts')
@parent
<script>
    $(function () {
        let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
        @can('permission_delete')
        let deleteButtonTrans = '{{ trans('
        global.datatables.delete ') }}'
        let deleteButton = {
            text: deleteButtonTrans,
            url: "{{ route('admin.permissions.massDestroy') }}",
            className: 'btn-danger',
            action: function (e, dt, node, config) {
                var ids = $.map(dt.rows({
                    selected: true
                }).nodes(), function (entry) {
                    return $(entry).data('entry-id')
                });
                if (ids.length === 0) {
                    alert('{{ trans('
                        global.datatables.zero_selected ') }}')
                    return
                }
                if (confirm('{{ trans('
                        global.areYouSure ') }}')) {
                    $.ajax({
                            headers: {
                                'x-csrf-token': _token
                            },
                            method: 'POST',
                            url: config.url,
                            data: {
                                ids: ids,
                                _method: 'DELETE'
                            }
                        })
                        .done(function () {
                            location.reload()
                        })
                }
            }
        }
        dtButtons.push(deleteButton)
        @endcan
        $.extend(true, $.fn.dataTable.defaults, {
            order: [
                [1, 'desc']
            ],
            pageLength: 100,
        });
        $('.datatable-Permission:not(.ajaxTable)').DataTable({
            buttons: dtButtons
        })
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            $($.fn.dataTable.tables(true)).DataTable()
                .columns.adjust();
        });
    })
</script>
@endsection
