@extends('layouts.admin')

@section('content')
    <div class="col-sm-12">
        <div class="home-tab">
            <div class="tab-content tab-content-basic">
                <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview">
                    <div class="row">
                        <div class="col-lg-8 d-flex flex-column">
                            <div class="row flex-grow">
                                <div class="col-12 grid-margin stretch-card">
                                    <div class="card card-rounded">
                                        <div class="card-body">
                                            <div class="d-sm-flex justify-content-between align-items-start">
                                                <div>
                                                    <h4 class="card-title card-title-dash">Касса на сегодня </h4>
                                                    <p class="card-subtitle card-subtitle-dash">Lorem ipsum dolor sit amet
                                                        consectetur
                                                        adipisicing elit</p>
                                                </div>
                                                <div>
                                                    <div class="dropdown">
                                                        <button
                                                            class="btn btn-light dropdown-toggle toggle-dark btn-lg mb-0 me-0"
                                                            type="button" id="dropdownMenuButton2"
                                                            data-bs-toggle="dropdown" aria-haspopup="true"
                                                            aria-expanded="false"> This month </button>
                                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton2">
                                                            <h6 class="dropdown-header">Settings</h6>
                                                            <a class="dropdown-item" href="#">Action</a>
                                                            <a class="dropdown-item" href="#">Another action</a>
                                                            <a class="dropdown-item" href="#">Something else
                                                                here</a>
                                                            <div class="dropdown-divider"></div>
                                                            <a class="dropdown-item" href="#">Separated link</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-sm-flex align-items-center mt-1 justify-content-between">
                                                <div class="d-sm-flex align-items-center mt-4 justify-content-between">
                                                    <h2 class="me-2 fw-bold">$36,2531.00</h2>
                                                    <h4 class="me-2">USD</h4>
                                                    <h4 class="text-success">(+1.37%)</h4>
                                                </div>
                                                <div class="me-3">
                                                    <div id="marketingOverview-legend"></div>
                                                </div>
                                            </div>
                                            <div class="chartjs-bar-wrapper mt-3">
                                                <canvas id="marketingOverview"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row flex-grow">
                                <div class="col-12 grid-margin stretch-card">
                                    <div class="card card-rounded">
                                        <div class="card-body">
                                            <div class="d-sm-flex justify-content-between align-items-start">
                                                <div>
                                                    <h4 class="card-title card-title-dash">Undone Loans week</h4>
                                                    <p class="card-subtitle card-subtitle-dash">You have 50+ new requests
                                                    </p>
                                                </div>
                                                <div>
                                                    <button class="btn btn-primary btn-lg text-white mb-0 me-0"
                                                        type="button"><i class="mdi mdi-eye"></i>view all</button>
                                                </div>
                                            </div>
                                            <div class="table-responsive  mt-1">
                                                <table class="table select-table">
                                                    <thead>
                                                        <tr>
                                                            <th>Name</th>
                                                            <th>phone</th>
                                                            <th>Loan by</th>
                                                            <th>loan amount</th>
                                                            <th>due to</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse ($activities as $a)
                                                            <tr>
                                                                <td>
                                                                    {{ $a->client_name ?? ($a->supplier?->brand?->name ?? 'N/A') }}
                                                                </td>
                                                                <td>
                                                                    {{ $a->client_phone ?? ($a->supplier?->brand?->phone ?? 'N/A') }}
                                                                </td>
                                                                <td>
                                                                    {{ $a->loan_direction ?? 'no loan officer' }}
                                                                </td>
                                                                <td>
                                                                    {{ $a->loan_amount ?? 'no amount' }}
                                                                </td>
                                                                <td>
                                                                    {{ $a->loan_due_to ?? 'no due date' }}
                                                                </td>
                                                                <td>
                                                                    @php
                                                                        $color =
                                                                            $a->status === 'complete'
                                                                                ? 'success'
                                                                                : ($a->status === 'incomplete'
                                                                                    ? 'warning'
                                                                                    : 'danger');

                                                                        $statusRu = match ($a->status) {
                                                                            'complete' => 'Завершен',
                                                                            'incomplete' => 'Не завершен',
                                                                            default => $a->status,
                                                                        };
                                                                    @endphp

                                                                    @if ($a->status === 'incomplete')
                                                                        <form method="POST"
                                                                            action="{{ route('history.updateStatus', $a->id) }}"
                                                                            class="d-inline">
                                                                            @csrf
                                                                            @method('PATCH')
                                                                            <select name="status"
                                                                                onchange="this.form.submit()"
                                                                                class="badge badge-warning">
                                                                                <option value="incomplete" selected>Не
                                                                                    завершен</option>
                                                                                <option value="complete">Завершен</option>
                                                                            </select>
                                                                        </form>
                                                                    @else
                                                                        <span class="badge badge-{{ $color }}">
                                                                            {{ $statusRu }}
                                                                        </span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="4" class="text-center">No activities found
                                                                </td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row flex-grow">
                                <div class="col-md-6 col-lg-6 grid-margin stretch-card">
                                    <div class="card card-rounded">
                                        <div class="card-body card-rounded">
                                            <h4 class="card-title  card-title-dash">Recent Events</h4>
                                            <div class="list align-items-center border-bottom py-2">
                                                <div class="wrapper w-100">
                                                    <p class="mb-2 font-weight-medium">
                                                        Change in Directors
                                                    </p>
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div class="d-flex align-items-center">
                                                            <i class="mdi mdi-calendar text-muted me-1"></i>
                                                            <p class="mb-0 text-small text-muted">Mar 14, 2019</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="list align-items-center border-bottom py-2">
                                                <div class="wrapper w-100">
                                                    <p class="mb-2 font-weight-medium">
                                                        Other Events
                                                    </p>
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div class="d-flex align-items-center">
                                                            <i class="mdi mdi-calendar text-muted me-1"></i>
                                                            <p class="mb-0 text-small text-muted">Mar 14, 2019</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="list align-items-center border-bottom py-2">
                                                <div class="wrapper w-100">
                                                    <p class="mb-2 font-weight-medium">
                                                        Quarterly Report
                                                    </p>
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div class="d-flex align-items-center">
                                                            <i class="mdi mdi-calendar text-muted me-1"></i>
                                                            <p class="mb-0 text-small text-muted">Mar 14, 2019</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="list align-items-center border-bottom py-2">
                                                <div class="wrapper w-100">
                                                    <p class="mb-2 font-weight-medium">
                                                        Change in Directors
                                                    </p>
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div class="d-flex align-items-center">
                                                            <i class="mdi mdi-calendar text-muted me-1"></i>
                                                            <p class="mb-0 text-small text-muted">Mar 14, 2019</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="list align-items-center pt-3">
                                                <div class="wrapper w-100">
                                                    <p class="mb-0">
                                                        <a href="#" class="fw-bold text-primary">Show all <i
                                                                class="mdi mdi-arrow-right ms-2"></i></a>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6 grid-margin stretch-card">
                                    <div class="card card-rounded">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <h4 class="card-title card-title-dash">Activities</h4>
                                                <p class="mb-0">20 finished, 5 remaining</p>
                                            </div>
                                            <ul class="bullet-line-list">
                                                <li>
                                                    <div class="d-flex justify-content-between">
                                                        <div><span class="text-light-green">Ben Tossell</span> assign you a
                                                            task</div>
                                                        <p>Just now</p>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="d-flex justify-content-between">
                                                        <div><span class="text-light-green">Oliver Noah</span> assign you a
                                                            task</div>
                                                        <p>1h</p>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="d-flex justify-content-between">
                                                        <div><span class="text-light-green">Jack William</span> assign you
                                                            a task</div>
                                                        <p>1h</p>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="d-flex justify-content-between">
                                                        <div><span class="text-light-green">Leo Lucas</span> assign you a
                                                            task</div>
                                                        <p>1h</p>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="d-flex justify-content-between">
                                                        <div><span class="text-light-green">Thomas Henry</span> assign you
                                                            a task</div>
                                                        <p>1h</p>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="d-flex justify-content-between">
                                                        <div><span class="text-light-green">Ben Tossell</span> assign you a
                                                            task</div>
                                                        <p>1h</p>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="d-flex justify-content-between">
                                                        <div><span class="text-light-green">Ben Tossell</span> assign you a
                                                            task</div>
                                                        <p>1h</p>
                                                    </div>
                                                </li>
                                            </ul>
                                            <div class="list align-items-center pt-3">
                                                <div class="wrapper w-100">
                                                    <p class="mb-0">
                                                        <a href="#" class="fw-bold text-primary">Show all <i
                                                                class="mdi mdi-arrow-right ms-2"></i></a>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 d-flex flex-column">
                            <div class="row flex-grow">
                                <div class="col-12 grid-margin stretch-card">
                                    <div class="card card-rounded">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <h4 class="card-title card-title-dash">Type By Amount</h4>
                                                    </div>
                                                    <div>
                                                        <canvas class="my-auto" id="doughnutChart"></canvas>
                                                    </div>
                                                    <div id="doughnutChart-legend" class="mt-5 text-center"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row flex-grow">
                                <div class="col-12 grid-margin stretch-card">
                                    <div class="card card-rounded">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <div>
                                                            <h4 class="card-title card-title-dash">Leave Report</h4>
                                                        </div>
                                                        <div>
                                                            <div class="dropdown">
                                                                <button
                                                                    class="btn btn-light dropdown-toggle toggle-dark btn-lg mb-0 me-0"
                                                                    type="button" id="dropdownMenuButton3"
                                                                    data-bs-toggle="dropdown" aria-haspopup="true"
                                                                    aria-expanded="false"> Month Wise </button>
                                                                <div class="dropdown-menu"
                                                                    aria-labelledby="dropdownMenuButton3">
                                                                    <h6 class="dropdown-header">week Wise</h6>
                                                                    <a class="dropdown-item" href="#">Year Wise</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mt-3">
                                                        <canvas id="leaveReport"></canvas>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row flex-grow">
                                <div class="col-12 grid-margin stretch-card">
                                    <div class="card card-rounded">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <div>
                                                            <h4 class="card-title card-title-dash">Top Performer</h4>
                                                        </div>
                                                    </div>
                                                    <div class="mt-3">
                                                        <div
                                                            class="wrapper d-flex align-items-center justify-content-between py-2 border-bottom">
                                                            <div class="d-flex">
                                                                <img class="img-sm rounded-10"
                                                                    src="assets/images/faces/face1.jpg" alt="profile">
                                                                <div class="wrapper ms-3">
                                                                    <p class="ms-1 mb-1 fw-bold">Brandon Washington</p>
                                                                    <small class="text-muted mb-0">162543</small>
                                                                </div>
                                                            </div>
                                                            <div class="text-muted text-small">
                                                                1h ago
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="wrapper d-flex align-items-center justify-content-between py-2 border-bottom">
                                                            <div class="d-flex">
                                                                <img class="img-sm rounded-10"
                                                                    src="assets/images/faces/face2.jpg" alt="profile">
                                                                <div class="wrapper ms-3">
                                                                    <p class="ms-1 mb-1 fw-bold">Wayne Murphy</p>
                                                                    <small class="text-muted mb-0">162543</small>
                                                                </div>
                                                            </div>
                                                            <div class="text-muted text-small">
                                                                1h ago
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="wrapper d-flex align-items-center justify-content-between py-2 border-bottom">
                                                            <div class="d-flex">
                                                                <img class="img-sm rounded-10"
                                                                    src="assets/images/faces/face3.jpg" alt="profile">
                                                                <div class="wrapper ms-3">
                                                                    <p class="ms-1 mb-1 fw-bold">Katherine Butler</p>
                                                                    <small class="text-muted mb-0">162543</small>
                                                                </div>
                                                            </div>
                                                            <div class="text-muted text-small">
                                                                1h ago
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="wrapper d-flex align-items-center justify-content-between py-2 border-bottom">
                                                            <div class="d-flex">
                                                                <img class="img-sm rounded-10"
                                                                    src="assets/images/faces/face4.jpg" alt="profile">
                                                                <div class="wrapper ms-3">
                                                                    <p class="ms-1 mb-1 fw-bold">Matthew Bailey</p>
                                                                    <small class="text-muted mb-0">162543</small>
                                                                </div>
                                                            </div>
                                                            <div class="text-muted text-small">
                                                                1h ago
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="wrapper d-flex align-items-center justify-content-between pt-2">
                                                            <div class="d-flex">
                                                                <img class="img-sm rounded-10"
                                                                    src="assets/images/faces/face5.jpg" alt="profile">
                                                                <div class="wrapper ms-3">
                                                                    <p class="ms-1 mb-1 fw-bold">Rafell John</p>
                                                                    <small class="text-muted mb-0">Alaska, USA</small>
                                                                </div>
                                                            </div>
                                                            <div class="text-muted text-small">
                                                                1h ago
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
