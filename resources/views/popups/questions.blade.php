@php
    $questions = [
        [
            'question' => 'What is your favorite food?',
            'options' => ['Pizza', 'Biryani', 'Pasta', 'Burger'],
        ],
        [
            'question' => 'Which type of movies do you like?',
            'options' => ['Action', 'Comedy', 'Drama', 'Horror'],
        ],
        [
            'question' => 'What is your favorite place to visit?',
            'options' => ['Beach', 'Mountains', 'City', 'Countryside'],
        ],
        [
            'question' => 'What kind of music do you enjoy?',
            'options' => ['Pop', 'Rock', 'Classical', 'Jazz'],
        ],
        [
            'question' => 'What is your favorite sport?',
            'options' => ['Cricket', 'Football', 'Basketball', 'Tennis'],
        ],
        [
            'question' => 'What do you prefer to drink?',
            'options' => ['Tea', 'Coffee', 'Juice', 'Water'],
        ],
        [
            'question' => 'Which season do you like the most?',
            'options' => ['Summer', 'Winter', 'Spring', 'Autumn'],
        ],
        [
            'question' => 'How do you spend your free time?',
            'options' => ['Reading', 'Traveling', 'Gaming', 'Watching Movies'],
        ],
        [
            'question' => 'Which pet do you prefer?',
            'options' => ['Dog', 'Cat', 'Bird', 'Fish'],
        ],
        [
            'question' => 'What is the capital of France?',
            'options' => ['Paris', 'Berlin', 'Madrid', 'Rome'],
        ],
        [
            'question' => 'Which language is used for web development?',
            'options' => ['Python', 'PHP', 'C++', 'Java'],
        ],
        [
            'question' => 'What is 5 + 3?',
            'options' => ['6', '7', '8', '9'],
        ],
        [
            'question' => 'Which planet is known as the Red Planet?',
            'options' => ['Earth', 'Mars', 'Venus', 'Jupiter'],
        ],
        [
            'question' => 'Who wrote "Hamlet"?',
            'options' => ['Charles Dickens', 'William Shakespeare', 'Leo Tolstoy', 'Mark Twain'],
        ],
        [
            'question' => 'What is the boiling point of water?',
            'options' => ['90°C', '100°C', '110°C', '120°C'],
        ],
        [
            'question' => 'Which ocean is the largest?',
            'options' => ['Atlantic', 'Indian', 'Pacific', 'Arctic'],
        ],
        [
            'question' => 'What is the national animal of India?',
            'options' => ['Lion', 'Tiger', 'Elephant', 'Peacock'],
        ],
        [
            'question' => 'Which is the smallest prime number?',
            'options' => ['1', '2', '3', '5'],
        ],
    ];

    // Get 3 random questions
    $randomQuestions = collect($questions)->random(4);
@endphp

<!-- Questions Modal -->
<div class="modal modal-md fade" id="questions" tabindex="-1" aria-labelledby="questionsLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="">
                <div class="modal-header">
                    <h4 class="m-0">For Suggestions</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3 row" id="questionContainer">
                    @foreach ($randomQuestions as $index => $q)
                        <div class="col-sm-12 col-md-12 col-lg-6 mb-4">
                            <label for="q{{ $index }}">{{ $q['question'] }}</label>
                            @foreach ($q['options'] as $opt)
                                <div>
                                    <input type="checkbox" name="q{{ $index }}" value="{{ $opt }}">
                                    <label>{{ $opt }}</label>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                    <div class="col-sm-12 d-flex align-items-center justify-content-center my-2">
                        <a href="">
                            <button class="formbtn">Save</button>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const modal = document.getElementById("qsts_1");
        modal.addEventListener("show.bs.modal", showRandomQuestions);
        let myModal = new bootstrap.Modal(modal);
        myModal.show();
    });
</script>