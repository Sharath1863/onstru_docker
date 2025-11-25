<div class="body-head mb-3">
    <h5>Projects</h5>
    <div class="d-flex align-items-center gap-2 mb-3">
        <button class="listbtn status-filter active" data-status="all">All</button>
        <button class="removebtn status-filter" data-status="ongoing">Ongoing</button>
        <button class="followingbtn status-filter" data-status="completed">Completed</button>
        @if (count($projects) > 0)
            <a data-bs-toggle="modal" data-bs-target="#addProject">
                <button class="listbtn">+ Add Project</button>
            </a>
        @else
            <a data-bs-toggle="modal" data-bs-target="#projectBadges">
                <button class="listbtn">+ Add Project</button>
            </a>
        @endif
        <a data-bs-toggle="modal" data-bs-target="#projectBadges">
            <button class="iconbtn"><i class="fas fa-info-circle" data-bs-toggle="tooltip"
                    data-bs-title="About"></i></button>
        </a>
    </div>
</div>

<!-- Search -->
<div class="form-div">
    <div class="inpleftflex mb-3">
        <i class="fas fa-search"></i>
        <input type="text" name="keyword" id="projectSearch" class="form-control border-0" placeholder="Search"
            value="{{ request('projectsKeyword') }}">
    </div>
</div>

<!-- Empty State -->
<div class="side-cards shadow-none border-0" id="noProjects"
    style="{{ count($projects) > 0 ? 'display: none;' : 'display: block;' }}">
    <div class="cards-content d-flex align-items-center justify-content-center flex-column gap-2">
        <img src="{{ asset('assets/images/Empty/NoProjects.png') }}" height="200px" class="d-flex mx-auto mb-2"
            alt="">
        <h5 class="text-center mb-0">No Projects Found</h5>
        <h6 class="text-center bio">No projects are available at the moment - try updating your filters or check back later
            for new opportunities.</h6>
    </div>
</div>

