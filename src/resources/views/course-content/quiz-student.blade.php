@extends('layouts.app')
@section('title', 'Quiz')
@section('content')
        <style>
            .employee-form {
            background: #f8f8f8;
            padding: 20px;
            }
        </style>
        <div class="container">

        <h1 class="text-black-40 main-title">Quiz</h1>
        <div class="row">
            <div class="col col-8">
                <div class="card">
                    <div class="card-body">
                        <h2 class="quiz-question"><span>Q 1.</span> Which of the following country has largest population? </h2>
                        <div class="quiz-options">
                            <div class="row">
                                <div class="form-check col col-6">
                                    <label class="form-check-label" for="option1">
                                        <input class="form-check-input" type="radio" name="quizOption" id="option1" value="option1">
                                        <span><span></span>Option One</span>
                                    </label>
                                </div>
                                <div class="form-check col col-6">
                                    <label class="form-check-label" for="option2">
                                        <input class="form-check-input" type="radio" name="quizOption" id="option2" value="option2">
                                        <span><span></span>Option Two</span>
                                    </label>
                                </div>
                                <div class="form-check col col-6">
                                    <label class="form-check-label" for="option3">
                                    <input class="form-check-input" type="radio" name="quizOption" id="option3" value="option3">
                                        <span><span></span>Option Three</span>
                                    </label>
                                </div>
                                <div class="form-check col col-6">
                                    <label class="form-check-label" for="option4">      
                                    <input class="form-check-input" type="radio" name="quizOption" id="option4" value="option4">
                                        <span><span></span>Option Four</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h2 class="quiz-question"><span>Q 2.</span> Which of the following country has largest population? </h2>
                        <div class="quiz-image"><img src="https://via.placeholder.com/600x100" /></div>
                        <div class="quiz-options">
                            <div class="row">
                                <div class="form-check col col-6">
                                    <label class="form-check-label" for="option11">
                                        <input class="form-check-input" type="radio" name="quizOption1" id="option11" value="option11">
                                        <span><span></span>Option One</span>
                                    </label>
                                </div>
                                <div class="form-check col col-6">
                                    <label class="form-check-label" for="option21">
                                        <input class="form-check-input" type="radio" name="quizOption1" id="option21" value="option21">
                                        <span><span></span>Option Two</span>
                                    </label>
                                </div>
                                <div class="form-check col col-6">
                                    <label class="form-check-label" for="option31">
                                    <input class="form-check-input" type="radio" name="quizOption1" id="option31" value="option31">
                                        <span><span></span>Option Three</span>
                                    </label>
                                </div>
                                <div class="form-check col col-6">
                                    <label class="form-check-label" for="option41">      
                                    <input class="form-check-input" type="radio" name="quizOption1" id="option41" value="option41">
                                        <span><span></span>Option Four</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h2 class="quiz-question"><span>Q 3.</span> Which of the following country has largest population? </h2>
                        
                        <div class="quiz-options">
                            <div class="row">
                                <div class="form-check col col-6">
                                    <label class="form-check-label" for="option12">
                                        <input class="form-check-input" type="radio" name="quizOption2" id="option12" value="option12">
                                        <span><span></span>Option One</span>
                                    </label>
                                </div>
                                <div class="form-check col col-6">
                                    <label class="form-check-label" for="option22">
                                        <input class="form-check-input" type="radio" name="quizOption2" id="option22" value="option22">
                                        <span><span></span>Option Two</span>
                                    </label>
                                </div>
                                <div class="form-check col col-6">
                                    <label class="form-check-label" for="option32">
                                    <input class="form-check-input" type="radio" name="quizOption2" id="option32" value="option32">
                                        <span><span></span>Option Three</span>
                                    </label>
                                </div>
                                <div class="form-check col col-6">
                                    <label class="form-check-label" for="option42">      
                                    <input class="form-check-input" type="radio" name="quizOption2" id="option42" value="option42">
                                        <span><span></span>Option Four</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <h2 class="quiz-question"><span>Q 4.</span> Which of the following country has largest population? </h2>
                        <div class="quiz-options">
                            <div class="row">
                                <div class="form-check col col-6">
                                    <label class="form-check-label" for="option13">
                                        <input class="form-check-input" type="radio" name="quizOption3" id="option13" value="option13">
                                        <span><span></span>Option One</span>
                                    </label>
                                </div>
                                <div class="form-check col col-6">
                                    <label class="form-check-label" for="option23">
                                        <input class="form-check-input" type="radio" name="quizOption3" id="option23" value="option23">
                                        <span><span></span>Option Two</span>
                                    </label>
                                </div>
                                <div class="form-check col col-6">
                                    <label class="form-check-label" for="option33">
                                    <input class="form-check-input" type="radio" name="quizOption3" id="option33" value="option33">
                                        <span><span></span>Option Three</span>
                                    </label>
                                </div>
                                <div class="form-check col col-6">
                                    <label class="form-check-label" for="option43">      
                                    <input class="form-check-input" type="radio" name="quizOption3" id="option43" value="option43">
                                        <span><span></span>Option Four</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">SUBMIT QUIZ</button>
                <br><br>
            </div>
            <div class="col col-4 info-content">
                <div class="card sticky-box">
                    <div class="card-body">
                        <div class="row">
                            <div class="col col-6">
                                <b>Quiz Time</b>
                            </div>
                            <div class="col col-6">
                                <span id="countdown"></span>
                            </div>
                            <br><br>
                            <div class="col col-6">
                                <b>Total Question</b>
                            </div>
                            <div class="col col-6">
                                10
                            </div>
                            <br><br>
                            <div class="col col-6">
                                <b>Quiz Instruction</b>
                            </div>
                            <div class="col col-6">
                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
       
    </div>
    
@endsection

@push('page_scripts')
    
    <script>
        function countdown( elementName, minutes, seconds )
        {
            var element, endTime, hours, mins, msLeft, time;

            function twoDigits( n )
            {
                return (n <= 9 ? "0" + n : n);
            }

            function updateTimer()
            {
                msLeft = endTime - (+new Date);
                if ( msLeft < 1000 ) {
                    element.innerHTML = "countdown's over!";
                } else {
                    time = new Date( msLeft );
                    hours = time.getUTCHours();
                    mins = time.getUTCMinutes();
                    element.innerHTML = (hours ? hours + ':' + twoDigits( mins ) : mins) + ':' + twoDigits( time.getUTCSeconds() );
                    setTimeout( updateTimer, time.getUTCMilliseconds() + 500 );
                }
            }

            element = document.getElementById( elementName );
            endTime = (+new Date) + 1000 * (60*minutes + seconds) + 500;
            updateTimer();
        }

        countdown( "countdown", 20, 30 );
    </script>
@endpush
