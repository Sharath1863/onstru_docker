@extends('layouts.app')

@section('title', 'Onstru | Order Status')

@section('content')

    <link rel="stylesheet" href="{{ asset('assets/css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/list.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/cards.css') }}">

    <style>
        .flex-sidebar {
            display: block !important;
        }
    </style>

    <div class="container-xl main-div">
        <div class="flex-side">
            <!-- Flex Left -->
            @include('orders.status_aside')

            <!-- Flex Right -->
            <div class="flex-cards pt-2">
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="active" role="tabpanel">
                        @include('orders.status_active')
                    </div>
                    <div class="tab-pane fade" id="processing" role="tabpanel">
                        @include('orders.status_processing')
                    </div>
                    {{-- <div class="tab-pane fade" id="cancelled" role="tabpanel">
                        @include('orders.status_cancelled')
                    </div> --}}
                    <div class="tab-pane fade" id="out_for_delivery" role="tabpanel">
                        @include('orders.status_outfordelivery')
                    </div>
                    <div class="tab-pane fade" id="completed" role="tabpanel">
                        @include('orders.status_completed')
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Datable Script -->
    <script>
        $(document).ready(function () {
            var tables = ['1', '2', '3', '4', '5'];
            tables.forEach(id => {
                $(`#table${id}`).DataTable({
                    "paging": true,
                    "searching": true,
                    "ordering": true,
                    "bDestroy": true,
                    "info": false,
                    "responsive": true,
                    "pageLength": 10,
                    "dom": '<"top"f>rt<"bottom"lp><"clear">',
                });
            });
        });
    </script>

@endsection