<!-- Project Cards -->
<div class="product-cards">
    @foreach ($projects as $project)
        <div class="side-cards project-card filter-projects position-relative"
            data-status="{{ $project->end_date > now() ? 'ongoing' : 'completed' }}">
            <div>
                <img src="{{ asset($project->image ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $project->image : 'assets/images/NoImage.png') }}"
                    class="mb-3 w-100 rounded-3 position-relative object-fit-cover object-center" height="175px"
                    alt="">
                {{-- @dd($project->end_date ,now()) --}}
                @if (\Carbon\Carbon::parse($project->start_date)->isFuture())
                    <a class="badge text-dark">Upcoming</a>
                @elseif (\Carbon\Carbon::parse($project->end_date)->isToday() || \Carbon\Carbon::parse($project->end_date)->isFuture())
                    <a class="badge text-dark">Ongoing</a>
                @else
                    <a class="badge text-dark">Completed</a>
                @endif
                <div class="cards-head">
                    <h5 class="mb-2 long-text">{{ $project->title }}</h5>
                    <h6 class="mb-2 long-text">{{ Str::limit($project->description, 100) }}</h6>
                </div>
                <div class="cards-content">
                    <h6 class="mb-2">
                        <i class="fas fa-location-dot pe-1"></i> {{ $project->locationDetails->value ?? '-' }}
                    </h6>
                    <h6 class="mb-2">
                        ₹ {{ $project->prjt_budget ?? '-' }}
                    </h6>
                    <div class="d-flex align-items-center justify-content-between flex-wrap">
                        <h6 class="mb-2">
                            <i class="far fa-calendar pe-1"></i>
                            {{ $project->start_date->format('d-m-Y') }}
                        </h6>
                        <h6 class="mb-2">
                            <i class="fas fa-flag text-danger pe-1"></i>
                            {{ $project->end_date->format('d-m-Y') }}
                        </h6>
                    </div>
                </div>
            </div>
            <div class="row align-items-center justify-content-between">
                <div class="col-9">
                    <button class="removebtn w-100" data-bs-toggle="modal"
                        data-bs-target="#editProject{{ $project->id }}">Edit</button>
                </div>
                <div class="col-3 ps-0">
                    <button class="iconbtn w-100" data-bs-toggle="modal"
                        data-bs-target="#viewProject{{ $project->id }}">
                        <i class="fas fa-external-link" data-bs-toggle="tooltip" data-bs-title="View"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- View Project Modal -->
        <div class="modal fade" id="viewProject{{ $project->id }}" data-bs-backdrop="static" data-bs-keyboard="false"
            tabindex="-1" aria-labelledby="viewProjectLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="m-0">View Project</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body pt-2">
                        @php
                            // Decode image JSON — make sure it's a string first
                            $images = is_string(value: $project->sub_image)
                                ? json_decode($project->sub_image, true)
                                : $project->sub_image;
                            $carouselId = 'mediaCarousel_' . $loop->index;
                        @endphp

                        @if (!empty($images) && is_array($images))
                            <div id="{{ $carouselId }}" class="carousel slide side-cards mb-4"
                                data-bs-ride="carousel">
                                <!-- Indicators -->
                                <div class="carousel-indicators">
                                    @foreach (array_values($images) as $index => $img)
                                        <button type="button" data-bs-target="#{{ $carouselId }}"
                                            data-bs-slide-to="{{ $index }}"
                                            class="{{ $index === 0 ? 'active' : '' }}"
                                            aria-current="{{ $index === 0 ? 'true' : 'false' }}"
                                            aria-label="Slide {{ $index + 1 }}">
                                        </button>
                                    @endforeach
                                </div>

                                <!-- Carousel Images -->
                                <div class="carousel-inner rounded-3 img-div">
                                    @foreach (array_values($images) as $index => $img)
                                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                            <img src="https://onstru-social.s3.ap-south-1.amazonaws.com/{{ $img }}"
                                                class="d-block w-100 object-fit-cover object-top rounded-3"
                                                height="250" alt="Image {{ $index + 1 }}">
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Controls -->
                                <button class="carousel-control-prev" type="button"
                                    data-bs-target="#{{ $carouselId }}" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Previous</span>
                                </button>
                                <button class="carousel-control-next" type="button"
                                    data-bs-target="#{{ $carouselId }}" data-bs-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Next</span>
                                </button>
                            </div>
                        @else
                            <img src="https://onstru-social.s3.ap-south-1.amazonaws.com/{{ $project->image }}"
                                class="d-block w-100 object-fit-cover object-top rounded-3" height="250"
                                alt="No Image">
                        @endif
                        <div class="modal-grid-card my-3">
                            <div class="col-sm-12 mb-2">
                                <label>Project Title</label>
                                <h6>{{ $project->title }}</h6>
                            </div>
                            <div class="col-sm-12 mb-2">
                                <label>Project Location</label>
                                <h6>{{ $project->locationDetails->value }}</h6>
                            </div>
                            <div class="col-sm-12 mb-2">
                                <label>Start Date</label>
                                <h6>{{ \Carbon\Carbon::parse($project->start_date)->format('d-m-Y') }}</h6>
                            </div>
                            <div class="col-sm-12 mb-2">
                                <label>End Date</label>
                                <h6>{{ \Carbon\Carbon::parse($project->end_date)->format('d-m-Y') }}</h6>
                            </div>
                            <div class="col-sm-12 mb-2">
                                <label>Description</label>
                                <h6>{{ $project->description }}</h6>
                            </div>
                            <div class="col-sm-12 mb-2">
                                <label>Key Outcomes</label>
                                <h6>{{ $project->key_outcomes }}</h6>
                            </div>
                            <div class="col-sm-12 mb-2">
                                <label>Project Budget</label>
                                <h6>₹ {{ $project->prjt_budget }}</h6>
                            </div>
                            <div class="col-sm-12 mb-2">
                                <label>Job Role</label>
                                <h6>{{ $project->job_role }}</h6>
                            </div>
                            <div class="col-sm-12 mb-2">
                                <label>Responsibilities</label>
                                <h6>{{ $project->responsibilities }}</h6>
                            </div>
                            @if (!$project->amount == 0 || !$project->amount == null)
                                <div class="col-sm-12">
                                    <label>Print Invoice</label>
                                    <a href="{{ url('project-list-bill', ['id' => $project->id]) }}" target="_blank"
                                        class="text-muted">
                                        <i class="fas fa-print" data-bs-toggle="tooltip"
                                            data-bs-title="Print Invoice"></i>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Project Modal -->
        <div class="modal fade" id="editProject{{ $project->id }}" data-bs-backdrop="static"
            data-bs-keyboard="false" tabindex="-1" aria-labelledby="editProjectLabel" aria-hidden="true">
            <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="m-0">Edit Project</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('projects.update', $project->id) }}" method="POST"
                            enctype="multipart/form-data" class="editProjectForm">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-sm-12 col-md-6 mb-2">
                                    <label for="title{{ $project->id }}">Project Title <span>*</span></label>
                                    <input type="text" class="form-control" name="title"
                                        id="title{{ $project->id }}" value="{{ $project->title }}" required>
                                </div>
                                <div class="col-sm-12 col-md-6 mb-2">
                                    <label for="location{{ $project->id }}">Project Location <span>*</span></label>
                                    <select class="form-select editProjectLoc" name="location" required>
                                        <option value="">Select Location</option>
                                        @foreach ($locations as $id => $location)
                                            <option value="{{ $id }}"
                                                {{ $id == $project->location ? 'selected' : '' }}>
                                                {{ $location }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-12 col-md-6 mb-2">
                                    <label for="start_date{{ $project->id }}">Start Date <span>*</span></label>
                                    <input type="date" class="form-control" name="start_date"
                                        id="start_date{{ $project->id }}"
                                        value="{{ $project->start_date->format('Y-m-d') }}" required>
                                </div>
                                <div class="col-sm-12 col-md-6 mb-2">
                                    <label for="end_date{{ $project->id }}">End Date <span>*</span></label>
                                    <input type="date" class="form-control" name="end_date"
                                        id="end_date{{ $project->id }}"
                                        value="{{ $project->end_date->format('Y-m-d') }}" required>
                                </div>
                                <div class="col-sm-12 col-md-6 mb-2">
                                    <label for="description{{ $project->id }}">Description <span>*</span></label>
                                    <textarea rows="2" class="form-control" name="description" id="description{{ $project->id }}" required>{{ $project->description }}</textarea>
                                </div>
                                <div class="col-sm-12 col-md-6 mb-2">
                                    <label for="key_outcomes{{ $project->id }}">Key Outcomes <span>*</span></label>
                                    <textarea rows="2" class="form-control" name="key_outcomes" id="key_outcomes{{ $project->id }}" required>{{ $project->key_outcomes }}</textarea>
                                </div>
                                <div class="col-sm-12 col-md-6 mb-2">
                                    <label for="edit_prjt_budget{{ $project->id }}">Project Budget
                                        <span>*</span></label>
                                    <input type="number" class="form-control" name="prjt_budget"
                                        id="edit_prjt_budget{{ $project->id }}" min="0"
                                        value="{{ $project->prjt_budget }}" required>
                                </div>
                                <div class="col-sm-12 col-md-6 mb-2">
                                    <label for="job_role{{ $project->id }}">Job Role <span>*</span></label>
                                    <input type="text" class="form-control" name="job_role"
                                        id="job_role{{ $project->id }}" value="{{ $project->job_role }}" required>
                                </div>
                                <div class="col-sm-12 col-md-12 mb-2">
                                    <label for="responsibilities{{ $project->id }}">Responsibilities
                                        <span>*</span></label>
                                    <textarea rows="2" class="form-control" name="responsibilities" id="responsibilities{{ $project->id }}"
                                        required>{{ $project->responsibilities }}</textarea>
                                </div>
                                @php
                                    $images = $project->sub_image ? json_decode($project->sub_image, true) : [];
                                    $s3BaseUrl = 'https://onstru-social.s3.ap-south-1.amazonaws.com/';
                                @endphp
                                <div class="col-sm-12 col-md-12 mb-3">
                                    <label for="images">Upload Project Images <span>*</span></label>
                                    <div class="col-sm-12 col-md-12" id="imgContainer">
                                        <div class="mb-2">
                                            <label class="custom-file-upload w-100"
                                                for="edit-image-1-{{ $project->id }}">
                                                <div class="icon mb-2">
                                                    <img src="{{ asset('assets/images/Upload_Dark.png') }}"
                                                        height="25px" alt="">
                                                </div>
                                                <input type="file" id="edit-image-1-{{ $project->id }}"
                                                    name="image-1" accept="image/*"
                                                    onchange="previewImage(this, 'edit-preview-1-{{ $project->id }}')">
                                            </label>
                                            <img src="{{ isset($images[0]) ? $s3BaseUrl . $images[0] : '' }}"
                                                class="rounded-2" width="100%" height="75px"
                                                id="edit-preview-1-{{ $project->id }}"
                                                style="{{ isset($images[0]) ? '' : 'display: none;' }}"
                                                alt="">
                                        </div>
                                        <div class="mb-2">
                                            <label class="custom-file-upload w-100"
                                                for="edit-image-2-{{ $project->id }}">
                                                <div class="icon mb-2">
                                                    <img src="{{ asset('assets/images/Upload_Dark.png') }}"
                                                        height="25px" alt="">
                                                </div>
                                                <input type="file" id="edit-image-2-{{ $project->id }}"
                                                    name="image-2" accept="image/*"
                                                    onchange="previewImage(this, 'edit-preview-2-{{ $project->id }}')">
                                            </label>
                                            <img src="{{ isset($images[1]) ? $s3BaseUrl . $images[1] : '' }}"
                                                class="rounded-2" width="100%" height="75px"
                                                id="edit-preview-2-{{ $project->id }}"
                                                style="{{ isset($images[1]) ? '' : 'display: none;' }}"
                                                alt="">
                                        </div>
                                        <div class="mb-2">
                                            <label class="custom-file-upload w-100"
                                                for="edit-image-3-{{ $project->id }}">
                                                <div class="icon mb-2">
                                                    <img src="{{ asset('assets/images/Upload_Dark.png') }}"
                                                        height="25px" alt="">
                                                </div>
                                                <input type="file" id="edit-image-3-{{ $project->id }}"
                                                    name="image-3" accept="image/*"
                                                    onchange="previewImage(this, 'edit-preview-3-{{ $project->id }}')">
                                            </label>
                                            <img src="{{ isset($images[2]) ? $s3BaseUrl . $images[2] : '' }}"
                                                class="rounded-2" width="100%" height="75px"
                                                id="edit-preview-3-{{ $project->id }}"
                                                style="{{ isset($images[2]) ? '' : 'display: none;' }}"
                                                alt="">
                                        </div>
                                        <div class="mb-2">
                                            <label class="custom-file-upload w-100"
                                                for="edit-image-4-{{ $project->id }}">
                                                <div class="icon mb-2">
                                                    <img src="{{ asset('assets/images/Upload_Dark.png') }}"
                                                        height="25px" alt="">
                                                </div>
                                                <input type="file" id="edit-image-4-{{ $project->id }}"
                                                    name="image-4" accept="image/*"
                                                    onchange="previewImage(this, 'edit-preview-4-{{ $project->id }}')">
                                            </label>
                                            <img src="{{ isset($images[3]) ? $s3BaseUrl . $images[3] : '' }}"
                                                class="rounded-2" width="100%" height="75px"
                                                id="edit-preview-4-{{ $project->id }}"
                                                style="{{ isset($images[3]) ? '' : 'display: none;' }}"
                                                alt="">
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-center align-items-center mt-3">
                                    <button type="submit" class="formbtn editProjectbtn">Update Project</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<!-- Add Project Modal -->
<div class="modal fade" id="addProject" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="addProjectLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="m-0">Add Project</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('projects.store') }}" method="POST" enctype="multipart/form-data"
                    id="addProjectForm">
                    @csrf
                    <div class="row mt-2">
                        @if (count($projects) >= 5)
                            <div class="d-flex align-items-center justify-content-between">
                                <label class="my-2">Listing : <span class="text-muted">₹
                                        {{ $project_list_charge ?? '' }} (Included Tax)</span></label>
                                <label class="my-2">Wallet : <span class="text-muted">₹
                                        {{ auth()->user()->balance }}</span></label>
                            </div>
                        @endif
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="addProjectTitle">Project Title <span>*</span></label>
                            <input type="text" class="form-control" name="title" id="addProjectTitle" required>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="addProjectLoc">Project Location <span>*</span></label>
                            <select class="form-select" name="location" id="addProjectLoc" required>
                                <option value="">Select Location</option>
                                @foreach ($locations as $id => $location)
                                    <option value="{{ $id }}">{{ $location }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="addProjectStart">Start Date <span>*</span></label>
                            <input type="date" class="form-control" name="start_date" id="addProjectStart"
                                required>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="addProjectEnd">End Date <span>*</span></label>
                            <input type="date" class="form-control" name="end_date" id="addProjectEnd" required>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="addProjectDescp">Description <span>*</span></label>
                            <textarea rows="2" class="form-control" name="description" id="addProjectDescp" required></textarea>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="addProjectKey">Key Outcomes <span>*</span></label>
                            <textarea rows="2" class="form-control" name="key_outcomes" id="addProjectKey" required></textarea>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="addProjectBudget">Project Budget <span>*</span></label>
                            <input type="number" class="form-control" name="prjt_budget" id="addProjectBudget"
                                min="0" required>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="addJobRole">Job Role <span>*</span></label>
                            <input type="text" class="form-control" name="job_role" id="addJobRole" required>
                        </div>
                        <div class="col-sm-12 col-md-12 mb-2">
                            <label for="addResponsibility">Responsibilities <span>*</span></label>
                            <textarea rows="2" class="form-control" name="responsibilities" id="addResponsibility" required></textarea>
                        </div>
                        <div class="col-sm-12 col-md-12 mb-3">
                            <label for="images">Upload Project Images <span>*</span></label>
                            <div class="col-sm-12 col-md-12" id="imgContainer">
                                <div class="mb-2">
                                    <label class="custom-file-upload w-100" for="image-1">
                                        <div class="icon mb-2">
                                            <img src="{{ asset('assets/images/Upload_Dark.png') }}" height="25px"
                                                alt="">
                                        </div>
                                        <input type="file" id="image-1" name="image-1" accept="image/*"
                                            onchange="previewImage(this, 'preview-img-1')">
                                    </label>
                                    <img src="" class="rounded-2" width="100%" id="preview-img-1"
                                        style="display: none;" alt="">
                                </div>
                                <div class="mb-2">
                                    <label class="custom-file-upload w-100" for="image-2">
                                        <div class="icon mb-2">
                                            <img src="{{ asset('assets/images/Upload_Dark.png') }}" height="25px"
                                                alt="">
                                        </div>
                                        <input type="file" id="image-2" name="image-2" accept="image/*"
                                            onchange="previewImage(this, 'preview-img-2')">
                                    </label>
                                    <img src="" class="rounded-2" width="100%" id="preview-img-2"
                                        style="display: none;" alt="">
                                </div>
                                <div class="mb-2">
                                    <label class="custom-file-upload w-100" for="image-3">
                                        <div class="icon mb-2">
                                            <img src="{{ asset('assets/images/Upload_Dark.png') }}" height="25px"
                                                alt="">
                                        </div>
                                        <input type="file" id="image-3" name="image-3" accept="image/*"
                                            onchange="previewImage(this, 'preview-img-3')">
                                    </label>
                                    <img src="" class="rounded-2" width="100%" id="preview-img-3"
                                        style="display: none;" alt="">
                                </div>
                                <div class="mb-2">
                                    <label class="custom-file-upload w-100" for="image-4">
                                        <div class="icon mb-2">
                                            <img src="{{ asset('assets/images/Upload_Dark.png') }}" height="25px"
                                                alt="">
                                        </div>
                                        <input type="file" id="image-4" name="image-4" accept="image/*"
                                            onchange="previewImage(this, 'preview-img-4')">
                                    </label>
                                    <img src="" class="rounded-2" width="100%" id="preview-img-4"
                                        style="display: none;" alt="">
                                </div>
                            </div>
                        </div>

                        @if (count($projects) >= 5)
                            <div class="col-sm-12 col-md-12 mb-2">
                                <label>Notes <span>*</span></label>
                                <h6>Add up to 5 projects for free! Unlock unlimited projects easily using your wallet balance.</h6>
                            </div>
                            <div class="col-sm-12 col-md-12 mb-2 d-flex align-items-center column-gap-2">
                                <input type="checkbox" id="projectPay" name="projectPay" required>
                                <label class="mb-0" for="projectPay">Agree to Pay</label>
                                <small class="project-balance-message" style="display:none;">Insufficient
                                    Balance</small>
                            </div>
                        @endif

                        <div class="d-flex justify-content-center align-items-center column-gap-2 mt-3">
                            <button type="submit" class="formbtn addProjectbtn">Add Project</button>
                            <a href="{{ url('wallet') }}" target="_blank">
                                <button type="button" class="removebtn" id="project_recharge_button"
                                    style="display: none;">
                                    Recharge
                                </button>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Search Filter -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const projectSearch = document.getElementById('projectSearch');
        const noProjects = document.getElementById('noProjects');
        const projectCards = document.querySelectorAll('.filter-projects');
        const statusButtons = document.querySelectorAll('.status-filter');

        let currentStatus = "all";

        function filterProjects() {
            let projectMatch = false;
            const keyword = projectSearch.value.toLowerCase();
            projectCards.forEach(card => {
                const cardText = card.textContent.toLowerCase();
                const cardStatus = card.getAttribute('data-status');
                const matchesKeyword = cardText.includes(keyword);
                const matchesStatus = (currentStatus === "all" || currentStatus === cardStatus);
                if (matchesKeyword && matchesStatus) {
                    card.style.display = 'block';
                    projectMatch = true;
                } else {
                    card.style.display = 'none';
                }
            });
            noProjects.style.display = projectMatch ? 'none' : 'block';
        }

        projectSearch.addEventListener('input', filterProjects);
        statusButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                statusButtons.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                currentStatus = this.getAttribute('data-status');
                filterProjects();
            });
        });
    });
