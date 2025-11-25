<div class="body-head mb-3">
    <h5>Projects</h5>
    <div class="d-flex gap-2 mb-3">
        <button class="listbtn status-filter active" data-status="all">All</button>
        <button class="removebtn status-filter" data-status="ongoing">Ongoing</button>
        <button class="followingbtn status-filter" data-status="completed">Completed</button>
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
        <img src="{{ asset('assets/images/Empty/NoProjects.png') }}" height="200px" class="d-flex mx-auto mb-2" alt="">
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
                <img src="{{ $project->image ? asset('https://onstru-social.s3.ap-south-1.amazonaws.com/' . $project->image) : asset('assets/images/NoImage.png') }}"
                    class="mb-3 w-100 rounded-3 position-relative object-fit-cover object-center" height="175px" alt="">
                @if ($project->end_date > now())
                    <a class="badge text-dark">Ongoing</a>
                @else
                    <a class="badge text-dark">Completed</a>
                @endif
                <div class="cards-head">
                    <h5 class="mb-2">{{ $project->title }}</h5>
                    <h6 class="mb-2 long-text">{{ Str::limit($project->description, 100) }}</h6>
                </div>
                <div class="cards-content">
                    <h6 class="mb-2"><i class="fas fa-location-dot pe-1"></i> {{ $project->locationDetails->value ?? '-' }}
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
            <a>
                <button class="removebtn w-100" data-bs-toggle="modal" data-bs-target="#viewProject{{ $project->id }}">View
                    Project</button>
            </a>
        </div>

        <!-- View Project Modal -->
        <div class="modal fade" id="viewProject{{ $project->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="viewProjectLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="m-0">View Project</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body pt-2">
                        @php
                            // Decode image JSON — make sure it's a string first
                            $images = is_string($project->sub_image)
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
                            <!-- Fallback if no images -->
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<!-- Search Filter -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
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
            btn.addEventListener('click', function () {
                statusButtons.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                currentStatus = this.getAttribute('data-status');
                filterProjects();
            });
        });
    });
</script>