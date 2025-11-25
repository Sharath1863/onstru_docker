<li class="mb-3">
    <div class="body-head d-block mb-3">
        <h5>
            <i class="fas fa-filter pe-2"></i> Filter
        </h5>
    </div>
</li>
<hr>
<li class="mb-3">
    <input type="text" name="keyword" id="keywordSearch" class="form-control" placeholder="Search"
        value="{{ request('keyword') }}">
</li>
<!-- <hr> -->
<li class="mb-3">
    <button class="filterbtn mx-auto collapsed" data-bs-toggle="collapse" data-bs-target="#collapse"
        aria-expanded="false">
        <div class="btnname">
            <span>Location</span>
        </div>
        <div class="righticon d-flex ms-auto">
            <i class="fa-solid fa-angle-right toggle-icon"></i>
        </div>
    </button>
    <div class="collapse" id="collapse">
        <ul class="btn-toggle-nav list-unstyled text-start ps-3 pe-0 py-2">
            @foreach ($locations as $loc)
                <li>
                    <input type="checkbox" class="filter-checkbox location-filter" name="loc"
                        id="{{ $loc }}" value="{{ $loc->id }}">
                    <label for="{{ $loc }}">{{ $loc->value }}</label>
                </li>
            @endforeach
        </ul>
    </div>
</li>
<hr>
<li class="mb-3">
    <button class="filterbtn mx-auto collapsed" data-bs-toggle="collapse" data-bs-target="#collapse5"
        aria-expanded="false">
        <div class="btnname">
            <span>Sub Location</span>
        </div>
        <div class="righticon d-flex ms-auto">
            <i class="fa-solid fa-angle-right toggle-icon"></i>
        </div>
    </button>
    <div class="collapse" id="collapse5">
        <ul class="btn-toggle-nav list-unstyled text-start ps-3 pe-0 py-2" id="sub_loc">
            {{-- @foreach ($sublocation as $sub) --}}
            {{-- <li>
                    <input type="checkbox" class="filter-checkbox sub-location-filter" id="{{ $sub->sublocality }}"
                        value="{{ $sub->sublocality }}">
                    <label for="{{ $sub->sublocality }}">{{ $sub->sublocality }}</label>
                </li> --}}
            {{-- @endforeach --}}
        </ul>
    </div>
</li>
<hr>
<li class="mb-3">
    <button class="filterbtn mx-auto collapsed" data-bs-toggle="collapse" data-bs-target="#collapse0"
        aria-expanded="false">
        <div class="btnname">
            <span>Category</span>
        </div>
        <div class="righticon d-flex ms-auto">
            <i class="fa-solid fa-angle-right toggle-icon"></i>
        </div>
    </button>
    <div class="collapse" id="collapse0">
        <ul class="btn-toggle-nav list-unstyled text-start ps-3 pe-0 py-2">
            @foreach ($category as $cat)
                <li>
                    <input type="checkbox" class="filter-checkbox category-filter" id="{{ $cat }}"
                        value="{{ $cat->value }}">
                    <label for="{{ $cat }}">{{ $cat->value }}</label>
                </li>
            @endforeach
        </ul>
    </div>
</li>
<hr>
<li class="mb-3">
    <button class="filterbtn mx-auto collapsed" data-bs-toggle="collapse" data-bs-target="#collapse1"
        aria-expanded="false">
        <div class="btnname">
            <span>Job Type</span>
        </div>
        <div class="righticon d-flex ms-auto">
            <i class="fa-solid fa-angle-right toggle-icon"></i>
        </div>
    </button>
    <div class="collapse" id="collapse1">
        <ul class="btn-toggle-nav list-unstyled text-start ps-3 pe-0 py-2">
            <li>
                <input type="checkbox" class="filter-checkbox type-filter" id="Intern" value="Intern">
                <label for="Intern">Intern</label>
            </li>
            <li>
                <input type="checkbox" class="filter-checkbox type-filter" id="Full-Time" value="Full-Time">
                <label for="Full-Time">Full-Time</label>
            </li>
            <li>
                <input type="checkbox" class="filter-checkbox type-filter" id="Part-Time" value="Part-Time">
                <label for="Part-Time">Part-Time</label>
            </li>
            <li>
                <input type="checkbox" class="filter-checkbox type-filter" id="Freelancer" value="Freelancer">
                <label for="Freelancer">Freelancer</label>
            </li>
            <li>
                <input type="checkbox" class="filter-checkbox type-filter" id="Contract" value="Contract">
                <label for="Contract">Contract</label>
            </li>
        </ul>
    </div>