</script>

<!-- Select 2 -->
<script>
    $(function() {
        function initSelect2(modal) {
            modal.find('.editProjectLoc, #addProjectLoc').each(function() {
                let $select = $(this);
                if ($select.hasClass('select2-hidden-accessible')) return;
                $select.select2({
                    width: "100%",
                    placeholder: "Select Options",
                    allowClear: true,
                    dropdownParent: modal
                });
            });
        }
        $('#addProject').on('shown.bs.modal', function() {
            initSelect2($(this));
        });
        $(document).on('shown.bs.modal', '[id^="editProject"]', function() {
            initSelect2($(this));
        });
    });
</script>

<!-- Wallent Balance -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const projectModal = document.getElementById('addProject');
        if (!projectModal) return;

        const walletBalance = parseFloat("{{ auth()->user()->balance ?? 0 }}") || 0;
        const listingCharge = parseFloat("{{ $project_list_charge ?? 0 }}") || 0;

        const checkbox = projectModal.querySelector('#projectPay');
        const submitBtn = projectModal.querySelector('.formbtn');
        const rechargeBtn = projectModal.querySelector('#project_recharge_button');
        const balanceMsg = projectModal.querySelector('.project-balance-message');

        function show(el) {
            if (el) el.style.display = 'block';
        }

        function hide(el) {
            if (el) el.style.display = 'none';
        }

        function validateProjectPay() {
            hide(balanceMsg);
            hide(rechargeBtn);
            if (submitBtn) submitBtn.disabled = false;

            if (checkbox && checkbox.checked) {
                if (listingCharge > walletBalance) {
                    if (balanceMsg) balanceMsg.textContent = "Insufficient Balance";
                    show(balanceMsg);
                    show(rechargeBtn);
                    if (submitBtn) submitBtn.disabled = true;
                } else {
                    if (balanceMsg) hide(balanceMsg);
                    if (rechargeBtn) hide(rechargeBtn);
                    if (submitBtn) submitBtn.disabled = false;
                }
            } else if (checkbox) {
                if (submitBtn) submitBtn.disabled = true;
            }
        }

        if (checkbox) checkbox.addEventListener('change', validateProjectPay);
        projectModal.addEventListener('shown.bs.modal', validateProjectPay);
    });
