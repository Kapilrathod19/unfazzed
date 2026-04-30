<x-master-layout>

    <head>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    </head>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-block card-stretch">
                    <div class="card-body p-0">
                        <div class="d-flex justify-content-between align-items-center p-3 flex-wrap gap-3">
                            <h5 class="fw-bold">{{ $pageTitle ?? trans('messages.list') }}</h5>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="row justify-content-between gy-3">
                            <div class="col-md-6 col-lg-4 col-xl-3">
                                <div class="d-flex align-items-center gap-3 justify-content-end">
                                    <div class="input-group input-group-search ms-2">
                                        <span class="input-group-text" id="addon-wrapping"><i
                                                class="fas fa-search"></i></span>
                                        <input type="text" class="form-control dt-search" placeholder="Search..."
                                            aria-label="Search" aria-describedby="addon-wrapping"
                                            aria-controls="dataTableBuilder">
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table id="datatable" class="table table-striped border"></table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            const columns = [
                {
                    data: 'provider_id',
                    name: 'provider_id',
                    title: "{{ __('messages.provider') }}",
                },
                {
                    data: 'type',
                    name: 'type',
                    title: "{{ __('messages.type') }}",
                },
                {
                    data: 'value',
                    name: 'value',
                    title: "{{ __('messages.item') }}",
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                    title: "{{ __('messages.created_at') }}"
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    title: "{{ __('messages.action') }}",
                    className: 'text-end'
                }
            ];

            window.renderedDataTable = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                responsive: true,
                dom: '<"row align-items-center"><"table-responsive my-3 mt-3 mb-2 pb-1" rt><"row align-items-center data_table_widgets" <"col-md-6" <"d-flex align-items-center flex-wrap gap-3" l i>><"col-md-6" p>><"clear">',
                ajax: {
                    "type": "GET",
                    "url": '{{ route('provider-change-request.index_data') }}',
                    "data": function(d) {
                        d.search = {
                            value: $('.dt-search').val()
                        };
                    },
                },
                columns: columns,
                order: [
                    [3, 'desc']
                ],
                language: {
                    processing: "{{ __('messages.processing') }}"
                }
            });
        });
    </script>
    
</x-master-layout>