</li>
<hr>
<li class="mb-3">
    <button class="filterbtn mx-auto collapsed" data-bs-toggle="collapse" data-bs-target="#collapse2"
        aria-expanded="false">
        <div class="btnname">
            <span>Experience</span>
        </div>
        <div class="righticon d-flex ms-auto">
            <i class="fa-solid fa-angle-right toggle-icon"></i>
        </div>
    </button>
    <div class="collapse" id="collapse2">
        <ul class="btn-toggle-nav list-unstyled text-start ps-3 pe-0 py-2">
            <li>
                <input type="checkbox" class="filter-checkbox exp-filter" id="Fresher" value="0">
                <label for="Fresher">Fresher</label>
            </li>
            <li>
                <input type="checkbox" class="filter-checkbox exp-filter" id="1" value="1">
                <label for="1">1 year</label>
            </li>
            <li>
                <input type="checkbox" class="filter-checkbox exp-filter" id="2" value="2">
                <label for="2">2 years</label>
            </li>
            <li>
                <input type="checkbox" class="filter-checkbox exp-filter" id="3" value="3">
                <label for="3">3 years</label>
            </li>
            <li>
                <input type="checkbox" class="filter-checkbox exp-filter" id="4" value="4">
                <label for="4">4 years</label>
            </li>
            <li>
                <input type="checkbox" class="filter-checkbox exp-filter" id="5" value="5">
                <label for="5">More than 5 years</label>
            </li>
        </ul>
    </div>
</li>
<hr>
<li class="mb-3">
    <button class="filterbtn mx-auto collapsed" data-bs-toggle="collapse" data-bs-target="#collapse3"
        aria-expanded="false">
        <div class="btnname">
            <span>Salary Range / Month</span>
        </div>
        <div class="righticon d-flex ms-auto">
            <i class="fa-solid fa-angle-right toggle-icon"></i>
        </div>
    </button>
    <div class="collapse" id="collapse3">
        <ul class="btn-toggle-nav list-unstyled text-start ps-3 pe-0 py-2">
            <!-- <li>
                <input type="checkbox" class="filter-checkbox salary-filter" id="under-10000" value="under-10000">
                <label for="under-10000">Under 10000 Rs</label>
            </li>
            <li>
                <input type="checkbox" class="filter-checkbox salary-filter" id="10000-25000" value="10000-25000">
                <label for="10000-25000">10000 to 25000</label>
            </li>
            <li>
                <input type="checkbox" class="filter-checkbox salary-filter" id="25000-35000" value="25000-35000">
                <label for="25000-35000">25000 to 35000</label>
            </li>
            <li>
                <input type="checkbox" class="filter-checkbox salary-filter" id="35000-50000" value="35000-50000">
                <label for="35000-50000">35000 to 50000</label>
            </li>
            <li>
                <input type="checkbox" class="filter-checkbox salary-filter" id="50000-75000" value="50000-75000">
                <label for="50000-75000">50000-75000</label>
            </li> -->
            <li class="d-flex align-items-center flex-wrap gap-2">
                <input type="number" id="minSalary" class="form-control" style="width: 40%;" placeholder="From">
                <input type="number" id="maxSalary" class="form-control" style="width: 40%;" placeholder="To">
            </li>
        </ul>
    </div>
</li>
<hr>
<li class="mb-3">
    <button class="filterbtn mx-auto collapsed" data-bs-toggle="collapse" data-bs-target="#collapse4"
        aria-expanded="false">
        <div class="btnname">
            <span>Boosted</span>
        </div>
        <div class="righticon d-flex ms-auto">
            <i class="fa-solid fa-angle-right toggle-icon"></i>
        </div>
    </button>
    <div class="collapse" id="collapse4">
        <ul class="btn-toggle-nav list-unstyled text-start ps-3 pe-0 py-2">
            @foreach ([1 => 'Boosted', 0 => 'Not Boosted'] as $key => $label)
                <li>
                    <input type="checkbox" class="filter-checkbox highlight-filter"
                        id="highlight_{{ $key }}" value="{{ $key }}">
                    <label for="highlight_{{ $key }}">{{ $label }}</label>
                </li>
            @endforeach
        </ul>
    </div>
</li>
<hr>