</script>

<!-- Prevent Form Submissions -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('addProjectForm');
        const submitBtn = document.querySelector('.addProjectbtn');
        let isSubmitting = false;

        form.addEventListener('submit', function(e) {
            const startDateInput = document.getElementById('addProjectStart');
            const endDateInput = document.getElementById('addProjectEnd');
            const requiredImageInput = document.getElementById('image-1');
            const startDate = new Date(startDateInput.value);
            const endDate = new Date(endDateInput.value);

            if (!requiredImageInput.files.length) {
                e.preventDefault();
                showToast("Project first Image is Mandatory. Please Fill all the fields");
                requiredImageInput.focus(); 
                return;
            }

            // End date must be >= Start date
            if (startDateInput.value && endDateInput.value && endDate < startDate) {
                e.preventDefault();
                showToast("End Date must be after or equal to Start Date.");
                return;
            }

            if (isSubmitting) {
                e.preventDefault();
                return;
            }
            isSubmitting = true;
            submitBtn.disabled = true;
            submitBtn.innerHTML =
                `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Submitting...`;
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('.editProjectForm');
        const submitBtn = document.querySelector('.editProjectbtn');
        let isSubmitting = false;

        form.addEventListener('submit', function(e) {
            if (isSubmitting) {
                e.preventDefault();
                return;
            }
            isSubmitting = true;
            submitBtn.disabled = true;
            submitBtn.innerHTML =
                `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Updating...`;
        });
    });
</script>
