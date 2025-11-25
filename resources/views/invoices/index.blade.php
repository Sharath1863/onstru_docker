@extends('layouts.app')

@section('title', 'Onstru | Invoices')

@section('content')

    <link rel="stylesheet" href="{{ asset('assets/css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/cards.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/list.css') }}">

    @php
        $my = auth()
            ->user()
            ->loadCount(['followers', 'following']);
    @endphp

    <style>
        .flex-sidebar {
            display: block !important;
        }

        @media screen and (max-width: 767px) {
            .profile-head {
                display: grid !important;
                grid-template-columns: 30% 69%;
                align-items: center;
                justify-content: space-between;
            }

            .side-cards .cards-content h5.label {
                width: 100%;
            }

            .profile-content h6 {
                font-size: 14px !important;
            }
        }
    </style>

    <div class="container-xl main-div">
        <div class="flex-side">
            <!-- Flex Left -->
            <div class="flex-sidebar border-0">
                <div class="flex-cards">

                    <div class="side-cards mb-3">
                        <div class="cards-content">
                            @include('flexleft.profile-card')

                            <hr class="w-75 mx-auto my-3">

                            <div class="profile-tabs">
                                <ul class="nav nav-tabs d-flex justify-content-sm-between justify-content-md-center align-items-start flex-sm-row flex-md-column column-gap-1 border-0"
                                    id="invTab" role="tablist">
                                    @include('invoices.tabs')
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Flex Right -->
            <div class="flex-cards pt-2">
                <div class="tab-content" id="invTabContent">
                    <div class="tab-pane fade show active" id="premium" role="tabpanel">
                        @include('invoices.premium')
                    </div>
                    <div class="tab-pane fade" id="badges" role="tabpanel">
                        @include('invoices.badges')
                    </div>
                    <div class="tab-pane fade" id="projects" role="tabpanel">
                        @include('invoices.projects')
                    </div>
                    <div class="tab-pane fade" id="products" role="tabpanel">
                        @include('invoices.products')
                    </div>
                    <div class="tab-pane fade" id="jobs" role="tabpanel">
                        @include('invoices.jobs')
                    </div>
                    <div class="tab-pane fade" id="services" role="tabpanel">
                        @include('invoices.services')
                    </div>
                    <div class="tab-pane fade" id="leads" role="tabpanel">
                        @include('invoices.leads')
                    </div>
                    <div class="tab-pane fade" id="readytowork" role="tabpanel">
                        @include('invoices.readytowork')
                    </div>
                    <div class="tab-pane fade" id="chatbot" role="tabpanel">
                        @include('invoices.chatbots')
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function () {
            function initTable(tableId, dropdownId, filterInputId) {
                var table = $(tableId).DataTable({
                    "paging": true,
                    "searching": true,
                    "ordering": true,
                    "order": [0, "asc"],
                    "bDestroy": true,
                    "info": false,
                    "responsive": true,
                    "pageLength": 30,
                    "dom": '<"top"f>rt<"bottom"ilp><"clear">',
                });
                $(tableId + ' thead th').each(function (index) {
                    var headerText = $(this).text();
                    if (headerText != "" && headerText.toLowerCase() != "action") {
                        $(dropdownId).append('<option value="' + index + '">' + headerText + '</option>');
                    }
                });
                $(filterInputId).on('keyup', function () {
                    var selectedColumn = $(dropdownId).val();
                    if (selectedColumn !== 'All') {
                        table.column(selectedColumn).search($(this).val()).draw();
                    } else {
                        table.search($(this).val()).draw();
                    }
                });
                $(dropdownId).on('change', function () {
                    $(filterInputId).val('');
                    table.search('').columns().search('').draw();
                });
                $(filterInputId).on('keyup', function () {
                    table.search($(this).val()).draw();
                });
            }
            // Initialize each table
            initTable('#badgeTable', '#badgeDropdown', '#badgeInput');
            initTable('#premiumTable', '#premiumDropdown', '#premiumInput');
            initTable('#projectTable', '#projectDropdown', '#projectInput');
            initTable('#productTable', '#productDropdown', '#productInput');
            initTable('#serviceTable', '#serviceDropdown', '#serviceInput');
            initTable('#jobTable', '#jobDropdown', '#jobInput');
            initTable('#leadTable', '#leadDropdown', '#leadInput');
            initTable('#readyTable', '#readyDropdown', '#readyInput');
            initTable('#chatbotTable', '#chatbotDropdown', '#chatbotInput');
        });
    </script>

@endsection