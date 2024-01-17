@extends('layouts.app')

@section('title', 'Quiz')

@section('content')
    <style>
        .employee-form {
            background: #f8f8f8;
            padding: 20px;
        }
    </style>
    <div class="container" id="quizAttemptApp" v-cloak>
        <h1 class="text-black-40 main-title">Quiz</h1>
        <div class="row">
            <div class="col col-8">
                <div class="card" v-for="(question, index) in questions">
                    <div class="card-body">
                        <h2 class="quiz-question"><span>Question @{{ index+1 }}.</span></h2>
                        <div v-html="question.text"></div>
                        <div class="quiz-options">
                            <multi-choice
                                v-if="question.type == 'Multi Choice'"
                                :index="index"
                                :options="question.options"
                                :init-answers="question.answers"
                                @on-answers-update="updateAnswer"
                            />
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary" @click="forceSubmit"
                        :disabled="isLoading || isSubmitted">
                    SUBMIT QUIZ
                    <div v-if="isLoading" class="spinner-border spinner-border-sm" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </button>
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
                                <span id="countdown">@{{ timer }}</span>
                            </div>
                            <br><br>
                            <div class="col col-6">
                                <b>Total Question</b>
                            </div>
                            <div class="col col-6">
                                @{{totalQuestions}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

{{--        Modal for showing student successfully completed the quiz--}}
        <div class="modal fade" id="feedbackModal" tabindex="-1" role="dialog"
             aria-labelledby="exampleModalLabel"
             aria-hidden="true"
             data-backdrop="static" data-keyboard="false"
        >
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Congratulations!</h5>
                    </div>
                    <div class="modal-body">
                        <p>Well done, you've completed the quiz.</p>
                    </div>
                    <div class="modal-footer">
                            <a href="{{route('quiz.landing',[$quizId])}}" type="button" class="btn btn-primary btn-success">
                                OK
                            </a>
                    </div>
                </div>
            </div>
        </div>

        {{--Modal for showing timer--}}
        <div class="modal fade" id="timeUpModal" tabindex="-1" role="dialog" aria-labelledby="timeUpModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="timeUpModalLabel">Time's Up!</h5>
                    </div>
                    <div class="modal-body">
                        <p>Your time for the quiz has expired.</p>
                    </div>
                    <div class="modal-footer">
                        <a href="{{ route('quiz.landing', [$quizId]) }}" type="button" class="btn btn-primary btn-success">
                            OK
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection

@push('page_scripts')
    <script src="//unpkg.com/vue@3.0.11"></script>
    <script>
        (function () {
            let app = Vue.createApp({
                data: function () {
                    return {
                        attemptDetailId: {{$attemptDetailId}},
                        questions: @json($questions),
                        changeCount: 0, //the change count which will be used by auto state saving
                        saveStateDelay: 1000 * 60, //1 minute, time diff at which current question state will be saved
                        timerStart: {{$timeLimit}},
                        timer: "-", //this display timer on page
                        clockTimer: null,
                        saveStateTimer: null,
                        isLoading: false,
                        isSubmitted: false,
                        isTimeUp: false,
                    }
                },
                computed: {
                    totalQuestions: function () {
                        return this.questions.length;
                    }
                },
                methods: {

                    //It will show the modal when timer runs out
                    showTimeUpModal: function () {
                        if (this.isTimeUp) {
                            $('#timeUpModal').modal('show');
                        }
                    },
                    updateAnswer: function (index, answers) {
                        this.questions[index].answers = answers;
                        this.changeCount++;
                    },
                    async submit(mode) {
                        let serverUrl = "/quiz/submit/" + (this.attemptDetailId);
                        let request = {
                            method: 'post',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                _token: "{{csrf_token()}}",
                                mode: mode,
                                answers: JSON.stringify(this.questions.map(function (question) {
                                    return question.answers
                                }))
                            })
                        };

                        let response;

                        try {
                            response = await fetch(serverUrl, request);
                        } catch (e) {
                            return false;
                        }

                        if (!response.ok) {
                            return false;
                        }

                        //if quiz not submitted then ignore further operations
                        if (mode !== "SUBMIT") {
                            return true;
                        }
                        // $('#feedbackModal').modal('show');

                        if (!this.isTimeUp){
                            window.location.href = "{{ route('quiz.landing', [$quizId]) }}";
                        }
                        this.stopAllIntervals();
                        this.isSubmitted = true;
                        return true;

                    },
                    forceSubmit: async function (isTimeUp = false) {
                        this.isLoading = true;
                        let success = await this.submit('SUBMIT');
                        if (!success) {
                            this.isLoading = false;
                            alertModal("Error", "Unable to submit, Please try again");
                            return;
                        }

                        if (isTimeUp) {
                            this.showTimeUpModal();
                        }
                    },
                    stopAllIntervals: function () {
                        clearInterval(this.clockTimer);
                        clearInterval(this.saveStateTimer);
                    }
                },
                watch: {
                    timerStart: function (newValue) {
                        if (!isNaN(newValue) && newValue <= 0 && !this.isSubmitted) {
                            this.stopAllIntervals();
                            this.isTimeUp = true;
                        }
                    },
                },
                created: function () {

                    //clock timer run only if quiz have time limit
                    if (this.timerStart) {
                        this.clockTimer = setInterval(function () {
                            this.timerStart -= 1;
                            this.timer = new Date(this.timerStart * 1000).toISOString().substr(11, 8);
                            if (this.timerStart <= 0) {
                                this.forceSubmit(true);
                            }
                        }.bind(this), 1000);
                    }

                    //save state
                    this.saveStateTimer = setInterval(function () {
                        //if no change detect then do not send save state request
                        if (this.changeCount === 0) {
                            return;
                        }
                        this.changeCount = 0; //reset change count
                        this.submit('SAVE_STATE');
                    }.bind(this), this.saveStateDelay)
                }
            });

            //multi choice component
            app.component('multi-choice', {
                props: ['options', 'initAnswers', 'index'],
                data: function () {
                    return {
                        answers: this.initAnswers
                    };
                },
                methods: {},
                watch: {
                    answers: function (newValue) {
                        this.$emit('onAnswersUpdate', this.index, this.answers);
                    }
                },
                template: `
                <div class="row">
                    <div class="form-check col col-6" v-for="(option, index) in options">
                        <label class="form-check-label">
                            <input class="form-check-input" type="checkbox" :value="index" v-model="answers"/>
                            <span><span></span>@{{ option.text }}</span>
                        </label>
                    </div>
                </div>
                `
            });
            app.mount('#quizAttemptApp');
        })();
    </script>
@endpush
